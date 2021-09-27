<?php namespace App\Constants;

class SectionConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        SectionConst::STATUS_UNACTIVE => 'UnActive',
        SectionConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        SectionConst::STATUS_UNACTIVE,
        SectionConst::STATUS_ACTIVE,
    ];
}
