<?php

namespace Modules\Sholat\Models;

use CodeIgniter\Model;

class SholatWajibModel extends Model
{
    protected $table         = 'mu_sholat_wajib';
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

        return $this->db->table('das_tabungan'.' f')
                    ->select('f.*, s.nama, s.tingkat, s.kelas')
                    ->join('mu_santri'.' s','s.id=f.id_santri')
                    ->where($whereAnd)
                    ->groupStart()
                        ->orWhere($whereOr)
                    ->groupEnd()
                    ->orderBy($order)
                    ->get()
                    ->getResult();
    }
}