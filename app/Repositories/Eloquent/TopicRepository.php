<?php
namespace App\Repositories\Eloquent;

use App\Constants\TopicConst;
use App\Repositories\TopicInterface;
use App\Models\Topic;

class TopicRepository implements TopicInterface {
    protected $model;
    function __construct(Topic $topic){
        $this->model = $topic;
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
            if(isset($filter['category_ids']) && $filter['category_ids'] != ''){
                $category_ids = $filter['category_ids'];
                $query = $query->whereHas('categories', function($query) use ($category_ids) {
                    $query->whereIn('id', explode(",", $category_ids));
                });
            }
        }
        return $query->orderBy('sort', 'asc')->orderBy('created_at', 'desc')->paginate($perPage);
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

    public function update($data){
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
            if(isset($filter['category_id']) && $filter['category_id'] != 0){
                $category_id = $filter['category_id'];
                $query = $query->whereHas('categories', function($query) use ($category_id) {
                    $query->where('category_id', $category_id);
                });
            }
        }
        $query = $query->showed();
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
                $query = $query->whereHas('categories', function($query) use ($category_id) {
                    $query->where('category_id', $category_id);
                });
            }
        }
        $query = $query->showed();
        return $query->orderBy('sort', 'asc')->orderBy('created_at', 'desc')->get();
    }

    public function getOneActive(){
        return $this->model->actived()->showed()->first();
    }
}
