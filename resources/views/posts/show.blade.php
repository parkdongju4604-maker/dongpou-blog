@php
    use Illuminate\Support\Str;
    use App\Models\Setting;

    $authorName   = Setting::get('author_name', Setting::get('blog_name', config('app.name')));
    $ogImgDefault = Setting::get('og_image_default', '');
    $excerpt      = $post->excerpt ?: Str::limit(strip_tags($post->content), 155);
    $postUrl      = route('posts.show', $post->slug);
    $blogName     = Setting::get('blog_name', config('app.name'));
    $baseUrl      = url('/');

    // JSON-LD: Article
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type'    => 'Article',
        'headline'          => $post->title,
        'description'       => $excerpt,
        'datePublished'     => $post->published_at?->toIso8601String(),
        'dateModified'      => $post->updated_at->toIso8601String(),
        'author'            => ['@type' => 'Person', 'name' => $authorName],
        'publisher'         => ['@type' => 'Organization', 'name' => $blogName, 'url' => $baseUrl],
        'mainEntityOfPage'  => ['@type' => 'WebPage', '@id' => $postUrl],
    ];
    if ($ogImgDefault) {
        $articleSchema['image'] = $ogImgDefault;
    }

    // JSON-LD: BreadcrumbList
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => $blogName, 'item' => $baseUrl],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $post->category, 'item' => route('posts.category', $post->category)],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => $postUrl],
        ],
    ];
@endphp

@extends('layouts.app')

@section('title'){{ $post->title . ' — ' . $blogName }}@endsection
@section('description'){{ $excerpt }}@endsection
@section('author'){{ $authorName }}@endsection
@section('canonical'){{ $postUrl }}@endsection
@section('og:type')article@endsection
@section('og:title'){{ $post->title }}@endsection
@section('og:description'){{ $excerpt }}@endsection
@if($ogImgDefault)
@section('og:image'){{ $ogImgDefault }}@endsection
@endif

@push('jsonld')
<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@push('head')
<meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
<meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
<meta property="article:section" content="{{ $post->category }}">
@endpush

@section('content')

{{-- 브레드크럼 --}}
<nav aria-label="브레드크럼" style="max-width:720px;margin:0 auto 20px">
    <ol style="display:flex;align-items:center;gap:6px;list-style:none;font-size:.8rem;color:#94a3b8;flex-wrap:wrap">
        <li><a href="{{ route('home') }}" style="color:#94a3b8" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='#94a3b8'">홈</a></li>
        <li aria-hidden="true">›</li>
        <li><a href="{{ route('posts.category', $post->category) }}" style="color:#94a3b8" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='#94a3b8'">{{ $post->category }}</a></li>
        <li aria-hidden="true">›</li>
        <li style="color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px" aria-current="page">{{ $post->title }}</li>
    </ol>
</nav>

<article itemscope itemtype="https://schema.org/Article">
    <meta itemprop="datePublished" content="{{ $post->published_at?->toIso8601String() }}">
    <meta itemprop="dateModified"  content="{{ $post->updated_at->toIso8601String() }}">
    <meta itemprop="author"        content="{{ $authorName }}">

    <header class="post-header">
        <div class="post-category">
            <a href="{{ route('posts.category', $post->category) }}" style="color:inherit" itemprop="articleSection">{{ $post->category }}</a>
        </div>
        <h1 class="post-title" itemprop="headline">{{ $post->title }}</h1>
        <div class="post-meta">
            <time datetime="{{ $post->published_at?->toIso8601String() }}">
                {{ $post->published_at?->format('Y년 m월 d일') }}
            </time>
            <span aria-hidden="true">·</span>
            <span>읽기 약 {{ $post->reading_time }}분</span>
        </div>
    </header>

    <div class="post-content" itemprop="articleBody">
        {!! nl2br(e($post->content)) !!}
    </div>
</article>

{{-- 관련 글 --}}
@if($related->isNotEmpty())
<aside class="related" aria-label="관련 글">
    <h2 class="related-title" style="font-size:1.15rem;font-weight:700;margin-bottom:18px">관련 글</h2>
    <div class="related-grid">
        @foreach($related as $r)
        <a href="{{ route('posts.show', $r->slug) }}" class="card">
            <div class="card-body" style="padding:16px">
                <div class="card-category">{{ $r->category }}</div>
                <div class="card-title" style="font-size:1rem">{{ $r->title }}</div>
                <div class="card-meta">
                    <time datetime="{{ $r->published_at?->toIso8601String() }}">{{ $r->published_at?->format('Y.m.d') }}</time>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</aside>
@endif

<div style="max-width:720px;margin:32px auto 0">
    <a href="{{ route('home') }}" class="btn btn-secondary">← 목록으로</a>
</div>
@endsection
