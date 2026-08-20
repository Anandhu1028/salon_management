<?php

namespace App\Http\Controllers;

use App\Models\JobCard;
use App\Models\ProductPurchase;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $this->date($request->input('start_date'), now()->startOfWeek());
        $endDate = $this->date($request->input('end_date'), now()->endOfWeek());

        if ($endDate->lt($startDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $activeTab = in_array($request->input('tab'), ['sales', 'expenses', 'staff', 'purchase'], true)
            ? $request->input('tab')
            : 'sales';

        $salesCards = JobCard::query()
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->with([
                'serviceItems.service',
                'serviceItems.staff',
                'serviceItems.paymentType',
                'primaryStaff',
                'staff',
                'customer',
                'customers',
                'paymentType',
            ])
            ->get();

        $cardTotal = fn (JobCard $card): float => max(0, (float) $card->serviceItems->sum('amount') - (float) $card->discount_amount);
        $totalSales = $salesCards->sum($cardTotal);

        $dailySales = collect(CarbonPeriod::create($startDate, $endDate))
            ->mapWithKeys(fn (Carbon $date) => [$date->toDateString() => 0.0]);

        foreach ($salesCards as $card) {
            $key = $card->created_at->toDateString();
            $dailySales[$key] = ($dailySales[$key] ?? 0) + $cardTotal($card);
        }

        $topServices = $salesCards->flatMap->serviceItems
            ->groupBy(fn ($item) => $item->service?->service_name ?? $item->subcategory ?? 'Uncategorised')
            ->map(fn ($items, $name) => ['name' => $name, 'amount' => $items->sum('amount'), 'transactions' => $items->count()])
            ->sortByDesc('amount')->take(5)->values();

        $purchaseRows = ProductPurchase::query()->with('product')
            ->whereBetween('purchase_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderByDesc('purchase_date')->get();

        $staffPerformance = $salesCards->flatMap(function (JobCard $card) {
            return $card->serviceItems->flatMap(function ($item) use ($card) {
                $assignedStaff = $item->staff;
                if ($assignedStaff->isEmpty() && $card->primaryStaff) {
                    $assignedStaff = collect([$card->primaryStaff]);
                }
                if ($assignedStaff->isEmpty() && $card->staff->isNotEmpty()) {
                    $assignedStaff = $card->staff;
                }

                return $assignedStaff->map(fn ($s) => [
                    'name' => $s->name,
                    'mobile' => $s->mobile_number,
                    'achieved' => (float) $item->amount,
                ]);
            });
        })
        ->groupBy('name')
        ->map(fn ($rows, $name) => ['name' => $name, 'achieved' => $rows->sum('achieved')])
        ->sortByDesc('achieved')
        ->values();

        $paymentBreakdown = $salesCards->groupBy(function (JobCard $card) {
            return $card->paymentType?->name 
                ?? $card->serviceItems->first()?->paymentType?->name 
                ?? 'Not recorded';
        })
            ->map(fn ($cards, $name) => ['name' => $name, 'amount' => $cards->sum($cardTotal), 'count' => $cards->count()])
            ->sortByDesc('amount')->values();

        // ----------------------------------------------------------------
        // Sales table: search + payment-method filter + pagination
        // ----------------------------------------------------------------
        $salesSearch = trim($request->input('search', ''));
        $paymentFilter = trim($request->input('payment_method', ''));

        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;
        $page = max(1, (int) $request->input('page', 1));

        $paymentMethods = $salesCards->flatMap(function (JobCard $card) {
            $types = collect([$card->paymentType?->name]);
            foreach ($card->serviceItems as $item) {
                if ($item->paymentType?->name) {
                    $types->push($item->paymentType->name);
                }
            }
            return $types;
        })->filter()->unique()->sort()->values();

        $filteredSalesRows = $salesCards->filter(function (JobCard $card) use ($salesSearch, $paymentFilter) {
            if ($salesSearch !== '') {
                $match = str_contains(strtolower($card->job_card_name), strtolower($salesSearch))
                    || str_contains(strtolower($card->customer?->name ?? ''), strtolower($salesSearch));

                if (! $match) {
                    return false;
                }
            }

            if ($paymentFilter !== '' && ($card->paymentType?->name ?? 'Not recorded') !== $paymentFilter) {
                return false;
            }

            return true;
        })->sortByDesc('created_at')->values();

        $salesPage = new LengthAwarePaginator(
            $filteredSalesRows->forPage($page, $perPage)->values(),
            $filteredSalesRows->count(),
            $perPage,
            $page,
            ['path' => $request->url()]
        );
        $salesPage->appends($request->except('page'));

        $filterStaff = \App\Models\Staff::where('status', 'active')->orderBy('name')->get();
        $filterCustomers = \App\Models\Customer::orderBy('name')->get();
        $filterCategories = \App\Models\Service::whereNotNull('category')->distinct()->pluck('category')->filter()->values();

        return view('report.index', [
            'activeTab' => $activeTab, 'startDate' => $startDate, 'endDate' => $endDate,
            'totalSales' => $totalSales, 'totalExpenses' => 0.0, 'staffDailyTarget' => 0.0,
            'staffAchieved' => $staffPerformance->sum('achieved'), 'totalPurchase' => $purchaseRows->sum->total_amount,
            'totalQuantity' => $purchaseRows->sum('quantity'), 'purchaseRows' => $purchaseRows,
            'salesCards' => $salesCards, 'dailySales' => $dailySales, 'topServices' => $topServices,
            'staffPerformance' => $staffPerformance,
            'salesPage' => $salesPage, 'perPage' => $perPage, 'paymentFilter' => $paymentFilter,
            'paymentMethods' => $paymentMethods,
            'salesSearch' => $salesSearch, 'paymentBreakdown' => $paymentBreakdown, 'cardTotal' => $cardTotal,
            'filterStaff' => $filterStaff,
            'filterCustomers' => $filterCustomers,
            'filterCategories' => $filterCategories,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $startDate = $this->date($request->input('start_date'), now()->startOfWeek());
        $endDate = $this->date($request->input('end_date'), now()->endOfWeek());

        if ($endDate->lt($startDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $activeTab = in_array($request->input('tab'), ['sales', 'expenses', 'staff', 'purchase'], true)
            ? $request->input('tab')
            : 'sales';

        $range = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');

        $salesCards = JobCard::query()
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->with([
                'serviceItems.service',
                'serviceItems.staff',
                'serviceItems.paymentType',
                'primaryStaff',
                'staff',
                'customer',
                'paymentType',
            ])
            ->get();

        // Filters
        $salesSearch   = trim($request->input('search', ''));
        $paymentFilter = trim($request->input('payment_method', ''));
        $staffFilter   = trim($request->input('staff_id', ''));
        $categoryFilter= trim($request->input('category', ''));

        $salesRows = $salesCards->filter(function (JobCard $card) use ($salesSearch, $paymentFilter, $staffFilter) {
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
                if (! $cardPayments->contains($paymentFilter)) return false;
            }
            if ($staffFilter !== '') {
                $staffIds = collect([$card->staff_id])
                    ->concat($card->serviceItems->flatMap->staff->pluck('id'))
                    ->concat($card->staff->pluck('id'))
                    ->filter()
                    ->map(fn($id) => (string) $id);
                if (! $staffIds->contains((string) $staffFilter)) return false;
            }
            return true;
        })->sortByDesc('created_at')->values();

        // Staff rows (flattened per service item)
        $staffRows = $salesCards->flatMap(function (JobCard $card) use ($staffFilter, $categoryFilter) {
            return $card->serviceItems->flatMap(function ($item) use ($card, $staffFilter, $categoryFilter) {
                $assignedStaff = $item->staff;
                if ($assignedStaff->isEmpty() && $card->primaryStaff) {
                    $assignedStaff = collect([$card->primaryStaff]);
                }
                if ($assignedStaff->isEmpty() && $card->staff->isNotEmpty()) {
                    $assignedStaff = $card->staff;
                }
                if ($assignedStaff->isEmpty()) {
                    $assignedStaff = collect([(object)['id' => null, 'name' => 'Unassigned', 'mobile_number' => '—']]);
                }

                $cat = $item->service?->category ?? $item->subcategory ?? 'General';
                if ($categoryFilter !== '' && strtolower($cat) !== strtolower($categoryFilter)) {
                    return [];
                }

                return $assignedStaff->filter(function ($s) use ($staffFilter) {
                    return $staffFilter === '' || (string) ($s->id ?? '') === (string) $staffFilter;
                })->map(function ($s) use ($card, $item, $cat) {
                    return [
                        'staff_name' => $s->name ?? 'Unassigned',
                        'mobile'     => $s->mobile_number ?? '—',
                        'date'       => $card->created_at,
                        'job_card'   => $card->job_card_name,
                        'service'    => $item->service?->service_name ?? $item->subcategory ?? 'Service',
                        'category'   => $cat,
                        'amount'     => (float) $item->amount,
                    ];
                });
            });
        })->sortByDesc('date')->values();

        $purchaseRows = ProductPurchase::query()->with('product')
            ->whereBetween('purchase_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($categoryFilter, function($q) use ($categoryFilter) {
                $q->whereHas('product', fn($pq) => $pq->where('category', $categoryFilter));
            })
            ->orderByDesc('purchase_date')->get();

        $filename = 'report-' . $activeTab . '-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.xls';

        $content = view('report.exports.excel', [
            'activeTab'    => $activeTab,
            'range'        => $range,
            'salesRows'    => $salesRows,
            'staffRows'    => $staffRows,
            'purchaseRows' => $purchaseRows,
        ])->render();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function sales(Request $request)
    {
        return $this->tabRedirect($request, 'sales');
    }

    public function expenses(Request $request)
    {
        return $this->tabRedirect($request, 'expenses');
    }

    public function staffDailyTarget(Request $request)
    {
        return $this->tabRedirect($request, 'staff');
    }

    public function purchases(Request $request)
    {
        return $this->tabRedirect($request, 'purchase');
    }

    private function tabRedirect(Request $request, string $tab)
    {
        return redirect()->route('reports.index', array_filter([
            'tab' => $tab,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]));
    }

    private function date(?string $value, Carbon $fallback): Carbon
    {
        try {
            return $value ? Carbon::createFromFormat('Y-m-d', $value)->startOfDay() : $fallback->copy()->startOfDay();
        } catch (\Throwable) {
            return $fallback->copy()->startOfDay();
        }
    }
}