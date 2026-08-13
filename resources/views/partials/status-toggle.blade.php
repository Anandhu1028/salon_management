{{--
    Premium Active / Inactive toggle
    @param int|string $id
    @param string $status  active|inactive
    @param string $onChange  JS handler e.g. onStaffStatusToggle(1, 'Name', this)
--}}

@php
    $isActive = ($status ?? 'active') === 'active';
    $toggleId = 'status-toggle-' . ($id ?? uniqid());
@endphp

<label class="mgmt-status-toggle" for="{{ $toggleId }}" title="{{ $isActive ? 'Active — switch to inactive' : 'Inactive — switch to active' }}">
    <input
        type="checkbox"
        id="{{ $toggleId }}"
        class="mgmt-status-toggle__input"
        {{ $isActive ? 'checked' : '' }}
        onchange="{{ $onChange }}"
        aria-label="Toggle active status"
    >
    <span class="mgmt-status-toggle__track" aria-hidden="true">
        <span class="mgmt-status-toggle__thumb"></span>
    </span>
    
</label>
