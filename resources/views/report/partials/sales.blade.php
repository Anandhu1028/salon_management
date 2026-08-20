@php
    $paymentStyles = [
        'Cash' => ['bi-cash-coin', 'cash'],
        'UPI'  => ['bi-phone',     'upi'],
        'Card' => ['bi-credit-card','card'],
    ];

    /* Apply search + payment-method filter, then sort newest-first */
    $salesSearch   = trim($salesSearch ?? '');
    $paymentFilter = trim($paymentFilter ?? '');

    $allRows = $salesCards->filter(function ($card) use ($salesSearch, $paymentFilter) {
        if ($salesSearch !== '') {
            $cardStaffNames = $card->serviceItems->flatMap->staff->pluck('name')->unique()->join(' ');
            $match = str_contains(strtolower($card->job_card_name), strtolower($salesSearch))
                  || str_contains(strtolower($card->customer?->name ?? ''), strtolower($salesSearch))
                  || str_contains(strtolower($card->customer?->phone ?? ''), strtolower($salesSearch))
                  || str_contains(strtolower($card->primaryStaff?->name ?? ''), strtolower($salesSearch))
                  || str_contains(strtolower($cardStaffNames), strtolower($salesSearch));
            if (! $match) return false;
        }
        if ($paymentFilter !== '') {
            $cardPayments = collect([$card->paymentType?->name])
                ->concat($card->serviceItems->map(fn($it) => $it->paymentType?->name))
                ->filter()
                ->unique();
            if (! $cardPayments->contains($paymentFilter)) {
                return false;
            }
        }
        return true;
    })->sortByDesc('created_at')->values();

    /* Totals across all rows */
    $grandAmount   = (float) $allRows->sum(fn($c) => (float) $c->serviceItems->sum('amount'));
    $grandDiscount = (float) $allRows->sum(fn($c) => (float) $c->discount_amount);
    $grandTotal    = max(0, $grandAmount - $grandDiscount);

    /* Pagination */
    $salesPageNum = max(1, (int) request('sales_page', 1));
    $salesPerPage = (int) request('sales_per_page', 10);
    $salesPerPage = in_array($salesPerPage, [10, 25, 50, 100], true) ? $salesPerPage : 10;

    $salesPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
        $allRows->forPage($salesPageNum, $salesPerPage)->values(),
        $allRows->count(),
        $salesPerPage,
        $salesPageNum,
        ['path' => request()->url(), 'pageName' => 'sales_page']
    );
    $salesPaginator->appends(request()->except('sales_page'));
@endphp

