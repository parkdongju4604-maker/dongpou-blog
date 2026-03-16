@php
    use Illuminate\Support\Str;
    use App\Models\Setting;

    $authorName   = Setting::get('author_name', Setting::get('blog_name', config('app.name')));
    $ogImgDefault = Setting::get('og_image_default', '');
    $kakaoJsKey   = Setting::get('kakao_js_key', '');

    // 메타: 글 정보 기반 자동 생성 (전역 설정 fallback)
    $seoTitle   = $post->title;
    $excerpt    = $post->excerpt ?: Str::limit(strip_tags($post->content), 155);
    $ogImage    = $post->thumbnail ?: $ogImgDefault;
    $postUrl      = route('posts.show', $post->slug);
    $blogName     = Setting::get('blog_name', config('app.name'));
    $baseUrl      = url('/');

    // wordCount: 한국어 포함이므로 공백 제거 후 글자 수로 산정
    $plainText = strip_tags($post->content ?? '');
    $wordCount = mb_strlen(preg_replace('/\s+/', '', $plainText));

    $articleSchema = [
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        'headline'         => $seoTitle,
        'description'      => $excerpt,
        'datePublished'    => $post->published_at?->toIso8601String(),
        'dateModified'     => $post->updated_at->toIso8601String(),
        'wordCount'        => $wordCount,
        'author'           => ['@type' => 'Person', 'name' => $authorName],
        'publisher'        => [
            '@type' => 'Organization',
            'name'  => $blogName,
            'url'   => $baseUrl,
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => $ogImgDefault ?: ($baseUrl . '/favicon.ico'),
            ],
        ],
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $postUrl],
    ];
    if ($ogImage) {
        $articleSchema['image'] = [
            '@type'  => 'ImageObject',
            'url'    => $ogImage,
            'width'  => 1200,
            'height' => 630,
        ];
    }

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => $blogName, 'item' => $baseUrl],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $post->category, 'item' => route('posts.category', $post->category)],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => $postUrl],
        ],
    ];

    // 목차(TOC) 자동 생성
    preg_match_all('/<h([2-3])[^>]*>(.*?)<\/h[2-3]>/i', $post->rendered_content, $headings);
    $tocItems   = [];
    $usedIds    = [];
    foreach ($headings[1] as $i => $level) {
        $text  = strip_tags($headings[2][$i]);
        $slug  = Str::slug($text);
        // 한글 등 비ASCII → slug가 빈 문자열 → md5 앞 8자리 폴백
        $base  = $slug !== '' ? 'h-' . $slug : 'h-' . substr(md5($text), 0, 8);
        // 같은 id 중복 방지
        $id    = $base;
        $n     = 2;
        while (in_array($id, $usedIds)) { $id = $base . '-' . $n++; }
        $usedIds[] = $id;
        $tocItems[] = ['level' => (int)$level, 'text' => $text, 'id' => $id];
    }

    // 렌더링된 콘텐츠에 heading id 삽입 (TOC와 동일한 로직)
    $usedIds2 = [];
    $renderedContent = preg_replace_callback(
        '/<h([2-3])([^>]*)>(.*?)<\/h[2-3]>/i',
        function($m) use (&$usedIds2) {
            $text  = strip_tags($m[3]);
            $slug  = \Illuminate\Support\Str::slug($text);
            $base  = $slug !== '' ? 'h-' . $slug : 'h-' . substr(md5($text), 0, 8);
            $id    = $base;
            $n     = 2;
            while (in_array($id, $usedIds2)) { $id = $base . '-' . $n++; }
            $usedIds2[] = $id;
            return "<h{$m[1]} id=\"{$id}\"{$m[2]}>{$m[3]}</h{$m[1]}>";
        },
        $post->rendered_content
    );
@endphp

@extends('layouts.app')

@section('title', $seoTitle . ' | ' . $blogName)
@section('description', $excerpt)
@section('author', $authorName)
@section('canonical', $postUrl)
@section('og:type', 'article')
@section('og:title', $seoTitle)
@section('og:description', $excerpt)
@if($ogImage)
@section('og:image', $ogImage)
@endif

@push('jsonld')
<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@push('head')
<meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
<meta property="article:modified_time"  content="{{ $post->updated_at->toIso8601String() }}">
<meta property="article:section"        content="{{ $post->category }}">

{{-- Highlight.js (코드 블록 문법 하이라이팅) --}}
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
<style>
/* ── 수정일 배지 ── */
.updated-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .75rem; font-weight: 600; color: #d97706;
    background: #fffbeb; border: 1px solid #fde68a;
    padding: 2px 9px; border-radius: 20px;
    white-space: nowrap;
}

