@php
    use App\Models\Setting;
    $heroTitle    = Setting::get('hero_title',    '최신 글');
    $heroSubtitle = Setting::get('hero_subtitle', '다양한 주제의 글을 만나보세요.');
    $blogName     = Setting::get('blog_name',     config('app.name'));
    $blogDesc     = Setting::get('blog_description', Setting::get('blog_tagline',''));
    $canonicalUrl = isset($category) ? route('posts.category', $category) : route('home');
    $blogSchema   = ['@context'=>'https://schema.org','@type'=>'Blog','name'=>$blogName,'description'=>$blogDesc,'url'=>url('/')];
@endphp

@extends('layouts.app')
@section('title', $blogName . (isset($category) ? ' — ' . $category : ''))
@section('description', $blogDesc)
@section('canonical', $canonicalUrl)

@push('jsonld')
<script type="application/ld+json">{!! json_encode($blogSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')

<div class="hero">
    <div class="hero-eyebrow">{{ $blogName }}</div>
    <h1>{{ isset($category) ? $category : $heroTitle }}</h1>
    <p>{{ isset($category) ? $category . ' 카테고리의 글 목록입니다.' : $heroSubtitle }}</p>
</div>

<nav aria-label="카테고리 필터">
    <div class="category-tabs">
        <a href="{{ route('home') }}"
           class="tab {{ !isset($category) ? 'active' : '' }}"
           aria-current="{{ !isset($category) ? 'page' : 'false' }}">전체</a>
        @foreach($categories as $cat)
            <a href="{{ route('posts.category', $cat) }}"
               class="tab {{ isset($category) && $category === $cat ? 'active' : '' }}"
               aria-current="{{ isset($category) && $category === $cat ? 'page' : 'false' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>
</nav>

@if($posts->isEmpty())
    <div style="text-align:center;padding:80px 0 100px;color:#9ca3af">
        <div style="font-size:3.5rem;margin-bottom:16px;opacity:.4">✍️</div>
        <p style="font-size:1rem;font-weight:500">아직 게시된 글이 없습니다.</p>
    </div>
@else
    <div class="grid" role="list">
        @foreach($posts as $post)
        <a href="{{ route('posts.show', $post->slug) }}" class="card" role="listitem">
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
                    <span class="card-meta-dot"></span>
                    <span>{{ $post->reading_time }}분 읽기</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="pagination" aria-label="페이지 탐색">
        {{ $posts->links() }}
    </div>
@endif

@endsection
