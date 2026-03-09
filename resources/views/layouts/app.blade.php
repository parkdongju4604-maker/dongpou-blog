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

    {{-- ── 기본 메타 ── --}}
    <title>@yield('title', $blogName)</title>
    <meta name="description" content="@yield('description', $blogDesc)">
    <meta name="author" content="@yield('author', $authorName)">
    <meta name="robots" content="{{ $robotsIndex }}">
    <link rel="canonical" href="@yield('canonical', $canonicalUrl)">

    {{-- ── 검색엔진 인증 ── --}}
    @if($googleVerify)
    <meta name="google-site-verification" content="{{ $googleVerify }}">
    @endif
    @if($naverVerify)
    <meta name="naver-site-verification" content="{{ $naverVerify }}">
    @endif

    {{-- ── Open Graph ── --}}
    @php
        $ogTitle = $__env->yieldContent('og:title') ?: ($__env->yieldContent('title') ?: $blogName);
        $ogDesc  = $__env->yieldContent('og:description') ?: ($__env->yieldContent('description') ?: $blogDesc);
        $ogImg   = $__env->yieldContent('og:image') ?: $ogImageDefault;
    @endphp
    <meta property="og:site_name" content="{{ $blogName }}">
    <meta property="og:locale" content="ko_KR">
    <meta property="og:type" content="@yield('og:type', 'website')">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDesc }}">
    <meta property="og:url" content="@yield('canonical', $canonicalUrl)">
    @if($ogImg)
    <meta property="og:image" content="{{ $ogImg }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    @endif

    {{-- ── Twitter Card ── --}}
    <meta name="twitter:card" content="{{ $ogImg ? 'summary_large_image' : 'summary' }}">
    @if($twitterHandle)
    <meta name="twitter:site" content="@{{ $twitterHandle }}">
    @endif
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDesc }}">
    @if($ogImg)
    <meta name="twitter:image" content="{{ $ogImg }}">
    @endif

    {{-- ── Google Analytics ── --}}
    @if($gaId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','{{ $gaId }}');</script>
    @endif

    {{-- ── JSON-LD 구조화 데이터 (WebSite 기본) ── --}}
    @php
        $websiteSchema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'            => $blogName,
            'description'     => $blogDesc,
            'url'             => url('/'),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => url('/') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>

    {{-- 페이지별 추가 JSON-LD --}}
    @stack('jsonld')

    {{-- ── 스타일 ── --}}
    <style>
        :root {
            --primary: {{ $primaryColor }};
            --primary-rgb: {{ implode(',', sscanf(ltrim($primaryColor,'#'), '%02x%02x%02x') ?: [79,70,229]) }};
            --primary-dark: color-mix(in srgb, {{ $primaryColor }} 82%, #000);
            --primary-light: color-mix(in srgb, {{ $primaryColor }} 10%, #fff);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
        body { font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8f9fa; color: #333; line-height: 1.7; min-height: 100vh; }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; height: auto; display: block; }

        /* ── 헤더 ── */
        header { background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 200; }
        .header-inner { max-width: 1100px; margin: 0 auto; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; height: 60px; }
        .logo { font-size: 1.35rem; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; flex-shrink: 0; }
        .logo span { color: var(--primary); }

        /* 데스크톱 nav */
        .nav-desktop { display: flex; align-items: center; gap: 4px; }
        .nav-desktop a { padding: 6px 12px; font-size: .875rem; color: #555; border-radius: 6px; transition: background .15s, color .15s; }
        .nav-desktop a:hover { background: var(--primary-light); color: var(--primary); }

        /* 햄버거 버튼 */
        .nav-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; border-radius: 6px; color: #555; }
        .nav-toggle:hover { background: #f1f5f9; }

        /* 모바일 드로어 */
        .nav-mobile {
            display: none; position: fixed; inset: 0; z-index: 300;
        }
        .nav-mobile.open { display: block; }
        .nav-overlay { position: absolute; inset: 0; background: rgba(0,0,0,.4); }
        .nav-drawer {
            position: absolute; top: 0; right: 0; bottom: 0; width: min(280px, 85vw);
            background: #fff; display: flex; flex-direction: column;
            padding: 20px; box-shadow: -4px 0 24px rgba(0,0,0,.12);
            transform: translateX(100%); transition: transform .25s ease;
        }
        .nav-mobile.open .nav-drawer { transform: translateX(0); }
        .nav-drawer-close { align-self: flex-end; background: none; border: none; cursor: pointer; padding: 4px; color: #64748b; margin-bottom: 16px; }
        .nav-drawer a { display: block; padding: 13px 4px; font-size: 1rem; font-weight: 500; color: #1e293b; border-bottom: 1px solid #f1f5f9; }
        .nav-drawer a:hover { color: var(--primary); }

        /* ── 메인 ── */
        main { max-width: 1100px; margin: 0 auto; padding: 36px 20px; min-height: 70vh; }

        /* ── 카드 그리드 ── */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.07); transition: transform .2s, box-shadow .2s; display: block; }
        .card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
        .card-body { padding: 22px; }
        .card-category { font-size: .72rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .card-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 10px; line-height: 1.45; color: #1a1a1a; }
        .card:hover .card-title { color: var(--primary); }
        .card-excerpt { font-size: .875rem; color: #666; margin-bottom: 14px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .card-meta { font-size: .75rem; color: #999; display: flex; gap: 10px; flex-wrap: wrap; }

        /* ── 글 상세 ── */
        .post-wrap { max-width: 720px; margin: 0 auto; }
        .post-header { margin-bottom: 0; padding-bottom: 28px; border-bottom: 1px solid #e5e7eb; }
        .post-category { font-size: .75rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 14px; display: inline-block; }
        .post-title { font-size: clamp(1.65rem, 4vw, 2.3rem); font-weight: 800; line-height: 1.35; color: #111827; margin-bottom: 16px; word-break: keep-all; letter-spacing: -.3px; }
        .post-meta { font-size: .82rem; color: #9ca3af; display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
        .post-meta .dot { color: #d1d5db; }

        /* 본문 */
        .post-content { font-size: 1.05rem; line-height: 1.95; color: #374151; word-break: keep-all; padding-top: 32px; }
        .post-content > *:first-child { margin-top: 0; }
        .post-content h1,
        .post-content h2 { font-size: clamp(1.2rem, 2.8vw, 1.5rem); font-weight: 700; color: #111827; margin: 2.4rem 0 1rem; padding-bottom: 8px; border-bottom: 2px solid #f3f4f6; letter-spacing: -.2px; }
        .post-content h3 { font-size: clamp(1.05rem, 2.5vw, 1.2rem); font-weight: 700; color: #1f2937; margin: 2rem 0 .75rem; }
        .post-content h4 { font-size: 1rem; font-weight: 700; color: #374151; margin: 1.5rem 0 .5rem; }
        .post-content p { margin-bottom: 1.4rem; }
        .post-content strong { font-weight: 700; color: #111827; }
        .post-content em { font-style: italic; color: #4b5563; }
        .post-content ul { list-style: none; margin: 0 0 1.4rem; padding: 0; }
        .post-content ul li { padding-left: 1.4rem; position: relative; margin-bottom: .5rem; }
        .post-content ul li::before { content: ''; position: absolute; left: 0; top: .7em; width: 6px; height: 6px; background: var(--primary); border-radius: 50%; }
        .post-content ol { margin: 0 0 1.4rem 1.5rem; padding: 0; }
        .post-content ol li { margin-bottom: .5rem; padding-left: .25rem; }
        .post-content li > ul, .post-content li > ol { margin-top: .4rem; margin-bottom: .4rem; }
        .post-content a { color: var(--primary); text-decoration: underline; text-underline-offset: 3px; }
        .post-content a:hover { opacity: .75; }
        .post-content hr { border: none; border-top: 2px solid #f3f4f6; margin: 2.5rem 0; }
        /* 인라인 코드 */
        .post-content code { background: #f3f4f6; color: #ef4444; padding: 2px 7px; border-radius: 5px; font-size: .875em; font-family: 'JetBrains Mono', 'Fira Code', monospace; word-break: break-all; }
        /* 코드 블록 */
        .post-content pre { background: #1e293b; border-radius: 10px; padding: 20px 22px; overflow-x: auto; margin-bottom: 1.6rem; }
        .post-content pre code { background: none; color: #e2e8f0; padding: 0; font-size: .875rem; line-height: 1.7; word-break: normal; }
        /* 인용구 */
        .post-content blockquote { border-left: 4px solid var(--primary); padding: 14px 20px; background: var(--primary-light); border-radius: 0 10px 10px 0; margin: 0 0 1.4rem; color: #4b5563; }
        .post-content blockquote p:last-child { margin-bottom: 0; }
        /* 이미지 */
        .post-content img { border-radius: 10px; margin: 1.5rem auto; box-shadow: 0 4px 16px rgba(0,0,0,.1); }
        /* 표 */
        .post-content table { width: 100%; border-collapse: collapse; margin-bottom: 1.6rem; font-size: .9rem; }
        .post-content th { background: #f9fafb; font-weight: 700; color: #374151; padding: 10px 14px; border: 1px solid #e5e7eb; text-align: left; }
        .post-content td { padding: 9px 14px; border: 1px solid #e5e7eb; color: #4b5563; }
        .post-content tr:nth-child(even) td { background: #f9fafb; }

        /* ── 히어로 ── */
        .hero { text-align: center; padding: 52px 0 44px; }
        .hero h1 { font-size: clamp(1.8rem, 5vw, 2.5rem); font-weight: 800; color: #1a1a1a; margin-bottom: 10px; word-break: keep-all; }
        .hero p { color: #666; font-size: clamp(.9rem, 2vw, 1.1rem); }

        /* ── 카테고리 탭 ── */
        .category-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 28px; }
        .tab { padding: 6px 15px; border-radius: 20px; font-size: .82rem; font-weight: 600; background: #fff; border: 1px solid #ddd; color: #555; transition: all .15s; white-space: nowrap; min-height: 36px; display: inline-flex; align-items: center; }
        .tab:hover, .tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── 페이지네이션 ── */
        .pagination { margin-top: 40px; display: flex; justify-content: center; gap: 6px; flex-wrap: wrap; }
        .pagination a, .pagination span { padding: 8px 13px; border-radius: 8px; background: #fff; border: 1px solid #ddd; font-size: .83rem; color: #555; min-width: 38px; text-align: center; }
        .pagination .active span { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── 관련글 ── */
        .related { max-width: 720px; margin: 56px auto 0; border-top: 1px solid #eee; padding-top: 36px; }
        .related h3 { font-size: 1.15rem; font-weight: 700; margin-bottom: 18px; }
        .related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }

        /* ── 버튼 ── */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: .875rem; font-weight: 600; cursor: pointer; border: none; transition: all .15s; min-height: 44px; text-decoration: none; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: #e2e8f0; }

        /* ── 푸터 ── */
        footer { background: #1a1a1a; color: #aaa; text-align: center; padding: 28px 20px; font-size: .83rem; margin-top: 72px; }
        footer a { color: #aaa; }

        /* ── 모바일 반응형 ── */
        @media (max-width: 768px) {
            .nav-desktop { display: none; }
            .nav-toggle { display: flex; align-items: center; justify-content: center; }
            main { padding: 24px 16px; }
            .hero { padding: 36px 0 28px; }
            .grid { grid-template-columns: 1fr; gap: 14px; }
            .related-grid { grid-template-columns: 1fr; }
            .post-content { font-size: 1rem; }
            .card-body { padding: 18px; }
            .category-tabs { gap: 6px; }
        }
        @media (max-width: 480px) {
            .header-inner { padding: 0 16px; }
            .logo { font-size: 1.2rem; }
            .post-title { font-size: 1.5rem; }
        }
        /* 큰 화면 */
        @media (min-width: 1200px) {
            .grid { grid-template-columns: repeat(3, 1fr); }
        }
    </style>
    @stack('styles')
    @stack('head')
</head>
<body>

{{-- ── 헤더 ── --}}
<header>
    <div class="header-inner">
        <a href="{{ route('home') }}" class="logo" aria-label="{{ $blogName }} 홈">
            {{ $blogName }}
        </a>

        {{-- 데스크톱 nav --}}
        <nav class="nav-desktop" aria-label="주 메뉴">
            <a href="{{ route('home') }}">홈</a>
        </nav>

        {{-- 햄버거 --}}
        <button class="nav-toggle" id="nav-toggle" aria-label="메뉴 열기" aria-expanded="false" aria-controls="nav-mobile">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
    </div>
</header>

{{-- 모바일 드로어 --}}
<div class="nav-mobile" id="nav-mobile" role="dialog" aria-modal="true" aria-label="모바일 메뉴">
    <div class="nav-overlay" id="nav-overlay"></div>
    <nav class="nav-drawer">
        <button class="nav-drawer-close" id="nav-close" aria-label="메뉴 닫기">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <a href="{{ route('home') }}">홈</a>
    </nav>
</div>

{{-- ── 메인 ── --}}
<main id="main-content">
    @yield('content')
</main>

{{-- ── 푸터 ── --}}
<footer>
    <p>&copy; {{ date('Y') }} {{ $blogName }}. {{ $footerText }}</p>
</footer>

<script>
(function(){
    const toggle = document.getElementById('nav-toggle');
    const nav    = document.getElementById('nav-mobile');
    const overlay= document.getElementById('nav-overlay');
    const close  = document.getElementById('nav-close');
    const drawer = nav.querySelector('.nav-drawer');

    function openNav() {
        nav.classList.add('open');
        toggle.setAttribute('aria-expanded','true');
        document.body.style.overflow = 'hidden';
        setTimeout(() => close.focus(), 50);
    }
    function closeNav() {
        nav.classList.remove('open');
        toggle.setAttribute('aria-expanded','false');
        document.body.style.overflow = '';
        toggle.focus();
    }
    toggle.addEventListener('click', openNav);
    overlay.addEventListener('click', closeNav);
    close.addEventListener('click', closeNav);
    document.addEventListener('keydown', e => { if(e.key === 'Escape') closeNav(); });
})();
</script>

@stack('scripts')
</body>
</html>
