<?php
namespace App\Repositories\Eloquent;

use App\Constants\PageConst;
use App\Repositories\PageInterface;
use App\Models\Page;

class PageRepository implements PageInterface {
    protected $model;
    function __construct(Page $page){
        $this->model = $page;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['type']) && $filter['type'] !=''){
                $type = $filter['type'];
                $query = $query->where('type', $type);
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
            if(isset($filter['type']) && $filter['type'] !=''){
                $type = $filter['type'];
                $query = $query->where('type', $type);
            }
        }
        $query = $query->where('status', PageConst::STATUS_ACTIVE);
        return $query->orderBy('sort', 'asc')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getAll($filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['type']) && $filter['type'] !=''){
                $type = $filter['type'];
                $query = $query->where('type', $type);
            }
        }
        $query = $query->where('status', PageConst::STATUS_ACTIVE);
        return $query->orderBy('sort', 'asc')->orderBy('created_at', 'desc')->get();
    }

    public function getBySlug($slug){
        $query = $this->model;
        $query = $query->where('slug', $slug);
        $query = $query->where('status', PageConst::STATUS_ACTIVE);
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
