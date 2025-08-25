<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataBacaanDzikirSholatModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu__dzikir_sholat';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama; });
    }
}
