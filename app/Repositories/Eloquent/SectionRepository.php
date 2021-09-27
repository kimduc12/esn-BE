<?php
namespace App\Repositories\Eloquent;

use App\Constants\SectionConst;
use App\Repositories\SectionInterface;
use App\Models\Section;

class SectionRepository implements SectionInterface {
    protected $model;
    function __construct(Section $section){
        $this->model = $section;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['position']) && $filter['position'] != '' ){
                $query = $query->where('position', $filter['position']);
            }
            if(isset($filter['type']) && $filter['type'] != '' ){
                $query = $query->where('type', $filter['type']);
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

    public function getOneByPosition($position){
        return $this->model->where('position', $position)->where('status', SectionConst::STATUS_ACTIVE)->first();
    }
}
