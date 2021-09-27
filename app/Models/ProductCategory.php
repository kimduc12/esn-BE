<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_categories';
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
            $model->advice_posts()->detach();
            $model->product_types()->detach();
            $model->topics()->detach();
            $model->ages()->detach();
            $model->products()->detach();
            $model->childrenRecursive()->delete();
        });
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function parent()
    {
        return $this->hasOne(self::class, 'id', 'parent_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category', 'category_id', 'product_id');
    }

    public function advice_posts()
    {
        return $this->belongsToMany(AdvicePost::class, 'advice_post_product_category', 'product_category_id', 'advice_post_id');
    }

    public function product_types()
    {
        return $this->morphedByMany(ProductType::class, 'categorizable');
    }

    public function topics()
    {
        return $this->morphedByMany(Topic::class, 'categorizable');
    }

    public function ages()
    {
        return $this->morphedByMany(Age::class, 'categorizable');
    }
}
