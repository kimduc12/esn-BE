<?php namespace App\Constants;

class PatternConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        PatternConst::STATUS_UNACTIVE => 'UnActive',
        PatternConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        PatternConst::STATUS_UNACTIVE,
        PatternConst::STATUS_ACTIVE,
    ];
}
