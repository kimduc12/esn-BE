<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CustomerInfo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'customer_id');
    }
}
