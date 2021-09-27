<?php
namespace App\Services;

use App\Events\ShippingCreated;
use App\Repositories\ShippingInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class ShippingService extends BaseService {
    protected $shipping;
    protected $product;
    function __construct(
        ShippingInterface $shipping
    ){
        $this->shipping = $shipping;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $shippings = $this->shipping->getAllPaginate($perPage, $filter);
        $shippings->load(['order']);
        return $shippings;
    }

    public function create($data){
        try {
            $data['shipping_code'] = IdGenerator::generate(['table' => 'shippings', 'length' => 10, 'prefix' => 'SP'.date('Y')]);
            $shipping = $this->shipping->create($data);
            ShippingCreated::dispatch($shipping);
            return $shipping;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getByID($id){
        $shipping = $this->shipping->getByID($id);
        $shipping->load(['order']);
        return $shipping;
    }

    public function updateByID($id, $data){
        $shipping = $this->shipping->getByID($id);
        if(!$shipping){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $this->shipping->updateByID($id, $data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->shipping->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }
}
