<?php

namespace Modules\Library\Models;

use CodeIgniter\Model;

class BukuModel extends Model
{
    protected $table      = 'dalib_buku';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $protectFields = false;
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

        $data = $this->db->table('dalib_buku l')
                    ->select("l.*, jb.jenis")
                    ->join('dalib_jenis_buku jb','l.id_jenis=jb.id')
                    // ->join('daiq_pelanggaran pel','i.id_pelanggaran=pel.id')
                    // ->join('daiq_iqab iq','i.id_iqab=iq.id')
                    ->where($whereAnd)
                    ->groupStart()
                        ->orWhere($whereOr)
                    ->groupEnd()
                    ->orderBy($order)
                    ->get()
                    ->getResultObject();

        return $data;
    }

    
    public function getSummary()
    {
        return $this->db->table('dalib_buku l')
                        ->select("jb.jenis, count(l.id) jumlah")
                        ->join('dalib_jenis_buku jb','l.id_jenis=jb.id')
                        ->groupBy('jb.jenis')
                        ->get()
                        ->getResultObject();
    }
}