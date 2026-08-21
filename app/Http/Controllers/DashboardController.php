<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\JobCard;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();
        $lastMonthStart = now()->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->copy()->subMonth()->endOfMonth();
        $completedToday = $this->completedJobsBetween($today, now()->endOfDay());
        $completedThisMonth = $this->completedJobsBetween($monthStart, now()->endOfMonth());
        $todayJobs = JobCard::query()->whereBetween('created_at', [$today, now()->endOfDay()])->get();
        $customerMonths = $this->monthlyCounts(Customer::query(), 5);
        $staffMonths = $this->monthlyCounts(Staff::query()->where('status', 'active'), 5);
        $appointmentMonths = $this->monthlyCounts(JobCard::query(), 5);
        $revenueMonths = $this->monthlyRevenue(5);
        $customerThisMonth = Customer::whereBetween('created_at', [$monthStart, now()->endOfMonth()])->count();
        $customerLastMonth = Customer::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $todayRevenue = $this->revenue($completedToday);

        return view('dashboard.index', [
            'staffMembers' => Staff::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'dashboard' => [
                'customerCount' => Customer::count(),
                'activeStaff' => Staff::where('status', 'active')->count(),
                'todayAppointments' => $todayJobs->count(),
                'todayRevenue' => $todayRevenue,
                'inProgressToday' => $todayJobs->where('status', 'in_progress')->count(),
                'customerThisMonth' => $customerThisMonth,
                'customerLastMonth' => $customerLastMonth,
                'customerGrowth' => $this->percentageChange($customerThisMonth, $customerLastMonth),
                'appointmentGrowth' => $this->percentageChange($todayJobs->count(), JobCard::whereDate('created_at', now()->subDay())->count()),
                'revenueGrowth' => $this->percentageChange($todayRevenue, $this->revenue($this->completedJobsBetween(now()->subDay()->startOfDay(), now()->subDay()->endOfDay()))),
                'kpiMonths' => [
                    'labels' => $customerMonths['labels'],
                    'customers' => $customerMonths['values'],
                    'staff' => $staffMonths['values'],
                    'appointments' => $appointmentMonths['values'],
                    'revenue' => $revenueMonths['values'],
                ],
                'staffRevenueSeries' => [
                    '7' => $this->staffRevenueSeries(7, 'day'),
                    '30' => $this->staffRevenueSeries(30, 'day'),
                    '12' => $this->staffRevenueSeries(12, 'month'),
                ],
                'revenueSeries' => [
                    '7' => $this->revenueSeries(7, 'day'),
                    '30' => $this->revenueSeries(30, 'day'),
                    '12' => $this->revenueSeries(12, 'month'),
                ],
                'categoryRevenue' => $this->categoryRevenue($completedThisMonth),
                'weeklyCustomers' => $this->weeklyCustomerCounts(),
                'heatmap' => $this->appointmentHeatmap(),
                'todayStatuses' => [
                    'pending' => $todayJobs->where('status', 'pending')->count(),
                    'in_progress' => $todayJobs->where('status', 'in_progress')->count(),
                    'completed' => $todayJobs->where('status', 'completed')->count(),
                    'cancelled' => $todayJobs->where('status', 'cancelled')->count(),
                ],
            ],
        ]);
    }

    public function staffPerformance(Request $request)
    {
        $validated = $request->validate([
            'period' => ['nullable', 'in:today,7,30,this_month,custom'],
            'staff_id' => ['nullable', 'integer', 'exists:staff,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $period = $validated['period'] ?? '7';
        [$from, $to] = $this->staffPerformanceRange(
            $period,
            $validated['start_date'] ?? null,
            $validated['end_date'] ?? null,
        );

        $staff = Staff::query()
            ->where('status', 'active')
            ->when($validated['staff_id'] ?? null, fn ($query, $staffId) => $query->whereKey($staffId))
            ->orderBy('name')
            ->get(['id', 'name']);

        $jobs = JobCard::query()
            ->with(['serviceItems.service', 'serviceItems.staff'])
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $performance = $staff->map(function (Staff $member) use ($jobs) {
            $assigned = $jobs->filter(function (JobCard $jobCard) use ($member) {
                return $jobCard->staff_id == $member->id
                    || $jobCard->serviceItems->contains(fn ($item) => $item->staff->contains('id', $member->id));
            });

            $total = $assigned->count();
            $completed = $assigned->where('status', 'completed');
            $completedCount = $completed->count();
            $completionRate = $total ? round(($completedCount / $total) * 100, 1) : 0;

            $revenue = $completed->sum(function (JobCard $jobCard) use ($member) {
                $memberItems = $jobCard->serviceItems->filter(function ($item) use ($member, $jobCard) {
                    return $item->staff->contains('id', $member->id) || ($item->staff->isEmpty() && $jobCard->staff_id == $member->id);
                });
                if ($memberItems->isNotEmpty()) {
                    return (float) $memberItems->sum('amount');
                }
                if ($jobCard->staff_id == $member->id) {
                    return (float) $jobCard->getTotalAmount();
                }
                return 0.0;
            });

            return [
                'id' => $member->id,
                'name' => $member->name,
                'total_appointments' => $total,
                'completed_appointments' => $completedCount,
                'cancelled_appointments' => $assigned->where('status', 'cancelled')->count(),
                'pending_appointments' => $assigned->whereIn('status', ['pending', 'in_progress'])->count(),
                'services_completed' => $completedCount,
                'revenue_generated' => round($revenue, 2),
                'completion_rate' => $completionRate,
                'overall_performance' => $completionRate,
            ];
        })->values();

        return response()->json([
            'data' => $performance,
            'range' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'average_performance' => round((float) $performance->avg('overall_performance'), 1),
        ]);
    }

    private function completedJobsBetween(Carbon $from, Carbon $to): Collection
    {
        return JobCard::query()
            ->with(['serviceItems.service', 'serviceItems.staff'])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$from, $to])
            ->get();
    }

    private function staffPerformanceRange(string $period, ?string $startDate, ?string $endDate): array
    {
        if ($period === 'today') {
            return [now()->startOfDay(), now()->endOfDay()];
        }

        if ($period === '30') {
            return [now()->copy()->subDays(29)->startOfDay(), now()->endOfDay()];
        }

        if ($period === 'this_month') {
            return [now()->startOfMonth(), now()->endOfMonth()];
        }

        if ($period === 'custom' && $startDate && $endDate) {
            return [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()];
        }

        return [now()->copy()->subDays(6)->startOfDay(), now()->endOfDay()];
    }

    private function revenue(Collection $jobCards): float
    {
        return (float) $jobCards->sum(fn (JobCard $jobCard) => $jobCard->getTotalAmount());
    }

    private function percentageChange(float|int $current, float|int $previous): float
    {
        return $previous == 0 ? ($current > 0 ? 100 : 0) : round((($current - $previous) / $previous) * 100, 1);
    }

    private function monthlyCounts($query, int $months): array
    {
        $labels = [];
        $values = [];
        for ($offset = $months - 1; $offset >= 0; $offset--) {
            $month = now()->copy()->subMonths($offset);
            $labels[] = $month->format('M');
            $values[] = (clone $query)->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->count();
        }
        return compact('labels', 'values');
    }

    private function monthlyRevenue(int $months): array
    {
        $labels = [];
        $values = [];
        for ($offset = $months - 1; $offset >= 0; $offset--) {
            $month = now()->copy()->subMonths($offset);
            $labels[] = $month->format('M');
            $values[] = $this->revenue($this->completedJobsBetween($month->copy()->startOfMonth(), $month->copy()->endOfMonth()));
        }
        return compact('labels', 'values');
    }

    private function revenueSeries(int $period, string $unit): array
    {
        $labels = [];
        $values = [];

        for ($offset = $period - 1; $offset >= 0; $offset--) {
            $date = $unit === 'month'
                ? now()->copy()->subMonths($offset)
                : now()->copy()->subDays($offset);

            $labels[] = $unit === 'month' ? $date->format('M') : $date->format('D');
            $values[] = $this->revenue($this->completedJobsBetween(
                $unit === 'month' ? $date->copy()->startOfMonth() : $date->copy()->startOfDay(),
                $unit === 'month' ? $date->copy()->endOfMonth() : $date->copy()->endOfDay(),
            ));
        }

        return compact('labels', 'values');
    }

    private function staffRevenueSeries(int $period, string $unit): array
    {
        $from = $unit === 'month'
            ? now()->copy()->subMonths($period - 1)->startOfMonth()
            : now()->copy()->subDays($period - 1)->startOfDay();
        $staff = Staff::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $jobs = JobCard::with(['serviceItems.service', 'serviceItems.staff'])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$from, now()])
            ->get();

        $staffRevenue = $staff->map(function (Staff $member) use ($jobs) {
            $amount = $jobs->sum(function (JobCard $jobCard) use ($member) {
                $memberItems = $jobCard->serviceItems->filter(function ($item) use ($member, $jobCard) {
                    return $item->staff->contains('id', $member->id) || ($item->staff->isEmpty() && $jobCard->staff_id == $member->id);
                });
                if ($memberItems->isNotEmpty()) {
                    return (float) $memberItems->sum('amount');
                }
                if ($jobCard->staff_id == $member->id) {
                    return (float) $jobCard->getTotalAmount();
                }
                return 0.0;
            });
            return round($amount, 2);
        });

        return [
            'labels' => $staff->pluck('name')->values()->all(),
            'values' => $staffRevenue->values()->all(),
        ];
    }

    private function categoryRevenue(Collection $jobs): Collection
    {
        return $jobs->flatMap(fn (JobCard $jobCard) => $jobCard->serviceItems)
            ->groupBy(fn ($item) => $item->service?->category ?: 'Other')
            ->map(fn (Collection $items) => (float) $items->sum('amount'))
            ->sortDesc()
            ->take(4)
            ->map(fn (float $amount, string $name) => ['name' => $name, 'amount' => $amount])
            ->values();
    }

    private function weeklyCustomerCounts(): array
    {
        $monthStart = now()->copy()->startOfMonth();
        $firstWeekStart = $monthStart->copy()->startOfWeek();
        $now = now();

        return collect(range(0, 3))->map(function (int $offset) use ($monthStart, $firstWeekStart, $now) {
            $weekStart = $firstWeekStart->copy()->addWeeks($offset);
            $from = $weekStart->lt($monthStart) ? $monthStart->copy() : $weekStart;
            $weekEnd = $weekStart->copy()->endOfWeek();
            $to = $weekEnd->gt($now) ? $now->copy() : $weekEnd;

            return [
                'label' => 'Week '.($offset + 1),
                'value' => $from->gt($to) ? 0 : Customer::whereBetween('created_at', [$from, $to])->count(),
            ];
        })->all();
    }

    private function appointmentHeatmap(): array
    {
        $jobs = JobCard::query()->where('created_at', '>=', now()->copy()->subWeeks(7)->startOfWeek())->get();
        return collect(range(6, 0))->map(function (int $weeksAgo) use ($jobs) {
            $week = now()->copy()->subWeeks($weeksAgo)->startOfWeek();
            return collect(range(0, 6))->map(function (int $day) use ($week, $jobs) {
                $date = $week->copy()->addDays($day);
                $count = $jobs->filter(fn (JobCard $jobCard) => $jobCard->created_at->isSameDay($date))->count();
                return ['count' => $count, 'level' => min(4, $count === 0 ? 0 : (int) ceil($count / 2)), 'label' => $date->format('D · j M')];
            })->all();
        })->all();
    }
}
