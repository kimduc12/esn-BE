<?php

namespace App\Models;

use App\Constants\ShippingConst;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
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

        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function getStatusAttribute($value)
    {
        $this->attributes['status_name'] = ShippingConst::STATUS_NAME[$value];
    }

    public function getCodStatusAttribute($value)
    {
        $this->attributes['cod_status_name'] = ShippingConst::COD_STATUS_NAME[$value];
    }
}
