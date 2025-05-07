<?php

namespace Modules\Sholat\Models;

use CodeIgniter\Model;

class SholatSunnahModel extends Model
{
    protected $table         = 'mu_sholat_sunnah';
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

    public function getAll($whereAnd = [], $whereOr = [], $order = '')
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        return $this->db->table('mu_sholat_sunnah f')
                    ->select('f.*, s.nama, s.nbm')
                    ->join('mu_anggota'.' s','s.id=f.id_anggota')
                    ->where($whereAnd)
                    ->groupStart()
                        ->orWhere($whereOr)
                    ->groupEnd()
                    ->orderBy($order)
                    ->get()
                    ->getResult();
    }
    
}