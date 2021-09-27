<?php namespace App\Constants;

class GiftConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        GiftConst::STATUS_UNACTIVE => 'UnActive',
        GiftConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        GiftConst::STATUS_UNACTIVE,
        GiftConst::STATUS_ACTIVE,
    ];
}
