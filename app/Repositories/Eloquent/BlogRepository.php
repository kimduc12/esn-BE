<?php
namespace App\Repositories\Eloquent;

use App\Constants\BlogConst;
use App\Repositories\BlogInterface;
use App\Models\Blog;

class BlogRepository implements BlogInterface {
    protected $model;
    function __construct(Blog $blog){
        $this->model = $blog;
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
                $query = $query->whereHas('categories' , function($query) use ($category_ids) {
                    $query->whereIn('id', explode(",", $category_ids));
                });
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

    public function updateAll($data){
        return $this->model->update($data);
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
            if(isset($filter['is_hot'])){
                $query = $query->where('is_hot', $filter['is_hot']);
            }
            if(isset($filter['is_top'])){
                $query = $query->where('is_top', $filter['is_top']);
            }
            if(isset($filter['is_sub_top'])){
                $query = $query->where('is_sub_top', $filter['is_sub_top']);
            }
            if(isset($filter['category_id']) && $filter['category_id'] != 0){
                $category_id = $filter['category_id'];
                $query = $query->with(['categories' => function($query) use ($category_id) {
                    $query->where('category_id', $category_id);
                }]);
            }
        }
        $query = $query->where('status', BlogConst::STATUS_ACTIVE);
        $query = $query->published();
        return $query->orderBy('published_at', 'desc')->paginate($perPage);
    }

    public function getAll($filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['is_hot'])){
                $query = $query->where('is_hot', $filter['is_hot']);
            }
            if(isset($filter['is_top'])){
                $query = $query->where('is_top', $filter['is_top']);
            }
            if(isset($filter['is_sub_top'])){
                $query = $query->where('is_sub_top', $filter['is_sub_top']);
            }
            if(isset($filter['category_id']) && $filter['category_id'] != 0){
                $category_id = $filter['category_id'];
                $query = $query->whereHas('categories', function($query) use ($category_id) {
                    $query->where('category_id', $category_id);
                });
            }
        }
        $query = $query->where('status', BlogConst::STATUS_ACTIVE);
        $query = $query->published();
        return $query->orderBy('published_at', 'desc')->get();
    }

    public function getBySlug($slug){
        $query = $this->model;
        $query = $query->published();
        $query = $query->where('slug', $slug);
        $query = $query->where('status', BlogConst::STATUS_ACTIVE);
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
