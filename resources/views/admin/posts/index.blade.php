@extends('layouts.admin')
@section('title', '글 관리')
@section('page-title', '글 관리')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>전체 글 <span style="font-size:.8rem;color:#94a3b8;font-weight:400">{{ $posts->total() }}개</span></h3>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">+ 새 글 작성</a>
    </div>
    <div class="table-wrap">
        <table>
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
                    <td style="color:#cbd5e1;width:40px">{{ $post->id }}</td>
                    <td>
                        <a href="{{ route('posts.show', $post->slug) }}" target="_blank"
                           style="font-weight:600;color:#1e293b;max-width:340px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            {{ $post->title }}
                        </a>
                    </td>
                    <td><span class="badge badge-purple">{{ $post->category }}</span></td>
                    <td>
                        @if($post->status === 'published')
                            <span class="badge badge-green">발행됨</span>
                        @elseif($post->status === 'scheduled')
                            <span class="badge" style="background:#fffbeb;color:#92400e;border:1px solid #fde68a;white-space:nowrap">
                                ⏰ {{ $post->published_at->format('m.d H:i') }}
                            </span>
                        @else
                            <span class="badge badge-gray">임시저장</span>
                        @endif
                    </td>
                    <td style="color:#94a3b8;white-space:nowrap">{{ $post->created_at->format('Y.m.d') }}</td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-secondary btn-sm">수정</a>
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
                                  onsubmit="return confirm('정말 삭제하시겠습니까?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">삭제</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8">
                        아직 작성한 글이 없습니다.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($posts->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f1f5f9">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
