<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductOption;

class Promotion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];
    protected $casts = [
        'apply_array_value_1' => 'array'
    ];

    public function array_value_1_product_options()
    {
        return ProductOption::with('product')->find($this->apply_array_value_1);
    }

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
