<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    protected $fillable = ['name', 'token', 'token_prefix', 'last_used_at', 'expires_at'];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    /**
     * 새 토큰 생성 → [plaintext, model] 반환
     * plaintext는 생성 시 딱 한 번만 노출
     */
    public static function generate(string $name, ?string $expiresAt = null): array
    {
        $plain  = 'dp_' . Str::random(40);
        $hash   = hash('sha256', $plain);
        $prefix = substr($plain, 0, 10); // "dp_xxxxxxx" 식별용

        $token = static::create([
            'name'         => $name,
            'token'        => $hash,
            'token_prefix' => $prefix,
            'expires_at'   => $expiresAt ? \Carbon\Carbon::parse($expiresAt) : null,
        ]);

        return [$plain, $token];
    }

    /**
     * Bearer 토큰으로 유효한 ApiToken 조회 + last_used_at 갱신
     */
    public static function findByToken(string $plain): ?static
    {
        $hash  = hash('sha256', $plain);
        $model = static::where('token', $hash)->first();

        if (!$model) return null;

        // 만료 체크
        if ($model->expires_at && $model->expires_at->isPast()) {
            return null;
        }

        $model->update(['last_used_at' => now()]);
        return $model;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
