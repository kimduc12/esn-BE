<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];

    protected $casts = [
        'options' => 'array',
    ];

    protected static function booted()
    {
        static::deleting(function ($model) {

        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function product_option()
    {
        return $this->belongsTo(ProductOption::class);
    }
}
