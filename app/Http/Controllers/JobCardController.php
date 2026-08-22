<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ExportsManagementList;
use App\Models\Customer;
use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\PaymentType;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $paymentTypes = PaymentType::where('is_active', true)->orderBy('name')->get();

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
            'paymentTypes',
            'filterCustomers',
            'filterServices',
            'filterSubcategories',
            'search',
            'filter'
        ));
    }

    public function exportExcel(Request $request)
    {
        $headers = ['Job Card', 'Customers', 'Service', 'Subcategory', 'Staff Assigned', 'Amount', 'Payment Type', 'Subtotal', 'Discount', 'Total Amount', 'Status', 'Created'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn(JobCard $jobCard) => [
                $jobCard->job_card_name,
                $jobCard->customers->isNotEmpty()
                ? $jobCard->customers->pluck('name')->join(', ')
                : ($jobCard->customer->name ?? '—'),
                $jobCard->serviceItems->map(fn($item) => $item->service?->service_name)->join(', ') ?: '—',
                $jobCard->serviceItems->map(fn($item) => $item->subcategory)->join(', ') ?: '—',
                $jobCard->serviceItems->flatMap(fn($item) => $item->staff->pluck('name'))->unique()->join(', ') ?: '—',
                $jobCard->serviceItems->map(fn($item) => '₹' . number_format($item->amount, 2))->join(', ') ?: '—',
                // Payment type is now a single value shared by every service item on the job card
                $jobCard->serviceItems->map(fn($item) => $item->paymentType?->name)->filter()->unique()->join(', ') ?: '—',
                '₹' . number_format($jobCard->getSubtotalAmount(), 2),
                '₹' . number_format($jobCard->getDiscountAmount(), 2),
                '₹' . number_format($jobCard->getFinalAmount(), 2),
                ucfirst(str_replace('_', ' ', $jobCard->status)),
                $jobCard->created_at?->format('d M Y') ?? '—',
            ]
        );

        return $this->exportCsvResponse($headers, $rows, 'job-cards-list');
    }

    public function exportPdf(Request $request)
    {
        $headers = ['Job Card', 'Customers', 'Service', 'Subcategory', 'Staff Assigned', 'Amount', 'Payment Type', 'Subtotal', 'Discount', 'Total Amount', 'Status', 'Created'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn(JobCard $jobCard) => [
                $jobCard->job_card_name,
                $jobCard->customers->isNotEmpty()
                ? $jobCard->customers->pluck('name')->join(', ')
                : ($jobCard->customer->name ?? '—'),
                $jobCard->serviceItems->map(fn($item) => $item->service?->service_name)->join(', ') ?: '—',
                $jobCard->serviceItems->map(fn($item) => $item->subcategory)->join(', ') ?: '—',
                $jobCard->serviceItems->flatMap(fn($item) => $item->staff->pluck('name'))->unique()->join(', ') ?: '—',
                $jobCard->serviceItems->map(fn($item) => '₹' . number_format($item->amount, 2))->join(', ') ?: '—',
                // Payment type is now a single value shared by every service item on the job card
                $jobCard->serviceItems->map(fn($item) => $item->paymentType?->name)->filter()->unique()->join(', ') ?: '—',
                '₹' . number_format($jobCard->getSubtotalAmount(), 2),
                '₹' . number_format($jobCard->getDiscountAmount(), 2),
                '₹' . number_format($jobCard->getFinalAmount(), 2),
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
     * Store job card with service items.
     *
     * NOTE: Payment method is now a single, job-card-level selection
     * (`payment_type_id`) made once in the UI — it is applied to every
     * service item created for this job card, rather than being chosen
     * per service item.
     */
    public function store(Request $request)
    {
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
            'service_items' => [
                'required',
                'array',
                'min:1',
            ],
            'service_items.*.service_id' => [
                'required',
                'exists:services,id',
            ],
            'service_items.*.subcategory' => [
                'required',
                'string',
                'max:100',
            ],
            'service_items.*.staff_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'service_items.*.staff_ids.*' => [
                'exists:staff,id',
            ],
            'service_items.*.amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            // Single payment method for the whole job card
            'payment_type_id' => [
                'required',
                'exists:payment_types,id',
            ],
            'discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'status' => [
                'nullable',
                Rule::in(['pending', 'in_progress', 'completed', 'cancelled']),
            ],
        ]);

        $subtotal = collect($validated['service_items'])->sum('amount');
        $discount = $validated['discount_amount'] ?? 0;

        if ($discount > $subtotal) {
            return back()
                ->withErrors(['discount_amount' => 'Discount cannot be greater than the subtotal amount.'])
                ->withInput();
        }

        return DB::transaction(function () use ($validated, $discount, $subtotal) {
            $number = 'JC-' . str_pad((string) ((int) JobCard::lockForUpdate()->max('id') + 1), 3, '0', STR_PAD_LEFT);
            $paymentMethod = PaymentType::find($validated['payment_type_id'])?->name;
            // Create the job card
            $jobCard = JobCard::create([
                'job_card_number' => $number,
                'job_card_name' => $validated['job_card_name'],
                'customer_id' => $validated['customer_ids'][0],
                'status' => $validated['status'] ?? 'pending',
                'discount_amount' => $discount,
                'subtotal' => $subtotal,
                'total' => max(0, $subtotal - $discount),
                'payment_method' => $paymentMethod,
                'job_card_date' => now()->toDateString(),
            ]);

            // Sync customers
            $jobCard->customers()->sync($validated['customer_ids']);

            // Create service items and assign staff — every item gets the
            // single job-card-level payment type selected in the UI.
            foreach ($validated['service_items'] as $item) {
                $service = Service::findOrFail($item['service_id']);

                // Validate that subcategory belongs to this service
                if ($service->subcategory !== $item['subcategory']) {
                    throw new \Exception('Selected subcategory does not belong to the selected service.');
                }

                $serviceItem = JobCardService::create([
                    'job_card_id' => $jobCard->id,
                    'service_id' => $item['service_id'],
                    'subcategory' => $item['subcategory'],
                    'amount' => $item['amount'],
                    'payment_type_id' => $validated['payment_type_id'],
                ]);

                // Assign staff to service item
                $serviceItem->staff()->sync($item['staff_ids']);
            }

            return redirect()
                ->route('job-cards.index')
                ->with('success', 'Job card created successfully.');
        });
    }

    /**
     * Update job card with service items.
     *
     * NOTE: Payment method is now a single, job-card-level selection
     * (`payment_type_id`) — it is applied to every service item.
     */
    public function update(Request $request, JobCard $jobCard)
    {
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
            'service_items' => [
                'required',
                'array',
                'min:1',
            ],
            'service_items.*.service_id' => [
                'required',
                'exists:services,id',
            ],
            'service_items.*.subcategory' => [
                'required',
                'string',
                'max:100',
            ],
            'service_items.*.staff_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'service_items.*.staff_ids.*' => [
                'exists:staff,id',
            ],
            'service_items.*.amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            // Single payment method for the whole job card
            'payment_type_id' => [
                'required',
                'exists:payment_types,id',
            ],
            'discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'status' => [
                'nullable',
                Rule::in(['pending', 'in_progress', 'completed', 'cancelled']),
            ],
        ]);

        $subtotal = collect($validated['service_items'])->sum('amount');
        $discount = $validated['discount_amount'] ?? 0;

        if ($discount > $subtotal) {
            return back()
                ->withErrors(['discount_amount' => 'Discount cannot be greater than the subtotal amount.'])
                ->withInput();
        }

        return DB::transaction(function () use ($jobCard, $validated, $discount, $subtotal) {
            $paymentMethod = PaymentType::find($validated['payment_type_id'])?->name;
            // Update job card
            $jobCard->update([
                'job_card_name' => $validated['job_card_name'],
                'customer_id' => $validated['customer_ids'][0],
                'status' => $validated['status'] ?? $jobCard->status ?? 'pending',
                'discount_amount' => $discount,
                'subtotal' => $subtotal,
                'total' => max(0, $subtotal - $discount),
                'payment_method' => $paymentMethod,
            ]);

            // Sync customers
            $jobCard->customers()->sync($validated['customer_ids']);

            // Delete existing service items (cascade will delete staff associations)
            $jobCard->serviceItems()->delete();

            // Create new service items and assign staff — every item gets the
            // single job-card-level payment type selected in the UI.
            foreach ($validated['service_items'] as $item) {
                $service = Service::findOrFail($item['service_id']);

                // Validate that subcategory belongs to this service
                if ($service->subcategory !== $item['subcategory']) {
                    throw new \Exception('Selected subcategory does not belong to the selected service.');
                }

                $serviceItem = JobCardService::create([
                    'job_card_id' => $jobCard->id,
                    'service_id' => $item['service_id'],
                    'subcategory' => $item['subcategory'],
                    'amount' => $item['amount'],
                    'payment_type_id' => $validated['payment_type_id'],
                ]);

                // Assign staff to service item
                $serviceItem->staff()->sync($item['staff_ids']);
            }

            return redirect()
                ->route('job-cards.index')
                ->with('success', 'Job card updated successfully.');
        });
    }

    /**
     * Delete job card.
     */
    public function destroy($id)
    {
        $jobCard = JobCard::findOrFail($id);
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
            'serviceItems.service',
            'serviceItems.staff',
            'serviceItems.paymentType',
        ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where(
                        'job_card_name',
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
                        ->orWhereHas('serviceItems.staff', function ($staff) use ($search) {
                            $staff->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        })
                        ->orWhereHas('serviceItems.service', function ($service) use ($search) {
                            $service->where(
                                'service_name',
                                'like',
                                "%{$search}%"
                            );
                        })
                        ->orWhereHas('serviceItems', function ($item) use ($search) {
                            $item->where('subcategory', 'like', "%{$search}%");
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
                $query->whereHas('serviceItems', fn ($item) => $item->where('service_id', $serviceId));
            })
            ->when($subcategory !== '', function ($query) use ($subcategory) {
                $query->whereHas('serviceItems', fn ($item) => $item->where('subcategory', $subcategory));
            })
            ->when(!empty($amountRange) && $amountRange !== 'all', function ($query) use ($amountRange) {
                $query->whereHas('serviceItems', function ($item) use ($amountRange) {
                    match ($amountRange) {
                        'under_500' => $item->where('amount', '<', 500),
                        '500_1000' => $item->whereBetween('amount', [500, 1000]),
                        '1001_2500' => $item->whereBetween('amount', [1001, 2500]),
                        '2501_5000' => $item->whereBetween('amount', [2501, 5000]),
                        'above_5000' => $item->where('amount', '>', 5000),
                        default => null,
                    };
                });
            })
            ->orderBy('created_at', 'desc');
    }
}
