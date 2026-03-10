@extends('layouts.admin')
@section('title', '보안 관리')
@section('page-title', '보안 관리')

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 18px;margin-bottom:16px;color:#166534;font-size:.875rem">
    ✅ {{ session('success') }}
</div>
@endif

{{-- 요약 카드 --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px">
    @php
        $activeRules  = $rules->where('is_active', true)->count();
        $uaRules      = $rules->where('type','useragent')->count();
        $ipRules      = $rules->where('type','ip')->count();
    @endphp
    <div style="padding:16px 18px;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px">
        <div style="font-size:1.5rem;font-weight:800;color:#dc2626">{{ $activeRules }}</div>
        <div style="font-size:.78rem;color:#64748b;margin-top:2px">활성 차단 규칙</div>
    </div>
    <div style="padding:16px 18px;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px">
        <div style="font-size:1.5rem;font-weight:800;color:#d97706">{{ $uaRules }}</div>
        <div style="font-size:.78rem;color:#64748b;margin-top:2px">UA 규칙</div>
    </div>
    <div style="padding:16px 18px;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px">
        <div style="font-size:1.5rem;font-weight:800;color:#7c3aed">{{ $ipRules }}</div>
        <div style="font-size:.78rem;color:#64748b;margin-top:2px">IP 규칙</div>
    </div>
</div>

{{-- 탭 --}}
<div style="display:flex;gap:0;border-bottom:2px solid #e2e8f0;margin-bottom:20px">
    <a href="?tab=ua"
       style="padding:10px 22px;font-size:.875rem;font-weight:600;text-decoration:none;
              border-bottom:2px solid {{ $tab==='ua' ? '#4f46e5' : 'transparent' }};
              color:{{ $tab==='ua' ? '#4f46e5' : '#64748b' }};margin-bottom:-2px">
        🤖 User-Agent
    </a>
    <a href="?tab=ip"
       style="padding:10px 22px;font-size:.875rem;font-weight:600;text-decoration:none;
              border-bottom:2px solid {{ $tab==='ip' ? '#4f46e5' : 'transparent' }};
              color:{{ $tab==='ip' ? '#4f46e5' : '#64748b' }};margin-bottom:-2px">
        🌐 IP 주소
    </a>
</div>

{{-- ══ UA 탭 ══ --}}
@if($tab === 'ua')

{{-- UA 차단 규칙 추가 --}}
<div class="card" style="margin-bottom:16px">
    <div class="card-header"><h3>➕ UA 차단 키워드 추가</h3></div>
    <div class="card-body" style="padding:16px 20px">
        <form action="{{ route('admin.security.rules.store') }}" method="POST"
              style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
            @csrf
            <input type="hidden" name="type" value="useragent">
            <div style="flex:1;min-width:200px">
                <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:4px">키워드 (포함 시 차단)</label>
                <input type="text" name="value" class="form-control" required placeholder="예: bot, crawler, scrapy, python-requests"
                       style="font-family:monospace">
            </div>
            <div style="flex:1;min-width:160px">
                <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:4px">메모 (선택)</label>
                <input type="text" name="note" class="form-control" placeholder="차단 이유">
            </div>
            <button type="submit" class="btn btn-danger" style="white-space:nowrap">🚫 차단 추가</button>
        </form>
    </div>
</div>

{{-- UA 차단 목록 --}}
@php $uaBlockRules = $rules->where('type','useragent'); @endphp
@if($uaBlockRules->isNotEmpty())
<div class="card" style="margin-bottom:16px">
    <div class="card-header"><h3>🚫 UA 차단 목록 <span style="font-size:.8rem;color:#94a3b8;font-weight:400">{{ $uaBlockRules->count() }}개</span></h3></div>
    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr>
                <th>키워드</th><th>메모</th><th>상태</th><th>등록일</th><th>관리</th>
            </tr></thead>
            <tbody>
                @foreach($uaBlockRules as $rule)
                <tr style="{{ !$rule->is_active ? 'opacity:.5' : '' }}">
                    <td><code style="background:#fef2f2;color:#dc2626;padding:2px 8px;border-radius:4px;font-size:.82rem">{{ $rule->value }}</code></td>
                    <td style="color:#64748b;font-size:.84rem">{{ $rule->note ?: '—' }}</td>
                    <td>
                        <form action="{{ route('admin.security.rules.toggle', $rule) }}" method="POST" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $rule->is_active ? 'btn-primary' : 'btn-secondary' }}">
                                {{ $rule->is_active ? '✅ 활성' : '⏸ 비활성' }}
                            </button>
                        </form>
                    </td>
                    <td style="color:#94a3b8;font-size:.8rem">{{ $rule->created_at->format('m.d H:i') }}</td>
                    <td>
                        <form action="{{ route('admin.security.rules.destroy', $rule) }}" method="POST"
                              onsubmit="return confirm('삭제하시겠습니까?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">삭제</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- UA 접속 로그 --}}
