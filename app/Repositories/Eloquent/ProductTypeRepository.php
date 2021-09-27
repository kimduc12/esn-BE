<?php
namespace App\Repositories\Eloquent;

use App\Constants\ProductTypeConst;
use App\Repositories\ProductTypeInterface;
use App\Models\ProductType;

class ProductTypeRepository implements ProductTypeInterface {
    protected $model;
    function __construct(ProductType $productType){
        $this->model = $productType;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model->with(['categories']);
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['status']) && $filter['status'] != '' ){
                $query = $query->where('status', $filter['status']);
            }
            if(isset($filter['category_ids']) && $filter['category_ids'] != ''){
                $category_ids = $filter['category_ids'];
                $query = $query->whereHas('categories', function($q) use ($category_ids) {
                    $q->whereIn('id', explode(",", $category_ids));
                });
            }
        }
        return $query->orderBy('sort', 'asc')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create($data){
        return $this->model->create($data);
    }

    public function getByID($id){
        return $this->model->with(['categories'])->where('id', $id)->first();
    }

    public function updateByID($id,$data){
        return $this->model->find($id)->update($data);
    }

    public function destroyByIDs($arrID){
        return $this->model->destroy($arrID);
    }

    public function getListPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['category_id']) && $filter['category_id'] != 0){
                $category_id = $filter['category_id'];
                $query = $query->with(['categories' => function($q) use ($category_id) {
                    $q->where('id', $category_id);
                }]);
            }
        }
        $query = $query->where('status', ProductTypeConst::STATUS_ACTIVE);
        return $query->orderBy('sort', 'asc')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getAll($filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['category_id']) && $filter['category_id'] != 0){
                $category_id = $filter['category_id'];
                $query = $query->whereHas('categories', function($q) use ($category_id) {
                    $q->where('id', $category_id);
                });
            }
        }
        $query = $query->where('status', ProductTypeConst::STATUS_ACTIVE);
        return $query->orderBy('sort', 'asc')->orderBy('created_at', 'desc')->get();
    }
}
