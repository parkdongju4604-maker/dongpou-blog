@extends('layouts.admin')
@section('title', '사이트 통계')

@section('content')
<style>
/* ── 통계 페이지 스타일 ── */
.stats-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.stats-header h1 { font-size: 1.4rem; font-weight: 700; color: #0f172a; margin: 0; }
.period-tabs { display: flex; gap: 4px; background: #f1f5f9; padding: 4px; border-radius: 8px; }
.period-tabs a {
    padding: 6px 16px; border-radius: 6px; font-size: .82rem; font-weight: 600;
    color: #64748b; text-decoration: none; transition: all .15s;
}
.period-tabs a.active { background: #fff; color: #4f46e5; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
.period-tabs a:hover:not(.active) { color: #334155; }

/* ── 요약 카드 ── */
.summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
@media (max-width: 900px) { .summary-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 500px) { .summary-grid { grid-template-columns: 1fr; } }
.stat-card {
    background: #fff; border-radius: 12px; padding: 20px 22px;
    border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,.04);
    display: flex; flex-direction: column; gap: 6px;
}
.stat-card .label { font-size: .78rem; font-weight: 600; color: #94a3b8; letter-spacing: .04em; text-transform: uppercase; }
.stat-card .value { font-size: 2rem; font-weight: 800; color: #0f172a; line-height: 1; }
.stat-card .sub { font-size: .78rem; color: #94a3b8; }
.stat-card .badge-up { color: #10b981; font-weight: 600; }
.stat-card .badge-down { color: #ef4444; font-weight: 600; }
.stat-card.accent-purple { border-top: 3px solid #4f46e5; }
.stat-card.accent-blue   { border-top: 3px solid #3b82f6; }
.stat-card.accent-green  { border-top: 3px solid #10b981; }
.stat-card.accent-orange { border-top: 3px solid #f59e0b; }

/* ── 그리드 레이아웃 ── */
.stats-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px; }
.stats-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px; }
@media (max-width: 1000px) { .stats-grid, .stats-grid-3 { grid-template-columns: 1fr; } }

/* ── 카드 ── */
.s-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
.s-card-header { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
.s-card-header h3 { font-size: .9rem; font-weight: 700; color: #334155; margin: 0; }
.s-card-header .badge { font-size: .72rem; background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 99px; font-weight: 600; }
.s-card-body { padding: 20px; }

/* ── 차트 ── */
.chart-wrap { position: relative; height: 220px; }

/* ── 테이블 ── */
.stats-table { width: 100%; border-collapse: collapse; font-size: .84rem; }
.stats-table th { text-align: left; font-size: .72rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; padding: 0 0 10px; border-bottom: 1px solid #f1f5f9; }
.stats-table th:not(:first-child) { text-align: right; }
.stats-table td { padding: 9px 0; border-bottom: 1px solid #f8fafc; color: #374151; vertical-align: middle; }
.stats-table td:not(:first-child) { text-align: right; font-weight: 600; color: #0f172a; }
.stats-table tr:last-child td { border-bottom: none; }
.stats-table .path-cell { max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #4f46e5; }
.stats-table .bar-cell { display: flex; align-items: center; gap: 8px; justify-content: flex-end; }
.mini-bar { height: 6px; border-radius: 3px; background: #4f46e5; min-width: 2px; flex-shrink: 0; }

/* ── 도넛 차트 대체 (CSS) ── */
.device-pie { display: flex; flex-direction: column; gap: 12px; }
.device-row { display: flex; align-items: center; gap: 10px; }
.device-label { width: 80px; font-size: .82rem; color: #374151; flex-shrink: 0; }
.device-bar-wrap { flex: 1; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }
.device-bar { height: 100%; border-radius: 4px; }
.device-pct { width: 42px; text-align: right; font-size: .82rem; font-weight: 700; color: #0f172a; }
.device-count { font-size: .75rem; color: #94a3b8; width: 50px; text-align: right; }

/* ── 유입 타입 ── */
.ref-type-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 12px; }
.ref-type-card {
    padding: 14px; border-radius: 10px; text-align: center;
    background: #f8fafc; border: 1px solid #f1f5f9;
}
.ref-type-card .rt-icon { font-size: 1.5rem; margin-bottom: 4px; }
.ref-type-card .rt-val  { font-size: 1.4rem; font-weight: 800; color: #0f172a; }
.ref-type-card .rt-lbl  { font-size: .72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; }

/* ── 최근 방문 ── */
.recent-table { width: 100%; border-collapse: collapse; font-size: .8rem; }
.recent-table th { text-align: left; font-size: .7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em; padding: 0 8px 10px 0; border-bottom: 1px solid #f1f5f9; }
.recent-table td { padding: 7px 8px 7px 0; border-bottom: 1px solid #f8fafc; color: #374151; vertical-align: top; }
.recent-table tr:last-child td { border-bottom: none; }
.device-icon { font-size: .9rem; }
</style>

<div class="stats-header">
    <h1>📊 사이트 통계</h1>
    <div class="period-tabs">
        @foreach(['7' => '7일', '30' => '30일', '90' => '90일', 'all' => '전체'] as $p => $label)
            <a href="{{ route('admin.stats.index', ['period' => $p]) }}"
               class="{{ $period === $p ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

{{-- ── 요약 카드 ── --}}
<div class="summary-grid">
    <div class="stat-card accent-purple">
        <div class="label">오늘 방문</div>
        <div class="value">{{ number_format($todayViews) }}</div>
        <div class="sub">
            어제
            @if($yesterdayViews > 0)
                @php $diff = $todayViews - $yesterdayViews; $pct = round(abs($diff)/$yesterdayViews*100); @endphp
                <span class="{{ $diff >= 0 ? 'badge-up' : 'badge-down' }}">
                    {{ $diff >= 0 ? '▲' : '▼' }} {{ $pct }}%
                </span>
                ({{ number_format($yesterdayViews) }})
            @else
                {{ number_format($yesterdayViews) }}
            @endif
        </div>
    </div>
    <div class="stat-card accent-blue">
        <div class="label">기간 내 총 페이지뷰</div>
        <div class="value">{{ number_format($totalViews) }}</div>
        <div class="sub">{{ $period === 'all' ? '전체 기간' : "최근 {$period}일" }}</div>
    </div>
    <div class="stat-card accent-green">
        <div class="label">순 방문자 (IP 기준)</div>
        <div class="value">{{ number_format($uniqueVisitors) }}</div>
        <div class="sub">{{ $period === 'all' ? '전체 기간' : "최근 {$period}일" }}</div>
    </div>
    <div class="stat-card accent-orange">
        <div class="label">페이지당 평균</div>
        <div class="value">{{ $uniqueVisitors > 0 ? round($totalViews / $uniqueVisitors, 1) : 0 }}</div>
        <div class="sub">뷰 / 방문자</div>
    </div>
</div>

{{-- ── 일별 트렌드 + 유입 경로 ── --}}
<div class="stats-grid">
    <div class="s-card">
        <div class="s-card-header">
            <h3>📈 일별 트렌드 (최근 30일)</h3>
            <span class="badge">페이지뷰 / 순방문자</span>
        </div>
        <div class="s-card-body">
            <div class="chart-wrap">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>
    </div>

    <div class="s-card">
        <div class="s-card-header"><h3>🔗 유입 경로 분류</h3></div>
        <div class="s-card-body">
            <div class="ref-type-grid">
                @php
                    $rtIcons = ['direct'=>'🔗','search'=>'🔍','social'=>'📱','referral'=>'🌐'];
                    $rtNames = ['direct'=>'직접 방문','search'=>'검색','social'=>'소셜','referral'=>'외부 링크'];
                    $rtTotal = $referrerTypes->sum();
                @endphp
                @foreach($rtIcons as $type => $icon)
                    <div class="ref-type-card">
                        <div class="rt-icon">{{ $icon }}</div>
                        <div class="rt-val">{{ number_format($referrerTypes[$type] ?? 0) }}</div>
                        <div class="rt-lbl">{{ $rtNames[$type] }}</div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 20px;">
                <div style="font-size:.78rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:12px;">기기 분류</div>
                <div class="device-pie">
                    @php
                        $deviceTotal = $deviceStats->sum();
                        $deviceColors = ['desktop'=>'#4f46e5','mobile'=>'#10b981','tablet'=>'#f59e0b'];
                        $deviceNames = ['desktop'=>'🖥 데스크톱','mobile'=>'📱 모바일','tablet'=>'📟 태블릿'];
                    @endphp
                    @foreach(['desktop','mobile','tablet'] as $dt)
                        @php $cnt = $deviceStats[$dt] ?? 0; $pct = $deviceTotal > 0 ? round($cnt/$deviceTotal*100) : 0; @endphp
                        <div class="device-row">
                            <div class="device-label" style="font-size:.8rem;">{{ $deviceNames[$dt] }}</div>
                            <div class="device-bar-wrap">
                                <div class="device-bar" style="width:{{ $pct }}%;background:{{ $deviceColors[$dt] }};"></div>
                            </div>
                            <div class="device-pct">{{ $pct }}%</div>
                            <div class="device-count">{{ number_format($cnt) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── 상위 페이지 + 유입처 ── --}}
<div class="stats-grid" style="margin-bottom:16px;">
    <div class="s-card">
        <div class="s-card-header">
            <h3>📄 상위 페이지</h3>
            <span class="badge">상위 20</span>
        </div>
        <div class="s-card-body" style="padding:0 20px 16px;">
            @php $maxPageViews = $topPages->max('views') ?: 1; @endphp
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>URL</th>
                        <th>순방문</th>
                        <th style="min-width:120px;">뷰</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topPages as $page)
                        <tr>
                            <td>
                                <a href="{{ $page->path }}" target="_blank" class="path-cell" title="{{ $page->path }}">
                                    {{ $page->path }}
                                </a>
                            </td>
                            <td>{{ number_format($page->unique_visitors) }}</td>
                            <td>
                                <div class="bar-cell">
                                    <div class="mini-bar" style="width:{{ round($page->views/$maxPageViews*80) }}px;"></div>
                                    <span>{{ number_format($page->views) }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:24px 0;">데이터 없음</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="s-card">
        <div class="s-card-header">
            <h3>🌍 상위 유입처</h3>
            <span class="badge">도메인 기준</span>
        </div>
        <div class="s-card-body" style="padding:0 20px 16px;">
            @php $maxRefViews = $topReferrers->max('views') ?: 1; @endphp
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>도메인</th>
                        <th style="min-width:120px;">뷰</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topReferrers as $ref)
                        <tr>
                            <td style="color:#374151;">{{ $ref->referrer_domain ?? '(직접)' }}</td>
                            <td>
                                <div class="bar-cell">
                                    <div class="mini-bar" style="width:{{ round($ref->views/$maxRefViews*80) }}px;background:#10b981;"></div>
                                    <span>{{ number_format($ref->views) }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" style="text-align:center;color:#94a3b8;padding:24px 0;">외부 유입 없음</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── 브라우저 + OS ── --}}
<div class="stats-grid-3" style="margin-bottom:16px;">
    <div class="s-card">
        <div class="s-card-header"><h3>🌐 브라우저</h3></div>
        <div class="s-card-body" style="padding:0 20px 16px;">
            @php $maxBrowser = $browserStats->max('count') ?: 1; @endphp
            <table class="stats-table">
                <thead><tr><th>브라우저</th><th>방문</th></tr></thead>
                <tbody>
                    @forelse($browserStats as $b)
                        <tr>
                            <td>{{ $b->browser }}</td>
                            <td>
                                <div class="bar-cell">
                                    <div class="mini-bar" style="width:{{ round($b->count/$maxBrowser*60) }}px;background:#8b5cf6;"></div>
                                    {{ number_format($b->count) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" style="text-align:center;color:#94a3b8;padding:16px 0;">데이터 없음</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="s-card">
        <div class="s-card-header"><h3>💻 운영체제</h3></div>
        <div class="s-card-body" style="padding:0 20px 16px;">
            @php $maxOS = $osStats->max('count') ?: 1; @endphp
            <table class="stats-table">
                <thead><tr><th>OS</th><th>방문</th></tr></thead>
                <tbody>
                    @forelse($osStats as $o)
                        <tr>
                            <td>{{ $o->os }}</td>
                            <td>
                                <div class="bar-cell">
                                    <div class="mini-bar" style="width:{{ round($o->count/$maxOS*60) }}px;background:#f59e0b;"></div>
                                    {{ number_format($o->count) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" style="text-align:center;color:#94a3b8;padding:16px 0;">데이터 없음</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="s-card">
        <div class="s-card-header"><h3>🕐 최근 방문</h3></div>
        <div class="s-card-body" style="padding:0 20px 16px;max-height:340px;overflow-y:auto;">
            <table class="recent-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>경로</th>
                        <th>시간</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentViews as $v)
                        <tr>
                            <td class="device-icon" style="width:20px;">
                                {{ $v->device_type === 'mobile' ? '📱' : ($v->device_type === 'tablet' ? '📟' : '🖥') }}
                            </td>
                            <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#4f46e5;">
                                {{ $v->path }}
                            </td>
                            <td style="color:#94a3b8;white-space:nowrap;font-size:.72rem;">
                                {{ \Carbon\Carbon::parse($v->created_at)->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:16px 0;">기록 없음</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels = @json(array_column($dailyChart, 'label'));
    const views  = @json(array_column($dailyChart, 'views'));
    const unique = @json(array_column($dailyChart, 'unique'));

    const ctx = document.getElementById('dailyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: '페이지뷰',
                    data: views,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,.08)',
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.35,
                },
                {
                    label: '순방문자',
                    data: unique,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,.06)',
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.35,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { font: { size: 12 }, padding: 16 } },
                tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8 },
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 }, maxTicksLimit: 10 } },
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
            },
        },
    });
})();
</script>
@endsection
