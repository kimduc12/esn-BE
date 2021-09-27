<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class District extends Model
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
            $model->wards()->delete();
        });
    }

    public function wards()
    {
        return $this->hasMany(Ward::class);
    }
}
