@extends('layouts.admin')
@section('title', 'API 문서')
@section('page-title', 'API 문서')

@section('content')

<style>
.api-doc .endpoint-card {
    background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;margin-bottom:20px;overflow:hidden;
}
.api-doc .endpoint-header {
    display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid #f1f5f9;
}
.method-badge {
    font-size:.72rem;font-weight:800;padding:4px 10px;border-radius:6px;letter-spacing:.05em;
    font-family:monospace;flex-shrink:0;
}
.method-get  { background:#dbeafe;color:#1d4ed8; }
.method-post { background:#dcfce7;color:#15803d; }
.endpoint-path {
    font-family:monospace;font-size:.9rem;font-weight:600;color:#0f172a;
}
.endpoint-desc { color:#64748b;font-size:.85rem;margin-left:auto; }
.api-doc .endpoint-body { padding:20px; }
.api-doc h4 {
    font-size:.72rem;font-weight:700;color:#64748b;text-transform:uppercase;
    letter-spacing:.05em;margin-bottom:10px;margin-top:16px;
}
.api-doc h4:first-child { margin-top:0; }
.api-doc pre {
    background:#0f172a;color:#e2e8f0;border-radius:10px;padding:16px 18px;
    font-size:.8rem;font-family:'JetBrains Mono','Fira Code',monospace;
    line-height:1.7;overflow-x:auto;margin-bottom:0;
}
.api-doc pre .c  { color:#64748b; }      /* comment */
.api-doc pre .k  { color:#818cf8; }      /* key */
.api-doc pre .s  { color:#86efac; }      /* string */
.api-doc pre .n  { color:#fbbf24; }      /* number/bool */
.api-doc .param-table { width:100%;border-collapse:collapse;font-size:.84rem; }
.api-doc .param-table th { background:#f8fafc;padding:8px 14px;text-align:left;font-weight:700;color:#374151;border:1px solid #e5e7eb; }
.api-doc .param-table td { padding:8px 14px;border:1px solid #e5e7eb;color:#374151;vertical-align:top; }
.api-doc .param-table td code { background:#f1f5f9;color:#4f46e5;padding:1px 6px;border-radius:4px;font-size:.8rem; }
.required-badge { background:#fee2e2;color:#dc2626;font-size:.68rem;padding:1px 7px;border-radius:4px;font-weight:700;margin-left:6px; }
.optional-badge { background:#f1f5f9;color:#64748b;font-size:.68rem;padding:1px 7px;border-radius:4px;font-weight:700;margin-left:6px; }
.auth-info {
    background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:10px;padding:16px 20px;margin-bottom:24px;
}
.base-url-bar {
    background:#0f172a;color:#7dd3fc;padding:12px 18px;border-radius:10px;
    font-family:monospace;font-size:.9rem;margin-bottom:24px;
    display:flex;align-items:center;gap:12px;
}
</style>

<div class="api-doc">

    {{-- 베이스 URL --}}
    <div class="base-url-bar">
        <span style="color:#64748b;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em">BASE URL</span>
        <span>{{ $baseUrl }}</span>
    </div>

    {{-- 인증 안내 --}}
    <div class="auth-info">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
            <span style="font-size:1rem">🔐</span>
            <strong style="font-size:.9rem;color:#1e40af">인증 방식: Bearer Token</strong>
        </div>
        <p style="font-size:.84rem;color:#1e40af;margin-bottom:10px">
            모든 API 요청에 <code style="background:#dbeafe;padding:1px 6px;border-radius:4px">Authorization</code> 헤더를 포함해야 합니다.
        </p>
        <pre style="background:#1e3a5f;color:#93c5fd;padding:12px 16px;border-radius:8px;font-size:.8rem;margin:0">Authorization: Bearer dp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</pre>
        <p style="font-size:.78rem;color:#3b82f6;margin-top:10px">
            토큰은 <a href="{{ route('admin.api-tokens.index') }}" style="color:#2563eb;font-weight:600">토큰 관리</a> 페이지에서 발급받으세요.
        </p>
    </div>

    {{-- ── GET /categories ── --}}
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="method-badge method-get">GET</span>
            <span class="endpoint-path">/api/categories</span>
            <span class="endpoint-desc">카테고리 목록 조회</span>
        </div>
        <div class="endpoint-body">
            <h4>Request</h4>
            <pre><span class="c"># 요청 예시</span>
curl -X GET {{ $baseUrl }}/categories \
  -H <span class="s">"Authorization: Bearer {token}"</span></pre>

            <h4 style="margin-top:16px">Response 200</h4>
            <pre>{
  <span class="k">"data"</span>: [
    {
      <span class="k">"id"</span>:   <span class="n">1</span>,
      <span class="k">"name"</span>: <span class="s">"개발"</span>,
      <span class="k">"slug"</span>: <span class="s">"개발"</span>
    },
    {
      <span class="k">"id"</span>:   <span class="n">2</span>,
      <span class="k">"name"</span>: <span class="s">"일상"</span>,
      <span class="k">"slug"</span>: <span class="s">"일상"</span>
    }
  ]
}</pre>
        </div>
    </div>

    {{-- ── POST /posts ── --}}
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="method-badge method-post">POST</span>
            <span class="endpoint-path">/api/posts</span>
            <span class="endpoint-desc">글 발행 / 예약 / 임시저장</span>
        </div>
        <div class="endpoint-body">
            <h4>Request Headers</h4>
            <pre>Authorization: Bearer {token}
Content-Type:  application/json</pre>

            <h4 style="margin-top:16px">Request Body</h4>
            <table class="param-table">
                <thead>
                    <tr>
                        <th>필드</th><th>타입</th><th>설명</th><th>기본값</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>title</code> <span class="required-badge">필수</span></td>
                        <td>string</td>
                        <td>글 제목 (최대 255자)</td>
                        <td>—</td>
                    </tr>
                    <tr>
                        <td><code>content</code> <span class="required-badge">필수</span></td>
                        <td>string</td>
                        <td>본문 (마크다운 형식)</td>
                        <td>—</td>
                    </tr>
                    <tr>
                        <td><code>category</code> <span class="required-badge">필수</span></td>
                        <td>string</td>
                        <td>카테고리 이름 (<code>GET /categories</code> 로 목록 조회)</td>
                        <td>—</td>
                    </tr>
                    <tr>
                        <td><code>excerpt</code> <span class="optional-badge">선택</span></td>
                        <td>string</td>
                        <td>목록 카드 요약문 (최대 500자)</td>
                        <td>null</td>
                    </tr>
                    <tr>
                        <td><code>publish_type</code> <span class="optional-badge">선택</span></td>
                        <td>enum</td>
                        <td>
                            <code>publish</code> — 즉시 발행<br>
                            <code>draft</code> — 임시저장<br>
                            <code>schedule</code> — 예약 발행
                        </td>
                        <td><code>publish</code></td>
                    </tr>
                    <tr>
                        <td><code>scheduled_at</code> <span class="optional-badge">선택</span></td>
                        <td>datetime</td>
                        <td><code>publish_type=schedule</code> 일 때 필수.<br>ISO 8601 형식 (<code>2026-03-10T09:00:00</code>)</td>
                        <td>null</td>
                    </tr>
                </tbody>
            </table>

            <h4 style="margin-top:16px">Request 예시</h4>
            <pre><span class="c"># 즉시 발행</span>
curl -X POST {{ $baseUrl }}/posts \
  -H <span class="s">"Authorization: Bearer {token}"</span> \
  -H <span class="s">"Content-Type: application/json"</span> \
  -d '{
    <span class="k">"title"</span>:        <span class="s">"FastAPI로 REST API 만들기"</span>,
    <span class="k">"content"</span>:      <span class="s">"## 소개\n\n마크다운 내용..."</span>,
    <span class="k">"category"</span>:     <span class="s">"개발"</span>,
    <span class="k">"excerpt"</span>:      <span class="s">"FastAPI 입문 튜토리얼"</span>,
    <span class="k">"publish_type"</span>: <span class="s">"publish"</span>
  }'

<span class="c"># 예약 발행</span>
curl -X POST {{ $baseUrl }}/posts \
  -H <span class="s">"Authorization: Bearer {token}"</span> \
  -H <span class="s">"Content-Type: application/json"</span> \
  -d '{
    <span class="k">"title"</span>:        <span class="s">"예약 발행 테스트"</span>,
    <span class="k">"content"</span>:      <span class="s">"내용..."</span>,
    <span class="k">"category"</span>:     <span class="s">"일상"</span>,
    <span class="k">"publish_type"</span>: <span class="s">"schedule"</span>,
    <span class="k">"scheduled_at"</span>: <span class="s">"2026-03-10T09:00:00"</span>
  }'</pre>

            <h4 style="margin-top:16px">Response 201 (성공)</h4>
            <pre>{
  <span class="k">"data"</span>: {
    <span class="k">"id"</span>:           <span class="n">42</span>,
    <span class="k">"title"</span>:        <span class="s">"FastAPI로 REST API 만들기"</span>,
    <span class="k">"slug"</span>:         <span class="s">"fastapi로-rest-api-만들기"</span>,
    <span class="k">"url"</span>:          <span class="s">"{{ url('/posts') }}/fastapi로-rest-api-만들기"</span>,
    <span class="k">"category"</span>:     <span class="s">"개발"</span>,
    <span class="k">"status"</span>:       <span class="s">"published"</span>,
    <span class="k">"published_at"</span>: <span class="s">"2026-03-09T19:30:00+09:00"</span>,
    <span class="k">"created_at"</span>:   <span class="s">"2026-03-09T19:30:00+09:00"</span>
  },
  <span class="k">"message"</span>: <span class="s">"글이 성공적으로 등록되었습니다."</span>
}</pre>

            <h4 style="margin-top:16px">Error Responses</h4>
            <pre><span class="c"># 401 — 토큰 없음 / 유효하지 않음</span>
{ <span class="k">"error"</span>: <span class="s">"Unauthorized"</span>, <span class="k">"message"</span>: <span class="s">"유효하지 않거나 만료된 토큰입니다."</span> }

<span class="c"># 422 — 입력값 오류</span>
{
  <span class="k">"message"</span>: <span class="s">"title은 필수입니다."</span>,
  <span class="k">"errors"</span>: { <span class="k">"title"</span>: [<span class="s">"title은 필수입니다."</span>] }
}</pre>
        </div>
    </div>

    {{-- Python 예시 --}}
    <div class="card" style="margin-bottom:20px">
        <div class="card-header"><h3>Python 사용 예시</h3></div>
        <div class="card-body" style="padding:0">
            <pre style="margin:0;border-radius:0 0 10px 10px;padding:20px"><span class="c">import requests

</span>BASE_URL = <span class="s">"{{ $baseUrl }}"</span>
TOKEN    = <span class="s">"dp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"</span>
HEADERS  = {
    <span class="s">"Authorization"</span>: f<span class="s">"Bearer {TOKEN}"</span>,
    <span class="s">"Content-Type"</span>:  <span class="s">"application/json"</span>,
}

<span class="c"># 1. 카테고리 목록 조회</span>
categories = requests.get(f<span class="s">"{BASE_URL}/categories"</span>, headers=HEADERS).json()
print(categories)

<span class="c"># 2. 글 발행</span>
post_data = {
    <span class="s">"title"</span>:        <span class="s">"Python으로 글 자동 발행"</span>,
    <span class="s">"content"</span>:      <span class="s">"## 내용\n\n파이썬으로 작성한 글입니다."</span>,
    <span class="s">"category"</span>:     <span class="s">"개발"</span>,
    <span class="s">"excerpt"</span>:      <span class="s">"API를 활용한 자동 발행 예시"</span>,
    <span class="s">"publish_type"</span>: <span class="s">"publish"</span>,
}
response = requests.post(f<span class="s">"{BASE_URL}/posts"</span>, json=post_data, headers=HEADERS)
print(response.status_code, response.json())</pre>
        </div>
    </div>

</div>
@endsection
