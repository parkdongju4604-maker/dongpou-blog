<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearer = $request->bearerToken();

        if (!$bearer) {
            return response()->json([
                'error'   => 'Unauthorized',
                'message' => 'Authorization: Bearer 토큰이 필요합니다.',
            ], 401);
        }

        $token = ApiToken::findByToken($bearer);

        if (!$token) {
            return response()->json([
                'error'   => 'Unauthorized',
                'message' => '유효하지 않거나 만료된 토큰입니다.',
            ], 401);
        }

        // 요청에 토큰 정보 바인딩 (필요 시 컨트롤러에서 사용 가능)
        $request->attributes->set('api_token', $token);

        return $next($request);
    }
}
