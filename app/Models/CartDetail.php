<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];

    protected $casts = [

    ];

    protected static function booted()
    {
        static::deleting(function ($model) {

        });
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
