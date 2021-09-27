<?php
namespace App\Utilities;

use App\Constants\PromotionConst;
use App\Repositories\PromotionInterface;
use App\Repositories\ProductInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PromotionUtility {
    protected $promotion;
    protected $product;
    function __construct(
        PromotionInterface $promotion,
        ProductInterface $product
    ){
        $this->promotion = $promotion;
        $this->product = $product;
    }

    public function getAllProgramPromotion(){
        return $this->promotion->getAll([
            'group_type' => PromotionConst::GROUP_PROGRAM,
            'is_active' => true,
        ]);
    }

    public function checkValid($promotion){
        $now = Carbon::now();
        $start_datetime = $promotion->start_datetime;
        $end_datetime = $promotion->end_datetime;
        $is_never_expired = $promotion->is_never_expired;
        $total_used_amount = $promotion->total_used_amount;
        $limited_amount = $promotion->limited_amount;
        if ($limited_amount > 0 && $total_used_amount >= $limited_amount) {
            return false;
        }

        if ($is_never_expired == true) {
            return true;
        }

        if ($end_datetime == null && Carbon::parse($start_datetime)->gt($now)) {
            return false;
        }

        if ($end_datetime != null && (Carbon::parse($start_datetime)->gt($now) || Carbon::parse($end_datetime)->lt($now))) {
            return false;
        }

        return true;
    }

    // Used for promotion code
    public function checkQualified($promotion, $cart){
        $details = $cart->details()->get();
        $is_common_use = $promotion->is_common_use;
        if (!$is_common_use) {
            if ($cart->promotion_program_id > 0) {
                return false;
            }
            foreach ($details as $detail) {
                if ($detail->promotion_id > 0) {
                    return false;
                }
            }
        }

        $apply_value_1 = $promotion->apply_value_1;
        $apply_array_value_1 = $promotion->apply_array_value_1;
        switch($promotion->apply_type_1) {
            case PromotionConst::APPLY_TYPE_TO_ALL_ORDER:
                return true;
                break;
            case PromotionConst::APPLY_TYPE_TO_ORDER_BY_TOTAL_PRICE:
                if ($cart->apply_value_1 > $cart->total_price) {
                    return false;
                }
                return true;
                break;
            case PromotionConst::APPLY_TYPE_TO_PRODUCT_CATEGORY:
                foreach ($details as $detail) {
                    $product = $detail->product()->whereHas('categories', function ($q) use ($apply_value_1){
                        $q->where('id', $apply_value_1);
                    })->first();
                    if ($product) {
                        return true;
                    }
                }
                return false;
                break;
            case PromotionConst::APPLY_TYPE_TO_PRODUCT_TYPE:
                foreach ($details as $detail) {
                    $product = $detail->product()->whereHas('product_types', function ($q) use ($apply_value_1){
                        $q->where('id', $apply_value_1);
                    })->first();
                    if ($product) {
                        return true;
                    }
                }
                return false;
                break;
            case PromotionConst::APPLY_TYPE_TO_PRODUCT_OPTION:
                foreach ($details as $detail) {
                    if (in_array($detail->product_option_id, $apply_array_value_1)) {
                        return true;
                    }
                }
                return false;
                break;
            case PromotionConst::APPLY_TYPE_TO_PRODUCT:
                foreach ($details as $detail) {
                    if (in_array($detail->product_id, $apply_array_value_1)) {
                        return true;
                    }
                }
                return false;
                break;
        }
        return false;
    }

    // Used for promotion code
    public function calcPriceByPromotionGroupCode($promotion, $cart){
        $discount = $this->calctDiscount($promotion, $cart->total_price);
        $cart->total_price -= $discount;
        if ($cart->total_price <= 0) {
            $cart->total_price = 0;
        }
        return $cart->total_price;
    }

    public function getDiscountPrice($promotion, $total_price){
        switch ($promotion->type) {
            case PromotionConst::TYPE_DISCOUNT_BY_AMOUNT:
                $total_price = $total_price - $promotion->discount_amount;
                break;
            case PromotionConst::TYPE_DISCOUNT_BY_PERCENT:
                $discount = $total_price * $promotion->discount_amount / 100;
                if ($discount > $promotion->limited_discount_amount) {
                    $discount = $promotion->limited_discount_amount;
                }
                $total_price = $total_price - $discount;
                break;
        }
        return $total_price;
    }

    public function handleProgramPromotionForCart($cart) {
        $promotions = $this->getAllProgramPromotion();
        $details = $cart->details()->get();
        foreach ($details as $detail) {
            $product = $this->product->getByID($detail['product_id']);
            $can_promotion = $product->can_promotion;
            $detail['discount_price'] = 0;

            if ($can_promotion) {
                $category_ids = $product->categories()->get()->pluck('id')->toArray();
                $product_option_id = $detail['product_option_id'];
                $product_option = $product->product_options()->find($product_option_id);

                foreach ($promotions as $promotion) {
                    $apply_value_1 = $promotion->apply_value_1;
                    $apply_array_value_1 = $promotion->apply_array_value_1;

                    switch ($promotion->apply_type_1) {
                        case PromotionConst::APPLY_TYPE_TO_PRODUCT_OPTION:
                            $detail = $this->handleProgramPromotionTypeProductOption(
                                $promotion, $apply_array_value_1, $product_option_id, $product_option, $detail, $details
                            );
                            break;
                        case PromotionConst::APPLY_TYPE_TO_PRODUCT:
                            $detail = $this->handleProgramPromotionTypeProduct(
                                $promotion, $apply_value_1, $product->id, $product, $detail, $details
                            );
                            break;
                        case PromotionConst::APPLY_TYPE_TO_PRODUCT_CATEGORY:
                            $detail = $this->handleProgramPromotionTypeProductCategory(
                                $promotion, $apply_value_1, $category_ids, $product, $detail, $details
                            );
                            break;
                    }

                }
            }
            $detail['discount_total_price'] = $detail['discount_price'] * $detail['quantity'];
            $cart->details()->find($detail['id'])->update([
                'discount_price'       => $detail['discount_price'],
                'discount_total_price' => $detail['discount_total_price'],
            ]);
        }
    }

    protected function handleProgramPromotionTypeProductOption(
        $promotion, $apply_array_value_1, $product_option_id, $product_option, $detail, $details
    ) {
        if (in_array($product_option_id, $apply_array_value_1)) {
            $apply_value_2 = $promotion->apply_value_2;
            switch ($promotion->apply_type_2) {
                case PromotionConst::APPLY_TYPE_2_TO_TOTAL_AMOUNT_PRODUCT:
                    if ($detail['quantity'] >= $apply_value_2) {
                        if ($detail['discount_price'] > 0) {
                            $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['discount_price']);
                        } else {
                            $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['price']);
                        }
                    } else {
                        $total_amount_product = 1;
                        foreach ($details as $dt) {
                            if ($dt['id'] != $detail['id'] && in_array($dt['product_option_id'], $apply_array_value_1)) {
                                $total_amount_product += $dt['quantity'];
                            }
                        }
                        if ($total_amount_product >= $apply_value_2) {
                            if ($detail['discount_price'] > 0) {
                                $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['discount_price']);
                            } else {
                                $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['price']);
                            }
                        }
                    }
                    break;
                case PromotionConst::APPLY_TYPE_2_TO_TOTAL_PRICE_PRODUCT:
                    if (($product_option->price * $detail['quantity']) >= $apply_value_2) {
                        if ($detail['discount_price'] > 0) {
                            $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['discount_price']);
                        } else {
                            $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['price']);
                        }
                    } else {
                        $total_price_products = $product_option->price * $detail['quantity'];
                        foreach ($details as $dt) {
                            if ($dt['id'] != $detail['id'] && in_array($dt['product_option_id'], $apply_array_value_1)) {
                                $total_price_products += ($dt['price'] * $dt['quantity']);
                            }
                        }
                        if ($total_price_products >= $apply_value_2) {
                            if ($detail['discount_price'] > 0) {
                                $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['discount_price']);
                            } else {
                                $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['price']);
                            }
                        }
                    }
                    break;
            }
        }
        return $detail;
    }

    protected function handleProgramPromotionTypeProduct(
        $promotion, $apply_value_1, $product_id, $product, $detail, $details
    ) {
        if ($product_id == $apply_value_1) {
            $apply_value_2 = $promotion->apply_value_2;
            switch ($promotion->apply_type_2) {
                case PromotionConst::APPLY_TYPE_2_TO_TOTAL_AMOUNT_PRODUCT:
                    if ($detail['quantity'] >= $apply_value_2) {
                        if ($detail['discount_price'] > 0) {
                            $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['discount_price']);
                        } else {
                            $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['price']);
                        }
                    } else {
                        $total_amount_product = 1;
                        foreach ($details as $dt) {
                            if ($dt['id'] != $detail['id'] && $dt['product_id'] == $apply_value_1) {
                                $total_amount_product += $dt['quantity'];
                            }
                        }
                        if ($total_amount_product >= $apply_value_2) {
                            if ($detail['discount_price'] > 0) {
                                $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['discount_price']);
                            } else {
                                $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['price']);
                            }
                        }
                    }
                    break;
                case PromotionConst::APPLY_TYPE_2_TO_TOTAL_PRICE_PRODUCT:
                    if (($product->price * $detail['quantity']) >= $apply_value_2) {
                        if ($detail['discount_price'] > 0) {
                            $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['discount_price']);
                        } else {
                            $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['price']);
                        }
                    } else {
                        $total_price_products = $product->price * $detail['quantity'];
                        foreach ($details as $dt) {
                            if ($dt['id'] != $detail['id'] && in_array($dt['product_id'], $apply_value_1)) {
                                $total_price_products += ($dt['price'] * $dt['quantity']);
                            }
                        }
                        if ($total_price_products >= $apply_value_2) {
                            if ($detail['discount_price'] > 0) {
                                $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['discount_price']);
                            } else {
                                $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['price']);
                            }
                        }
                    }
                    break;
            }
        }
        return $detail;
    }

    protected function handleProgramPromotionTypeProductCategory(
        $promotion, $apply_value_1, $category_ids, $product, $detail, $details
    ) {
        if (in_array($apply_value_1, $category_ids)) {
            $apply_value_2 = $promotion->apply_value_2;
            switch ($promotion->apply_type_2) {
                case PromotionConst::APPLY_TYPE_2_TO_TOTAL_AMOUNT_PRODUCT:
                    if ($detail['quantity'] >= $apply_value_2) {
                        if ($detail['discount_price'] > 0) {
                            $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['discount_price']);
                        } else {
                            $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['price']);
                        }
                    } else {
                        $total_amount_product = 1;
                        foreach ($details as $dt) {
                            $product_dt = $this->product->getByID($detail['product_id']);
                            $category_ids = $product_dt->categories()->get()->pluck('id')->toArray();
                            if ($dt['id'] != $detail['id'] && in_array($apply_value_1, $category_ids)) {
                                $total_amount_product += $dt['quantity'];
                            }
                        }
                        if ($total_amount_product >= $apply_value_2) {
                            if ($detail['discount_price'] > 0) {
                                $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['discount_price']);
                            } else {
                                $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['price']);
                            }
                        }
                    }
                    break;
                case PromotionConst::APPLY_TYPE_2_TO_TOTAL_PRICE_PRODUCT:
                    if (($product->price * $detail['quantity']) >= $apply_value_2) {
                        if ($detail['discount_price'] > 0) {
                            $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['discount_price']);
                        } else {
                            $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['price']);
                        }
                    } else {
                        $total_price_products = $product->price * $detail['quantity'];
                        foreach ($details as $dt) {
                            $product_dt = $this->product->getByID($detail['product_id']);
                            $category_ids = $product_dt->categories()->get()->pluck('id')->toArray();
                            if ($dt['id'] != $detail['id'] && in_array($apply_value_1, $category_ids)) {
                                $total_price_products += ($dt['price'] * $dt['quantity']);
                            }
                        }
                        if ($total_price_products >= $apply_value_2) {
                            if ($detail['discount_price'] > 0) {
                                $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['discount_price']);
                            } else {
                                $detail['discount_price'] = $this->getDiscountPrice($promotion, $detail['price']);
                            }
                        }
                    }
                    break;
            }
        }
        return $detail;
    }
}
