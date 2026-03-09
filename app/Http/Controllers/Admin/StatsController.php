<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30'); // 7 / 30 / 90 / all

        $query = PageView::query();
        if ($period !== 'all') {
            $query->where('created_at', '>=', now()->subDays((int)$period));
        }

        // ── 요약 카드 ─────────────────────────────────────────
        $totalViews   = (clone $query)->count();
        $uniqueVisitors = (clone $query)->distinct('ip_hash')->count('ip_hash');
        $todayViews   = PageView::whereDate('created_at', today())->count();
        $yesterdayViews = PageView::whereDate('created_at', today()->subDay())->count();

        // ── 일별 트렌드 (최근 30일 고정) ──────────────────────
        $days = 30;
        $dailyStats = PageView::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as views'),
                DB::raw('COUNT(DISTINCT ip_hash) as unique_visitors')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // 날짜 빈 구간 채우기
        $dailyChart = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyChart[] = [
                'date'   => $date,
                'label'  => now()->subDays($i)->format('m/d'),
                'views'  => $dailyStats[$date]->views ?? 0,
                'unique' => $dailyStats[$date]->unique_visitors ?? 0,
            ];
        }

        // ── 상위 페이지 ────────────────────────────────────────
        $topPages = (clone $query)
            ->select('path', DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT ip_hash) as unique_visitors'))
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(20)
            ->get();

        // ── 상위 유입처 (도메인) ───────────────────────────────
        $topReferrers = (clone $query)
            ->select('referrer_domain', DB::raw('COUNT(*) as views'))
            ->whereNotNull('referrer_domain')
            ->groupBy('referrer_domain')
            ->orderByDesc('views')
            ->limit(20)
            ->get();

        // ── 유입 경로 타입 ─────────────────────────────────────
        $referrerTypes = (clone $query)
            ->select('referrer_type', DB::raw('COUNT(*) as count'))
            ->groupBy('referrer_type')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(fn($r) => [$r->referrer_type => $r->count]);

        // ── 기기 분류 ─────────────────────────────────────────
        $deviceStats = (clone $query)
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(fn($r) => [$r->device_type => $r->count]);

        // ── 브라우저 분류 ──────────────────────────────────────
        $browserStats = (clone $query)
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

        // ── OS 분류 ───────────────────────────────────────────
        $osStats = (clone $query)
            ->select('os', DB::raw('COUNT(*) as count'))
            ->whereNotNull('os')
            ->groupBy('os')
            ->orderByDesc('count')
            ->get();

        // ── 최근 방문 목록 ─────────────────────────────────────
        $recentViews = PageView::orderByDesc('created_at')->limit(50)->get();

        return view('admin.stats.index', compact(
            'period', 'totalViews', 'uniqueVisitors', 'todayViews', 'yesterdayViews',
            'dailyChart', 'topPages', 'topReferrers', 'referrerTypes',
            'deviceStats', 'browserStats', 'osStats', 'recentViews'
        ));
    }
}
