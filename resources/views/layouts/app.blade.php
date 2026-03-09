@php
    use App\Models\Setting;
    use App\Models\CssTheme;
    $blogName     = Setting::get('blog_name',     config('app.name', 'Blog'));
    $blogTagline  = Setting::get('blog_tagline',  '');
    $blogDesc     = Setting::get('blog_description', $blogTagline);
    $primaryColor = Setting::get('primary_color', '#4f46e5');
    $footerText   = Setting::get('footer_text',   'All rights reserved.');
    $gaId         = Setting::get('google_analytics',      '');
    $googleVerify = Setting::get('google_site_verification', '');
    $naverVerify  = Setting::get('naver_site_verification',  '');
    $ogImageDefault = Setting::get('og_image_default', '');
    $twitterHandle  = Setting::get('twitter_handle', '');
    $robotsIndex    = Setting::get('robots_index', 'index,follow');
    $authorName     = Setting::get('author_name', $blogName);
    $metaKeywords   = Setting::get('meta_keywords', '');
    $headCode       = Setting::get('head_code', '');
    $canonicalUrl   = url()->current();
    $activeTheme    = CssTheme::getActive();
    $activeThemeCss = $activeTheme ? $activeTheme->css : '';
@endphp
<!DOCTYPE html>
<html lang="ko" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', $blogName)</title>
    <meta name="description" content="@yield('description', $blogDesc)">
    <meta name="author" content="@yield('author', $authorName)">
    <meta name="robots" content="{{ $robotsIndex }}">
    @if($metaKeywords)
    <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    <link rel="canonical" href="@yield('canonical', $canonicalUrl)">
    @if($googleVerify)
    <meta name="google-site-verification" content="{{ $googleVerify }}">
    @endif
    @if($naverVerify)
    <meta name="naver-site-verification" content="{{ $naverVerify }}">
    @endif
    @php
        $ogTitle = $__env->yieldContent('og:title') ?: ($__env->yieldContent('title') ?: $blogName);
        $ogDesc  = $__env->yieldContent('og:description') ?: ($__env->yieldContent('description') ?: $blogDesc);
        $ogImg   = $__env->yieldContent('og:image') ?: $ogImageDefault;
    @endphp
    <meta property="og:site_name" content="{{ $blogName }}">
    <meta property="og:locale"    content="ko_KR">
    <meta property="og:type"      content="@yield('og:type', 'website')">
    <meta property="og:title"     content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDesc }}">
    <meta property="og:url"       content="@yield('canonical', $canonicalUrl)">
    @if($ogImg)
    <meta property="og:image"       content="{{ $ogImg }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    @endif
    <meta name="twitter:card"        content="{{ $ogImg ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title"       content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDesc }}">
    @if($ogImg)
    <meta name="twitter:image" content="{{ $ogImg }}">
    @endif
    @if($twitterHandle)
    <meta name="twitter:site" content="@{{ $twitterHandle }}">
    @endif
    @if($gaId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','{{ $gaId }}');</script>
    @endif
    @php
        $websiteSchema = ['@context'=>'https://schema.org','@type'=>'WebSite','name'=>$blogName,'description'=>$blogDesc,'url'=>url('/')];
    @endphp
    <script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
    @stack('jsonld')
    @stack('head')
    @if($headCode)
    {!! $headCode !!}
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
{!! $activeThemeCss !!}
        :root {
            --primary: {{ $primaryColor }};
            --primary-dark: color-mix(in srgb, {{ $primaryColor }} 80%, #000);
            --primary-light: color-mix(in srgb, {{ $primaryColor }} 8%, #fff);
            --primary-mid: color-mix(in srgb, {{ $primaryColor }} 15%, #fff);
        }
    </style>
    @stack('styles')
</head>
<body>

<div id="reading-progress"></div>

{{-- ── 헤더 ── --}}
<header>
    <div class="header-inner">
        <a href="{{ route('home') }}" class="logo" aria-label="{{ $blogName }} 홈">
            <span class="logo-dot"></span>{{ $blogName }}
        </a>
        <nav class="nav-desktop" aria-label="주 메뉴">
            <a href="{{ route('home') }}">홈</a>
        </nav>
        <div style="display:flex;align-items:center;gap:4px;">
            <button id="search-toggle" onclick="openSearch()" aria-label="검색"
                style="background:none;border:none;cursor:pointer;padding:8px;border-radius:8px;color:#555;display:flex;align-items:center;transition:background .15s"
                onmouseover="this.style.background='#f4f4f8'" onmouseout="this.style.background='none'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </button>
        <button class="nav-toggle" id="nav-toggle" aria-label="메뉴 열기" aria-expanded="false">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
        </div>{{-- end header-right --}}
    </div>
</header>

{{-- 검색 오버레이 --}}
<div id="search-overlay" role="dialog" aria-modal="true" aria-label="검색"
     style="display:none;position:fixed;inset:0;z-index:400;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);"
     onclick="if(event.target===this)closeSearch()">
    <div style="max-width:620px;margin:80px auto 0;padding:0 20px;">
        <form action="{{ route('search') }}" method="GET" role="search"
              style="display:flex;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,.3);">
            <label for="overlay-search-input" style="display:none">검색어</label>
            <input type="search" id="overlay-search-input" name="q"
                   placeholder="검색어를 입력하세요..."
                   autocomplete="off"
                   style="flex:1;padding:18px 22px;border:none;font-size:1.1rem;outline:none;font-family:inherit;background:transparent;color:#0f0f23;">
            <button type="submit"
                    style="padding:0 22px;background:none;border:none;cursor:pointer;color:#4f46e5;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </button>
        </form>
        <p style="text-align:center;margin-top:14px;font-size:.8rem;color:rgba(255,255,255,.5)">
            ESC 로 닫기
        </p>
    </div>
</div>

{{-- 모바일 메뉴 --}}
<div class="nav-mobile" id="nav-mobile"
     role="dialog" aria-modal="true" aria-label="모바일 메뉴">
    <div class="nav-overlay" id="nav-overlay"></div>
    <nav class="nav-drawer">
        <button class="nav-drawer-close" id="nav-close" aria-label="닫기">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <a href="{{ route('home') }}">홈</a>
    </nav>
</div>

<main id="main-content">
    @yield('content')
</main>

<footer>
    <p><strong>{{ $blogName }}</strong> &nbsp;·&nbsp; {{ $footerText }} &nbsp;·&nbsp; &copy; {{ date('Y') }}</p>
</footer>

<script>
(function(){
    // 모바일 메뉴
    const toggle = document.getElementById('nav-toggle');
    const nav    = document.getElementById('nav-mobile');
    const overlay= document.getElementById('nav-overlay');
    const close  = document.getElementById('nav-close');
    function openNav()  { nav.classList.add('open'); toggle.setAttribute('aria-expanded','true');  document.body.style.overflow='hidden'; }
    function closeNav() { nav.classList.remove('open'); toggle.setAttribute('aria-expanded','false'); document.body.style.overflow=''; }
    toggle.addEventListener('click', openNav);
    overlay.addEventListener('click', closeNav);
    close.addEventListener('click', closeNav);
    document.addEventListener('keydown', e => { if(e.key==='Escape') { closeNav(); closeSearch(); } });

    // 검색 오버레이
    const searchOverlay = document.getElementById('search-overlay');
    const searchInput   = document.getElementById('overlay-search-input');
    window.openSearch  = function() { searchOverlay.style.display='block'; document.body.style.overflow='hidden'; setTimeout(()=>searchInput.focus(),50); };
    window.closeSearch = function() { searchOverlay.style.display='none'; document.body.style.overflow=''; };
    // Ctrl+K / Cmd+K 단축키
    document.addEventListener('keydown', e => { if((e.ctrlKey||e.metaKey)&&e.key==='k'){e.preventDefault();openSearch();} });

    // 읽기 진행 바
    const bar = document.getElementById('reading-progress');
    if(bar) {
        window.addEventListener('scroll', function(){
            const doc = document.documentElement;
            const scrolled = doc.scrollTop || document.body.scrollTop;
            const total = doc.scrollHeight - doc.clientHeight;
            bar.style.width = (total > 0 ? (scrolled/total*100) : 0) + '%';
        }, { passive: true });
    }
})();
</script>
@stack('scripts')
</body>
</html>
