<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AdvicePost extends Model
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
        });
    }

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'advice_post_product_category', 'advice_post_id', 'product_category_id');
    }
}
