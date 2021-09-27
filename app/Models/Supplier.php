<?php

namespace App\Models;

use App\Services\ProductService;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
