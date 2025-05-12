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

    public function show()
    {
        $id = $this->request->getGetPost('id');

        $user = $this->model->getData($id);

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
