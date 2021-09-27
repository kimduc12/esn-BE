<?php
namespace App\Repositories\Eloquent;

use App\Repositories\CityInterface;
use App\Models\City;

class CityRepository implements CityInterface {
    protected $model;
    function __construct(City $city){
        $this->model = $city;
    }

    public function getAll(){
        $query = $this->model;
        return $query->orderBy('id', 'asc')->get();
    }
}
