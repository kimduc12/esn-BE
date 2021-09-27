<?php
namespace App\Services;

use App\Constants\OrderConst;
use App\Events\OrderCreated;
use App\Repositories\OrderInterface;
use App\Repositories\ProductInterface;
use App\Repositories\CartInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class OrderService extends BaseService {
    protected $order;
    protected $product;
    protected $cart;
    function __construct(
        OrderInterface $order,
        ProductInterface $product,
        CartInterface $cart
    ){
        $this->order = $order;
        $this->product = $product;
        $this->cart = $cart;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $orders = $this->order->getAllPaginate($perPage, $filter);
        $orders->load(['customer', 'shipping', 'details']);
        return $orders;
    }

    public function create($data){
        try {
            DB::beginTransaction();
            $details = $data['details'];
            unset($data['details']);
            $data['channel'] = OrderConst::CHANNEL_PORTAL;
            $data['order_code'] = IdGenerator::generate(['table' => 'orders', 'field' => 'order_code', 'length' => 10, 'prefix' => 'DH'.date('Y')]);
            $order = $this->order->create($data);
            $total_price_order = 0;
            foreach ($details as $detail) {
                $product = $this->product->getByID($detail['product_id']);
                $product_option = $product->product_options()->find($detail['product_option_id']);
                $detail['sku'] = $product_option->sku;
                $detail['price'] = $product_option->price;
                $detail['total_price'] = $detail['price'] * $detail['quantity'];
                $attributes = $product_option->getEntityAttributes()->keys()->all();
                $options = [];
                foreach ($attributes as $attribute) {
                    $options[$attribute] = $product_option->{$attribute};
                }
                $detail['options'] = $options;
                //dd($detail);
                $order->details()->create($detail);
                $total_price_order += $detail['total_price'];
            }
            if ($data['is_gift_wrapping'] == true) {
                $total_price_order += OrderConst::GIFT_WRAPPING_PRICE;
            }
            $this->order->updateByID($order->id, [
                'total_price' => $total_price_order
            ]);
            DB::commit();
            OrderCreated::dispatch($order);
            return $order;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getByID($id){
        $order = $this->order->getByID($id);
        $order->load(['customer', 'details', 'shipping']);
        return $order;
    }

    public function updateByID($id, $data){
        $order = $this->order->getByID($id);
        if(!$order){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $this->order->updateByID($id, $data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->order->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getMyPaginate($perPage = 20, $filter = []){
        $user = Auth::user();
        $filter['user_id'] = $user->id;
        $orders = $this->order->getAllPaginate($perPage, $filter);
        $orders->load(['details']);
        return $orders;
    }

    public function checkout($data){
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $cart = $this->cart->getByUserID($user->id);
            if (!$cart) {
                return $this->_result(false, trans('messages.not_found'));
            }
            $details = $cart->details()->get();
            if ($details->isEmpty()) {
                return $this->_result(false, trans('messages.not_found'));
            }
            $data['channel']              = OrderConst::CHANNEL_WEB;
            $data['user_id']              = $user->id;
            $data['order_code']           = IdGenerator::generate(['table' => 'orders', 'field' => 'order_code', 'length' => 10, 'prefix' => 'DH'.date('Y')]);
            $data['is_gift_wrapping']     = $cart->is_gift_wrapping;
            $data['gift_message']         = $cart->gift_message;
            $data['promotion_program_id'] = $cart->promotion_program_id;
            $data['promotion_code_id']    = $cart->promotion_code_id;
            $data['promotion_code']       = $cart->promotion_code;
            $data['total_price']          = $cart->total_price;
            $data['discount_total_price'] = $cart->discount_total_price;
            $order = $this->order->create($data);
            foreach ($details as $detail) {
                $detail = $detail->toArray();
                if ($detail['product_option_id'] != null) {
                    $product        = $this->product->getByID($detail['product_id']);
                    $product_option = $product->product_options()->find($detail['product_option_id']);
                    $attributes     = $product_option->getEntityAttributes()->keys()->all();
                    $options        = [];
                    foreach ($attributes as $attribute) {
                        $options[$attribute] = $product_option->{$attribute};
                    }
                    $detail['options'] = json_encode($options);
                }
                unset($detail['id']);
                unset($detail['cart_id']);
                unset($detail['product']);
                unset($detail['product_option']);
                unset($detail['created_at']);
                unset($detail['updated_at']);
                $order->details()->create($detail);
            }
            $this->cart->destroyByID($cart->id);
            DB::commit();
            OrderCreated::dispatch($order);
            return $this->_result(true, '', $order);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
