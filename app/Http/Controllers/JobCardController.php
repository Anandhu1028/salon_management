<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ExportsManagementList;
use App\Models\Customer;
use App\Models\JobCard;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobCardController extends Controller
{
    use ExportsManagementList;

    /**
     * Display job cards.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $filter = trim($request->input('filter', ''));

        $jobCards = $this->filteredQuery($request)
            ->paginate(9)
            ->withQueryString();

        $customers = Customer::orderBy('name')
            ->get();

        $services = Service::where('status', 'active')
            ->whereNotNull('subcategory')
            ->orderBy('service_name')
            ->get();

        $staff = Staff::where('status', 'active')->orderBy('name')->get();

        return view('job-cards.index', compact(
            'jobCards',
            'customers',
            'services',
            'staff',
            'search',
            'filter'
        ));
    }

    public function exportExcel(Request $request)
    {
        $headers = ['Job Card', 'Customers', 'Staff Assigned', 'Service', 'Sub Category', 'Status', 'Created'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn(JobCard $jobCard) => [
                $jobCard->job_card_name,
                $jobCard->customers->isNotEmpty()
                ? $jobCard->customers->pluck('name')->join(', ')
                : ($jobCard->customer->name ?? '—'),
                $jobCard->staff->isNotEmpty()
                ? $jobCard->staff->pluck('name')->join(', ')
                : '—',
                $jobCard->service->service_name ?? '—',
                $jobCard->subcategory ?: '—',
                ucfirst(str_replace('_', ' ', $jobCard->status)),
                $jobCard->created_at?->format('d M Y') ?? '—',
            ]
        );

        return $this->exportCsvResponse($headers, $rows, 'job-cards-list');
    }

    public function exportPdf(Request $request)
    {
        $headers = ['Job Card', 'Customers', 'Staff Assigned', 'Service', 'Sub Category', 'Status', 'Created'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn(JobCard $jobCard) => [
                $jobCard->job_card_name,
                $jobCard->customers->isNotEmpty()
                ? $jobCard->customers->pluck('name')->join(', ')
                : ($jobCard->customer->name ?? '—'),
                $jobCard->staff->isNotEmpty()
                ? $jobCard->staff->pluck('name')->join(', ')
                : '—',
                $jobCard->service->service_name ?? '—',
                $jobCard->subcategory ?: '—',
                ucfirst(str_replace('_', ' ', $jobCard->status)),
                $jobCard->created_at?->format('d M Y') ?? '—',
            ]
        );

        return $this->exportPdfResponse(
            'Job Cards List',
            $headers,
            $rows,
            'job-cards-list',
            'Job card records export'
        );
    }

    /**
     * Store job card.
     */
    public function store(Request $request)
    {
        if ($request->has('customer_id') && !$request->has('customer_ids')) {
            $customerVal = $request->input('customer_id');
            $request->merge(['customer_ids' => is_array($customerVal) ? $customerVal : array_filter([$customerVal])]);
        }

        if ($request->has('staff_id') && !$request->has('staff_ids')) {
            $staffVal = $request->input('staff_id');
            $request->merge(['staff_ids' => is_array($staffVal) ? $staffVal : array_filter([$staffVal])]);
        }

        $validated = $request->validate([
            'job_card_name' => [
                'required',
                'string',
                'max:150',
            ],

            'customer_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'customer_ids.*' => [
                'exists:customers,id',
            ],

            'service_id' => [
                'required',
                'exists:services,id',
            ],

            'staff_ids' => ['nullable', 'array'],
            'staff_ids.*' => ['exists:staff,id'],

            'subcategory' => [
                'required',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'pending',
                    'in_progress',
                    'completed',
                    'cancelled',
                ]),
            ],
        ]);

        $service = Service::where('id', $validated['service_id'])
            ->where('status', 'active')
            ->firstOrFail();

        if (
            !$service->subcategory ||
            $service->subcategory !== $validated['subcategory']
        ) {
            return back()
                ->withErrors([
                    'subcategory' =>
                        'Selected subcategory does not belong to the selected service.',
                ])
                ->withInput();
        }

        $customerIds = $validated['customer_ids'];
        $staffIds = $validated['staff_ids'] ?? [];

        $jobCard = JobCard::create([
            'job_card_name' => $validated['job_card_name'],
            'customer_id' => $customerIds[0] ?? null,
            'staff_id' => $staffIds[0] ?? null,
            'service_id' => $validated['service_id'],
            'subcategory' => $validated['subcategory'],
            'status' => $validated['status'] ?? 'pending',
        ]);

        $jobCard->customers()->sync($customerIds);
        $jobCard->staff()->sync($staffIds);

        return redirect()
            ->route('job-cards.index')
            ->with(
                'success',
                'Job card created successfully.'
            );
    }

    /**
     * Update job card.
     */
    public function update(Request $request, JobCard $jobCard)
    {
        if ($request->has('customer_id') && !$request->has('customer_ids')) {
            $customerVal = $request->input('customer_id');
            $request->merge(['customer_ids' => is_array($customerVal) ? $customerVal : array_filter([$customerVal])]);
        }

        if ($request->has('staff_id') && !$request->has('staff_ids')) {
            $staffVal = $request->input('staff_id');
            $request->merge(['staff_ids' => is_array($staffVal) ? $staffVal : array_filter([$staffVal])]);
        }

        $validated = $request->validate([
            'job_card_name' => [
                'required',
                'string',
                'max:150',
            ],

            'customer_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'customer_ids.*' => [
                'exists:customers,id',
            ],

            'service_id' => [
                'required',
                'exists:services,id',
            ],

            'staff_ids' => ['nullable', 'array'],
            'staff_ids.*' => ['exists:staff,id'],

            'subcategory' => [
                'required',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'pending',
                    'in_progress',
                    'completed',
                    'cancelled',
                ]),
            ],
        ]);

        $service = Service::where('id', $validated['service_id'])
            ->where('status', 'active')
            ->firstOrFail();

        if (
            !$service->subcategory ||
            $service->subcategory !== $validated['subcategory']
        ) {
            return back()
                ->withErrors([
                    'subcategory' =>
                        'Selected subcategory does not belong to the selected service.',
                ])
                ->withInput();
        }

        $customerIds = $validated['customer_ids'];
        $staffIds = $validated['staff_ids'] ?? [];

        $jobCard->update([
            'job_card_name' => $validated['job_card_name'],
            'customer_id' => $customerIds[0] ?? null,
            'staff_id' => $staffIds[0] ?? null,
            'service_id' => $validated['service_id'],
            'subcategory' => $validated['subcategory'],
            'status' => $validated['status'] ?? $jobCard->status ?? 'pending',
        ]);

        $jobCard->customers()->sync($customerIds);
        $jobCard->staff()->sync($staffIds);

        return redirect()
            ->route('job-cards.index')
            ->with(
                'success',
                'Job card updated successfully.'
            );
    }

    /**
     * Delete job card.
     */
    public function destroy(JobCard $jobCard)
    {
        $jobCard->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job card deleted successfully.',
        ]);
    }

    private function filteredQuery(Request $request)
    {
        $search = trim($request->input('search', ''));
        $filter = trim($request->input('filter', ''));

        return JobCard::with([
            'customer',
            'customers',
            'service',
            'staff',
        ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where(
                        'job_card_name',
                        'like',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'subcategory',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas('customers', function ($customer) use ($search) {
                            $customer->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )->orWhere(
                                    'mobile_number',
                                    'like',
                                    "%{$search}%"
                                );
                        })
                        ->orWhereHas('staff', function ($staff) use ($search) {
                            $staff->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        })
                        ->orWhereHas('service', function ($service) use ($search) {
                            $service->where(
                                'service_name',
                                'like',
                                "%{$search}%"
                            );
                        });
                });
            })
            ->when(in_array($filter, ['pending', 'in_progress', 'completed', 'cancelled'], true), function ($query) use ($filter) {
                $query->where('status', $filter);
            })
            ->latest();
    }
}
