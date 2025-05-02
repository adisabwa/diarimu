<?php

namespace Modules\Iqab\Models;

use CodeIgniter\Model;

class ListIqabModel extends Model
{
    protected $table         = 'daiq_list_iqab';
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

        $data = $this->db->table('daiq_list_iqab i')
                    ->select("i.*, s.nama, s.kelas, pel.pelanggaran, pel.tingkat, iq.iqab")
										->join('mu_santri s','i.id_santri=s.id')
										->join('daiq_pelanggaran pel','i.id_pelanggaran=pel.id')
										->join('daiq_iqab iq','i.id_iqab=iq.id')
                    ->where($whereAnd)
                    ->groupStart()
                        ->orWhere($whereOr)
                    ->groupEnd()
                    ->orderBy($order)
                    ->get()
                    ->getResultObject();

        return $data;
    }

    
    public function getSummary($where = [])
    {
        return $this->db->table('daiq_list_iqab li')
                        ->select("q.tingkat_iqab, tanggal, count(li.id) jumlah")
                        ->join("daiq_iqab q","q.id=li.id_iqab")
                        ->where($where)
                        ->groupBy('LEFT(tanggal, 7), tingkat_iqab')
                        ->orderBy('tanggal, tingkat_iqab')
                        ->get()
                        ->getResultObject();
    }
}