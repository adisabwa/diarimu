<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataBacaanDoaModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu__doa_harian';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama; });
    }
}