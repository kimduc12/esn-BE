<?php
namespace App\Repositories\Eloquent;

use App\Constants\GiftConst;
use App\Repositories\GiftInterface;
use App\Models\Gift;

class GiftRepository implements GiftInterface {
    protected $model;
    function __construct(Gift $gift){
        $this->model = $gift;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
        }
        return $query->orderBy('sort', 'asc')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getTotalPrice($filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
        }
        return $query->sum('price');
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

    public function getActiveListPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
        }
        $query = $query->canShowInFE();
        return $query->orderBy('sort', 'asc')->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
