<?php

namespace Modules\Data\Models;

use CodeIgniter\Model;

class DataUnitModel extends Model
{
    protected $table      = 'mu__unit_kerja';
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
    
    
    public function getAll($whereAnd = [], $whereOr = [], $order = '')
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        $data = $this->db->table('mu__unit_kerja i')
                    ->select("i.*")
                    ->where($whereAnd)
                    ->groupStart()
                        ->orWhere($whereOr)
                    ->groupEnd()
                    ->orderBy($order)
                    ->get()
                    ->getResultObject();

        return $data;
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
          'label' => "$d->unit_kerja",
        ];
      }
      return $options;
    }
}