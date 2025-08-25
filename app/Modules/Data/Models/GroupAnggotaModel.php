<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class GroupAnggotaModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu_group_anggota';
        $this->relations = [
            'id_anggota' => [
                'foreign_key' => 'id_anggota',
                'table' => 'mu_anggota',
                'selects' => [
                    'nama',
                    'id_unit',
                ]
            ],
        ];
    }
}