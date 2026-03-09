@php
    use App\Models\Setting;
    $blogName   = Setting::get('blog_name', config('app.name', 'Blog'));
    $blogTagline= Setting::get('blog_tagline', '');
    $primaryColor = Setting::get('primary_color', '#4f46e5');
    $footerText = Setting::get('footer_text', 'All rights reserved.');
    $blogDesc   = Setting::get('blog_description', '개인 블로그');
    $gaId       = Setting::get('google_analytics', '');
@endphp
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $blogName)</title>
    <meta name="description" content="@yield('description', $blogDesc)">
    @if($gaId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','{{ $gaId }}');</script>
    @endif
    <style>
        :root { --primary: {{ $primaryColor }}; --primary-dark: color-mix(in srgb, {{ $primaryColor }} 85%, black); }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Noto Sans KR', -apple-system, sans-serif; background: #f8f9fa; color: #333; line-height: 1.7; }
        a { color: inherit; text-decoration: none; }

        /* 헤더 */
        header { background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 100; }
        .header-inner { max-width: 1100px; margin: 0 auto; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 64px; }
        .logo { font-size: 1.4rem; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .logo span { color: var(--primary); }
        nav a { margin-left: 24px; font-size: .9rem; color: #555; transition: color .2s; }
        nav a:hover { color: var(--primary); }

        /* 메인 */
        main { max-width: 1100px; margin: 0 auto; padding: 40px 24px; min-height: 70vh; }

        /* 카드 */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
        .card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.07); transition: transform .2s, box-shadow .2s; }
        .card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
        .card-body { padding: 24px; }
        .card-category { font-size: .75rem; font-weight: 600; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .card-title { font-size: 1.15rem; font-weight: 700; margin-bottom: 10px; line-height: 1.4; color: #1a1a1a; }
        .card-title:hover { color: var(--primary); }
        .card-excerpt { font-size: .88rem; color: #666; margin-bottom: 16px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .card-meta { font-size: .78rem; color: #999; display: flex; gap: 12px; }

        /* 글 상세 */
        .post-header { max-width: 720px; margin: 0 auto 40px; }
        .post-category { font-size: .8rem; font-weight: 600; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .post-title { font-size: 2.2rem; font-weight: 800; line-height: 1.3; color: #1a1a1a; margin-bottom: 16px; }
        .post-meta { font-size: .85rem; color: #999; display: flex; gap: 16px; align-items: center; }
        .post-content { max-width: 720px; margin: 0 auto; font-size: 1.05rem; line-height: 1.9; }
        .post-content h2 { font-size: 1.5rem; font-weight: 700; margin: 2rem 0 1rem; color: #1a1a1a; }
        .post-content h3 { font-size: 1.2rem; font-weight: 600; margin: 1.5rem 0 .8rem; }
        .post-content p { margin-bottom: 1.2rem; }
        .post-content ul, .post-content ol { margin: 0 0 1.2rem 1.5rem; }
        .post-content li { margin-bottom: .4rem; }
        .post-content code { background: #f4f4f4; padding: 2px 6px; border-radius: 4px; font-size: .9em; }
        .post-content pre { background: #1e1e1e; color: #d4d4d4; padding: 20px; border-radius: 8px; overflow-x: auto; margin-bottom: 1.2rem; }
        .post-content blockquote { border-left: 4px solid var(--primary); padding: 12px 20px; background: #f0f0ff; border-radius: 0 8px 8px 0; margin-bottom: 1.2rem; color: #444; }

        /* 히어로 */
        .hero { text-align: center; padding: 60px 0 50px; }
        .hero h1 { font-size: 2.5rem; font-weight: 800; color: #1a1a1a; margin-bottom: 12px; }
        .hero p { color: #666; font-size: 1.1rem; }

        /* 카테고리 탭 */
        .category-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 32px; }
        .tab { padding: 6px 16px; border-radius: 20px; font-size: .85rem; font-weight: 500; background: #fff; border: 1px solid #ddd; color: #555; transition: all .2s; }
        .tab:hover, .tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* 페이지네이션 */
        .pagination { margin-top: 40px; display: flex; justify-content: center; gap: 8px; }
        .pagination a, .pagination span { padding: 8px 14px; border-radius: 8px; background: #fff; border: 1px solid #ddd; font-size: .85rem; color: #555; }
        .pagination .active span { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* 관련글 */
        .related { max-width: 720px; margin: 60px auto 0; border-top: 1px solid #eee; padding-top: 40px; }
        .related h3 { font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; }
        .related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        @media (max-width: 640px) { .related-grid { grid-template-columns: 1fr; } .post-title { font-size: 1.6rem; } .grid { grid-template-columns: 1fr; } }

        /* 푸터 */
        footer { background: #1a1a1a; color: #aaa; text-align: center; padding: 32px 24px; font-size: .85rem; margin-top: 80px; }
        footer a { color: #aaa; }
    </style>
    @stack('styles')
</head>
<body>
<header>
    <div class="header-inner">
        <a href="{{ route('home') }}" class="logo">{{ $blogName }}</a>
        <nav>
            <a href="{{ route('home') }}">홈</a>
        </nav>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer>
    <p>&copy; {{ date('Y') }} {{ $blogName }}. {{ $footerText }}</p>
</footer>
@stack('scripts')
</body>
</html>
