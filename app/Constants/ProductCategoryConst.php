<?php namespace App\Constants;

class ProductCategoryConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        ProductCategoryConst::STATUS_UNACTIVE => 'UnActive',
        ProductCategoryConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        ProductCategoryConst::STATUS_UNACTIVE,
        ProductCategoryConst::STATUS_ACTIVE,
    ];
}
