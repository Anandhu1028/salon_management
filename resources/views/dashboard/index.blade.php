@extends('layouts.app')

@section('title', 'Dashboard — SalonPro')
@section('page-title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
@endpush

@section('content')

<div class="dashboard-page">

    {{-- Hero + KPI stat cards --}}
    <section class="dashboard-section dashboard-section--hero" id="welcomeBanner">
        <div class="dash-compact-welcome">
            <h2 class="dash-welcome-title" id="dashboardGreeting">Good morning, Admin! 👋</h2>
            <p class="dash-welcome-sub">Here's what's happening with your salon today.</p>
        </div>

        <div class="dashboard-stats-grid" id="statsGrid">

        @php
            $kpiMonths = $dashboard['kpiMonths']['labels'];
            $normalizeBars = fn (array $values) => collect($values)->map(fn ($value) => max(8, round(($value / max(1, max($values))) * 100)))->all();
            $customerBars = $normalizeBars($dashboard['kpiMonths']['customers']);
            $staffBars = $normalizeBars($dashboard['kpiMonths']['staff']);
            $appointmentBars = $normalizeBars($dashboard['kpiMonths']['appointments']);
            $revenueBars = $normalizeBars($dashboard['kpiMonths']['revenue']);
            $growthClass = fn ($value) => $value >= 0 ? 'up' : 'down';
            $growthText = fn ($value) => ($value >= 0 ? '↑ ' : '↓ ').number_format(abs($value), 1).'%';
        @endphp

        <div class="dash-stat-card customers" id="cardCustomers">
            <div class="dash-stat-top">
                <div class="dash-stat-icon-circle">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="dash-stat-content">
                    <span class="dash-stat-label">Total Customers</span>
                    <div class="dash-stat-value-row">
                        <span class="dash-stat-value">{{ number_format($dashboard['customerCount']) }}</span>
                        <span class="stat-badge-inline {{ $growthClass($dashboard['customerGrowth']) }}">{{ $growthText($dashboard['customerGrowth']) }}</span>
                    </div>
                </div>
            </div>
            <div class="dash-stat-footer">
                <span class="card-deco-icon"><i class="bi bi-people-fill"></i></span>
                <span class="dash-stat-subtext">vs last month</span>
                <div class="dash-kpi-chart dash-kpi-chart--customers">
                    @foreach ($kpiMonths as $i => $m)
                        <div class="dash-kpi-bar-col{{ $loop->last ? ' is-current' : '' }}">
                            <div class="dash-kpi-bar" style="--bar-h: {{ $customerBars[$i] }}%;"></div>
                            <span class="dash-kpi-bar-label">{{ $m }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="dash-stat-card staff" id="cardStaff">
            <div class="dash-stat-top">
                <div class="dash-stat-icon-circle">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="dash-stat-content">
                    <span class="dash-stat-label">Active Staff</span>
                    <div class="dash-stat-value-row">
                        <span class="dash-stat-value">{{ number_format($dashboard['activeStaff']) }}</span>
                    </div>
                </div>
            </div>
            <div class="dash-stat-footer">
                <span class="card-deco-icon"><i class="bi bi-person-badge-fill"></i></span>
                <span class="dash-stat-subtext">on duty today</span>
                <div class="dash-kpi-chart dash-kpi-chart--staff">
                    @foreach ($kpiMonths as $i => $m)
                        <div class="dash-kpi-bar-col{{ $loop->last ? ' is-current' : '' }}">
                            <div class="dash-kpi-bar" style="--bar-h: {{ $staffBars[$i] }}%;"></div>
                            <span class="dash-kpi-bar-label">{{ $m }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="dash-stat-card appointments" id="cardAppointments">
            <div class="dash-stat-top">
                <div class="dash-stat-icon-circle">
                    <i class="bi bi-bag-check-fill"></i>
                </div>
                <div class="dash-stat-content">
                    <span class="dash-stat-label">Today's Appointments</span>
                    <div class="dash-stat-value-row">
                        <span class="dash-stat-value">{{ number_format($dashboard['todayAppointments']) }}</span>
                        <span class="stat-badge-inline {{ $growthClass($dashboard['appointmentGrowth']) }}">{{ $growthText($dashboard['appointmentGrowth']) }}</span>
                    </div>
                </div>
            </div>
            <div class="dash-stat-footer">
                <span class="card-deco-icon"><i class="bi bi-bag-check-fill"></i></span>
                <span class="dash-stat-subtext">{{ $dashboard['inProgressToday'] }} in progress</span>
                <div class="dash-kpi-chart dash-kpi-chart--appointments">
                    @foreach ($kpiMonths as $i => $m)
                        <div class="dash-kpi-bar-col{{ $loop->last ? ' is-current' : '' }}">
                            <div class="dash-kpi-bar" style="--bar-h: {{ $appointmentBars[$i] }}%;"></div>
                            <span class="dash-kpi-bar-label">{{ $m }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="dash-stat-card revenue" id="cardRevenue">
            <div class="dash-stat-top">
                <div class="dash-stat-icon-circle">
                    <i class="bi bi-currency-rupee"></i>
                </div>
                <div class="dash-stat-content">
                    <span class="dash-stat-label">Today's Revenue</span>
                    <div class="dash-stat-value-row">
                        <span class="dash-stat-value">₹{{ number_format($dashboard['todayRevenue'], 0) }}</span>
                        <span class="stat-badge-inline {{ $growthClass($dashboard['revenueGrowth']) }}">{{ $growthText($dashboard['revenueGrowth']) }}</span>
                    </div>
                </div>
            </div>
            <div class="dash-stat-footer">
                <span class="card-deco-icon"><i class="bi bi-currency-rupee"></i></span>
                <span class="dash-stat-subtext">vs yesterday</span>
                <div class="dash-kpi-chart dash-kpi-chart--revenue">
                    @foreach ($kpiMonths as $i => $m)
                        <div class="dash-kpi-bar-col{{ $loop->last ? ' is-current' : '' }}">
                            <div class="dash-kpi-bar" style="--bar-h: {{ $revenueBars[$i] }}%;"></div>
                            <span class="dash-kpi-bar-label">{{ $m }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        </div>
    </section>

    {{-- Analytics row --}}
    <section class="dashboard-section" id="sectionCharts">
    <div class="dashboard-grid-charts">

        {{-- Revenue Overview --}}
        <div class="content-card content-card--wide content-card--revenue" id="revenueCard">
            <div class="content-card-header">
                <div class="content-card-title-group">
                    <span class="content-card-icon-badge content-card-icon-badge--indigo"><i class="bi bi-graph-up"></i></span>
                    <div>
                        <h2>Revenue Overview</h2>
                        <span class="sub-text">Daily earnings · last 7 days</span>
                    </div>
                </div>
                <div class="chart-period-control">
                    <button type="button" class="chart-period-btn active" data-period="7">7 Days</button>
                    <button type="button" class="chart-period-btn" data-period="30">30 Days</button>
                    <button type="button" class="chart-period-btn" data-period="12">12 Months</button>
                </div>
            </div>

            <div class="chart-card-body">
                <div class="chart-container chart-container--revenue">
                    <svg viewBox="0 0 700 180" preserveAspectRatio="none" style="width:100%; height:100%; overflow:visible;" id="revenueChart">
                        <defs>
                            <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#6366F1" stop-opacity="0.32" />
                                <stop offset="60%" stop-color="#8B5CF6" stop-opacity="0.10" />
                                <stop offset="100%" stop-color="#C026D3" stop-opacity="0.0" />
                            </linearGradient>
                            <linearGradient id="lineGradient" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0%" stop-color="#6366F1" />
                                <stop offset="50%" stop-color="#8B5CF6" />
                                <stop offset="100%" stop-color="#C026D3" />
                            </linearGradient>
                            <filter id="dotGlow">
                                <feGaussianBlur in="SourceGraphic" stdDeviation="2" result="blur" />
                                <feColorMatrix in="blur" type="matrix" values="0 0 0 0 0.39  0 0 0 0 0.40  0 0 0 0 0.95  0 0 0 0.6 0" result="coloredBlur" />
                                <feMerge>
                                    <feMergeNode in="coloredBlur" />
                                    <feMergeNode in="SourceGraphic" />
                                </feMerge>
                            </filter>
                        </defs>

                        <text x="0" y="16" class="chart-y-label">75K</text>
                        <text x="0" y="52" class="chart-y-label">56K</text>
                        <text x="0" y="88" class="chart-y-label">38K</text>
                        <text x="0" y="124" class="chart-y-label">19K</text>
                        <text x="0" y="160" class="chart-y-label">0</text>

                        <line x1="36" y1="14" x2="700" y2="14" class="chart-grid-line" />
                        <line x1="36" y1="50" x2="700" y2="50" class="chart-grid-line" />
                        <line x1="36" y1="86" x2="700" y2="86" class="chart-grid-line" />
                        <line x1="36" y1="122" x2="700" y2="122" class="chart-grid-line" />
                        <line x1="36" y1="158" x2="700" y2="158" class="chart-grid-line" />

                        <path id="chartArea" d="M36,128 C120,108 180,88 240,98 S360,48 450,52 S560,38 630,42 L680,24 L680,158 L36,158 Z" fill="url(#chartGradient)" />
                        <path id="chartPath" d="M36,128 C120,108 180,88 240,98 S360,48 450,52 S560,38 630,42 L680,24" fill="none" stroke="url(#lineGradient)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />

                        <line id="chartGuideLine" x1="0" y1="14" x2="0" y2="158" class="chart-guide-line" style="display:none;" />

                        <g id="chartPoints">
                            <circle class="chart-point" data-day="Mon" data-val="₹18,500" cx="36" cy="128" r="4.5" fill="#6366F1" stroke="#fff" stroke-width="2" />
                            <circle class="chart-point" data-day="Tue" data-val="₹28,200" cx="136" cy="102" r="4.5" fill="#6C63F8" stroke="#fff" stroke-width="2" />
                            <circle class="chart-point" data-day="Wed" data-val="₹25,800" cx="240" cy="98" r="4.5" fill="#7C5CF6" stroke="#fff" stroke-width="2" />
                            <circle class="chart-point" data-day="Thu" data-val="₹44,100" cx="350" cy="52" r="4.5" fill="#8B5CF6" stroke="#fff" stroke-width="2" />
                            <circle class="chart-point" data-day="Fri" data-val="₹48,650" cx="450" cy="42" r="5" fill="#9D4FC5" stroke="#fff" stroke-width="2" />
                            <circle class="chart-point" data-day="Sat" data-val="₹52,000" cx="560" cy="34" r="4.5" fill="#B040BF" stroke="#fff" stroke-width="2" />
                            <circle class="chart-point" data-day="Sun" data-val="₹57,400" cx="680" cy="24" r="5.5" fill="#C026D3" stroke="#fff" stroke-width="2.5" filter="url(#dotGlow)" />
                        </g>
                    </svg>

                    <div class="chart-tooltip" id="chartTooltip">
                        <span class="tooltip-day" id="tooltipDay">Friday</span>
                        <span class="tooltip-val" id="tooltipVal">₹48,650</span>
                    </div>
                </div>

                <div class="chart-x-labels" id="chartXLabels">
                    <span>Mon</span><span>Tue</span><span>Wed</span>
                    <span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                </div>
            </div>
        </div>

        {{-- Revenue by Category --}}
        <div class="content-card content-card--category" id="revenueCategoryCard">
            <div class="content-card-header">
                <div class="content-card-title-group">
                    <span class="content-card-icon-badge content-card-icon-badge--purple"><i class="bi bi-pie-chart"></i></span>
                    <div>
                        <h2>Revenue by Category</h2>
                        <span class="sub-text">Breakdown by type · this month</span>
                    </div>
                </div>
                <button type="button" class="dash-period-select">This month <i class="bi bi-chevron-down"></i></button>
            </div>
            <div class="donut-card-body donut-card-body--premium">
                <div class="donut-wrap donut-wrap--lg">
                    <div class="donut-glow donut-glow--purple" aria-hidden="true"></div>
                    <svg class="donut-svg donut-svg--revenue" viewBox="0 0 120 120" aria-hidden="true">
                        <defs>
                            <filter id="donutShadowRev" x="-30%" y="-30%" width="160%" height="160%">
                                <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#8650f3" flood-opacity="0.22"/>
                            </filter>
                        </defs>
                        <circle cx="60" cy="60" r="48" fill="none" stroke="#F1F5F9" stroke-width="10"/>
                        <g transform="rotate(-90 60 60)" filter="url(#donutShadowRev)">
                            @php $donutColors = ['#8650f3', '#6366F1', '#F59E0B', '#10B981']; $donutCircumference = 276.46; $donutOffset = 0; @endphp
                            @foreach($dashboard['categoryRevenue'] as $category)
                                @php $dash = ($category['amount'] / max(1, $dashboard['categoryRevenue']->sum('amount'))) * $donutCircumference; @endphp
                                <circle cx="60" cy="60" r="44" fill="none" stroke="{{ $donutColors[$loop->index] }}" stroke-width="11" stroke-linecap="round" stroke-dasharray="{{ $dash }} {{ $donutCircumference - $dash }}" stroke-dashoffset="-{{ $donutOffset }}"/>
                                @php $donutOffset += $dash; @endphp
                            @endforeach
                        </g>
                    </svg>
                    <div class="donut-center">
                        <span class="donut-center-value">₹{{ number_format($dashboard['categoryRevenue']->sum('amount'), 0) }}</span>
                        <span class="donut-center-label">Total Revenue</span>
                    </div>
                </div>
                <ul class="donut-legend donut-legend--premium">
                    @php
                        $categoryTotal = max(1, $dashboard['categoryRevenue']->sum('amount'));
                        $categoryThemes = ['purple', 'blue', 'orange', 'green'];
                    @endphp
                    @forelse($dashboard['categoryRevenue'] as $category)
                        @php $percentage = round(($category['amount'] / $categoryTotal) * 100); $theme = $categoryThemes[$loop->index]; @endphp
                        <li>
                            <span class="legend-dot legend-dot--{{ $theme }}"></span>
                            <div class="legend-detail">
                                <span class="legend-name">{{ $category['name'] }}</span>
                                <div class="legend-track"><div class="legend-track-fill legend-track-fill--{{ $theme }}" style="width: {{ $percentage }}%;"></div></div>
                            </div>
                            <span class="legend-amt">₹{{ number_format($category['amount'], 0) }}</span>
                            <span class="legend-pct">{{ $percentage }}%</span>
                        </li>
                    @empty
                        <li><span class="legend-name">No completed service revenue this month</span></li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Customer Growth --}}
        <div class="content-card content-card--growth" id="customerGrowthCard">
            <div class="content-card-header">
                <div class="content-card-title-group">
                    <span class="content-card-icon-badge content-card-icon-badge--violet"><i class="bi bi-graph-up-arrow"></i></span>
                    <div>
                        <h2>Customer Growth</h2>
                        <span class="sub-text">New customers this month</span>
                    </div>
                </div>
                <button type="button" class="dash-period-select">This month <i class="bi bi-chevron-down"></i></button>
            </div>
            <div class="growth-card-body growth-card-body--premium">
                <div class="growth-metric-row">
                    <div class="growth-metric-main">
                        <span class="growth-value">{{ number_format($dashboard['customerCount']) }}</span>
                        <span class="growth-label">Total Customers</span>
                    </div>
                    <span class="stat-badge-inline {{ $growthClass($dashboard['customerGrowth']) }}">{{ $growthText($dashboard['customerGrowth']) }}</span>
                </div>
                <div class="growth-stats-pills">
                    <span class="growth-pill"><strong>+{{ $dashboard['customerThisMonth'] }}</strong> new</span>
                    <span class="growth-pill growth-pill--muted">{{ $dashboard['customerLastMonth'] }} last month</span>
                </div>
                <div class="growth-chart-premium">
                    <div class="growth-chart-y-axis" aria-hidden="true">
                        <span>200</span><span>150</span><span>100</span><span>50</span><span>0</span>
                    </div>
                    <div class="growth-chart-main">
                        <div class="growth-grid-lines" aria-hidden="true"></div>
                        <div class="bar-chart bar-chart--premium">
                            @php $maxWeeklyCustomers = max(1, max(array_column($dashboard['weeklyCustomers'], 'value'))); @endphp
                            @foreach($dashboard['weeklyCustomers'] as $week)
                                <div class="bar-col">
                                    <span class="bar-value">{{ $week['value'] }}</span>
                                    <div class="bar-fill bar-fill--premium{{ $loop->last ? ' bar-fill--peak' : '' }}" style="--bar-h: {{ max(8, round(($week['value'] / $maxWeeklyCustomers) * 100)) }}%;"></div>
                                    <span class="bar-label">{{ $week['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </section>

    {{-- Appointments row --}}
    <section class="dashboard-section dashboard-section--insights" id="sectionAppt">
    <div class="dashboard-grid-appt">

        {{-- Appointments Overview — weekly day-column heatmap --}}
        <div class="content-card content-card--minimal content-card--appt-heatmap content-card--appt-square" id="appointmentOverviewCard">
            @php
                $apptDayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                $apptHeatmapByDay = collect(range(0, 6))->map(fn ($day) => collect($dashboard['heatmap'])->map(fn ($week) => $week[$day])->all())->all();
                $apptHeatmapTotal = collect($dashboard['heatmap'])->flatten(1)->sum('count');
            @endphp

            <div class="content-card-header">
                <div class="content-card-title-group">
                    <span class="content-card-icon-badge content-card-icon-badge--purple"><i class="bi bi-calendar3"></i></span>
                    <div>
                        <h2>Appointments Overview</h2>
                        <span class="sub-text">Weekly booking activity by day</span>
                    </div>
                </div>
                <div class="appt-heatmap-legend-inline" aria-hidden="true">
                    <span class="appt-heatmap-legend-label">Less</span>
                    <span class="appt-heatmap-cell level-0"></span>
                    <span class="appt-heatmap-cell level-1"></span>
                    <span class="appt-heatmap-cell level-2"></span>
                    <span class="appt-heatmap-cell level-3"></span>
                    <span class="appt-heatmap-cell level-4"></span>
                    <span class="appt-heatmap-legend-label">More</span>
                </div>
            </div>

            <div class="appt-heatmap-body">
                <div class="appt-heatmap-metric">
                    <span class="appt-heatmap-total">{{ number_format($apptHeatmapTotal) }}</span>
                    <span class="appt-heatmap-caption">Appointments</span>
                    <span class="stat-badge-inline {{ $growthClass($dashboard['appointmentGrowth']) }}">{{ $growthText($dashboard['appointmentGrowth']) }}</span>
                    <span class="appt-heatmap-vs">vs. last month</span>
                </div>

                <div class="appt-heatmap-chart-wrap">
                    <div class="appt-heatmap-grid" id="apptHeatmapGrid" role="grid" aria-label="Weekly appointment activity by day">
                        @foreach ($apptDayLabels as $d => $dayLabel)
                            <div class="appt-heatmap-col" role="rowgroup" aria-label="{{ $dayLabel }}">
                                @foreach ($apptHeatmapByDay[$d] as $cell)
                                    <span
                                        class="appt-heatmap-cell level-{{ $cell['level'] }}"
                                        tabindex="0"
                                        role="gridcell"
                                        data-count="{{ $cell['count'] }}"
                                        data-label="{{ $cell['label'] }}"
                                        aria-label="{{ $cell['count'] }} appointments · {{ $cell['label'] }}"
                                    ></span>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <div class="appt-heatmap-day-labels" aria-hidden="true">
                        @foreach ($apptDayLabels as $dayLabel)
                            <span>{{ $dayLabel }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="appt-heatmap-tooltip" id="apptHeatmapTooltip" role="tooltip">
                    <span class="appt-heatmap-tooltip-count" id="apptHeatmapTooltipCount">0 appointments</span>
                    <span class="appt-heatmap-tooltip-date" id="apptHeatmapTooltipDate">Mon · 1w ago</span>
                </div>
            </div>
        </div>

        {{-- Appointment insight mini cards --}}
        <div class="dash-appt-stats-col" id="apptInsightCards">
            <div class="dash-appt-mini-card peak-hour">
                <span class="dash-appt-mini-icon"><i class="bi bi-clock-history"></i></span>
                <div class="dash-appt-mini-body">
                    <div class="dash-appt-mini-value-row">
                        <span class="dash-appt-mini-value">{{ $dashboard['todayStatuses']['pending'] }}</span>
                    </div>
                    <span class="dash-appt-mini-meta">Pending Today <span class="dash-appt-mini-dot">·</span> Awaiting service</span>
                </div>
            </div>
            <div class="dash-appt-mini-card walkins">
                <span class="dash-appt-mini-icon"><i class="bi bi-door-open"></i></span>
                <div class="dash-appt-mini-body">
                    <div class="dash-appt-mini-value-row">
                        <span class="dash-appt-mini-value">{{ $dashboard['inProgressToday'] }}</span>
                    </div>
                    <span class="dash-appt-mini-meta">In Progress <span class="dash-appt-mini-dot">·</span> Currently in service</span>
                </div>
            </div>
            <div class="dash-appt-mini-card repeat-rate">
                <span class="dash-appt-mini-icon"><i class="bi bi-arrow-repeat"></i></span>
                <div class="dash-appt-mini-body">
                    <div class="dash-appt-mini-value-row">
                        <span class="dash-appt-mini-value">{{ $dashboard['todayAppointments'] ? round(($dashboard['todayStatuses']['completed'] / $dashboard['todayAppointments']) * 100) : 0 }}%</span>
                    </div>
                    <span class="dash-appt-mini-meta">Completion Rate <span class="dash-appt-mini-dot">·</span> Today</span>
                </div>
            </div>
        </div>

        {{-- Today's Appointment Status --}}
        <div class="content-card content-card--minimal content-card--appt-status" id="appointmentStatusCard">
            <div class="content-card-header">
                <div class="content-card-title-group">
                    <span class="content-card-icon-badge content-card-icon-badge--amber"><i class="bi bi-calendar-check"></i></span>
                    <div>
                        <h2>Today's Status</h2>
                        <span class="sub-text">Live appointment breakdown</span>
                    </div>
                </div>
                <span class="appt-live-badge"><span class="appt-live-dot"></span> Live</span>
            </div>
            <div class="appt-status-body">
                <div class="appt-status-total">
                    <span class="appt-status-total-val">{{ $dashboard['todayAppointments'] }}</span>
                    <span class="appt-status-total-label">appointments today</span>
                </div>
                <div class="appt-status-list">
                    <div class="appt-status-row upcoming">
                        <span class="appt-status-icon blue"><i class="bi bi-clock"></i></span>
                        <div class="appt-status-info">
                            <div class="appt-status-title">Upcoming</div>
                            <div class="appt-status-desc">Scheduled & waiting</div>
                        </div>
                        <span class="appt-status-count">{{ $dashboard['todayStatuses']['pending'] }}</span>
                    </div>
                    <div class="appt-status-row inprogress">
                        <span class="appt-status-icon amber"><i class="bi bi-hourglass-split"></i></span>
                        <div class="appt-status-info">
                            <div class="appt-status-title">In Progress</div>
                            <div class="appt-status-desc">Currently in chair</div>
                        </div>
                        <span class="appt-status-count">{{ $dashboard['todayStatuses']['in_progress'] }}</span>
                    </div>
                    <div class="appt-status-row completed">
                        <span class="appt-status-icon green"><i class="bi bi-check-circle"></i></span>
                        <div class="appt-status-info">
                            <div class="appt-status-title">Completed</div>
                            <div class="appt-status-desc">Finished today</div>
                        </div>
                        <span class="appt-status-count">{{ $dashboard['todayStatuses']['completed'] }}</span>
                    </div>
                    <div class="appt-status-row cancelled">
                        <span class="appt-status-icon red"><i class="bi bi-x-circle"></i></span>
                        <div class="appt-status-info">
                            <div class="appt-status-title">Cancelled</div>
                            <div class="appt-status-desc">No-shows & cancelled</div>
                        </div>
                        <span class="appt-status-count">{{ $dashboard['todayStatuses']['cancelled'] }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </section>

    {{-- Performance & actions --}}
    <section class="dashboard-section dashboard-section--insights" id="sectionBottom">
    <div class="dashboard-grid-bottom">

        {{-- Top Staff Performance --}}
        <!-- <div class="content-card content-card--minimal content-card--staff" id="staffPerfCard">
            <div class="content-card-header">
                <div class="content-card-title-group">
                    <span class="content-card-icon-badge content-card-icon-badge--blue"><i class="bi bi-award"></i></span>
                    <div>
                        <h2>Top Staff Performance</h2>
                        <span class="sub-text">Ranked by appointments & revenue</span>
                    </div>
                </div>
                <button type="button" class="dash-period-select">This month <i class="bi bi-chevron-down"></i></button>
            </div>
            <div class="staff-perf-list">
                <div class="staff-perf-row">
                    <div class="staff-perf-avatar staff-perf-avatar--1">AA</div>
                    <div class="staff-perf-info">
                        <div class="staff-perf-name">Anandhu A</div>
                        <div class="staff-perf-meta">28 Appointments · ₹32,450</div>
                    </div>
                    <div class="staff-perf-score">
                        <span class="staff-perf-pct staff-perf-pct--high">92%</span>
                        <div class="staff-perf-bar"><div class="staff-perf-bar-fill" style="--bar-w: 92%;"></div></div>
                    </div>
                </div>
                <div class="staff-perf-row">
                    <div class="staff-perf-avatar staff-perf-avatar--2">RK</div>
                    <div class="staff-perf-info">
                        <div class="staff-perf-name">Rahul K</div>
                        <div class="staff-perf-meta">24 Appointments · ₹28,600</div>
                    </div>
                    <div class="staff-perf-score">
                        <span class="staff-perf-pct staff-perf-pct--high">88%</span>
                        <div class="staff-perf-bar"><div class="staff-perf-bar-fill" style="--bar-w: 88%;"></div></div>
                    </div>
                </div>
                <div class="staff-perf-row">
                    <div class="staff-perf-avatar staff-perf-avatar--3">PM</div>
                    <div class="staff-perf-info">
                        <div class="staff-perf-name">Priya M</div>
                        <div class="staff-perf-meta">20 Appointments · ₹22,150</div>
                    </div>
                    <div class="staff-perf-score">
                        <span class="staff-perf-pct staff-perf-pct--mid">78%</span>
                        <div class="staff-perf-bar"><div class="staff-perf-bar-fill staff-perf-bar-fill--mid" style="--bar-w: 78%;"></div></div>
                    </div>
                </div>
                <div class="staff-perf-row">
                    <div class="staff-perf-avatar staff-perf-avatar--4">SP</div>
                    <div class="staff-perf-info">
                        <div class="staff-perf-name">Sneha P</div>
                        <div class="staff-perf-meta">18 Appointments · ₹18,700</div>
                    </div>
                    <div class="staff-perf-score">
                        <span class="staff-perf-pct staff-perf-pct--mid">70%</span>
                        <div class="staff-perf-bar"><div class="staff-perf-bar-fill staff-perf-bar-fill--mid" style="--bar-w: 70%;"></div></div>
                    </div>
                </div>
            </div>
        </div> -->

        {{-- Quick Actions --}}
        <!-- <div class="content-card content-card--minimal content-card--actions" id="quickActionsCard">
            <div class="content-card-header">
                <div class="content-card-title-group">
                    <span class="content-card-icon-badge content-card-icon-badge--indigo"><i class="bi bi-lightning-charge"></i></span>
                    <div>
                        <h2>Quick Actions</h2>
                        <span class="sub-text">Common salon tasks</span>
                    </div>
                </div>
            </div>
            <div class="quick-actions-grid">
                <a href="{{ route('job-cards.index') }}" class="quick-action-btn qa-purple">
                    <span class="qa-icon"><i class="bi bi-calendar-plus"></i></span>
                    <span class="qa-label">Add Appointment</span>
                </a>
                <a href="{{ route('customers.index') }}" class="quick-action-btn qa-pink">
                    <span class="qa-icon"><i class="bi bi-person-plus"></i></span>
                    <span class="qa-label">Add Customer</span>
                </a>
                <a href="{{ route('services.index') }}" class="quick-action-btn qa-lavender">
                    <span class="qa-icon"><i class="bi bi-scissors"></i></span>
                    <span class="qa-label">Add Service</span>
                </a>
                <a href="{{ route('products.index') }}" class="quick-action-btn qa-orange">
                    <span class="qa-icon"><i class="bi bi-box-seam"></i></span>
                    <span class="qa-label">Add Product</span>
                </a>
                <a href="{{ route('job-cards.index') }}" class="quick-action-btn qa-blue">
                    <span class="qa-icon"><i class="bi bi-card-checklist"></i></span>
                    <span class="qa-label">Create Job Card</span>
                </a>
                <a href="{{ route('dashboard') }}" class="quick-action-btn qa-green">
                    <span class="qa-icon"><i class="bi bi-bar-chart-line"></i></span>
                    <span class="qa-label">View Reports</span>
                </a>
            </div>
        </div> -->

    </div>
    </section>

</div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const greeting = document.getElementById('dashboardGreeting');
            const updateGreeting = () => {
                if (!greeting) return;

                const hour = new Date().getHours();
                const salutation = hour < 12
                    ? 'Good morning'
                    : hour < 17
                        ? 'Good afternoon'
                        : 'Good evening';

                greeting.textContent = `${salutation}, Admin! 👋`;
            };

            updateGreeting();
            setInterval(updateGreeting, 60 * 1000);

            const revenueSeries = @json($dashboard['revenueSeries']);
            const renderRevenueChart = (period) => {
                const series = revenueSeries[period];
                const chartSvg = document.getElementById('revenueChart');
                const chartPath = document.getElementById('chartPath');
                const chartArea = document.getElementById('chartArea');
                const pointsGroup = document.getElementById('chartPoints');
                const xLabels = document.getElementById('chartXLabels');
                if (!series || !chartSvg || !chartPath || !chartArea || !pointsGroup) return;

                const max = Math.max(1, ...series.values);
                const left = 36; const right = 680; const top = 14; const bottom = 158;
                const step = series.values.length > 1 ? (right - left) / (series.values.length - 1) : 0;
                const coords = series.values.map((value, index) => ({
                    x: left + (index * step), y: bottom - ((value / max) * (bottom - top)), value,
                }));
                const line = coords.map((point, index) => `${index ? 'L' : 'M'}${point.x},${point.y}`).join(' ');
                chartPath.setAttribute('d', line);
                chartArea.setAttribute('d', `${line} L${right},${bottom} L${left},${bottom} Z`);
                pointsGroup.innerHTML = coords.map((point, index) => `<circle class="chart-point" data-day="${series.labels[index]}" data-val="₹${Number(point.value).toLocaleString('en-IN')}" cx="${point.x}" cy="${point.y}" r="4.5" fill="#7C5CF6" stroke="#fff" stroke-width="2" />`).join('');
                if (xLabels) xLabels.innerHTML = series.labels.map((label) => `<span>${label}</span>`).join('');
                chartSvg.querySelectorAll('.chart-y-label').forEach((label, index) => {
                    label.textContent = index === 4 ? '0' : `₹${Math.round(max * ((4 - index) / 4)).toLocaleString('en-IN')}`;
                });
            };

            renderRevenueChart('7');
            const periodBtns = document.querySelectorAll('.chart-period-btn');
            periodBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    periodBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    renderRevenueChart(btn.dataset.period);
                });
            });

            const chartSvg = document.getElementById('revenueChart');
            const points = document.querySelectorAll('.chart-point');
            const tooltip = document.getElementById('chartTooltip');
            const tooltipDay = document.getElementById('tooltipDay');
            const tooltipVal = document.getElementById('tooltipVal');
            const guideLine = document.getElementById('chartGuideLine');

            if (chartSvg && points.length && tooltip) {
                points.forEach(point => {
                    point.addEventListener('mouseenter', () => {
                        const day = point.getAttribute('data-day');
                        const val = point.getAttribute('data-val');
                        const cx = parseFloat(point.getAttribute('cx'));
                        const cy = parseFloat(point.getAttribute('cy'));

                        tooltipDay.textContent = day;
                        tooltipVal.textContent = val;

                        if (guideLine) {
                            guideLine.setAttribute('x1', cx);
                            guideLine.setAttribute('x2', cx);
                            guideLine.style.display = 'block';
                        }

                        const container = chartSvg.parentElement;
                        const rect = container.getBoundingClientRect();
                        const scaleX = rect.width / 700;
                        const scaleY = rect.height / 180;

                        tooltip.style.left = `${cx * scaleX}px`;
                        tooltip.style.top = `${cy * scaleY - 45}px`;
                        tooltip.style.display = 'flex';
                        tooltip.style.opacity = '1';

                        point.setAttribute('r', '7');
                    });

                    point.addEventListener('mouseleave', () => {
                        tooltip.style.display = 'none';
                        if (guideLine) guideLine.style.display = 'none';
                        const isSun = point.getAttribute('data-day') === 'Sun';
                        point.setAttribute('r', isSun ? '5.5' : '4.5');
                    });
                });
            }

            /* Appointments heatmap tooltip */
            const heatmapCells = document.querySelectorAll('#apptHeatmapGrid .appt-heatmap-cell');
            const heatmapTooltip = document.getElementById('apptHeatmapTooltip');
            const heatmapTooltipCount = document.getElementById('apptHeatmapTooltipCount');
            const heatmapTooltipDate = document.getElementById('apptHeatmapTooltipDate');
            const heatmapWrap = document.querySelector('.appt-heatmap-chart-wrap');

            if (heatmapCells.length && heatmapTooltip && heatmapWrap) {
                const showHeatmapTooltip = (cell) => {
                    const count = parseInt(cell.getAttribute('data-count'), 10) || 0;
                    const label = cell.getAttribute('data-label');

                    heatmapTooltipCount.textContent = count === 1 ? '1 appointment' : `${count} appointments`;
                    heatmapTooltipDate.textContent = label;

                    const wrapRect = heatmapWrap.getBoundingClientRect();
                    const cellRect = cell.getBoundingClientRect();

                    heatmapTooltip.style.left = `${cellRect.left - wrapRect.left + cellRect.width / 2}px`;
                    heatmapTooltip.style.top = `${cellRect.top - wrapRect.top - 6}px`;
                    heatmapTooltip.style.display = 'flex';
                    heatmapTooltip.style.opacity = '1';
                };

                const hideHeatmapTooltip = () => {
                    heatmapTooltip.style.display = 'none';
                    heatmapTooltip.style.opacity = '0';
                };

                heatmapCells.forEach(cell => {
                    cell.addEventListener('mouseenter', () => showHeatmapTooltip(cell));
                    cell.addEventListener('focus', () => showHeatmapTooltip(cell));
                    cell.addEventListener('mouseleave', hideHeatmapTooltip);
                    cell.addEventListener('blur', hideHeatmapTooltip);
                });
            }
        });
    </script>
@endpush
