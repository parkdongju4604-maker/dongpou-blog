@extends('layouts.admin')
@section('title', '사이트 설정')
@section('page-title', '사이트 설정')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}">
@csrf

{{-- 탭 네비게이션 --}}
<div class="tab-nav">
    <button type="button" class="tab-btn active" data-tab="general">기본 설정</button>
    <button type="button" class="tab-btn" data-tab="appearance">디자인</button>
    <button type="button" class="tab-btn" data-tab="seo">SEO</button>
    <button type="button" class="tab-btn" data-tab="verification">인증 코드</button>
</div>

{{-- ── 기본 설정 ── --}}
<div id="tab-general" class="tab-panel active">
    <div class="card">
        <div class="card-header"><h3>기본 설정</h3></div>
        <div class="card-body">
            @foreach(($settings['general'] ?? collect()) as $setting)
            @include('admin.settings._field', ['setting' => $setting])
            @endforeach
        </div>
    </div>
</div>

{{-- ── 디자인 ── --}}
<div id="tab-appearance" class="tab-panel">
    <div class="card">
        <div class="card-header"><h3>디자인</h3></div>
        <div class="card-body">
            @foreach(($settings['appearance'] ?? collect()) as $setting)
            @include('admin.settings._field', ['setting' => $setting])
            @endforeach
        </div>
    </div>
</div>

{{-- ── SEO ── --}}
<div id="tab-seo" class="tab-panel">
    <div class="card" style="margin-bottom:16px">
        <div class="card-header"><h3>기본 SEO</h3></div>
        <div class="card-body">
            @foreach(($settings['seo'] ?? collect()) as $setting)
            @include('admin.settings._field', ['setting' => $setting])
            @endforeach
        </div>
    </div>

    <div class="card" style="background:#f0f9ff;border:1px solid #bae6fd">
        <div class="card-body" style="padding:14px 18px">
            <p style="font-size:.82rem;color:#0369a1;font-weight:600;margin-bottom:4px">📌 sitemap.xml / robots.txt</p>
            <p style="font-size:.8rem;color:#0369a1">
                사이트맵과 robots.txt는 자동 생성됩니다.<br>
                <a href="{{ route('sitemap') }}" target="_blank" style="color:#0369a1;text-decoration:underline">/sitemap.xml</a> ·
                <a href="{{ route('robots') }}" target="_blank" style="color:#0369a1;text-decoration:underline">/robots.txt</a>
            </p>
        </div>
    </div>
</div>

{{-- ── 인증 코드 ── --}}
<div id="tab-verification" class="tab-panel">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

        {{-- Google --}}
        <div class="card">
            <div class="card-header">
                <h3 style="display:flex;align-items:center;gap:8px">
                    <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Google Search Console
                </h3>
            </div>
            <div class="card-body">
                <div style="background:#f8fafc;border-radius:8px;padding:14px;margin-bottom:16px;font-size:.8rem;color:#475569;line-height:1.7">
                    <strong>등록 방법:</strong><br>
                    1. <a href="https://search.google.com/search-console" target="_blank" style="color:#4f46e5">Google Search Console</a> 접속<br>
                    2. 속성 추가 → URL 접두어 방식<br>
                    3. HTML 태그 인증 선택<br>
                    4. <code style="background:#e2e8f0;padding:1px 5px;border-radius:3px">content="xxxxx"</code> 값만 복사하여 아래 입력
                </div>
                @php $gs = ($settings['verification'] ?? collect())->firstWhere('key', 'google_site_verification') @endphp
                @if($gs)
                <input type="hidden" name="settings[{{ $gs->key }}][key]" value="{{ $gs->key }}">
                <div class="form-group">
                    <label class="form-label">인증 코드 (content 값)</label>
                    <input type="text" name="settings[{{ $gs->key }}][value]" class="form-control"
                           value="{{ old('settings.'.$gs->key.'.value', $gs->value) }}"
                           placeholder="예: AbCdEfGhIjKlMnOpQrStUvWxYz123456">
                </div>
                @if($gs->value)
                <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:10px 14px;font-size:.8rem;color:#15803d">
                    ✅ 인증 코드가 설정되었습니다. 메타 태그가 자동으로 삽입됩니다.
                </div>
                @endif
                @endif
            </div>
        </div>

        {{-- Naver --}}
        <div class="card">
            <div class="card-header">
                <h3 style="display:flex;align-items:center;gap:8px">
                    <span style="background:#03C75A;color:#fff;font-size:.7rem;font-weight:800;padding:2px 7px;border-radius:4px">N</span>
                    네이버 서치어드바이저
                </h3>
            </div>
            <div class="card-body">
                <div style="background:#f8fafc;border-radius:8px;padding:14px;margin-bottom:16px;font-size:.8rem;color:#475569;line-height:1.7">
                    <strong>등록 방법:</strong><br>
                    1. <a href="https://searchadvisor.naver.com" target="_blank" style="color:#4f46e5">네이버 서치어드바이저</a> 접속<br>
                    2. 웹마스터 도구 → 사이트 추가<br>
                    3. HTML 태그 인증 선택<br>
                    4. <code style="background:#e2e8f0;padding:1px 5px;border-radius:3px">content="xxxxx"</code> 값만 복사하여 아래 입력
                </div>
                @php $ns = ($settings['verification'] ?? collect())->firstWhere('key', 'naver_site_verification') @endphp
                @if($ns)
                <input type="hidden" name="settings[{{ $ns->key }}][key]" value="{{ $ns->key }}">
                <div class="form-group">
                    <label class="form-label">인증 코드 (content 값)</label>
                    <input type="text" name="settings[{{ $ns->key }}][value]" class="form-control"
                           value="{{ old('settings.'.$ns->key.'.value', $ns->value) }}"
                           placeholder="예: abcdef1234567890abcdef1234567890">
                </div>
                @if($ns->value)
                <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:10px 14px;font-size:.8rem;color:#15803d">
                    ✅ 인증 코드가 설정되었습니다. 메타 태그가 자동으로 삽입됩니다.
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>

    {{-- 생성된 메타 태그 미리보기 --}}
    @php
        $gv = ($settings['verification'] ?? collect())->firstWhere('key', 'google_site_verification');
        $nv = ($settings['verification'] ?? collect())->firstWhere('key', 'naver_site_verification');
    @endphp
    @if(($gv && $gv->value) || ($nv && $nv->value))
    <div class="card" style="margin-top:16px">
        <div class="card-header"><h3>현재 삽입된 메타 태그 (미리보기)</h3></div>
        <div class="card-body">
            <pre style="background:#1e293b;color:#94a3b8;padding:16px;border-radius:8px;font-size:.8rem;overflow-x:auto;line-height:1.8">@if($gv && $gv->value)<span style="color:#7dd3fc">&lt;meta name="google-site-verification" content="<span style="color:#86efac">{{ $gv->value }}</span>"&gt;</span>
@endif
@if($nv && $nv->value)<span style="color:#7dd3fc">&lt;meta name="naver-site-verification" content="<span style="color:#86efac">{{ $nv->value }}</span>"&gt;</span>
@endif</pre>
        </div>
    </div>
    @endif
</div>

{{-- 저장 버튼 --}}
<div style="margin-top:20px;display:flex;justify-content:flex-end">
    <button type="submit" class="btn btn-primary" style="padding:10px 28px">설정 저장</button>
</div>
</form>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
    });
});
</script>
@endpush
