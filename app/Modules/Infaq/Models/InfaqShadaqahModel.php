<?php

namespace Modules\Infaq\Models;

use App\Models\BaseModel;

class InfaqShadaqahModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu_infaq_shadaqah';
        $this->selects = [
            '{n} 1 as count',
            'jumlah data_chart',
        ];
        $this->relations = [
            'id_anggota' => [
                'foreign_key' => 'id_anggota',
                'table' => 'mu_anggota',
                'selects' => [
                    'nama', 'nbm',
                ]
            ],
        ];
    }

    public function get_last($id_anggota)
    {
        $data = $this->getDataWhere(whereAnd: [ 'id_anggota' => $id_anggota], order: 'tanggal desc');
        return $data;
    }
}