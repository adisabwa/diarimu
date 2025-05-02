<?php

namespace Modules\Library\Controllers\Admin;

use Modules\Data\Controllers\BaseData;
use App\Libraries\PdfBuilder;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use Config\Upload;
use stdClass;

class JenisBuku extends BaseData
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('JenisBukuModel');
    }

}
