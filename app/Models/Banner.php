<?php

namespace App\Models;

use App\Constants\BannerConst;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];

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
        return $query->where('status', BannerConst::STATUS_ACTIVE);
    }
}
