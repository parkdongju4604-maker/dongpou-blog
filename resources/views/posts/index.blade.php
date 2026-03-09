@extends('layouts.app')
@section('title', 'DongPou Blog')

@section('content')
<div class="hero">
    <h1>DongPou Blog</h1>
    <p>개발, 마케팅, 일상의 기록</p>
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
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary" style="margin-top:20px">첫 글 작성하기</a>
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
