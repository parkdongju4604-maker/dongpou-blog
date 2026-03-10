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
        $path      = $request->path();
        $isAdmin   = str_starts_with($path, 'admin');
        $isSkipLog = $isAdmin ||
                     str_starts_with($path, 'api/') ||
                     str_starts_with($path, '_debugbar');

        $ip = $request->ip() ?? '';
        $ua = $request->userAgent() ?? '';

        // 접속 기록 — 공개 라우트만 (어드민/API 제외)
        if (!$isSkipLog) {
            AccessLog::record($ip, $ua);
        }

        // 차단 규칙 체크 — 모든 경로 적용 (어드민 포함)
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
