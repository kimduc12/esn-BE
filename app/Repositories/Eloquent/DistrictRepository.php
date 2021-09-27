<?php
namespace App\Repositories\Eloquent;

use App\Repositories\DistrictInterface;
use App\Models\District;

class DistrictRepository implements DistrictInterface {
    protected $model;
    function __construct(District $district){
        $this->model = $district;
    }

    public function getAll($filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['city_id'])){
                $city_id = $filter['city_id'];
                $query = $query->where('city_id', $city_id);
            }
        }
        return $query->orderBy('id', 'asc')->get();
    }
}
