@extends('layouts.app')
@section('title', '새 글 작성')

@section('content')
<div class="admin-wrap">
    <div class="page-header">
        <h2>새 글 작성</h2>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">← 목록</a>
    </div>

    <form action="{{ route('admin.posts.store') }}" method="POST"
          style="background:#fff;padding:32px;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,.07)">
        @csrf
        @include('admin.posts._form')
        <div style="display:flex;gap:12px;margin-top:8px">
            <button type="submit" class="btn btn-primary">게시하기</button>
        </div>
    </form>
</div>
@endsection
