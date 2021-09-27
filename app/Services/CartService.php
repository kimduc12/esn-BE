<?php
namespace App\Services;

use App\Constants\PromotionConst;
use App\Repositories\CartInterface;
use App\Repositories\ProductInterface;
use App\Repositories\PromotionInterface;
use Illuminate\Support\Facades\Auth;
use App\Utilities\PromotionUtility;
use App\Constants\OrderConst;

class CartService extends BaseService {
    protected $cart;
    protected $product;
    protected $promotionUtility;
    protected $promotion;
    function __construct(
        CartInterface $cart,
        ProductInterface $product,
        PromotionUtility $promotionUtility,
        PromotionInterface $promotion
    ){
        $this->cart = $cart;
        $this->product = $product;
        $this->promotion = $promotion;
        $this->promotionUtility = $promotionUtility;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $carts = $this->cart->getAllPaginate($perPage, $filter);
        $carts->load(['user', 'details']);
        return $carts;
    }

    public function create($data){
        $user = Auth::user();
        $product_id = $data['product_id'];
        $product = $this->product->getByID($product_id);
        //$category_ids = $product->categories()->get()->pluck('id')->toArray();
        if (isset($data['product_option_id'])){
            $product_option_id = $data['product_option_id'];
            $product_option = $product->product_options()->find($product_option_id);
        }

        if (isset($data['product_option_id']) && isset($product_option)) {
            $data['image_url'] = $product_option->image_url;
            $data['image_mobile_url'] = $product_option->image_mobile_url;
            $data['sku'] = $product_option->sku;
            $data['barcode'] = $product_option->barcode;
            $data['price'] = $product_option->price;
            $data['total_price'] = $product_option->price * $data['quantity'];
        } else {
            $data['image_url'] = $product->image_url;
            $data['image_mobile_url'] = $product->image_mobile_url;
            $data['sku'] = $product->sku;
            $data['barcode'] = $product->barcode;
            $data['price'] = $product->price;
            $data['total_price'] = $product->price * $data['quantity'];
        }

        $cart = $this->cart->getByUserID($user->id);
        if(!$cart){
            $cart = $this->cart->create([
                'user_id' => $user->id,
                'total_price' => $data['total_price']
            ]);
        } else {
            $where = [
                'product_id'        => $data['product_id']
            ];
            if (isset($data['product_option_id'])) {
                $where['product_option_id'] = $data['product_option_id'];
            }
            $checkExists = $cart->details()->where($where)->first();

            if ($checkExists) {
                return $this->_result(false, trans('cart.duplicate_product'));
            }
        }

        $cart->details()->create($data);
        $this->reCalcMyCart();
        $cart = $this->cart->getByUserID($user->id);
        $cart->load(['details']);
        return $this->_result(true, trans('cart.add_success'), $cart);
    }

    public function updateByID($data){
        $user = Auth::user();
        $cart = $this->cart->getByUserID($user->id);
        if(!$cart){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $details = $data['details'];
        unset($data['details']);
        foreach ($details as $detail) {
            $cartDetail = $cart->details()->find($detail['id']);
            $update = [
                'quantity'    => $detail['quantity'],
                'total_price' => $cartDetail['price'] * $detail['quantity']
            ];
            if (isset($data['product_option_id'])) {
                if (!isset($data['product_id'])) {
                    return $this->_result(false,  trans('cart.product_id_required'));
                }
                if ($data['product_id'] != $detail['product_id']) {
                    return $this->_result(false,  trans('cart.product_id_not_match'));
                }
                $product = $this->product->getByID($data['product_id']);
                $product_option = $product->product_options()->find($data['product_option_id']);

                $update['product_option_id'] = $data['product_option_id'];
                $update['image_url'] = $product_option->image_url;
                $update['image_mobile_url'] = $product_option->image_mobile_url;
                $update['sku'] = $product_option->sku;
                $update['barcode'] = $product_option->barcode;
                $update['price'] = $product_option->price;
                $update['total_price'] = $product_option->price * $data['quantity'];
            }
            $cartDetail->update($update);
        }

        if (isset($data['promotion_code'])) {
            $promotion_code = $data['promotion_code'];
            $promotion = $this->promotion->getByCode($promotion_code);
            if (!$promotion) {
                return $this->_result(false,  trans('cart.promotion_code_not_exist'));
            }
            $check = $this->promotionUtility->checkValid($promotion);
            if (!$check) {
                return $this->_result(false,  trans('cart.promotion_code_invalid'));
            }
            $qualified = $this->promotionUtility->checkQualified($promotion, $cart);
            if (!$qualified) {
                return $this->_result(false,  trans('cart.promotion_code_can_not_use_for_promotion_cart'));
            }
            $data['promotion_code_id'] = $promotion->id;
        }

        $this->cart->updateByID($cart->id, $data);
        $this->reCalcMyCart();
        $cart = $this->cart->getByUserID($user->id);
        $cart->load(['details']);
        return $this->_result(true,  trans('cart.update_success'), $cart);
    }

    public function destroyByIDs($arrID){
        $result =$this->cart->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function destroyByID($id){
        $result =$this->cart->destroyByID($id);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getMyCart(){
        $user = Auth::user();
        $cart = $this->cart->getByUserID($user->id);
        if (!$cart) {
            return null;
        }
        $cart->load(['details']);
        return $cart;
    }

    public function removeDetail($id){
        $user = Auth::user();
        $cart = $this->cart->getByUserID($user->id);
        if (!$cart) {
            return $this->_result(false, trans('messages.not_found'));
        }
        $cart->details()->where('id', $id)->delete();
        $this->reCalcMyCart();
        $cart = $this->cart->getByUserID($user->id);
        $cart->load(['details']);
        return $this->_result(true, trans('messages.deleted_success'), $cart);
    }

    public function reCalcMyCart(){
        $user = Auth::user();
        $cart = $this->cart->getByUserID($user->id);
        $this->promotionUtility->handleProgramPromotionForCart($cart);

        $total_price_cart = 0;
        $discount_total_price_cart = 0;

        $details = $cart->details()->get();
        foreach ($details as $detail) {
            if ($detail['discount_total_price'] > 0) {
                $discount_total_price_cart += $detail['discount_total_price'];
            } else {
                $discount_total_price_cart += $detail['total_price'];
            }
            $total_price_cart += $detail['total_price'];
        }

        if ($total_price_cart == $discount_total_price_cart) {
            $discount_total_price_cart = 0;
        }

        if ($cart->promotion_code != null && $cart->promotion_code_id != 0) {
            $promotion = $this->promotion->getByID($cart->promotion_code_id);
            if ($promotion) {
                if ($discount_total_price_cart > 0) {
                    $discount_total_price_cart = $this->promotionUtility->getDiscountPrice($promotion, $discount_total_price_cart);
                } else {
                    $discount_total_price_cart = $this->promotionUtility->getDiscountPrice($promotion, $total_price_cart);
                }
            }
        }

        if ($cart->is_gift_wrapping == true) {
            $total_price_cart += OrderConst::GIFT_WRAPPING_PRICE;
            if ($discount_total_price_cart > 0) {
                $discount_total_price_cart += OrderConst::GIFT_WRAPPING_PRICE;
            }
        }

        $data = [
            'total_price'          => $total_price_cart,
            'discount_total_price' => $discount_total_price_cart
        ];

        $this->cart->updateByID($cart->id, $data);
    }
}
