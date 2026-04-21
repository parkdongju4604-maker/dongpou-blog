@php
    use App\Models\Setting;
    use Illuminate\Support\Str;

    $blogName = Setting::get('blog_name', config('app.name', 'Blog'));
    $plainAbout = trim(strip_tags($aboutHtml));
    $metaDescription = $plainAbout !== '' ? Str::limit($plainAbout, 150) : $blogName . ' 소개 페이지';
@endphp

@extends('layouts.app')

@section('title', $aboutTitle . ' | ' . $blogName)
@section('description', $metaDescription)
@section('canonical', route('about.show'))

@push('styles')
<style>
.about-wrap {
    max-width: 860px;
    margin: 0 auto;
    padding: 44px 0 72px;
}
.about-header {
    margin-bottom: 22px;
}
.about-title {
    font-size: clamp(1.7rem, 4vw, 2.3rem);
    font-weight: 800;
    letter-spacing: -0.03em;
    color: #0f172a;
}
.about-content {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 26px 24px;
    line-height: 1.8;
    color: #334155;
    word-break: break-word;
}
.about-content h1,
.about-content h2,
.about-content h3,
.about-content h4 {
    color: #0f172a;
    margin: 20px 0 10px;
    line-height: 1.35;
}
.about-content p { margin: 0 0 14px; }
.about-content ul,
.about-content ol { margin: 0 0 14px 20px; }
.about-content a {
    color: var(--primary, #4f46e5);
    text-decoration: underline;
    text-underline-offset: 3px;
}
.about-empty {
    text-align: center;
    color: #64748b;
    font-size: 0.95rem;
    padding: 34px 14px;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    background: #f8fafc;
}
</style>
@endpush

@section('content')
<section class="about-wrap">
    <header class="about-header">
        <h1 class="about-title">{{ $aboutTitle }}</h1>
    </header>

    @if(trim($aboutHtml) !== '')
    <article class="about-content">
        {!! $aboutHtml !!}
    </article>
    @else
    <div class="about-empty">
        아직 소개 내용이 등록되지 않았습니다.
    </div>
    @endif
</section>
@endsection
