<?php

namespace App\Controllers;

use Modules\Data\Controllers\BaseData;

class Pengguna extends BaseData
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('PenggunaModel');
        // helper('auth');
    }

    public function index()
    {
        $where = $this->request->getGet('where') ?? [];
        $order = $this->request->getGet('order') ?? [];
        $order = implode(",", $order);

        $data = $this->model->getAll($where, $order);
        foreach ($data as $key => $d) {
            $d->checked = false;
        }
        return $this->respondCreated($data);

    }

    public function show()
    {
        $id = $this->request->getGetPost('id');

        $user = $this->model->find($id);

        if ($user) {
            return $this->respondCreated($user);
        } else {
            return $this->failServerError();
        }
    }

    
    public function get()
    {
        $id = $this->request->getGetPost('id');

        $user = $this->model->getData($id);

        if ($user) {
            return $this->respondCreated($user);
        } else {
            return $this->failServerError();
        }
    }

    public function save(){
        
    }
}
