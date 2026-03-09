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

@section('title', $post->title . ' — ' . $blogName)
@section('description', $excerpt)
@section('author', $authorName)
@section('canonical', $postUrl)
@section('og:type', 'article')
@section('og:title', $post->title)
@section('og:description', $excerpt)
@if($ogImgDefault)
@section('og:image', $ogImgDefault)
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

<div class="post-wrap">

    {{-- 브레드크럼 --}}
    <nav aria-label="브레드크럼" style="margin-bottom:20px">
        <ol style="display:flex;align-items:center;gap:6px;list-style:none;font-size:.78rem;color:#9ca3af;flex-wrap:wrap">
            <li><a href="{{ route('home') }}" style="color:#9ca3af;transition:color .15s" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='#9ca3af'">홈</a></li>
            <li aria-hidden="true" style="font-size:.65rem">›</li>
            <li><a href="{{ route('posts.category', $post->category) }}" style="color:#9ca3af;transition:color .15s" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='#9ca3af'">{{ $post->category }}</a></li>
            <li aria-hidden="true" style="font-size:.65rem">›</li>
            <li style="color:#6b7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px" aria-current="page">{{ $post->title }}</li>
        </ol>
    </nav>

    <article itemscope itemtype="https://schema.org/Article">
        <meta itemprop="datePublished" content="{{ $post->published_at?->toIso8601String() }}">
        <meta itemprop="dateModified"  content="{{ $post->updated_at->toIso8601String() }}">
        <meta itemprop="author"        content="{{ $authorName }}">

        {{-- 포스트 헤더 --}}
        <header class="post-header">
            <a href="{{ route('posts.category', $post->category) }}" class="post-category" itemprop="articleSection">
                {{ $post->category }}
            </a>
            <h1 class="post-title" itemprop="headline">{{ $post->title }}</h1>
            <div class="post-meta">
                <time datetime="{{ $post->published_at?->toIso8601String() }}">
                    {{ $post->published_at?->format('Y년 m월 d일') }}
                </time>
                <span class="dot">·</span>
                <span>읽기 약 {{ $post->reading_time }}분</span>
            </div>
        </header>

        {{-- 본문 --}}
        <div class="post-content" itemprop="articleBody">
            {!! $post->rendered_content !!}
        </div>
    </article>

    {{-- 관련 글 --}}
    @if($related->isNotEmpty())
    <aside class="related" aria-label="관련 글">
        <h2 style="font-size:1.1rem;font-weight:700;color:#111827;margin-bottom:16px">관련 글</h2>
        <div class="related-grid">
            @foreach($related as $r)
            <a href="{{ route('posts.show', $r->slug) }}" class="card">
                <div class="card-body" style="padding:16px">
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

    {{-- 하단 버튼 --}}
    <div style="margin-top:36px;padding-top:24px;border-top:1px solid #f3f4f6">
        <a href="{{ route('home') }}" class="btn btn-secondary">← 목록으로</a>
    </div>

</div>
@endsection
