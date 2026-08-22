<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Staff;
use Illuminate\Http\Request;

class ComplaintsController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Complaint::with('staff')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;

                // Wrapped in a nested closure so the OR conditions stay scoped
                // to the search itself instead of escaping the when() clause.
                $q->where(function ($inner) use ($search) {
                    $inner->where('complaint_type_text', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%")
                        ->orWhereHas('staff', fn ($staff) => $staff->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('complaint_date')
            ->paginate(12)
            ->withQueryString();

        return view('complaints.index', [
            'complaints' => $complaints,
            'staff' => Staff::where('status', 'active')->orderBy('name')->get(),
            'search' => $request->search,

            // Dashboard stat cards
            'totalComplaintsCount' => Complaint::count(),
            'pendingComplaintsCount' => Complaint::where(function ($q) {
                $q->whereNull('action_taken')->orWhere('action_taken', '');
            })->count(),
            'resolvedComplaintsCount' => Complaint::whereNotNull('action_taken')
                ->where('action_taken', '!=', '')
                ->count(),
            'totalCompensationSum' => (float) Complaint::sum('compensation'),
        ]);
    }

    public function store(Request $request)
    {
        Complaint::create($this->rules($request));

        return back()->with('success', 'Complaint added successfully.');
    }

    public function update(Request $request, Complaint $complaint)
    {
        $complaint->update($this->rules($request));

        return back()->with('success', 'Complaint updated successfully.');
    }

    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        return back()->with('success', 'Complaint deleted successfully.');
    }

    private function rules(Request $request): array
    {
        return $request->validate([
            'staff_id' => ['required', 'exists:staff,id'],
            'complaint_type_text' => ['required', 'string', 'max:150'],
            'reason' => ['required', 'string', 'max:2000'],
            'action_taken' => ['required', 'string', 'max:2000'],
            // Compensation stays optional — nullable, no min-value requirement beyond 0.
            'compensation' => ['nullable', 'numeric', 'min:0'],
            'complaint_date' => ['required', 'date'],
        ]);
    }
}