<article class="report-block report-block--wide">
    <div class="sales-report-header">
        <div>
            <h2>Sales Report</h2>
            <p>Detailed sales transactions for the selected period</p>
        </div>
    </div>

    @if($salesPaginator->isNotEmpty())
        <div class="report-table-wrap">
            <table class="report-table sales-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Job Card</th>
                        <th>Customer</th>
                        <th>Staff</th>
                        <th>Payment Type</th>
                        <th style="text-align:right">Amount (₹)</th>
                        <th style="text-align:right">Discount (₹)</th>
                        <th style="text-align:right">Total (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesPaginator as $card)
                        @php
                            $paymentName = $card->paymentType?->name 
                                ?? $card->serviceItems->map(fn($it) => $it->paymentType?->name)->filter()->unique()->first() 
                                ?? 'Cash';
                            [$icon, $cls] = $paymentStyles[$paymentName] ?? ['bi-wallet2', 'other'];
                            $amount   = (float) $card->serviceItems->sum('amount');
                            $discount = (float) $card->discount_amount;
                            $rowTotal = max(0, $amount - $discount);
                            
                            $staffList = $card->serviceItems->flatMap->staff->pluck('name')->unique()->filter()->join(', ');
                            if (empty($staffList)) {
                                $staffList = $card->primaryStaff?->name ?? ($card->staff->pluck('name')->unique()->join(', ') ?: '—');
                            }
                        @endphp
                        <tr>
                            <td class="sales-col-date">
                                <span class="sales-date-day">{{ $card->created_at->format('d M Y') }}</span>
                                <span class="sales-date-time">{{ $card->created_at->format('h:i a') }}</span>
                            </td>
                            <td>
                                <a class="sales-jobcard-link"
                                   href="{{ route('job-cards.index', ['search' => $card->job_card_name]) }}">
                                    {{ $card->job_card_name }}
                                </a>
                            </td>
                            <td>
                                <span class="sales-customer-name">{{ $card->customer?->name ?? '—' }}</span>
                                @if($card->customer?->phone)
                                    <span class="report-contact-cell" style="margin-top: 3px; font-size: 11px;">
                                        <svg class="report-contact-icon" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                        </svg>
                                        <span>{{ $card->customer->phone }}</span>
                                    </span>
                                @endif
                            </td>
                            <td>{{ $staffList }}</td>
                            <td>
                                <span class="payment-pill payment-pill--{{ $cls }}">
                                    <i class="bi {{ $icon }}"></i> {{ $paymentName }}
                                </span>
                            </td>
                            <td style="text-align:right; font-weight:700; color:#172033;">
                                ₹{{ number_format($amount, 2) }}
                            </td>
                            <td style="text-align:right; font-weight:700; color:{{ $discount > 0 ? '#e0242a' : '#7b8495' }};">
                                {{ $discount > 0 ? '-₹'.number_format($discount, 2) : '₹0.00' }}
                            </td>
                            <td style="text-align:right; font-weight:800; color:#172033;">
                                ₹{{ number_format($rowTotal, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="sales-totals-row">
                        <td colspan="5" style="text-align:right; font-weight:700; font-size:12px; color:#697386; padding:14px 12px;">
                            Total ({{ $allRows->count() }} transactions)
                        </td>
                        <td style="text-align:right; padding:14px 12px;">
                            <span style="display:block; color:#697386; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;">Total Amount</span>
                            <strong style="font-size:13px; color:#172033;">₹{{ number_format($grandAmount, 2) }}</strong>
                        </td>
                        <td style="text-align:right; padding:14px 12px;">
                            <span style="display:block; color:#697386; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;">Total Discount</span>
                            <strong style="font-size:13px; color:{{ $grandDiscount > 0 ? '#e0242a' : '#7b8495' }};">
                                {{ $grandDiscount > 0 ? '-₹'.number_format($grandDiscount, 2) : '₹0.00' }}
                            </strong>
                        </td>
                        <td style="text-align:right; padding:14px 12px;">
                            <span style="display:block; color:#697386; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;">Grand Total</span>
                            <strong style="font-size:14px; color:#5146d8;">₹{{ number_format($grandTotal, 2) }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="sales-table-footer">
            <span class="sales-table-count">
                Showing {{ $salesPaginator->firstItem() }} to {{ $salesPaginator->lastItem() }} of {{ $salesPaginator->total() }} entries
            </span>

            <div class="sales-table-pagination">
                <form method="GET" action="{{ route('reports.index') }}" class="per-page-form">
                    <input type="hidden" name="tab"        value="sales">
                    <input type="hidden" name="start_date" value="{{ $startDate->toDateString() }}">
                    <input type="hidden" name="end_date"   value="{{ $endDate->toDateString() }}">
                    <input type="hidden" name="search"     value="{{ $salesSearch }}">
                    <input type="hidden" name="payment_method" value="{{ $paymentFilter }}">
                    <select name="sales_per_page" class="per-page-select" onchange="this.form.submit()">
                        @foreach([10, 25, 50, 100] as $opt)
                            <option value="{{ $opt }}" {{ $salesPerPage == $opt ? 'selected' : '' }}>{{ $opt }} per page</option>
                        @endforeach
                    </select>
                </form>

                <div class="pagination-buttons">
                    <a class="page-btn {{ $salesPaginator->onFirstPage() ? 'disabled' : '' }}"
                       href="{{ $salesPaginator->onFirstPage() ? '#' : $salesPaginator->previousPageUrl() }}">
                        <i class="bi bi-chevron-left"></i>
                    </a>

                    @foreach($salesPaginator->getUrlRange(max(1, $salesPaginator->currentPage() - 2), min($salesPaginator->lastPage(), $salesPaginator->currentPage() + 2)) as $pageNum => $url)
                        <a class="page-btn {{ $pageNum == $salesPaginator->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $pageNum }}</a>
                    @endforeach

                    <a class="page-btn {{ !$salesPaginator->hasMorePages() ? 'disabled' : '' }}"
                       href="{{ $salesPaginator->hasMorePages() ? $salesPaginator->nextPageUrl() : '#' }}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="report-empty">No completed sales transactions match your filters.</div>
    @endif
</article>