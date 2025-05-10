<?php

namespace Modules\Sholat\Controllers\Sunnah;

use Modules\Data\Controllers\BaseData;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;

class SholatController extends BaseData
{
    private $data;

    public function __construct()
    {
        $this->model = model('SholatSunnahModel');
        $this->data = model('DataSholatSunnahModel');
    }
    
    public function get_initial()
    {
        $postData = $this->request->getGetPost();
        $tanggal = $postData['tanggal'];

        $data = $this->model->getAll([
            'id_anggota' => $postData['id_anggota'],
            'tanggal' => $tanggal,
        ]);

        $keys = [];
        foreach ($data as $key => $d) {
            $keys[$d->id_sholat] = $key;
        }

        $sholats = $this->data->findAll();
        // $sholats = [$sholats[0]];
        $datas = [];
        foreach ($sholats as $key => $d) {
            $exist = isset($keys[$d->id]);
            $datas[$d->id] = (object) [
                'ref'           => "dropdown$tanggal$d->nama_sholat",
                'id_sholat'     => $d->id,
                'nama_sholat'   => $d->nama_sholat,
                'do'            => $exist,
                'edit'          => false,
                'rakaat'        => $exist ? $data[$keys[$d->id]]->rakaat : ($d->rakaat == 'even' ? 2 : 1),
                'min'           => $d->rakaat == 'even' ? 2 : 1,
                'optionsRakaat' => $d->rakaat == 'even'
                    ? ['2','4','6'] : ['1','3','5'],
            ];
        }

        return $this->respondCreated($datas);

    }
    
    public function store()
    {
        $data = $this->request->getPost();
        $this->model->transBegin();
        
        $params = [
            'id_anggota' => $data['id_anggota'],
            'tanggal' => $data['tanggal'],
            'id_sholat' => $data['id_sholat'],
        ];

        $this->model->where($params)->delete();
        
        if ($data['insert']) {
            $params['rakaat'] = $data['rakaat'];
            $save = $this->model->insert($params, TRUE);
        }

        if ($this->model->transStatus() === false) {
            $this->model->transRollback();
            // var_dump($this->model->error());
            return $this->failServerError();
        } else {
            $this->model->transCommit();
            return $this->respondCreated($data);
        }

    }

    public function get_before()
    {
        $data = $this->model->orderBy('tanggal desc')->find()[0];
        $now = date('Y-m-d');

        // var_dump($data->tanggal, $now);
        return $this->respondCreated(get_date_interval($data->tanggal ?? $now, $now));
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
}