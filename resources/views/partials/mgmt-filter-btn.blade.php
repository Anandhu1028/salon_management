{{--
    Management list filter dropdown
    @param string $route
    @param array $options   key => label
    @param string $filter   current filter value
    @param string|null $search
    @param string $param     query param name (default: filter)
--}}

@php
    $param = $param ?? 'filter';
    $filter = $filter ?? '';
    $search = $search ?? '';
    $isActive = $filter !== '' && $filter !== null;
    $activeLabel = $options[$filter] ?? ($options[''] ?? 'All');
@endphp

<div class="dropdown mgmt-filter">
    <button
        type="button"
        class="mgmt-action-btn mgmt-action-btn--filter {{ $isActive ? 'is-active' : '' }}"
        data-bs-toggle="dropdown"
        data-bs-auto-close="true"
        aria-expanded="false"
        title="Filter list"
    >
        <span class="mgmt-action-btn__icon" aria-hidden="true">
            <i class="bi bi-funnel-fill"></i>
        </span>
        <span class="mgmt-action-btn__label">Filter</span>
        @if($isActive)
            <span class="mgmt-filter-badge">{{ $activeLabel }}</span>
        @endif
    </button>

    <ul class="dropdown-menu dropdown-menu-end mgmt-filter-menu">
        <li class="dropdown-header">Filter by</li>
        @foreach($options as $value => $label)
            @php
                $query = array_filter([
                    $param => $value !== '' ? $value : null,
                    'search' => $search !== '' ? $search : null,
                ], fn ($v) => $v !== null && $v !== '');
            @endphp
            <li>
                <a
                    class="dropdown-item {{ (string) $filter === (string) $value ? 'active' : '' }}"
                    href="{{ $route . ($query ? '?' . http_build_query($query) : '') }}"
                >
                    @if((string) $filter === (string) $value)
                        <i class="bi bi-check2"></i>
                    @else
                        <i class="bi bi-circle"></i>
                    @endif
                    {{ $label }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
