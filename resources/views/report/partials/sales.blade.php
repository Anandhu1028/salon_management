@php
    $maximumDailySales = max(1, $dailySales->max());
    $days = max(1, $dailySales->count());
@endphp
<div class="report-section-grid">
    <div class="report-column">
        <article class="report-block">
            <h2>Sales Overview</h2><div class="report-amount">₹{{ number_format($totalSales, 2) }}</div><p>Total completed sales for the selected period</p>
            @if ($dailySales->sum() > 0)
                <div class="bar-chart" aria-label="Daily sales chart">
                    @foreach ($dailySales as $date => $amount)
                        <div class="bar-item" title="{{ \Carbon\Carbon::parse($date)->format('d M') }}: ₹{{ number_format($amount, 2) }}"><i style="height: {{ max(2, ($amount / $maximumDailySales) * 100) }}%"></i><span>{{ \Carbon\Carbon::parse($date)->format('d M') }}</span></div>
                    @endforeach
                </div>
            @else
                <div class="report-empty">No sales data available for this date range.</div>
            @endif
            <div class="report-stat-grid">
                <div class="report-stat"><span>Average Daily Sales</span><strong>₹{{ number_format($totalSales / $days, 2) }}</strong></div>
                <div class="report-stat"><span>Highest Sales</span><strong>₹{{ number_format($dailySales->max(), 2) }}</strong></div>
                <div class="report-stat"><span>Lowest Sales</span><strong>₹{{ number_format($dailySales->min(), 2) }}</strong></div>
                <div class="report-stat"><span>Total Transactions</span><strong>{{ $salesCards->count() }}</strong></div>
            </div>
        </article>
        <article class="report-block"><h2>Sales Trend Comparison</h2><p>Daily sales within the selected date range</p>@if($dailySales->sum() > 0)<div class="bar-chart">@foreach($dailySales as $date => $amount)<div class="bar-item"><i style="height: {{ max(2, ($amount / $maximumDailySales) * 100) }}%"></i><span>{{ \Carbon\Carbon::parse($date)->format('d M') }}</span></div>@endforeach</div>@else <div class="report-empty">No data available.</div>@endif</article>
    </div>
    <div class="report-column">
        <article class="report-block"><h2>Sales by Payment Method</h2><div class="report-empty">No payment method is stored with completed job cards.</div></article>
        <article class="report-block"><h2>Top Services by Sales</h2>@if($topServices->isNotEmpty())<ul class="service-list">@foreach($topServices as $service)<li><span>{{ $service['name'] }}<small>{{ $service['transactions'] }} transactions</small></span><strong>₹{{ number_format($service['amount'], 2) }}</strong></li>@endforeach</ul>@else<div class="report-empty">No data available.</div>@endif</article>
    </div>
</div>
