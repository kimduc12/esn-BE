<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
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

    public function categories()
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_category', 'blog_id', 'category_id');
    }

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', Carbon::now());
    }
}
