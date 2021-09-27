<?php namespace App\Constants;

class BrandConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        BrandConst::STATUS_UNACTIVE => 'UnActive',
        BrandConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        BrandConst::STATUS_UNACTIVE,
        BrandConst::STATUS_ACTIVE,
    ];
}
