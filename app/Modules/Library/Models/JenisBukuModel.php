<?php

namespace Modules\Library\Models;

use CodeIgniter\Model;

class JenisBukuModel extends Model
{
    protected $table         = 'dalib_jenis_buku';
    protected $primaryKey = 'id';

    protected $protectFields = false;
    protected $useAutoIncrement = true;
    protected $returnType    = 'object';

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

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
      $data = $this->db->table($this->table)
                    ->select('*')
                    ->where($where)
                    ->get()
                    ->getResult();
      foreach ($data as $key => $d) {
        $options[] = (object)[
          'value' => "$d->id",
          'label' => $d->jenis,
        ];
      }
      return $options;
    }
}