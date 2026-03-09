@extends('layouts.admin')
@section('title', 'CSS 테마 관리')

@section('content')
<style>
.themes-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; flex-wrap: wrap; gap: 12px; }
.themes-header h1 { font-size: 1.4rem; font-weight: 700; color: #0f172a; margin: 0; }

.theme-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
.theme-card {
    background: #fff; border-radius: 14px; border: 2px solid #e2e8f0;
    overflow: hidden; transition: border-color .2s, box-shadow .2s;
}
.theme-card.active { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
.theme-card:not(.active):hover { border-color: #c7d2fe; }

.theme-preview {
    height: 140px; position: relative; overflow: hidden;
    display: flex; flex-direction: column;
}
.preview-header { height: 32px; display: flex; align-items: center; padding: 0 14px; gap: 6px; }
.preview-dot { width: 6px; height: 6px; border-radius: 50%; }
.preview-logo { height: 8px; border-radius: 3px; width: 60px; margin-left: 4px; }
.preview-body { flex: 1; padding: 12px 14px; display: flex; flex-direction: column; gap: 8px; }
.preview-title { height: 10px; border-radius: 3px; width: 80%; }
.preview-line { height: 6px; border-radius: 2px; }
.preview-line.w60 { width: 60%; }
.preview-line.w80 { width: 80%; }
.preview-line.w45 { width: 45%; }
.preview-cards { display: flex; gap: 6px; margin-top: 4px; }
.preview-card-mini { flex: 1; border-radius: 4px; height: 28px; }

.theme-info { padding: 16px 18px; }
.theme-name { font-size: .95rem; font-weight: 700; color: #0f172a; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
.theme-desc { font-size: .8rem; color: #64748b; margin-bottom: 14px; }
.active-badge { font-size: .65rem; font-weight: 700; background: #4f46e5; color: #fff; padding: 2px 8px; border-radius: 99px; text-transform: uppercase; letter-spacing: .05em; }

.theme-actions { display: flex; gap: 8px; }
.btn-sm { padding: 7px 14px; border-radius: 7px; font-size: .8rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all .15s; display: inline-flex; align-items: center; gap: 5px; }
.btn-edit { background: #f1f5f9; color: #475569; }
.btn-edit:hover { background: #e2e8f0; }
.btn-activate { background: #4f46e5; color: #fff; }
.btn-activate:hover { background: #4338ca; }
.btn-activated { background: #dcfce7; color: #16a34a; cursor: default; }
.btn-delete { background: #fef2f2; color: #ef4444; }
.btn-delete:hover { background: #fee2e2; }

/* 새 테마 생성 폼 */
.new-theme-card {
    background: #f8fafc; border-radius: 14px; border: 2px dashed #e2e8f0;
    padding: 28px; display: flex; flex-direction: column; justify-content: center;
    align-items: center; text-align: center; gap: 16px; min-height: 260px;
}
.new-theme-card h3 { font-size: .95rem; font-weight: 700; color: #334155; }
.new-theme-card p { font-size: .8rem; color: #94a3b8; }
.new-theme-form { width: 100%; display: flex; flex-direction: column; gap: 10px; }
.new-theme-form input { width: 100%; padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: .85rem; outline: none; }
.new-theme-form input:focus { border-color: #4f46e5; }
.btn-create { background: #4f46e5; color: #fff; padding: 9px 20px; border-radius: 8px; font-size: .85rem; font-weight: 600; border: none; cursor: pointer; width: 100%; transition: background .15s; }
.btn-create:hover { background: #4338ca; }
</style>

<div class="themes-header">
    <h1>🎨 CSS 테마 관리</h1>
</div>

@if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #86efac;color:#16a34a;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:.875rem;font-weight:600;">
        ✅ {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#fef2f2;border:1px solid #fca5a5;color:#ef4444;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:.875rem;font-weight:600;">
        ⚠️ {{ session('error') }}
    </div>
@endif

<div class="theme-grid">
    @foreach($themes as $theme)
        @php
            $pc  = $theme->preview_color;
            $isDark = in_array($theme->name, ['Dark Mode']) || str_contains(strtolower($theme->description ?? ''), 'dark');
            $bgColor = $isDark ? '#0f172a' : '#ffffff';
            $headerBg = $isDark ? '#1e293b' : '#f8fafc';
            $cardBg  = $isDark ? '#1e293b' : '#f1f5f9';
            $lineColor = $isDark ? '#334155' : '#e2e8f0';
        @endphp
        <div class="theme-card {{ $theme->is_active ? 'active' : '' }}">
            {{-- 미리보기 --}}
            <div class="theme-preview" style="background: {{ $bgColor }};">
                <div class="preview-header" style="background: {{ $headerBg }}; border-bottom: 1px solid {{ $lineColor }};">
                    <div class="preview-dot" style="background: {{ $pc }};"></div>
                    <div class="preview-logo" style="background: {{ $lineColor }};"></div>
                </div>
                <div class="preview-body">
                    <div class="preview-title" style="background: {{ $pc }}; opacity: .7;"></div>
                    <div class="preview-line w80" style="background: {{ $lineColor }};"></div>
                    <div class="preview-line w60" style="background: {{ $lineColor }};"></div>
                    <div class="preview-cards">
                        <div class="preview-card-mini" style="background: {{ $cardBg }}; border: 1px solid {{ $lineColor }};"></div>
                        <div class="preview-card-mini" style="background: {{ $cardBg }}; border: 1px solid {{ $lineColor }};"></div>
                        <div class="preview-card-mini" style="background: {{ $cardBg }}; border: 1px solid {{ $lineColor }};"></div>
                    </div>
                </div>
            </div>

            <div class="theme-info">
                <div class="theme-name">
                    <span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:{{ $pc }};flex-shrink:0;"></span>
                    {{ $theme->name }}
                    @if($theme->is_active)
                        <span class="active-badge">Active</span>
                    @endif
                </div>
                <div class="theme-desc">{{ $theme->description ?? '-' }}</div>

                <div class="theme-actions">
                    <a href="{{ route('admin.themes.edit', $theme) }}" class="btn-sm btn-edit">✏️ 편집</a>
                    @if($theme->is_active)
                        <span class="btn-sm btn-activated">✅ 적용중</span>
                    @else
                        <form method="POST" action="{{ route('admin.themes.activate', $theme) }}" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-sm btn-activate">🎨 활성화</button>
                        </form>
                    @endif
                    @if(!$theme->is_active)
                        <form method="POST" action="{{ route('admin.themes.destroy', $theme) }}" style="display:inline;" onsubmit="return confirm('정말 삭제하시겠습니까?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-delete">🗑</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    {{-- 새 테마 생성 --}}
    <div class="new-theme-card">
        <h3>➕ 새 테마 만들기</h3>
        <p>빈 CSS 에디터에서 새 테마를 작성하거나<br>기존 테마를 복사해 변형하세요.</p>
        <form method="POST" action="{{ route('admin.themes.store') }}" class="new-theme-form">
            @csrf
            <input type="text" name="name" placeholder="테마 이름" required>
            <input type="text" name="description" placeholder="테마 설명 (선택)">
            <input type="color" name="preview_color" value="#4f46e5" style="height:36px;cursor:pointer;">
            <button type="submit" class="btn-create">테마 생성 →</button>
        </form>
    </div>
</div>
@endsection
