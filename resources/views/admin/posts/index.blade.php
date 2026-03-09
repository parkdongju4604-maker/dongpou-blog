@extends('layouts.admin')
@section('title', '글 관리')
@section('page-title', '글 관리')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>전체 글 <span style="font-size:.8rem;color:#94a3b8;font-weight:400">{{ $posts->total() }}개</span></h3>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">+ 새 글 작성</a>
    </div>

    {{-- 검색 / 필터 --}}
    <form method="GET" action="{{ route('admin.posts.index') }}"
          style="display:flex;gap:8px;flex-wrap:wrap;padding:14px 20px;border-bottom:1px solid #f1f5f9;background:#fafcff;align-items:center;">
        <div style="position:relative;flex:1;min-width:180px;">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="q" value="{{ $q }}" placeholder="제목/내용 검색..."
                   style="width:100%;padding:7px 10px 7px 32px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:.85rem;outline:none;"
                   onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
        <select name="status"
                style="padding:7px 12px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:.85rem;outline:none;background:#fff;cursor:pointer;"
                onchange="this.form.submit()">
            <option value=""        {{ $status===''          ? 'selected':'' }}>전체 상태</option>
            <option value="published" {{ $status==='published' ? 'selected':'' }}>✅ 발행됨</option>
            <option value="scheduled" {{ $status==='scheduled' ? 'selected':'' }}>⏰ 예약됨</option>
            <option value="draft"     {{ $status==='draft'     ? 'selected':'' }}>🗒 임시저장</option>
        </select>
        <select name="category"
                style="padding:7px 12px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:.85rem;outline:none;background:#fff;cursor:pointer;"
                onchange="this.form.submit()">
            <option value="">전체 카테고리</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ $category===$cat ? 'selected':'' }}>{{ $cat }}</option>
            @endforeach
        </select>
        <button type="submit" style="padding:7px 16px;background:#4f46e5;color:#fff;border:none;border-radius:7px;font-size:.85rem;font-weight:600;cursor:pointer;">검색</button>
        @if($q || $status || $category)
            <a href="{{ route('admin.posts.index') }}"
               style="padding:7px 14px;background:#f1f5f9;color:#64748b;border-radius:7px;font-size:.83rem;font-weight:500;text-decoration:none;">✕ 초기화</a>
        @endif
    </form>
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
