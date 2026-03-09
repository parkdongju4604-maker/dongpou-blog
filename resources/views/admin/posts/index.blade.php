@extends('layouts.app')
@section('title', '글 관리 - 관리자')

@section('content')
<div class="admin-wrap">
    <div class="page-header">
        <h2>글 관리</h2>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">+ 새 글 작성</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>제목</th>
                <th>카테고리</th>
                <th>상태</th>
                <th>작성일</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
            <tr>
                <td style="color:#999">{{ $post->id }}</td>
                <td>
                    <a href="{{ route('posts.show', $post->slug) }}" target="_blank"
                       style="font-weight:600;color:#1a1a1a">{{ $post->title }}</a>
                </td>
                <td>{{ $post->category }}</td>
                <td>
                    @if($post->published)
                        <span class="badge badge-green">공개</span>
                    @else
                        <span class="badge badge-gray">임시저장</span>
                    @endif
                </td>
                <td>{{ $post->created_at->format('Y.m.d') }}</td>
                <td style="display:flex;gap:8px">
                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-secondary" style="padding:6px 12px;font-size:.8rem">수정</a>
                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
                          onsubmit="return confirm('정말 삭제하시겠습니까?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger" style="padding:6px 12px;font-size:.8rem">삭제</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:#999">
                    아직 작성한 글이 없습니다.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination" style="margin-top:24px">
        {{ $posts->links() }}
    </div>
</div>
@endsection