/* ── 모바일 목차 (960px 이하) ── */
.toc-mobile {
    display: none;
    margin: 0 0 32px;
    border: 1.5px solid var(--border, #e5e7eb);
    border-radius: 12px;
    overflow: hidden;
    background: #f9f9fc;
}
.toc-mobile-toggle {
    display: flex; align-items: center; gap: 8px;
    padding: 13px 16px;
    cursor: pointer;
    font-size: .875rem; font-weight: 700; color: #374151;
    list-style: none;
    user-select: none;
    transition: background .12s;
}
.toc-mobile-toggle::-webkit-details-marker { display: none; }
.toc-mobile-toggle:hover { background: #f0f0f8; }
.toc-mobile-icon { color: var(--primary, #4f46e5); flex-shrink: 0; }
.toc-mobile-count {
    margin-left: 4px; font-size: .7rem; font-weight: 600;
    color: #9ca3af; background: #e5e7eb;
    padding: 1px 7px; border-radius: 20px;
}
.toc-mobile-arrow { margin-left: auto; color: #9ca3af; transition: transform .2s; flex-shrink: 0; }
details[open].toc-mobile .toc-mobile-arrow { transform: rotate(180deg); }
.toc-mobile-nav { border-top: 1px solid var(--border, #e5e7eb); padding: 12px 0; }
.toc-mobile-nav ul { list-style: none; margin: 0; padding: 0; }
.toc-mobile-nav li { margin: 0; }
.toc-mobile-nav li a {
    display: block; padding: 6px 18px;
    font-size: .84rem; color: #4b5563;
    text-decoration: none; transition: color .12s, background .12s;
    border-left: 3px solid transparent; margin: 1px 10px; border-radius: 6px;
}
.toc-mobile-nav li a:hover,
.toc-mobile-nav li a.active { color: var(--primary, #4f46e5); background: var(--primary-light, #eef2ff); border-left-color: var(--primary, #4f46e5); font-weight: 600; }
.toc-mobile-nav li.toc-h3 a { padding-left: 30px; font-size: .8rem; color: #6b7280; }

@media (max-width: 960px) {
    .toc-mobile { display: block; }
}

/* ── 이전/다음 글 네비게이션 ── */
.post-nav {
    margin-top: 48px;
    padding-top: 32px;
    border-top: 1px solid var(--border, #e5e7eb);
}
.post-nav-inner {
    display: grid;
    grid-template-columns: 1fr 44px 1fr;
    gap: 12px;
    align-items: stretch;
}
.post-nav-item {
    display: flex; flex-direction: column; gap: 6px;
    padding: 16px 18px;
    border: 1.5px solid var(--border, #e5e7eb);
    border-radius: 12px;
    text-decoration: none;
    transition: border-color .15s, background .15s, transform .1s;
    background: #fff;
    min-width: 0;
}
.post-nav-item:hover { border-color: var(--primary, #4f46e5); background: var(--primary-light, #eef2ff); transform: translateY(-1px); }
.post-nav-empty { pointer-events: none; opacity: .4; background: #f9fafb; }
.post-nav-prev { text-align: left; }
.post-nav-next { text-align: right; }
.post-nav-label {
    display: flex; align-items: center; gap: 4px;
    font-size: .72rem; font-weight: 700; color: var(--primary, #4f46e5);
    text-transform: uppercase; letter-spacing: .06em;
}
.post-nav-next .post-nav-label { justify-content: flex-end; }
.post-nav-title {
    font-size: .875rem; font-weight: 600; color: #1e293b;
    line-height: 1.4;
    overflow: hidden; display: -webkit-box;
    -webkit-line-clamp: 2; -webkit-box-orient: vertical;
}
.post-nav-cat {
    font-size: .72rem; color: #9ca3af; font-weight: 500;
}
.post-nav-home {
    display: flex; align-items: center; justify-content: center;
    border: 1.5px solid var(--border, #e5e7eb);
    border-radius: 12px;
    color: #6b7280; text-decoration: none;
    transition: border-color .15s, color .15s, background .15s;
    background: #fff;
}
.post-nav-home:hover { border-color: var(--primary, #4f46e5); color: var(--primary, #4f46e5); background: var(--primary-light, #eef2ff); }

@media (max-width: 540px) {
    .post-nav-inner {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto auto;
    }
    .post-nav-home { display: none; }
}

/* ── 소셜 공유 버튼 ── */
.share-section {
    margin-top: 40px; padding: 28px 24px;
    background: #f9f9fc; border-radius: 16px;
    border: 1.5px solid var(--border, #e5e7eb);
    text-align: center;
}
.share-label {
    font-size: .82rem; font-weight: 600; color: #6b7280;
    margin-bottom: 16px; letter-spacing: .01em;
}
.share-buttons {
    display: flex; justify-content: center;
    gap: 10px; flex-wrap: wrap;
}
.share-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px; border-radius: 10px;
    font-size: .83rem; font-weight: 700;
    cursor: pointer; text-decoration: none;
    border: none; transition: all .15s;
    white-space: nowrap; line-height: 1;
}
.share-btn svg { flex-shrink: 0; }
.share-kakao  { background: #FEE500; color: #191919; }
.share-kakao:hover  { background: #f0d800; transform: translateY(-1px); }
.share-twitter { background: #000; color: #fff; }
.share-twitter:hover { background: #1a1a1a; transform: translateY(-1px); }
.share-facebook { background: #1877F2; color: #fff; }
.share-facebook:hover { background: #166fe5; transform: translateY(-1px); }
.share-copy { background: #f1f5f9; color: #374151; border: 1.5px solid #e2e8f0; }
.share-copy:hover { background: #e2e8f0; transform: translateY(-1px); }
.share-copy.copied { background: #dcfce7; color: #15803d; border-color: #86efac; }
@media (max-width: 480px) {
    .share-btn { padding: 8px 14px; font-size: .8rem; }
}

/* ── 태그 ── */
.post-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 12px; }
.post-tag {
    display: inline-block; padding: 4px 12px;
    background: var(--primary-light, #eef2ff); color: var(--primary, #4f46e5);
    border: 1px solid var(--primary-mid, #c7d2fe); border-radius: 20px;
    font-size: .78rem; font-weight: 600; text-decoration: none;
    transition: background .15s, color .15s;
}
.post-tag:hover { background: var(--primary, #4f46e5); color: #fff; }

/* ── 관련 글 ── */
.related-section { margin-top: 56px; padding-top: 40px; border-top: 1px solid var(--border, #e5e7eb); }
.related-title { font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 20px; letter-spacing: -.2px; }
.related-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
}
.related-card {
    border: 1.5px solid var(--border, #e5e7eb); border-radius: 12px;
    overflow: hidden; text-decoration: none; color: inherit;
    display: flex; flex-direction: column;
    transition: border-color .15s, box-shadow .15s, transform .15s;
    background: #fff;
}
.related-card:hover { border-color: var(--primary, #4f46e5); box-shadow: 0 4px 20px rgba(79,70,229,.1); transform: translateY(-2px); }
.related-thumb {
    width: 100%; aspect-ratio: 16/9; object-fit: cover;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: block;
}
.related-thumb-fallback {
    width: 100%; aspect-ratio: 16/9;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem;
}
.related-body { padding: 14px 16px; flex: 1; display: flex; flex-direction: column; gap: 6px; }
.related-cat {
    font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    color: var(--primary, #4f46e5);
}
.related-card-title {
    font-size: .9rem; font-weight: 700; color: #1e293b; line-height: 1.5;
    overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
}
.related-meta { font-size: .75rem; color: #94a3b8; margin-top: auto; padding-top: 6px; }
@media (max-width: 720px) {
    .related-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
}
@media (max-width: 480px) {
    .related-grid { grid-template-columns: 1fr; }
}

/* ── 댓글 섹션 ── */
.comments-section { margin-top: 48px; padding-top: 36px; border-top: 1px solid var(--border, #e5e7eb); }
.comments-title { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 28px; }
.comment-count { font-size: .85rem; font-weight: 500; color: #94a3b8; margin-left: 6px; }

/* 댓글 카드 */
.comment-item { display: flex; gap: 12px; margin-bottom: 20px; }
.comment-avatar {
    width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg, var(--primary, #4f46e5), #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; font-weight: 700; color: #fff;
}
.comment-body-wrap { flex: 1; min-width: 0; }
.comment-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap; }
.comment-author { font-weight: 700; font-size: .875rem; color: #1e293b; }
.comment-date { font-size: .76rem; color: #94a3b8; }
.comment-text { font-size: .9rem; line-height: 1.7; color: #374151; white-space: pre-wrap; word-break: break-word; }
.comment-actions { display: flex; gap: 10px; margin-top: 8px; }
.comment-action-btn {
    font-size: .75rem; color: #94a3b8; background: none; border: none;
    cursor: pointer; padding: 0; transition: color .12s;
}
.comment-action-btn:hover { color: var(--primary, #4f46e5); }

/* 대댓글 */
.replies-wrap { margin-top: 12px; padding-left: 16px; border-left: 2px solid var(--border, #e5e7eb); }

/* 답글 폼 (인라인) */
.reply-form-wrap {
    margin-top: 12px; background: #f9f9fc; border-radius: 10px;
    padding: 14px; display: none; border: 1.5px solid var(--border, #e5e7eb);
}
.reply-form-wrap.open { display: block; }

/* 삭제 폼 */
.delete-form-wrap {
    margin-top: 8px; background: #fff5f5; border-radius: 8px;
    padding: 10px 12px; display: none; border: 1px solid #fecaca;
}
.delete-form-wrap.open { display: block; }
.delete-form-wrap form { display: flex; gap: 8px; align-items: center; }
.delete-form-wrap input {
    flex: 1; padding: 6px 10px; border: 1px solid #fecaca; border-radius: 6px;
    font-size: .83rem; outline: none;
}
.delete-form-wrap button[type=submit] {
    padding: 6px 14px; background: #ef4444; color: #fff; border: none;
    border-radius: 6px; font-size: .8rem; font-weight: 600; cursor: pointer;
}
.delete-error { font-size: .78rem; color: #dc2626; margin-top: 4px; }

/* 댓글 작성 폼 */
.comment-form-section { margin-top: 36px; padding-top: 28px; border-top: 1px solid var(--border, #e5e7eb); }
.comment-form-title { font-size: .95rem; font-weight: 700; color: #1e293b; margin-bottom: 16px; }
.comment-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px; }
.comment-form-grid input,
.comment-form-section textarea,
.reply-form-wrap textarea {
    padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
    font-size: .875rem; outline: none; width: 100%; box-sizing: border-box;
    transition: border-color .12s; font-family: inherit; background: #fff;
}
.comment-form-grid input:focus,
.comment-form-section textarea:focus,
.reply-form-wrap textarea:focus { border-color: var(--primary, #4f46e5); outline: none; }
.comment-form-section textarea {
    display: block; resize: vertical; min-height: 110px;
    margin-bottom: 10px; line-height: 1.6;
}
.reply-form-wrap textarea {
    display: block; resize: vertical; min-height: 70px;
    margin-bottom: 8px; line-height: 1.6;
}
.comment-notice {
    padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: .875rem; font-weight: 500;
    background: #f0fdf4; color: #166534; border: 1px solid #86efac;
}
.comment-notice.warn { background: #fffbeb; color: #92400e; border-color: #fde68a; }

@media (max-width: 600px) {
    .comment-form-grid { grid-template-columns: 1fr; }
    .comment-form-section textarea { min-height: 90px; font-size: .875rem; }
}
</style>
<style>
/* sticky 헤더(64px) 높이만큼 앵커 스크롤 오프셋 확보 */
html { scroll-padding-top: 80px; }

/* ── 포스트 제목 영역 스크롤 처리 ── */
.post-hero {
    padding: 32px 0;
    margin: 0 0 32px 0;
    border-bottom: 1px solid var(--border, #e5e7eb);
    transition: all .2s ease;
    background: #fff;
}

.post-hero.scrolled {
    display: none;
}

.post-layout {
    position: relative;
}

/* hljs 자체 배경·패딩 제거 → 기존 pre 스타일 유지 */
.post-content pre code.hljs { background: transparent !important; padding: 0 !important; }

/* 언어 배지 + 복사 버튼 공통 컨테이너 */
.post-content pre { position: relative; }

.code-lang-badge,
.code-copy-btn {
    position: absolute;
    top: 10px; right: 14px;
    font-size: .68rem;
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    letter-spacing: .06em;
    border-radius: 5px;
    padding: 2px 9px;
    pointer-events: none;
    transition: opacity .15s;
}
.code-lang-badge {
    color: #6b7280;
    text-transform: uppercase;
    user-select: none;
    opacity: 1;
}
.code-copy-btn {
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.13);
    color: #9ca3af;
    cursor: pointer;
    pointer-events: auto;
    display: none;
}
/* 호버 시 배지 숨기고 복사 버튼 표시 */
.post-content pre:hover .code-lang-badge { display: none; }
.post-content pre:hover .code-copy-btn   { display: block; }
.code-copy-btn:hover   { background: rgba(255,255,255,.16); color: #e2e8f0; }
.code-copy-btn.copied  { color: #34d399 !important; border-color: #34d399 !important; }
</style>
@endpush

@section('content')

<div class="post-layout">

    {{-- ── 메인 컬럼 ── --}}
    <div>
        {{-- 브레드크럼 --}}
        <nav aria-label="브레드크럼">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">홈</a></li>
                <li class="breadcrumb-sep" aria-hidden="true">›</li>
                <li><a href="{{ route('posts.category', $post->category) }}">{{ $post->category }}</a></li>
                <li class="breadcrumb-sep" aria-hidden="true">›</li>
                <li style="color:#6b7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px" aria-current="page">{{ $post->title }}</li>
            </ol>
        </nav>

        {{-- 포스트 헤더 --}}
        <article itemscope itemtype="https://schema.org/Article">
            <meta itemprop="datePublished" content="{{ $post->published_at?->toIso8601String() }}">
            <meta itemprop="dateModified"  content="{{ $post->updated_at->toIso8601String() }}">
            <meta itemprop="author"        content="{{ $authorName }}">

            <header class="post-hero">
                <a href="{{ route('posts.category', $post->category) }}"
                   class="post-category-badge" itemprop="articleSection">
                    {{ $post->category }}
                </a>
                <h1 class="post-title" itemprop="headline">{{ $post->title }}</h1>
                @php
                    $isUpdated = $post->updated_at->diffInDays($post->published_at ?? $post->created_at) >= 1;
                @endphp
                <div class="post-meta">
                    <time datetime="{{ $post->published_at?->toIso8601String() }}">
                        {{ $post->published_at?->format('Y년 m월 d일') }}
                    </time>
                    @if($isUpdated)
                        <span class="post-meta-sep">·</span>
                        <span class="updated-badge" title="최종 수정: {{ $post->updated_at->format('Y.m.d') }}">
                            🔄 {{ $post->updated_at->format('Y.m.d') }} 업데이트
                        </span>
                    @endif
                    <span class="post-meta-sep">·</span>
                    <span class="post-meta-badge">읽기 {{ $post->reading_time }}분</span>
                    <span class="post-meta-sep">·</span>
                    <span class="post-meta-badge">👁 {{ number_format($post->view_count) }}</span>
                </div>
                @if($post->tags->isNotEmpty())
                <div class="post-tags">
                    @foreach($post->tags as $tag)
                    @if($tag->slug)
                    <a href="{{ route('tags.show', $tag->slug) }}" class="post-tag">#{{ $tag->name }}</a>
                    @else
                    <span class="post-tag">#{{ $tag->name }}</span>
                    @endif
                    @endforeach
                </div>
                @endif
            </header>

            {{-- 모바일 목차 (960px 이하에서만 표시) --}}
            @if(count($tocItems) >= 2)
            <details class="toc-mobile" id="toc-mobile">
                <summary class="toc-mobile-toggle">
                    <span class="toc-mobile-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/></svg>
                    </span>
                    <span>목차</span>
                    <span class="toc-mobile-count">{{ count($tocItems) }}개</span>
                    <svg class="toc-mobile-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </summary>
                <nav class="toc-mobile-nav" aria-label="목차">
                    <ul>
                        @foreach($tocItems as $item)
                        <li class="{{ $item['level'] === 3 ? 'toc-h3' : '' }}">
                            <a href="#{{ $item['id'] }}" onclick="document.getElementById('toc-mobile').removeAttribute('open')">
                                {{ $item['text'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </nav>
            </details>
            @endif

            {{-- 본문 --}}
            <div class="post-content" id="post-content" itemprop="articleBody">
                {!! $renderedContent !!}
            </div>
        </article>

        {{-- 관련 글 --}}
        @if($related->isNotEmpty())
        @php
            $fallbackGradients = [
                'linear-gradient(135deg,#667eea,#764ba2)',
                'linear-gradient(135deg,#f093fb,#f5576c)',
                'linear-gradient(135deg,#4facfe,#00f2fe)',
                'linear-gradient(135deg,#43e97b,#38f9d7)',
                'linear-gradient(135deg,#fa709a,#fee140)',
                'linear-gradient(135deg,#a18cd1,#fbc2eb)',
            ];
        @endphp
        <aside class="related-section" aria-label="관련 글">
            <h2 class="related-title">📚 관련 글</h2>
            <div class="related-grid">
                @foreach($related as $i => $r)
                <a href="{{ route('posts.show', $r->slug) }}" class="related-card">
                    @if($r->thumbnail)
                        <img class="related-thumb" src="{{ $r->thumbnail }}" alt="{{ $r->title }}" loading="lazy">
                    @else
                        <div class="related-thumb-fallback"
                             style="background:{{ $fallbackGradients[$i % count($fallbackGradients)] }}">
                        </div>
                    @endif
                    <div class="related-body">
                        <span class="related-cat">{{ $r->category }}</span>
                        <span class="related-card-title">{{ $r->title }}</span>
                        <span class="related-meta">
                            {{ $r->published_at?->format('Y.m.d') }}
                            · {{ $r->reading_time }}분 읽기
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </aside>
        @endif

        {{-- 이전 / 다음 글 네비게이션 --}}
        <nav class="post-nav" aria-label="이전/다음 글">
            <div class="post-nav-inner">
                @if($prevPost)
                <a href="{{ route('posts.show', $prevPost->slug) }}" class="post-nav-item post-nav-prev">
                    <span class="post-nav-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        이전 글
                    </span>
                    <span class="post-nav-title">{{ $prevPost->title }}</span>
                    <span class="post-nav-cat">{{ $prevPost->category }}</span>
                </a>
                @else
                <div class="post-nav-item post-nav-prev post-nav-empty">
                    <span class="post-nav-label">첫 번째 글입니다</span>
                </div>
                @endif

                <a href="{{ route('home') }}" class="post-nav-home" title="목록으로">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </a>

                @if($nextPost)
                <a href="{{ route('posts.show', $nextPost->slug) }}" class="post-nav-item post-nav-next">
                    <span class="post-nav-label">
                        다음 글
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                    <span class="post-nav-title">{{ $nextPost->title }}</span>
                    <span class="post-nav-cat">{{ $nextPost->category }}</span>
                </a>
                @else
                <div class="post-nav-item post-nav-next post-nav-empty">
                    <span class="post-nav-label">최신 글입니다</span>
                </div>
                @endif
            </div>
        </nav>

        {{-- 소셜 공유 버튼 --}}
        <div class="share-section">
            <p class="share-label">이 글이 도움이 됐다면 공유해보세요 🙌</p>
            <div class="share-buttons">

                @if($kakaoJsKey)
                <button type="button" id="kakao-share-btn" class="share-btn share-kakao">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3C6.477 3 2 6.477 2 10.8c0 2.7 1.6 5.08 4.02 6.54L5 21l4.67-2.43A11.8 11.8 0 0 0 12 18.6c5.523 0 10-3.478 10-7.8S17.523 3 12 3z"/>
                    </svg>
                    카카오톡
                </button>
                @endif

                <a href="https://twitter.com/intent/tweet?url={{ urlencode($postUrl) }}&text={{ urlencode($post->title) }}"
                   class="share-btn share-twitter" target="_blank" rel="noopener noreferrer">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                    X (Twitter)
                </a>

                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($postUrl) }}"
                   class="share-btn share-facebook" target="_blank" rel="noopener noreferrer">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Facebook
                </a>

                <button type="button" id="copy-link-btn" class="share-btn share-copy"
                        data-url="{{ $postUrl }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                    <span id="copy-link-text">링크 복사</span>
                </button>

            </div>
        </div>

        {{-- 댓글 섹션 --}}
        <section class="comments-section" id="comments">

            {{-- 알림 --}}
            @if(session('comment_notice'))
            <div class="comment-notice {{ str_contains(session('comment_notice'),'스팸') ? 'warn' : '' }}">
                {{ session('comment_notice') }}
            </div>
            @endif

            <h2 class="comments-title">
                댓글
                <span class="comment-count">{{ $comments->count() + $comments->sum(fn($c) => $c->replies->count()) }}개</span>
            </h2>

            {{-- 댓글 목록 --}}
            @forelse($comments as $comment)
            <div class="comment-item" id="comment-{{ $comment->id }}">
                <div class="comment-avatar">{{ mb_substr($comment->author_name, 0, 1) }}</div>
                <div class="comment-body-wrap">
                    <div class="comment-meta">
                        <span class="comment-author">{{ $comment->author_name }}</span>
                        <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="comment-text">{{ $comment->content }}</div>
                    <div class="comment-actions">
                        <button class="comment-action-btn" onclick="toggleReplyForm({{ $comment->id }})">💬 답글</button>
                        @if($comment->password_hash)
                        <button class="comment-action-btn" onclick="toggleDeleteForm('c{{ $comment->id }}')">🗑 삭제</button>
                        @endif
                    </div>

                    {{-- 삭제 폼 --}}
                    @if($comment->password_hash)
                    <div class="delete-form-wrap" id="del-c{{ $comment->id }}">
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                            @csrf @method('DELETE')
                            <input type="password" name="password" placeholder="비밀번호 입력" autocomplete="off">
                            <button type="submit">삭제</button>
                        </form>
                        @error('delete_' . $comment->id)
                        <p class="delete-error">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    {{-- 대댓글 목록 --}}
                    @if($comment->replies->isNotEmpty())
                    <div class="replies-wrap">
                        @foreach($comment->replies as $reply)
                        <div class="comment-item" id="comment-{{ $reply->id }}" style="margin-bottom:14px">
                            <div class="comment-avatar" style="width:30px;height:30px;font-size:.75rem">{{ mb_substr($reply->author_name, 0, 1) }}</div>
                            <div class="comment-body-wrap">
                                <div class="comment-meta">
                                    <span class="comment-author">{{ $reply->author_name }}</span>
                                    <span class="comment-date">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="comment-text">{{ $reply->content }}</div>
                                @if($reply->password_hash)
                                <div class="comment-actions">
                                    <button class="comment-action-btn" onclick="toggleDeleteForm('c{{ $reply->id }}')">🗑 삭제</button>
                                </div>
                                <div class="delete-form-wrap" id="del-c{{ $reply->id }}">
                                    <form action="{{ route('comments.destroy', $reply) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <input type="password" name="password" placeholder="비밀번호 입력" autocomplete="off">
                                        <button type="submit">삭제</button>
                                    </form>
                                    @error('delete_' . $reply->id)
                                    <p class="delete-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- 답글 작성 폼 --}}
                    <div class="reply-form-wrap" id="reply-form-{{ $comment->id }}">
                        <form action="{{ route('comments.store', $post) }}" method="POST">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">
                            <div class="comment-form-grid">
                                <input type="text"     name="author_name"  placeholder="이름 *" required maxlength="50">
                                <input type="password" name="password" placeholder="비밀번호 * (삭제 시 사용)" required minlength="4" maxlength="30">
                            </div>
                            <textarea name="content" placeholder="답글을 입력하세요..." required minlength="2" maxlength="2000"></textarea>
                            <div style="display:flex;gap:8px;justify-content:flex-end">
                                <button type="button" class="btn btn-secondary btn-sm"
                                        onclick="toggleReplyForm({{ $comment->id }})">취소</button>
                                <button type="submit" class="btn btn-primary btn-sm">답글 달기</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p style="color:#94a3b8;font-size:.9rem;text-align:center;padding:20px 0 28px">
                첫 번째 댓글을 남겨보세요! 👋
            </p>
            @endforelse

            {{-- 댓글 작성 폼 --}}
            <div class="comment-form-section" id="comment-form">
                <h3 class="comment-form-title">✏️ 댓글 작성</h3>
                <form action="{{ route('comments.store', $post) }}" method="POST">
                    @csrf
                    {{-- 허니팟 (숨김) --}}
                    <input type="text" name="website" style="display:none;position:absolute" tabindex="-1" autocomplete="off">
                    <div class="comment-form-grid">
                        <div>
                            <input type="text" name="author_name" placeholder="이름 *" required maxlength="50"
                                   value="{{ old('author_name') }}">
                            @error('author_name')<p style="font-size:.73rem;color:#ef4444;margin-top:3px">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <input type="password" name="password" placeholder="비밀번호 * (삭제 시 사용)" required minlength="4" maxlength="30">
                            @error('password')<p style="font-size:.73rem;color:#ef4444;margin-top:3px">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <textarea name="content" placeholder="댓글을 입력하세요... (2000자 이내)"
                              required minlength="2" maxlength="2000">{{ old('content') }}</textarea>
                    @error('content')<p style="font-size:.73rem;color:#ef4444;margin-top:-6px;margin-bottom:8px">{{ $message }}</p>@enderror
                    <div style="display:flex;justify-content:flex-end">
                        <button type="submit" class="btn btn-primary">댓글 달기</button>
                    </div>
                </form>
            </div>

        </section>

        {{-- 하단 --}}
        <footer class="post-footer">
            <a href="{{ route('home') }}" class="btn btn-secondary">← 목록으로</a>
        </footer>
    </div>

    {{-- ── 사이드바: 목차 ── --}}
    @if(count($tocItems) >= 2)
    <aside class="post-sidebar">
        <div class="toc-box">
            <h2 class="toc-title" id="toc-label">목차</h2>
            <ul class="toc-list" aria-labelledby="toc-label">
                @foreach($tocItems as $item)
                <li class="{{ $item['level'] === 3 ? 'toc-h3' : '' }}">
                    <a href="#{{ $item['id'] }}">{{ $item['text'] }}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </aside>
    @endif

</div>

@endsection

@push('scripts')
{{-- 소셜 공유 버튼 JS --}}
@if($kakaoJsKey)
<script src="https://t1.kakaocdn.net/kakao_js_sdk/2.7.2/kakao.min.js" crossorigin="anonymous"></script>
<script>
Kakao.init('{{ $kakaoJsKey }}');
document.getElementById('kakao-share-btn')?.addEventListener('click', function () {
    Kakao.Share.sendDefault({
        objectType: 'feed',
        content: {
            title:       '{{ addslashes($post->title) }}',
            description: '{{ addslashes($excerpt) }}',
            @if($ogImage)
            imageUrl: '{{ $ogImage }}',
            @elseif($post->thumbnail)
            imageUrl: '{{ url($post->thumbnail) }}',
            @endif
            link: { mobileWebUrl: '{{ $postUrl }}', webUrl: '{{ $postUrl }}' },
        },
    });
});
</script>
@endif

<script>
// 답글 폼 토글
function toggleReplyForm(id) {
    const el = document.getElementById('reply-form-' + id);
    if (!el) return;
    el.classList.toggle('open');
    if (el.classList.contains('open')) {
        el.querySelector('input[name="author_name"]')?.focus();
    }
}

// 삭제 폼 토글
function toggleDeleteForm(id) {
    const el = document.getElementById('del-' + id);
    if (!el) return;
    el.classList.toggle('open');
    if (el.classList.contains('open')) {
        el.querySelector('input[type="password"]')?.focus();
    }
}

// 링크 복사
document.getElementById('copy-link-btn')?.addEventListener('click', function () {
    const url  = this.dataset.url;
    const btn  = this;
    const text = document.getElementById('copy-link-text');

    (navigator.clipboard ? navigator.clipboard.writeText(url) : Promise.reject())
        .catch(function () {
            // fallback
            const ta = document.createElement('textarea');
            ta.value = url; ta.style.cssText = 'position:fixed;opacity:0';
            document.body.appendChild(ta); ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
        })
        .finally(function () {
            btn.classList.add('copied');
            text.textContent = '✓ 복사됨';
            setTimeout(function () {
                btn.classList.remove('copied');
                text.textContent = '링크 복사';
            }, 2000);
        });
});
</script>

{{-- Highlight.js 로드 및 초기화 --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
(function () {
    document.querySelectorAll('.post-content pre code').forEach(function (block) {
        // 1. 문법 하이라이팅
        hljs.highlightElement(block);

        const pre  = block.parentElement;
        const lang = (block.className.match(/language-(\w+)/) || [])[1] || '';

        // 2. 언어 배지
        if (lang) {
            const badge = document.createElement('span');
            badge.className   = 'code-lang-badge';
            badge.textContent = lang;
            pre.appendChild(badge);
        }

        // 3. 복사 버튼
        const btn = document.createElement('button');
        btn.className   = 'code-copy-btn';
        btn.textContent = '복사';
        btn.addEventListener('click', function () {
            const text = block.innerText;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function () {
                    btn.textContent = '✓ 복사됨';
                    btn.classList.add('copied');
                    setTimeout(function () {
                        btn.textContent = '복사';
                        btn.classList.remove('copied');
                    }, 1800);
                });
            } else {
                // fallback (older browsers)
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.opacity  = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                btn.textContent = '✓ 복사됨';
                btn.classList.add('copied');
                setTimeout(function () {
                    btn.textContent = '복사';
                    btn.classList.remove('copied');
                }, 1800);
            }
        });
        pre.appendChild(btn);
    });
})();
</script>

<script>
// 포스트 제목 스크롤 처리 (스크롤 시 숨기기)
(function(){
    const postHero = document.querySelector('.post-hero');
    if (!postHero) return;
    
    const scrollThreshold = 150; // 150px 이상 스크롤하면 숨김
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > scrollThreshold) {
            postHero.classList.add('scrolled');
        } else {
            postHero.classList.remove('scrolled');
        }
    }, { passive: true });
})();

// 목차 활성화 (데스크탑 사이드바 + 모바일 TOC 공통)
(function(){
    // 데스크탑 + 모바일 TOC 링크 모두 선택
    const tocLinks = document.querySelectorAll('.toc-list a, .toc-mobile-nav a');
    if (!tocLinks.length) return;

    const headings = Array.from(document.querySelectorAll('.post-content h2, .post-content h3'));
    if (!headings.length) return;

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                tocLinks.forEach(a => a.classList.remove('active'));
                const id = entry.target.id;
                document.querySelectorAll('.toc-list a[href="#' + id + '"], .toc-mobile-nav a[href="#' + id + '"]')
                    .forEach(a => a.classList.add('active'));
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    headings.forEach(h => observer.observe(h));
})();
</script>
@endpush
