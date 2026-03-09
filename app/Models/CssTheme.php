<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CssTheme extends Model
{
    protected $fillable = ['name', 'description', 'preview_color', 'css', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function getActive(): ?self
    {
        return Cache::rememberForever('active_css_theme', function () {
            return static::where('is_active', true)->first();
        });
    }

    public static function activate(int $id): void
    {
        static::query()->update(['is_active' => false]);
        static::where('id', $id)->update(['is_active' => true]);
        Cache::forget('active_css_theme');
    }

    public static function clearCache(): void
    {
        Cache::forget('active_css_theme');
    }
}
