<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataJuzModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu__juz_quran';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, 
        function($d) { return $d->id; },
        function($option, $d) { 
          $option->surat_mulai = $d->surat_mulai;
          $option->ayat_mulai = $d->ayat_mulai;
          $option->surat_selesai = $d->surat_selesai;
          $option->ayat_selesai = $d->ayat_selesai;
          $option->juz = $d->juz;
          $option->halaman_mulai = $d->halaman_mulai;
          $option->halaman_selesai = $d->halaman_selesai;

          return $option;
        }
      );
    }
}