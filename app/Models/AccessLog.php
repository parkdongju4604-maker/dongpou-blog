<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AccessLog extends Model
{
    protected $fillable = ['ip', 'user_agent', 'count', 'last_seen_at'];

    protected $casts = ['last_seen_at' => 'datetime'];

    /** IP + UA 조합 upsert (접속 횟수 누적) */
    public static function record(string $ip, string $ua): void
    {
        try {
            DB::table('access_logs')->upsert(
                [
                    'ip'           => $ip,
                    'user_agent'   => $ua,
                    'count'        => 1,
                    'last_seen_at' => now(),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                ['ip', 'user_agent'],
                [
                    'count'        => DB::raw('count + 1'),
                    'last_seen_at' => now(),
                    'updated_at'   => now(),
                ]
            );
        } catch (\Throwable) {
            // 로깅 실패는 무시
        }
    }
}
