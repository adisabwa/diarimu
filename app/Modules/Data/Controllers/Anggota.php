<?php

namespace Modules\Data\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;
use Modules\Data\Controllers\BaseData;

class Anggota extends BaseData
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('AnggotaModel');
    }

    public function search()
    {
        $whereAnd = $this->request->getGet('and') ?? [];
        $whereOr = $this->request->getGet('or') ?? ['1=1'];
        $order = $this->request->getGet('order') ?? [];
        $order = implode(",", $order);

        $data = $this->model->getAll($whereAnd, $whereOr, $order)[0] ?? [];

        // var_dump($this->model->db->getLastQuery());
        return $this->respondCreated($data);
    }
}