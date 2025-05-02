<?php

namespace Modules\Data\Models;

use CodeIgniter\Model;

class PelanggaranIqabModel extends Model
{
    // protected $table         = 'daiq_pelanggaran_iqab';
    // protected $primaryKey = 'id';

    // protected $protectFields = false;
    // protected $useAutoIncrement = true;
    // protected $returnType    = 'object';

    // protected $useTimestamps = true;
    // protected $dateFormat    = 'datetime';
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';

    protected function initialize()
    {

    }
    
    public function getTableName()
    {
        return $this->table;
    }
    
    public function getOptions($where = [])
    {
      $options = [];
      $data = $this->db->table("daiq_pelanggaran p")
                    ->select('p.id id_pelanggaran, i.id id_iqab, p.*, i.*')
                    ->join('daiq_iqab i',"i.tingkat_iqab LIKE CONCAT('%',p.tingkat,'%')
                      OR p.tingkat LIKE CONCAT('%',i.tingkat_iqab,'%')","LEFT")
                    ->where($where)
                    ->get()
                    ->getResult();
      // echo json_encode($data);exit;
      foreach ($data as $key => $d) {
				$option = (object)[
					'value' => "$d->id_pelanggaran-$d->id_iqab",
					'label' => $d->iqab,
				];
				if (empty($options[$d->id_pelanggaran])) {
					$options[$d->id_pelanggaran] = (object) [
						'value' => $d->id_pelanggaran,
						'label' => "Pelanggaran $d->tingkat - $d->pelanggaran",
						'options' => empty($d->iqab) ? [] : [$d->id_iqab => $option]
					];
				} else {
					$options[$d->id_pelanggaran]->options[$d->id_iqab] = $option;
				}
      }
      return $options;
    }
}