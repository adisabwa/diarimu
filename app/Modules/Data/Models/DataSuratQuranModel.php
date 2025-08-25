<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataSuratQuranModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu__surat_quran';
    }

    public function getOptions($where = [])
    {
      $options = [];
      $data = $this->db->table($this->table.' p')
                    ->select('*')
                    ->where($where)
                    ->get()
                    ->getResult();
      foreach ($data as $key => $d) {
        $items = [];
        foreach(range(1, $d->jumlah_ayat) as $i) {
          $items[$i] = (object)[
            'value' => "$d->id-$i",
            'label' => "$i",
          ];
        }
        $options[$d->id] = (object)[
          'value' => "$d->id",
          'label' => "$d->nama_latin",
          'options' => $items,
        ];
      }
      return $options;
    }
    
    public function countAyat($surat_mulai, $ayat_mulai, $surat_selesai, $ayat_selesai)
    {
      $total_ayat = $this->db->table($this->table.' p')
                    ->select('*, SUM(jumlah_ayat) total_ayat')
                    ->where('id >=', $surat_mulai)
                    ->where('id <', $surat_selesai)
                    ->get()
                    ->getRow();

      $total_ayat = $total_ayat->total_ayat ?? 0;
      $count = $total_ayat - $ayat_mulai + $ayat_selesai + 1;

      return $count;
    }
}