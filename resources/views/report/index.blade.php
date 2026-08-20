@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
    @php
        $tabs = ['sales' => ['Total Sales', 'bi-bar-chart-line'], 'expenses' => ['Total Expenses', 'bi-wallet2'], 'staff' => ['Staff Daily Target', 'bi-people'], 'purchase' => ['Total Purchase', 'bi-cart3']];
        $range = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');

        $activeFiltersCount = 0;
        if (request('search'))
            $activeFiltersCount++;
        if (request('payment_method'))
            $activeFiltersCount++;
        if (request('staff_id'))
            $activeFiltersCount++;
        if (request('category'))
            $activeFiltersCount++;
        if (request('start_date') || request('end_date'))
            $activeFiltersCount++;
    @endphp

    <div class="reports-page">
        {{-- Top Management Action Bar --}}
        <div class="mgmt-top-actions" style="margin-bottom: 16px;">
            <div class="mgmt-top-actions__right">
                <a href="{{ route('reports.export.excel', request()->query()) }}"
                    class="mgmt-action-btn mgmt-action-btn--excel" title="Export Excel Report">
                    <span class="mgmt-action-btn__icon" aria-hidden="true">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    </span>
                    <span class="mgmt-action-btn__label">Export </span>
                </a>

                <button type="button"
                    class="mgmt-action-btn mgmt-action-btn--filter {{ $activeFiltersCount > 0 ? 'is-active' : '' }}"
                    data-bs-toggle="modal" data-bs-target="#reportFilterModal" title="Filter Report">
                    <span class="mgmt-action-btn__icon" aria-hidden="true">
                        <i class="bi bi-funnel-fill"></i>
                    </span>
                    <span class="mgmt-action-btn__label">Filter</span>
                    @if($activeFiltersCount > 0)
                        <span class="mgmt-filter-badge">{{ $activeFiltersCount }}</span>
                    @endif
                </button>
            </div>
        </div>

        <section class="reports-summary-grid" aria-label="Report summary for {{ $range }}">
            <a href="{{ route('reports.index', ['tab' => 'sales', 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"
                class="report-summary-card report-summary-card--sales">
                <span class="report-summary-icon"><i class="bi bi-bag"></i></span>
                <div>
                    <span class="report-summary-label">Total Sales</span>
                    <strong class="report-summary-value">₹{{ number_format($totalSales, 2) }}</strong>
                    <span class="report-summary-meta">{{ $salesCards->count() }} completed job cards</span>
                </div>
                <span class="report-card-spark" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></span>
            </a>

            <a href="{{ route('reports.index', ['tab' => 'expenses', 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"
                class="report-summary-card report-summary-card--expense">
                <span class="report-summary-icon"><i class="bi bi-wallet2"></i></span>
                <div>
                    <span class="report-summary-label">Total Expenses</span>
                    <strong class="report-summary-value">₹{{ number_format($totalExpenses, 2) }}</strong>
                    <span class="report-summary-meta">No expense records available</span>
                </div>
                <span class="report-card-spark" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></span>
            </a>

            <a href="{{ route('reports.index', ['tab' => 'staff', 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"
                class="report-summary-card report-summary-card--staff">
                <span class="report-summary-icon"><i class="bi bi-people"></i></span>
                <div>
                    <span class="report-summary-label">Staff Daily Target</span>
                    <strong
                        class="report-summary-value">₹{{ number_format($staffAchieved > 0 ? $staffAchieved : $staffDailyTarget, 2) }}</strong>
                    <span class="report-summary-meta">{{ $staffPerformance->count() }} staff completed services</span>
                </div>
                <span class="report-card-spark" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></span>
            </a>

            <a href="{{ route('reports.index', ['tab' => 'purchase', 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"
                class="report-summary-card report-summary-card--purchase">
                <span class="report-summary-icon"><i class="bi bi-cart3"></i></span>
                <div>
                    <span class="report-summary-label">Total Purchase</span>
                    <strong class="report-summary-value">₹{{ number_format($totalPurchase, 2) }}</strong>
                    <span class="report-summary-meta">{{ $purchaseRows->count() }} purchase transactions</span>
                </div>
                <span class="report-card-spark" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></span>
            </a>
        </section>

        <section class="reports-card">
            <nav class="reports-tabs" aria-label="Report categories">
                @foreach ($tabs as $key => [$label, $icon])
                    <a class="reports-tab {{ $activeTab === $key ? 'active' : '' }}"
                        href="{{ route('reports.index', array_merge(request()->query(), ['tab' => $key, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()])) }}">
                        <i class="bi {{ $icon }}"></i>
                        <span>{{ $label }}</span>
                    </a>
                @endforeach
            </nav>
            <div class="reports-content">
                @include('report.partials.' . $activeTab)
            </div>
        </section>
    </div>

    {{-- Report Filter Modal --}}
    <div class="modal fade" id="reportFilterModal" tabindex="-1" aria-labelledby="reportFilterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 560px;">
            <div class="modal-content filter-modal-content">
                <form method="GET" action="{{ route('reports.index') }}" id="reportFilterForm">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">

                    <div class="modal-header filter-modal-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="filter-modal-icon">
                                <i class="bi bi-funnel-fill"></i>
                            </div>
                            <div>
                                <h5 class="modal-title filter-modal-title" id="reportFilterModalLabel">Filter
                                    {{ $tabs[$activeTab][0] ?? 'Report' }}</h5>
                                <span class="filter-modal-subtitle">Customize date range and parameters to refine your
                                    results</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body filter-modal-body">
                        {{-- Quick Date Presets --}}
                        <div class="mb-3">
                            <label class="filter-section-label">Quick Range</label>
                            <div class="quick-range-group" id="quickRangeGroup">
                                <button type="button" class="quick-range-btn" data-preset="today">Today</button>
                                <button type="button" class="quick-range-btn" data-preset="yesterday">Yesterday</button>
                                <button type="button" class="quick-range-btn" data-preset="this_week">This Week</button>
                                <button type="button" class="quick-range-btn" data-preset="this_month">This Month</button>
                                <button type="button" class="quick-range-btn" data-preset="last_month">Last Month</button>
                            </div>
                        </div>

                        {{-- Start Date and End Date --}}
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="filter-field-label" for="filter_start_date">Start Date</label>
                                <div class="filter-input-pill">
                                    <span class="filter-input-icon"><i class="bi bi-calendar3"></i></span>
                                    <input type="date" name="start_date" id="filter_start_date"
                                        class="filter-input-control" value="{{ $startDate->toDateString() }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="filter-field-label" for="filter_end_date">End Date</label>
                                <div class="filter-input-pill">
                                    <span class="filter-input-icon"><i class="bi bi-calendar3"></i></span>
                                    <input type="date" name="end_date" id="filter_end_date" class="filter-input-control"
                                        value="{{ $endDate->toDateString() }}">
                                </div>
                            </div>
                        </div>

                        {{-- Search Input --}}
                        <div class="mb-3">
                            <label class="filter-field-label" for="modal_search">Keyword Search</label>
                            <div class="filter-input-pill filter-input-pill--muted">
                                <span class="filter-input-icon filter-input-icon--muted"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" id="modal_search" class="filter-input-control"
                                    placeholder="Search customer, job card, staff..." value="{{ request('search', '') }}">
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="mb-3">
                            <label class="filter-field-label" for="modal_payment_method">Payment Method</label>
                            <div class="filter-input-pill">
                                <span class="filter-input-icon"><i class="bi bi-credit-card-2-front"></i></span>
                                <select name="payment_method" id="modal_payment_method" class="filter-input-control filter-input-select">
                                    <option value="">All Payment Methods</option>
                                    @foreach($paymentMethods as $m)
                                        <option value="{{ $m }}" {{ request('payment_method') === $m ? 'selected' : '' }}>
                                            {{ $m }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Staff Filter (for Sales, Staff tabs) --}}
                        <div class="mb-3">
                            <label class="filter-field-label" for="modal_staff_id">Staff Member</label>
                            <div class="filter-input-pill">
                                <span class="filter-input-icon"><i class="bi bi-person"></i></span>
                                <select name="staff_id" id="modal_staff_id" class="filter-input-control filter-input-select">
                                    <option value="">All Staff Members</option>
                                    @foreach($filterStaff as $s)
                                        <option value="{{ $s->id }}" {{ (string) request('staff_id') === (string) $s->id ? 'selected' : '' }}>
                                            {{ $s->name }} @if($s->mobile_number)({{ $s->mobile_number }})@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Category Filter --}}
                        <div class="mb-1">
                            <label class="filter-field-label" for="modal_category">Category</label>
                            <div class="filter-input-pill">
                                <span class="filter-input-icon"><i class="bi bi-tag"></i></span>
                                <select name="category" id="modal_category" class="filter-input-control filter-input-select">
                                    <option value="">All Categories</option>
                                    @foreach($filterCategories as $cat)
                                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer filter-modal-footer">
                        <a href="{{ route('reports.index', ['tab' => $activeTab]) }}" class="btn filter-btn filter-btn--ghost">
                            <i class="bi bi-arrow-counterclockwise"></i> Clear All
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn filter-btn filter-btn--outline" data-bs-dismiss="modal">
                                <i class="bi bi-x-lg"></i> Cancel
                            </button>
                            <button type="submit" class="btn filter-btn filter-btn--primary">
                                <i class="bi bi-funnel-fill"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management/management.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
    <style>
        .app-content {
            padding: 22px 24px 34px
        }

        .reports-page {
            max-width: none
        }

        .reports-summary-grid {
            gap: 16px;
            margin-bottom: 22px
        }

        .report-summary-card {
            position: relative;
            isolation: isolate;
            min-height: 130px;
            padding: 18px 20px;
            overflow: hidden;
            border-radius: 14px;
            border: 1px solid #e8eaf0;
            background: #fff;
            box-shadow: 0 4px 14px rgba(15, 23, 42, .03);
            align-items: flex-start;
            text-decoration: none;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease
        }

        .report-summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(51, 65, 114, .09)
        }

        .report-summary-card:before {
            content: "";
            position: absolute;
            z-index: -1;
            inset: 0;
            border-left: 3px solid var(--summary-accent)
        }

        .report-summary-card:after {
            content: "";
            position: absolute;
            z-index: -1;
            width: 140px;
            height: 140px;
            right: -40px;
            top: -60px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--summary-glow), transparent 68%)
        }

        .report-summary-card--sales {
            --summary-accent: #665cf5;
            --summary-glow: rgba(102, 92, 245, .14);
            background: linear-gradient(135deg, #fff 20%, #f8f7ff)
        }

        .report-summary-card--expense {
            --summary-accent: #ff9d12;
            --summary-glow: rgba(255, 185, 44, .15);
            background: linear-gradient(135deg, #fff 20%, #fffaf0)
        }

        .report-summary-card--staff {
            --summary-accent: #10bb71;
            --summary-glow: rgba(32, 202, 124, .15);
            background: linear-gradient(135deg, #fff 20%, #f2fdf7)
        }

        .report-summary-card--purchase {
            --summary-accent: #3a82ee;
            --summary-glow: rgba(58, 130, 238, .14);
            background: linear-gradient(135deg, #fff 20%, #f4f8ff)
        }

        .report-summary-card:has(.report-summary-icon) {
            gap: 14px
        }

        .report-summary-icon {
            position: relative;
            width: 44px;
            height: 44px;
            flex-basis: 44px;
            border-radius: 12px;
            color: #fff !important;
            background: linear-gradient(145deg, var(--summary-accent), color-mix(in srgb, var(--summary-accent) 72%, #fff)) !important;
            border: 2px solid rgba(255, 255, 255, .7);
            box-shadow: 0 4px 10px color-mix(in srgb, var(--summary-accent) 28%, transparent)
        }

        .report-summary-label {
            margin-top: 2px;
            letter-spacing: .06em;
            color: #475467;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase
        }

        .report-summary-value {
            margin-top: 5px;
            color: #101828;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -.03em
        }

        .report-summary-meta {
            color: #7184aa;
            font-size: 11px;
            margin-top: 3px
        }

        .report-card-spark {
            position: absolute;
            right: 18px;
            bottom: 16px;
            height: 24px;
            display: flex;
            align-items: flex-end;
            gap: 3px
        }

        .report-card-spark i {
            width: 3px;
            border-radius: 2px;
            background: var(--summary-accent);
            opacity: .5
        }

        .report-card-spark i:nth-child(1) {
            height: 9px
        }

        .report-card-spark i:nth-child(2) {
            height: 16px
        }

        .report-card-spark i:nth-child(3) {
            height: 13px
        }

        .report-card-spark i:nth-child(4) {
            height: 20px
        }

        .report-card-spark i:nth-child(5) {
            height: 15px
        }

        .report-card-spark i:nth-child(6) {
            height: 10px
        }

        .reports-tabs {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: #f8f9fc;
            border-bottom: 1px solid #edf0f4;
            overflow-x: auto
        }

        .reports-tab {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            border: 1px solid transparent;
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: all .15s ease
        }

        .reports-tab:hover {
            color: #5146d8;
            background: #edf0f8
        }

        .reports-tab.active {
            color: #ffffff !important;
            background: #5146d8 !important;
            font-weight: 700;
            border-color: #5146d8 !important;
            box-shadow: 0 3px 10px rgba(81, 70, 216, 0.35)
        }

        .reports-tab.active i {
            color: #ffffff !important
        }

        .reports-tab.active::after {
            display: none !important
        }

        .reports-tab i {
            font-size: 13px
        }

        .reports-content {
            padding: 20px
        }

        .report-section-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(300px, .8fr);
            gap: 18px
        }

        .report-column {
            display: grid;
            gap: 18px
        }

        .report-block {
            border: 1px solid #e8eaf0;
            border-radius: 13px;
            padding: 18px;
            background: #fff
        }

        .report-block h2 {
            margin: 0;
            color: #101828;
            font-size: 14px;
            font-weight: 800
        }

        .report-block p {
            margin: 4px 0 0;
            color: #7b8495;
            font-size: 11px
        }

        .report-amount {
            margin: 8px 0;
            color: #101828;
            font-size: 23px;
            font-weight: 800
        }

        .report-empty {
            padding: 34px 12px;
            text-align: center;
            color: #7b8495;
            font-size: 13px
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px
        }

        .report-table th {
            color: #697386;
            background: #f8f9fc;
            font-weight: 700;
            text-align: left
        }

        .report-table th,
        .report-table td {
            padding: 11px 10px;
            border-bottom: 1px solid #edf0f4
        }

        .report-table tr:last-child td {
            border: 0
        }

        .report-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: #edf0f4;
            border-radius: 10px;
            overflow: hidden
        }

        .report-stat {
            background: #fff;
            padding: 13px
        }

        .report-stat span {
            display: block;
            color: #7b8495;
            font-size: 11px
        }

        .report-stat strong {
            display: block;
            margin-top: 4px;
            font-size: 15px
        }

        .bar-chart {
            display: flex;
            align-items: end;
            gap: 8px;
            height: 185px;
            padding: 14px 4px 24px;
            border-bottom: 1px solid #edf0f4
        }

        .bar-item {
            height: 100%;
            flex: 1;
            display: flex;
            align-items: end;
            justify-content: center;
            position: relative
        }

        .bar-item i {
            width: 65%;
            min-height: 2px;
            border-radius: 4px 4px 0 0;
            background: linear-gradient(180deg, #7a6af7, #5748e8)
        }

        .bar-item span {
            position: absolute;
            top: calc(100% + 7px);
            font-size: 10px;
            color: #7b8495;
            white-space: nowrap
        }

        .service-list {
            list-style: none;
            margin: 0;
            padding: 0
        }

        .service-list li {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #edf0f4;
            font-size: 12px
        }

        .service-list li:last-child {
            border: 0
        }

        .service-list small {
            display: block;
            color: #7b8495;
            margin-top: 2px
        }

        .progress-track {
            height: 8px;
            border-radius: 99px;
            background: #ecebff;
            overflow: hidden
        }

        .progress-track i {
            display: block;
            height: 100%;
            background: #5b4be8;
            border-radius: inherit
        }

        /* ============ Filter Modal (redesigned) ============ */
        .filter-modal-content {
            border-radius: 18px;
            border: 1px solid #E7E9F3;
            overflow: hidden;
            box-shadow: 0 24px 48px rgba(15, 23, 42, .16);
        }

        .filter-modal-header {
            background: #ffffff;
            border-bottom: 1px solid #EEF0F7;
            padding: 20px 24px 18px;
            align-items: flex-start;
        }

        .filter-modal-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(81, 70, 216, .12);
            color: #5146D8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .filter-modal-title {
            font-size: 16px;
            font-weight: 800;
            color: #101828;
            margin: 0;
        }

        .filter-modal-subtitle {
            font-size: 12px;
            color: #7C8598;
            display: block;
            margin-top: 2px;
        }

        .filter-modal-body {
            padding: 20px 24px 6px;
        }

        .filter-section-label,
        .filter-field-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #475467;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 8px;
        }

        .quick-range-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .quick-range-btn {
            flex: 1 1 auto;
            min-width: 78px;
            font-size: 12.5px;
            font-weight: 600;
            color: #475467;
            background: #fff;
            border: 1px solid #E2E6F0;
            border-radius: 9px;
            padding: 8px 10px;
            text-align: center;
            transition: all .15s ease;
            cursor: pointer;
        }

        .quick-range-btn:hover {
            border-color: #C7CCE6;
            background: #F8F8FF;
        }

        .quick-range-btn.active {
            background: #5146D8;
            border-color: #5146D8;
            color: #fff;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(81, 70, 216, .28);
        }

        .filter-input-pill {
            display: flex;
            align-items: stretch;
            border: 1px solid #E2E6F0;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .filter-input-pill:focus-within {
            border-color: #5146D8;
            box-shadow: 0 0 0 3px rgba(81, 70, 216, .12);
        }

        .filter-input-icon {
            flex: 0 0 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(81, 70, 216, .10);
            color: #5146D8;
            font-size: 14px;
        }

        .filter-input-icon--muted {
            background: #F1F3F8;
            color: #8A93A8;
        }

        .filter-input-pill--muted {
            border-color: #E2E6F0;
        }

        .filter-input-control {
            flex: 1;
            border: 0;
            outline: none;
            background: transparent;
            padding: 9px 12px;
            font-size: 13px;
            color: #1E293B;
            min-width: 0;
        }

        .filter-input-select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%238A93A8' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 30px;
        }

        .filter-modal-footer {
            background: #F8FAFC;
            border-top: 1px solid #EEF0F7;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-btn {
            font-size: 13px;
            font-weight: 600;
            border-radius: 10px;
            padding: 8px 18px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid transparent;
            text-decoration: none;
            cursor: pointer;
        }

        .filter-btn--ghost {
            background: transparent;
            color: #64748B;
            border-color: transparent;
        }

        .filter-btn--ghost:hover {
            color: #475467;
        }

        .filter-btn--outline {
            background: #fff;
            color: #475467;
            border-color: #E2E6F0;
        }

        .filter-btn--outline:hover {
            background: #F8F8FF;
            border-color: #C7CCE6;
        }

        .filter-btn--primary {
            background: #5146D8;
            border-color: #5146D8;
            color: #fff;
        }

        .filter-btn--primary:hover {
            background: #453BC4;
            border-color: #453BC4;
            color: #fff;
        }

        @media(max-width:1000px) {
            .report-section-grid {
                grid-template-columns: 1fr
            }

            .reports-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }
        }

        @media(max-width:640px) {
            .app-content {
                padding: 16px
            }

            .reports-header {
                align-items: flex-start;
                flex-direction: column
            }

            .reports-header-actions {
                width: 100%;
                flex-wrap: wrap
            }

            .reports-summary-grid {
                grid-template-columns: 1fr
            }

            .report-stat-grid {
                grid-template-columns: repeat(2, 1fr)
            }

            .quick-range-btn {
                min-width: 70px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function setDateRangePreset(type) {
            const today = new Date();
            let start = new Date();
            let end = new Date();

            if (type === 'today') {
                start = new Date(today);
                end = new Date(today);
            } else if (type === 'yesterday') {
                start.setDate(today.getDate() - 1);
                end.setDate(today.getDate() - 1);
            } else if (type === 'this_week') {
                const day = today.getDay();
                const diff = today.getDate() - day + (day === 0 ? -6 : 1);
                start = new Date(today.setDate(diff));
                end = new Date(start);
                end.setDate(start.getDate() + 6);
            } else if (type === 'this_month') {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            } else if (type === 'last_month') {
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
            }

            const formatDate = (d) => {
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            document.getElementById('filter_start_date').value = formatDate(start);
            document.getElementById('filter_end_date').value = formatDate(end);
        }

        (function () {
            const group = document.getElementById('quickRangeGroup');
            if (!group) return;

            const startInput = document.getElementById('filter_start_date');
            const endInput = document.getElementById('filter_end_date');
            const buttons = group.querySelectorAll('.quick-range-btn');

            function setActive(btn) {
                buttons.forEach(b => b.classList.remove('active'));
                if (btn) btn.classList.add('active');
            }

            buttons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const preset = this.dataset.preset;
                    setActive(this);
                    if (preset !== 'custom') {
                        setDateRangePreset(preset);
                    }
                });
            });

            // If the user manually edits either date, treat it as a custom range.
            [startInput, endInput].forEach(input => {
                if (!input) return;
                input.addEventListener('input', function () {
                    setActive(group.querySelector('[data-preset="custom"]'));
                });
            });
        })();
    </script>
@endpush