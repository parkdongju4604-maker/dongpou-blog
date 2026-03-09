@extends('layouts.admin')
@section('title', '대시보드')
@section('page-title', '대시보드')

@section('content')
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-label">전체 글</div>
        <div class="stat-value">{{ $totalPosts }}</div>
        <div class="stat-sub">등록된 글</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">공개 글</div>
        <div class="stat-value">{{ $publishedPosts }}</div>
        <div class="stat-sub">게시 중</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">예약 발행</div>
        <div class="stat-value" style="color:#d97706">{{ $scheduledPosts }}</div>
        <div class="stat-sub">발행 예정</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">임시저장</div>
        <div class="stat-value">{{ $draftPosts }}</div>
        <div class="stat-sub">비공개</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">카테고리</div>
        <div class="stat-value">{{ $totalCategories }}</div>
        <div class="stat-sub">등록된 카테고리</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px">
    {{-- 최근 글 --}}
    <div class="card">
        <div class="card-header">
            <h3>최근 글</h3>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">+ 새 글</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>제목</th>
                        <th>카테고리</th>
                        <th>상태</th>
                        <th>작성일</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPosts as $post)
                    <tr>
                        <td style="font-weight:600;max-width:280px">
                            <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block">{{ $post->title }}</span>
                        </td>
                        <td><span class="badge badge-purple">{{ $post->category }}</span></td>
                        <td>
                            @if($post->status === 'published')
                                <span class="badge badge-green">발행됨</span>
                            @elseif($post->status === 'scheduled')
                                <span class="badge" style="background:#fffbeb;color:#92400e;border:1px solid #fde68a;white-space:nowrap;font-size:.68rem">
                                    ⏰ {{ $post->published_at->format('m.d H:i') }}
                                </span>
                            @else
                                <span class="badge badge-gray">임시저장</span>
                            @endif
                        </td>
                        <td style="color:#94a3b8;white-space:nowrap">{{ $post->created_at->format('Y.m.d') }}</td>
                        <td>
                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-secondary btn-sm">수정</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:32px;color:#94a3b8">아직 글이 없습니다.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($totalPosts > 8)
        <div style="padding:12px 20px;border-top:1px solid #f1f5f9">
            <a href="{{ route('admin.posts.index') }}" style="font-size:.82rem;color:#6366f1">전체 글 보기 →</a>
        </div>
        @endif
    </div>

    {{-- 퀵 링크 / 블로그 정보 --}}
    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="card">
            <div class="card-header"><h3>블로그 정보</h3></div>
            <div class="card-body" style="font-size:.85rem;line-height:2">
                <div style="color:#64748b">블로그 이름</div>
                <div style="font-weight:600">{{ $blogName }}</div>
                <div style="color:#64748b;margin-top:8px">태그라인</div>
                <div style="font-weight:600">{{ $blogTagline }}</div>
                <div style="margin-top:16px">
                    <a href="{{ route('admin.settings') }}" class="btn btn-secondary" style="width:100%;justify-content:center">설정 변경 →</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>빠른 작업</h3></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:8px">
                <a href="{{ route('admin.posts.create') }}" class="btn btn-primary" style="justify-content:center">✏️ 새 글 작성</a>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary" style="justify-content:center">📁 카테고리 관리</a>
                <a href="{{ route('home') }}" target="_blank" class="btn btn-secondary" style="justify-content:center">🔗 블로그 열기</a>
            </div>
        </div>
    </div>
</div>
@endsection
