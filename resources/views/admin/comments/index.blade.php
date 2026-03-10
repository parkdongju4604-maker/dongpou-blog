@extends('layouts.admin')
@section('title', '댓글 관리')
@section('page-title', '댓글 관리')

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 18px;margin-bottom:16px;color:#166534;font-size:.875rem">
    ✅ {{ session('success') }}
</div>
@endif

{{-- 통계 카드 --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
    @foreach([
        ['label'=>'전체', 'key'=>'all',      'color'=>'#4f46e5', 'bg'=>'#eef2ff'],
        ['label'=>'승인됨', 'key'=>'approved','color'=>'#16a34a', 'bg'=>'#dcfce7'],
        ['label'=>'검토 대기', 'key'=>'pending','color'=>'#d97706', 'bg'=>'#fffbeb'],
        ['label'=>'스팸',   'key'=>'spam',    'color'=>'#dc2626', 'bg'=>'#fee2e2'],
    ] as $card)
    <a href="?filter={{ $card['key'] }}"
       style="display:block;padding:16px 18px;background:{{ $filter===$card['key'] ? $card['bg'] : '#fff' }};border:1.5px solid {{ $filter===$card['key'] ? $card['color'] : '#e2e8f0' }};border-radius:10px;text-decoration:none;transition:all .15s">
        <div style="font-size:1.6rem;font-weight:800;color:{{ $card['color'] }}">{{ $counts[$card['key']] }}</div>
        <div style="font-size:.78rem;color:#64748b;margin-top:2px">{{ $card['label'] }}</div>
    </a>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <h3>
            @switch($filter)
                @case('spam') 🚫 스팸 댓글 @break
                @case('pending') ⏳ 검토 대기 @break
                @case('approved') ✅ 승인된 댓글 @break
                @default 전체 댓글
            @endswitch
            <span style="font-size:.8rem;color:#94a3b8;font-weight:400">{{ $comments->total() }}개</span>
        </h3>
        @if($filter === 'spam' && $counts['spam'] > 0)
        <form action="{{ route('admin.comments.purge-spam') }}" method="POST"
              onsubmit="return confirm('스팸 댓글 {{ $counts['spam'] }}개를 모두 삭제하시겠습니까?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">🗑 스팸 전체 삭제</button>
        </form>
        @endif
    </div>

    <div style="overflow-x:auto">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>작성자</th>
                    <th>내용</th>
                    <th>글</th>
                    <th>상태</th>
                    <th style="white-space:nowrap">스팸점수</th>
                    <th style="white-space:nowrap">작성일</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comments as $c)
                <tr style="{{ $c->is_spam ? 'opacity:.65;background:#fff5f5' : '' }}">
                    <td style="color:#cbd5e1">{{ $c->id }}</td>
                    <td>
                        <div style="font-weight:600;font-size:.85rem;color:#1e293b">{{ $c->author_name }}</div>
                        @if($c->author_email)
                        <div style="font-size:.73rem;color:#94a3b8">{{ $c->author_email }}</div>
                        @endif
                        @if($c->parent_id)
                        <div style="font-size:.7rem;color:#a78bfa;margin-top:2px">↳ 대댓글</div>
                        @endif
                    </td>
                    <td style="max-width:280px">
                        <div style="font-size:.84rem;color:#374151;line-height:1.5;
                             overflow:hidden;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical">
                            {{ $c->content }}
                        </div>
                    </td>
                    <td style="max-width:160px">
                        @if($c->post)
                        <a href="{{ route('posts.show', $c->post->slug) }}#comment-{{ $c->id }}"
                           target="_blank"
                           style="font-size:.8rem;color:#4f46e5;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">
                            {{ $c->post->title }}
                        </a>
                        @endif
                    </td>
                    <td>
                        @if($c->is_spam)
                            <span class="badge" style="background:#fee2e2;color:#dc2626">🚫 스팸</span>
                        @elseif($c->is_approved)
                            <span class="badge badge-green">✅ 승인</span>
                        @else
                            <span class="badge" style="background:#fffbeb;color:#d97706;border:1px solid #fde68a">⏳ 대기</span>
                        @endif
                    </td>
                    <td>
                        @if($c->spam_score > 0)
                        <span style="font-size:.8rem;font-weight:700;color:{{ $c->spam_score >= 10 ? '#dc2626' : ($c->spam_score >= 5 ? '#d97706' : '#94a3b8') }}">
                            {{ $c->spam_score }}점
                        </span>
                        @else
                        <span style="color:#94a3b8;font-size:.8rem">—</span>
                        @endif
                    </td>
                    <td style="color:#94a3b8;font-size:.8rem;white-space:nowrap">
                        {{ $c->created_at->format('m.d H:i') }}
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;flex-wrap:wrap">
                            {{-- 승인/취소 --}}
                            <form action="{{ route('admin.comments.approve', $c) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $c->is_approved && !$c->is_spam ? 'btn-secondary' : 'btn-primary' }}"
                                        title="{{ $c->is_approved && !$c->is_spam ? '승인 취소' : '승인' }}">
                                    {{ $c->is_approved && !$c->is_spam ? '취소' : '승인' }}
                                </button>
                            </form>
                            {{-- 스팸 처리 --}}
                            @if(!$c->is_spam)
                            <form action="{{ route('admin.comments.spam', $c) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa"
                                        title="스팸 처리">스팸</button>
                            </form>
                            @endif
                            {{-- 삭제 --}}
                            <form action="{{ route('admin.comments.destroy', $c) }}" method="POST"
                                  onsubmit="return confirm('이 댓글을 삭제하시겠습니까?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">삭제</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#94a3b8">
                        댓글이 없습니다.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($comments->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f1f5f9">
        {{ $comments->links() }}
    </div>
    @endif
</div>
@endsection
