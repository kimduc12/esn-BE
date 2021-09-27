<?php namespace App\Constants;

class GiftExchangeConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_RETURN = 2;

    const STATUS_NAME = [
        GiftExchangeConst::STATUS_UNACTIVE => 'UnActive',
        GiftExchangeConst::STATUS_ACTIVE => 'Active',
        GiftExchangeConst::STATUS_RETURN => 'Return',
    ];

    const STATUS_VALIDATE = [
        GiftExchangeConst::STATUS_UNACTIVE,
        GiftExchangeConst::STATUS_ACTIVE,
        GiftExchangeConst::STATUS_RETURN,
    ];
}
