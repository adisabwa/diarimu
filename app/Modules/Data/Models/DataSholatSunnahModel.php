<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataSholatSunnahModel  extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu__sholat_sunnah';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama_sholat; });
    }
}