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

        return view('services.index', compact(
            'services',
            'search',
            'filter'
        ));
    }

    public function exportExcel(Request $request)
    {
        $headers = ['Service', 'Category', 'Sub Category', 'Price', 'Status'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn (Service $service) => [
                $service->service_name,
                $service->category,
                $service->subcategory ?: '—',
                number_format((float) $service->price, 2),
                ucfirst($service->status),
            ]
        );

        return $this->exportCsvResponse($headers, $rows, 'services-list');
    }

    public function exportPdf(Request $request)
    {
        $headers = ['Service', 'Category', 'Sub Category', 'Price', 'Status'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn (Service $service) => [
                $service->service_name,
                $service->category,
                $service->subcategory ?: '—',
                '₹' . number_format((float) $service->price, 2),
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
     * Suggest icon from service name (JSON).
     */
    public function suggestIcon(Request $request)
    {
        $validated = $request->validate([
            'service_name' => ['nullable', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:100'],
            'subcategory' => ['nullable', 'string', 'max:100'],
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
            'label' => ServiceIconResolver::label($result['primary']),
        ]);
    }

    /**
     * Store service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->serviceRules());

        if (empty($validated['icon'])) {
            $resolved = ServiceIconResolver::resolve(
                $validated['service_name'],
                $validated['category'],
                $validated['subcategory'] ?? null
            );
            $validated['icon'] = $resolved['primary'];
        } else {
            $validated['icon'] = ServiceIconResolver::normalize($validated['icon']);
        }

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
        $validated = $request->validate($this->serviceRules());

        $validated['icon'] = ServiceIconResolver::normalize(
            $validated['icon'] ?? $service->icon
        );

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
     * @return array<string, mixed>
     */
    private function serviceRules(): array
    {
        return [
            'service_name' => [
                'required',
                'string',
                'max:150',
            ],

            'icon' => [
                'nullable',
                'string',
                Rule::in(ServiceIconResolver::validKeys()),
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

            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
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

    private function filteredQuery(Request $request)
    {
        $search = trim($request->input('search', ''));
        $filter = trim($request->input('filter', ''));

        return Service::query()
            ->when($search !== '', function ($query) use ($search) {
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
            })
            ->when(in_array($filter, ['active', 'inactive'], true), function ($query) use ($filter) {
                $query->where('status', $filter);
            })
            ->latest();
    }
}
