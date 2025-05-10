<?php

namespace Modules\Quran\Controllers\Tarjamah;

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
        $this->model = model('QuranTarjamahModel');
        $this->quran = model('DataSuratQuranModel');
    }
    
    
    public function index()
    {
        $where = $this->request->getGetPost('where') ?? [];
        $whereOr = $this->request->getGetPost('or') ?? [];
        $order = $this->request->getGetPost('order') ?? [];
        $limit = $this->request->getGetPost('limit') ?? 5;
        $offset = $this->request->getGetPost('offset') ?? 0;

        $data = $this->model->getAll($where,$order,'tanggal desc, id',
        $limit, $offset);

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

    public function get_before()
    {
        $data = $this->model->orderBy('tanggal desc')->find()[0];
        $now = date('Y-m-d');

        // var_dump($data->tanggal, $now);
        return $this->respondCreated(get_date_interval($data->tanggal ?? $now, $now));
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
        return $this->respondCreated($this->model->get_last(userdata()->id_anggota));
    }

    public function dashboard()
    {
        $postData = $this->request->getGetPost();
        $type = $postData['tipe'] ?? 'week';
        $end = $postData['end'] ?? date('Y-m-d');
        $start = $postData['start'] ?? date('Y-m-d');

        $date_range = getDateRange($start, $end);
        $data = $this->model->getAll(
            [
                'id_anggota' => userdata()->id_anggota,
                "tanggal >= '$start'" => NULL,
                "tanggal <= '$end'" => NULL,
            ]
        );
        $_data = [];
        // var_dump($data);
        foreach ($data as $key => $d) {
            if (empty($_data[$d->tanggal])) {
                $_data[$d->tanggal] = $d->total_ayat ?? 0;
            } else {
                $_data[$d->tanggal] += $d->total_ayat ?? 0;
            }
        }
        // var_dump($_data);
        $total = $labels = [];
        $max = $min = 0;

        foreach ($date_range as $key => $tgl) {
            // var_dump($tgl);
            $labels[$tgl] = date("d M", strtotime($tgl));
            $total[$tgl] = $_data[$tgl] ?? 0;
        }
        
        $color =  setRandomColor();
        $datasets[] = (object)[
            'label' => 'Jumlah Tarjamahan Ayat',
            'data' => array_values($total),
            'tension' => 0.1,
            'borderColor' => $color,
            'backgroundColor' => $color,
            'pointRadius' => 5,
        ];
        $max = empty($total) ? 1 : max($total);
        $min = empty($total) ? -1 : min($total);
        
        // $datasets = array_values($datasets);
        $labels = array_values($labels);
        return $this->respondCreated(compact('labels','datasets','max','min'));
    }

    // public function status($id, $status)
    // {
    //     $data = ['status' => $status];
    //     if ($status == '2')
    //         $data['no_pendaftaran'] = getNomorPendaftaran();
    //     // var_dump($data);exit;
    //     $save = $this->model->update($id,$data);

    //     if ($save)
    //         return $this->respondCreated();
    //     else
    //         return $this->failServerError();
    // }

    // public function status_many()
    // {
    //     ini_set('max_input_vars', -1);

    //     $ids = $this->request->getPost('id') ?? -1;
    //     $status = $this->request->getPost('status') ?? 1;
    //     // var_dump($ids);exit;
    //     $save = $this->model->update($ids,['status' => $status]);

    //     if ($save)
    //         return $this->respondCreated();
    //     else
    //         return $this->failServerError();
    // }

    // public function delete($id)
    // {
    //     $save = $this->model->delete($id);

    //     if ($save)
    //         return $this->respondCreated();
    //     else
    //         return $this->failServerError();
    // }

    // public function delete_many()
    // {
    //     ini_set('max_input_vars', -1);

    //     $ids = $this->request->getPost('id') ?? -1;
    //     // var_dump($ids);exit;
    //     $save = $this->model->delete($ids);

    //     if ($save)
    //         return $this->respondCreated();
    //     else
    //         return $this->failServerError();
    // }

    
    // public function download($id)
    // {
    //     $PdfBuilder = new PdfBuilder();

    //     $data  = $this->model->getData(['p.id' => $id]);
    //     if ($data->status != '2')
    //         exit('Data belum diverifikasi');
        
    //     $html = view('dokumens/kartu-pendaftaran', ['content' => $data]);
    //     // echo $html;exit;
    //     $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 842]);
    // }

    // public function download_many()
    // {
    //     ini_set('max_input_vars', -1);

    //     $ids = $this->request->getPost('id') ?? -1;

    //     $PdfBuilder = new PdfBuilder();

    //     $data  = $this->model->getAll(['p.id IN ('.implode(',',$ids).')' => NULL, 'status' => '2']);
    //     $html = '';
    //     foreach($data as $d) {
    //         $html .= view('dokumens/kartu-pendaftaran', ['content' => $d]);
    //     }

    //     $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 500]);
    // }

}