@if($errors->any())
<div class="alert" style="background:#fee2e2;color:#991b1b;margin-bottom:20px">
    <ul style="list-style:none">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 200px;gap:20px">
    <div>
        <div class="form-group">
            <label>제목 *</label>
            <input type="text" name="title" class="form-control"
                   value="{{ old('title', $post->title ?? '') }}"
                   placeholder="글 제목을 입력하세요" required>
        </div>

        <div class="form-group">
            <label>내용 *</label>
            <textarea name="content" class="form-control"
                      placeholder="글 내용을 입력하세요" required>{{ old('content', $post->content ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label>요약 (목록에 표시됩니다)</label>
            <input type="text" name="excerpt" class="form-control"
                   value="{{ old('excerpt', $post->excerpt ?? '') }}"
                   placeholder="간단한 글 요약 (선택)">
        </div>
    </div>

    <div>
        <div class="form-group">
            <label>카테고리 *</label>
            <input type="text" name="category" class="form-control"
                   value="{{ old('category', $post->category ?? '일반') }}"
                   list="categories" required>
            <datalist id="categories">
                <option value="일반">
                <option value="개발">
                <option value="마케팅">
                <option value="SEO">
                <option value="일상">
                <option value="리뷰">
            </datalist>
        </div>

        <div class="form-group" style="margin-top:16px">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="checkbox" name="published" value="1"
                       {{ old('published', $post->published ?? false) ? 'checked' : '' }}
                       style="width:16px;height:16px">
                공개 게시
            </label>
        </div>
    </div>
</div>
