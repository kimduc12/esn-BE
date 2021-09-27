<?php
namespace App\Services;

use App\Constants\PromotionConst;
use App\Repositories\PromotionInterface;
use Illuminate\Support\Facades\Auth;

class PromotionService extends BaseService {
    protected $promotion;
    function __construct(
        PromotionInterface $promotion
    ){
        $this->promotion = $promotion;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $promotions = $this->promotion->getAllPaginate($perPage, $filter);
        $promotions->load(['created_user']);
        return $promotions;
    }

    public function createCode($data){
        $user = Auth::user();
        $data['group_type'] = PromotionConst::GROUP_CODE;
        $data['created_by'] = $user->id;
        $promotion = $this->promotion->create($data);
        return $promotion;
    }

    public function createProgram($data){
        $user = Auth::user();
        $data['group_type'] = PromotionConst::GROUP_PROGRAM;
        $data['created_by'] = $user->id;
        $promotion = $this->promotion->create($data);
        return $promotion;
    }

    public function getByID($id){
        return $this->promotion->getByID($id);
    }

    public function updateByID($id, $data){
        $promotion = $this->promotion->getByID($id);
        if(!$promotion){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $this->promotion->updateByID($id, $data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->promotion->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }
}
