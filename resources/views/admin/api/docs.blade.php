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
.method-get    { background:#dbeafe;color:#1d4ed8; }
.method-post   { background:#dcfce7;color:#15803d; }
.method-patch  { background:#fef9c3;color:#854d0e; }
.method-delete { background:#fee2e2;color:#dc2626; }
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

    {{-- ── GET /settings ── --}}
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="method-badge method-get">GET</span>
            <span class="endpoint-path">/api/settings</span>
            <span class="endpoint-desc">전체 사이트 설정 조회</span>
        </div>
        <div class="endpoint-body">
            <h4>Request</h4>
            <pre><span class="c"># curl</span>
curl -X GET {{ $baseUrl }}/settings \
  -H <span class="s">"Authorization: Bearer {token}"</span></pre>

            <h4 style="margin-top:16px">Response 200</h4>
            <pre>{
  <span class="k">"data"</span>: {
    <span class="k">"general"</span>: {
      <span class="k">"blog_name"</span>:      <span class="s">"DongPou Blog"</span>,
      <span class="k">"blog_tagline"</span>:   <span class="s">"개인 블로그"</span>,
      <span class="k">"footer_text"</span>:    <span class="s">"All rights reserved."</span>,
      <span class="k">"hero_title"</span>:     <span class="s">"최신 글"</span>,
      <span class="k">"hero_subtitle"</span>:  <span class="s">"다양한 주제의 글을 만나보세요."</span>,
      <span class="k">"posts_per_page"</span>: <span class="s">"9"</span>
    },
    <span class="k">"appearance"</span>: {
      <span class="k">"primary_color"</span>: <span class="s">"#4f46e5"</span>
    },
    <span class="k">"seo"</span>: {
      <span class="k">"blog_description"</span>:  <span class="s">"개인 블로그입니다."</span>,
      <span class="k">"meta_keywords"</span>:    <span class="s">"블로그,개발"</span>,
      <span class="k">"author_name"</span>:      <span class="s">"홍길동"</span>,
      <span class="k">"og_image_default"</span>: <span class="s">""</span>,
      <span class="k">"robots_index"</span>:     <span class="s">"index,follow"</span>,
      <span class="k">"google_analytics"</span>: <span class="s">"G-XXXXXXXXXX"</span>,
      <span class="k">"twitter_handle"</span>:   <span class="s">"@handle"</span>,
      <span class="k">"kakao_js_key"</span>:     <span class="s">""</span>,
      <span class="k">"head_code"</span>:        <span class="s">""</span>
    },
    <span class="k">"verification"</span>: {
      <span class="k">"google_site_verification"</span>: <span class="s">""</span>,
      <span class="k">"naver_site_verification"</span>:  <span class="s">""</span>
    }
  }
}</pre>
        </div>
    </div>

    {{-- ── PATCH /settings ── --}}
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="method-badge method-patch">PATCH</span>
            <span class="endpoint-path">/api/settings</span>
            <span class="endpoint-desc">사이트 설정 수정 (전달된 키만 업데이트)</span>
        </div>
        <div class="endpoint-body">
            <h4>Request Headers</h4>
            <pre>Authorization: Bearer {token}
Content-Type:  application/json</pre>

            <h4 style="margin-top:16px">Request Body — 허용 키 목록</h4>
            <table class="param-table">
                <thead><tr><th>키</th><th>그룹</th><th>설명</th></tr></thead>
                <tbody>
                    <tr><td><code>blog_name</code></td><td>general</td><td>블로그 이름</td></tr>
                    <tr><td><code>blog_tagline</code></td><td>general</td><td>블로그 부제</td></tr>
                    <tr><td><code>footer_text</code></td><td>general</td><td>푸터 텍스트</td></tr>
                    <tr><td><code>hero_title</code></td><td>general</td><td>메인 히어로 제목</td></tr>
                    <tr><td><code>hero_subtitle</code></td><td>general</td><td>메인 히어로 부제</td></tr>
                    <tr><td><code>posts_per_page</code></td><td>general</td><td>페이지당 글 수 (1~100)</td></tr>
                    <tr><td><code>primary_color</code></td><td>appearance</td><td>메인 색상 (예: #4f46e5)</td></tr>
                    <tr><td><code>blog_description</code></td><td>seo</td><td>블로그 설명 (meta description)</td></tr>
                    <tr><td><code>meta_keywords</code></td><td>seo</td><td>메타 키워드</td></tr>
                    <tr><td><code>author_name</code></td><td>seo</td><td>작성자 이름</td></tr>
                    <tr><td><code>og_image_default</code></td><td>seo</td><td>기본 OG 이미지 URL</td></tr>
                    <tr><td><code>robots_index</code></td><td>seo</td><td>robots 지시자 (예: index,follow)</td></tr>
                    <tr><td><code>google_analytics</code></td><td>seo</td><td>GA 측정 ID (G-XXXXXXXXXX)</td></tr>
                    <tr><td><code>twitter_handle</code></td><td>seo</td><td>트위터 핸들 (@handle)</td></tr>
                    <tr><td><code>kakao_js_key</code></td><td>seo</td><td>카카오 JS SDK 키</td></tr>
                    <tr><td><code>head_code</code></td><td>seo</td><td>&lt;head&gt;에 삽입할 커스텀 코드</td></tr>
                    <tr><td><code>google_site_verification</code></td><td>verification</td><td>구글 사이트 인증 코드</td></tr>
                    <tr><td><code>naver_site_verification</code></td><td>verification</td><td>네이버 사이트 인증 코드</td></tr>
                </tbody>
            </table>
            <p style="font-size:.78rem;color:#64748b;margin-top:8px">
                💡 전달하지 않은 키는 그대로 유지됩니다. 원하는 키만 포함하세요.
            </p>

            <h4 style="margin-top:16px">Request 예시</h4>
            <pre><span class="c"># curl — 전체 설정 한번에 변경</span>
curl -X PATCH {{ $baseUrl }}/settings \
  -H <span class="s">"Authorization: Bearer {token}"</span> \
  -H <span class="s">"Content-Type: application/json"</span> \
  -d '{
    <span class="k">"blog_name"</span>:                    <span class="s">"DongPou Blog"</span>,
    <span class="k">"blog_tagline"</span>:                 <span class="s">"개인 기술 블로그"</span>,
    <span class="k">"footer_text"</span>:                  <span class="s">"All rights reserved."</span>,
    <span class="k">"hero_title"</span>:                   <span class="s">"최신 글"</span>,
    <span class="k">"hero_subtitle"</span>:                <span class="s">"다양한 주제의 글을 만나보세요."</span>,
    <span class="k">"posts_per_page"</span>:               <span class="n">9</span>,
    <span class="k">"primary_color"</span>:               <span class="s">"#4f46e5"</span>,
    <span class="k">"blog_description"</span>:            <span class="s">"개발, 일상, 생각을 기록하는 블로그입니다."</span>,
    <span class="k">"meta_keywords"</span>:               <span class="s">"블로그,개발,파이썬,라라벨"</span>,
    <span class="k">"author_name"</span>:                 <span class="s">"박동주"</span>,
    <span class="k">"og_image_default"</span>:            <span class="s">"https://example.com/og-default.jpg"</span>,
    <span class="k">"robots_index"</span>:                <span class="s">"index,follow"</span>,
    <span class="k">"google_analytics"</span>:            <span class="s">"G-XXXXXXXXXX"</span>,
    <span class="k">"twitter_handle"</span>:              <span class="s">"@handle"</span>,
    <span class="k">"kakao_js_key"</span>:                <span class="s">"xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"</span>,
    <span class="k">"head_code"</span>:                   <span class="s">"&lt;meta name=\"theme-color\" content=\"#4f46e5\"&gt;"</span>,
    <span class="k">"google_site_verification"</span>:    <span class="s">"AbCdEfGhIjKlMnOpQrStUvWxYz123456"</span>,
    <span class="k">"naver_site_verification"</span>:     <span class="s">"abcdef1234567890abcdef1234567890"</span>
  }'

<span class="c"># Python — 전체 설정 한번에 변경</span>
import requests
res = requests.patch(
    <span class="s">"{{ $baseUrl }}/settings"</span>,
    headers={
        <span class="s">"Authorization"</span>: <span class="s">"Bearer {token}"</span>,
        <span class="s">"Content-Type"</span>:  <span class="s">"application/json"</span>,
    },
    json={
        <span class="c"># General</span>
        <span class="s">"blog_name"</span>:                 <span class="s">"DongPou Blog"</span>,
        <span class="s">"blog_tagline"</span>:              <span class="s">"개인 기술 블로그"</span>,
        <span class="s">"footer_text"</span>:               <span class="s">"All rights reserved."</span>,
        <span class="s">"hero_title"</span>:                <span class="s">"최신 글"</span>,
        <span class="s">"hero_subtitle"</span>:             <span class="s">"다양한 주제의 글을 만나보세요."</span>,
        <span class="s">"posts_per_page"</span>:            <span class="n">9</span>,
        <span class="c"># Appearance</span>
        <span class="s">"primary_color"</span>:            <span class="s">"#4f46e5"</span>,
        <span class="c"># SEO</span>
        <span class="s">"blog_description"</span>:         <span class="s">"개발, 일상, 생각을 기록하는 블로그입니다."</span>,
        <span class="s">"meta_keywords"</span>:            <span class="s">"블로그,개발,파이썬,라라벨"</span>,
        <span class="s">"author_name"</span>:              <span class="s">"박동주"</span>,
        <span class="s">"og_image_default"</span>:         <span class="s">"https://example.com/og-default.jpg"</span>,
        <span class="s">"robots_index"</span>:             <span class="s">"index,follow"</span>,
        <span class="s">"google_analytics"</span>:         <span class="s">"G-XXXXXXXXXX"</span>,
        <span class="s">"twitter_handle"</span>:           <span class="s">"@handle"</span>,
        <span class="s">"kakao_js_key"</span>:             <span class="s">"xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"</span>,
        <span class="s">"head_code"</span>:                <span class="s">""</span>,
        <span class="c"># Verification</span>
        <span class="s">"google_site_verification"</span>: <span class="s">"AbCdEfGhIjKlMnOpQrStUvWxYz123456"</span>,
        <span class="s">"naver_site_verification"</span>:  <span class="s">"abcdef1234567890abcdef1234567890"</span>,
    }
)</pre>

            <h4 style="margin-top:16px">Response 200</h4>
            <pre>{
  <span class="k">"message"</span>: <span class="s">"2개 설정이 업데이트되었습니다."</span>,
  <span class="k">"updated"</span>: [<span class="s">"blog_name"</span>, <span class="s">"google_analytics"</span>],
  <span class="k">"data"</span>: {
    <span class="c">// ... 업데이트 후 전체 설정 (GET /settings 응답과 동일 구조)</span>
  }
}</pre>

            <h4 style="margin-top:16px">Response 422 (허용되지 않은 키만 전달된 경우)</h4>
            <pre>{
  <span class="k">"message"</span>: <span class="s">"변경할 설정이 없습니다. 허용된 키를 확인하세요."</span>,
  <span class="k">"allowed_keys"</span>: [<span class="s">"blog_name"</span>, <span class="s">"blog_tagline"</span>, <span class="s">"..."</span>]
}</pre>
        </div>
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
      <span class="k">"id"</span>:          <span class="n">1</span>,
      <span class="k">"name"</span>:        <span class="s">"개발"</span>,
      <span class="k">"slug"</span>:        <span class="s">"개발"</span>,
      <span class="k">"post_count"</span>: <span class="n">12</span>
    },
    {
      <span class="k">"id"</span>:          <span class="n">2</span>,
      <span class="k">"name"</span>:        <span class="s">"일상"</span>,
      <span class="k">"slug"</span>:        <span class="s">"일상"</span>,
      <span class="k">"post_count"</span>: <span class="n">3</span>
    }
  ]
}</pre>
        </div>
    </div>

    {{-- ── POST /categories ── --}}
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="method-badge method-post">POST</span>
            <span class="endpoint-path">/api/categories</span>
            <span class="endpoint-desc">카테고리 생성</span>
        </div>
        <div class="endpoint-body">
            <h4>Request Headers</h4>
            <pre>Authorization: Bearer {token}
Content-Type:  application/json</pre>

            <h4 style="margin-top:16px">Request Body (JSON)</h4>
            <table class="param-table">
                <thead><tr><th>필드</th><th>타입</th><th>설명</th></tr></thead>
                <tbody>
                    <tr>
                        <td><code>name</code> <span class="required-badge">필수</span></td>
                        <td>string</td>
                        <td>카테고리 이름 (최대 100자, 중복 불가)</td>
                    </tr>
                    <tr>
                        <td><code>description</code> <span class="optional-badge">선택</span></td>
                        <td>string</td>
                        <td>카테고리 설명 (최대 500자)</td>
                    </tr>
                    <tr>
                        <td><code>sort_order</code> <span class="optional-badge">선택</span></td>
                        <td>integer</td>
                        <td>정렬 순서 (기본값: 0)</td>
                    </tr>
                </tbody>
            </table>

            <h4 style="margin-top:16px">Request 예시</h4>
            <pre><span class="c"># curl</span>
curl -X POST {{ $baseUrl }}/categories \
  -H <span class="s">"Authorization: Bearer {token}"</span> \
  -H <span class="s">"Content-Type: application/json"</span> \
  -d <span class="s">'{"name": "개발", "sort_order": 1}'</span>

<span class="c"># Python</span>
import requests
res = requests.post(
    <span class="s">"{{ $baseUrl }}/categories"</span>,
    headers={<span class="s">"Authorization"</span>: <span class="s">"Bearer {token}"</span>},
    json={<span class="s">"name"</span>: <span class="s">"개발"</span>, <span class="s">"sort_order"</span>: <span class="n">1</span>}
)</pre>

            <h4 style="margin-top:16px">Response 201</h4>
            <pre>{
  <span class="k">"message"</span>: <span class="s">"카테고리가 생성되었습니다."</span>,
  <span class="k">"data"</span>: {
    <span class="k">"id"</span>:   <span class="n">3</span>,
    <span class="k">"name"</span>: <span class="s">"개발"</span>,
    <span class="k">"slug"</span>: <span class="s">"개발"</span>
  }
}</pre>

            <h4 style="margin-top:16px">Response 422 (중복 또는 유효성 오류)</h4>
            <pre>{
  <span class="k">"message"</span>: <span class="s">"이미 존재하는 카테고리입니다."</span>,
  <span class="k">"errors"</span>: { <span class="k">"name"</span>: [<span class="s">"이미 존재하는 카테고리입니다."</span>] }
}</pre>
        </div>
    </div>

    {{-- ── DELETE /categories/{id} ── --}}
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="method-badge method-delete">DELETE</span>
            <span class="endpoint-path">/api/categories/{id}</span>
            <span class="endpoint-desc">카테고리 삭제</span>
        </div>
        <div class="endpoint-body">
            <h4>Request</h4>
            <pre><span class="c"># curl</span>
curl -X DELETE {{ $baseUrl }}/categories/3 \
  -H <span class="s">"Authorization: Bearer {token}"</span>

<span class="c"># Python</span>
import requests
res = requests.delete(
    <span class="s">"{{ $baseUrl }}/categories/3"</span>,
    headers={<span class="s">"Authorization"</span>: <span class="s">"Bearer {token}"</span>}
)</pre>

            <h4 style="margin-top:16px">Response 200</h4>
            <pre>{
  <span class="k">"message"</span>: <span class="s">"카테고리가 삭제되었습니다."</span>
}</pre>

            <h4 style="margin-top:16px">Response 422 (글이 존재하는 경우)</h4>
            <pre>{
  <span class="k">"message"</span>: <span class="s">"카테고리를 삭제할 수 없습니다. 해당 카테고리에 글이 5개 있습니다."</span>,
  <span class="k">"post_count"</span>: <span class="n">5</span>
}</pre>
        </div>
    </div>

    {{-- ── POST /images ── --}}
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="method-badge method-post">POST</span>
            <span class="endpoint-path">/api/images</span>
            <span class="endpoint-desc">이미지 업로드 → URL 반환</span>
        </div>
        <div class="endpoint-body">
            <h4>Request Headers</h4>
            <pre>Authorization: Bearer {token}
Content-Type:  multipart/form-data</pre>

            <h4 style="margin-top:16px">Request Body (form-data)</h4>
            <table class="param-table">
                <thead>
                    <tr><th>필드</th><th>타입</th><th>설명</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>image</code> <span class="required-badge">필수</span></td>
                        <td>file</td>
                        <td>이미지 파일 (JPG, PNG, GIF, WEBP / 최대 10MB)</td>
                    </tr>
                </tbody>
            </table>

            <h4 style="margin-top:16px">Request 예시</h4>
            <pre><span class="c"># curl (파일 업로드)</span>
curl -X POST {{ $baseUrl }}/images \
  -H <span class="s">"Authorization: Bearer {token}"</span> \
  -F <span class="s">"image=@/path/to/photo.jpg"</span></pre>

            <h4 style="margin-top:16px">Response 201</h4>
            <pre>{
  <span class="k">"data"</span>: {
    <span class="k">"url"</span>:      <span class="s">"http://43.200.236.216/storage/uploads/posts/2026/03/1234567890_abcdef12.jpg"</span>,
    <span class="k">"path"</span>:     <span class="s">"uploads/posts/2026/03/1234567890_abcdef12.jpg"</span>,
    <span class="k">"filename"</span>: <span class="s">"1234567890_abcdef12.jpg"</span>,
    <span class="k">"size"</span>:     <span class="n">204800</span>,
    <span class="k">"mime"</span>:     <span class="s">"image/jpeg"</span>
  },
  <span class="k">"message"</span>: <span class="s">"이미지가 업로드되었습니다."</span>
}</pre>

            <div style="margin-top:14px;padding:12px 16px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;font-size:.82rem;color:#92400e">
                💡 <strong>사용 방법:</strong>
                이미지를 먼저 업로드해서 받은 <code>url</code>을 글 본문 마크다운에 삽입하세요.<br>
                <code style="background:#fef3c7;padding:1px 5px;border-radius:3px">![이미지 설명](반환된 url)</code>
            </div>
        </div>
    </div>

    {{-- ── GET /posts ── --}}
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="method-badge method-get">GET</span>
            <span class="endpoint-path">/api/posts?status=publish&amp;per_page=1</span>
            <span class="endpoint-desc">워드프레스 유사 포스트 목록 조회 (최신순)</span>
        </div>
        <div class="endpoint-body">
            <h4>Query Parameters</h4>
            <table class="param-table">
                <thead>
                    <tr><th>파라미터</th><th>설명</th><th>기본값</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>status</code></td><td><code>publish</code> | <code>draft</code> | <code>future</code> | <code>any</code></td><td><code>publish</code></td></tr>
                    <tr><td><code>per_page</code></td><td>페이지당 개수 (1~50)</td><td><code>10</code></td></tr>
                    <tr><td><code>page</code></td><td>페이지 번호 (1부터 시작)</td><td><code>1</code></td></tr>
                    <tr><td><code>orderby</code></td><td><code>date</code> | <code>modified</code> | <code>id</code></td><td><code>date</code></td></tr>
                    <tr><td><code>order</code></td><td><code>desc</code> | <code>asc</code></td><td><code>desc</code></td></tr>
                </tbody>
            </table>

            <p style="font-size:.78rem;color:#64748b;margin-top:8px">
                💡 응답 헤더에 <code>X-WP-Total</code>, <code>X-WP-TotalPages</code>가 포함됩니다.
            </p>

            <h4 style="margin-top:16px">Request 예시</h4>
            <pre><span class="c"># 발행글 중 최신 1개</span>
curl -X GET "{{ $baseUrl }}/posts?status=publish&per_page=1" \
  -H <span class="s">"Authorization: Bearer {token}"</span>

<span class="c"># 2페이지, 페이지당 20개</span>
curl -X GET "{{ $baseUrl }}/posts?status=publish&per_page=20&page=2&orderby=date&order=desc" \
  -H <span class="s">"Authorization: Bearer {token}"</span></pre>

            <h4 style="margin-top:16px">Response 200 (일부)</h4>
            <pre>[
  {
    <span class="k">"id"</span>: <span class="n">498</span>,
    <span class="k">"date"</span>: <span class="s">"2026-03-18T11:18:21"</span>,
    <span class="k">"date_gmt"</span>: <span class="s">"2026-03-18T02:18:21"</span>,
    <span class="k">"slug"</span>: <span class="s">"%ED%8C%94-%EC%98%AC%EB%A6%B4%EB%95%8C-..."</span>,
    <span class="k">"status"</span>: <span class="s">"publish"</span>,
    <span class="k">"type"</span>: <span class="s">"post"</span>,
    <span class="k">"link"</span>: <span class="s">"{{ url('/posts') }}/..."</span>,
    <span class="k">"title"</span>: { <span class="k">"rendered"</span>: <span class="s">"글 제목"</span> },
    <span class="k">"content"</span>: { <span class="k">"rendered"</span>: <span class="s">"&lt;p&gt;...&lt;/p&gt;"</span>, <span class="k">"protected"</span>: <span class="n">false</span> },
    <span class="k">"excerpt"</span>: { <span class="k">"rendered"</span>: <span class="s">"&lt;p&gt;요약...&lt;/p&gt;"</span>, <span class="k">"protected"</span>: <span class="n">false</span> },
    <span class="k">"categories"</span>: [<span class="n">3</span>],
    <span class="k">"tags"</span>: [<span class="n">41</span>, <span class="n">42</span>]
  }
]</pre>
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
                        <td>본문 (마크다운 또는 HTML)</td>
                        <td>—</td>
                    </tr>
                    <tr>
                        <td><code>content_type</code> <span class="optional-badge">선택</span></td>
                        <td>enum</td>
                        <td>
                            <code>markdown</code> — 마크다운 형식<br>
                            <code>html</code> — HTML 태그 형식
                        </td>
                        <td><code>markdown</code></td>
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
                        <td><code>tags</code> <span class="optional-badge">선택</span></td>
                        <td>string</td>
                        <td>태그 목록 (쉼표로 구분. 예: <code>javascript,python,web</code>)</td>
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
            <pre><span class="c"># 마크다운으로 즉시 발행 + 태그 추가</span>
curl -X POST {{ $baseUrl }}/posts \
  -H <span class="s">"Authorization: Bearer {token}"</span> \
  -H <span class="s">"Content-Type: application/json"</span> \
  -d '{
    <span class="k">"title"</span>:        <span class="s">"FastAPI로 REST API 만들기"</span>,
    <span class="k">"content"</span>:      <span class="s">"## 소개\n\n마크다운 내용..."</span>,
    <span class="k">"content_type"</span>: <span class="s">"markdown"</span>,
    <span class="k">"category"</span>:     <span class="s">"개발"</span>,
    <span class="k">"excerpt"</span>:      <span class="s">"FastAPI 입문 튜토리얼"</span>,
    <span class="k">"tags"</span>:         <span class="s">"api,python,fastapi"</span>,
    <span class="k">"publish_type"</span>: <span class="s">"publish"</span>
  }'

