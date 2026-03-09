@if($errors->any())
<div class="alert alert-danger">
    <ul style="list-style:none">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 240px;gap:20px;align-items:start">
    <div>
        <div class="form-group">
            <label class="form-label">제목 *</label>
            <input type="text" name="title" class="form-control"
                   value="{{ old('title', $post->title ?? '') }}"
                   placeholder="글 제목을 입력하세요" required>
        </div>

        <div class="form-group">
            <label class="form-label">
                내용 *
                <span class="hint" style="font-weight:400;color:#94a3b8;font-size:.75rem;margin-left:6px">
                    마크다운 지원 — <code style="background:#f1f5f9;padding:1px 5px;border-radius:3px;font-size:.75rem">## 제목</code>
                    <code style="background:#f1f5f9;padding:1px 5px;border-radius:3px;font-size:.75rem">**굵게**</code>
                    <code style="background:#f1f5f9;padding:1px 5px;border-radius:3px;font-size:.75rem">- 목록</code>
                    <code style="background:#f1f5f9;padding:1px 5px;border-radius:3px;font-size:.75rem">`코드`</code>
                </span>
            </label>
            <textarea name="content" class="form-control" style="min-height:480px;font-family:'JetBrains Mono','Fira Code',monospace;font-size:.875rem;line-height:1.7"
                      placeholder="마크다운 문법으로 글을 작성하세요..." required>{{ old('content', $post->content ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">요약 <span class="hint">(목록에 표시됩니다, 선택)</span></label>
            <input type="text" name="excerpt" class="form-control"
                   value="{{ old('excerpt', $post->excerpt ?? '') }}"
                   placeholder="간단한 글 요약">
        </div>
    </div>

    <div style="position:sticky;top:80px">
        <div class="card" style="margin-bottom:12px">
            <div class="card-header" style="padding:12px 16px"><h3 style="font-size:.875rem">발행 설정</h3></div>
            <div class="card-body" style="padding:14px 16px">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.875rem;font-weight:500">
                    <input type="checkbox" name="published" value="1"
                           {{ old('published', $post->published ?? false) ? 'checked' : '' }}
                           style="width:16px;height:16px;accent-color:#4f46e5">
                    공개 게시
                </label>
                <p style="font-size:.75rem;color:#94a3b8;margin-top:6px">체크 해제 시 임시저장 상태</p>
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
                   style="font-size:.75rem;color:#6366f1;display:block;margin-top:8px">
                    카테고리 관리 →
                </a>
            </div>
        </div>
    </div>
</div>
