<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class GroupActivityModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu_group_activity';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->unit_kerja; });
    }
}
