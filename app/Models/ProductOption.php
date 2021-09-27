<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Rinvex\Attributes\Traits\Attributable;

class ProductOption extends Model
{
    use Attributable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];

    // Eager loading all the registered attributes
    protected $with = ['eav'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes()
    {
        return $this->hasMany(AttributeVarcharValue::class, 'entity_id', 'id')->where('entity_type', "App\Models\ProductOption");
    }
}
