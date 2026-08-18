@php
    $fieldName = $name ?? 'mobile_number';
    $codeFieldName = $codeName ?? ($fieldName . '_country_code');
    $fieldId = $id ?? $fieldName;
    $codeFieldId = $codeId ?? ($fieldId . '_country_code');
    $fieldLabel = $label ?? 'Phone Number';
    $fieldIcon = $icon ?? 'bi-telephone';
    $placeholder = $placeholder ?? '98765 43210';
    $hint = $hint ?? null;
    $required = $required ?? false;
    $defaultCode = $defaultCode ?? '+91';
@endphp

<div class="form-field">
    <label for="{{ $fieldId }}" class="form-label">
        {{ $fieldLabel }} @if($required)<span>*</span>@endif
    </label>
    <div class="phone-input-group">
        <div class="phone-prefix-dropdown" data-code-dropdown>
            <input type="hidden" name="{{ $codeFieldName }}" id="{{ $codeFieldId }}" value="{{ $defaultCode }}">
            <button type="button" class="phone-prefix-btn" aria-haspopup="listbox" aria-expanded="false">
                <span class="phone-prefix-val">{{ $defaultCode }}</span>
                <i class="bi bi-chevron-down phone-prefix-chevron"></i>
            </button>
            <div class="phone-prefix-menu" role="listbox">
                <div class="phone-prefix-list">
                    @forelse($countryCodes ?? [] as $code)
                        <button type="button" 
                                class="phone-prefix-item {{ $code->dial_code === $defaultCode ? 'is-selected' : '' }}" 
                                data-code="{{ $code->dial_code }}" 
                                data-name="{{ $code->name }}"
                                title="{{ $code->name }}"
                                role="option">
                            <span class="phone-prefix-item-code">{{ $code->dial_code }}</span>
                        </button>
                    @empty
                        <div style="padding: 10px; text-align: center; color: #999;">No countries</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="phone-number-box">
            <span class="form-field-icon"><i class="bi {{ $fieldIcon }}"></i></span>
            <input type="tel" name="{{ $fieldName }}" id="{{ $fieldId }}" class="form-control"
                placeholder="{{ $placeholder }}" maxlength="20" @if($required) required @endif>
        </div>
    </div>
    @if($hint)
        <span class="form-field-hint">{{ $hint }}</span>
    @endif
</div>
