<?php namespace App\Constants;

class ProductTypeConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        ProductTypeConst::STATUS_UNACTIVE => 'UnActive',
        ProductTypeConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        ProductTypeConst::STATUS_UNACTIVE,
        ProductTypeConst::STATUS_ACTIVE,
    ];
}
