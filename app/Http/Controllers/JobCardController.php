<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ExportsManagementList;
use App\Models\Customer;
use App\Models\JobCard;
use App\Models\Service;
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

        return view('job-cards.index', compact(
            'jobCards',
            'customers',
            'services',
            'search',
            'filter'
        ));
    }

    public function exportExcel(Request $request)
    {
        $headers = ['Job Card', 'Customer', 'Service', 'Sub Category', 'Status', 'Created'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn (JobCard $jobCard) => [
                $jobCard->job_card_name,
                $jobCard->customer->name ?? '—',
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
        $headers = ['Job Card', 'Customer', 'Service', 'Sub Category', 'Status', 'Created'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn (JobCard $jobCard) => [
                $jobCard->job_card_name,
                $jobCard->customer->name ?? '—',
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
        $validated = $request->validate([
            'job_card_name' => [
                'required',
                'string',
                'max:150',
            ],

            'customer_id' => [
                'required',
                'exists:customers,id',
            ],

            'service_id' => [
                'required',
                'exists:services,id',
            ],

            'subcategory' => [
                'required',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
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
            ! $service->subcategory ||
            $service->subcategory !== $validated['subcategory']
        ) {
            return back()
                ->withErrors([
                    'subcategory' =>
                        'Selected subcategory does not belong to the selected service.',
                ])
                ->withInput();
        }

        JobCard::create($validated);

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
    public function update(  Request $request,  JobCard $jobCard     ) {
        $validated = $request->validate([
            'job_card_name' => [
                'required',
                'string',
                'max:150',
            ],

            'customer_id' => [
                'required',
                'exists:customers,id',
            ],

            'service_id' => [
                'required',
                'exists:services,id',
            ],

            'subcategory' => [
                'required',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
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
            ! $service->subcategory ||
            $service->subcategory !== $validated['subcategory']
        ) {
            return back()
                ->withErrors([
                    'subcategory' =>
                        'Selected subcategory does not belong to the selected service.',
                ])
                ->withInput();
        }

        $jobCard->update($validated);

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
            'service',
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
                        ->orWhereHas('customer', function ($customer) use ($search) {
                            $customer->where(
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