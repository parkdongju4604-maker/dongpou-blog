@extends('layouts.admin')
@section('title', 'About 페이지')
@section('page-title', 'About 페이지')

@section('content')
<form method="POST" action="{{ route('admin.about.update') }}">
    @csrf

    <div class="card" style="margin-bottom:16px">
        <div class="card-header">
            <h3>About 페이지 설정</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label" for="about_enabled">사용 여부</label>
                <label style="display:inline-flex;align-items:center;gap:8px;font-size:.9rem;color:#334155;">
                    <input type="hidden" name="about_enabled" value="0">
                    <input
                        type="checkbox"
                        id="about_enabled"
                        name="about_enabled"
                        value="1"
                        {{ old('about_enabled', $aboutEnabled ? '1' : '0') === '1' ? 'checked' : '' }}
                    >
                    사용
                </label>
                <p style="font-size:.78rem;color:#64748b;margin-top:6px;">
                    사용 시 푸터의 About 링크와 글 하단 소개 카드가 함께 노출됩니다.
                </p>
            </div>

            <div class="form-group">
                <label class="form-label" for="about_title">페이지 제목</label>
                <input
                    id="about_title"
                    type="text"
                    name="about_title"
                    class="form-control"
                    maxlength="120"
                    value="{{ old('about_title', $aboutTitle) }}"
                    placeholder="예: About Me"
                >
                @error('about_title')
                <p style="font-size:.78rem;color:#dc2626;margin-top:6px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="about_html">내용 (HTML)</label>
                <textarea
                    id="about_html"
                    name="about_html"
                    class="form-control"
                    style="min-height:320px;font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;font-size:.83rem;"
                    placeholder="<h2>안녕하세요</h2><p>About 페이지에 표시할 내용을 HTML로 입력하세요.</p>"
                >{{ old('about_html', $aboutHtml) }}</textarea>
                @error('about_html')
                <p style="font-size:.78rem;color:#dc2626;margin-top:6px;">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;">
        <button type="submit" class="btn btn-primary">저장</button>
    </div>
</form>
@endsection
