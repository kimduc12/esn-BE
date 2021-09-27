<?php namespace App\Constants;

class MaterialConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        MaterialConst::STATUS_UNACTIVE => 'UnActive',
        MaterialConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        MaterialConst::STATUS_UNACTIVE,
        MaterialConst::STATUS_ACTIVE,
    ];
}
