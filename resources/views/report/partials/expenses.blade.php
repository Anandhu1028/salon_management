@php
    $expenseCategoryStyles = [
        'Supplies'    => ['bi-box-seam',        'supplies'],
        'Meals'       => ['bi-egg-fried',        'meals'],
        'Utilities'   => ['bi-lightning-charge', 'utilities'],
        'Products'    => ['bi-droplet',          'products'],
        'Maintenance' => ['bi-tools',            'maintenance'],
        'Marketing'   => ['bi-megaphone',        'marketing'],
    ];
@endphp

<article class="report-block report-block--wide">
    <div class="sales-report-header">
        <div>
            <h2>Total Expenses</h2>
            <p>Detailed expense transactions for the selected period</p>
        </div>
        
    </div>

    {{-- No expense model exists yet, show a styled empty state --}}
    @if($totalExpenses <= 0)
        <div class="report-empty-state">
            <i class="bi bi-wallet2"></i>
            <p>No expense records available for this period.</p>
            <small>Expense tracking will appear here once expense records are added.</small>
        </div>
    @else
        <div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Date</th>
                        <th>Expense Name</th>
                        <th>Category</th>
                        <th>Vendor / Payee</th>
                        <th>Payment Type</th>
                        <th style="text-align:right">Amount (₹)</th>
                        <th style="text-align:center; width:60px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="8" class="report-empty">No expense data to display.</td></tr>
                </tbody>
            </table>
        </div>
    @endif
</article>