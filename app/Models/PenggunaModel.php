<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class PenggunaModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu_anggota';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama; });
    }
}