<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table = 'blog_categories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    protected $guarded  = [];

    protected static function booted()
    {
        static::deleting(function ($model) {
            $model->blogs()->detach();
            $model->childrenRecursive()->delete();
        });
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function parent()
    {
        return $this->hasOne(self::class, 'id', 'parent_id');
    }

    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'blog_category', 'category_id', 'blog_id');
    }
}
