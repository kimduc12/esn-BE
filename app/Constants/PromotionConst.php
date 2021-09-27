<?php namespace App\Constants;

class PromotionConst
{
    const GROUP_CODE = 1;
    const GROUP_PROGRAM = 2;

    const TYPE_DISCOUNT_BY_AMOUNT = 1;
    const TYPE_DISCOUNT_BY_PERCENT = 2;
    const TYPE_DISCOUNT_BY_FREE_TRANSPORT = 3;

    const TYPE_VALIDATE = [
        PromotionConst::TYPE_DISCOUNT_BY_AMOUNT,
        PromotionConst::TYPE_DISCOUNT_BY_PERCENT,
    ];

    const APPLY_TYPE_TO_ALL_ORDER = 0;
    const APPLY_TYPE_TO_ORDER_BY_TOTAL_PRICE = 1;
    const APPLY_TYPE_TO_PRODUCT_CATEGORY = 2;
    const APPLY_TYPE_TO_PRODUCT_TYPE = 3;
    const APPLY_TYPE_TO_PRODUCT_OPTION = 4;
    const APPLY_TYPE_TO_PRODUCT = 5;

    const APPLY_TYPE_VALIDATE = [
        PromotionConst::APPLY_TYPE_TO_ALL_ORDER,
        PromotionConst::APPLY_TYPE_TO_ORDER_BY_TOTAL_PRICE,
        PromotionConst::APPLY_TYPE_TO_PRODUCT_CATEGORY,
        PromotionConst::APPLY_TYPE_TO_PRODUCT_TYPE,
        PromotionConst::APPLY_TYPE_TO_PRODUCT_OPTION,
        PromotionConst::APPLY_TYPE_TO_PRODUCT,
    ];

    const APPLY_TYPE_2_TO_TOTAL_PRICE_ORDER = 1;
    const APPLY_TYPE_2_TO_TOTAL_AMOUNT_PRODUCT = 2;
    const APPLY_TYPE_2_TO_TOTAL_PRICE_PRODUCT = 3;

    const APPLY_TYPE_2_VALIDATE = [
        PromotionConst::APPLY_TYPE_2_TO_TOTAL_PRICE_ORDER,
        PromotionConst::APPLY_TYPE_2_TO_TOTAL_AMOUNT_PRODUCT,
        PromotionConst::APPLY_TYPE_2_TO_TOTAL_PRICE_PRODUCT,
    ];
}
