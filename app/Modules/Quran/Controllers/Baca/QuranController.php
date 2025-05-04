<?php

namespace Modules\Quran\Controllers\Baca;

use Modules\Data\Controllers\BaseData;
use App\Libraries\PdfBuilder;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use Config\Upload;
use stdClass;

class QuranController extends BaseData
{
    protected $quran;

    public function __construct()
    {
        $this->model = model('QuranBacaModel');
        $this->quran = model('DataSuratQuranModel');
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
        $data = $this->model->getAll($whereAnd, $whereOr, $order);

        // var_dump($this->model->db->getLastQuery());
        return $this->respondCreated($data);
    }

    public function get()
    {
        $id = $this->request->getGet('id');
        $data = $this->model->find($id);
        if (!empty($data)) {
            $data->parentSelect = (object)[
                'surat_mulai-ayat_mulai' => $data->surat_mulai,
                'surat_selesai-ayat_selesai' => $data->surat_selesai,
            ];
            $data->{'surat_mulai-ayat_mulai'} = "$data->surat_mulai-$data->ayat_mulai";
            $data->{'surat_selesai-ayat_selesai'} = "$data->surat_selesai-$data->ayat_selesai";
        }
        return $this->respondCreated($data);
    }

    public function save()
    {
        $posted_data = $this->request->getPost();
        $countAyat = $this->quran->countAyat($posted_data['surat_mulai'], $posted_data['ayat_mulai'],     $posted_data['surat_selesai'], $posted_data['ayat_selesai']);
        $posted_data['total_ayat'] = $countAyat;
        $this->request->setGlobal('post', $posted_data);
        return $this->store();
    }

    public function get_last()
    {
        return $this->respondCreated($this->model->get_last());
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
        $save = $this->model->update($id,$data);

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
        $save = $this->model->update($ids,['status' => $status]);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    public function delete($id)
    {
        $save = $this->model->delete($id);

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
        $save = $this->model->delete($ids);

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
        
        $html = view('dokumens/kartu-pendaftaran', ['content' => $data]);
        // echo $html;exit;
        $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 842]);
    }

    public function download_many()
    {
        ini_set('max_input_vars', -1);

        $ids = $this->request->getPost('id') ?? -1;

        $PdfBuilder = new PdfBuilder();

        $data  = $this->model->getAll(['p.id IN ('.implode(',',$ids).')' => NULL, 'status' => '2']);
        $html = '';
        foreach($data as $d) {
            $html .= view('dokumens/kartu-pendaftaran', ['content' => $d]);
        }

        $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 500]);
    }

}