<div class="card">
    <div class="card-header">
        <h3>📋 User-Agent 접속 기록
            <span style="font-size:.8rem;color:#94a3b8;font-weight:400">최근 100종</span>
        </h3>
        <form action="{{ route('admin.security.logs.clear') }}" method="POST"
              onsubmit="return confirm('로그를 모두 초기화하시겠습니까?')">
            @csrf @method('DELETE')
            <button class="btn btn-secondary btn-sm">🗑 로그 초기화</button>
        </form>
    </div>
    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr>
                <th>User-Agent</th>
                <th style="white-space:nowrap">접속 횟수</th>
                <th style="white-space:nowrap">고유 IP</th>
                <th style="white-space:nowrap">최근 접속</th>
                <th>차단</th>
            </tr></thead>
            <tbody>
                @forelse($uaLogs as $log)
                @php
                    $isBlocked = $uaBlockRules->contains(fn($r) =>
                        str_contains(strtolower($log->user_agent), strtolower($r->value))
                    );
                @endphp
                <tr style="{{ $isBlocked ? 'background:#fff5f5' : '' }}">
                    <td style="max-width:420px">
                        <div style="font-size:.78rem;font-family:monospace;color:#374151;
                             overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
                             max-width:420px" title="{{ $log->user_agent }}">
                            {{ $log->user_agent ?: '(없음)' }}
                        </div>
                        @if($isBlocked)
                            <span style="font-size:.68rem;background:#fee2e2;color:#dc2626;padding:1px 6px;border-radius:4px">차단됨</span>
                        @endif
                    </td>
                    <td style="font-weight:700;color:#1e293b;white-space:nowrap">{{ number_format($log->total) }}</td>
                    <td style="color:#64748b">{{ $log->unique_ips }}</td>
                    <td style="color:#94a3b8;font-size:.8rem;white-space:nowrap">
                        {{ \Carbon\Carbon::parse($log->last_seen)->format('m.d H:i') }}
                    </td>
                    <td>
                        @if(!$isBlocked)
                        <form action="{{ route('admin.security.rules.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="useragent">
                            <input type="hidden" name="value" value="{{ $log->user_agent }}">
                            <input type="hidden" name="note" value="로그에서 차단">
                            <button type="submit" class="btn btn-sm"
                                    style="background:#fff1f2;color:#dc2626;border:1px solid #fecaca"
                                    onclick="return confirm('이 UA를 차단하시겠습니까?\n\n{{ addslashes(substr($log->user_agent,0,80)) }}')">
                                🚫 차단
                            </button>
                        </form>
                        @else
                            <span style="font-size:.78rem;color:#dc2626">차단 중</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:40px;color:#94a3b8">접속 기록이 없습니다.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══ IP 탭 ══ --}}
@else

{{-- IP 차단 규칙 추가 --}}
<div class="card" style="margin-bottom:16px">
    <div class="card-header"><h3>➕ IP 차단 추가</h3></div>
    <div class="card-body" style="padding:16px 20px">
        <form action="{{ route('admin.security.rules.store') }}" method="POST"
              style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
            @csrf
            <input type="hidden" name="type" value="ip">
            <div style="flex:1;min-width:180px">
                <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:4px">IP 주소</label>
                <input type="text" name="value" class="form-control" required placeholder="예: 123.45.67.89"
                       style="font-family:monospace">
            </div>
            <div style="flex:1;min-width:160px">
                <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:4px">메모 (선택)</label>
                <input type="text" name="note" class="form-control" placeholder="차단 이유">
            </div>
            <button type="submit" class="btn btn-danger" style="white-space:nowrap">🚫 차단 추가</button>
        </form>
    </div>
