<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ExportsManagementList;
use App\Models\Service;
use App\Support\ServiceIconResolver;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    use ExportsManagementList;

    /**
     * Display services.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $filter = trim($request->input('filter', ''));

        $services = $this->filteredQuery($request)
            ->paginate(9)
            ->withQueryString();

        $filterCategories = Service::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $filterSubcategories = Service::whereNotNull('subcategory')
            ->distinct()
            ->pluck('subcategory')
            ->filter()
            ->values();

        return view('services.index', compact(
            'services',
            'filterCategories',
            'filterSubcategories',
            'search',
            'filter'
        ));
    }

    /**
     * Export services as Excel/CSV.
     */
    public function exportExcel(Request $request)
    {
        $headers = [
            'Service',
            'Category',
            'Sub Category',
            'Status',
        ];

        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn (Service $service) => [
                $service->service_name,
                $service->category,
                $service->subcategory ?: '—',
                ucfirst($service->status),
            ]
        );

        return $this->exportCsvResponse(
            $headers,
            $rows,
            'services-list'
        );
    }

    /**
     * Export services as PDF.
     */
    public function exportPdf(Request $request)
    {
        $headers = [
            'Service',
            'Category',
            'Sub Category',
            'Status',
        ];

        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn (Service $service) => [
                $service->service_name,
                $service->category,
                $service->subcategory ?: '—',
                ucfirst($service->status),
            ]
        );

        return $this->exportPdfResponse(
            'Services List',
            $headers,
            $rows,
            'services-list',
            'Salon services export'
        );
    }

    /**
     * Suggest icon from service name.
     */
    public function suggestIcon(Request $request)
    {
        $validated = $request->validate([
            'service_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'subcategory' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $result = ServiceIconResolver::resolve(
            $validated['service_name'] ?? '',
            $validated['category'] ?? null,
            $validated['subcategory'] ?? null
        );

        return response()->json([
            'success' => true,
            'primary' => $result['primary'],
            'alternatives' => $result['alternatives'],
            'category' => $result['category'],
            'label' => ServiceIconResolver::label(
                $result['primary']
            ),
        ]);
    }

    /**
     * Store service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->serviceRules()
        );

        $validated['icon'] = $this->serviceNameIcon($validated['service_name']);

        Service::create($validated);

        return redirect()
            ->route('services.index')
            ->with(
                'success',
                'Service added successfully.'
            );
    }

    /**
     * Update service.
     */
    public function update(
        Request $request,
        Service $service
    ) {
        $validated = $request->validate(
            $this->serviceRules()
        );

        $validated['icon'] = $this->serviceNameIcon($validated['service_name']);

        $service->update($validated);

        return redirect()
            ->route('services.index')
            ->with(
                'success',
                'Service updated successfully.'
            );
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Service $service)
    {
        $newStatus = $service->status === 'active'
            ? 'inactive'
            : 'active';

        $service->update([
            'status' => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => $newStatus === 'active'
                ? 'Service activated successfully.'
                : 'Service deactivated successfully.',
        ]);
    }

    /**
     * Delete service.
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully.',
        ]);
    }

    /**
     * Service validation rules.
     *
     * Price intentionally does not exist.
     */
    private function serviceRules(): array
    {
        return [
            'service_name' => [
                'required',
                'string',
                'max:150',
            ],

            'category' => [
                'required',
                'string',
                'max:100',
            ],

            'subcategory' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ];
    }

    private function serviceNameIcon(string $name): string
    {
        $name = strtolower($name);

        if (preg_match('/hair|haircut|hairstyle|hair spa|colour|color|smoothing|smoothening|keratin/', $name)) return 'haircut';
        if (preg_match('/nail|manicure|pedicure|nail art/', $name)) return 'nails';
        if (preg_match('/skin|facial|cleanup|clean up|skin care|treatment/', $name)) return 'facial';
        if (preg_match('/makeup|make up|bridal makeup|party makeup/', $name)) return 'makeup';

        return 'default';
    }

    /**
     * Filter services.
     *
     * No price filtering because services no longer have prices.
     */
    private function filteredQuery(Request $request)
    {
        $search = trim(
            $request->input('search', '')
        );

        $filter = trim(
            $request->input('filter', '')
        );

        $name = trim(
            $request->input('name', '')
        );

        $category = trim(
            $request->input('category', '')
        );

        $subcategory = trim(
            $request->input('subcategory', '')
        );

        $status = trim(
            $request->input(
                'status',
                $filter
            )
        );

        return Service::query()

            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where(
                            'service_name',
                            'like',
                            "%{$search}%"
                        )
                            ->orWhere(
                                'category',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'subcategory',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )

            ->when(
                $name !== '',
                fn ($q) =>
                    $q->where(
                        'service_name',
                        'like',
                        "%{$name}%"
                    )
            )

            ->when(
                $category !== '',
                fn ($q) =>
                    $q->where(
                        'category',
                        $category
                    )
            )

            ->when(
                $subcategory !== '',
                fn ($q) =>
                    $q->where(
                        'subcategory',
                        $subcategory
                    )
            )

            ->when(
                in_array(
                    $status,
                    ['active', 'inactive'],
                    true
                ),
                function ($query) use ($status) {
                    $query->where(
                        'status',
                        $status
                    );
                }
            )

            ->latest();
    }
}
