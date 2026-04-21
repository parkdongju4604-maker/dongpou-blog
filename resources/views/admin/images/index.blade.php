@extends('layouts.admin')
@section('title', '이미지 관리')
@section('page-title', '이미지 관리')

@push('styles')
<style>
.image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 14px;
}
.image-item {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
}
.image-thumb-wrap {
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.image-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.image-meta {
    padding: 12px;
}
.image-name {
    font-size: .82rem;
    color: #0f172a;
    font-weight: 700;
    margin-bottom: 6px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.image-sub {
    font-size: .75rem;
    color: #64748b;
    margin-bottom: 8px;
}
.image-url-input {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 7px 9px;
    font-size: .74rem;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    color: #334155;
    margin-bottom: 8px;
}
.image-actions {
    display: flex;
    gap: 6px;
    align-items: center;
}
.upload-help {
    margin-top: 8px;
    font-size: .76rem;
    color: #64748b;
    line-height: 1.6;
}
</style>
@endpush

@section('content')
<div class="card" style="margin-bottom:16px">
    <div class="card-header">
        <h3>이미지 업로드</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.images.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group" style="margin-bottom:10px">
                <label class="form-label" for="images">이미지 파일</label>
                <input id="images" type="file" name="images[]" class="form-control" accept="image/*" multiple required>
            </div>
            @error('images')
            <p style="font-size:.78rem;color:#dc2626;margin-bottom:8px">{{ $message }}</p>
            @enderror
            @error('images.*')
            <p style="font-size:.78rem;color:#dc2626;margin-bottom:8px">{{ $message }}</p>
            @enderror
            <button type="submit" class="btn btn-primary">업로드</button>
            <p class="upload-help">
                업로드 파일은 `storage/app/public/uploads/library/YYYY/MM` 경로에 저장됩니다.<br>
                목록의 URL은 About 카드/본문 HTML에서 바로 사용할 수 있습니다.
            </p>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>서버 이미지 목록</h3>
        <span class="badge badge-purple">총 {{ number_format($totalImages) }}개</span>
    </div>
    <div class="card-body">
        @if($images->isEmpty())
            <p style="color:#64748b;font-size:.9rem">업로드된 이미지가 없습니다.</p>
        @else
            <div class="image-grid">
                @foreach($images as $image)
                    <article class="image-item">
                        <div class="image-thumb-wrap">
                            <img src="{{ $image['url'] }}" alt="{{ $image['name'] }}" class="image-thumb" loading="lazy">
                        </div>
                        <div class="image-meta">
                            <p class="image-name" title="{{ $image['name'] }}">{{ $image['name'] }}</p>
                            <p class="image-sub">{{ $image['size_kb'] }} KB · {{ date('Y-m-d H:i', $image['updated_at']) }}</p>

                            <input class="image-url-input js-image-url" type="text" readonly value="{{ $image['full_url'] }}">
                            <p class="image-sub" style="margin-top:-3px;word-break:break-all">{{ $image['path'] }}</p>

                            <div class="image-actions">
                                <button type="button" class="btn btn-secondary btn-sm js-copy-url">URL 복사</button>
                                <form action="{{ route('admin.images.destroy') }}" method="POST" onsubmit="return confirm('이 이미지를 삭제할까요?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="path" value="{{ $image['path'] }}">
                                    <button type="submit" class="btn btn-danger btn-sm">삭제</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top:18px">
                {{ $images->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.js-copy-url').forEach(function(button) {
    button.addEventListener('click', function() {
        var input = this.closest('.image-meta')?.querySelector('.js-image-url');
        if (!input) return;

        var value = input.value;
        (navigator.clipboard ? navigator.clipboard.writeText(value) : Promise.reject())
            .catch(function() {
                input.focus();
                input.select();
                document.execCommand('copy');
            });

        var original = this.textContent;
        this.textContent = '복사됨';
        this.disabled = true;
        var btn = this;
        setTimeout(function() {
            btn.textContent = original;
            btn.disabled = false;
        }, 1200);
    });
});
</script>
@endpush
