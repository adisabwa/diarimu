<?php

namespace Modules\Infaq\Controllers\Shadaqah;

use App\Controllers\BasePageController;

class InfaqController extends BasePageController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('InfaqShadaqahModel');
    }
    
    public function get_last()
    {
        $postData = $this->request->getGetPost();
        $id_anggota = $postData['id_anggota'] ?? userdata()->id_anggota;
        return $this->respondCreated($this->model->get_last($id_anggota));
    }
    
    public function dashboard()
    {
        return $this->createChart();
    }

    public function get_before()
    {
        return parent::get_before();
    }

    public function download()
    {
        $label = $this->request->getGetPost('label') ?? 'Nominal';
        return $this->downloadData('DATA-INFAQ', "Tanggal / $label Infaq");
    }
}