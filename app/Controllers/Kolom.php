<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use stdClass;

class Kolom extends BaseController
{
    protected $kolomModel;

    public function __construct()
    {      
      $this->kolomModel = model('KolomModel');
    }

    public function preparation($table = NULL, $return_data = FALSE, $grouping = NULL)
    {
        $input = empty($this->request) ? '1' : $this->request->getGet('input');
        $input = ($input ?? '1') == '1';
        $table = $table ?? $this->request->getGet('table');
        $grouping = ($grouping ?? $this->request->getGet('grouping')) != '0';
        $datas = $this->kolomModel->getAll($table, $input);

        $results = [];
        // echo json_encode($datas);exit;
        foreach ($datas as $key => $data) {
          $options = [];
          if (!empty($data->pilihan)) {
            try {
                $options = unserialize($data->pilihan);
            } catch (\Throwable $e) {
                $pilihan = explode(',',$data->pilihan);
                if (count($pilihan) > 1) {
                    foreach ($pilihan as $opt) {
                        $options[] = (object)[
                            'value'   => $opt,
                            'label'   => ucwords($opt),
                        ];
                    }
                } else {
                    $model = $data->pilihan;
                    $model = model($model);
                    $options = $model->getOptions() ?? [];
                }
            }
            $data->options = $options;
            $data->allow_add = $data->allow_add == '1';
            if ($data->allow_add){
                // echo json_encode($data);exit;
                $data->isAdding = false;
                $data->optionName = '';
                $data->optionSave = '';
                $data->addHref = $data->add_href;
                $data->addReset = $data->add_reset;
                $data->addFields = empty($model) ? [] : $this->preparation($model->getTableName(), TRUE, '0');
                // echo json_encode($data);exit;
            }
            if ($data->input == 'select-double') {
              $data->parentSelect = '';
              $labels = explode(' dan ', $data->label);
              $data->label1 = $labels[0] ?? $data->label;
              $data->label2 = $labels[1] ?? $data->label;
            }
          }
          
          $data->change = true;
          if ($data->sortable == '1') {
            $data->sort = '';
          }

          if ($grouping) {
            if (empty($results[ $data->group ])) {
              $result = new stdClass;
              $result->label = "Data ".ucwords($data->group);
              $result->group = $data->group;
              $result->group_icon = $data->group_icon;
              $result->children = [$data->nama_kolom => $data];
              $results[$data->group] = $result;
            } else {
              $results[$data->group]->children[$data->nama_kolom] = $data;
            }
          } else {
            $results[$data->nama_kolom] = $data;
          }
        }


        if ($return_data) return $results;


        return $this->respondCreated($results);
    }

    public function getModelFromTable($tableName)
    {
        // Convert table name to model name
        // Example: 'users' → 'UserModel'
        $tableName = preg_replace('/^[^_]+_/', '', $tableName);
        $tableName = str_replace('_', ' ', $tableName);
        $tableName = ucwords($tableName);
        $tableName = str_replace(' ', '', $tableName);
        $modelName = ucfirst($tableName) . 'Model';

        // Check if the model class exists
        return model($modelName);
    }

    public function getKolomDetail($table, $nama_kolom){
      $data = $this->kolomModel->getKolom($table, $nama_kolom);
      // var_dump($table, $nama_kolom, $data);
      return $data;
    }

}