<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ExportsManagementList;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use ExportsManagementList;

    /**
     * Customer list.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $filter = trim($request->input('filter', ''));

        $customers = $this->filteredQuery($request)
            ->paginate(9)
            ->withQueryString();

        return view('customers.index', compact(
            'customers',
            'search',
            'filter'
        ));
    }

    public function exportExcel(Request $request)
    {
        $headers = ['Name', 'Email', 'Mobile', 'Joined'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn (Customer $customer) => [
                $customer->name,
                $customer->email ?: '—',
                $customer->mobile_number ?: '—',
                $customer->created_at?->format('d M Y') ?? '—',
            ]
        );

        return $this->exportCsvResponse($headers, $rows, 'customers-list');
    }

    public function exportPdf(Request $request)
    {
        $headers = ['Name', 'Email', 'Mobile', 'Joined'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn (Customer $customer) => [
                $customer->name,
                $customer->email ?: '—',
                $customer->mobile_number ?: '—',
                $customer->created_at?->format('d M Y') ?? '—',
            ]
        );

        return $this->exportPdfResponse(
            'Customers List',
            $headers,
            $rows,
            'customers-list',
            'Registered clients export'
        );
    }

    /**
     * Store customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'mobile_number' => [
                'nullable',
                'string',
                'max:20',
            ],
        ]);

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with(
                'success',
                'Customer added successfully.'
            );
    }

    /**
     * Update customer.
     */
    public function update(
        Request $request,
        Customer $customer
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'mobile_number' => [
                'nullable',
                'string',
                'max:20',
            ],
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with(
                'success',
                'Customer updated successfully.'
            );
    }

    /**
     * Delete customer.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.',
        ]);
    }

    private function filteredQuery(Request $request)
    {
        $search = trim($request->input('search', ''));
        $filter = trim($request->input('filter', ''));
        $name = trim($request->input('name', ''));
        $email = trim($request->input('email', ''));
        $contact = trim($request->input('contact', ''));
        $status = trim($request->input('status', $filter));

        $query = match ($status) {
            'inactive' => Customer::onlyTrashed(),
            'all' => Customer::withTrashed(),
            default => Customer::query(),
        };

        return $query
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere(
                            'mobile_number',
                            'like',
                            "%{$search}%"
                        );
                });
            })
            ->when($name !== '', fn ($q) => $q->where('name', 'like', "%{$name}%"))
            ->when($email !== '', fn ($q) => $q->where('email', 'like', "%{$email}%"))
            ->when($contact !== '', fn ($q) => $q->where('mobile_number', 'like', "%{$contact}%"))
            ->when($filter === 'with_email', function ($q) {
                $q->whereNotNull('email')->where('email', '!=', '');
            })
            ->when($filter === 'new_month', function ($q) {
                $q->where('created_at', '>=', now()->startOfMonth());
            })
            ->latest();
    }
}