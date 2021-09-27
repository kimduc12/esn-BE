<?php
namespace App\Repositories\Eloquent;

use App\Constants\ShippingConst;
use App\Repositories\ShippingInterface;
use App\Models\Shipping;

class ShippingRepository implements ShippingInterface {
    protected $model;
    function __construct(Shipping $shipping){
        $this->model = $shipping;
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
            if(isset($filter['order_id']) && $filter['order_id'] !=''){
                $order_id = $filter['order_id'];
                $query = $query->where('order_id', $order_id);
            }
            if(isset($filter['status']) && $filter['status'] !=''){
                $status = $filter['status'];
                $query = $query->where('status', $status);
            }
            if(isset($filter['delivery_status']) && $filter['delivery_status'] !=''){
                $delivery_status = $filter['delivery_status'];
                $query = $query->where('delivery_status', $delivery_status);
            }
            if(isset($filter['cod_status']) && $filter['cod_status'] !=''){
                $cod_status = $filter['cod_status'];
                $query = $query->where('cod_status', $cod_status);
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
