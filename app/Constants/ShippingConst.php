<?php namespace App\Constants;

class ShippingConst
{
    const STATUS_NONE = 0;
    const STATUS_WAIT = 1;
    const STATUS_DELIVERING = 2;
    const STATUS_DELIVERED = 3;

    const STATUS_NAME = [
        ShippingConst::STATUS_NONE => 'Chưa giao hàng',
        ShippingConst::STATUS_WAIT => 'Chờ giao hàng',
        ShippingConst::STATUS_DELIVERING => 'Đang giao hàng',
        ShippingConst::STATUS_DELIVERED => 'Đã giao hàng'
    ];

    const COD_STATUS_NONE = 0;
    const COD_STATUS_NO_CHARGE = 1;
    const COD_STATUS_NOT_RECEIVED = 2;
    const COD_STATUS_RECEIVED = 3;

    const COD_STATUS_NAME = [
        ShippingConst::COD_STATUS_NONE => '',
        ShippingConst::COD_STATUS_NO_CHARGE => 'Không thu',
        ShippingConst::COD_STATUS_NOT_RECEIVED => 'Chưa nhận',
        ShippingConst::COD_STATUS_RECEIVED => 'Đã nhận'
    ];
}
