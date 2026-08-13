{{--
    Premium management KPI stat card
    @param string $theme       indigo|green|red|blue|violet|orange|pink|cyan
    @param string $icon        kpi-3d-icons type
    @param string $label
    @param string|int $value
    @param string $subtext
    @param string|null $sparkColor
    @param string|null $trend
    @param bool|null $trendUp
    @param array|null $sparkBars
--}}

@php
    $sparkColor = $sparkColor ?? '#6366F1';
    $trendUp = $trendUp ?? true;

    if (!empty($sparkBars) && is_array($sparkBars)) {
        $bars = array_slice($sparkBars, 0, 10);
    } else {
        $seed = crc32(($label ?? '') . ($value ?? 0));
        $bars = [];
        for ($i = 0; $i < 10; $i++) {
            $bars[] = 35 + (($seed >> ($i % 8)) & 0x3F);
        }
    }

    $barCount = count($bars);
    $barWidth = 3.5;
    $barGap = 2.5;
    $chartWidth = ($barCount * $barWidth) + (($barCount - 1) * $barGap);
    $chartHeight = 32;
    $gradId = 'spark-' . md5(($label ?? '') . ($theme ?? ''));
@endphp

<div class="mgmt-stat-card {{ $theme ?? 'indigo' }}">
    <div class="mgmt-stat-accent" aria-hidden="true"></div>
    <div class="mgmt-stat-glow" aria-hidden="true"></div>
    <div class="mgmt-stat-shine" aria-hidden="true"></div>

    <span class="mgmt-stat-dot" aria-hidden="true"></span>

    @if(isset($trend) && $trend !== '')
        <span class="mgmt-stat-trend {{ $trendUp ? 'is-up' : 'is-down' }}">
            <span class="mgmt-stat-trend-arrow" aria-hidden="true">{{ $trendUp ? '↑' : '↓' }}</span>
            {{ $trend }}%
        </span>
    @endif

    <div class="mgmt-stat-header">
        <div class="mgmt-stat-icon-wrap">
            <span class="mgmt-stat-icon-ring" aria-hidden="true"></span>
            <div class="mgmt-stat-icon-circle">
                @include('partials.kpi-3d-icons', ['type' => $icon, 'size' => 'kpi-3d-icon--sm'])
            </div>
        </div>
        <span class="mgmt-stat-label">{{ $label }}</span>
    </div>

    <div class="mgmt-stat-body">
        <span class="mgmt-stat-value">{{ $value }}</span>

        @if(!empty($subtext))
            <span class="mgmt-stat-sub">{{ $subtext }}</span>
        @endif
    </div>

    <svg
        class="mgmt-stat-sparkline"
        width="{{ $chartWidth }}"
        height="{{ $chartHeight }}"
        viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}"
        aria-hidden="true"
    >
        <defs>
            <linearGradient id="{{ $gradId }}" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="{{ $sparkColor }}" stop-opacity="1"/>
                <stop offset="100%" stop-color="{{ $sparkColor }}" stop-opacity="0.35"/>
            </linearGradient>
        </defs>
        @foreach($bars as $i => $height)
            @php
                $normalized = max(20, min(100, (int) $height));
                $barH = ($normalized / 100) * $chartHeight;
                $x = $i * ($barWidth + $barGap);
                $y = $chartHeight - $barH;
            @endphp
            <rect
                x="{{ $x }}"
                y="{{ $y }}"
                width="{{ $barWidth }}"
                height="{{ $barH }}"
                rx="2"
                fill="url(#{{ $gradId }})"
                opacity="{{ number_format(0.5 + ($i / max(1, $barCount - 1)) * 0.5, 2, '.', '') }}"
            />
        @endforeach
    </svg>
</div>
