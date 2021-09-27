<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
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
            $model->products()->update([
                'brand_id' => 0
            ]);
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
