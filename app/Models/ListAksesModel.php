<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class ListAksesModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu_list_akses';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama_app; });
    }
}