</div>

{{-- IP 차단 목록 --}}
@php $ipBlockRules = $rules->where('type','ip'); @endphp
@if($ipBlockRules->isNotEmpty())
<div class="card" style="margin-bottom:16px">
    <div class="card-header"><h3>🚫 IP 차단 목록 <span style="font-size:.8rem;color:#94a3b8;font-weight:400">{{ $ipBlockRules->count() }}개</span></h3></div>
    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr>
                <th>IP 주소</th><th>메모</th><th>상태</th><th>등록일</th><th>관리</th>
            </tr></thead>
            <tbody>
                @foreach($ipBlockRules as $rule)
                <tr style="{{ !$rule->is_active ? 'opacity:.5' : '' }}">
                    <td><code style="background:#fef2f2;color:#dc2626;padding:2px 8px;border-radius:4px;font-size:.85rem">{{ $rule->value }}</code></td>
                    <td style="color:#64748b;font-size:.84rem">{{ $rule->note ?: '—' }}</td>
                    <td>
                        <form action="{{ route('admin.security.rules.toggle', $rule) }}" method="POST" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $rule->is_active ? 'btn-primary' : 'btn-secondary' }}">
                                {{ $rule->is_active ? '✅ 활성' : '⏸ 비활성' }}
                            </button>
                        </form>
                    </td>
                    <td style="color:#94a3b8;font-size:.8rem">{{ $rule->created_at->format('m.d H:i') }}</td>
                    <td>
                        <form action="{{ route('admin.security.rules.destroy', $rule) }}" method="POST"
                              onsubmit="return confirm('삭제하시겠습니까?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">삭제</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- IP 접속 로그 --}}
<div class="card">
    <div class="card-header">
        <h3>📋 IP 접속 기록
            <span style="font-size:.8rem;color:#94a3b8;font-weight:400">최근 100개</span>
        </h3>
        <form action="{{ route('admin.security.logs.clear') }}" method="POST"
              onsubmit="return confirm('로그를 모두 초기화하시겠습니까?')">
            @csrf @method('DELETE')
            <button class="btn btn-secondary btn-sm">🗑 로그 초기화</button>
        </form>
    </div>
    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr>
                <th>IP 주소</th>
                <th style="white-space:nowrap">접속 횟수</th>
                <th style="white-space:nowrap">고유 UA</th>
                <th style="white-space:nowrap">최근 접속</th>
                <th>차단</th>
            </tr></thead>
            <tbody>
                @forelse($ipLogs as $log)
                @php
                    $isBlocked = $ipBlockRules->contains(fn($r) => $r->value === $log->ip);
                @endphp
                <tr style="{{ $isBlocked ? 'background:#fff5f5' : '' }}">
                    <td>
                        <code style="font-size:.84rem;color:#1e293b">{{ $log->ip }}</code>
                        @if($isBlocked)
                            <span style="font-size:.68rem;background:#fee2e2;color:#dc2626;padding:1px 6px;border-radius:4px;margin-left:4px">차단됨</span>
                        @endif
                    </td>
                    <td style="font-weight:700;color:#1e293b;white-space:nowrap">{{ number_format($log->total) }}</td>
                    <td style="color:#64748b">{{ $log->unique_uas }}</td>
                    <td style="color:#94a3b8;font-size:.8rem;white-space:nowrap">
                        {{ \Carbon\Carbon::parse($log->last_seen)->format('m.d H:i') }}
                    </td>
                    <td>
                        @if(!$isBlocked)
                        <form action="{{ route('admin.security.rules.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="ip">
                            <input type="hidden" name="value" value="{{ $log->ip }}">
                            <input type="hidden" name="note" value="로그에서 차단">
                            <button type="submit" class="btn btn-sm"
                                    style="background:#fff1f2;color:#dc2626;border:1px solid #fecaca"
                                    onclick="return confirm('{{ $log->ip }} 를 차단하시겠습니까?')">
                                🚫 차단
                            </button>
                        </form>
                        @else
                            <span style="font-size:.78rem;color:#dc2626">차단 중</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:40px;color:#94a3b8">접속 기록이 없습니다.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endif

@endsection
