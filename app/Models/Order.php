<?php

namespace App\Models;

use App\Constants\OrderConst;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];

    protected static function booted()
    {
        static::deleting(function ($model) {
            $model->details()->delete();
            $model->shipping()->delete();
        });
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class)->with(['product', 'product_option']);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id')->with(['customer_info']);
    }

    public function getStatusAttribute($value)
    {
        $this->attributes['status_name'] = OrderConst::STATUS_NAME[$value];
        return $value;
    }

    public function getChannelAttribute($value)
    {
        $this->attributes['channel_name'] = OrderConst::CHANNEL_NAME[$value];
        return $value;
    }

    public function getPaidStatusAttribute($value)
    {
        $this->attributes['paid_status_name'] = OrderConst::PAID_STATUS_NAME[$value];
        return $value;
    }

    public function getDeliveryStatusAttribute($value)
    {
        $this->attributes['delivery_status_name'] = OrderConst::DELIVERY_STATUS_NAME[$value];
        return $value;
    }

    public function getCodStatusAttribute($value)
    {
        $this->attributes['cod_status_name'] = OrderConst::COD_STATUS_NAME[$value];
        return $value;
    }
}
