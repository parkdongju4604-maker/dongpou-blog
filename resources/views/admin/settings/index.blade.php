@extends('layouts.admin')
@section('title', '사이트 설정')
@section('page-title', '사이트 설정')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}">
@csrf

{{-- 탭 네비게이션 --}}
<div class="tab-nav">
    <button type="button" class="tab-btn active" data-tab="general">기본 설정</button>
    <button type="button" class="tab-btn" data-tab="appearance">디자인</button>
    <button type="button" class="tab-btn" data-tab="seo">SEO</button>
</div>

@php
    $groupLabels = ['general' => '기본 설정', 'appearance' => '디자인', 'seo' => 'SEO'];
    $groupIds    = ['general' => 'general', 'appearance' => 'appearance', 'seo' => 'seo'];
    $allSettings = $settings->flatten()->keyBy('key');
@endphp

{{-- 기본 설정 탭 --}}
<div id="tab-general" class="tab-panel active">
    <div class="card">
        <div class="card-header"><h3>기본 설정</h3></div>
        <div class="card-body">
            @foreach(($settings['general'] ?? collect()) as $i => $setting)
            <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
            <div class="form-group">
                <label class="form-label">
                    {{ $setting->label }}
                    @if($setting->key === 'posts_per_page')
                        <span class="hint">(숫자)</span>
                    @endif
                </label>
                @if($setting->type === 'textarea')
                    <textarea name="settings[{{ $setting->key }}][value]" class="form-control">{{ old('settings.'.$setting->key.'.value', $setting->value) }}</textarea>
                @elseif($setting->type === 'number')
                    <input type="number" name="settings[{{ $setting->key }}][value]" class="form-control"
                           value="{{ old('settings.'.$setting->key.'.value', $setting->value) }}" min="1" max="50">
                @else
                    <input type="text" name="settings[{{ $setting->key }}][value]" class="form-control"
                           value="{{ old('settings.'.$setting->key.'.value', $setting->value) }}">
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- 디자인 탭 --}}
<div id="tab-appearance" class="tab-panel">
    <div class="card">
        <div class="card-header"><h3>디자인</h3></div>
        <div class="card-body">
            @foreach(($settings['appearance'] ?? collect()) as $setting)
            <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
            <div class="form-group">
                <label class="form-label">{{ $setting->label }}</label>
                @if($setting->type === 'color')
                    <div style="display:flex;align-items:center;gap:12px">
                        <input type="color" name="settings[{{ $setting->key }}][value]" class="form-control"
                               value="{{ old('settings.'.$setting->key.'.value', $setting->value) }}">
                        <span style="font-size:.85rem;color:#64748b">선택한 색상이 포인트 컬러로 적용됩니다.</span>
                    </div>
                @else
                    <input type="text" name="settings[{{ $setting->key }}][value]" class="form-control"
                           value="{{ old('settings.'.$setting->key.'.value', $setting->value) }}">
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- SEO 탭 --}}
<div id="tab-seo" class="tab-panel">
    <div class="card">
        <div class="card-header"><h3>SEO 설정</h3></div>
        <div class="card-body">
            @foreach(($settings['seo'] ?? collect()) as $setting)
            <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
            <div class="form-group">
                <label class="form-label">{{ $setting->label }}</label>
                @if($setting->type === 'textarea')
                    <textarea name="settings[{{ $setting->key }}][value]" class="form-control">{{ old('settings.'.$setting->key.'.value', $setting->value) }}</textarea>
                @else
                    <input type="text" name="settings[{{ $setting->key }}][value]" class="form-control"
                           value="{{ old('settings.'.$setting->key.'.value', $setting->value) }}"
                           placeholder="{{ $setting->key === 'google_analytics' ? 'G-XXXXXXXXXX' : '' }}">
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- 저장 버튼 --}}
<div style="margin-top:20px;display:flex;justify-content:flex-end">
    <button type="submit" class="btn btn-primary" style="padding:10px 28px">설정 저장</button>
</div>
</form>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
    });
});
</script>
@endpush