<span class="c"># HTML로 발행</span>
curl -X POST {{ $baseUrl }}/posts \
  -H <span class="s">"Authorization: Bearer {token}"</span> \
  -H <span class="s">"Content-Type: application/json"</span> \
  -d '{
    <span class="k">"title"</span>:        <span class="s">"HTML 글 발행"</span>,
    <span class="k">"content"</span>:      <span class="s">"&lt;h2&gt;소개&lt;/h2&gt;&lt;p&gt;HTML 내용...&lt;/p&gt;"</span>,
    <span class="k">"content_type"</span>: <span class="s">"html"</span>,
    <span class="k">"category"</span>:     <span class="s">"개발"</span>,
    <span class="k">"tags"</span>:         <span class="s">"web,html"</span>,
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
    <span class="k">"tags"</span>:         <span class="s">"예약,테스트"</span>,
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

<span class="c"># 2. 이미지 업로드 → URL 획득</span>
with open(<span class="s">"/path/to/image.jpg"</span>, <span class="s">"rb"</span>) as f:
    img_resp = requests.post(
        f<span class="s">"{BASE_URL}/images"</span>,
        headers={<span class="s">"Authorization"</span>: f<span class="s">"Bearer {TOKEN}"</span>},  <span class="c"># Content-Type 은 자동 설정</span>
        files={<span class="s">"image"</span>: (<span class="s">"image.jpg"</span>, f, <span class="s">"image/jpeg"</span>)},
    )
