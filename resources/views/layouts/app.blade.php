@php
    use App\Models\Setting;
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
    $canonicalUrl   = url()->current();
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: {{ $primaryColor }};
            --primary-dark: color-mix(in srgb, {{ $primaryColor }} 80%, #000);
            --primary-light: color-mix(in srgb, {{ $primaryColor }} 8%, #fff);
            --primary-mid: color-mix(in srgb, {{ $primaryColor }} 15%, #fff);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
        body {
            font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #fff;
            color: #1a1a2e;
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; height: auto; display: block; }

        /* ── HEADER ── */
        header {
            position: sticky; top: 0; z-index: 200;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,.06);
        }
        .header-inner {
            max-width: 1200px; margin: 0 auto; padding: 0 28px;
            display: flex; align-items: center; justify-content: space-between;
            height: 64px;
        }
        .logo {
            font-size: 1.3rem; font-weight: 800; color: #0f0f23;
            letter-spacing: -0.5px; display: flex; align-items: center; gap: 8px;
        }
        .logo-dot { width: 8px; height: 8px; background: var(--primary); border-radius: 50%; display: inline-block; }
        .nav-desktop { display: flex; align-items: center; gap: 2px; }
        .nav-desktop a {
            padding: 7px 14px; font-size: .875rem; font-weight: 500;
            color: #555; border-radius: 8px; transition: all .15s;
        }
        .nav-desktop a:hover { background: #f4f4f8; color: #0f0f23; }
        .nav-toggle {
            display: none; background: none; border: none; cursor: pointer;
            padding: 8px; border-radius: 8px; color: #555;
        }
        .nav-toggle:hover { background: #f4f4f8; }
        .nav-mobile { display: none; position: fixed; inset: 0; z-index: 300; }
        .nav-mobile.open { display: block; }
        .nav-overlay { position: absolute; inset: 0; background: rgba(0,0,0,.5); }
        .nav-drawer {
            position: absolute; top: 0; right: 0; bottom: 0; width: min(300px, 88vw);
            background: #fff; display: flex; flex-direction: column; padding: 24px 20px;
            transform: translateX(100%); transition: transform .28s cubic-bezier(.4,0,.2,1);
        }
        .nav-mobile.open .nav-drawer { transform: translateX(0); }
        .nav-drawer-close {
            align-self: flex-end; background: none; border: none; cursor: pointer;
            color: #888; margin-bottom: 20px; padding: 4px;
        }
        .nav-drawer a {
            display: block; padding: 14px 4px; font-size: 1rem; font-weight: 600;
            color: #1a1a2e; border-bottom: 1px solid #f1f1f5;
        }
        .nav-drawer a:hover { color: var(--primary); }

        /* ── MAIN ── */
        main { max-width: 1200px; margin: 0 auto; padding: 48px 28px; min-height: 70vh; }

        /* ── HERO (INDEX) ── */
        .hero {
            text-align: center; padding: 72px 0 56px;
            border-bottom: 1px solid #f1f1f5; margin-bottom: 48px;
        }
        .hero-eyebrow {
            display: inline-block; font-size: .75rem; font-weight: 700;
            color: var(--primary); letter-spacing: 2px; text-transform: uppercase;
            background: var(--primary-light); padding: 5px 14px;
            border-radius: 20px; margin-bottom: 20px;
        }
        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 800;
            color: #0f0f23; line-height: 1.2; margin-bottom: 14px;
            letter-spacing: -1px; word-break: keep-all;
        }
        .hero p { color: #6b7280; font-size: clamp(.95rem, 2vw, 1.15rem); }

        /* ── CATEGORY TABS ── */
        .category-tabs {
            display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 36px;
        }
        .tab {
            padding: 7px 18px; border-radius: 24px; font-size: .82rem;
            font-weight: 600; background: #f5f5f8; border: 1.5px solid transparent;
            color: #555; transition: all .18s; white-space: nowrap;
            min-height: 38px; display: inline-flex; align-items: center; cursor: pointer;
        }
        .tab:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
        .tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── CARD GRID ── */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
        .card {
            background: #fff; border-radius: 16px; overflow: hidden;
            border: 1.5px solid #f0f0f5;
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
            display: block;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0,0,0,.08);
            border-color: var(--primary);
        }
        .card-body { padding: 26px; }
        .card-category {
            font-size: .7rem; font-weight: 700; color: var(--primary);
            text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px;
        }
        .card-title {
            font-size: 1.1rem; font-weight: 700; margin-bottom: 10px;
            line-height: 1.5; color: #0f0f23; word-break: keep-all;
        }
        .card:hover .card-title { color: var(--primary); }
        .card-excerpt {
            font-size: .875rem; color: #6b7280; margin-bottom: 18px; line-height: 1.7;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .card-meta { font-size: .75rem; color: #9ca3af; display: flex; gap: 10px; align-items: center; }
        .card-meta-dot { width: 3px; height: 3px; border-radius: 50%; background: #d1d5db; }

        /* ── POST PAGE ── */
        .post-layout {
            display: grid;
            grid-template-columns: 1fr 240px;
            gap: 60px;
            align-items: start;
            max-width: 1080px;
            margin: 0 auto;
        }

        /* 포스트 헤더 */
        .post-hero {
            padding-bottom: 32px;
            border-bottom: 1px solid #f0f0f5;
            margin-bottom: 40px;
        }
        .post-category-badge {
            display: inline-flex; align-items: center;
            font-size: .72rem; font-weight: 700; letter-spacing: 1.5px;
            text-transform: uppercase; color: var(--primary);
            background: var(--primary-light); padding: 5px 14px;
            border-radius: 20px; margin-bottom: 20px;
        }
        .post-title {
            font-size: clamp(1.75rem, 4vw, 2.6rem);
            font-weight: 800; line-height: 1.3;
            color: #0f0f23; margin-bottom: 20px;
            word-break: keep-all; letter-spacing: -0.5px;
        }
        .post-meta {
            display: flex; align-items: center; gap: 8px;
            font-size: .82rem; color: #9ca3af; flex-wrap: wrap;
        }
        .post-meta-sep { color: #d1d5db; }
        .post-meta-badge {
            background: #f5f5f8; padding: 3px 10px; border-radius: 8px;
            font-weight: 600; color: #6b7280; font-size: .75rem;
        }

        /* 브레드크럼 */
        .breadcrumb {
            display: flex; align-items: center; gap: 6px;
            list-style: none; font-size: .78rem; color: #9ca3af;
            flex-wrap: wrap; margin-bottom: 28px;
        }
        .breadcrumb a { color: #9ca3af; transition: color .15s; }
        .breadcrumb a:hover { color: var(--primary); }
        .breadcrumb-sep { font-size: .65rem; color: #d1d5db; }

        /* 본문 */
        .post-content {
            font-size: 1.05rem; line-height: 1.95;
            color: #374151; word-break: keep-all;
        }
        .post-content > *:first-child { margin-top: 0; }
        .post-content h1,
        .post-content h2 {
            font-size: clamp(1.25rem, 2.5vw, 1.55rem);
            font-weight: 800; color: #0f0f23;
            margin: 2.8rem 0 1rem; padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f5;
            letter-spacing: -.3px;
        }
        .post-content h3 {
            font-size: clamp(1.1rem, 2vw, 1.25rem);
            font-weight: 700; color: #1a1a2e; margin: 2.2rem 0 .8rem;
        }
        .post-content h4 { font-size: 1rem; font-weight: 700; color: #374151; margin: 1.6rem 0 .6rem; }
        .post-content p { margin-bottom: 1.5rem; }
        .post-content strong { font-weight: 700; color: #0f0f23; }
        .post-content em { font-style: italic; }
        .post-content ul { list-style: none; margin: 0 0 1.5rem; padding: 0; }
        .post-content ul li {
            padding: 4px 0 4px 22px; position: relative; color: #374151;
        }
        .post-content ul li::before {
            content: '';
            position: absolute; left: 0; top: 50%;
            transform: translateY(-50%);
            width: 7px; height: 7px;
            background: var(--primary); border-radius: 50%;
            opacity: .8;
        }
        .post-content ol { margin: 0 0 1.5rem 1.6rem; padding: 0; }
        .post-content ol li { padding: 3px 0; color: #374151; }
        .post-content a { color: var(--primary); text-decoration: underline; text-underline-offset: 3px; }
        .post-content a:hover { opacity: .75; }
        .post-content hr { border: none; border-top: 2px solid #f0f0f5; margin: 3rem 0; }
        .post-content code {
            background: #f3f4f6; color: #e11d48;
            padding: 2px 8px; border-radius: 6px;
            font-size: .875em;
            font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace;
            word-break: break-all;
        }
        .post-content pre {
            background: #0f172a; border-radius: 12px;
            padding: 22px 24px; overflow-x: auto; margin-bottom: 1.8rem;
            box-shadow: 0 4px 20px rgba(0,0,0,.12);
        }
        .post-content pre code {
            background: none; color: #e2e8f0; padding: 0;
            font-size: .875rem; line-height: 1.75; word-break: normal;
        }
        .post-content blockquote {
            border-left: 4px solid var(--primary);
            padding: 16px 22px;
            background: var(--primary-light);
            border-radius: 0 12px 12px 0;
            margin: 0 0 1.6rem;
            color: #374151;
        }
        .post-content blockquote p:last-child { margin-bottom: 0; }
        .post-content img { border-radius: 12px; margin: 1.6rem auto; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
        .post-content table { width: 100%; border-collapse: collapse; margin-bottom: 1.8rem; font-size: .9rem; overflow: hidden; border-radius: 10px; }
        .post-content th {
            background: #f9fafb; font-weight: 700; color: #374151;
            padding: 12px 16px; border: 1px solid #e5e7eb; text-align: left;
        }
        .post-content td { padding: 10px 16px; border: 1px solid #e5e7eb; color: #4b5563; }
        .post-content tr:nth-child(even) td { background: #f9fafb; }

        /* ── TOC 사이드바 ── */
        .post-sidebar { position: sticky; top: 88px; }
        .toc-box {
            background: #f9f9fc; border-radius: 14px;
            padding: 20px; border: 1.5px solid #f0f0f5;
        }
        .toc-title {
            font-size: .7rem; font-weight: 800; letter-spacing: 2px;
            text-transform: uppercase; color: #9ca3af; margin-bottom: 14px;
        }
        .toc-list { list-style: none; }
        .toc-list li { margin-bottom: 4px; }
        .toc-list a {
            font-size: .8rem; color: #6b7280; line-height: 1.5;
            transition: color .15s; display: block; padding: 3px 0 3px 0;
            border-left: 2px solid transparent; padding-left: 10px;
        }
        .toc-list a:hover { color: var(--primary); border-left-color: var(--primary); }
        .toc-list a.active { color: var(--primary); border-left-color: var(--primary); font-weight: 600; }
        .toc-list li.toc-h3 a { padding-left: 22px; font-size: .77rem; }

        /* 관련 글 */
        .related {
            margin-top: 56px; padding-top: 40px;
            border-top: 1px solid #f0f0f5;
        }
        .related h2 {
            font-size: 1.1rem; font-weight: 800; color: #0f0f23;
            margin-bottom: 20px; letter-spacing: -.2px;
        }
        .related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }

        /* 하단 */
        .post-footer {
            margin-top: 40px; padding-top: 28px;
            border-top: 1px solid #f0f0f5;
            display: flex; align-items: center; justify-content: space-between;
        }

        /* ── 버튼 ── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 20px; border-radius: 10px;
            font-size: .875rem; font-weight: 600; cursor: pointer;
            border: none; transition: all .18s; min-height: 44px; text-decoration: none;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-secondary {
            background: #f5f5f8; color: #374151;
            border: 1.5px solid #e8e8ee;
        }
        .btn-secondary:hover { background: #ebebf2; border-color: #d0d0da; }

        /* ── 페이지네이션 ── */
        .pagination { margin-top: 48px; display: flex; justify-content: center; gap: 6px; flex-wrap: wrap; }
        .pagination a, .pagination span {
            padding: 9px 14px; border-radius: 10px; background: #fff;
            border: 1.5px solid #e8e8ee; font-size: .83rem; color: #555;
            min-width: 40px; text-align: center; transition: all .15s;
        }
        .pagination a:hover { border-color: var(--primary); color: var(--primary); }
        .pagination .active span { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── 읽기 진행 바 ── */
        #reading-progress {
            position: fixed; top: 0; left: 0; height: 3px;
            background: var(--primary); z-index: 500;
            width: 0%; transition: width .1s linear;
        }

        /* ── FOOTER ── */
        footer {
            background: #0f0f23; color: rgba(255,255,255,.5);
            text-align: center; padding: 36px 24px;
            font-size: .83rem; margin-top: 80px;
        }
        footer a { color: rgba(255,255,255,.5); }
        footer strong { color: rgba(255,255,255,.8); font-weight: 600; }

        /* ── 반응형 ── */
        @media (max-width: 960px) {
            .post-layout { grid-template-columns: 1fr; gap: 0; }
            .post-sidebar { display: none; }
        }
        @media (max-width: 768px) {
            main { padding: 32px 20px; }
            .header-inner { padding: 0 20px; }
            .hero { padding: 48px 0 36px; }
            .nav-desktop { display: none; }
            .nav-toggle { display: flex; align-items: center; }
            .grid { grid-template-columns: 1fr; gap: 16px; }
            .related-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .logo { font-size: 1.1rem; }
            .post-title { font-size: 1.55rem; }
        }
        @media (min-width: 1100px) {
            .grid { grid-template-columns: repeat(3, 1fr); }
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
        <button class="nav-toggle" id="nav-toggle" aria-label="메뉴 열기" aria-expanded="false">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
    </div>
</header>

{{-- 모바일 메뉴 --}}
<div class="nav-mobile" id="nav-mobile">
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
    document.addEventListener('keydown', e => { if(e.key==='Escape') closeNav(); });

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
