<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class City extends Model
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
            $model->districts()->delete();
        });
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
