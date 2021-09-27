<?php
namespace App\Repositories\Eloquent;

use App\Constants\CountryConst;
use App\Repositories\CountryInterface;
use App\Models\Country;

class CountryRepository implements CountryInterface {
    protected $model;
    function __construct(Country $country){
        $this->model = $country;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create($data){
        return $this->model->create($data);
    }

    public function getByID($id){
        return $this->model->where('id', $id)->first();
    }

    public function updateByID($id,$data){
        return $this->model->find($id)->update($data);
    }

    public function destroyByIDs($arrID){
        return $this->model->destroy($arrID);
    }

    public function getAllActive($filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
        }
        $query = $query->where('status', CountryConst::STATUS_ACTIVE);
        return $query->orderBy('sort', 'asc')->orderBy('created_at', 'desc')->get();
    }
}
