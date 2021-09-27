<?php
namespace App\Repositories\Eloquent;

use App\Repositories\WardInterface;
use App\Models\Ward;

class WardRepository implements WardInterface {
    protected $model;
    function __construct(Ward $ward){
        $this->model = $ward;
    }

    public function getAll($filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['district_id'])){
                $district_id = $filter['district_id'];
                $query = $query->where('district_id', $district_id);
            }
        }
        return $query->orderBy('id', 'asc')->get();
    }
}
