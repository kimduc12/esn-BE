<?php
namespace App\Repositories\Eloquent;

use App\Constants\GiftExchangeConst;
use App\Repositories\GiftExchangeInterface;
use App\Models\GiftExchange;

class GiftExchangeRepository implements GiftExchangeInterface {
    protected $model;
    function __construct(GiftExchange $giftExchange){
        $this->model = $giftExchange;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['user_id']) && $filter['user_id'] != '' ){
                $query = $query->where('user_id', $filter['user_id']);
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
}
