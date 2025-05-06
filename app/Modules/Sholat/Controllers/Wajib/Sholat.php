<?php

namespace Modules\Sholat\Controllers\Wajib;

use Modules\Data\Controllers\BaseData;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;

class Sholat extends BaseData
{

    public function __construct()
    {
        $this->model = model('SholatWajibModel');
    }
    
}