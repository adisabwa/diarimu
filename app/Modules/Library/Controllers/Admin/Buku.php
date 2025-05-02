<?php

namespace Modules\Library\Controllers\Admin;

use Modules\Data\Controllers\BaseData;
use App\Libraries\PdfBuilder;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use Config\Upload;
use stdClass;

class Buku extends BaseData
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('BukuModel');
        helper('buku');
    }

    public function index()
    {
        $jenis = $this->request->getPostGet('jenis');
        $keyword = $this->request->getPostGet('keyword');
        $order = implode(",", $this->request->getPostGet('order') ?? []);

        $whereAnd = [
            empty($jenis) ? '1=1' : "id_jenis='$jenis'" => NULL,
        ];
        $whereOr = [
            "judul LIKE '%$keyword%'" => NULL,
            "penulis LIKE '%$keyword%'" => NULL,
            "penerbit LIKE '%$keyword%'" => NULL,
        ];
        $data = $this->model->getAll($whereAnd, $whereOr, $order);

        // var_dump($this->model->db->getLastQuery());
        return $this->respondCreated($data);
    }

    public function dashboard()
    {
        $data = $this->model->getSummary();
        $labels = [];
        $datasets = [
            'backgroundColor' => [],
            'data' => [],
        ];
        foreach ($data as $key => $value) {
            $labels[] = $value->jenis;
            $datasets['backgroundColor'][] = setStatusColor($value->jenis);
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
        $save = $this->model->update($id,$data);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    public function status_many()
    {
        ini_set('max_input_vars', -1);

        $ids = $this->request->getPostGet('id') ?? -1;
        $status = $this->request->getPostGet('status') ?? 1;
        // var_dump($ids);exit;
        $save = $this->model->update($ids,['status' => $status]);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }
    
    public function download($id)
    {
        $PdfBuilder = new PdfBuilder();

        $data  = $this->model->getData(['p.id' => $id]);
        if ($data->status != '2')
            exit('Data belum diverifikasi');
        
        $html = view('Modules\Library\dokumens/kartu-pendaftaran', ['content' => $data]);
        // echo $html;exit;
        $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 842]);
    }

    public function download_many()
    {
        ini_set('max_input_vars', -1);

        $ids = $this->request->getPostGet('id') ?? -1;

        $PdfBuilder = new PdfBuilder();

        $data  = $this->model->getAll(['p.id IN ('.implode(',',$ids).')' => NULL, 'status' => '2']);
        $html = '';
        foreach($data as $d) {
            $html .= view('Modules\Library\dokumens/kartu-pendaftaran', ['content' => $d]);
        }

        $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 500]);
    }

}
