@php
    use App\Models\Setting;
    $heroTitle    = Setting::get('hero_title',    '최신 글');
    $heroSubtitle = Setting::get('hero_subtitle', '다양한 주제의 글을 만나보세요.');
    $blogName     = Setting::get('blog_name',     config('app.name'));
    $blogDesc     = Setting::get('blog_description', Setting::get('blog_tagline',''));
    $isTagPage    = isset($tag);
    $isAuthorPage = isset($authorNameForArchive);
    $canonicalUrl = $isTagPage
        ? route('tags.show', $tag->slug)
        : ($isAuthorPage
            ? route('posts.author', ['authorSlug' => ($currentAuthorSlug ?? 'author')])
            : (isset($category)
                ? route('posts.category', ['categorySlug' => ($currentCategorySlug ?? rawurlencode($category))])
                : route('home')));
    $blogSchema   = ['@context'=>'https://schema.org','@type'=>'Blog','name'=>$blogName,'description'=>$blogDesc,'url'=>url('/')];
@endphp

@extends('layouts.app')
@section('title', $isTagPage
    ? '#'.$tag->name.' | '.$blogName
    : ($isAuthorPage
        ? $authorNameForArchive.' 작성 글 | '.$blogName
        : (isset($category) ? $category.' | '.$blogName : $blogName)))
@section('description', $isAuthorPage ? $authorNameForArchive.' 작성자의 글 모음입니다.' : $blogDesc)
@section('canonical', $canonicalUrl)
@section('robots', ($isTagPage || isset($category)) ? 'noindex,follow' : '')

@push('jsonld')
<script type="application/ld+json">{!! json_encode($blogSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')

<section class="hero" aria-labelledby="hero-heading">
    <div class="hero-eyebrow">{{ $blogName }}</div>
    <h1 id="hero-heading">
        @if($isTagPage) #{{ $tag->name }}
        @elseif($isAuthorPage) {{ $authorNameForArchive }} 작성 글
        @elseif(isset($category)) {{ $category }}
        @else {{ $heroTitle }}
        @endif
    </h1>
    <p>
        @if($isTagPage) <span style="background:#eef2ff;color:#4f46e5;padding:3px 10px;border-radius:20px;font-size:.875rem">#{{ $tag->name }}</span> 태그가 달린 글 {{ $posts->total() }}개
        @elseif($isAuthorPage) {{ $authorNameForArchive }} 작성자의 공개 글 {{ $posts->total() }}개
        @elseif(isset($category)) {{ $category }} 카테고리의 글 목록입니다.
        @else {{ $heroSubtitle }}
        @endif
    </p>
</section>

@if(!$isAuthorPage)
<nav aria-label="카테고리 필터">
    <div class="category-tabs">
        <a href="{{ route('home') }}"
           class="tab {{ !isset($category) ? 'active' : '' }}"
           aria-current="{{ !isset($category) ? 'page' : 'false' }}">전체</a>
        @foreach($categories as $cat)
            <a href="{{ route('posts.category', ['categorySlug' => $cat['slug']]) }}"
               class="tab {{ isset($category) && $category === $cat['name'] ? 'active' : '' }}"
               aria-current="{{ isset($category) && $category === $cat['name'] ? 'page' : 'false' }}">
                {{ $cat['name'] }}
            </a>
        @endforeach
    </div>
</nav>
@endif

@if($posts->isEmpty())
    <div style="text-align:center;padding:80px 0 100px;color:#9ca3af">
        <div style="font-size:3.5rem;margin-bottom:16px;opacity:.4">✍️</div>
        <p style="font-size:1rem;font-weight:500">아직 게시된 글이 없습니다.</p>
    </div>
@else
    <div class="grid" role="list">
        @foreach($posts as $post)
        <article role="listitem">
            <a href="{{ route('posts.show', ['categorySlug' => $post->category_path_segment, 'slug' => $post->slug]) }}" class="card">
                <div class="card-body">
                    <div class="card-category">{{ $post->category }}</div>
                    <h2 class="card-title">{{ $post->title }}</h2>
                    @if($post->excerpt)
                        <p class="card-excerpt">{{ $post->excerpt }}</p>
                    @endif
                    <div class="card-meta">
                        <time datetime="{{ $post->published_at?->toIso8601String() }}">
                            {{ $post->published_at?->format('Y.m.d') }}
                        </time>
                        <span class="card-meta-dot" aria-hidden="true"></span>
                        <span>{{ $post->reading_time }}분 읽기</span>
                        <span class="card-meta-dot" aria-hidden="true"></span>
                        <span>👁 {{ number_format($post->view_count) }}</span>
                    </div>
                    @if($post->relationLoaded('tags') && $post->tags->isNotEmpty())
                    <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:8px">
                        @foreach($post->tags->take(3) as $tag)
                        <span style="display:inline-block;padding:2px 8px;background:#eef2ff;color:#4f46e5;border-radius:20px;font-size:.7rem;font-weight:600">#{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </a>
        </article>
        @endforeach
    </div>
    <nav class="pagination" aria-label="페이지 탐색">
        {{ $posts->links() }}
    </nav>
@endif

@endsection
