<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body { font-family: Calibri, 'Segoe UI', Arial, sans-serif; font-size: 10pt; color: #1E293B; margin: 0; padding: 10px; }
        .report-title-cell { font-size: 16pt; font-weight: bold; color: #5146D8; padding: 12px 6px; }
        .report-meta-cell { font-size: 10pt; color: #64748B; padding: 4px 6px 14px 6px; }
        th { background-color: #5146D8; color: #FFFFFF; font-weight: bold; font-size: 10.5pt; padding: 8px 12px; border: 1pt solid #3D34B3; text-align: left; vertical-align: middle; }
        th.text-center { text-align: center; }
        th.text-right { text-align: right; }
        td { font-size: 10pt; padding: 6px 10px; border: 1pt solid #CBD5E1; vertical-align: middle; color: #1E293B; }
        td.text-center { text-align: center; }
        td.text-right { text-align: right; }
        td.text-bold { font-weight: bold; }
        tr.zebra td { background-color: #F8FAFC; }
        tr.total-row td { background-color: #EDE9FE; font-weight: bold; border-top: 2pt solid #5146D8; border-bottom: 2pt solid #5146D8; }
        .grand-total-val { color: #5146D8; font-weight: bold; font-size: 11pt; }
        .discount-val { color: #DC2626; font-weight: bold; }
    </style>
</head>
<body>

    {{-- ======================================================== --}}
    {{-- 1. SALES REPORT EXCEL --}}
    {{-- ======================================================== --}}
    @if($activeTab === 'sales')
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="8" class="report-title-cell">Total Sales Report</td>
            </tr>
            <tr>
                <td colspan="8" class="report-meta-cell">Period: {{ $range }} | Generated on: {{ now()->format('d M Y, h:i A') }}</td>
            </tr>
            <tr>
                <th class="text-center" style="width: 40px;">#</th>
                <th style="width: 120px;">Date</th>
                <th style="width: 180px;">Job Card</th>
                <th style="width: 180px;">Customer</th>
                <th style="width: 180px;">Staff</th>
                <th style="width: 120px;">Payment Type</th>
                <th class="text-right" style="width: 120px;">Amount (₹)</th>
                <th class="text-right" style="width: 120px;">Discount (₹)</th>
                <th class="text-right" style="width: 130px;">Total (₹)</th>
            </tr>
            @php
                $totAmt = 0;
                $totDisc = 0;
                $totFinal = 0;
            @endphp
            @foreach($salesRows as $idx => $card)
                @php
                    $amt = (float) $card->serviceItems->sum('amount');
                    $disc = (float) $card->discount_amount;
                    $rowTot = max(0, $amt - $disc);

                    $totAmt += $amt;
                    $totDisc += $disc;
                    $totFinal += $rowTot;

                    $staffList = $card->serviceItems->flatMap->staff->pluck('name')->unique()->filter()->join(', ');
                    if (empty($staffList)) {
                        $staffList = $card->primaryStaff?->name ?? ($card->staff->pluck('name')->unique()->join(', ') ?: '—');
                    }

                    $paymentName = $card->paymentType?->name 
                        ?? $card->serviceItems->map(fn($it) => $it->paymentType?->name)->filter()->unique()->first() 
                        ?? 'Cash';
                @endphp
                <tr class="{{ $idx % 2 === 1 ? 'zebra' : '' }}">
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $card->created_at->format('d M Y h:i A') }}</td>
                    <td class="text-bold">{{ $card->job_card_name }}</td>
                    <td>{{ $card->customer?->name ?? '—' }}{{ $card->customer?->phone ? ' (' . $card->customer->phone . ')' : '' }}</td>
                    <td>{{ $staffList }}</td>
                    <td>{{ $paymentName }}</td>
                    <td class="text-right">₹{{ number_format($amt, 2) }}</td>
                    <td class="text-right {{ $disc > 0 ? 'discount-val' : '' }}">{{ $disc > 0 ? '-₹' . number_format($disc, 2) : '₹0.00' }}</td>
                    <td class="text-right text-bold">₹{{ number_format($rowTot, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" class="text-right text-bold">Total ({{ count($salesRows) }} transactions)</td>
                <td class="text-right text-bold">₹{{ number_format($totAmt, 2) }}</td>
                <td class="text-right discount-val">{{ $totDisc > 0 ? '-₹' . number_format($totDisc, 2) : '₹0.00' }}</td>
                <td class="text-right grand-total-val">₹{{ number_format($totFinal, 2) }}</td>
            </tr>
        </table>

    {{-- ======================================================== --}}
    {{-- 2. STAFF DAILY TARGET REPORT EXCEL --}}
    {{-- ======================================================== --}}
    @elseif($activeTab === 'staff')
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="8" class="report-title-cell">Staff Daily Target Report</td>
            </tr>
            <tr>
                <td colspan="8" class="report-meta-cell">Period: {{ $range }} | Generated on: {{ now()->format('d M Y, h:i A') }}</td>
            </tr>
            <tr>
                <th class="text-center" style="width: 40px;">#</th>
                <th style="width: 180px;">Staff Name</th>
                <th style="width: 140px;">Mobile Number</th>
                <th style="width: 120px;">Date</th>
                <th style="width: 180px;">Job Card</th>
                <th style="width: 180px;">Service</th>
                <th style="width: 140px;">Category</th>
                <th class="text-right" style="width: 140px;">Collected Amount (₹)</th>
            </tr>
            @php $totStaffAmt = 0; @endphp
            @foreach($staffRows as $idx => $row)
                @php $totStaffAmt += (float) $row['amount']; @endphp
                <tr class="{{ $idx % 2 === 1 ? 'zebra' : '' }}">
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-bold">{{ $row['staff_name'] }}</td>
                    <td>{{ $row['mobile'] }}</td>
                    <td>{{ $row['date']->format('d M Y h:i A') }}</td>
                    <td>{{ $row['job_card'] }}</td>
                    <td>{{ $row['service'] }}</td>
                    <td>{{ $row['category'] }}</td>
                    <td class="text-right text-bold">₹{{ number_format($row['amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="7" class="text-right text-bold">Total Collected ({{ count($staffRows) }} services)</td>
                <td class="text-right grand-total-val">₹{{ number_format($totStaffAmt, 2) }}</td>
            </tr>
        </table>

    {{-- ======================================================== --}}
    {{-- 3. TOTAL PURCHASE REPORT EXCEL --}}
    {{-- ======================================================== --}}
    @elseif($activeTab === 'purchase')
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="8" class="report-title-cell">Total Purchase Report</td>
            </tr>
            <tr>
                <td colspan="8" class="report-meta-cell">Period: {{ $range }} | Generated on: {{ now()->format('d M Y, h:i A') }}</td>
            </tr>
            <tr>
                <th class="text-center" style="width: 40px;">#</th>
                <th style="width: 120px;">Date</th>
                <th style="width: 200px;">Product Name</th>
                <th style="width: 140px;">Category</th>
                <th style="width: 140px;">Sub Category</th>
                <th style="width: 120px;">Payment Type</th>
                <th class="text-right" style="width: 130px;">Amount (₹)</th>
                <th class="text-right" style="width: 130px;">Total (₹)</th>
            </tr>
            @php $totPurAmt = 0; @endphp
            @foreach($purchaseRows as $idx => $row)
                @php
                    $amt = (float) $row->total_amount;
                    $totPurAmt += $amt;
                    $product = $row->product;
                @endphp
                <tr class="{{ $idx % 2 === 1 ? 'zebra' : '' }}">
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $row->purchase_date->format('d M Y') }}</td>
                    <td class="text-bold">{{ $product?->product_name ?? '—' }}</td>
                    <td>{{ $product?->category ?? 'Uncategorised' }}</td>
                    <td>{{ $product?->subcategory ?? '—' }}</td>
                    <td>{{ $row->paymentType?->name ?? 'Cash' }}</td>
                    <td class="text-right">₹{{ number_format($amt, 2) }}</td>
                    <td class="text-right text-bold">₹{{ number_format($amt, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" class="text-right text-bold">Grand Total ({{ count($purchaseRows) }} purchases)</td>
                <td class="text-right text-bold">₹{{ number_format($totPurAmt, 2) }}</td>
                <td class="text-right grand-total-val">₹{{ number_format($totPurAmt, 2) }}</td>
            </tr>
        </table>

    {{-- ======================================================== --}}
    {{-- 4. TOTAL EXPENSES REPORT EXCEL --}}
    {{-- ======================================================== --}}
    @elseif($activeTab === 'expenses')
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="7" class="report-title-cell">Total Expenses Report</td>
            </tr>
            <tr>
                <td colspan="7" class="report-meta-cell">Period: {{ $range }} | Generated on: {{ now()->format('d M Y, h:i A') }}</td>
            </tr>
            <tr>
                <th class="text-center" style="width: 40px;">#</th>
                <th style="width: 120px;">Date</th>
                <th style="width: 200px;">Expense Name</th>
                <th style="width: 150px;">Category</th>
                <th style="width: 150px;">Vendor / Payee</th>
                <th style="width: 120px;">Payment Type</th>
                <th class="text-right" style="width: 140px;">Amount (₹)</th>
            </tr>
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #64748B;">No expense records available for this period.</td>
            </tr>
            <tr class="total-row">
                <td colspan="6" class="text-right text-bold">Grand Total</td>
                <td class="text-right grand-total-val">₹0.00</td>
            </tr>
        </table>
    @endif

</body>
</html>
