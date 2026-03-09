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
    /* 에디터 컨테이너 */
    .editor-wrap { border: 1.5px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
    .editor-wrap:focus-within { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
    .toastui-editor-defaultUI { border: none !important; }
    .toastui-editor-toolbar { background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important; }
    .toastui-editor-mode-switch { background: #f8fafc !important; border-top: 1px solid #e2e8f0 !important; }
    .toastui-editor-defaultUI .ProseMirror { font-size: 1rem !important; line-height: 1.8 !important; font-family: 'Noto Sans KR', -apple-system, sans-serif !important; }
    .toastui-editor-contents p { margin-bottom: .8em; }
    /* 이미지 업로드 버튼 숨김 (서버 설정 전) */
    .toastui-editor-toolbar-icons.image { display: none; }
</style>

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

        {{-- 에디터 영역 --}}
        <div class="form-group">
            <label class="form-label" style="display:flex;align-items:center;justify-content:space-between">
                <span>내용 *</span>
                <span style="font-size:.72rem;color:#94a3b8;font-weight:400">
                    비주얼 / 마크다운 전환 가능 &nbsp;·&nbsp; 이미지 드래그&드롭 지원
                </span>
            </label>
            {{-- 실제 전송될 hidden input --}}
            <textarea name="content" id="content-hidden" style="display:none">{{ old('content', $post->content ?? '') }}</textarea>
            {{-- 에디터 마운트 포인트 --}}
            <div class="editor-wrap">
                <div id="toast-editor"></div>
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

        {{-- 발행 설정 --}}
        <div class="card">
            <div class="card-header" style="padding:12px 16px"><h3 style="font-size:.875rem">발행 설정</h3></div>
            <div class="card-body" style="padding:14px 16px">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.875rem;font-weight:500">
                    <input type="checkbox" name="published" value="1"
                           {{ old('published', $post->published ?? false) ? 'checked' : '' }}
                           style="width:16px;height:16px;accent-color:#4f46e5">
                    공개 게시
                </label>
                <p style="font-size:.75rem;color:#94a3b8;margin-top:6px;line-height:1.5">체크 해제 시 임시저장</p>
            </div>
        </div>

        {{-- 카테고리 --}}
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

        {{-- 마크다운 치트시트 --}}
        <div class="card" style="background:#f8fafc">
            <div class="card-header" style="padding:10px 16px;border-bottom:1px solid #f1f5f9">
                <h3 style="font-size:.78rem;color:#64748b">마크다운 단축키</h3>
            </div>
            <div class="card-body" style="padding:12px 16px;font-size:.75rem;color:#64748b;line-height:2">
                <div><kbd style="background:#e2e8f0;padding:1px 5px;border-radius:3px;font-size:.7rem">##</kbd> 소제목</div>
                <div><kbd style="background:#e2e8f0;padding:1px 5px;border-radius:3px;font-size:.7rem">**굵게**</kbd> 굵은 글씨</div>
                <div><kbd style="background:#e2e8f0;padding:1px 5px;border-radius:3px;font-size:.7rem">- 항목</kbd> 목록</div>
                <div><kbd style="background:#e2e8f0;padding:1px 5px;border-radius:3px;font-size:.7rem">`코드`</kbd> 인라인 코드</div>
                <div><kbd style="background:#e2e8f0;padding:1px 5px;border-radius:3px;font-size:.7rem">> 인용</kbd> 인용구</div>
            </div>
        </div>
    </div>
</div>

{{-- Toast UI Editor JS --}}
<script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
<script>
(function () {
    const hiddenTextarea = document.getElementById('content-hidden');
    const initialValue   = hiddenTextarea.value;

    const editor = new toastui.Editor({
        el: document.getElementById('toast-editor'),
        height: '560px',
        initialEditType: 'wysiwyg',   // 기본값: 비주얼 에디터
        previewStyle: 'tab',
        initialValue: initialValue,
        language: 'ko-KR',
        toolbarItems: [
            ['heading', 'bold', 'italic', 'strike'],
            ['hr', 'quote'],
            ['ul', 'ol', 'task'],
            ['table', 'link'],
            ['code', 'codeblock'],
        ],
        placeholder: '내용을 입력하세요...',
        events: {
            change: function () {
                // 실시간으로 hidden textarea 동기화
                hiddenTextarea.value = editor.getMarkdown();
            }
        }
    });

    // 폼 제출 시 마크다운으로 동기화
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function () {
            hiddenTextarea.value = editor.getMarkdown();
        });
    }

    // 제목으로 에디터 높이 포커스 이동
    document.getElementById('post-title').addEventListener('keydown', function (e) {
        if (e.key === 'Tab') {
            e.preventDefault();
            editor.focus();
        }
    });
})();
</script>