image_url = img_resp.json()[<span class="s">"data"</span>][<span class="s">"url"</span>]
print(<span class="s">"업로드된 이미지 URL:"</span>, image_url)

<span class="c"># 3. 이미지 URL을 본문에 포함해 글 발행 (마크다운 + 태그)</span>
post_data = {
    <span class="s">"title"</span>:        <span class="s">"Python으로 글 자동 발행"</span>,
    <span class="s">"content"</span>:      f<span class="s">"## 내용\n\n![대표 이미지]({image_url})\n\n파이썬으로 작성한 글입니다."</span>,
    <span class="s">"content_type"</span>: <span class="s">"markdown"</span>,
    <span class="s">"category"</span>:     <span class="s">"개발"</span>,
    <span class="s">"excerpt"</span>:      <span class="s">"API를 활용한 자동 발행 예시"</span>,
    <span class="s">"tags"</span>:         <span class="s">"api,python,자동화"</span>,
    <span class="s">"publish_type"</span>: <span class="s">"publish"</span>,
}
response = requests.post(f<span class="s">"{BASE_URL}/posts"</span>, json=post_data, headers=HEADERS)
print(response.status_code, response.json())

<span class="c"># 4. HTML로 글 발행</span>
html_post = {
    <span class="s">"title"</span>:        <span class="s">"HTML 글 발행"</span>,
    <span class="s">"content"</span>:      <span class="s">"&lt;h2&gt;제목&lt;/h2&gt;&lt;p&gt;HTML 형식의 본문입니다.&lt;/p&gt;"</span>,
    <span class="s">"content_type"</span>: <span class="s">"html"</span>,
    <span class="s">"category"</span>:     <span class="s">"개발"</span>,
    <span class="s">"tags"</span>:         <span class="s">"html,web"</span>,
    <span class="s">"publish_type"</span>: <span class="s">"publish"</span>,
}
response = requests.post(f<span class="s">"{BASE_URL}/posts"</span>, json=html_post, headers=HEADERS)
print(response.status_code, response.json())</pre>
        </div>
    </div>

</div>
@endsection
