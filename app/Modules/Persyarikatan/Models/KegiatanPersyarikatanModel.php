<?php

namespace Modules\Persyarikatan\Models;

use App\Models\BaseModel;

class KegiatanPersyarikatanModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu_kegiatan_persyarikatan';
        $this->selects = [
            '{n} 1 as data_chart'
        ];
        $this->relations = [
            'id_anggota' => [
                'foreign_key' => 'id_anggota',
                'table' => 'mu_anggota',
                'selects' => [
                    'nama','nbm'
                ]
            ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->unit_kerja; });
    }

    public function getOptionsTipe()
    {
        $data = $this->db->table($this->table)
                        ->select('*')
                        ->groupBy('tipe')
                        ->get()
                        ->getResult();
        
        $options = [];
        foreach ($data as $key => $val) {
            $options[] = (object)[
                'value' => $val->tipe,
                'label' => ucfirst($val->tipe),
            ];
        }

        if (empty($options)) 
            $options = [[
                'value' => 'kajian',
                'label' => 'Persyarikatan',
            ]];


        return $options;
    }
}