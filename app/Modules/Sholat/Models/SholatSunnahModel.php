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

    public function get_last($id_anggota)
    {
        return $this->db->table('mu_sholat_sunnah f')
                    ->select('f.*, s.nama, s.nbm')
                    ->join('mu_anggota'.' s','s.id=f.id_anggota')
                    ->where('f.id_anggota', $id_anggota)
                    ->orderBy('tanggal desc')
                    ->get()
                    ->getRow();
    }

    public function get_best($id_anggota)
    {
        return $this->db->table('mu_sholat_sunnah f')
                    ->select('f.*, s.nama, s.nbm')
                    ->join('mu_anggota'.' s','s.id=f.id_anggota')
                    ->where('f.id_anggota', $id_anggota)
                    ->orderBy('total_score desc')
                    ->get()
                    ->getRow();
    }

    public function update_total_score($id)
    {
        return $this->db->query("UPDATE $this->table
                        SET total_score = (
                            IF(shubuh IS NULL, 0, shubuh) +
                            IF(dhuhur IS NULL, 0, dhuhur) +
                            IF(asar IS NULL, 0, asar) +
                            IF(maghrib IS NULL, 0, maghrib) +
                            IF(isya IS NULL, 0, isya)
                        )
                        WHERE id = $id");
    }
}