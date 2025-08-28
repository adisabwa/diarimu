<?php

namespace Modules\Sholat\Models;

use App\Models\BaseModel;

class SholatWajibModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu_sholat_wajib';
        $this->selects = ['total_score data_chart'];
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

    public function get_best($id_anggota)
    {
        $data = $this->getDataWhere(whereAnd: [ 'id_anggota' => $id_anggota], order: 'total_score desc');
        return $data;
    }

    public function update_total_score($id)
    {
        return $this->db->query("UPDATE $this->table
                        SET total_score = (
                            IF(shubuh IS NULL, 0, shubuh) +
                            IF(dhuhur IS NULL, 0, dhuhur) +
                            IF(asar IS NULL, 0, asar) +
                            IF(maghrib IS NULL, 0, maghrib) +
                            IF(isya IS NULL, 0, isya)
                        )
                        WHERE id = $id");
    }
}