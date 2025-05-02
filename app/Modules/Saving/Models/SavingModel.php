<?php

namespace Modules\Saving\Models;

use CodeIgniter\Model;

class SavingModel extends Model
{
    protected $table         = 'das_tabungan';
    protected $primaryKey = 'id';

    protected $protectFields = false;
    protected $useAutoIncrement = true;
    // protected $returnType    = \App\Entities\Prodi::class;
    protected $returnType    = 'array';

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'created_at';

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


    public function getCount($start, $end)
    {
        $where2 = empty($start) ? '1=1' : "tanggal >= '$start'";
        $where3 = empty($end) ? '1=1' : "tanggal <= '$end'";

        return $this->db->table('mu_santri'.' s')
                    ->select("f.id_santri, f.tanggal, s.nama, s.tingkat, s.kelas, f.jenis, SUM(f.nominal) jumlah" )
                    ->join('das_tabungan'.' f',"s.id=f.id_santri")
                    ->where($where2)
                    ->where($where3)
                    ->groupBy('s.id, f.jenis, f.tanggal')
                    ->orderBy('tingkat, kelas, nama')
                    ->get()
                    ->getResult();
    }


    public function getSaldo($end)
    {
        $where3 = empty($end) ? '1=1' : "tanggal < '$end'";

        return $this->db->table('mu_santri'.' s')
                    ->select("f.id_santri, f.tanggal, s.nama, s.tingkat, s.kelas, f.jenis, SUM(f.nominal) jumlah" )
                    ->join('das_tabungan'.' f',"s.id=f.id_santri")
                    ->where($where3)
                    ->groupBy('s.id, f.jenis')
                    ->orderBy('tingkat, kelas, nama')
                    ->having("jumlah > 0")
                    ->get()
                    ->getResult();
    }
}