@if($errors->any())
<div class="alert alert-danger">
    <ul style="list-style:none">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
</div>
@endif

{{-- Toast UI Editor CSS --}}
<link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css">

<style>
/* ── 에디터 래퍼 ── */
.editor-wrap {
    border: 1.5px solid #e2e8f0; border-radius: 8px; overflow: hidden;
    position: relative; transition: border-color .15s;
}
.editor-wrap:focus-within { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
.toastui-editor-defaultUI { border: none !important; }
.toastui-editor-toolbar { background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important; }
.toastui-editor-mode-switch { background: #f8fafc !important; border-top: 1px solid #e2e8f0 !important; }
.toastui-editor-defaultUI .ProseMirror {
    font-size: 1rem !important; line-height: 1.8 !important;
    font-family: 'Noto Sans KR', -apple-system, sans-serif !important;
}
.toastui-editor-contents p { margin-bottom: .8em; }

/* ── 높이 조절 핸들 ── */
.editor-resize-handle {
    height: 10px; background: #f1f5f9; cursor: ns-resize;
    display: flex; align-items: center; justify-content: center;
    border-top: 1px solid #e2e8f0; user-select: none;
    transition: background .15s;
}
.editor-resize-handle:hover { background: #e2e8f0; }
.editor-resize-handle::before {
    content: ''; display: block; width: 36px; height: 3px;
    background: #cbd5e1; border-radius: 2px;
}

/* ── 이미지 삽입 다이얼로그 ── */
.img-dialog-overlay {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.45); align-items: center; justify-content: center;
}
.img-dialog-overlay.open { display: flex; }
.img-dialog {
    background: #fff; border-radius: 14px; padding: 28px;
    width: min(460px, 92vw); box-shadow: 0 20px 60px rgba(0,0,0,.2);
    animation: dialogIn .18s ease;
}
@keyframes dialogIn { from { transform: scale(.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.img-dialog h3 { font-size: 1rem; font-weight: 700; margin-bottom: 20px; color: #0f172a; }
.img-dialog-preview {
    width: 100%; max-height: 180px; object-fit: contain;
    border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;
    margin-bottom: 18px; display: none;
}
.img-dialog label { display: block; font-size: .8rem; font-weight: 600; color: #374151; margin-bottom: 5px; }
.img-dialog input[type=text],
.img-dialog input[type=number] {
    width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0;
    border-radius: 8px; font-size: .9rem; margin-bottom: 14px;
    outline: none; transition: border .15s;
}
.img-dialog input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
.img-dialog .hint { font-size: .75rem; color: #94a3b8; margin-top: -10px; margin-bottom: 14px; }
.img-dialog-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 4px; }
.img-dialog-actions button {
    padding: 9px 20px; border-radius: 8px; font-size: .875rem;
    font-weight: 600; cursor: pointer; border: none;
}
.btn-insert { background: #4f46e5; color: #fff; }
.btn-insert:hover { background: #4338ca; }
.btn-cancel-dialog { background: #f1f5f9; color: #475569; }
.btn-cancel-dialog:hover { background: #e2e8f0; }
</style>

{{-- 이미지 삽입 다이얼로그 --}}
<div class="img-dialog-overlay" id="img-dialog-overlay">
    <div class="img-dialog">
        <h3>🖼 이미지 설정</h3>
        <img id="img-dialog-preview" class="img-dialog-preview" alt="미리보기">
        <div>
            <label for="img-alt-input">대체 텍스트 (Alt)</label>
            <input type="text" id="img-alt-input" placeholder="이미지 내용을 설명하세요 (SEO, 접근성)">
        </div>
        <div>
            <label for="img-width-input">너비 (px)</label>
            <input type="number" id="img-width-input" placeholder="비워두면 원본 크기" min="50" max="2000" step="10">
            <p class="hint">예: 700 입력 시 → 너비 700px로 표시</p>
        </div>
        <div class="img-dialog-actions">
            <button class="btn-cancel-dialog" id="img-dialog-cancel">취소</button>
            <button class="btn-insert" id="img-dialog-confirm">삽입</button>
        </div>
    </div>
</div>

{{-- 폼 레이아웃 --}}
<div style="display:grid;grid-template-columns:1fr 240px;gap:20px;align-items:start">
    <div>
        {{-- 제목 --}}
        <div class="form-group">
            <label class="form-label">제목 *</label>
            <input type="text" name="title" id="post-title" class="form-control"
                   value="{{ old('title', $post->title ?? '') }}"
                   placeholder="글 제목을 입력하세요" required
                   style="font-size:1.05rem;font-weight:600">
        </div>

        {{-- 에디터 --}}
        <div class="form-group">
            <label class="form-label" style="display:flex;align-items:center;justify-content:space-between">
                <span>내용 *</span>
                <span style="font-size:.72rem;color:#94a3b8;font-weight:400">
                    이미지 드래그&드롭 · 클립보드 붙여넣기 지원
                </span>
            </label>
            <textarea name="content" id="content-hidden" style="display:none">{{ old('content', $post->content ?? '') }}</textarea>
            <div class="editor-wrap" id="editor-wrap">
                <div id="toast-editor"></div>
                <div class="editor-resize-handle" id="editor-resize-handle" title="드래그해서 높이 조절"></div>
            </div>
            <div style="font-size:.73rem;color:#94a3b8;margin-top:5px;text-align:right">
                아래 경계선을 드래그해서 에디터 높이를 조절할 수 있습니다
            </div>
        </div>

        {{-- 요약 --}}
        <div class="form-group">
            <label class="form-label">
                요약
                <span style="font-weight:400;color:#94a3b8;font-size:.75rem;margin-left:4px">목록 카드에 표시됩니다 (선택)</span>
            </label>
            <input type="text" name="excerpt" class="form-control"
                   value="{{ old('excerpt', $post->excerpt ?? '') }}"
                   placeholder="글의 핵심 내용을 한 줄로 요약하세요">
        </div>
    </div>

    {{-- 사이드 패널 --}}
    <div style="position:sticky;top:80px;display:flex;flex-direction:column;gap:12px">
        <div class="card">
            <div class="card-header" style="padding:12px 16px"><h3 style="font-size:.875rem">발행 설정</h3></div>
            <div class="card-body" style="padding:14px 16px">
                @php
                    $currentType = old('publish_type',
                        isset($post)
                            ? ($post->status === 'scheduled' ? 'schedule' : ($post->published ? 'publish' : 'draft'))
                            : 'draft'
                    );
                    $currentScheduledAt = old('scheduled_at',
                        isset($post) && $post->status === 'scheduled'
                            ? $post->published_at?->format('Y-m-d\TH:i')
                            : ''
                    );
                @endphp

                {{-- 3가지 상태 라디오 --}}
                <div style="display:flex;flex-direction:column;gap:10px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.875rem;font-weight:500;padding:9px 12px;border-radius:8px;border:1.5px solid {{ $currentType==='draft' ? '#6366f1' : '#e2e8f0' }};transition:border .15s" id="label-draft">
                        <input type="radio" name="publish_type" value="draft"
                               {{ $currentType === 'draft' ? 'checked' : '' }}
                               style="accent-color:#6366f1" onchange="onPublishTypeChange()">
                        <span>🗒 임시저장</span>
                        <span style="font-size:.72rem;color:#94a3b8;font-weight:400;margin-left:auto">비공개</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.875rem;font-weight:500;padding:9px 12px;border-radius:8px;border:1.5px solid {{ $currentType==='publish' ? '#6366f1' : '#e2e8f0' }};transition:border .15s" id="label-publish">
                        <input type="radio" name="publish_type" value="publish"
                               {{ $currentType === 'publish' ? 'checked' : '' }}
                               style="accent-color:#6366f1" onchange="onPublishTypeChange()">
                        <span>🌐 즉시 발행</span>
                        <span style="font-size:.72rem;color:#10b981;font-weight:400;margin-left:auto">공개</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.875rem;font-weight:500;padding:9px 12px;border-radius:8px;border:1.5px solid {{ $currentType==='schedule' ? '#6366f1' : '#e2e8f0' }};transition:border .15s" id="label-schedule">
                        <input type="radio" name="publish_type" value="schedule"
                               {{ $currentType === 'schedule' ? 'checked' : '' }}
                               style="accent-color:#6366f1" onchange="onPublishTypeChange()">
                        <span>⏰ 예약 발행</span>
                        <span style="font-size:.72rem;color:#f59e0b;font-weight:400;margin-left:auto">예약</span>
                    </label>
                </div>

                {{-- 예약 날짜 피커 --}}
                <div id="schedule-picker" style="margin-top:12px;display:{{ $currentType==='schedule' ? 'block' : 'none' }}">
                    <label style="font-size:.75rem;font-weight:600;color:#64748b;display:block;margin-bottom:5px">
                        발행 날짜 / 시간
                    </label>
                    <input type="datetime-local" name="scheduled_at"
                           value="{{ $currentScheduledAt }}"
                           min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}"
                           style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.85rem;outline:none;transition:border .15s"
                           onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
                    @error('scheduled_at')
                        <p style="font-size:.75rem;color:#ef4444;margin-top:4px">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 현재 예약 정보 표시 --}}
                @if(isset($post) && $post->status === 'scheduled')
                    <div style="margin-top:10px;padding:8px 10px;background:#fffbeb;border:1px solid #fde68a;border-radius:6px;font-size:.78rem;color:#92400e;">
                        ⏰ 예약됨: {{ $post->published_at->format('Y.m.d H:i') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="padding:12px 16px"><h3 style="font-size:.875rem">카테고리</h3></div>
            <div class="card-body" style="padding:14px 16px">
                <select name="category" class="form-control" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}"
                            {{ old('category', $post->category ?? '') === $cat->name ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                <a href="{{ route('admin.categories.index') }}"
                   style="font-size:.75rem;color:#6366f1;display:block;margin-top:10px">
                    카테고리 관리 →
                </a>
            </div>
        </div>

        <div class="card" style="background:#f8fafc">
            <div class="card-header" style="padding:10px 16px;border-bottom:1px solid #f1f5f9">
                <h3 style="font-size:.78rem;color:#64748b">마크다운 단축키</h3>
            </div>
            <div class="card-body" style="padding:12px 16px;font-size:.75rem;color:#64748b;line-height:2.1">
                <div><kbd style="background:#e2e8f0;padding:1px 5px;border-radius:3px;font-size:.7rem">##</kbd> 소제목</div>
                <div><kbd style="background:#e2e8f0;padding:1px 5px;border-radius:3px;font-size:.7rem">**굵게**</kbd> 굵은 글씨</div>
                <div><kbd style="background:#e2e8f0;padding:1px 5px;border-radius:3px;font-size:.7rem">- 항목</kbd> 목록</div>
                <div><kbd style="background:#e2e8f0;padding:1px 5px;border-radius:3px;font-size:.7rem">`코드`</kbd> 인라인 코드</div>
                <div><kbd style="background:#e2e8f0;padding:1px 5px;border-radius:3px;font-size:.7rem">> 인용</kbd> 인용구</div>
            </div>
        </div>
    </div>
</div>

{{-- SEO 메타 섹션 --}}
<div style="margin-top:20px;">
    <button type="button" onclick="toggleMeta()" id="meta-toggle-btn"
        style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:13px 18px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;font-size:.875rem;font-weight:600;color:#334155;transition:all .15s">
        <span>🔍 SEO 메타태그 설정</span>
        <span id="meta-arrow" style="transition:transform .2s;font-size:.75rem;color:#94a3b8">▼</span>
    </button>

    <div id="meta-panel" style="display:none;border:1.5px solid #e2e8f0;border-top:none;border-radius:0 0 10px 10px;padding:20px;background:#fff;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">

            <div>
                <label style="display:block;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;margin-bottom:5px">
                    SEO 제목 <span style="font-weight:400;color:#94a3b8">(비워두면 포스트 제목 사용)</span>
                </label>
                <input type="text" name="meta_title" maxlength="70"
                       value="{{ old('meta_title', $post->meta_title ?? '') }}"
                       placeholder="검색결과에 표시될 제목"
                       id="meta-title-input"
                       style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.875rem;outline:none"
                       onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e2e8f0'"
                       oninput="updateCounter('meta-title-input','meta-title-count',70)">
                <div style="text-align:right;font-size:.7rem;color:#94a3b8;margin-top:3px">
                    <span id="meta-title-count">{{ mb_strlen(old('meta_title', $post->meta_title ?? '')) }}</span>/70
                </div>
            </div>

            <div>
                <label style="display:block;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;margin-bottom:5px">
                    키워드 <span style="font-weight:400;color:#94a3b8">(쉼표 구분)</span>
                </label>
                <input type="text" name="meta_keywords"
                       value="{{ old('meta_keywords', $post->meta_keywords ?? '') }}"
                       placeholder="예: 요리, 레시피, 한식"
                       style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.875rem;outline:none"
                       onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="margin-bottom:14px">
            <label style="display:block;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;margin-bottom:5px">
                SEO 설명 <span style="font-weight:400;color:#94a3b8">(비워두면 요약문 사용, 최대 160자)</span>
            </label>
            <textarea name="meta_description" maxlength="160" rows="2"
                      id="meta-desc-input"
                      placeholder="검색결과에 표시될 설명"
                      oninput="updateCounter('meta-desc-input','meta-desc-count',160)"
                      style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.875rem;outline:none;resize:vertical;line-height:1.5"
                      onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e2e8f0'">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
            <div style="text-align:right;font-size:.7rem;color:#94a3b8;margin-top:2px">
                <span id="meta-desc-count">{{ mb_strlen(old('meta_description', $post->meta_description ?? '')) }}</span>/160
            </div>
        </div>

        <div style="margin-bottom:14px">
            <label style="display:block;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;margin-bottom:5px">
                OG 이미지 URL <span style="font-weight:400;color:#94a3b8">(SNS 공유 시 표시 이미지)</span>
            </label>
            <div style="display:flex;gap:8px;align-items:center">
                <input type="url" name="og_image" id="og-image-input"
                       value="{{ old('og_image', $post->og_image ?? '') }}"
                       placeholder="https://..."
                       style="flex:1;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.875rem;outline:none"
                       onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e2e8f0'"
                       oninput="previewOgImage()">
                <button type="button" onclick="previewOgImage()"
                    style="padding:8px 14px;background:#f1f5f9;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.8rem;cursor:pointer;color:#475569">
                    미리보기
                </button>
            </div>
            <div id="og-image-preview" style="margin-top:8px;display:none">
                <img id="og-img-tag" src="" alt="OG 이미지"
                     style="max-height:120px;max-width:240px;border-radius:6px;border:1px solid #e2e8f0;object-fit:cover">
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:#fef2f2;border:1.5px solid #fecaca;border-radius:8px">
            <input type="checkbox" name="noindex" value="1" id="noindex-check"
                   {{ old('noindex', $post->noindex ?? false) ? 'checked' : '' }}
                   style="width:15px;height:15px;accent-color:#ef4444;cursor:pointer">
            <label for="noindex-check" style="font-size:.875rem;font-weight:500;color:#dc2626;cursor:pointer">
                🚫 noindex — 검색엔진 색인 제외
            </label>
            <span style="font-size:.75rem;color:#f87171;margin-left:auto">robots: noindex, nofollow</span>
        </div>

        {{-- SERP 미리보기 --}}
        <div style="margin-top:16px;padding:14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px">
            <div style="font-size:.7rem;font-weight:700;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;margin-bottom:10px">검색 결과 미리보기</div>
            <div id="serp-title" style="font-size:1.1rem;color:#1a0dab;font-weight:400;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                {{ old('meta_title', $post->meta_title ?? $post->title ?? '포스트 제목') }}
            </div>
            <div id="serp-url" style="font-size:.78rem;color:#006621;margin-bottom:4px">
                {{ url('/posts/' . ($post->slug ?? 'slug')) }}
            </div>
            <div id="serp-desc" style="font-size:.85rem;color:#545454;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                {{ old('meta_description', $post->meta_description ?? $post->excerpt ?? '포스트 설명이 여기 표시됩니다.') }}
            </div>
        </div>
    </div>
</div>

{{-- Toast UI Editor JS --}}
<script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
<script>
(function () {
    const CSRF_TOKEN    = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const UPLOAD_URL    = '{{ route("admin.upload.image") }}';
    const hiddenTA      = document.getElementById('content-hidden');
    const editorWrap    = document.getElementById('editor-wrap');
    const DEFAULT_H     = parseInt(localStorage.getItem('editorHeight') || '560');

    // ── 이미지 너비 맵 (url → width) ──
    const imgWidthMap = {};

    // 초기 콘텐츠에서 =Nx 너비 정보 추출 후 제거 (WYSIWYG 호환)
    function preprocessContent(md) {
        return md.replace(/!\[([^\]]*)\]\(([^ \)\n]+) =(\d+)x\)/g, function(match, alt, url, w) {
            imgWidthMap[url] = parseInt(w);
            return `![${alt}](${url})`;
        });
    }

    // 저장 전 마크다운에 너비 정보 재삽입
    function postprocessMarkdown(md) {
        return md.replace(/!\[([^\]]*)\]\(([^)\s]+)\)/g, function(match, alt, url) {
            if (imgWidthMap[url]) {
                return `![${alt}](${url} =${imgWidthMap[url]}x)`;
            }
            return match;
        });
    }

    // ── 이미지 다이얼로그 상태 ──
    const overlay   = document.getElementById('img-dialog-overlay');
    const preview   = document.getElementById('img-dialog-preview');
    const altInput  = document.getElementById('img-alt-input');
    const widthInput= document.getElementById('img-width-input');
    const confirmBtn= document.getElementById('img-dialog-confirm');
    const cancelBtn = document.getElementById('img-dialog-cancel');
    let   pendingCallback = null;
    let   pendingUrl      = null;

    function openImgDialog(url, blobOrName) {
        preview.src = url;
        preview.style.display = 'block';
        altInput.value   = '';
        widthInput.value = imgWidthMap[url] || '';
        overlay.classList.add('open');
        altInput.focus();
    }

    function closeImgDialog() {
        overlay.classList.remove('open');
        pendingCallback = null;
        pendingUrl      = null;
        preview.style.display = 'none';
    }

    confirmBtn.addEventListener('click', function () {
        if (!pendingCallback || !pendingUrl) return;
        const alt   = altInput.value.trim() || '이미지';
        const width = parseInt(widthInput.value);

        // 항상 콜백으로 WYSIWYG에 이미지 삽입 (텍스트 삽입 X)
        pendingCallback(pendingUrl, alt);

        // 너비 정보는 맵에 저장 → 저장 시 마크다운에 반영
        if (width > 0) {
            imgWidthMap[pendingUrl] = width;
        } else {
            delete imgWidthMap[pendingUrl];
        }

        closeImgDialog();
    });

    cancelBtn.addEventListener('click', closeImgDialog);
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeImgDialog();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeImgDialog();
        if (e.key === 'Enter' && overlay.classList.contains('open')) confirmBtn.click();
    });

    // ── 이미지 업로드 ──
    function uploadImage(blob, callback) {
        const formData = new FormData();
        formData.append('image', blob, blob.name || 'image.png');
        formData.append('_token', CSRF_TOKEN);
        editorWrap.style.opacity = '.75';

        fetch(UPLOAD_URL, { method: 'POST', body: formData })
            .then(res => { if (!res.ok) throw new Error('업로드 실패'); return res.json(); })
            .then(data => {
                pendingUrl      = data.url;
                pendingCallback = callback;
                openImgDialog(data.url, blob.name || '');
            })
            .catch(err => alert('이미지 업로드 실패: ' + err.message))
            .finally(() => { editorWrap.style.opacity = '1'; });
    }

    // ── 에디터 초기화 ──
    const editor = new toastui.Editor({
        el: document.getElementById('toast-editor'),
        height: DEFAULT_H + 'px',
        initialEditType: 'wysiwyg',
        previewStyle: 'tab',
        initialValue: preprocessContent(hiddenTA.value),
        language: 'ko-KR',
        toolbarItems: [
            ['heading', 'bold', 'italic', 'strike'],
            ['hr', 'quote'],
            ['ul', 'ol', 'task'],
            ['table', 'link', 'image'],
            ['code', 'codeblock'],
        ],
        placeholder: '내용을 입력하세요...',
        hooks: {
            addImageBlobHook: function (blob, callback) {
                uploadImage(blob, callback);
            }
        },
        events: {
            change: function () { hiddenTA.value = editor.getMarkdown(); }
        }
    });

    // ── 높이 조절 핸들 ──
    const handle   = document.getElementById('editor-resize-handle');
    let resizing   = false;
    let startY     = 0;
    let startH     = 0;

    handle.addEventListener('mousedown', function (e) {
        resizing = true;
        startY   = e.clientY;
        startH   = parseInt(editor.getHeight()) || DEFAULT_H;
        document.body.style.userSelect = 'none';
        document.body.style.cursor     = 'ns-resize';
    });

    document.addEventListener('mousemove', function (e) {
        if (!resizing) return;
        const newH = Math.max(280, Math.min(1600, startH + (e.clientY - startY)));
        editor.setHeight(newH + 'px');
    });

    document.addEventListener('mouseup', function () {
        if (!resizing) return;
        resizing = false;
        document.body.style.userSelect = '';
        document.body.style.cursor     = '';
        // 높이 저장
        localStorage.setItem('editorHeight', parseInt(editor.getHeight()));
    });

    // ── 폼 제출 동기화 ──
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function () {
            // 너비 정보 재삽입 후 저장
            hiddenTA.value = postprocessMarkdown(editor.getMarkdown());
        });
    }

    // ── Tab 키 → 에디터 포커스 ──
    document.getElementById('post-title').addEventListener('keydown', function (e) {
        if (e.key === 'Tab') { e.preventDefault(); editor.focus(); }
    });
})();

// ── 메타 패널 토글 ──
function toggleMeta() {
    const panel  = document.getElementById('meta-panel');
    const arrow  = document.getElementById('meta-arrow');
    const isOpen = panel.style.display !== 'none';
    panel.style.display = isOpen ? 'none' : 'block';
    arrow.style.transform = isOpen ? '' : 'rotate(180deg)';
    // 패널 열 때 에러 있으면 자동 열기 처리됨
}

// 에러 있으면 자동으로 메타 패널 열기
document.addEventListener('DOMContentLoaded', function () {
    @if($errors->hasAny(['meta_title','meta_description','meta_keywords','og_image']))
        toggleMeta();
    @endif
    // 기존 값 있으면 자동 열기
    @if(old('meta_title', $post->meta_title ?? '') || old('meta_description', $post->meta_description ?? '') || old('meta_keywords', $post->meta_keywords ?? '') || old('og_image', $post->og_image ?? '') || old('noindex', $post->noindex ?? false))
        toggleMeta();
    @endif
    // OG 이미지 기존 값 미리보기
    if (document.getElementById('og-image-input').value) previewOgImage();
    // SERP 실시간 업데이트
    initSerpPreview();
});

function updateCounter(inputId, countId, max) {
    const val = document.getElementById(inputId).value;
    const el  = document.getElementById(countId);
    el.textContent = [...val].length;
    el.style.color = [...val].length > max * 0.9 ? '#ef4444' : '#94a3b8';
    updateSerp();
}

function previewOgImage() {
    const url  = document.getElementById('og-image-input').value.trim();
    const wrap = document.getElementById('og-image-preview');
    const img  = document.getElementById('og-img-tag');
    if (url) { img.src = url; wrap.style.display = 'block'; }
    else      { wrap.style.display = 'none'; }
}

function initSerpPreview() {
    const titleInput = document.getElementById('post-title');
    const metaTitle  = document.getElementById('meta-title-input');
    const metaDesc   = document.getElementById('meta-desc-input');
    [titleInput, metaTitle, metaDesc].forEach(el => {
        if (el) el.addEventListener('input', updateSerp);
    });
}

function updateSerp() {
    const serpTitle = document.getElementById('serp-title');
    const serpDesc  = document.getElementById('serp-desc');
    if (!serpTitle) return;

    const metaTitle = document.getElementById('meta-title-input')?.value.trim();
    const postTitle = document.getElementById('post-title')?.value.trim();
    const metaDesc  = document.getElementById('meta-desc-input')?.value.trim();

    serpTitle.textContent = metaTitle || postTitle || '포스트 제목';
    if (metaDesc) serpDesc.textContent = metaDesc;
}

// ── 발행 타입 변경 핸들러 ──
function onPublishTypeChange() {
    const type    = document.querySelector('input[name="publish_type"]:checked')?.value;
    const picker  = document.getElementById('schedule-picker');
    const labels  = { draft: 'label-draft', publish: 'label-publish', schedule: 'label-schedule' };

    // 피커 토글
    picker.style.display = type === 'schedule' ? 'block' : 'none';
    if (type !== 'schedule') {
        document.querySelector('input[name="scheduled_at"]').value = '';
    }

    // 라디오 카드 테두리 강조
    Object.values(labels).forEach(id => {
        document.getElementById(id).style.borderColor = '#e2e8f0';
    });
    if (labels[type]) {
        document.getElementById(labels[type]).style.borderColor = '#6366f1';
    }
}
</script>
