{{--
    Dashboard KPI mini vertical bar chart (5 months)
    @param string $color     Primary bar color
    @param string $gradId    Unique SVG gradient id
    @param array  $bars       Bar heights as % of chart height (0–100)
--}}
@php
    $color = $color ?? '#8B5CF6';
    $gradId = $gradId ?? 'kpiBarGrad';
    $bars = $bars ?? [55, 70, 50, 75, 100];

    $barWidth = 14;
    $barGap = 10;
    $chartH = 60;
    $count = count($bars);
    $chartW = ($count * $barWidth) + (max(0, $count - 1) * $barGap);
@endphp

<svg
    class="stat-bar-chart"
    viewBox="0 0 {{ $chartW }} {{ $chartH }}"
    width="{{ $chartW }}"
    height="{{ $chartH }}"
    aria-hidden="true"
>
    <defs>
        <linearGradient id="{{ $gradId }}-muted" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="{{ $color }}" stop-opacity="0.85"/>
            <stop offset="100%" stop-color="{{ $color }}" stop-opacity="0.30"/>
        </linearGradient>
        <linearGradient id="{{ $gradId }}-strong" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="{{ $color }}" stop-opacity="1"/>
            <stop offset="100%" stop-color="{{ $color }}" stop-opacity="0.55"/>
        </linearGradient>
    </defs>
    @foreach($bars as $i => $pct)
        @php
            $normalized = max(22, min(100, (int) $pct));
            $barH = ($normalized / 100) * $chartH;
            $x = $i * ($barWidth + $barGap);
            $y = $chartH - $barH;
            $isLatest = $i === $count - 1;
            $fill = $isLatest ? "url(#{$gradId}-strong)" : "url(#{$gradId}-muted)";
            $opacity = $isLatest ? 1 : number_format(0.50 + ($i / max(1, $count - 1)) * 0.30, 2, '.', '');
        @endphp
        <rect
            x="{{ $x }}"
            y="{{ $y }}"
            width="{{ $barWidth }}"
            height="{{ $barH }}"
            rx="4"
            ry="4"
            fill="{{ $fill }}"
            opacity="{{ $opacity }}"
        />
    @endforeach
</svg>
