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

        $data = $this->model->getAll(
            ['id_anggota' => userdata()->id_anggota],
            [],
            'tanggal asc'
        );
        $_data = [];
        foreach ($data as $key => $d) {
            if (empty($_data[$d->tanggal])) {
                $tmp = (object)[
                    'label' => date('d M y', strtotime($d->tanggal)),
                    'total' => $d->total_score
                ];
                $_data[$d->tanggal] = $tmp;
            } else {
                $_data[$d->tanggal]->total += $d->total_score;
            }
        }
        // var_dump($_data);
        $total = $labels = [];
        $max = $min = 0;

        foreach ($_data as $key => $value) {
            if (empty($labels[$value->label])) {
                $labels[$value->label] = $value->label;
                $total[$value->label] = $value->total;
            }
        }
        
        $color =  setRandomColor();
        $datasets[] = (object)[
            'label' => 'Nilai Infaq',
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
}