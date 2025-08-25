<?php

namespace Modules\Quran\Models;

use App\Models\BaseModel;

class QuranTarjamahModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu_quran_tarjamah';
        $this->selects = ['total_ayat data_chart'];
        $this->relations = [
            'id_anggota' => [
                'foreign_key' => 'id_anggota',
                'table' => 'mu_anggota',
                'selects' => [
                    'id',
                    'nama',
                ]
            ],
            'surat_mulai' => [
                'foreign_key' => 'surat_mulai',
                'table' => 'mu__surat_quran',
                'alias' => 'sq1',
                'selects' => [
                    'nama_latin nama_surat_mulai',
                ]
            ],
            'surat_selesai' => [
                'foreign_key' => 'surat_selesai',
                'table' => 'mu__surat_quran',
                'alias' => 'sq2',
                'selects' => [
                    'nama_latin nama_surat_selesai',
                ]
            ],
        ];
    }

    public function get_last($id_anggota)
    {
        $data = $this->getDataWhere(whereAnd: [ 'id_anggota' => $id_anggota], order: 'qb.tanggal desc,surat_selesai desc,surat_mulai desc');
        return $data;
    }
}