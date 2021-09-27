<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
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
        });
    }

    public function categories()
    {
        return $this->morphToMany(ProductCategory::class, 'categorizable');
    }

    public function products()
    {
        return $this->morphToMany(Product::class, 'productable');
    }
}
