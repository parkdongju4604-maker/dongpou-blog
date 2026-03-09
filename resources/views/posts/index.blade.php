@php
    use App\Models\Setting;
    $heroTitle    = Setting::get('hero_title', '최신 글');
    $heroSubtitle = Setting::get('hero_subtitle', '다양한 주제의 글을 만나보세요.');
    $blogName     = Setting::get('blog_name', config('app.name'));
@endphp
@extends('layouts.app')
@section('title', $blogName)

@section('content')
<div class="hero">
    <h1>{{ $heroTitle }}</h1>
    <p>{{ $heroSubtitle }}</p>
</div>

<div class="category-tabs">
    <a href="{{ route('home') }}" class="tab {{ !isset($category) ? 'active' : '' }}">전체</a>
    @foreach($categories as $cat)
        <a href="{{ route('posts.category', $cat) }}"
           class="tab {{ (isset($category) && $category === $cat) ? 'active' : '' }}">
            {{ $cat }}
        </a>
    @endforeach
</div>

@if($posts->isEmpty())
    <div style="text-align:center;padding:80px 0;color:#999">
        <p style="font-size:3rem;margin-bottom:16px">📝</p>
        <p>아직 게시된 글이 없습니다.</p>
    </div>
@else
    <div class="grid">
        @foreach($posts as $post)
        <a href="{{ route('posts.show', $post->slug) }}" class="card">
            <div class="card-body">
                <div class="card-category">{{ $post->category }}</div>
                <div class="card-title">{{ $post->title }}</div>
                @if($post->excerpt)
                    <div class="card-excerpt">{{ $post->excerpt }}</div>
                @endif
                <div class="card-meta">
                    <span>{{ $post->published_at?->format('Y.m.d') }}</span>
                    <span>읽기 {{ $post->reading_time }}분</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="pagination">
        {{ $posts->links() }}
    </div>
@endif
@endsection
