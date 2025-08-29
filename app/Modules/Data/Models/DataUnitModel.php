<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataUnitModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu__unit_kerja';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, 
        function($d) { return $d->unit_kerja; },
        function($option, $data) { 
          $option->bidang = $data->bidang;
          return $option;
        }
      );
    }
}