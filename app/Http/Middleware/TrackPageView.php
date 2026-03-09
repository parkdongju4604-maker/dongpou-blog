<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // GET 요청만, 성공 응답만 기록
        if (!$request->isMethod('GET') || $response->getStatusCode() !== 200) {
            return $response;
        }

        // 어드민, API, 에셋, sitemap/robots 제외
        $path = $request->path();
        if (str_starts_with($path, 'admin') ||
            str_starts_with($path, 'api')   ||
            str_starts_with($path, '_')     ||
            in_array($path, ['sitemap.xml', 'robots.txt', 'favicon.ico'])) {
            return $response;
        }

        $ua = $request->userAgent() ?? '';

        // 봇 제외
        if (PageView::isBot($ua)) {
            return $response;
        }

        try {
            $referrer = $request->headers->get('referer');
            $domain   = PageView::extractDomain($referrer);

            // 자기 사이트 referrer 제외 (내부 이동)
            $ownHost = parse_url(config('app.url'), PHP_URL_HOST);
            if ($domain && str_contains($domain, $ownHost ?? '')) {
                $domain   = null;
                $referrer = null;
            }

            PageView::create([
                'path'           => '/' . $path,
                'referrer'       => $referrer ? substr($referrer, 0, 500) : null,
                'referrer_domain'=> $domain,
                'referrer_type'  => PageView::classifyReferrer($domain),
                'user_agent'     => substr($ua, 0, 500),
                'device_type'    => PageView::detectDevice($ua),
                'browser'        => PageView::detectBrowser($ua),
                'os'             => PageView::detectOS($ua),
                'ip_hash'        => hash('sha256', $request->ip() . config('app.key')),
            ]);
        } catch (\Throwable) {
            // 통계 실패가 서비스 영향 주지 않도록
        }

        return $response;
    }
}
