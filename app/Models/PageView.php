<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'path', 'title', 'referrer', 'referrer_domain', 'referrer_type',
        'user_agent', 'device_type', 'browser', 'os', 'ip_hash',
    ];

    // ── 기기 타입 감지 ──────────────────────────────────────────
    public static function detectDevice(string $ua): string
    {
        $ua = strtolower($ua);
        if (preg_match('/tablet|ipad|kindle|playbook|silk|android(?!.*mobile)/i', $ua)) {
            return 'tablet';
        }
        if (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|iemobile|windows phone/i', $ua)) {
            return 'mobile';
        }
        return 'desktop';
    }

    // ── 브라우저 감지 ────────────────────────────────────────────
    public static function detectBrowser(string $ua): string
    {
        if (str_contains($ua, 'Edg/'))       return 'Edge';
        if (str_contains($ua, 'OPR/') || str_contains($ua, 'Opera')) return 'Opera';
        if (str_contains($ua, 'Chrome/'))    return 'Chrome';
        if (str_contains($ua, 'Safari/') && str_contains($ua, 'Version/')) return 'Safari';
        if (str_contains($ua, 'Firefox/'))   return 'Firefox';
        if (str_contains($ua, 'MSIE') || str_contains($ua, 'Trident/')) return 'IE';
        if (str_contains($ua, 'SamsungBrowser')) return 'Samsung';
        if (str_contains($ua, 'Instagram'))  return 'Instagram';
        if (str_contains($ua, 'KAKAOTALK')) return 'KakaoTalk';
        if (str_contains($ua, 'NaverSearchApp')) return 'Naver App';
        return 'Other';
    }

    // ── OS 감지 ─────────────────────────────────────────────────
    public static function detectOS(string $ua): string
    {
        if (preg_match('/Windows NT ([\d.]+)/', $ua, $m)) return 'Windows';
        if (preg_match('/Android ([\d.]+)/', $ua, $m))    return 'Android';
        if (str_contains($ua, 'iPhone OS') || str_contains($ua, 'iOS')) return 'iOS';
        if (str_contains($ua, 'Mac OS X'))  return 'macOS';
        if (str_contains($ua, 'Linux'))     return 'Linux';
        return 'Other';
    }

    // ── 유입 경로 타입 분류 ─────────────────────────────────────
    public static function classifyReferrer(?string $domain): string
    {
        if (!$domain) return 'direct';
        $d = strtolower($domain);
        $search = ['google', 'naver', 'daum', 'bing', 'yahoo', 'baidu', 'duckduckgo', 'zum'];
        $social = ['facebook', 'instagram', 'twitter', 'x.com', 't.co', 'youtube', 'tiktok',
                   'linkedin', 'pinterest', 'kakaostory', 'band.us'];
        foreach ($search as $s) { if (str_contains($d, $s)) return 'search'; }
        foreach ($social  as $s) { if (str_contains($d, $s)) return 'social'; }
        return 'referral';
    }

    // ── 봇 감지 ─────────────────────────────────────────────────
    public static function isBot(string $ua): bool
    {
        return (bool) preg_match(
            '/bot|crawl|spider|slurp|bingpreview|pinterest|facebook|twitter|whatsapp|telegram|curl|wget|python|java|ruby|go-http|axios|node-fetch/i',
            $ua
        );
    }

    // ── 도메인 추출 ──────────────────────────────────────────────
    public static function extractDomain(?string $url): ?string
    {
        if (!$url) return null;
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) return null;
        return preg_replace('/^www\./', '', $host);
    }
}
