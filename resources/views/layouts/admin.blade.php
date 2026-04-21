<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::get('blog_name', 'DongPou Blog') }} — @yield('title', '관리자')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: #1e293b; display: flex; min-height: 100vh; }

        /* ── 사이드바 ── */
        .sidebar {
            width: 240px; min-height: 100vh; background: #1e293b;
            display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 100;
        }
        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-logo a { text-decoration: none; }
        .sidebar-logo .brand {
            font-size: 1.15rem; font-weight: 800; color: #f8fafc; letter-spacing: -.3px;
        }

        .sidebar-nav { flex: 1; padding: 16px 0; }
        .nav-section { padding: 0 12px; margin-bottom: 6px; }
        .nav-label {
            font-size: .65rem; font-weight: 600; color: #475569;
            text-transform: uppercase; letter-spacing: 1px;
            padding: 8px 8px 4px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 10px; border-radius: 8px; margin-bottom: 2px;
            color: #94a3b8; font-size: .875rem; font-weight: 500;
            text-decoration: none; transition: background .15s, color .15s;
        }
        .nav-item:hover { background: rgba(255,255,255,.07); color: #e2e8f0; }
        .nav-item.active { background: #3730a3; color: #e0e7ff; }
        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.06);
        }
        .sidebar-footer form button {
            width: 100%; display: flex; align-items: center; gap: 10px;
            padding: 9px 10px; border-radius: 8px;
            background: none; border: none; cursor: pointer;
            color: #64748b; font-size: .875rem; font-weight: 500; transition: all .15s;
        }
        .sidebar-footer form button:hover { background: rgba(255,255,255,.07); color: #e2e8f0; }
        .sidebar-footer form button svg { width: 17px; height: 17px; }

        /* ── 메인 영역 ── */
        .admin-main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* ── 상단 헤더 ── */
        .admin-header {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: 0 28px; height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .admin-header h1 { font-size: 1.05rem; font-weight: 700; color: #1e293b; }
        .header-right { display: flex; align-items: center; gap: 12px; }
        .admin-badge {
            font-size: .72rem; font-weight: 600;
            background: #ede9fe; color: #5b21b6;
            padding: 3px 10px; border-radius: 20px;
        }
        .btn-blog-link {
            font-size: .8rem; color: #64748b; text-decoration: none;
            padding: 5px 12px; border: 1px solid #e2e8f0; border-radius: 6px;
            transition: border-color .15s, color .15s;
        }
        .btn-blog-link:hover { border-color: #94a3b8; color: #1e293b; }

        /* ── 콘텐츠 ── */
        .admin-content { padding: 28px; flex: 1; }

        /* ── 공통 컴포넌트 ── */
        .page-title { font-size: 1.35rem; font-weight: 700; color: #0f172a; margin-bottom: 20px; }
        .card { background: #fff; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .card-header {
            padding: 16px 20px; border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-header h3 { font-size: .95rem; font-weight: 600; }
        .card-body { padding: 20px; }

        /* 버튼 */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 7px; font-size: .85rem; font-weight: 600; cursor: pointer; border: none; transition: all .15s; text-decoration: none; }
        .btn-primary { background: #4f46e5; color: #fff; }
        .btn-primary:hover { background: #4338ca; }
        .btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .btn-danger:hover { background: #fee2e2; }
        .btn-success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .btn-success:hover { background: #dcfce7; }
        .btn-sm { padding: 5px 11px; font-size: .78rem; }

        /* 알럿 */
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: .875rem; }
        .alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .alert-danger  { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        /* 폼 */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: .82rem; font-weight: 600; color: #374151; margin-bottom: 5px; }
        .form-label .hint { font-weight: 400; color: #94a3b8; font-size: .75rem; margin-left: 4px; }
        .form-control {
            width: 100%; padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 7px;
            font-size: .9rem; color: #1e293b; outline: none; transition: border .15s;
            background: #fff;
        }
        .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
        textarea.form-control { min-height: 120px; resize: vertical; font-family: inherit; }
        input[type="number"].form-control { max-width: 120px; }
        input[type="color"].form-control { max-width: 80px; height: 38px; padding: 2px 4px; cursor: pointer; }

        /* 테이블 */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 10px 14px; text-align: left; font-size: .78rem;
            font-weight: 600; color: #64748b; background: #f8fafc;
            border-bottom: 1px solid #e2e8f0; white-space: nowrap;
        }
        tbody td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; font-size: .875rem; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #f8fafc; }

        /* 배지 */
        .badge { display: inline-block; padding: 2px 9px; border-radius: 12px; font-size: .73rem; font-weight: 600; }
        .badge-green  { background: #dcfce7; color: #15803d; }
        .badge-gray   { background: #f1f5f9; color: #64748b; }
        .badge-purple { background: #ede9fe; color: #6d28d9; }

        /* 통계 카드 */
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .stat-card .stat-label { font-size: .78rem; color: #64748b; font-weight: 500; margin-bottom: 6px; }
        .stat-card .stat-value { font-size: 2rem; font-weight: 800; color: #0f172a; }
        .stat-card .stat-sub { font-size: .75rem; color: #94a3b8; margin-top: 4px; }

        /* 설정 탭 */
        .tab-nav { display: flex; gap: 4px; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; }
        .tab-btn {
            padding: 10px 18px; font-size: .875rem; font-weight: 600; cursor: pointer;
            color: #64748b; background: none; border: none; border-bottom: 2px solid transparent;
            margin-bottom: -2px; transition: all .15s;
        }
        .tab-btn.active { color: #4f46e5; border-bottom-color: #4f46e5; }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        /* 반응형 */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .admin-main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── 사이드바 ── --}}
<aside class="sidebar">
    <div class="sidebar-logo">
        <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:8px;">
            <div class="brand">관리자 패널</div>
            <span style="font-size:.7rem;font-weight:600;color:#64748b;">v1.5.0</span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-label">메인</div>
            <a href="{{ route('admin.dashboard') }}"
               class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                대시보드
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">콘텐츠</div>
            <a href="{{ route('admin.posts.index') }}"
               class="nav-item {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/><polyline points="10,9 9,9 8,9"/>
                </svg>
                글 관리
            </a>
            <a href="{{ route('admin.categories.index') }}"
               class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                </svg>
                카테고리
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">커뮤니티</div>
            <a href="{{ route('admin.comments.index') }}"
               class="nav-item {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                댓글 관리
                @php $pendingComments = \App\Models\Comment::where('is_approved',false)->where('is_spam',false)->count(); @endphp
                @if($pendingComments > 0)
                    <span style="margin-left:auto;background:#ef4444;color:#fff;font-size:.65rem;font-weight:700;padding:1px 6px;border-radius:10px">{{ $pendingComments }}</span>
                @endif
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">분석</div>
            <a href="{{ route('admin.stats.index') }}"
               class="nav-item {{ request()->routeIs('admin.stats.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/>
                </svg>
                사이트 통계
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">디자인</div>
            <a href="{{ route('admin.themes.index') }}"
               class="nav-item {{ request()->routeIs('admin.themes.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/><path d="M20.39 4.61a1 1 0 00-1.42 0l-4.24 4.24a4 4 0 00-5.66 5.66l-3.54 3.54A1 1 0 107.95 19.46l3.54-3.54a4 4 0 005.66-5.66l4.24-4.24a1 1 0 000-1.41z"/>
                </svg>
                CSS 테마
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">설정</div>
            <a href="{{ route('admin.settings') }}"
               class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                </svg>
                사이트 설정
            </a>
            <a href="{{ route('admin.about.edit') }}"
               class="nav-item {{ request()->routeIs('admin.about.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                About 페이지
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">보안</div>
            <a href="{{ route('admin.security.index') }}"
               class="nav-item {{ request()->routeIs('admin.security.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                보안 관리
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">API</div>
            <a href="{{ route('admin.api-tokens.index') }}"
               class="nav-item {{ request()->routeIs('admin.api-tokens.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                </svg>
                토큰 관리
            </a>
            <a href="{{ route('admin.api-docs') }}"
               class="nav-item {{ request()->routeIs('admin.api-docs') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
                API 문서
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                    <polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                로그아웃
            </button>
        </form>
    </div>
</aside>

{{-- ── 메인 ── --}}
<div class="admin-main">
    <header class="admin-header">
        <h1>@yield('page-title', '관리자')</h1>
        <div class="header-right">
            <span class="admin-badge">Admin</span>
            <a href="{{ route('home') }}" class="btn-blog-link" target="_blank">블로그 보기 →</a>
        </div>
    </header>

    <div class="admin-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
