<?php

namespace App\Models;

use App\Constants\RouteConst;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];

    protected $appends = ['type_name'];

    protected static function booted()
    {
        static::deleting(function ($model) {

        });
    }

    public function getTypeNameAttribute()
    {
        return RouteConst::TYPE_NAME[$this->type];
    }
}
