<?php

namespace Modules\Data\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;
use App\Controllers\Kolom;

class BaseData extends BaseController
{
    public $model;
    public $kolomClass;

    public function __construct()
    {
        $this->kolomClass = new Kolom;
    }

    public function index()
    {
        $where = $this->request->getGet('where') ?? [];
        $order = $this->request->getGet('order') ?? [];
        $order = implode(",", $order);

        $data = $this->model->where($where)->orderBy($order)->findAll();
        foreach ($data as $key => $d) {
            $d->checked = false;
        }
        return $this->respondCreated($data);
    }

    public function get()
    {
        $id = $this->request->getGet('id');

        return $this->respondCreated($this->model->find($id));
    }

    
    public function options()
    {
        $where = $this->request->getGet('where') ?? [];
        return $this->respondCreated($this->model->getOptions($where));
    }

    public function store()
    {
        $posted_data = $this->request->getPost();
        $data = $posted_data;
        unset($data['id']);
        $data["created_by"] = userdata()->id ?? 0;
        $child_key = [];
        $child_table = [];
        foreach ($data as $key => $values) {
            if (is_array($values)) {
                $el = explode('-',$key);
                $table_name = $el[0];
                $child_table[$table_name] = [];
                array_shift($el);
                foreach($values as $value) {
                    $vals = explode('-',$value);
                    $tmp = [];
                    foreach ($el as $ind => $e){
                        $tmp[$e] = $vals[$ind];
                    }
                    $child_table[$table_name][] = $tmp;
                }
                $child_key[$table_name] = $key;
                unset($data[$key]);
            }
        }
        // Start the transaction
        $this->model->transBegin();
        if ($posted_data['id'] > 0) {
            $save = $this->model->update($posted_data['id'], $data);
        } else {
            $save = $this->model->insert($data);
            $posted_data['id'] = $this->model->insertID();
        }
        // var_dump($posted_data);
        // var_dump($this->model, $this->model->error());
        // Append ID to data
        foreach ($child_table as $table => $values) {
            $data_kolom = $this->kolomClass->getKolomDetail($this->model->getTableName(), $child_key[$table]);
            // var_dump($data_kolom);
            $fk = $data_kolom->nama_fk;
            $model = $this->kolomClass->getModelFromTable($table);
            if ($model) {
                $model->where([$fk => $posted_data['id']])->delete();
                foreach($values as $val){
                    $val[$fk] = $posted_data['id'];
                    $model->insert($val);
                }
            }
        }

        if ($this->model->transStatus() === false) {
            $this->model->transRollback();
            // var_dump($this->model->error());
            return $this->failServerError();
        } else {
            $this->model->transCommit();
            return $this->respondCreated($posted_data);
        }
    }

    public function resetOptions()
    {
        return $this->respondCreated($this->model->getOptions());
    }

    public function delete($id)
    {
        // Start the transaction
        $this->model->transBegin();

        $save = $this->model->delete($id);

        if ($this->model->transStatus() === false) {
            $this->model->transRollback();
            return $this->failServerError();
        } else {
            $this->model->transCommit();
            return $this->respondCreated();
        }
    }

    public function delete_many()
    {
        // Start the transaction
        $this->model->transBegin();
        
        $ids = $this->request->getPost('id') ?? -1;
        // var_dump($ids);exit;
        $save = $this->model->delete($ids);

        if ($this->model->transStatus() === false) {
            $this->model->transRollback();
            return $this->failServerError();
        } else {
            $this->model->transCommit();
            return $this->respondCreated();
        }
    }

    public function template()
    {
        $filename = 'TEMPLATE-UPLOAD-DATA-PENGHASILAN';
        // var_dump($filename);exit;
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
                    ->setCreator('Codev-App')
                    ->setTitle('Finance App');
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'No');
        $spreadsheet->getActiveSheet()->setCellValue('B1', 'Label Iqab');
        $spreadsheet->getActiveSheet()->setCellValue('C1', 'Rentang Awal');
        $spreadsheet->getActiveSheet()->setCellValue('D1', 'Rentang Akhir');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Cth');
        $spreadsheet->getActiveSheet()->setCellValue('B2', 'Gol. 2');
        $spreadsheet->getActiveSheet()->setCellValue('C2', '0');
        $spreadsheet->getActiveSheet()->setCellValue('D2', '1000000');

        for ($i = 'A'; $i !=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        // ob_end_clean();
        $writer->save('php://output');
    }

    public function upload()
    {
        $validationRule = [
            'file' => [
                'uploaded[file]',
                'max_size[file,2048]',
                'ext_in[file,xls,xlsx]',
            ],
        ];

        if (! $this->validate($validationRule)) {
            return $this->fail($this->validator->getErrors());
        }

        $file = $this->request->getFile('file');
        // var_dump($file);exit;
        if ($file->hasMoved()) {
            return $this->fail(['file' => 'The file has already been moved.']);
        }

        $filename = WRITEPATH . 'uploads/' . $file->store();
        // $file = new File($filename);
        // var_dump($filename);exit;
        /**  Identify the type of $inputFileName  **/
        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filename);
        /**  Create a new Reader of the type that has been identified  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($filename);
        // var_dump($inputFileType, $reader, $spreadsheet);exit;
        // $spreadsheet->setActiveSheetIndexByName('Sheet1');
        $sheet = $spreadsheet->getActiveSheet();
        $data_pegnghasilan = [];
        $error_pegnghasilan = [];
        $row = 2;
        $save = true;
        $no = $sheet->getCell('A'.$row)->getValue();
        // var_dump($no);exit;
        while (!empty($no)) {
            $this->validation->reset();

            $this->validation->setRule('label', 'Label', 'required');
            $this->validation->setRule('dari', 'Rentang Awal', 'required');
            $this->validation->setRule('hingga', 'Rentang Akhir', 'required');

            $data = [
                'label'      => trim($sheet->getCell('B'.$row)->getValue()),
                'dari'   => trim($sheet->getCell('C'.$row)->getValue()),
                'hingga'     => trim($sheet->getCell('D'.$row)->getValue()),
            ];

            $row++;
            $no = $sheet->getCell('A'.$row)->getValue();

            // var_dump($data);
            // var_dump(in_array($data['no_id'],['-','','+',NULL]));
            if ($this->validation->run($data)) {
                $check = $this->model->where($data)->first();

                if (!empty($check)) {
                    $data["created_by"] =  userdata()->id;
                    $save = $this->model->update($check['id'], $data);
                    $data['id'] = $check['id'];

                } else {
                    $save = $this->model->insert($data);
                    $data['id'] = $this->model->insertID();
                }
                $data_pegnghasilan[] = $data;
            } else {
                $data['error'] = implode(', ', $this->validation->getErrors());
                $error_pegnghasilan[] = $data;
            }
            // Your idea go here...
        }
        unlink($filename);
        array_walk($error_pegnghasilan, function(&$val) {
            $val['keterangan'] = $val['label'];
        });
        // return sendInternalServerError();
        if ($save) return $this->respondCreated(['data' => $data_pegnghasilan,'error' => $error_pegnghasilan]);
        else return $this->failServerError();
    }
}
