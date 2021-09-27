<?php
namespace App\Repositories\Eloquent;


use App\Repositories\SettingInterface;
use App\Models\Setting;


class SettingRepository implements SettingInterface {
    protected $model;
    function __construct(Setting $setting){
        $this->model = $setting;
    }

    public function create($data){
        return $this->model->create($data);
    }

    public function update($id, $data){
        return $this->model->find($id)->update($data);
    }




}
