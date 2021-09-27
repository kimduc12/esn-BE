<?php

namespace App\Models;

use App\Constants\GiftConst;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GiftExchange extends Model
{
    protected $table = 'gift_exchange';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];
}
