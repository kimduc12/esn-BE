<?php namespace App\Constants;

class RouteConst
{
    const TYPE_PRODUCT_CATEGORY = 1;
    const TYPE_PRODUCT = 2;
    const TYPE_BLOG_CATEGORY = 3;
    const TYPE_BLOG = 4;
    const TYPE_TOPIC = 5;

    const TYPE_NAME = [
        RouteConst::TYPE_PRODUCT_CATEGORY => 'product_category',
        RouteConst::TYPE_PRODUCT => 'product',
        RouteConst::TYPE_BLOG_CATEGORY => 'blog_category',
        RouteConst::TYPE_BLOG => 'blog',
        RouteConst::TYPE_TOPIC => 'topic',
    ];
}
