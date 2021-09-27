<?php
namespace App\Repositories\Eloquent;

use App\Constants\BlogCategoryConst;
use App\Repositories\BlogCategoryInterface;
use App\Models\BlogCategory;

class BlogCategoryRepository implements BlogCategoryInterface {
    protected $model;
    function __construct(BlogCategory $category){
        $this->model = $category;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['status']) && $filter['status'] != '' ){
                $query = $query->where('status', $filter['status']);
            }
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create($data){
        return $this->model->create($data);
    }

    public function getByID($id){
        return $this->model->where('id',$id)->first();
    }

    public function updateByID($id,$data){
        return $this->model->find($id)->update($data);
    }

    public function destroyByIDs($arrID){
        return $this->model->destroy($arrID);
    }

    public function getAll($filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['parent_id'])){
                $query = $query->where('parent_id', $filter['parent_id']);
            }
        }
        $query = $query->where('status', BlogCategoryConst::STATUS_ACTIVE);
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getBySlug($slug){
        $query = $this->model;
        $query = $query->where('slug', $slug);
        $query = $query->where('status', BlogCategoryConst::STATUS_ACTIVE);
        return $query->first();
    }

    public function checkSlug($slug, $id = 0){
        $query = $this->model->where('slug', $slug);
        if ($id != 0) {
            $query = $query->where('id', '!=',  $id);
        }
        return $query->first();
    }
}
