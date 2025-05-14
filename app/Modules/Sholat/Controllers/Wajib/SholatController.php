<?php

namespace Modules\Sholat\Controllers\Wajib;

use Modules\Data\Controllers\BaseData;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;

class SholatController extends BaseData
{

    public function __construct()
    {
        $this->model = model('SholatWajibModel');
    }
    
    
    public function index()
    {
        $where = $this->request->getGetPost('where') ?? [];
        $or = $this->request->getGetPost('or') ?? [];
        $limit = $this->request->getGetPost('limit') ?? 5;
        $offset = $this->request->getGetPost('offset') ?? 0;

        $data = $this->model->getAll($where,$or,'tanggal desc, id',
        $limit, $offset,'tanggal');

        array_walk($data, function(&$value, $key) {
            $value->show_detail = false;
        });

        return $this->respondCreated($data);

    }

    public function get_last_and_best()
    {
        $postData = $this->request->getGetPost();
        $id_anggota = $postData['id_anggota'] ?? userdata()->id_anggota;
        $last = $this->model->get_last($id_anggota);
        $best = $this->model->get_best($id_anggota);

        return $this->respondCreated(compact('last','best'));

    }
    
    public function store()
    {
        // Keep the original behavior
        $data = parent::store();
        // // Add your own logic
        $id = $this->request->getPost('id');
        $this->model->update_total_score($id);
        return $data;

    }

    public function dashboard()
    {
        $postData = $this->request->getGetPost();
        $id_anggota = $postData['id_anggota'] ?? userdata()->id_anggota;
        $type = $postData['tipe'] ?? 'week';
        $end = $postData['end'] ?? date('Y-m-d');
        $start = $postData['start'] ?? date('Y-m-d');
        
        $date_range = getDateRange($start, $end);
        $data = $this->model->getAll(
            [
                'id_anggota' => $id_anggota,
                "tanggal >= '$start'" => NULL,
                "tanggal <= '$end'" => NULL,
            ]
        );
        $_data = [];
        foreach ($data as $key => $d) {
            if (empty($_data[$d->tanggal])) {
                $_data[$d->tanggal] = $d->total_score;
            } else {
                $_data[$d->tanggal] += $d->total_score;
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
            'label' => 'Nilai Sholat',
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

    public function get_before()
    {
        $postData = $this->request->getGetPost();
        $id_anggota = $postData['id_anggota'] ?? userdata()->id_anggota;
        $data = $this->model->where('id_anggota', $id_anggota)->orderBy('tanggal desc')->find();
        $now = date('Y-m-d');
        $tanggal = $data[0]->tanggal ?? $now;

        // var_dump($data->tanggal, $now);
        return $this->respondCreated(get_date_interval($tanggal ?? $now, $now));
    }
}