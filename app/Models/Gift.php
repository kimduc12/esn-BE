<?php

namespace App\Models;

use App\Constants\GiftConst;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];

    public function scopeCanShowInFE($query)
    {
        return $query
                    ->where('quantity', '>', 0)
                    ->where('published_at', '<=', Carbon::now())
                    ->where('status', GiftConst::STATUS_ACTIVE);
    }

    public function exchange()
    {
        return $this->hasMany(GiftExchange::class, 'gift_id', 'id');
    }
}
