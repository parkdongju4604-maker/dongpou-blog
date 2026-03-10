<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use App\Models\BlockRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecurityController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'ua');   // 'ua' | 'ip'

        $rules = BlockRule::orderByDesc('created_at')->get();

        // UA 로그: UA 기준 집계 (상위 100개, 최근 접속순)
        $uaLogs = DB::table('access_logs')
            ->select('user_agent',
                DB::raw('SUM(count) as total'),
                DB::raw('MAX(last_seen_at) as last_seen'),
                DB::raw('COUNT(DISTINCT ip) as unique_ips'))
            ->groupBy('user_agent')
            ->orderByDesc('last_seen')
            ->limit(100)
            ->get();

        // IP 로그: IP 기준 집계 (상위 100개, 최근 접속순)
        $ipLogs = DB::table('access_logs')
            ->select('ip',
                DB::raw('SUM(count) as total'),
                DB::raw('MAX(last_seen_at) as last_seen'),
                DB::raw('COUNT(DISTINCT user_agent) as unique_uas'))
            ->groupBy('ip')
            ->orderByDesc('last_seen')
            ->limit(100)
            ->get();

        $totalLogs = DB::table('access_logs')->count();

        return view('admin.security.index',
            compact('rules', 'uaLogs', 'ipLogs', 'tab', 'totalLogs'));
    }

    /** 차단 규칙 추가 */
    public function store(Request $request)
    {
        $request->validate([
            'type'  => 'required|in:useragent,ip',
            'value' => 'required|string|max:500',
            'note'  => 'nullable|string|max:200',
        ]);

        BlockRule::create([
            'type'      => $request->type,
            'value'     => trim($request->value),
            'is_active' => true,
            'note'      => $request->note,
        ]);

        BlockRule::clearCache();

        return back()->with('success', '차단 규칙이 추가되었습니다.');
    }

    /** 활성/비활성 토글 */
    public function toggle(BlockRule $rule)
    {
        $rule->update(['is_active' => !$rule->is_active]);
        BlockRule::clearCache();
        return back()->with('success', $rule->is_active ? '활성화되었습니다.' : '비활성화되었습니다.');
    }

    /** 규칙 삭제 */
    public function destroy(BlockRule $rule)
    {
        $rule->delete();
        BlockRule::clearCache();
        return back()->with('success', '삭제되었습니다.');
    }

    /** 접속 로그 전체 초기화 */
    public function clearLogs()
    {
        DB::table('access_logs')->truncate();
        return back()->with('success', '접속 로그가 초기화되었습니다.');
    }
}
