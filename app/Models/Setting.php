<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'type', 'group'];

    /**
     * 설정값 가져오기 (캐시 포함)
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $row = static::where('key', $key)->first();
            return $row ? $row->value : $default;
        });
    }

    /**
     * 설정값 저장 + 캐시 무효화
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    /**
     * 그룹별 설정 목록 반환
     */
    public static function grouped(): array
    {
        return static::all()->groupBy('group')->toArray();
    }

    /**
     * 모든 설정값을 key => value 배열로 반환
     */
    public static function allValues(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }
}
