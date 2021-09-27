<?php
namespace App\Repositories;

interface SettingInterface {

    public function create($data);

    public function update($id, $data);

}
