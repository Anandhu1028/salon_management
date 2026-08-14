<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\JobCard;
use App\Models\Staff;
use Carbon\Carbon;
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

    private function completedJobsBetween(Carbon $from, Carbon $to): Collection
    {
        return JobCard::query()->with('service')->where('status', 'completed')->whereBetween('created_at', [$from, $to])->get();
    }

    private function revenue(Collection $jobCards): float
    {
        return (float) $jobCards->sum(fn (JobCard $jobCard) => (float) ($jobCard->service?->price ?? 0));
    }

    private function percentageChange(float|int $current, float|int $previous): float
    {
        return $previous == 0 ? ($current > 0 ? 100 : 0) : round((($current - $previous) / $previous) * 100, 1);
    }

    private function monthlyCounts($query, int $months): array
    {
        $labels = []; $values = [];
        for ($offset = $months - 1; $offset >= 0; $offset--) {
            $month = now()->copy()->subMonths($offset);
            $labels[] = $month->format('M');
            $values[] = (clone $query)->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->count();
        }
        return compact('labels', 'values');
    }

    private function monthlyRevenue(int $months): array
    {
        $labels = []; $values = [];
        for ($offset = $months - 1; $offset >= 0; $offset--) {
            $month = now()->copy()->subMonths($offset);
            $labels[] = $month->format('M');
            $values[] = $this->revenue($this->completedJobsBetween($month->copy()->startOfMonth(), $month->copy()->endOfMonth()));
        }
        return compact('labels', 'values');
    }

    private function revenueSeries(int $period, string $unit): array
    {
        $labels = []; $values = [];
        for ($offset = $period - 1; $offset >= 0; $offset--) {
            $date = $unit === 'month' ? now()->copy()->subMonths($offset) : now()->copy()->subDays($offset);
            $labels[] = $unit === 'month' ? $date->format('M') : $date->format('D');
            $values[] = $this->revenue($this->completedJobsBetween($unit === 'month' ? $date->copy()->startOfMonth() : $date->copy()->startOfDay(), $unit === 'month' ? $date->copy()->endOfMonth() : $date->copy()->endOfDay()));
        }
        return compact('labels', 'values');
    }

    private function categoryRevenue(Collection $jobs): Collection
    {
        return $jobs->groupBy(fn (JobCard $jobCard) => $jobCard->service?->category ?: 'Other')
            ->map(fn (Collection $items) => $this->revenue($items))->sortDesc()->take(4)
            ->map(fn (float $amount, string $name) => ['name' => $name, 'amount' => $amount])->values();
    }

    private function weeklyCustomerCounts(): array
    {
        return collect(range(0, 3))->map(function (int $offset) {
            $week = now()->copy()->startOfMonth()->addWeeks($offset);
            return ['label' => 'Week '.($offset + 1), 'value' => Customer::whereBetween('created_at', [$week, $week->copy()->endOfWeek()])->count()];
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
