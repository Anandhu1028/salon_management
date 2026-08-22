@php
    $paymentStyles = [
        'Cash' => ['bi-cash-coin', 'cash'],
        'UPI' => ['bi-phone', 'upi'],
        'Card' => ['bi-credit-card', 'card'],
    ];
@endphp

<article class="report-block report-block--wide">
    <div class="sales-report-header">
        <div>
            <h2>Total Expenses</h2>
            <p>Detailed expense transactions for the selected period</p>
        </div>
        
    </div>

    @if($expenseRows->isEmpty())
        <div class="report-empty-state">
            <i class="bi bi-wallet2"></i>
            <p>No expense records available for this period.</p>
            <small>Try changing the date range or add a new expense record.</small>
        </div>
    @else
        <div class="report-table-wrap">
            <table class="report-table sales-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Date</th>
                        <th>Expense Name</th>
                        <th>Category</th>
                        <th>Staff Name</th>
                        <th>Payment Type</th>
                        <th style="text-align:right">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenseRows as $expense)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="sales-col-date">
                                <span class="sales-date-day">{{ $expense->expense_date->format('d M Y') }}</span>
                            </td>
                            <td><span class="sales-customer-name">{{ $expense->description ?: '—' }}</span></td>
                            <td>{{ $expense->category?->name ?? '—' }}</td>
                            <td><span class="sales-customer-name">{{ $expense->staff?->name ?? '—' }}</span></td>
                            <td>
                                @php([$paymentIcon, $paymentClass] = $paymentStyles[$expense->payment_method] ?? ['bi-wallet2', 'other'])
                                <span class="payment-pill payment-pill--{{ $paymentClass }}">
                                    <i class="bi {{ $paymentIcon }}"></i> {{ $expense->payment_method ?: '—' }}
                                </span>
                            </td>
                            <td style="text-align:right; font-weight:800; color:#172033;">₹{{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="sales-totals-row">
                        <td colspan="6" style="text-align:right; font-weight:700; font-size:12px; color:#697386; padding:14px 12px;">
                            Total ({{ $expenseRows->count() }} {{ \Illuminate\Support\Str::plural('expense', $expenseRows->count()) }})
                        </td>
                        <td style="text-align:right; padding:14px 12px;">
                            <span style="display:block; color:#697386; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;">Grand Total</span>
                            <strong style="font-size:14px; color:#5146d8;">₹{{ number_format($totalExpenses, 2) }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</article>
