<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasApiTokens, HasRoles;

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'birthday' => 'date',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::deleting(function ($model) {
            $model->customer_info()->delete();
            $model->user_providers()->delete();
            $model->favourite_products()->detach();
            $model->read_products()->detach();
        });
    }

    public function user_providers()
    {
        return $this->hasMany(UserProvider::class, 'user_id', 'id');
    }

    public function customer_info()
    {
        return $this->hasOne(CustomerInfo::class, 'customer_id', 'id');
    }

    public function favourite_products()
    {
        return $this->belongsToMany(Product::class, 'user_favourite_product', 'user_id', 'product_id');
    }

    public function read_products()
    {
        return $this->belongsToMany(Product::class, 'user_read_product', 'user_id', 'product_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function history_exchange()
    {
        return $this->hasMany(GiftExchange::class, 'user_id', 'id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id', 'id');
    }
}
