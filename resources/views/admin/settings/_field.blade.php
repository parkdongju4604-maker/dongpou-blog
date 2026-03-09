<input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
<div class="form-group">
    <label class="form-label">{{ $setting->label }}</label>
    @if($setting->type === 'textarea')
        <textarea name="settings[{{ $setting->key }}][value]" class="form-control" style="min-height:80px">{{ old('settings.'.$setting->key.'.value', $setting->value) }}</textarea>
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
