<input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
<div class="form-group">
    <label class="form-label">{{ $setting->label }}</label>
    @if($setting->type === 'textarea')
        <textarea name="settings[{{ $setting->key }}][value]" class="form-control"
                  style="min-height:{{ $setting->key === 'head_code' ? '140px' : '80px' }};{{ $setting->key === 'head_code' ? 'font-family:monospace;font-size:.82rem;' : '' }}"
                  placeholder="{{ $setting->key === 'head_code' ? '예: <meta name="theme-color" content="#4f46e5">' : '' }}">{{ old('settings.'.$setting->key.'.value', $setting->value) }}</textarea>
        @if($setting->key === 'head_code')
        <p style="font-size:.75rem;color:#94a3b8;margin-top:4px">모든 페이지 &lt;head&gt;에 삽입됩니다. &lt;meta&gt;, &lt;link&gt; 등 HTML 태그를 직접 입력하세요.</p>
        @endif
    @elseif($setting->type === 'number')
        <input type="number" name="settings[{{ $setting->key }}][value]" class="form-control"
               value="{{ old('settings.'.$setting->key.'.value', $setting->value) }}" min="1" max="50" style="max-width:120px">
    @elseif($setting->type === 'color')
        <div style="display:flex;align-items:center;gap:12px">
            <input type="color" name="settings[{{ $setting->key }}][value]" class="form-control"
                   value="{{ old('settings.'.$setting->key.'.value', $setting->value) }}" style="max-width:80px;height:38px;padding:2px 4px;cursor:pointer">
            <input type="text" id="color-text-{{ $setting->key }}"
                   value="{{ old('settings.'.$setting->key.'.value', $setting->value) }}"
                   style="width:110px;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:.85rem;font-family:monospace"
                   oninput="document.querySelector('[name=\'settings[{{ $setting->key }}][value]\']').value=this.value">
        </div>
        <script>
        document.querySelector('[name="settings[{{ $setting->key }}][value]"]').addEventListener('input', function(){
            document.getElementById('color-text-{{ $setting->key }}').value = this.value;
        });
        </script>
    @else
        <input type="text" name="settings[{{ $setting->key }}][value]" class="form-control"
               value="{{ old('settings.'.$setting->key.'.value', $setting->value) }}">
    @endif
</div>
