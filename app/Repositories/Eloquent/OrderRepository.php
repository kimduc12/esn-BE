<?php
namespace App\Repositories\Eloquent;

use App\Constants\OrderConst;
use App\Repositories\OrderInterface;
use App\Models\Order;

class OrderRepository implements OrderInterface {
    protected $model;
    function __construct(Order $order){
        $this->model = $order;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->whereHas('customer', function($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            }
            if(isset($filter['user_id']) && $filter['user_id'] !=''){
                $user_id = $filter['user_id'];
                $query = $query->where('user_id', $user_id);
            }
            if(isset($filter['is_verified']) && $filter['is_verified'] !=''){
                $is_verified = $filter['is_verified'];
                $query = $query->where('is_verified', $is_verified);
            }
            if(isset($filter['channel']) && $filter['channel'] !=''){
                $channel = $filter['channel'];
                $query = $query->where('channel', $channel);
            }
            if(isset($filter['status']) && $filter['status'] !=''){
                $status = $filter['status'];
                $query = $query->where('status', $status);
            }
            if(isset($filter['paid_status']) && $filter['paid_status'] !=''){
                $paid_status = $filter['paid_status'];
                $query = $query->where('paid_status', $paid_status);
            }
            if(isset($filter['delivery_status']) && $filter['delivery_status'] !=''){
                $delivery_status = $filter['delivery_status'];
                $query = $query->whereHas('shipping', function($q) use ($delivery_status) {
                    $q->whereDeliveryStatus($delivery_status);
                });
            }
            if(isset($filter['cod_status']) && $filter['cod_status'] !=''){
                $cod_status = $filter['cod_status'];
                $query = $query->whereHas('shipping', function($q) use ($cod_status) {
                    $q->whereCodStatus($cod_status);
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

    public function destroyByIDs($arrID){
        return $this->model->destroy($arrID);
    }
}
