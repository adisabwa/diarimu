<?php

namespace App\Controllers;

use App\Controllers\BaseDataController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;

class BasePageController extends BaseDataController
{
    private $modelAnggota;

    public function __construct()
    {
        parent::__construct();
        
        $this->modelAnggota = model('AnggotaModel');
    }

    public function get_before()
    {
        $postData = $this->request->getGetPost();
        $id_anggota = $postData['id_anggota'] ?? userdata()->id_anggota;
        $data = $this->model->where('id_anggota', $id_anggota)->orderBy('tanggal desc')->find();
        $now = date('Y-m-d');
        $tanggal = $data[0]->tanggal ?? $now;

        // var_dump($tanggal, $now);
        return $this->respondCreated(get_date_interval($tanggal ?? $now, $now));
    }

    public function createChart(string $attr = 'data_chart', bool $return_data = false)
    {
        $postData = $this->request->getGetPost();
        $type = $postData['tipe'] ?? 'week';
        $end = empty($postData['end']) ? date('Y-m-d') : $postData['end'];
        $start = empty($postData['start']) ? date('Y-m-d') : $postData['start'];
        $id_anggota = $postData['id_anggota'] ?? userdata()->id_anggota;
        $where_anggota = "id_anggota IN ($id_anggota)";
        $anggotas = $this->modelAnggota->where(["id IN ($id_anggota)" => NULL])->findAll();

        $date_range = getDateRange($start, $end);
        $data = $this->model->getAll(
            [
                $where_anggota => NULL,
                "tanggal >= '$start'" => NULL,
                "tanggal <= '$end'" => NULL,
            ]
        );
        $_data = [];
        // var_dump($data);
        // var_dump($this->model->getLastQuery());
        foreach ($data as $key => $d) {
            $d->id_anggota = "$d->id_anggota-$d->nama";
            if (empty($_data[$d->id_anggota][$d->tanggal])) {
                $_data[$d->id_anggota][$d->tanggal] = $d->$attr;
            } else {
                $_data[$d->id_anggota][$d->tanggal] += $d->$attr;
            }
        }
        // var_dump(userdata());
            // foreach ($anggotas as $key => $d) {
            //     $_data["$d->id-$d->nama"] =  [date('Y-m-d') => '0'];
            // }
        // var_dump($_data);
        $total = $labels = [];
        $max = $min = 0;
        $datasets = [];
        foreach ($anggotas as $ind => $d) {
            $data_ang = $_data["$d->id-$d->nama"] ?? [];
            // var_dump($id_anggota);
            foreach ($date_range as $key => $tgl) {
                // var_dump($tgl);
                $labels[$tgl] = date("d M", strtotime($tgl));
                $total[$tgl] = $data_ang[$tgl] ?? 0;
            }
            
            $color =  setRandomColor();
            $datasets[] = (object)[
                'label' => $d->nama,
                'data' => array_values($total),
                'tension' => 0.1,
                'borderColor' => $color,
                'backgroundColor' => $color,
                'pointRadius' => 5,
            ];
            $tmp_max = empty($total) ? 1 : max($total);
            $tmp_min = empty($total) ? -1 : min($total);
            if ($tmp_max > $max) $max = $tmp_max;
            if ($tmp_min < $min) $min = $tmp_min;
        }
        
        // $datasets = array_values($datasets);
        $labels = array_values($labels);
        $compact = compact('labels','datasets','max','min');
        if ($return_data)
            return $compact;
        return $this->respondCreated($compact);
    }

    public function download(
        string $filename = 'DATA-REKAP',
        string $data_label = 'Tanggal / Jumlah Ayat'
    )
    {
        // var_dump($filename);exit;
        $workbook = new Spreadsheet();
        $workbook->getProperties()
                    ->setCreator('Codev-App')
                    ->setTitle('Ashoi-Mu');
        $data = $this->createChart(return_data: true);
        $columns = excelColumnRange('A', 'ZZ');
        // return $this->respondCreated($data);
        // return;
        $activeWorksheet = $workbook->getActiveSheet();
        $activeWorksheet->mergeCells('A1:A2');
        $activeWorksheet->setCellValue('A1', 'No');
        $activeWorksheet->mergeCells('B1:B2');
        $activeWorksheet->setCellValue('B1', 'Nama');
        $activeWorksheet->mergeCells('C1:'.$columns[count($data['labels'])].'1');
        $activeWorksheet->setCellValue('C1', $data_label);
        foreach ($data['labels'] as $key => $label) {
            $activeWorksheet->setCellValue($columns[$key+2].'2', $label);
        }
        $row = 3;
        foreach ($data['datasets'] as $key => $stat) {
            $activeWorksheet->setCellValue('A'.$row, $key+1);
            $activeWorksheet->setCellValue('B'.$row, $stat->label);
            foreach ($stat->data as $k => $d) {
                $activeWorksheet->setCellValue($columns[$k+2].$row, $d);
            }
            $row++;
        }

        for ($i = 'A'; $i !=  $activeWorksheet->getHighestColumn(); $i++) {
            $activeWorksheet->getColumnDimension($i)->setAutoSize(TRUE);
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
        $writer = IOFactory::createWriter($workbook, 'Xls');
        // ob_end_clean();
        $writer->save('php://output');

    }
}