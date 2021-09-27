<?php

namespace App\Models;

use App\Constants\TopicConst;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
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
            $model->categories()->detach();
            $model->products()->detach();
            $model->home_products()->detach();
        });
    }

    public function scopeActived($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeShowed($query)
    {
        return $query->where('status', TopicConst::STATUS_ACTIVE);
    }

    public function categories()
    {
        return $this->morphToMany(ProductCategory::class, 'categorizable');
    }

    public function products()
    {
        return $this->morphToMany(Product::class, 'productable');
    }

    public function home_products()
    {
        return $this->belongsToMany(Product::class, 'topic_home_product', 'topic_id', 'product_id');
    }
}
