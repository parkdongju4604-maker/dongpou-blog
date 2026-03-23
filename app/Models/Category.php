<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'sort_order'];

    protected static function boot(): void
    {
        parent::boot();
        static::saving(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name) ?: Str::lower(Str::random(8));
            }
        });
    }

    public static function ordered()
    {
        return static::orderBy('sort_order')->orderBy('name');
    }

    public function getPostCountAttribute(): int
    {
        return Post::where('category', $this->name)->count();
    }
}
