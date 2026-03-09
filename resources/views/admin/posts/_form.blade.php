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

{{-- 사이트 전체 메타태그는 관리자 > 사이트 설정 > SEO 탭에서 관리합니다 --}}

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
