<?php
namespace App\Repositories\Eloquent;

use App\Constants\PromotionConst;
use App\Repositories\PromotionInterface;
use App\Models\Promotion;
use Carbon\Carbon;

class PromotionRepository implements PromotionInterface {
    protected $model;
    function __construct(Promotion $promotion){
        $this->model = $promotion;
    }

    public function getAll($filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['group_type']) && $filter['group_type'] !=''){
                $group_type = $filter['group_type'];
                $query = $query->where('group_type', $group_type);
            }
            if(isset($filter['is_active']) && $filter['is_active'] !=''){
                $is_active = $filter['is_active'];
                if ($is_active == true) {
                    $query = $query->where(function($q){
                        $q->where(function($q){
                            $q->where('start_datetime', '<=', Carbon::now());
                            $q->where(function($q){
                                $q->whereNull('end_datetime');
                                $q->orWhere('is_never_expired', 1);
                            });
                        });

                        $q->orWhere(function($q){
                            $q->where('start_datetime', '<=', Carbon::now());
                            $q->where('end_datetime', '>=', Carbon::now());
                        });
                    });
                } else {
                    $query = $query->where('start_datetime', '>', Carbon::now());
                }
            }
        }
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where(function($q) use ($keyword) {
                    $q = $q->where('name', 'like', '%'.$keyword.'%');
                    $q = $q->orWhere('code', 'like', '%'.$keyword.'%');
                });
            }
            if(isset($filter['group_type']) && $filter['group_type'] !=''){
                $group_type = $filter['group_type'];
                $query = $query->where('group_type', $group_type);
            }
            if(isset($filter['created_by']) && $filter['created_by'] !=''){
                $created_by = $filter['created_by'];
                $query = $query->where('created_by', $created_by);
            }
            if(isset($filter['is_active']) && $filter['is_active'] !=''){
                $is_active = $filter['is_active'];
                if ($is_active == true) {
                    $query = $query->where(function($q){
                        $q->where(function($q){
                            $q->where('start_datetime', '<=', Carbon::now());
                            $q->where(function($q){
                                $q->whereNull('end_datetime');
                                $q->orWhere('is_never_expired', 1);
                            });
                        });

                        $q->orWhere(function($q){
                            $q->where('start_datetime', '<=', Carbon::now());
                            $q->where('end_datetime', '>=', Carbon::now());
                        });
                    });
                } else {
                    $query = $query->where('start_datetime', '>', Carbon::now());
                }
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

    public function getByCode($code){
        return $this->model->where('code', $code)->first();
    }

    public function updateByID($id,$data){
        return $this->model->find($id)->update($data);
    }

    public function destroyByIDs($arrID){
        return $this->model->destroy($arrID);
    }
}
