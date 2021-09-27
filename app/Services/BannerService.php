<?php
namespace App\Services;

use App\Helpers\CustomFunctions;
use App\Repositories\BannerInterface;
use App\Models\Banner;

class BannerService extends BaseService {
    protected $banner;
    function __construct(
        BannerInterface $banner
    ){
        $this->banner = $banner;
    }

    public function getAllListBannerPaginate($perPage = 20,$filter){
        return $this->banner->getAllListBannerPaginate($perPage,$filter);
    }

    public function getListByType($type){
        return $this->banner->getListByType($type);
    }

    public function createNewBanner($data){
       return $this->banner->createNewBanner($data);
    }

    public function getBannerByID($id){
        return $this->banner->getBannerByID($id);
    }

    public function updateBannerByID($id,$data){
        $banners = $this->banner->getBannerByID($id);
        if(!$banners){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $this->banner->updateBannerByID($id,$data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyBannerByIDs($arrID){
        $result =$this->banner->destroyBannerByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }


}
