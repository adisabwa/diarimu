<?php

namespace Modules\Sholat\Models;

use App\Models\BaseModel;

class SholatSunnahModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu_sholat_sunnah';
        $this->selects = [
            'SUM({f}.rakaat) total_rakaat',
            'SUM({f}.rakaat) data_chart',
            "GROUP_CONCAT(CONCAT(nama_sholat,'-',{f}.rakaat) SEPARATOR '/') daftar_sholat",
        ];
        $this->relations = [
            'id_anggota' => [
                'foreign_key' => 'id_anggota',
                'table' => 'mu_anggota',
                'selects' => [
                    'nama', 'nbm',
                ]
            ],
            'id_sholat' => [
                'foreign_key' => 'id_sholat',
                'table' => 'mu__sholat_sunnah',
                'alias' => 'su',
                'selects' => [
                    'nama_sholat',
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