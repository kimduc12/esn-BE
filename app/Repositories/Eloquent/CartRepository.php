<?php
namespace App\Repositories\Eloquent;

use App\Repositories\CartInterface;
use App\Models\Cart;

class CartRepository implements CartInterface {
    protected $model;
    function __construct(Cart $cart){
        $this->model = $cart;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['user_id']) && $filter['user_id'] != '' ){
                $query = $query->where('user_id', $filter['user_id']);
            }
        }
        return $query->orderBy('created_at', 'asc')->paginate($perPage);
    }

    public function create($data){
        return $this->model->create($data);
    }

    public function getByID($id){
        return $this->model->where('id', $id)->first();
    }

    public function getByUserID($user_id){
        return $this->model->where('user_id', $user_id)->first();
    }

    public function updateByID($id,$data){
        return $this->model->find($id)->update($data);
    }

    public function destroyByIDs($arrID){
        return $this->model->destroy($arrID);
    }

    public function destroyByID($id){
        return $this->model->destroy($id);
    }
}
