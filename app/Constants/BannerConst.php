<?php namespace App\Constants;

class BannerConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_VALIDATE = [
        BannerConst::STATUS_UNACTIVE,
        BannerConst::STATUS_ACTIVE,
    ];

    const STATUS_NAME = [
        BannerConst::STATUS_UNACTIVE => 'UnActive',
        BannerConst::STATUS_ACTIVE => 'Active',
    ];

    const TYPE_HOME_BANNER = 0;
    const TYPE_TOP_NEWS_BANNER = 1;
    const TYPE_NEWS_BANNER = 2;

    const TYPE_VALIDATE = [
        BannerConst::TYPE_HOME_BANNER,
        BannerConst::TYPE_TOP_NEWS_BANNER,
        BannerConst::TYPE_NEWS_BANNER,
    ];
}
