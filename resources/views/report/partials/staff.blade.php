@php
    /*
     * Flatten all completed sales cards into one row per service item.
     * Each row contains: staff, mobile, date, job card, service, category, amount.
     */

    /* Map known service categories → pill CSS class */
    $catPillMap = [
        'hair care'    => 'supplies',
        'skin care'    => 'products',
        'nail care'    => 'utilities',
        'spa'          => 'maintenance',
        'makeup'       => 'marketing',
        'massage'      => 'meals',
        'supplies'     => 'supplies',
        'products'     => 'products',
        'utilities'    => 'utilities',
        'maintenance'  => 'maintenance',
        'marketing'    => 'marketing',
        'meals'        => 'meals',
    ];

    $staffServiceRows = $salesCards
        ->flatMap(function ($card) {
            return $card->serviceItems->flatMap(function ($item) use ($card) {
                $assignedStaff = $item->staff;
                if ($assignedStaff->isEmpty() && $card->primaryStaff) {
                    $assignedStaff = collect([$card->primaryStaff]);
                }
                if ($assignedStaff->isEmpty() && $card->staff->isNotEmpty()) {
                    $assignedStaff = $card->staff;
                }

                if ($assignedStaff->isNotEmpty()) {
                    return $assignedStaff->map(function ($staff) use ($card, $item) {
                        return [
                            'staff_name'   => $staff->name,
                            'mobile'       => $staff->mobile_number ?? '—',
                            'date'         => $card->created_at,
                            'job_card'     => $card->job_card_name,
                            'job_card_id'  => $card->id,
                            'service'      => $item->service?->service_name ?? $item->subcategory ?? '—',
                            'category'     => $item->service?->category ?? '—',
                            'amount'       => (float) $item->amount,
                        ];
                    });
                }

                return collect([[
                    'staff_name'   => '—',
                    'mobile'       => '—',
                    'date'         => $card->created_at,
                    'job_card'     => $card->job_card_name,
                    'job_card_id'  => $card->id,
                    'service'      => $item->service?->service_name ?? $item->subcategory ?? '—',
                    'category'     => $item->service?->category ?? '—',
                    'amount'       => (float) $item->amount,
                ]]);
            });
        })
        ->sortByDesc('date')
        ->values();

    /* Grand total (all rows) */
    $staffGrandTotal = $staffServiceRows->sum('amount');

    /* Pagination */
    $stfPage    = max(1, (int) request('stf_page', 1));
    $stfPerPage = (int) request('stf_per_page', 10);
    $stfPerPage = in_array($stfPerPage, [10, 25, 50, 100], true) ? $stfPerPage : 10;

    $stfPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
        $staffServiceRows->forPage($stfPage, $stfPerPage)->values(),
        $staffServiceRows->count(),
        $stfPerPage,
        $stfPage,
        ['path' => request()->url(), 'pageName' => 'stf_page']
    );
    $stfPaginator->appends(request()->except('stf_page'));
@endphp

