<?php namespace App\Constants;

class BlogConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        BlogConst::STATUS_UNACTIVE => 'UnActive',
        BlogConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        BlogConst::STATUS_UNACTIVE,
        BlogConst::STATUS_ACTIVE,
    ];
}
