@extends('layouts.admin')
@section('title', '새 글 작성')
@section('page-title', '새 글 작성')

@section('content')
<form method="POST" action="{{ route('admin.posts.store') }}">
    @csrf
    @include('admin.posts._form')
    <div style="margin-top:20px;display:flex;gap:10px;justify-content:flex-end">
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">취소</a>
        <button type="submit" class="btn btn-primary" style="padding:10px 28px">글 등록</button>
    </div>
</form>
@endsection