<article class="report-block report-block--wide">
    <div class="sales-report-header">
        <div>
            <h2>Staff Daily Target</h2>
            <p>Service-level collection records for the selected period</p>
        </div>
    </div>

    @if($stfPaginator->isNotEmpty())
        <div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Staff Name</th>
                        <th>Mobile Number</th>
                        <th>Date</th>
                        <th>Job Card</th>
                        <th>Service</th>
                        <th>Category</th>
                        <th style="text-align:right">Collected Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stfPaginator as $i => $row)
                        <tr>
                            <td style="color:#98a2b3; font-weight:700;">{{ $stfPaginator->firstItem() + $i }}</td>
                            <td>
                                <div class="staff-member-cell">
                                    @php
                                        $initial = strtoupper(substr(trim($row['staff_name']), 0, 1));
                                        $initial = $initial ?: '—';
                                    @endphp
                                    <div class="staff-avatar staff-avatar--indigo">{{ $initial }}</div>
                                    <span class="staff-member-name">{{ $row['staff_name'] }}</span>
                                </div>
                            </td>
                            <td>
                                @if(!empty($row['mobile']) && $row['mobile'] !== '—')
                                    <div class="report-contact-cell">
                                        <svg class="report-contact-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                        </svg>
                                        <span>{{ $row['mobile'] }}</span>
                                    </div>
                                @else
                                    <span style="color:#98a2b3; font-size:12px;">—</span>
                                @endif
                            </td>
                            <td class="sales-col-date">
                                <span class="sales-date-day">{{ $row['date']->format('d M Y') }}</span>
                            </td>
                            <td>
                                <a class="sales-jobcard-link" href="{{ route('job-cards.index', ['search' => $row['job_card']]) }}">
                                    {{ $row['job_card'] }}
                                </a>
                            </td>
                            <td style="color:#344054; font-size:12px; font-weight:500;">{{ $row['service'] }}</td>
                            <td>
                                @if($row['category'] !== '—')
                                    @php $catCls = $catPillMap[strtolower($row['category'])] ?? 'other'; @endphp
                                    <span class="category-pill category-pill--{{ $catCls }}">
                                        {{ $row['category'] }}
                                    </span>
                                @else
                                    <span style="color:#c0c8d8; font-size:11px;">—</span>
                                @endif
                            </td>
                            <td style="text-align:right; font-weight:700; color:#172033;">
                                ₹{{ number_format($row['amount'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="sales-totals-row">
                        <td colspan="7" style="text-align:right; padding:12px; color:#697386; font-size:11px; font-weight:700;">
                            Total Collected ({{ $staffServiceRows->count() }} services)
                        </td>
                        <td style="text-align:right; padding:14px 12px;">
                            <span style="display:block; color:#697386; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;">Grand Total</span>
                            <strong style="font-size:14px; color:#5146d8;">₹{{ number_format($staffGrandTotal, 2) }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="sales-table-footer">
            <span class="sales-table-count">
                Showing {{ $stfPaginator->firstItem() }} to {{ $stfPaginator->lastItem() }} of {{ $stfPaginator->total() }} entries
            </span>

            <div class="sales-table-pagination">
                <form method="GET" action="{{ route('reports.index') }}" class="per-page-form">
                    <input type="hidden" name="tab"        value="staff">
                    <input type="hidden" name="start_date" value="{{ $startDate->toDateString() }}">
                    <input type="hidden" name="end_date"   value="{{ $endDate->toDateString() }}">
                    <select name="stf_per_page" class="per-page-select" onchange="this.form.submit()">
                        @foreach([10, 25, 50, 100] as $opt)
                            <option value="{{ $opt }}" {{ $stfPerPage == $opt ? 'selected' : '' }}>{{ $opt }} per page</option>
                        @endforeach
                    </select>
                </form>

                <div class="pagination-buttons">
                    <a class="page-btn {{ $stfPaginator->onFirstPage() ? 'disabled' : '' }}"
                       href="{{ $stfPaginator->onFirstPage() ? '#' : $stfPaginator->previousPageUrl() }}">
                        <i class="bi bi-chevron-left"></i>
                    </a>

                    @foreach($stfPaginator->getUrlRange(max(1, $stfPaginator->currentPage() - 2), min($stfPaginator->lastPage(), $stfPaginator->currentPage() + 2)) as $pageNum => $url)
                        <a class="page-btn {{ $pageNum == $stfPaginator->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $pageNum }}</a>
                    @endforeach

                    <a class="page-btn {{ !$stfPaginator->hasMorePages() ? 'disabled' : '' }}"
                       href="{{ $stfPaginator->hasMorePages() ? $stfPaginator->nextPageUrl() : '#' }}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="report-empty-state">
            <i class="bi bi-people"></i>
            <p>No staff service records found for this period.</p>
            <small>Completed job cards with assigned staff will appear here.</small>
        </div>
    @endif
</article>