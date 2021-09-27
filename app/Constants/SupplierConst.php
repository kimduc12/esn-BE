<?php namespace App\Constants;

class SupplierConst
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NAME = [
        SupplierConst::STATUS_UNACTIVE => 'UnActive',
        SupplierConst::STATUS_ACTIVE => 'Active',
    ];

    const STATUS_VALIDATE = [
        SupplierConst::STATUS_UNACTIVE,
        SupplierConst::STATUS_ACTIVE,
    ];
}
