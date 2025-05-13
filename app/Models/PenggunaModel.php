<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggunaModel extends Model
{
    protected $table         = 'mu_pengguna';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    // protected $returnType    = \App\Entities\Pengguna::class;
    protected $returnType    = 'object';

    protected $protectFields = false;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // protected $db;

    protected function initialize()
    {
        // $this->db = $this->builder();
    }
    
    public function getTableName()
    {
        return $this->table;
    }

    public function login($email = '', $no_hp = '', $password = '')
    {
        $data = $this->db->table('mu_pengguna p')
                    ->select("ang.*, p.*, IF(ga.id IS NULL,'0','1') is_mentor")
                    ->join("mu_anggota ang","ang.id=p.id_anggota")
                    ->join("mu_group_anggota ga","ga.id_anggota=ang.id AND ga.type='mentor'","left")
                    ->groupStart()
                      ->where('ang.no_hp', $no_hp)
                      ->orWhere('ang.email', $email)
                    ->groupEnd()
                    ->where('p.password', $password)
                    ->groupBy('p.id')
                    ->get()
                    ->getRow();
        
        if (!empty($data)) {
          $allowed_roles = ['user'];
          if ($data->role == 'admin')
            $allowed_roles[] = 'admin';
          if ($data->is_mentor == '1')
            $allowed_roles[] = 'mentor';
          $data->allowed_roles = $allowed_roles;
        }
      // var_dump($this->db->getLastQuery(), $data);        
        return $data;
    }
    
    public function getOptions($where = [])
    {
      $options = [];
      $data = $this->where($where)
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

    public function getAll($where = [], $order = '')
    {
      $data = $this->db->table('mu_pengguna p')
                    ->select("p.*")
                    ->where($where)
                    ->groupBy('p.id')
                    ->orderBy($order)
                    ->get()
                    ->getResult();

      return $data;
    }

    
    public function getData($id)
    {
      $data = $this->db->table('mu_pengguna p')
                  ->select("ang.*, p.*")
                  ->join("mu_anggota ang","ang.id=p.id_anggota")
                  ->where("p.id", $id)
                  ->groupBy('p.id')
                  ->get()
                  ->getRow();
                  
      return $data;
    }
}