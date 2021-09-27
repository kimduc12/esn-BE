<?php namespace App\Constants;

class OrderConst
{
    const STATUS_INIT = 0;
    const STATUS_PAID = 1;
    const STATUS_CANCEL = 2;
    const STATUS_DELIVERING = 3;
    const STATUS_DELIVERED = 4;

    const STATUS_VALIDATE = [
        OrderConst::STATUS_INIT,
        OrderConst::STATUS_PAID,
        OrderConst::STATUS_CANCEL,
        OrderConst::STATUS_DELIVERING,
        OrderConst::STATUS_DELIVERED,
    ];

    const STATUS_NAME = [
        OrderConst::STATUS_INIT => 'Khởi tạo',
        OrderConst::STATUS_PAID => 'Đã thanh toán',
        OrderConst::STATUS_CANCEL => 'Hủy',
        OrderConst::STATUS_DELIVERING => 'Đang giao hàng',
        OrderConst::STATUS_DELIVERED => 'Đã giao hàng'
    ];

    const CHANNEL_WEB = 0;
    const CHANNEL_PORTAL = 1;
    const CHANNEL_FACEBOOK = 2;

    const CHANNEL_NAME = [
        OrderConst::CHANNEL_WEB => 'web',
        OrderConst::CHANNEL_PORTAL => 'portal',
        OrderConst::CHANNEL_FACEBOOK => 'facebook'
    ];

    const PAID_STATUS_NONE = 0;
    const PAID_STATUS_WAIT = 1;
    const PAID_STATUS_UNPAID = 2;
    const PAID_STATUS_PAID = 3;

    const PAID_STATUS_NAME = [
        OrderConst::PAID_STATUS_NONE => '',
        OrderConst::PAID_STATUS_WAIT => 'Chờ xử lý',
        OrderConst::PAID_STATUS_UNPAID => 'Chưa thanh toán',
        OrderConst::PAID_STATUS_PAID => 'Đã thanh toán'
    ];

    const GIFT_WRAPPING_PRICE = 20000;

    const PAYMENT_TYPE_CASH = 0;
    const PAYMENT_TYPE_TRANSFER_ONLINE = 1;

    const PAYMENT_TYPE_VALIDATE = [
        OrderConst::PAYMENT_TYPE_CASH,
        OrderConst::PAYMENT_TYPE_TRANSFER_ONLINE
    ];

    const TRANSPORT_TYPE_BASIC = 0;
    const TRANSPORT_TYPE_FAST = 1;

    const TRANSPORT_TYPE_VALIDATE = [
        OrderConst::TRANSPORT_TYPE_BASIC,
        OrderConst::TRANSPORT_TYPE_FAST
    ];
}
