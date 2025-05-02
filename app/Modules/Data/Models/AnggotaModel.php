<?php

namespace Modules\Data\Models;

use CodeIgniter\Model;

class AnggotaModel extends Model
{
    protected $table         = 'mu_anggota';
    protected $primaryKey = 'id';

    protected $protectFields = false;
    protected $useAutoIncrement = true;
    // protected $returnType    = \App\Entities\Prodi::class;
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

        $data = $this->db->table('mu_anggota i')
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
      $data = $this->db->table('mu_anggota p')
                    ->select('*')
                    ->where($where)
                    ->orderBy('nama')
                    ->get()
                    ->getResult();
                    
      foreach ($data as $key => $d) {
        $options[] = (object)[
          'value' => "$d->id",
          'label' => "$d->nama"
        ];
      }
      return $options;
    }
    
}