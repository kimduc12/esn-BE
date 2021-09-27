<?php namespace App\Constants;

class BlogCategoryConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        BlogCategoryConst::STATUS_UNACTIVE => 'UnActive',
        BlogCategoryConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        BlogCategoryConst::STATUS_UNACTIVE,
        BlogCategoryConst::STATUS_ACTIVE,
    ];
}
