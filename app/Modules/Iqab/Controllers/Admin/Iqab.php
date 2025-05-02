<?php

namespace Modules\Iqab\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\PdfBuilder;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use Config\Upload;
use stdClass;

class Iqab extends BaseController
{
    protected $listIqabModel;

    public function __construct()
    {
        $this->listIqabModel = model('ListIqabModel');
    }

    public function index()
    {   
        $nama = $this->request->getPostGet('nama');
        $kelas = $this->request->getPostGet('kelas'); 
        $order = implode(",", $this->request->getPostGet('order') ?? []);
        $whereAnd = [
            "nama LIKE '%$nama%'" => NULL,
            empty($kelas) ? '1=1' : "kelas='$kelas'" => NULL
        ];
        
        $whereOr = [
            
        ];
        $data = $this->listIqabModel->getAll($whereAnd, $whereOr, $order);

        // var_dump($this->listIqabModel->db->getLastQuery());
        return $this->respondCreated($data);
    }

    public function get()
    {
        $id = $this->request->getGet('id');
        $data = $this->listIqabModel->find($id);
        if (!empty($data)) {
            $data->parentSelect = (object)['id_pelanggaran-id_iqab' => $data->id_pelanggaran];
            $data->{'id_pelanggaran-id_iqab'} = "$data->id_pelanggaran-$data->id_iqab";
        }
        return $this->respondCreated($data);
    }

    public function store()
    {
        $posted_data = $this->request->getPost();
        $data = $posted_data;
        unset($data['id']);
        $data["created_by"] = userdata()->id;

        // var_dump($data);
        if ($posted_data['id'] > 0) {
            $save = $this->listIqabModel->update($posted_data['id'], $data);
        } else {
            $save = $this->listIqabModel->insert($data);
            $posted_data['id'] = $this->listIqabModel->insertID();
        }
        // Append ID to data
        if ($save) {
            return $this->respondCreated($posted_data);
        } else {
            return $this->failServerError();
        }
    }

    public function dashboard()
    {
        $data = $this->listIqabModel->getSummary();
        $labels = [];
        $datasets = [
            'backgroundColor' => [],
            'data' => [],
        ];
        foreach ($data as $key => $value) {
            $labels[] = setStatusText($value->status);
            $datasets['backgroundColor'][] = setStatusColor($value->status);
            $datasets['data'][] = ($value->jumlah);
        }
        return $this->respondCreated(['labels' => $labels, 'datasets' => [$datasets]]);
    }

    public function status($id, $status)
    {
        $data = ['status' => $status];
        if ($status == '2')
            $data['no_pendaftaran'] = getNomorPendaftaran();
        // var_dump($data);exit;
        $save = $this->listIqabModel->update($id,$data);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    public function status_many()
    {
        ini_set('max_input_vars', -1);

        $ids = $this->request->getPost('id') ?? -1;
        $status = $this->request->getPost('status') ?? 1;
        // var_dump($ids);exit;
        $save = $this->listIqabModel->update($ids,['status' => $status]);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    public function delete($id)
    {
        $save = $this->listIqabModel->delete($id);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    public function delete_many()
    {
        ini_set('max_input_vars', -1);

        $ids = $this->request->getPost('id') ?? -1;
        // var_dump($ids);exit;
        $save = $this->listIqabModel->delete($ids);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    
    public function download($id)
    {
        $PdfBuilder = new PdfBuilder();

        $data  = $this->listIqabModel->getData(['p.id' => $id]);
        if ($data->status != '2')
            exit('Data belum diverifikasi');
        
        $html = view('dokumens/kartu-pendaftaran', ['content' => $data]);
        // echo $html;exit;
        $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 842]);
    }

    public function download_many()
    {
        ini_set('max_input_vars', -1);

        $ids = $this->request->getPost('id') ?? -1;

        $PdfBuilder = new PdfBuilder();

        $data  = $this->listIqabModel->getAll(['p.id IN ('.implode(',',$ids).')' => NULL, 'status' => '2']);
        $html = '';
        foreach($data as $d) {
            $html .= view('dokumens/kartu-pendaftaran', ['content' => $d]);
        }

        $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 500]);
    }

}