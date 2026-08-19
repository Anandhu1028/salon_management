@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
    @php
        $tabs = ['sales' => ['Total Sales', 'bi-bar-chart-line'], 'expenses' => ['Total Expenses', 'bi-wallet2'], 'staff' => ['Staff Daily Target', 'bi-people'], 'purchase' => ['Total Purchase', 'bi-cart3']];
        $range = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
    @endphp

    <div class="reports-page">
        <header class="reports-header">
            <div>
                <h1 class="reports-title">Reports</h1>
                <p class="reports-subtitle">View and analyze your business performance</p>
            </div>
            <form class="reports-header-actions" method="GET" action="{{ route('reports.index') }}">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <label class="reports-date-picker"><i class="bi bi-calendar4"></i><input type="date" name="start_date" value="{{ $startDate->toDateString() }}" aria-label="Start date" onchange="this.form.submit()"><span>to</span><input type="date" name="end_date" value="{{ $endDate->toDateString() }}" aria-label="End date" onchange="this.form.submit()"></label>
                <button class="reports-export-btn" type="submit"><i class="bi bi-funnel"></i> Apply</button>
            </form>
        </header>

        <section class="reports-summary-grid" aria-label="Report summary for {{ $range }}">
            <article class="report-summary-card report-summary-card--sales"><span class="report-summary-icon"><i class="bi bi-bag"></i></span><div><span class="report-summary-label">Total Sales</span><strong class="report-summary-value">₹{{ number_format($totalSales, 2) }}</strong><span class="report-summary-meta">{{ $salesCards->count() }} completed job cards</span></div><span class="report-card-spark" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></span></article>
            <article class="report-summary-card report-summary-card--expense"><span class="report-summary-icon"><i class="bi bi-wallet2"></i></span><div><span class="report-summary-label">Total Expenses</span><strong class="report-summary-value">₹{{ number_format($totalExpenses, 2) }}</strong><span class="report-summary-meta">No expense records available</span></div><span class="report-card-spark" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></span></article>
            <article class="report-summary-card report-summary-card--staff"><span class="report-summary-icon"><i class="bi bi-people"></i></span><div><span class="report-summary-label">Staff Daily Target</span><strong class="report-summary-value">₹{{ number_format($staffDailyTarget, 2) }}</strong><span class="report-summary-meta">No staff target configured</span></div><span class="report-card-spark" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></span></article>
            <article class="report-summary-card report-summary-card--purchase"><span class="report-summary-icon"><i class="bi bi-cart3"></i></span><div><span class="report-summary-label">Total Purchase</span><strong class="report-summary-value">₹{{ number_format($totalPurchase, 2) }}</strong><span class="report-summary-meta">{{ $purchaseRows->count() }} purchase transactions</span></div><span class="report-card-spark" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></span></article>
        </section>

        <section class="reports-card">
            <nav class="reports-tabs" aria-label="Report categories">
                @foreach ($tabs as $key => [$label, $icon])
                    <a class="reports-tab {{ $activeTab === $key ? 'active' : '' }}" href="{{ route('reports.index', ['tab' => $key, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"><i class="bi {{ $icon }}"></i>{{ $label }}</a>
                @endforeach
            </nav>
            <div class="reports-content">
                @include('report.partials.' . $activeTab)
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
    <style>
        .app-content{padding:22px 24px 34px}.reports-page{max-width:none}.reports-summary-grid{gap:16px;margin-bottom:22px}.report-summary-card{position:relative;isolation:isolate;min-height:142px;padding:19px 22px;overflow:hidden;border-radius:17px;border:1px solid rgba(255,255,255,.9);box-shadow:0 10px 26px rgba(51,65,114,.08);align-items:flex-start}.report-summary-card:before{content:"";position:absolute;z-index:-1;inset:0;border-left:4px solid var(--summary-accent)}.report-summary-card:after{content:"";position:absolute;z-index:-1;width:160px;height:160px;right:-46px;top:-78px;border-radius:50%;background:radial-gradient(circle,var(--summary-glow),transparent 68%)}.report-summary-card--sales{--summary-accent:#665cf5;--summary-glow:rgba(102,92,245,.19);background:linear-gradient(135deg,#fff 10%,#f4f3ff)}.report-summary-card--expense{--summary-accent:#ff9d12;--summary-glow:rgba(255,185,44,.21);background:linear-gradient(135deg,#fff 10%,#fff9eb)}.report-summary-card--staff{--summary-accent:#10bb71;--summary-glow:rgba(32,202,124,.20);background:linear-gradient(135deg,#fff 10%,#edfcf4)}.report-summary-card--purchase{--summary-accent:#3a82ee;--summary-glow:rgba(58,130,238,.19);background:linear-gradient(135deg,#fff 10%,#edf5ff)}.report-summary-card:has(.report-summary-icon){gap:14px}.report-summary-icon{position:relative;width:49px;height:49px;flex-basis:49px;border-radius:14px;color:#fff!important;background:linear-gradient(145deg,var(--summary-accent),color-mix(in srgb,var(--summary-accent) 72%,#fff))!important;border:3px solid rgba(255,255,255,.72);box-shadow:0 6px 14px color-mix(in srgb,var(--summary-accent) 33%,transparent),inset 0 1px 1px rgba(255,255,255,.5)}.report-summary-label{margin-top:4px;letter-spacing:.075em;color:#2c4168;font-size:10px;font-weight:800;text-transform:uppercase}.report-summary-value{margin-top:6px;color:color-mix(in srgb,var(--summary-accent) 75%,#172554);font-size:25px;letter-spacing:-.045em}.report-summary-meta{color:#7184aa;font-size:11px}.report-card-spark{position:absolute;right:18px;bottom:18px;height:28px;display:flex;align-items:flex-end;gap:3px}.report-card-spark i{width:3px;border-radius:3px 3px 1px 1px;background:var(--summary-accent);opacity:.66}.report-card-spark i:nth-child(1){height:11px}.report-card-spark i:nth-child(2){height:18px}.report-card-spark i:nth-child(3){height:15px}.report-card-spark i:nth-child(4){height:24px}.report-card-spark i:nth-child(5){height:19px}.report-card-spark i:nth-child(6){height:13px}.reports-date-picker input{border:0;outline:0;width:112px;color:#344054;font:inherit;background:transparent}.reports-date-picker span{color:#98a2b3}.reports-content{padding:20px}.report-section-grid{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(300px,.8fr);gap:18px}.report-column{display:grid;gap:18px}.report-block{border:1px solid #e8eaf0;border-radius:13px;padding:18px;background:#fff}.report-block h2{margin:0;color:#101828;font-size:14px;font-weight:800}.report-block p{margin:4px 0 0;color:#7b8495;font-size:11px}.report-amount{margin:8px 0;color:#101828;font-size:23px;font-weight:800}.report-empty{padding:34px 12px;text-align:center;color:#7b8495;font-size:13px}.report-table{width:100%;border-collapse:collapse;font-size:12px}.report-table th{color:#697386;background:#f8f9fc;font-weight:700;text-align:left}.report-table th,.report-table td{padding:11px 10px;border-bottom:1px solid #edf0f4}.report-table tr:last-child td{border:0}.report-stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:#edf0f4;border-radius:10px;overflow:hidden}.report-stat{background:#fff;padding:13px}.report-stat span{display:block;color:#7b8495;font-size:11px}.report-stat strong{display:block;margin-top:4px;font-size:15px}.bar-chart{display:flex;align-items:end;gap:8px;height:185px;padding:14px 4px 24px;border-bottom:1px solid #edf0f4}.bar-item{height:100%;flex:1;display:flex;align-items:end;justify-content:center;position:relative}.bar-item i{width:65%;min-height:2px;border-radius:4px 4px 0 0;background:linear-gradient(180deg,#7a6af7,#5748e8)}.bar-item span{position:absolute;top:calc(100% + 7px);font-size:10px;color:#7b8495;white-space:nowrap}.service-list{list-style:none;margin:0;padding:0}.service-list li{display:flex;justify-content:space-between;gap:12px;padding:12px 0;border-bottom:1px solid #edf0f4;font-size:12px}.service-list li:last-child{border:0}.service-list small{display:block;color:#7b8495;margin-top:2px}.progress-track{height:8px;border-radius:99px;background:#ecebff;overflow:hidden}.progress-track i{display:block;height:100%;background:#5b4be8;border-radius:inherit}@media(max-width:1000px){.report-section-grid{grid-template-columns:1fr}.reports-summary-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:640px){.app-content{padding:16px}.reports-header{align-items:flex-start;flex-direction:column}.reports-header-actions{width:100%;flex-wrap:wrap}.reports-date-picker{flex:1}.reports-summary-grid{grid-template-columns:1fr}.reports-tabs{padding:0;overflow:auto}.reports-tab{padding:0 13px;white-space:nowrap}.report-stat-grid{grid-template-columns:repeat(2,1fr)}}
    </style>
@endpush
