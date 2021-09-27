<?php namespace App\Constants;

class ProductConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        ProductConst::STATUS_UNACTIVE => 'UnActive',
        ProductConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        ProductConst::STATUS_UNACTIVE,
        ProductConst::STATUS_ACTIVE,
    ];
}
