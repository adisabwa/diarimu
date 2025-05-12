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
}