<?php

namespace App\Http\Controllers;

use App\Models\JobCard;
use App\Models\ProductPurchase;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

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
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->with(['serviceItems.service', 'primaryStaff', 'customer', 'paymentType'])
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

        $staffPerformance = $salesCards->filter(fn (JobCard $card) => $card->primaryStaff)
            ->groupBy(fn (JobCard $card) => $card->primaryStaff->name)
            ->map(fn ($cards, $name) => ['name' => $name, 'achieved' => $cards->sum($cardTotal)])
            ->sortByDesc('achieved')->values();

        $salesSearch = trim($request->input('search', ''));
        $salesRows = $salesCards->filter(function (JobCard $card) use ($salesSearch) {
            if ($salesSearch === '') {
                return true;
            }

            return str_contains(strtolower($card->job_card_name), strtolower($salesSearch))
                || str_contains(strtolower($card->customer?->name ?? ''), strtolower($salesSearch));
        })->values();

        $paymentBreakdown = $salesCards->groupBy(fn (JobCard $card) => $card->paymentType?->name ?? 'Not recorded')
            ->map(fn ($cards, $name) => ['name' => $name, 'amount' => $cards->sum($cardTotal), 'count' => $cards->count()])
            ->sortByDesc('amount')->values();

        return view('report.index', [
            'activeTab' => $activeTab, 'startDate' => $startDate, 'endDate' => $endDate,
            'totalSales' => $totalSales, 'totalExpenses' => 0.0, 'staffDailyTarget' => 0.0,
            'staffAchieved' => $staffPerformance->sum('achieved'), 'totalPurchase' => $purchaseRows->sum->total_amount,
            'totalQuantity' => $purchaseRows->sum('quantity'), 'purchaseRows' => $purchaseRows,
            'salesCards' => $salesCards, 'dailySales' => $dailySales, 'topServices' => $topServices,
            'staffPerformance' => $staffPerformance, 'salesRows' => $salesRows,
            'salesSearch' => $salesSearch, 'paymentBreakdown' => $paymentBreakdown, 'cardTotal' => $cardTotal,
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
