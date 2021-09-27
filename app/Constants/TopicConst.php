<?php namespace App\Constants;

class TopicConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        TopicConst::STATUS_UNACTIVE => 'UnActive',
        TopicConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        TopicConst::STATUS_UNACTIVE,
        TopicConst::STATUS_ACTIVE,
    ];
}
