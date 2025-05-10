<?php

namespace Modules\Infaq\Controllers\Shadaqah;

use Modules\Data\Controllers\BaseData;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;

class InfaqController extends BaseData
{

    public function __construct()
    {
        $this->model = model('InfaqShadaqahModel');
    }
    
    public function index()
    {
        $postData = $this->request->getGetPost();

        $data = $this->model->getAll([
            'id_anggota'    => $postData['id_anggota'] ?? userdata()->id_anggota
        ],[],'tanggal desc, id',
        $postData['limit'] ?? 5, $postData['offset'] ?? 0);

        return $this->respondCreated($data);

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
        foreach ($data as $key => $d) {
            if (empty($_data[$d->tanggal])) {
                $_data[$d->tanggal] = $d->jumlah;
            } else {
                $_data[$d->tanggal] += $d->jumlah;
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
            'label' => 'Jumlah Shadaqah',
            'data' => array_values($total),
            'tension' => 0.1,
            'borderColor' => $color,
            'backgroundColor' => $color,
            'pointRadius' => 5,
        ];
        $max = empty($total) ? 10000 : max($total);
        $min = empty($total) ? 0 : min($total);
        
        // $datasets = array_values($datasets);
        $labels = array_values($labels);
        return $this->respondCreated(compact('labels','datasets','max','min'));
    }
}