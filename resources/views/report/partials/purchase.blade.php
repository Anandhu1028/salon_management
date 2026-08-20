@php
    $purchaseCategoryStyles = [
        'Supplies'    => ['bi-box-seam',        'supplies'],
        'Meals'       => ['bi-egg-fried',        'meals'],
        'Utilities'   => ['bi-lightning-charge', 'utilities'],
        'Products'    => ['bi-droplet',          'products'],
        'Maintenance' => ['bi-tools',            'maintenance'],
        'Marketing'   => ['bi-megaphone',        'marketing'],
    ];

    $purchasePaymentStyles = [
        'Cash' => ['bi-cash-coin',   'cash'],
        'UPI'  => ['bi-phone',       'upi'],
        'Card' => ['bi-credit-card', 'card'],
    ];

    /* Grand totals always across ALL rows (not just current page) */
    $grandTotalAmount = $purchaseRows->sum(fn($r) => (float) $r->total_amount);
    $grandTotal       = $grandTotalAmount;

    /* Pagination */
    $purPage    = max(1, (int) request('pur_page', 1));
    $purPerPage = (int) request('pur_per_page', 10);
    $purPerPage = in_array($purPerPage, [10, 25, 50, 100], true) ? $purPerPage : 10;

    $purPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
        $purchaseRows->forPage($purPage, $purPerPage)->values(),
        $purchaseRows->count(),
        $purPerPage,
        $purPage,
        ['path' => request()->url(), 'pageName' => 'pur_page']
    );
    $purPaginator->appends(request()->except('pur_page'));
@endphp

<article class="report-block report-block--wide">
    <div class="sales-report-header">
        <div>
            <h2>Total Purchase</h2>
            <p>Product purchases for the selected period</p>
        </div>
       
    </div>

    @if($purchaseRows->isNotEmpty())
        <div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Date</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Payment Type</th>
                        <th style="text-align:right">Amount (₹)</th>
                        <th style="text-align:right">Total (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purPaginator as $i => $row)
                        @php
                            $amount   = (float) $row->total_amount;
                            $total    = $amount;

                            $product     = $row->product;
                            $productName = $product?->product_name ?? '—';
                            $category    = $product?->category ?? 'Uncategorised';
                            $subCategory = $product?->subcategory ?? '—';
                            [$catIcon, $catCls] = $purchaseCategoryStyles[$category] ?? ['bi-tag', 'other'];

                            /* Payment (not stored on purchase yet) */
                            $paymentName = $row->paymentType?->name ?? null;
                            [$payIcon, $payCls] = $purchasePaymentStyles[$paymentName ?? ''] ?? ['bi-cash-coin', 'cash'];
                        @endphp
                        <tr>
                            <td style="color:#98a2b3; font-weight:700;">{{ $purPaginator->firstItem() + $i }}</td>
                            <td class="sales-col-date">
                                <span class="sales-date-day">{{ $row->purchase_date->format('d M Y') }}</span>
                               
                            </td>
                            <td>
                                <span class="sales-customer-name">{{ $productName }}</span>
                            </td>
                            <td>
                                <span class="category-pill category-pill--{{ $catCls }}">
                                    <i class="bi {{ $catIcon }}"></i> {{ $category }}
                                </span>
                            </td>
                            <td style="color:#697386; font-size:12px;">
                                {{ $subCategory }}
                            </td>
                            <td>
                                <span class="payment-pill payment-pill--{{ $payCls }}">
                                    <i class="bi {{ $payIcon }}"></i> {{ $paymentName ?? 'Cash' }}
                                </span>
                            </td>
                            <td style="text-align:right; font-weight:700; color:#172033;">
                                ₹{{ number_format($amount, 2) }}
                            </td>
                           
                            <td style="text-align:right; font-weight:800; color:#172033;">
                                ₹{{ number_format($total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="sales-totals-row">
                        <td colspan="6"></td>
                        <td style="text-align:right; padding:14px 12px;">
                            <span style="display:block; color:#697386; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;">Total Amount</span>
                            <strong style="font-size:13px; color:#172033;">₹{{ number_format($grandTotalAmount, 2) }}</strong>
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
                Showing {{ $purPaginator->firstItem() }} to {{ $purPaginator->lastItem() }} of {{ $purPaginator->total() }} entries
            </span>

            <div class="sales-table-pagination">
                <form method="GET" action="{{ route('reports.index') }}" class="per-page-form">
                    <input type="hidden" name="tab" value="purchase">
                    <input type="hidden" name="start_date" value="{{ $startDate->toDateString() }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->toDateString() }}">
                    <select name="pur_per_page" class="per-page-select" onchange="this.form.submit()">
                        @foreach([10, 25, 50, 100] as $opt)
                            <option value="{{ $opt }}" {{ $purPerPage == $opt ? 'selected' : '' }}>{{ $opt }} per page</option>
                        @endforeach
                    </select>
                </form>

                <div class="pagination-buttons">
                    <a class="page-btn {{ $purPaginator->onFirstPage() ? 'disabled' : '' }}"
                       href="{{ $purPaginator->onFirstPage() ? '#' : $purPaginator->previousPageUrl() }}">
                        <i class="bi bi-chevron-left"></i>
                    </a>

                    @foreach($purPaginator->getUrlRange(max(1, $purPaginator->currentPage() - 2), min($purPaginator->lastPage(), $purPaginator->currentPage() + 2)) as $pageNum => $url)
                        <a class="page-btn {{ $pageNum == $purPaginator->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $pageNum }}</a>
                    @endforeach

                    <a class="page-btn {{ !$purPaginator->hasMorePages() ? 'disabled' : '' }}"
                       href="{{ $purPaginator->hasMorePages() ? $purPaginator->nextPageUrl() : '#' }}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="report-empty-state">
            <i class="bi bi-cart3"></i>
            <p>No purchase records found for this period.</p>
            <small>Purchases will appear here once product purchase entries are added.</small>
        </div>
    @endif
</article>