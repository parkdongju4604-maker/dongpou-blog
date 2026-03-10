<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BlockRule extends Model
{
    protected $fillable = ['type', 'value', 'is_active', 'note'];

    protected $casts = ['is_active' => 'boolean'];

    /** 캐시된 활성 규칙 컬렉션 반환 (5분 캐시) */
    public static function getCached(): \Illuminate\Support\Collection
    {
        return Cache::remember('block_rules_active', 300, function () {
            return static::where('is_active', true)->get(['type', 'value']);
        });
    }

    /** 규칙 변경 시 캐시 무효화 */
    public static function clearCache(): void
    {
        Cache::forget('block_rules_active');
    }
}
