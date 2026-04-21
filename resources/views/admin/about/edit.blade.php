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
                    사용 시 푸터의 About 링크와 글 하단 소개 카드(카드 HTML 입력 시)가 노출됩니다.
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

            <div class="form-group">
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

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="about_card_html">글 하단 소개 카드 (HTML)</label>
                <textarea
                    id="about_card_html"
                    name="about_card_html"
                    class="form-control"
                    style="min-height:220px;font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;font-size:.83rem;"
                    placeholder='<a href="/about" style="display:block;padding:18px;border:1px solid #e2e8f0;border-radius:12px;text-decoration:none;color:inherit;"><strong>About</strong><p style="margin:8px 0 0;">소개 페이지 보러가기</p></a>'
                >{{ old('about_card_html', $aboutCardHtml) }}</textarea>
                <p style="font-size:.75rem;color:#94a3b8;margin-top:4px">
                    글 상세 하단에 그대로 렌더링됩니다. 디자인/링크를 직접 HTML로 작성하세요.
                </p>
                @error('about_card_html')
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
