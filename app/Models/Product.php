<?php

namespace App\Models;

use App\Constants\ProductConst;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
class Product extends Model
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
            $model->files()->detach();
            $model->ages()->detach();
            $model->topics()->detach();
            $model->product_types()->detach();
            $model->categories()->detach();
            $options = $model->product_options()->get();
            if ($options->isNotEmpty()) {
                foreach ($options as $option) {
                    $option->attributes()->delete();
                }
                $option_ids = $options->pluck('id')->toArray();
                ProductOption::destroy($option_ids);
            }
        });
    }

    protected $casts = [
        'published_at' => 'datetime'
    ];

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', Carbon::now());
    }

    public function scopeShowed($query)
    {
        return $query->where('is_show', 1);
    }

    public function scopeActived($query)
    {
        return $query->where('status', ProductConst::STATUS_ACTIVE);
    }

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category', 'product_id', 'category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function ages()
    {
        return $this->morphedByMany(Age::class, 'productable');
    }

    public function topics()
    {
        return $this->morphedByMany(Topic::class, 'productable');
    }

    public function product_types()
    {
        return $this->morphedByMany(ProductType::class, 'productable');
    }

    public function patterns()
    {
        return $this->morphedByMany(Pattern::class, 'productable');
    }

    public function materials()
    {
        return $this->morphedByMany(Material::class, 'productable');
    }

    public function product_options()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function files()
    {
        return $this->morphToMany(File::class, 'fileable');
    }
}
