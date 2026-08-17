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

        $filterCustomers = $customers;
        $filterServices = $services;
        $filterSubcategories = Service::whereNotNull('subcategory')
            ->distinct()
            ->pluck('subcategory')
            ->filter()
            ->values();

        return view('job-cards.index', compact(
            'jobCards',
            'customers',
            'services',
            'staff',
            'filterCustomers',
            'filterServices',
            'filterSubcategories',
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
        $jobCard = trim($request->input('job_card', ''));
        $customerId = $request->input('customer_id');
        $serviceId = $request->input('service_id');
        $subcategory = trim($request->input('subcategory', ''));
        $amountRange = $request->input('amount_range');

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
            ->when($jobCard !== '', function ($query) use ($jobCard) {
                $cleanId = preg_replace('/[^0-9]/', '', $jobCard);
                $query->where(function ($q) use ($jobCard, $cleanId) {
                    $q->where('job_card_name', 'like', "%{$jobCard}%");
                    if ($cleanId !== '') {
                        $q->orWhere('id', (int) $cleanId);
                    }
                });
            })
            ->when(!empty($customerId), function ($query) use ($customerId) {
                $query->where(function ($q) use ($customerId) {
                    $q->where('customer_id', $customerId)
                      ->orWhereHas('customers', fn ($c) => $c->where('customers.id', $customerId));
                });
            })
            ->when(!empty($serviceId), function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })
            ->when($subcategory !== '', function ($query) use ($subcategory) {
                $query->where('subcategory', $subcategory);
            })
            ->when(!empty($amountRange) && $amountRange !== 'all', function ($query) use ($amountRange) {
                $query->whereHas('service', function ($serviceQuery) use ($amountRange) {
                    match ($amountRange) {
                        'under_500' => $serviceQuery->where('price', '<', 500),
                        '500_1000' => $serviceQuery->whereBetween('price', [500, 1000]),
                        '1001_2500' => $serviceQuery->whereBetween('price', [1001, 2500]),
                        '2501_5000' => $serviceQuery->whereBetween('price', [2501, 5000]),
                        'above_5000' => $serviceQuery->where('price', '>', 5000),
                        default => null,
                    };
                });
            })
            ->when(in_array($filter, ['pending', 'in_progress', 'completed', 'cancelled'], true), function ($query) use ($filter) {
                $query->where('status', $filter);
            })
            ->latest();
    }
}
