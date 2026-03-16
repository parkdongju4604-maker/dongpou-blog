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
            <button type="button" class="btn-cancel-dialog" id="img-dialog-cancel">취소</button>
            <button type="button" class="btn-insert" id="img-dialog-confirm">삽입</button>
        </div>
    </div>
</div>

{{-- 자동 저장 복원 배너 (새 글 작성 시에만) --}}
@if(!isset($post))
<div id="draft-restore-banner"
     style="display:none;align-items:center;gap:12px;padding:12px 18px;margin-bottom:16px;
            background:#fffbeb;border:1px solid #fde68a;border-radius:10px;font-size:.875rem;color:#92400e">
    <span>💾 이전에 작성 중이던 임시 저장본이 있습니다.</span>
    <div style="display:flex;gap:8px;margin-left:auto;flex-shrink:0">
        <button type="button" id="draft-restore-btn"
                style="padding:5px 14px;background:#f59e0b;color:#fff;border:none;border-radius:6px;font-size:.8rem;font-weight:600;cursor:pointer">
            불러오기
        </button>
        <button type="button" id="draft-discard-btn"
                style="padding:5px 12px;background:#fff;color:#92400e;border:1px solid #fde68a;border-radius:6px;font-size:.8rem;cursor:pointer">
            무시
        </button>
    </div>
</div>
@endif

