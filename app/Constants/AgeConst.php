<?php namespace App\Constants;

class AgeConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        AgeConst::STATUS_UNACTIVE => 'UnActive',
        AgeConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        AgeConst::STATUS_UNACTIVE,
        AgeConst::STATUS_ACTIVE,
    ];
}
