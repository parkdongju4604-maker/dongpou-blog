@extends('layouts.admin')
@section('title', '글 수정')
@section('page-title', '글 수정')

@section('content')
<form method="POST" action="{{ route('admin.posts.update', $post) }}" id="update-form">
    @csrf @method('PUT')
    @include('admin.posts._form')
</form>

<div style="margin-top:20px;display:flex;gap:10px;justify-content:space-between;align-items:center">
    {{-- 삭제 버튼 (별도 폼) --}}
    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
          onsubmit="return confirm('정말 삭제하시겠습니까?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">글 삭제</button>
    </form>

    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">취소</a>
        <button type="submit" form="update-form" class="btn btn-primary" style="padding:10px 28px">수정 저장</button>
    </div>
</div>
@endsection
