<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Blog'))</title>
    <meta name="description" content="@yield('description', '개인 블로그')">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Noto Sans KR', -apple-system, sans-serif; background: #f8f9fa; color: #333; line-height: 1.7; }
        a { color: inherit; text-decoration: none; }

        /* 헤더 */
        header { background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 100; }
        .header-inner { max-width: 1100px; margin: 0 auto; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 64px; }
        .logo { font-size: 1.4rem; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .logo span { color: #4f46e5; }
        nav a { margin-left: 24px; font-size: .9rem; color: #555; transition: color .2s; }
        nav a:hover { color: #4f46e5; }

        /* 메인 */
        main { max-width: 1100px; margin: 0 auto; padding: 40px 24px; min-height: 70vh; }

        /* 카드 그리드 */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
        .card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.07); transition: transform .2s, box-shadow .2s; }
        .card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
        .card-body { padding: 24px; }
        .card-category { font-size: .75rem; font-weight: 600; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .card-title { font-size: 1.15rem; font-weight: 700; margin-bottom: 10px; line-height: 1.4; color: #1a1a1a; }
        .card-title:hover { color: #4f46e5; }
        .card-excerpt { font-size: .88rem; color: #666; margin-bottom: 16px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .card-meta { font-size: .78rem; color: #999; display: flex; gap: 12px; }

        /* 글 상세 */
        .post-header { max-width: 720px; margin: 0 auto 40px; }
        .post-category { font-size: .8rem; font-weight: 600; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
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
        .post-content blockquote { border-left: 4px solid #4f46e5; padding: 12px 20px; background: #f0f0ff; border-radius: 0 8px 8px 0; margin-bottom: 1.2rem; color: #444; }

        /* 히어로 */
        .hero { text-align: center; padding: 60px 0 50px; }
        .hero h1 { font-size: 2.5rem; font-weight: 800; color: #1a1a1a; margin-bottom: 12px; }
        .hero p { color: #666; font-size: 1.1rem; }

        /* 카테고리 탭 */
        .category-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 32px; }
        .tab { padding: 6px 16px; border-radius: 20px; font-size: .85rem; font-weight: 500; background: #fff; border: 1px solid #ddd; color: #555; transition: all .2s; }
        .tab:hover, .tab.active { background: #4f46e5; color: #fff; border-color: #4f46e5; }

        /* 페이지네이션 */
        .pagination { margin-top: 40px; display: flex; justify-content: center; gap: 8px; }
        .pagination a, .pagination span { padding: 8px 14px; border-radius: 8px; background: #fff; border: 1px solid #ddd; font-size: .85rem; color: #555; }
        .pagination .active span { background: #4f46e5; color: #fff; border-color: #4f46e5; }

        /* 관련글 */
        .related { max-width: 720px; margin: 60px auto 0; border-top: 1px solid #eee; padding-top: 40px; }
        .related h3 { font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; }
        .related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        @media (max-width: 640px) { .related-grid { grid-template-columns: 1fr; } .post-title { font-size: 1.6rem; } .grid { grid-template-columns: 1fr; } }

        /* 관리자 */
        .admin-wrap { max-width: 900px; margin: 0 auto; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: .9rem; font-weight: 600; cursor: pointer; border: none; transition: all .2s; }
        .btn-primary { background: #4f46e5; color: #fff; }
        .btn-primary:hover { background: #4338ca; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-secondary { background: #f1f5f9; color: #333; }
        .btn-secondary:hover { background: #e2e8f0; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: 6px; color: #555; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: .95rem; outline: none; transition: border .2s; }
        .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
        textarea.form-control { min-height: 400px; font-family: inherit; resize: vertical; }
        .table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.07); }
        .table th, .table td { padding: 14px 16px; text-align: left; font-size: .9rem; }
        .table th { background: #f8f9fa; font-weight: 600; color: #555; border-bottom: 1px solid #eee; }
        .table td { border-bottom: 1px solid #f1f1f1; }
        .table tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: .75rem; font-weight: 600; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-gray { background: #f1f5f9; color: #64748b; }
        .alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: .9rem; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; }
        .page-header h2 { font-size: 1.5rem; font-weight: 700; }

        /* 푸터 */
        footer { background: #1a1a1a; color: #aaa; text-align: center; padding: 32px 24px; font-size: .85rem; margin-top: 80px; }
        footer a { color: #aaa; }
    </style>
    @stack('styles')
</head>
<body>
<header>
    <div class="header-inner">
        <a href="{{ route('home') }}" class="logo">Dong<span>Pou</span> Blog</a>
        <nav>
            <a href="{{ route('home') }}">홈</a>
            <a href="{{ route('admin.posts.index') }}">관리자</a>
        </nav>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer>
    <p>&copy; {{ date('Y') }} DongPou Blog. All rights reserved.</p>
</footer>
@stack('scripts')
</body>
</html>
