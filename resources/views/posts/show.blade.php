@extends('layouts.app')
@section('title', $post->title . ' - DongPou Blog')
@section('description', $post->excerpt ?? Str::limit(strip_tags($post->content), 150))

@section('content')
<article>
    <div class="post-header">
        <div class="post-category">{{ $post->category }}</div>
        <h1 class="post-title">{{ $post->title }}</h1>
        <div class="post-meta">
            <span>{{ $post->published_at?->format('Y년 m월 d일') }}</span>
            <span>·</span>
            <span>읽기 {{ $post->reading_time }}분</span>
        </div>
    </div>

    <div class="post-content">
        {!! nl2br(e($post->content)) !!}
    </div>
</article>

@if($related->isNotEmpty())
<div class="related">
    <h3>관련 글</h3>
    <div class="related-grid">
        @foreach($related as $r)
        <a href="{{ route('posts.show', $r->slug) }}" class="card">
            <div class="card-body" style="padding:16px">
                <div class="card-category">{{ $r->category }}</div>
                <div class="card-title" style="font-size:1rem">{{ $r->title }}</div>
                <div class="card-meta">{{ $r->published_at?->format('Y.m.d') }}</div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

<div style="max-width:720px;margin:32px auto 0">
    <a href="{{ route('home') }}" class="btn btn-secondary">← 목록으로</a>
</div>
@endsection

@php use Illuminate\Support\Str; @endphp
