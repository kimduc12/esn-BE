<?php namespace App\Constants;

class CustomerConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE   = 1;
    const STATUS_LOCK     = 2;

    const STATUS_NAME = [
        UserConst::STATUS_UNACTIVE => 'UnActive',
        UserConst::STATUS_ACTIVE   => 'Active',
        UserConst::STATUS_LOCK     => 'Lock',
    ];

    const BADGE_BRONZE   = 1;
    const BADGE_SILVER   = 2;
    const BADGE_GOLD     = 3;
    const BADGE_PLATINUM = 4;
    const BADGE_DIAMOND  = 5;
}
