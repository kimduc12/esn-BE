<?php namespace App\Constants;

class CountryConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        CountryConst::STATUS_UNACTIVE => 'UnActive',
        CountryConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        CountryConst::STATUS_UNACTIVE,
        CountryConst::STATUS_ACTIVE,
    ];
}
