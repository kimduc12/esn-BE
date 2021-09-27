<?php
namespace App\Repositories\Eloquent;

use App\Constants\BannerConst;
use App\Repositories\BannerInterface;
use App\Models\Banner;

class BannerRepository implements BannerInterface {
    protected $model;
    function __construct(Banner $banner){
        $this->model = $banner;
    }

    public function getAllListBannerPaginate($perPage = 20,$filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['type'])){
                $query = $query->where('type',$filter['type']);
            }
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getListByType($type){
        return $this->model->whereType($type)
                    ->actived()
                    ->published()
                    ->showed()
                    ->get();
    }

    public function createNewBanner($data){
        return $this->model->create($data);
    }

    public function getBannerByID($id){
        return $this->model->where('id',$id)->first();
    }

    public function updateBannerByID($id,$data){
        return $this->model->find($id)->update($data);
    }

    public function destroyBannerByIDs($arrID){
        return $this->model->destroy($arrID);
    }

}
