<?php

namespace Modules\Data\Controllers;

use App\Controllers\BaseDataController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;

class GroupController extends BaseDataController
{
    private $modelAnggota;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('GroupModel');
        $this->modelAnggota = model('GroupAnggotaModel');
    }

    public function index($return = TRUE)
    {
        $data = parent::index(true);

        return $this->respondCreated($this->grouping_data($data));
    }
    
    public function get()
    {
        $id = $this->request->getGet('id');
        $data = $this->model->find($id);
        if (!empty($data))
        $data->mu_group_anggota = array_map(function($val){
            return (object)[
                'id_anggota'    => $val->id_anggota,
                'type'    => $val->type,
            ];
        }, $this->modelAnggota->getAll(whereAnd:['id_group' => $id], order: 'nama asc') );

        return $this->respondCreated(($data));
    }

    public function grouping_data($data)
    {
        $results = [];
        foreach ($data as $key => $d) {
            $ind = md5($d->id);
            $d->checked = false;
            if (empty($results[$ind])) {
                $results[$ind] = (object) [
                    'id' => $d->id,
                    'id_unit' => $d->id_unit,
                    'unit_kerja' => $d->unit_kerja,
                    'bidang' => $d->bidang,
                    'nama_group' => $d->nama_group,
                    'anggota' => [],
                    'show'  => false,
                ];
            }
            $results[$ind]->anggota[] = (object) [
                'id_group' => $d->id_group,
                'id_anggota' => $d->id_anggota,
                'type' => $d->type,
                'nama' => $d->nama,
            ];
        }
        return array_values($results);
    }

    public function get_anggota()
    {
        $user = userdata();
        $id = $this->request->getGetPost('id') ?? userdata()->id_anggota;
        $data = $this->modelAnggota->where(['id_anggota' => $id,'type' => 'mentor'])->find()[0] ?? [];
        $datas = [];
        
        if ($user->role == 'super-admin')
            $where_group = "1=1";
        else if ($user->role == 'admin')
            $where_group = "id_unit='$user->id_unit'" ; 
        else
            $where_group = "id_group='$user->id_group'";
        
        if (!empty($data)) {
            $datas = $this->modelAnggota->getAll([
                $where_group => NULL
            ]);
        }

        return $this->respondCreated($datas);
    }
}
