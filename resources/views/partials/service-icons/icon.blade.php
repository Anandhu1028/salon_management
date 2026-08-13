@php
    use App\Support\ServiceIconResolver;

    $iconKey = ServiceIconResolver::normalize($key ?? 'default');
    $sizeClass = $size ?? 'md';
    $extraClass = $class ?? '';
    $interactive = $interactive ?? false;
@endphp

<span
    class="svc-icon svc-icon--{{ $sizeClass }} {{ $extraClass }}"
    data-icon="{{ $iconKey }}"
    @if($interactive) role="img" @endif
    aria-hidden="{{ ($ariaLabel ?? null) ? 'false' : 'true' }}"
    @if($ariaLabel ?? null) aria-label="{{ $ariaLabel }}" @endif
>
    <span class="svc-icon__box">
        @includeFirst([
            'partials.service-icons.icons.' . $iconKey,
            'partials.service-icons.icons.default',
        ])
    </span>
</span>
