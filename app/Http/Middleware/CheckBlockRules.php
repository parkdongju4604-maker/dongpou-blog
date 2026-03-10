<?php

namespace App\Http\Middleware;

use App\Models\AccessLog;
use App\Models\BlockRule;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockRules
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();

        // 관리자/API/asset 경로는 로깅·차단 제외
        if (str_starts_with($path, 'admin') ||
            str_starts_with($path, 'api/') ||
            str_starts_with($path, '_debugbar')) {
            return $next($request);
        }

        $ip = $request->ip() ?? '';
        $ua = $request->userAgent() ?? '';

        // 접속 기록 (비차단 유저 포함 — 차단 전에 기록)
        AccessLog::record($ip, $ua);

        // 차단 규칙 체크 (캐시됨)
        $rules = BlockRule::getCached();

        foreach ($rules as $rule) {
            if ($rule->type === 'ip' && $rule->value === $ip) {
                abort(403, '접근이 차단되었습니다.');
            }
            if ($rule->type === 'useragent' &&
                str_contains(strtolower($ua), strtolower($rule->value))) {
                abort(403, '접근이 차단되었습니다.');
            }
        }

        return $next($request);
    }
}
