<?php namespace App\Constants;

class PageConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        PageConst::STATUS_UNACTIVE => 'UnActive',
        PageConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        PageConst::STATUS_UNACTIVE,
        PageConst::STATUS_ACTIVE,
    ];

    const TYPE_SERVICE = 0;
    const TYPE_ABOUT = 1;
    const TYPE_TOP_MENU = 2;
    const TYPE_NOTIFICATION = 3;

    const TYPE_VALIDATE = [
        PageConst::TYPE_SERVICE,
        PageConst::TYPE_ABOUT,
        PageConst::TYPE_TOP_MENU,
        PageConst::TYPE_NOTIFICATION,
    ];
}
