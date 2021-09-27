<?php namespace App\Constants;

class BadgeConst
{
    const NONE     = 0;
    const BRONZE   = 1;
    const SILVER   = 2;
    const GOLD     = 3;
    const PLATINUM = 4;
    const DIAMOND  =  5;

    const TOTAL_PRICE_TARGET = [
        BadgeConst::BRONZE   => 2000000,
        BadgeConst::SILVER   => 4000000,
        BadgeConst::GOLD     => 6000000,
        BadgeConst::PLATINUM => 8000000,
        BadgeConst::DIAMOND  => 10000000
    ];

    const PRICE_DISCOUNT_PERCENT = [
        BadgeConst::BRONZE   => 3,
        BadgeConst::SILVER   => 6,
        BadgeConst::GOLD     => 9,
        BadgeConst::PLATINUM => 12,
        BadgeConst::DIAMOND  => 15
    ];
}
