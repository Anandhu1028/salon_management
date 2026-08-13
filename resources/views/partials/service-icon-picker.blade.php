{{-- Service Icon Auto-Suggestion Picker (Add / Edit Service modal) --}}
<div class="svc-icon-picker" id="serviceIconPicker">
    <input type="hidden" name="icon" id="service_icon" value="{{ old('icon', 'default') }}">

    <div class="svc-icon-picker__header">
        <div>
            <label class="form-label svc-icon-picker__label">Service Icon</label>
            <p class="svc-icon-picker__hint mb-0">Icon is suggested automatically from the service name.</p>
        </div>
        <span class="svc-icon-picker__badge" id="serviceIconSuggestedBadge">Suggested</span>
    </div>

    <div class="svc-icon-picker__preview-row">
        <div class="svc-icon-picker__preview" id="serviceIconPreview">
            @include('partials.service-icons.icon', ['key' => old('icon', 'default'), 'size' => 'xl'])
        </div>
        <div class="svc-icon-picker__meta">
            <span class="svc-icon-picker__icon-name" id="serviceIconLabel">Salon Service</span>
            <span class="svc-icon-picker__icon-category" id="serviceIconCategoryHint"></span>
        </div>
    </div>

    <div class="svc-icon-picker__alternatives-wrap" id="serviceIconAlternativesWrap">
        <span class="svc-icon-picker__alt-label">Alternatives</span>
        <div class="svc-icon-picker__alternatives" id="serviceIconAlternatives" role="listbox" aria-label="Alternative service icons"></div>
    </div>
</div>

<div id="serviceIconTemplates" class="d-none" aria-hidden="true">
    @foreach(\App\Support\ServiceIconResolver::validKeys() as $iconKey)
        <template data-icon="{{ $iconKey }}">
            @include('partials.service-icons.icon', ['key' => $iconKey, 'size' => 'lg'])
        </template>
    @endforeach
</div>

<script>
    window.ServiceIconConfig = @json(\App\Support\ServiceIconResolver::exportForFrontend());
</script>
