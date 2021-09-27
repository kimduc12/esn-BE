<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
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
            $model->products()->detach();
        });
    }

    public function products()
    {
        return $this->morphToMany(Product::class, 'productable');
    }
}
