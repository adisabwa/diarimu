<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class GroupModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'mu_group';
        $this->selects = ['id'];
        $this->relations = [
            'id_group' => [
                'foreign_key' => 'id_group',
                'table' => 'mu_group_anggota',
                'alias' => 'ga',
                'selects' => [
                    '*',
                    'id id_ga',
                ]
            ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama_group; });
    }
    
    public function getAll(
        array $whereAnd = [], 
        array $whereOr = [], 
        array $whereIn = [], 
        array $orWhereIn = [], 
        array $groupBy = [],  
        string $order = '', 
        int $limit = 0, 
        int $offset = 0,  
        $relations = NULL)
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        $subQuery =  $this->db->table('mu_group g')
                              ->select('g.*, u.unit_kerja, u.bidang')
                              ->join('mu_group_anggota ga','ga.id_group=g.id')
                              ->join('mu__unit_kerja u','g.id_unit=u.id','left')
                              ->where($whereAnd)
                              ->groupStart()
                                  ->orWhere($whereOr)
                              ->groupEnd();

        foreach ($whereIn as $key => $value) {
           $subQuery = $subQuery->whereIn($key, $value);
        }
          $subQuery = $subQuery->groupBy('g.id')
                              ->limit($limit, $offset);
        // var_dump($subQuery->getCompiledSelect());
        $data = $this->db->table('mu_group_anggota ga')
                    ->select("ga.id_group, ga.id_anggota, ga.type, g.*, ga.id id_ga, s.nama, '' as tanggal")
                    ->join("({$subQuery->getCompiledSelect()}) g",'ga.id_group=g.id')
                    ->join('mu_anggota s','ga.id_anggota=s.id')
                    // ->where($whereAnd)
                    // ->groupStart()
                    //     ->orWhere($whereOr)
                    // ->groupEnd()
                    ->orderBy($order)
                    ->get()
                    ->getResultObject();

        return $data;
    }
}