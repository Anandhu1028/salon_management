<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\JobCard;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Http\Request;

class ComplaintsController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $dateFrom = trim($request->input('date_from', ''));
        $dateTo = trim($request->input('date_to', ''));
        $status = trim($request->input('status', ''));
        $staffId = trim($request->input('staff_id', ''));
        $serviceId = trim($request->input('service_id', ''));

        $complaints = Complaint::with([
            'jobCard.customer',
            'jobCard.serviceItems.service',
            'jobCard.serviceItems.staff',
            'staff',
            'service',
        ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('reason', 'like', "%{$search}%")
                        ->orWhere('action_taken', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('subcategory', 'like', "%{$search}%")
                        ->orWhere('complaint_type_text', 'like', "%{$search}%")
                        ->orWhereHas('staff', fn ($staff) => $staff->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('service', fn ($srv) => $srv->where('service_name', 'like', "%{$search}%"))
                        ->orWhereHas('jobCard', function ($jc) use ($search) {
                            $jc->where('job_card_number', 'like', "%{$search}%")
                                ->orWhere('job_card_name', 'like', "%{$search}%")
                                ->orWhereHas('customer', fn ($cust) => $cust->where('name', 'like', "%{$search}%"));
                        });
                });
            })
            ->when($dateFrom !== '', fn ($q) => $q->whereDate('complaint_date', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($q) => $q->whereDate('complaint_date', '<=', $dateTo))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($staffId !== '', fn ($q) => $q->where('staff_id', $staffId))
            ->when($serviceId !== '', fn ($q) => $q->where('service_id', $serviceId))
            ->latest('complaint_date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $jobCards = JobCard::with([
            'customer',
            'customers',
            'service',
            'staff',
            'serviceItems.service',
            'serviceItems.staff',
        ])
            ->latest('id')
            ->get();

        return view('complaints.index', [
            'complaints' => $complaints,
            'jobCards' => $jobCards,
            'staff' => Staff::where('status', 'active')->orderBy('name')->get(),
            'services' => Service::where('status', 'active')->orderBy('service_name')->get(),
            'search' => $search,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'statusFilter' => $status,
            'staffId' => $staffId,
            'serviceId' => $serviceId,

            // Dashboard stat cards
            'totalComplaintsCount' => Complaint::count(),
            'pendingComplaintsCount' => Complaint::where(function ($q) {
                $q->whereNull('action_taken')
                    ->orWhere('action_taken', '')
                    ->where('status', '!=', 'Resolved');
            })->count(),
            'resolvedComplaintsCount' => Complaint::where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('action_taken')->where('action_taken', '!=', '');
                })->orWhere('status', 'Resolved');
            })->count(),
            'totalCompensationSum' => (float) Complaint::sum('compensation'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->rules($request);

        if (empty($data['status'])) {
            $data['status'] = !empty($data['action_taken']) ? 'Resolved' : 'Pending';
        }

        Complaint::create($data);

        return back()->with('success', 'Complaint added successfully.');
    }

    public function update(Request $request, Complaint $complaint)
    {
        $data = $this->rules($request);

        if (empty($data['status'])) {
            $data['status'] = !empty($data['action_taken']) ? 'Resolved' : 'Pending';
        }

        $complaint->update($data);

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
            'job_card_id' => ['required', 'exists:job_cards,id'],
            'staff_id' => ['required', 'exists:staff,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'category' => ['nullable', 'string', 'max:150'],
            'subcategory' => ['nullable', 'string', 'max:150'],
            'reason' => ['required', 'string', 'max:3000'],
            'action_taken' => ['nullable', 'string', 'max:3000'],
            'compensation' => ['nullable', 'numeric', 'min:0'],
            'complaint_date' => ['required', 'date'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
