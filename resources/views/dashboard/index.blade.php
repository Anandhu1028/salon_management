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

        {{-- Staff Work Performance --}}
        <div class="content-card content-card--wide content-card--revenue" id="revenueCard">
            <div class="content-card-header">
                <div class="content-card-title-group">
                    <span class="content-card-icon-badge content-card-icon-badge--indigo"><i class="bi bi-people-fill"></i></span>
                    <div>
                        <h2>Staff Work Performance</h2>
                        <span class="sub-text" id="staffPerformanceSubtitle">Staff productivity and service completion · last 7 days</span>
                    </div>
                </div>
                <div class="staff-performance-filters">
                    <div class="staff-performance-filter-chip staff-performance-filter-chip--staff">
                        <span class="staff-performance-filter-icon"><i class="bi bi-person-workspace"></i></span>
                        <span class="staff-performance-filter-label">Staff</span>
                        <select class="staff-performance-select-native" id="staffPerformanceStaff" aria-label="Filter by staff member">
                            <option value="">All Staff</option>
                            @foreach($staffMembers as $staffMember)
                                <option value="{{ $staffMember->id }}">{{ $staffMember->name }}</option>
                            @endforeach
                        </select>
                        <div class="staff-performance-dropdown" data-select="staffPerformanceStaff">
                            <button type="button" class="staff-performance-dropdown-trigger" aria-haspopup="listbox" aria-expanded="false">
                                <span>All Staff</span><i class="bi bi-chevron-down"></i>
                            </button>
                            <div class="staff-performance-dropdown-menu" role="listbox">
                                <button type="button" role="option" aria-selected="true" data-value=""><span>All Staff</span><i class="bi bi-check2"></i></button>
                                @foreach($staffMembers as $staffMember)
                                    <button type="button" role="option" aria-selected="false" data-value="{{ $staffMember->id }}"><span>{{ $staffMember->name }}</span><i class="bi bi-check2"></i></button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="staff-performance-filter-chip">
                        <span class="staff-performance-filter-icon"><i class="bi bi-calendar3"></i></span>
                        <span class="staff-performance-filter-label">Period</span>
                        <select class="staff-performance-select-native" id="staffPerformancePeriod" aria-label="Filter by period">
                            <option value="today">Today</option>
                            <option value="7" selected>7 Days</option>
                            <option value="30">30 Days</option>
                            <option value="this_month">This Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                        <div class="staff-performance-dropdown" data-select="staffPerformancePeriod">
                            <button type="button" class="staff-performance-dropdown-trigger" aria-haspopup="listbox" aria-expanded="false">
                                <span>7 Days</span><i class="bi bi-chevron-down"></i>
                            </button>
                            <div class="staff-performance-dropdown-menu" role="listbox">
                                <button type="button" role="option" aria-selected="false" data-value="today"><span>Today</span><i class="bi bi-check2"></i></button>
                                <button type="button" role="option" aria-selected="true" data-value="7"><span>7 Days</span><i class="bi bi-check2"></i></button>
                                <button type="button" role="option" aria-selected="false" data-value="30"><span>30 Days</span><i class="bi bi-check2"></i></button>
                                <button type="button" role="option" aria-selected="false" data-value="this_month"><span>This Month</span><i class="bi bi-check2"></i></button>
                                <button type="button" role="option" aria-selected="false" data-value="custom"><span>Custom Range</span><i class="bi bi-check2"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="staff-performance-custom-range" id="staffPerformanceCustomRange" hidden>
                <label>From <input type="date" id="staffPerformanceStart"></label>
                <label>To <input type="date" id="staffPerformanceEnd"></label>
                <button type="button" id="staffPerformanceApply">Apply</button>
            </div>
            <div class="chart-card-body staff-performance-body">
                <div class="staff-performance-chart-wrap">
                    <div class="staff-performance-chart-scroll" id="staffPerformanceChartScroll">
                        <canvas id="staffPerformanceChart" aria-label="Staff work performance chart"></canvas>
                    </div>
                </div>
                <div class="staff-performance-summary">
                    <span class="staff-performance-summary-icon"><i class="bi bi-arrow-up-right"></i></span>
                    <span><strong id="staffPerformanceAverage">0%</strong> average overall performance</span>
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
                    @php
                        $maxWeeklyCustomers = max(25, (int) (ceil(max(array_column($dashboard['weeklyCustomers'], 'value')) / 5) * 5));
                        $growthTicks = collect(range(0, $maxWeeklyCustomers, 5))->reverse()->values();
                        $growthGridStep = 100 / max(1, $growthTicks->count() - 1);
                    @endphp
                    <div class="growth-chart-y-axis" aria-hidden="true">
                        @foreach($growthTicks as $tick)
                            <span>{{ $tick }}</span>
                        @endforeach
                    </div>
                    <div class="growth-chart-main">
                        <div class="growth-grid-lines" style="--growth-grid-step: {{ $growthGridStep }}%;" aria-hidden="true"></div>
                        <div class="bar-chart bar-chart--premium">
                            @foreach($dashboard['weeklyCustomers'] as $week)
                                <div class="bar-col">
                                    <span class="bar-value">{{ $week['value'] }}</span>
                                    <div class="bar-fill bar-fill--premium{{ $loop->last ? ' bar-fill--peak' : '' }}" style="--bar-h: {{ $week['value'] > 0 ? max(8, round(($week['value'] / $maxWeeklyCustomers) * 100)) : 0 }}%;"></div>
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
        <!-- <div class="content-card content-card--minimal content-card--appt-heatmap content-card--appt-square" id="appointmentOverviewCard">
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
        </div> -->

        {{-- Appointment insight mini cards --}}
        <!-- <div class="dash-appt-stats-col" id="apptInsightCards">
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
        </div> -->

        {{-- Today's Appointment Status --}}
        <!-- <div class="content-card content-card--minimal content-card--appt-status" id="appointmentStatusCard">
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
        </div> -->

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
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

            const performanceCanvas = document.getElementById('staffPerformanceChart');
            const performanceChartScroll = document.getElementById('staffPerformanceChartScroll');
            const periodSelect = document.getElementById('staffPerformancePeriod');
            const staffSelect = document.getElementById('staffPerformanceStaff');
            const customRange = document.getElementById('staffPerformanceCustomRange');
            const startDate = document.getElementById('staffPerformanceStart');
            const endDate = document.getElementById('staffPerformanceEnd');
            const applyRange = document.getElementById('staffPerformanceApply');
            const averagePerformance = document.getElementById('staffPerformanceAverage');
            const subtitle = document.getElementById('staffPerformanceSubtitle');
            let staffPerformanceChart;

            document.querySelectorAll('.staff-performance-dropdown').forEach(dropdown => {
                const select = document.getElementById(dropdown.dataset.select);
                const trigger = dropdown.querySelector('.staff-performance-dropdown-trigger');
                const options = dropdown.querySelectorAll('[role="option"]');
                const close = () => { dropdown.classList.remove('is-open'); trigger.setAttribute('aria-expanded', 'false'); };

                trigger.addEventListener('click', () => {
                    const willOpen = !dropdown.classList.contains('is-open');
                    document.querySelectorAll('.staff-performance-dropdown.is-open').forEach(item => item.classList.remove('is-open'));
                    dropdown.classList.toggle('is-open', willOpen);
                    trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                });
                options.forEach(option => option.addEventListener('click', () => {
                    select.value = option.dataset.value;
                    trigger.querySelector('span').textContent = option.querySelector('span').textContent;
                    options.forEach(item => item.setAttribute('aria-selected', item === option ? 'true' : 'false'));
                    close();
                    select.dispatchEvent(new Event('change'));
                }));
                document.addEventListener('click', event => { if (!dropdown.contains(event.target)) close(); });
            });

            const formatCurrency = (value) => `₹${Number(value || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 })}`;
            const periodLabels = { today: 'today', 7: 'last 7 days', 30: 'last 30 days', this_month: 'this month', custom: 'custom range' };

            const renderStaffPerformanceChart = (staff) => {
                const context = performanceCanvas.getContext('2d');
                const chartWidth = Math.max(performanceCanvas.closest('.staff-performance-chart-wrap').clientWidth, staff.length * 108);
                performanceChartScroll.style.width = `${chartWidth}px`;
                const gradient = context.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, '#8B5CF6');
                gradient.addColorStop(.55, '#6366F1');
                gradient.addColorStop(1, '#4F46E5');
                if (staffPerformanceChart) staffPerformanceChart.destroy();

                const performanceLabels = {
                    id: 'performanceLabels',
                    afterDatasetsDraw(chart) {
                        const { ctx } = chart;
                        const bars = chart.getDatasetMeta(0).data;
                        ctx.save();
                        ctx.fillStyle = '#4C4A70';
                        ctx.font = '700 11px Inter, sans-serif';
                        ctx.textAlign = 'center';
                        bars.forEach((bar, index) => {
                            ctx.fillText(`${staff[index].overall_performance}%`, bar.x, bar.y - 10);
                        });
                        ctx.restore();
                    },
                    beforeDatasetDraw(chart) {
                        chart.ctx.save();
                        chart.ctx.shadowColor = 'rgba(99, 102, 241, .22)';
                        chart.ctx.shadowBlur = 12;
                        chart.ctx.shadowOffsetY = 6;
                    },
                    afterDatasetDraw(chart) { chart.ctx.restore(); },
                };

                staffPerformanceChart = new Chart(context, {
                    type: 'bar',
                    plugins: [performanceLabels],
                    data: { labels: staff.map(member => member.name.split(' ')), datasets: [{ data: staff.map(member => member.overall_performance), backgroundColor: gradient, borderRadius: 10, borderSkipped: false, maxBarThickness: 56, hoverBackgroundColor: '#5B4DEB' }] },
                    options: {
                        responsive: true, maintainAspectRatio: false, animation: { duration: 560, easing: 'easeOutQuart' }, layout: { padding: { top: 28, right: 8, left: 2 } },
                        plugins: {
                            legend: { display: false }, displayColors: false,
                            tooltip: { displayColors: false, backgroundColor: '#17152E', titleColor: '#FFFFFF', bodyColor: '#DCD9FF', padding: 14, cornerRadius: 12, callbacks: {
                                title: ([item]) => staff[item.dataIndex].name,
                                label: (item) => { const member = staff[item.dataIndex]; return [`Total appointments: ${member.total_appointments}`, `Completed: ${member.completed_appointments}`, `Pending / in progress: ${member.pending_appointments}`, `Cancelled: ${member.cancelled_appointments}`, `Revenue generated: ${formatCurrency(member.revenue_generated)}`, `Overall performance: ${member.overall_performance}%`]; },
                            } },
                        },
                        scales: {
                            x: { grid: { display: false }, border: { display: false }, ticks: { color: '#64748B', font: { family: 'Inter, sans-serif', weight: '600' }, maxRotation: 0, autoSkip: false, padding: 10 } },
                            y: { beginAtZero: true, max: 100, border: { display: false }, grid: { color: 'rgba(148, 163, 184, .19)', drawTicks: false }, ticks: { stepSize: 25, padding: 12, color: '#94A3B8', callback: value => `${value}%` } },
                        },
                    },
                });
            };

            const loadStaffPerformance = async () => {
                const params = new URLSearchParams({ period: periodSelect.value });
                if (staffSelect.value) params.set('staff_id', staffSelect.value);
                if (periodSelect.value === 'custom') {
                    if (!startDate.value || !endDate.value) return;
                    params.set('start_date', startDate.value); params.set('end_date', endDate.value);
                }
                const chartWrap = performanceCanvas.closest('.staff-performance-chart-wrap');
                try {
                    chartWrap.classList.add('is-loading');
                    const response = await fetch(`{{ route('dashboard.staff-performance') }}?${params.toString()}`, { headers: { Accept: 'application/json' } });
                    if (!response.ok) throw new Error('Unable to load staff performance');
                    const result = await response.json();
                    renderStaffPerformanceChart(result.data);
                    averagePerformance.textContent = `${result.average_performance}%`;
                    subtitle.textContent = `Staff productivity and service completion · ${periodLabels[periodSelect.value]}`;
                } catch (error) { console.error(error); } finally { chartWrap.classList.remove('is-loading'); }
            };

            periodSelect.addEventListener('change', () => { customRange.hidden = periodSelect.value !== 'custom'; if (periodSelect.value !== 'custom') loadStaffPerformance(); });
            staffSelect.addEventListener('change', loadStaffPerformance);
            applyRange.addEventListener('click', loadStaffPerformance);
            loadStaffPerformance();

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
