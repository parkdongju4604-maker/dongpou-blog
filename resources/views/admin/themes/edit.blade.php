@extends('layouts.admin')
@section('title', '테마 편집 — ' . $theme->name)

@section('content')
{{-- CodeMirror CSS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/dracula.min.css">

<style>
.edit-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.edit-header h1 { font-size: 1.2rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px; }
.edit-header h1 .theme-dot { width: 12px; height: 12px; border-radius: 3px; display: inline-block; }
.edit-actions { display: flex; gap: 8px; align-items: center; }

.meta-row { display: grid; grid-template-columns: 1fr 1fr auto; gap: 12px; margin-bottom: 16px; align-items: end; }
@media (max-width: 640px) { .meta-row { grid-template-columns: 1fr; } }

.field-group { display: flex; flex-direction: column; gap: 5px; }
.field-label { font-size: .75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
.field-input { padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: .9rem; outline: none; transition: border .15s; }
.field-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.08); }

/* CodeMirror 래퍼 */
.editor-container {
    border: 1.5px solid #e2e8f0; border-radius: 10px; overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.editor-toolbar {
    background: #1e293b; padding: 8px 14px;
    display: flex; align-items: center; justify-content: space-between;
    font-size: .75rem; color: #64748b;
}
.editor-toolbar .file-name { color: #94a3b8; display: flex; align-items: center; gap: 6px; }
.editor-toolbar .shortcuts { display: flex; gap: 16px; }
.editor-toolbar kbd { background: #334155; color: #94a3b8; padding: 1px 5px; border-radius: 3px; font-size: .7rem; }
.CodeMirror { height: 600px; font-size: .875rem; font-family: 'JetBrains Mono','Fira Code','Cascadia Code',monospace; line-height: 1.65; }
.CodeMirror-scroll { padding-bottom: 20px; }

.editor-footer { background: #1e293b; padding: 6px 14px; display: flex; justify-content: space-between; align-items: center; }
.char-count { font-size: .7rem; color: #475569; }
.cursor-pos { font-size: .7rem; color: #475569; }

.btn-save { background: #4f46e5; color: #fff; padding: 9px 22px; border-radius: 8px; font-size: .875rem; font-weight: 600; border: none; cursor: pointer; transition: background .15s; display: inline-flex; align-items: center; gap: 6px; }
.btn-save:hover { background: #4338ca; }
.btn-back { background: #f1f5f9; color: #475569; padding: 9px 16px; border-radius: 8px; font-size: .875rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; border: none; cursor: pointer; }
.btn-back:hover { background: #e2e8f0; }
.btn-activate { background: #059669; color: #fff; padding: 9px 18px; border-radius: 8px; font-size: .875rem; font-weight: 600; border: none; cursor: pointer; transition: background .15s; }
.btn-activate:hover { background: #047857; }

.success-banner { background: #f0fdf4; border: 1px solid #86efac; color: #16a34a; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: .875rem; font-weight: 600; }
</style>

<form method="POST" action="{{ route('admin.themes.update', $theme) }}" id="theme-form">
    @csrf @method('PUT')

    <div class="edit-header">
        <h1>
            <span class="theme-dot" style="background:{{ $theme->preview_color }};"></span>
            {{ $theme->name }}
            @if($theme->is_active)
                <span style="font-size:.65rem;background:#4f46e5;color:#fff;padding:2px 8px;border-radius:99px;">ACTIVE</span>
            @endif
        </h1>
        <div class="edit-actions">
            <a href="{{ route('admin.themes.index') }}" class="btn-back">← 목록</a>
            @if(!$theme->is_active)
                <form method="POST" action="{{ route('admin.themes.activate', $theme) }}" style="display:inline;" id="activate-form">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-activate">🎨 활성화</button>
                </form>
            @endif
            <button type="submit" class="btn-save" form="theme-form">💾 저장</button>
        </div>
    </div>

    @if(session('success'))
        <div class="success-banner">✅ {{ session('success') }}</div>
    @endif

    {{-- 메타 정보 --}}
    <div class="meta-row">
        <div class="field-group">
            <label class="field-label" for="name">테마 이름</label>
            <input type="text" id="name" name="name" class="field-input" value="{{ old('name', $theme->name) }}" required>
        </div>
        <div class="field-group">
            <label class="field-label" for="description">설명</label>
            <input type="text" id="description" name="description" class="field-input" value="{{ old('description', $theme->description) }}" placeholder="테마 설명">
        </div>
        <div class="field-group">
            <label class="field-label" for="preview_color">대표 색상</label>
            <input type="color" id="preview_color" name="preview_color" class="field-input" value="{{ old('preview_color', $theme->preview_color) }}" style="height:42px;cursor:pointer;padding:4px 6px;">
        </div>
    </div>

    {{-- CodeMirror CSS 에디터 --}}
    <div class="editor-container">
        <div class="editor-toolbar">
            <div class="file-name">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="#64748b"><circle cx="12" cy="12" r="10"/></svg>
                style.css
            </div>
            <div class="shortcuts">
                <span><kbd>Ctrl+S</kbd> 저장</span>
                <span><kbd>Ctrl+Z</kbd> 실행취소</span>
                <span><kbd>Ctrl+/</kbd> 주석</span>
                <span><kbd>Ctrl+F</kbd> 찾기</span>
            </div>
        </div>
        <textarea name="css" id="css-editor">{{ old('css', $theme->css) }}</textarea>
        <div class="editor-footer">
            <span class="char-count" id="char-count">0 chars</span>
            <span class="cursor-pos" id="cursor-pos">Ln 1, Col 1</span>
        </div>
    </div>
</form>

{{-- CodeMirror JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/search/searchcursor.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/dialog/dialog.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/search/search.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/comment/comment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/matchbrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closebrackets.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/dialog/dialog.min.css">

<script>
(function () {
    const textarea = document.getElementById('css-editor');
    const charCount = document.getElementById('char-count');
    const cursorPos = document.getElementById('cursor-pos');

    const editor = CodeMirror.fromTextArea(textarea, {
        mode: 'css',
        theme: 'dracula',
        lineNumbers: true,
        matchBrackets: true,
        autoCloseBrackets: true,
        indentUnit: 4,
        tabSize: 4,
        indentWithTabs: false,
        lineWrapping: false,
        extraKeys: {
            'Ctrl-/': 'toggleComment',
            'Ctrl-S': function (cm) {
                cm.save();
                document.getElementById('theme-form').submit();
            },
        },
    });

    function updateInfo() {
        const val = editor.getValue();
        charCount.textContent = val.length.toLocaleString() + ' chars';
        const cur = editor.getCursor();
        cursorPos.textContent = `Ln ${cur.line + 1}, Col ${cur.ch + 1}`;
    }

    editor.on('change', updateInfo);
    editor.on('cursorActivity', updateInfo);
    updateInfo();

    // Ctrl+S 저장
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            editor.save();
            document.getElementById('theme-form').submit();
        }
    });
})();
</script>
@endsection
