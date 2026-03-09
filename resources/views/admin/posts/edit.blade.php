@extends('layouts.app')
@section('title', '글 수정')

@section('content')
<div class="admin-wrap">
    <div class="page-header">
        <h2>글 수정</h2>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">← 목록</a>
    </div>

    <form action="{{ route('admin.posts.update', $post) }}" method="POST"
          style="background:#fff;padding:32px;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,.07)">
        @csrf @method('PUT')
        @include('admin.posts._form')
        <div style="display:flex;gap:12px;margin-top:8px">
            <button type="submit" class="btn btn-primary">저장하기</button>
            <a href="{{ route('posts.show', $post->slug) }}" target="_blank" class="btn btn-secondary">미리보기</a>
        </div>
    </form>
</div>
@endsection