{{-- 미리보기 모달 스타일 --}}
<style>
.preview-modal { display:none;position:fixed;inset:0;z-index:9998;background:rgba(0,0,0,.4);align-items:center;justify-content:center;animation:fadeIn .15s }
@keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
.preview-modal.open { display:flex }
.preview-container { background:#fff;border-radius:12px;width:min(90vw,900px);max-height:90vh;overflow:auto;box-shadow:0 20px 60px rgba(0,0,0,.25) }
.preview-header { display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid #e2e8f0 }
.preview-header h3 { font-size:1rem;font-weight:700;color:#0f172a;margin:0 }
.preview-close { background:none;border:none;font-size:1.5rem;cursor:pointer;color:#94a3b8;padding:0;width:32px;height:32px;display:flex;align-items:center;justify-content:center }
.preview-close:hover { color:#64748b }
.preview-body { padding:28px 36px;color:#1f2937;line-height:1.7;font-size:.95rem }
.preview-body h1, .preview-body h2, .preview-body h3, .preview-body h4 { margin:1.6em 0 .6em;font-weight:700 }
.preview-body h1 { font-size:1.8em;color:#0f172a }
.preview-body h2 { font-size:1.5em;color:#1f2937;border-bottom:2px solid #e2e8f0;padding-bottom:.4em }
.preview-body h3 { font-size:1.2em;color:#374151 }
.preview-body p { margin:.8em 0 }
.preview-body a { color:#4f46e5;text-decoration:underline }
.preview-body a:hover { color:#4338ca }
.preview-body blockquote { margin:1.2em 0;padding:.8em 1.2em;border-left:4px solid #4f46e5;background:#eef2ff;color:#6366f1 }
.preview-body code { background:#f3f4f6;padding:2px 6px;border-radius:4px;font-family:monospace;font-size:.9em;color:#dc2626 }
.preview-body pre { background:#1e1e2e;padding:16px;border-radius:8px;overflow-x:auto;color:#cdd6f4;line-height:1.5;font-size:.85em }
.preview-body pre code { background:none;padding:0;color:inherit }
.preview-body img { max-width:100%;height:auto;border-radius:8px;margin:1em 0;cursor:pointer;position:relative;transition:filter .15s }
.preview-body img:hover { filter:brightness(0.85);box-shadow:0 0 0 3px rgba(16,185,129,.3) }
.preview-body ul, .preview-body ol { margin:1em 0;padding-left:2em }
.preview-body li { margin:.4em 0 }
.preview-body table { width:100%;border-collapse:collapse;margin:1.5em 0 }
.preview-body table th { background:#f3f4f6;padding:10px;border:1px solid #e5e7eb;text-align:left;font-weight:600 }
.preview-body table td { padding:10px;border:1px solid #e5e7eb }
.preview-body hr { border:none;border-top:2px solid #e2e8f0;margin:2em 0 }
.preview-loading { text-align:center;padding:40px;color:#94a3b8 }
.preview-error { padding:20px;background:#fee2e2;border:1px solid #fecaca;border-radius:8px;color:#991b1b;margin:12px }
.img-replace-modal { display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center }
.img-replace-modal.open { display:flex }
.img-replace-container { background:#fff;border-radius:12px;width:min(500px,90vw);box-shadow:0 20px 60px rgba(0,0,0,.3);padding:28px;animation:slideIn .2s ease }
@keyframes slideIn { from { transform:translateY(-20px);opacity:0 } to { transform:translateY(0);opacity:1 } }
.img-replace-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:20px }
.img-replace-header h3 { font-size:1rem;font-weight:700;color:#0f172a;margin:0 }
.img-replace-close { background:none;border:none;font-size:1.5rem;cursor:pointer;color:#94a3b8;padding:0 }
.img-replace-tabs { display:flex;gap:10px;margin-bottom:20px;border-bottom:1px solid #e2e8f0 }
.img-replace-tab { padding:10px 16px;border:none;background:none;cursor:pointer;color:#64748b;font-weight:500;border-bottom:2px solid transparent;transition:all .15s }
.img-replace-tab.active { border-bottom-color:#10b981;color:#10b981 }
.img-replace-tab-content { display:none }
.img-replace-tab-content.active { display:block }
.img-replace-url-input { width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.9rem;outline:none;margin-bottom:12px;transition:border .15s }
.img-replace-url-input:focus { border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,.1) }
.img-replace-dropzone { border:2px dashed #e2e8f0;border-radius:8px;padding:40px;text-align:center;cursor:pointer;transition:all .15s;background:#f8fafc }
.img-replace-dropzone.dragover { border-color:#10b981;background:#ecfdf5 }
.img-replace-dropzone p { color:#64748b;margin:8px 0;font-size:.9rem }
.img-replace-preview { margin:12px 0;max-width:100%;max-height:200px;border-radius:6px;border:1px solid #e2e8f0 }
.img-replace-actions { display:flex;gap:10px;justify-content:flex-end;margin-top:20px }
.img-replace-actions button { padding:10px 20px;border-radius:6px;font-weight:600;border:none;cursor:pointer;font-size:.875rem }
.btn-replace-confirm { background:#10b981;color:#fff }
.btn-replace-confirm:hover { background:#059669 }
.btn-replace-cancel { background:#f1f5f9;color:#475569 }
.btn-replace-cancel:hover { background:#e2e8f0 }
</style>

{{-- 미리보기 모달 --}}
<div class="preview-modal" id="preview-modal">
    <div class="preview-container">
        <div class="preview-header">
            <h3>📄 미리보기</h3>
            <button type="button" class="preview-close" onclick="closePreview()">✕</button>
        </div>
        <div class="preview-body" id="preview-content">
            <div class="preview-loading">로드 중...</div>
        </div>
    </div>
</div>

{{-- 이미지 교체 모달 --}}
<div class="img-replace-modal" id="img-replace-modal">
    <div class="img-replace-container">
        <div class="img-replace-header">
            <h3>🖼 이미지 교체</h3>
            <button type="button" class="img-replace-close" onclick="closeImgReplace()">✕</button>
        </div>
        
        <div class="img-replace-tabs">
            <button type="button" class="img-replace-tab active" data-tab="url" onclick="switchImgReplaceTab('url')">URL 입력</button>
            <button type="button" class="img-replace-tab" data-tab="upload" onclick="switchImgReplaceTab('upload')">파일 업로드</button>
        </div>
        
        <div id="img-replace-url" class="img-replace-tab-content active">
            <input type="text" id="img-replace-url-input" class="img-replace-url-input" placeholder="이미지 URL을 입력하세요">
            <p style="font-size:.75rem;color:#94a3b8">예: https://example.com/image.jpg</p>
        </div>
        
        <div id="img-replace-upload" class="img-replace-tab-content">
            <div class="img-replace-dropzone" id="img-replace-dropzone" ondrop="handleImgReplaceDrop(event)" ondragover="handleImgReplaceDragover(event)" ondragleave="handleImgReplaceDragleave(event)">
                <div style="font-size:2rem;margin-bottom:10px">📁</div>
                <p><strong>파일을 여기에 드래그하세요</strong></p>
                <p style="color:#94a3b8">또는</p>
                <button type="button" style="padding:8px 16px;background:#10b981;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600" onclick="document.getElementById('img-replace-file-input').click()">파일 선택</button>
                <input type="file" id="img-replace-file-input" accept="image/*" style="display:none" onchange="handleImgReplaceFileSelect(event)">
            </div>
        </div>
        
        <img id="img-replace-preview" class="img-replace-preview" style="display:none" alt="미리보기">
        
        <div class="img-replace-actions">
            <button type="button" class="btn-replace-cancel" onclick="closeImgReplace()">취소</button>
            <button type="button" class="btn-replace-confirm" onclick="confirmImgReplace()">교체</button>
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
        @php
            $initContentType = old('content_type', $post->content_type ?? 'markdown');
        @endphp
        <input type="hidden" name="content_type" id="content-type-input" value="{{ $initContentType }}">

        <div class="form-group">
            <label class="form-label" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px">
                <span style="display:flex;align-items:center;gap:10px">
                    <span>내용 *</span>
                    {{-- 모드 토글 --}}
                    <span id="editor-mode-toggle" style="display:inline-flex;border:1.5px solid #e2e8f0;border-radius:8px;overflow:hidden;font-size:.78rem;font-weight:600">
                        <button type="button" id="btn-mode-md"
                                onclick="switchEditorMode('markdown')"
                                style="padding:4px 14px;border:none;cursor:pointer;transition:background .15s">
                            📝 마크다운
                        </button>
                        <button type="button" id="btn-mode-html"
                                onclick="switchEditorMode('html')"
                                style="padding:4px 14px;border:none;cursor:pointer;border-left:1.5px solid #e2e8f0;transition:background .15s">
                            &lt;/&gt; HTML
                        </button>
                    </span>
                </span>
                <span style="display:flex;align-items:center;gap:10px">
                    <span id="autosave-status" style="font-size:.72rem;color:#94a3b8;font-weight:400;transition:color .3s"></span>
                    <span id="editor-mode-hint" style="font-size:.72rem;color:#94a3b8;font-weight:400">이미지 드래그&드롭 · 클립보드 붙여넣기 지원</span>
                    <button type="button" id="btn-preview" onclick="openPreview()" title="미리보기" style="margin-left:auto">👁 미리보기</button>
                </span>
                <style>
                    #btn-preview { font-size:.72rem; padding:3px 10px; background:#10b981; color:#fff; border:none; border-radius:5px; cursor:pointer; font-weight:600; }
                </style>
            </label>

            {{-- 마크다운/WYSIWYG 에디터 --}}
            <textarea name="content" id="content-hidden" style="display:none">{{ old('content', $post->content ?? '') }}</textarea>
            <div id="editor-md-wrap">
                <div class="editor-wrap" id="editor-wrap">
                    <div id="toast-editor"></div>
                    <div class="editor-resize-handle" id="editor-resize-handle" title="드래그해서 높이 조절"></div>
                </div>
                <div style="font-size:.73rem;color:#94a3b8;margin-top:5px;text-align:right">
                    아래 경계선을 드래그해서 에디터 높이를 조절할 수 있습니다
                </div>
            </div>

            {{-- HTML 에디터 --}}
            <div id="editor-html-wrap" style="display:none">
                <textarea id="html-editor"
                          placeholder="HTML 코드를 입력하세요..."
                          style="width:100%;min-height:560px;padding:16px;
                                 font-family:'JetBrains Mono','Fira Code','Cascadia Code',monospace;
                                 font-size:.875rem;line-height:1.7;
                                 border:1.5px solid #e2e8f0;border-radius:8px;
                                 background:#1e1e2e;color:#cdd6f4;
                                 resize:vertical;outline:none;
                                 tab-size:2;transition:border .15s"
                          onfocus="this.style.borderColor='#6366f1'"
                          onblur="this.style.borderColor='#e2e8f0'"
                          spellcheck="false"></textarea>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;gap:8px">
                    <span style="font-size:.73rem;color:#94a3b8">HTML 태그를 직접 작성하세요. 저장 시 그대로 렌더링됩니다.</span>
                    <div style="display:flex;gap:6px">
                        <button type="button" id="btn-html-preview" onclick="openPreview()" title="미리보기">👁 미리보기</button>
                        <button type="button" id="btn-snippet-toggle" onclick="insertHtmlSnippet()" title="자주 쓰는 태그">📎 자주 쓰는 태그</button>
                    </div>
                </div>
                <style>
                    #btn-html-preview, #btn-snippet-toggle { font-size:.75rem; padding:4px 12px; border-radius:6px; cursor:pointer; border:none; }
                    #btn-html-preview { background:#10b981; color:#fff; }
                    #btn-snippet-toggle { background:#f1f5f9; border:1px solid #e2e8f0; color:#475569; }
                </style>
                {{-- 스니펫 패널 (숨김) --}}
                <div id="html-snippet-panel" style="display:none;margin-top:8px;padding:12px 16px;
                     background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;
                     grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:8px">
                    @foreach([
                        ['label'=>'제목 h2',      'code'=>'<h2>제목</h2>'],
                        ['label'=>'제목 h3',      'code'=>'<h3>제목</h3>'],
                        ['label'=>'굵게',         'code'=>'<strong>텍스트</strong>'],
                        ['label'=>'기울임',       'code'=>'<em>텍스트</em>'],
                        ['label'=>'단락',         'code'=>'<p>내용</p>'],
                        ['label'=>'링크',         'code'=>'<a href="URL">텍스트</a>'],
                        ['label'=>'이미지',       'code'=>'<img src="URL" alt="설명" style="max-width:100%">'],
                        ['label'=>'순서없는 목록','code'=>"<ul>\n  <li>항목 1</li>\n  <li>항목 2</li>\n</ul>"],
                        ['label'=>'순서있는 목록','code'=>"<ol>\n  <li>항목 1</li>\n  <li>항목 2</li>\n</ol>"],
                        ['label'=>'인용구',       'code'=>'<blockquote>인용 내용</blockquote>'],
                        ['label'=>'코드 블록',    'code'=>"<pre><code>코드 내용</code></pre>"],
                        ['label'=>'구분선',       'code'=>'<hr>'],
                    ] as $snip)
                    <button type="button"
                            onclick="insertSnippet({{ json_encode($snip['code']) }})"
                            style="text-align:left;padding:6px 10px;background:#fff;
                                   border:1px solid #e2e8f0;border-radius:6px;
                                   font-size:.78rem;cursor:pointer;color:#374151;
                                   transition:background .1s"
                            onmouseover="this.style.background='#f1f5f9'"
                            onmouseout="this.style.background='#fff'">
                        {{ $snip['label'] }}
                    </button>
                    @endforeach
                </div>
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

        {{-- 태그 --}}
        <div class="form-group">
            <label class="form-label">
                태그
                <span style="font-weight:400;color:#94a3b8;font-size:.75rem;margin-left:4px">Enter 또는 쉼표로 추가 (선택)</span>
            </label>
            @php
                $existingTags = isset($post) ? $post->tags->pluck('name')->join(',') : old('tags','');
            @endphp
            <input type="hidden" name="tags" id="tags-hidden" value="{{ $existingTags }}">
            <div id="tag-input-wrap" style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;
                 padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;cursor:text;
                 min-height:42px;background:#fff;transition:border-color .15s"
                 onclick="document.getElementById('tag-text-input').focus()">
                <div id="tag-chips" style="display:contents"></div>
                <input type="text" id="tag-text-input" placeholder="태그 입력..."
                       style="border:none;outline:none;font-size:.875rem;flex:1;min-width:80px;background:transparent;padding:2px 4px"
                       autocomplete="off">
            </div>
            {{-- 자동완성 드롭다운 --}}
            <div id="tag-suggestions" style="display:none;position:absolute;z-index:50;
                 background:#fff;border:1.5px solid #e2e8f0;border-radius:8px;
                 box-shadow:0 4px 20px rgba(0,0,0,.1);max-height:180px;overflow-y:auto;margin-top:4px;min-width:180px">
            </div>
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

    // ── 모드 상태 (에디터 초기화 전에 선언) ──
    const INIT_MODE      = '{{ $initContentType }}';
    const ORIGINAL_CONTENT = hiddenTA.value;   // 에디터 change 이벤트 전에 원본 보존
    let   currentMode    = INIT_MODE;           // 'markdown' | 'html'

    // ── 에디터 초기화 ──
    const editor = new toastui.Editor({
        el: document.getElementById('toast-editor'),
        height: DEFAULT_H + 'px',
        initialEditType: 'wysiwyg',
        previewStyle: 'tab',
        // HTML 모드일 때는 에디터를 빈 상태로 초기화 (HTML 내용을 마크다운으로 파싱하지 않음)
        initialValue: INIT_MODE === 'html' ? '' : preprocessContent(hiddenTA.value),
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
            // HTML 모드에서는 hiddenTA를 덮어쓰지 않음
            change: function () {
                if (currentMode !== 'html') {
                    hiddenTA.value = editor.getMarkdown();
                }
            }
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

    // ── 에디터 모드 스위칭 ──
    const htmlEditorTA  = document.getElementById('html-editor');
    const modeInput     = document.getElementById('content-type-input');
    const btnMd         = document.getElementById('btn-mode-md');
    const btnHtml       = document.getElementById('btn-mode-html');
    const wrapMd        = document.getElementById('editor-md-wrap');
    const wrapHtml      = document.getElementById('editor-html-wrap');
    const modeHint      = document.getElementById('editor-mode-hint');

    function applyModeUI(mode) {
        const isMd = mode === 'markdown';
        currentMode = mode;
        wrapMd.style.display   = isMd ? '' : 'none';
        wrapHtml.style.display = isMd ? 'none' : '';
        btnMd.style.background   = isMd ? '#6366f1' : '#fff';
        btnMd.style.color        = isMd ? '#fff'    : '#374151';
        btnHtml.style.background = isMd ? '#fff'    : '#6366f1';
        btnHtml.style.color      = isMd ? '#374151' : '#fff';
        modeHint.textContent = isMd
            ? '이미지 드래그&드롭 · 클립보드 붙여넣기 지원'
            : 'HTML 태그를 직접 작성합니다';
        modeInput.value = mode;
    }

    window.switchEditorMode = function(mode) {
        if (currentMode === mode) return;

        if (mode === 'html') {
            // 마크다운 → HTML 전환: HTML 에디터를 비워둠 (getHTML() 결과의 <p>/<br> 태그 오염 방지)
            // 사용자가 직접 HTML을 작성하도록 함
        } else {
            // HTML → 마크다운 전환: 경고 후 HTML textarea 비우기
            if (htmlEditorTA.value.trim() &&
                !confirm('마크다운 모드로 전환하면 작성한 HTML 내용이 초기화됩니다.\n계속하시겠습니까?')) {
                return;
            }
            htmlEditorTA.value = '';
            hiddenTA.value = editor.getMarkdown();
        }
        applyModeUI(mode);
    };

    // HTML textarea 입력 시 hiddenTA 실시간 동기화 (submit 의존 없음)
    htmlEditorTA.addEventListener('input', function() {
        if (currentMode === 'html') {
            hiddenTA.value = this.value;
        }
    });

    // 초기 모드 설정
    if (INIT_MODE === 'html') {
        htmlEditorTA.value = ORIGINAL_CONTENT;  // 원본 HTML 내용 (에디터 change에 오염되지 않은)
        hiddenTA.value     = ORIGINAL_CONTENT;  // hiddenTA도 원본으로 복원
        applyModeUI('html');
    } else {
        applyModeUI('markdown');
    }

    // ── HTML 스니펫 패널 토글 ──
    window.insertHtmlSnippet = function() {
        const panel = document.getElementById('html-snippet-panel');
        const isHidden = panel.style.display === 'none' || panel.style.display === '';
        panel.style.display = isHidden ? 'grid' : 'none';
    };

    // ── HTML 스니펫 삽입 ──
    window.insertSnippet = function(code) {
        const ta = htmlEditorTA;
        const start = ta.selectionStart;
        const end   = ta.selectionEnd;
        ta.value = ta.value.substring(0, start) + code + ta.value.substring(end);
        ta.selectionStart = ta.selectionEnd = start + code.length;
        ta.focus();
        // 실시간 동기화 트리거
        ta.dispatchEvent(new Event('input'));
    };

    // ── 폼 제출 동기화 (실시간 동기화 실패 대비 안전장치) ──
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function () {
            if (currentMode === 'html') {
                hiddenTA.value = htmlEditorTA.value;
            } else {
                hiddenTA.value = postprocessMarkdown(editor.getMarkdown());
            }
            // content_type hidden input 최종 확인
            modeInput.value = currentMode;
            try { localStorage.removeItem(DRAFT_KEY); } catch(e) {}
        });
    }

    // ── Tab 키 → 에디터 포커스 ──
    document.getElementById('post-title').addEventListener('keydown', function (e) {
        if (e.key === 'Tab') { e.preventDefault(); editor.focus(); }
    });

    // ── 자동 저장 ──
    const DRAFT_KEY    = 'post_draft_{{ isset($post) ? $post->id : "new" }}';
    const statusEl     = document.getElementById('autosave-status');
    let   saveTimer    = null;

    function collectDraft() {
        return {
            title:        document.getElementById('post-title').value,
            excerpt:      document.querySelector('input[name="excerpt"]')?.value || '',
            content:      currentMode === 'html'
                            ? (htmlEditorTA ? htmlEditorTA.value : '')
                            : postprocessMarkdown(editor.getMarkdown()),
            content_type: currentMode,
            category:     document.querySelector('select[name="category"]')?.value || '',
            publish_type: document.querySelector('input[name="publish_type"]:checked')?.value || 'draft',
            scheduled_at: document.querySelector('input[name="scheduled_at"]')?.value || '',
            savedAt:      new Date().toISOString(),
        };
    }

    function saveDraft() {
        try {
            localStorage.setItem(DRAFT_KEY, JSON.stringify(collectDraft()));
            if (statusEl) {
                const t = new Date().toLocaleTimeString('ko-KR', { hour: '2-digit', minute: '2-digit' });
                statusEl.textContent = '💾 ' + t + ' 자동 저장됨';
                statusEl.style.color = '#10b981';
                clearTimeout(statusEl._fade);
                statusEl._fade = setTimeout(() => { statusEl.style.color = '#94a3b8'; }, 3000);
            }
        } catch(e) {}
    }

    function scheduleSave() {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(saveDraft, 2000); // 입력 멈춘 후 2초
    }

    // 입력 이벤트 훅
    document.getElementById('post-title').addEventListener('input', scheduleSave);
    document.querySelector('input[name="excerpt"]')?.addEventListener('input', scheduleSave);
    document.querySelector('select[name="category"]')?.addEventListener('change', scheduleSave);
    document.querySelectorAll('input[name="publish_type"]').forEach(r => r.addEventListener('change', scheduleSave));
    editor.on('change', scheduleSave);
    document.getElementById('html-editor')?.addEventListener('input', scheduleSave);

    // 주기적 저장 (30초)
    setInterval(saveDraft, 30000);

    // ── 복원 (새 글 작성 페이지만) ──
    @if(!isset($post))
    (function () {
        try {
            const saved = JSON.parse(localStorage.getItem(DRAFT_KEY) || 'null');
            if (!saved || !saved.title) return;

            const banner = document.getElementById('draft-restore-banner');
            if (banner) banner.style.display = 'flex';

            document.getElementById('draft-restore-btn')?.addEventListener('click', function () {
                document.getElementById('post-title').value = saved.title || '';
                const excerptEl = document.querySelector('input[name="excerpt"]');
                if (excerptEl && saved.excerpt) excerptEl.value = saved.excerpt;
                const catEl = document.querySelector('select[name="category"]');
                if (catEl && saved.category) catEl.value = saved.category;
                const radio = document.querySelector(`input[name="publish_type"][value="${saved.publish_type}"]`);
                if (radio) { radio.checked = true; onPublishTypeChange(); }
                const schedEl = document.querySelector('input[name="scheduled_at"]');
                if (schedEl && saved.scheduled_at) schedEl.value = saved.scheduled_at;
                if (saved.content_type === 'html') {
                    if (typeof switchEditorMode === 'function') switchEditorMode('html');
                    if (htmlEditorTA) htmlEditorTA.value = saved.content || '';
                } else if (saved.content) {
                    editor.setMarkdown(preprocessContent(saved.content));
                    hiddenTA.value = saved.content;
                }
                if (banner) banner.style.display = 'none';
            });

            document.getElementById('draft-discard-btn')?.addEventListener('click', function () {
                try { localStorage.removeItem(DRAFT_KEY); } catch(e) {}
                if (banner) banner.style.display = 'none';
            });
        } catch(e) {}
    })();
    @endif
})();

// ── 태그 칩 UI ──
(function () {
    const hidden      = document.getElementById('tags-hidden');
    const wrap        = document.getElementById('tag-input-wrap');
    const chipsEl     = document.getElementById('tag-chips');
    const textInput   = document.getElementById('tag-text-input');
    const suggestions = document.getElementById('tag-suggestions');
    let   allTags     = [];   // 서버에서 가져온 전체 태그 목록
    let   tags        = hidden.value ? hidden.value.split(',').map(t => t.trim()).filter(Boolean) : [];

    // 초기 칩 렌더
    tags.forEach(addChip);
    syncHidden();

    // 전체 태그 가져오기 (자동완성용)
    fetch('{{ route("tags.all") }}')
        .then(r => r.json())
        .then(data => { allTags = data.map(t => t.name); })
        .catch(() => {});

    function addChip(name) {
        name = name.trim();
        if (!name || tags.includes(name)) return;
        tags.push(name);
        const chip = document.createElement('span');
        chip.style.cssText = 'display:inline-flex;align-items:center;gap:4px;padding:3px 10px;' +
            'background:#eef2ff;color:#4f46e5;border-radius:20px;font-size:.78rem;font-weight:600;white-space:nowrap';
        chip.innerHTML = `${name}<button type="button" style="background:none;border:none;cursor:pointer;color:#6366f1;font-size:.9rem;padding:0;line-height:1" data-name="${name}">×</button>`;
        chip.querySelector('button').addEventListener('click', function () {
            tags = tags.filter(t => t !== this.dataset.name);
            chip.remove();
            syncHidden();
        });
        chipsEl.appendChild(chip);
        syncHidden();
    }

    function syncHidden() {
        hidden.value = tags.join(',');
    }

    function showSuggestions(q) {
        const filtered = allTags.filter(t => t.toLowerCase().includes(q.toLowerCase()) && !tags.includes(t));
        if (!filtered.length || !q) { suggestions.style.display = 'none'; return; }
        suggestions.innerHTML = filtered.slice(0, 8).map(t =>
            `<div style="padding:8px 14px;cursor:pointer;font-size:.875rem;transition:background .1s"
                  onmousedown="event.preventDefault()"
                  onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background=''"
                  data-tag="${t}">${t}</div>`
        ).join('');
        suggestions.style.display = 'block';
        suggestions.querySelectorAll('[data-tag]').forEach(el => {
            el.addEventListener('click', () => {
                addChip(el.dataset.tag);
                textInput.value = '';
                suggestions.style.display = 'none';
                textInput.focus();
            });
        });
    }

    // 쉼표 포함 텍스트 붙여넣기 → 태그 자동 분리
    textInput.addEventListener('paste', function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text');
        const parts  = pasted.split(/[,，、\n]+/).map(s => s.trim()).filter(Boolean);
        if (parts.length > 1) {
            parts.forEach(p => addChip(p));
        } else {
            // 쉼표 없으면 그냥 현재 위치에 삽입
            const start = this.selectionStart;
            this.value  = this.value.slice(0, start) + pasted + this.value.slice(this.selectionEnd);
            this.selectionStart = this.selectionEnd = start + pasted.length;
        }
    });

    textInput.addEventListener('keydown', function (e) {
        if ((e.key === 'Enter' || e.key === ',') && this.value.trim()) {
            e.preventDefault();
            addChip(this.value.replace(/,/g, '').trim());
            this.value = '';
            suggestions.style.display = 'none';
        } else if (e.key === 'Backspace' && !this.value && tags.length) {
            const last = tags[tags.length - 1];
            tags.pop();
            chipsEl.lastElementChild?.remove();
            syncHidden();
        }
    });

    textInput.addEventListener('input', function () {
        showSuggestions(this.value.trim());
    });

    textInput.addEventListener('blur', function () {
        setTimeout(() => { suggestions.style.display = 'none'; }, 150);
        if (this.value.trim()) {
            addChip(this.value.trim());
            this.value = '';
        }
    });

    wrap.addEventListener('focus', () => {
        wrap.style.borderColor = '#6366f1';
    }, true);
    wrap.addEventListener('blur', () => {
        wrap.style.borderColor = '#e2e8f0';
    }, true);
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

// ── 미리보기 ──
function openPreview() {
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const PREVIEW_URL = '{{ route("admin.posts.preview") }}';
    const currentMode = document.getElementById('content-type-input').value || 'markdown';
    
    let content;
    if (currentMode === 'html') {
        content = document.getElementById('html-editor')?.value || '';
    } else {
        // 마크다운 모드에서는 editor.getMarkdown()에서 직접 가져옴
        content = document.getElementById('content-hidden')?.value || '';
    }

    if (!content.trim()) {
        alert('내용을 입력한 후 미리보기를 열어주세요.');
        return;
    }

    const modal = document.getElementById('preview-modal');
    const contentEl = document.getElementById('preview-content');
    contentEl.innerHTML = '<div class="preview-loading">로드 중...</div>';
    modal.classList.add('open');

    fetch(PREVIEW_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
        },
        body: JSON.stringify({
            content: content,
            content_type: currentMode,
        }),
    })
    .then(res => {
        if (!res.ok) throw new Error('미리보기 생성 실패');
        return res.json();
    })
    .then(data => {
        contentEl.innerHTML = data.html || '<p>미리보기를 표시할 내용이 없습니다.</p>';
    })
    .catch(err => {
        contentEl.innerHTML = `<div class="preview-error">❌ 오류: ${err.message}</div>`;
    });
}

function closePreview() {
    document.getElementById('preview-modal').classList.remove('open');
}

// 모달 바깥쪽 클릭 시 닫기
document.getElementById('preview-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closePreview();
});

document.getElementById('img-replace-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeImgReplace();
});

// ESC 키로 닫기
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('preview-modal').classList.contains('open')) {
        closePreview();
    }
    if (e.key === 'Escape' && document.getElementById('img-replace-modal').classList.contains('open')) {
        closeImgReplace();
    }
});

// ── 이미지 교체 기능 ──
let currentImageElement = null;

// 미리보기에서 이미지 클릭 시
document.addEventListener('click', function(e) {
    if (e.target.tagName === 'IMG' && e.target.closest('.preview-body')) {
        currentImageElement = e.target;
        const src = e.target.src;
        document.getElementById('img-replace-url-input').value = src;
        document.getElementById('img-replace-preview').src = src;
        document.getElementById('img-replace-preview').style.display = 'block';
        openImgReplace();
    }
});

function openImgReplace() {
    document.getElementById('img-replace-modal').classList.add('open');
    document.getElementById('img-replace-url-input').focus();
}

function closeImgReplace() {
    document.getElementById('img-replace-modal').classList.remove('open');
    currentImageElement = null;
    document.getElementById('img-replace-preview').style.display = 'none';
}

function switchImgReplaceTab(tab) {
    // 모든 탭 비활성화
    document.querySelectorAll('.img-replace-tab').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // 해당 탭 활성화
    document.querySelector('.img-replace-tab[data-tab="' + tab + '"]')?.classList.add('active');
    
    // 모든 콘텐츠 숨기기
    document.querySelectorAll('.img-replace-tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // 선택된 콘텐츠 표시
    document.getElementById('img-replace-' + tab)?.classList.add('active');
}

function handleImgReplaceDragover(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('img-replace-dropzone').classList.add('dragover');
}

function handleImgReplaceDragleave(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('img-replace-dropzone').classList.remove('dragover');
}

function handleImgReplaceDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('img-replace-dropzone').classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0 && files[0].type.startsWith('image/')) {
        uploadImgReplaceFile(files[0]);
    }
}

function handleImgReplaceFileSelect(e) {
    const files = e.target.files;
    if (files.length > 0) {
        uploadImgReplaceFile(files[0]);
    }
}

function uploadImgReplaceFile(file) {
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const UPLOAD_URL = '{{ route("admin.upload.image") }}';
    
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', CSRF_TOKEN);
    
    fetch(UPLOAD_URL, { method: 'POST', body: formData })
        .then(res => {
            if (!res.ok) throw new Error('업로드 실패');
            return res.json();
        })
        .then(data => {
            document.getElementById('img-replace-url-input').value = data.url;
            document.getElementById('img-replace-preview').src = data.url;
            document.getElementById('img-replace-preview').style.display = 'block';
            switchImgReplaceTab('url');
        })
        .catch(err => alert('이미지 업로드 실패: ' + err.message));
}

function confirmImgReplace() {
    const newUrl = document.getElementById('img-replace-url-input').value.trim();
    if (!newUrl) {
        alert('이미지 URL을 입력하세요');
        return;
    }
    
    if (currentImageElement) {
        const oldSrc = currentImageElement.src;
        currentImageElement.src = newUrl;
        
        const currentMode = document.getElementById('content-type-input').value || 'markdown';
        if (currentMode === 'html') {
            const htmlEditor = document.getElementById('html-editor');
            htmlEditor.value = htmlEditor.value.replace(oldSrc, newUrl);
            htmlEditor.dispatchEvent(new Event('input'));
        }
    }
    
    closeImgReplace();
}
</script>
