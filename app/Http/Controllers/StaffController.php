<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ExportsManagementList;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    use ExportsManagementList;

    /**
     * Staff list.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $filter = trim($request->input('filter', ''));

        $staff = $this->filteredQuery($request)
            ->paginate(9)
            ->withQueryString();

        return view('staff.index', compact('staff', 'search', 'filter'));
    }

    public function exportExcel(Request $request)
    {
        $headers = ['Name', 'Email', 'Mobile', 'Status'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn (Staff $staff) => [
                $staff->name,
                $staff->email ?: '—',
                $staff->mobile_number ?: '—',
                ucfirst($staff->status),
            ]
        );

        return $this->exportCsvResponse($headers, $rows, 'staff-list');
    }

    public function exportPdf(Request $request)
    {
        $headers = ['Name', 'Email', 'Mobile', 'Status'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn (Staff $staff) => [
                $staff->name,
                $staff->email ?: '—',
                $staff->mobile_number ?: '—',
                ucfirst($staff->status),
            ]
        );

        return $this->exportPdfResponse(
            'Staff List',
            $headers,
            $rows,
            'staff-list',
            'Salon team members export'
        );
    }

    /**
     * Store new staff.
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

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        Staff::create($validated);

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff member added successfully.');
    }

    /**
     * Update staff.
     */
    public function update(Request $request, Staff $staff)
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

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $staff->update($validated);

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Change active/inactive status.
     */
    public function toggleStatus(Staff $staff)
    {
        $newStatus = $staff->status === 'active'
            ? 'inactive'
            : 'active';

        $staff->update([
            'status' => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => $newStatus === 'active'
                ? 'Staff member activated successfully.'
                : 'Staff member deactivated successfully.',
        ]);
    }

    private function filteredQuery(Request $request)
    {
        $search = trim($request->input('search', ''));
        $filter = trim($request->input('filter', ''));

        return Staff::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile_number', 'like', "%{$search}%");
                });
            })
            ->when(in_array($filter, ['active', 'inactive'], true), function ($query) use ($filter) {
                $query->where('status', $filter);
            })
            ->latest();
    }
}
