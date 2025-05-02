<?php

namespace Modules\Data\Models;

use CodeIgniter\Model;

class PcmModel extends Model
{
    protected $table         = 'mu_pcm';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType    = 'object';

    protected $protectFields = false;
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
      $data = $this->db->table('mu_pcm p')
                    ->select('*')
                    ->where($where)
                    ->get()
                    ->getResult();
      foreach ($data as $key => $d) {
        $options[] = (object)[
          'value' => "$d->id",
          'label' => "$d->pcm"
        ];
      }
      return $options;
    }
}