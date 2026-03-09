@php
    use Illuminate\Support\Str;
    use App\Models\Setting;

    $authorName   = Setting::get('author_name', Setting::get('blog_name', config('app.name')));
    $ogImgDefault = Setting::get('og_image_default', '');

    // 메타: 글 정보 기반 자동 생성 (전역 설정 fallback)
    $seoTitle   = $post->title;
    $excerpt    = $post->excerpt ?: Str::limit(strip_tags($post->content), 155);
    $ogImage    = $post->thumbnail ?: $ogImgDefault;
    $postUrl      = route('posts.show', $post->slug);
    $blogName     = Setting::get('blog_name', config('app.name'));
    $baseUrl      = url('/');

    $articleSchema = [
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        'headline'         => $seoTitle,
        'description'      => $excerpt,
        'datePublished'    => $post->published_at?->toIso8601String(),
        'dateModified'     => $post->updated_at->toIso8601String(),
        'author'           => ['@type' => 'Person', 'name' => $authorName],
        'publisher'        => ['@type' => 'Organization', 'name' => $blogName, 'url' => $baseUrl],
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $postUrl],
    ];
    if ($ogImage) { $articleSchema['image'] = $ogImage; }

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => $blogName, 'item' => $baseUrl],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $post->category, 'item' => route('posts.category', $post->category)],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => $postUrl],
        ],
    ];

    // 목차(TOC) 자동 생성
    preg_match_all('/<h([2-3])[^>]*>(.*?)<\/h[2-3]>/i', $post->rendered_content, $headings);
    $tocItems = [];
    foreach ($headings[1] as $i => $level) {
        $text = strip_tags($headings[2][$i]);
        $id   = 'h-' . Str::slug($text) ?: 'h-' . $i;
        $tocItems[] = ['level' => (int)$level, 'text' => $text, 'id' => $id];
    }

    // 렌더링된 콘텐츠에 heading id 삽입
    $renderedContent = preg_replace_callback(
        '/<h([2-3])([^>]*)>(.*?)<\/h[2-3]>/i',
        function($m) {
            $text = strip_tags($m[3]);
            $id   = 'h-' . \Illuminate\Support\Str::slug($text);
            return "<h{$m[1]} id=\"{$id}\"{$m[2]}>{$m[3]}</h{$m[1]}>";
        },
        $post->rendered_content
    );
@endphp

@extends('layouts.app')

@section('title', $seoTitle . ' — ' . $blogName)
@section('description', $excerpt)
@section('author', $authorName)
@section('canonical', $postUrl)
@section('og:type', 'article')
@section('og:title', $seoTitle)
@section('og:description', $excerpt)
@if($ogImage)
@section('og:image', $ogImage)
@endif

@push('jsonld')
<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@push('head')
<meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
<meta property="article:modified_time"  content="{{ $post->updated_at->toIso8601String() }}">
<meta property="article:section"        content="{{ $post->category }}">
@endpush

@section('content')

<div class="post-layout">

    {{-- ── 메인 컬럼 ── --}}
    <div>
        {{-- 브레드크럼 --}}
        <nav aria-label="브레드크럼">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">홈</a></li>
                <li class="breadcrumb-sep" aria-hidden="true">›</li>
                <li><a href="{{ route('posts.category', $post->category) }}">{{ $post->category }}</a></li>
                <li class="breadcrumb-sep" aria-hidden="true">›</li>
                <li style="color:#6b7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px" aria-current="page">{{ $post->title }}</li>
            </ol>
        </nav>

        {{-- 포스트 헤더 --}}
        <article itemscope itemtype="https://schema.org/Article">
            <meta itemprop="datePublished" content="{{ $post->published_at?->toIso8601String() }}">
            <meta itemprop="dateModified"  content="{{ $post->updated_at->toIso8601String() }}">
            <meta itemprop="author"        content="{{ $authorName }}">

            <header class="post-hero">
                <a href="{{ route('posts.category', $post->category) }}"
                   class="post-category-badge" itemprop="articleSection">
                    {{ $post->category }}
                </a>
                <h1 class="post-title" itemprop="headline">{{ $post->title }}</h1>
                <div class="post-meta">
                    <time datetime="{{ $post->published_at?->toIso8601String() }}">
                        {{ $post->published_at?->format('Y년 m월 d일') }}
                    </time>
                    <span class="post-meta-sep">·</span>
                    <span class="post-meta-badge">읽기 {{ $post->reading_time }}분</span>
                    <span class="post-meta-sep">·</span>
                    <span class="post-meta-badge">👁 {{ number_format($post->view_count) }}</span>
                </div>
            </header>

            {{-- 본문 --}}
            <div class="post-content" id="post-content" itemprop="articleBody">
                {!! $renderedContent !!}
            </div>
        </article>

        {{-- 관련 글 --}}
        @if($related->isNotEmpty())
        <aside class="related" aria-label="관련 글">
            <h2>관련 글</h2>
            <div class="related-grid">
                @foreach($related as $r)
                <a href="{{ route('posts.show', $r->slug) }}" class="card">
                    <div class="card-body" style="padding:18px">
                        <div class="card-category">{{ $r->category }}</div>
                        <div class="card-title" style="font-size:.95rem">{{ $r->title }}</div>
                        <div class="card-meta" style="margin-top:10px">
                            <time datetime="{{ $r->published_at?->toIso8601String() }}">{{ $r->published_at?->format('Y.m.d') }}</time>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </aside>
        @endif

        {{-- 하단 --}}
        <footer class="post-footer">
            <a href="{{ route('home') }}" class="btn btn-secondary">
                ← 목록으로
            </a>
            <time datetime="{{ $post->published_at?->toIso8601String() }}"
                  style="font-size:.78rem;color:#9ca3af">
                {{ $post->published_at?->format('Y.m.d') }}
            </time>
        </footer>
    </div>

    {{-- ── 사이드바: 목차 ── --}}
    @if(count($tocItems) >= 2)
    <aside class="post-sidebar">
        <div class="toc-box">
            <h2 class="toc-title" id="toc-label">목차</h2>
            <ul class="toc-list" aria-labelledby="toc-label">
                @foreach($tocItems as $item)
                <li class="{{ $item['level'] === 3 ? 'toc-h3' : '' }}">
                    <a href="#{{ $item['id'] }}">{{ $item['text'] }}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </aside>
    @endif

</div>

@endsection

@push('scripts')
<script>
// 목차 활성화 (스크롤 위치 기반)
(function(){
    const tocLinks = document.querySelectorAll('.toc-list a');
    if (!tocLinks.length) return;

    const headings = Array.from(document.querySelectorAll('.post-content h2, .post-content h3'));
    if (!headings.length) return;

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                tocLinks.forEach(a => a.classList.remove('active'));
                const active = document.querySelector('.toc-list a[href="#' + entry.target.id + '"]');
                if (active) active.classList.add('active');
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    headings.forEach(h => observer.observe(h));
})();
</script>
@endpush
