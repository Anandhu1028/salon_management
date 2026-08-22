<?php

namespace App\Http\Controllers;

use App\Models\MarketingActivity;
use App\Models\Staff;
use Illuminate\Http\Request;

class MarketingActivityController extends Controller
{
    /**
     * Suggested marketing type values — used ONLY to populate a <datalist>
     * for autocomplete convenience. Marketing Type is a free-text field;
     * these are NOT enforced by validation.
     */
    public static function marketingTypeSuggestions(): array
    {
        return [
            'Google Review',
            'Instagram Post',
            'Instagram Reel',
            'Instagram Story',
            'Facebook Post',
            'Facebook Reel',
            'Facebook Story',
            'WhatsApp Promotion',
            'Promotional Video',
            'Influencer Collaboration',
        ];
    }

    /**
     * Display marketing activities.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $activityDate = trim((string) $request->input('activity_date', ''));
        $dateFrom = trim((string) $request->input('date_from', ''));
        $dateTo = trim((string) $request->input('date_to', ''));
        $marketingType = trim((string) $request->input('marketing_type', ''));
        $location = trim((string) $request->input('location', ''));
        $staffId = trim((string) $request->input('staff_id', ''));

        $query = MarketingActivity::with('staff');

        /*
        |--------------------------------------------------------------------------
        | Search — location, marketing_type, notes, staff name
        |--------------------------------------------------------------------------
        */
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('location', 'like', "%{$search}%")
                    ->orWhere('marketing_type', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('staff', function ($staffQuery) use ($search) {
                        $staffQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Date filter
        |--------------------------------------------------------------------------
        */
        if ($activityDate !== '') {
            $query->whereDate('activity_date', $activityDate);
        }
        if ($dateFrom !== '') {
            $query->whereDate('activity_date', '>=', $dateFrom);
        }
        if ($dateTo !== '') {
            $query->whereDate('activity_date', '<=', $dateTo);
        }

        /*
        |--------------------------------------------------------------------------
        | Marketing type filter — PARTIAL match, since type is free text now
        |--------------------------------------------------------------------------
        */
        if ($marketingType !== '') {
            $query->where('marketing_type', 'like', "%{$marketingType}%");
        }

        /*
        |--------------------------------------------------------------------------
        | Location filter
        |--------------------------------------------------------------------------
        */
        if ($location !== '') {
            $query->where('location', 'like', "%{$location}%");
        }

        /*
        |--------------------------------------------------------------------------
        | Staff filter
        |--------------------------------------------------------------------------
        */
        if ($staffId !== '') {
            $query->where('staff_id', $staffId);
        }

        /*
        |--------------------------------------------------------------------------
        | Paginated list
        |--------------------------------------------------------------------------
        */
        $marketingActivities = $query
            ->orderByDesc('activity_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Active staff — for Staff <select> and staff filter
        |--------------------------------------------------------------------------
        */
        $staff = Staff::where('status', 'active')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Marketing type suggestions — for the <datalist> only
        |--------------------------------------------------------------------------
        */
        $marketingTypes = self::marketingTypeSuggestions();

        /*
        |--------------------------------------------------------------------------
        | Stats
        |--------------------------------------------------------------------------
        */
        $today = now()->toDateString();

        $todayQuery = MarketingActivity::whereDate('activity_date', $today);

        $totalActivities = MarketingActivity::count();
        $todayActivities = (clone $todayQuery)->count();
        $todayCount = (int) (clone $todayQuery)->sum('count');

        $googleReviewsToday = (int) (clone $todayQuery)
            ->where('marketing_type', 'like', '%Google Review%')
            ->sum('count');

        $instagramToday = (int) (clone $todayQuery)
            ->where('marketing_type', 'like', '%Instagram%')
            ->sum('count');

        return view('marketing.index', compact(
            'marketingActivities',
            'staff',
            'marketingTypes',
            'search',
            'activityDate',
            'dateFrom',
            'dateTo',
            'marketingType',
            'location',
            'staffId',
            'totalActivities',
            'todayActivities',
            'todayCount',
            'googleReviewsToday',
            'instagramToday'
        ));
    }

    /**
     * Store marketing activity.
     */
    public function store(Request $request)
    {
        $validated = $this->validated($request);

        MarketingActivity::create($validated);

        return redirect()
            ->route('marketing.index')
            ->with('success', 'Marketing activity added successfully.');
    }

    /**
     * Update marketing activity.
     */
    public function update(Request $request, MarketingActivity $marketing)
    {
        $validated = $this->validated($request);

        $marketing->update($validated);

        return redirect()
            ->route('marketing.index')
            ->with('success', 'Marketing activity updated successfully.');
    }

    /**
     * Show marketing activity (AJAX — used by the View modal, expects JSON).
     */
    public function show(MarketingActivity $marketing)
    {
        return response()->json($marketing->load('staff'));
    }

    /**
     * Delete marketing activity.
     */
    public function destroy(MarketingActivity $marketing)
    {
        $marketing->delete();

        return redirect()
            ->route('marketing.index')
            ->with('success', 'Marketing activity deleted successfully.');
    }

    /**
     * Shared validation rules for store() and update().
     * NOTE: activity_time removed. marketing_type is free text (no 'in:' list).
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'activity_date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:100'],
            'marketing_type' => ['required', 'string', 'max:60'],
            'count' => ['required', 'integer', 'min:1'],
            'staff_id' => ['nullable', 'exists:staff,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
