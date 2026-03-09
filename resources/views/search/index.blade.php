@extends('layouts.app')
@section('title', $q ? ""{$q}" 검색 결과" : '검색')
@section('description', $q ? "'{$q}' 검색 결과 {$total}건" : '블로그 내 검색')

@push('styles')
<style>
.search-hero {
    padding: 52px 0 40px;
    border-bottom: 1px solid var(--border, #f0f0f5);
    margin-bottom: 40px;
}
.search-hero h1 {
    font-size: clamp(1.4rem, 3vw, 2rem);
    font-weight: 800; color: #0f0f23;
    margin-bottom: 20px; letter-spacing: -.5px;
}
.search-form {
    display: flex; gap: 0; max-width: 600px;
}
.search-input {
    flex: 1; padding: 12px 18px;
    border: 2px solid var(--primary, #4f46e5);
    border-right: none;
    border-radius: 10px 0 0 10px;
    font-size: 1rem; outline: none;
    font-family: inherit;
    background: #fff; color: #0f0f23;
}
.search-input:focus { box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
.search-btn {
    padding: 12px 22px;
    background: var(--primary, #4f46e5); color: #fff;
    border: none; border-radius: 0 10px 10px 0;
    font-size: .9rem; font-weight: 700; cursor: pointer;
    transition: background .15s;
}
.search-btn:hover { background: var(--primary-dark, #4338ca); }

.search-meta {
    font-size: .875rem; color: #6b7280; margin-top: 14px;
}
.search-meta strong { color: var(--primary, #4f46e5); }

/* 결과 카드 */
.search-results { display: flex; flex-direction: column; gap: 0; }
.search-item {
    padding: 24px 0; border-bottom: 1px solid var(--border, #f0f0f5);
    display: block; text-decoration: none; color: inherit;
    transition: background .15s;
}
.search-item:last-child { border-bottom: none; }
.search-item:hover .search-item-title { color: var(--primary, #4f46e5); }
.search-item-cat {
    font-size: .7rem; font-weight: 700; color: var(--primary, #4f46e5);
    text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px;
}
.search-item-title {
    font-size: 1.15rem; font-weight: 700; color: #0f0f23;
    margin-bottom: 8px; line-height: 1.4; word-break: keep-all;
    transition: color .15s;
}
.search-item-title mark,
.search-item-snippet mark {
    background: rgba(255,214,0,.4);
    color: inherit; border-radius: 2px; padding: 0 1px;
    font-weight: 700;
}
.search-item-snippet {
    font-size: .9rem; color: #6b7280; line-height: 1.75;
    margin-bottom: 10px;
}
.search-item-meta {
    font-size: .75rem; color: #9ca3af;
    display: flex; gap: 10px; align-items: center;
}
.search-item-dot { width: 3px; height: 3px; border-radius: 50%; background: #d1d5db; }

/* 빈 상태 */
.search-empty {
    text-align: center; padding: 60px 0;
    color: #9ca3af;
}
.search-empty .icon { font-size: 3rem; margin-bottom: 14px; opacity: .4; }
.search-empty p { font-size: 1rem; font-weight: 500; }
.search-empty small { font-size: .85rem; color: #cbd5e1; margin-top: 6px; display: block; }
</style>
@endpush

@section('content')

<section class="search-hero" aria-labelledby="search-heading">
    <h1 id="search-heading">
        @if($q)
            <span style="color:var(--primary)">"{{ $q }}"</span> 검색 결과
        @else
            🔍 검색
        @endif
    </h1>

    <form action="{{ route('search') }}" method="GET" role="search" class="search-form">
        <label for="search-input" class="sr-only">검색어</label>
        <input type="search" id="search-input" name="q"
               value="{{ $q }}"
               placeholder="검색어를 입력하세요..."
               class="search-input"
               autocomplete="off"
               autofocus>
        <button type="submit" class="search-btn" aria-label="검색">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </button>
    </form>

    @if($q)
        <p class="search-meta">
            <strong>{{ number_format($total) }}개</strong>의 결과를 찾았습니다.
        </p>
    @endif
</section>

{{-- 결과 목록 --}}
@if($q)
    @if($total > 0)
        <div class="search-results" role="list" aria-label="검색 결과">
            @foreach($posts as $post)
            <article role="listitem">
                <a href="{{ route('posts.show', $post->slug) }}" class="search-item">
                    <div class="search-item-cat">{{ $post->category }}</div>
                    <h2 class="search-item-title">
                        {!! preg_replace('/(' . preg_quote(e($q), '/') . ')/iu', '<mark>$1</mark>', e($post->title)) !!}
                    </h2>
                    <p class="search-item-snippet">
                        {!! \App\Http\Controllers\SearchController::snippet($post->excerpt ?: $post->content, $q) !!}
                    </p>
                    <div class="search-item-meta">
                        <time datetime="{{ $post->published_at?->toIso8601String() }}">
                            {{ $post->published_at?->format('Y.m.d') }}
                        </time>
                        <span class="search-item-dot" aria-hidden="true"></span>
                        <span>{{ $post->reading_time }}분 읽기</span>
                    </div>
                </a>
            </article>
            @endforeach
        </div>

        <nav class="pagination" aria-label="검색 결과 페이지">
            {{ $posts->links() }}
        </nav>

    @else
        <div class="search-empty" role="status">
            <div class="icon">🔍</div>
            <p>"{{ $q }}"에 대한 검색 결과가 없습니다.</p>
            <small>다른 키워드로 검색하거나 맞춤법을 확인해보세요.</small>
        </div>
    @endif

@else
    <div class="search-empty" role="status">
        <div class="icon">✍️</div>
        <p>검색어를 입력해주세요.</p>
        <small>제목, 본문, 카테고리에서 검색합니다.</small>
    </div>
@endif

@endsection
