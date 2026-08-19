<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComplaintsController extends Controller
{
    /**
     * Display complaints.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $typeFilter = trim($request->input('type_filter', ''));

        /*
        |--------------------------------------------------------------------------
        | Complaint Query
        |--------------------------------------------------------------------------
        */
        $query = Complaint::with([
            'complainantStaff',
            'complaintType',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($search !== '') {
            $query->where(function ($q) use ($search) {

                $q->whereHas(
                    'complainantStaff',
                    function ($staffQuery) use ($search) {
                        $staffQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                );

                $q->orWhereHas(
                    'complaintType',
                    function ($typeQuery) use ($search) {
                        $typeQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                );

                $q->orWhere(
                    'subject',
                    'like',
                    "%{$search}%"
                );

                $q->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Complaint Type Filter
        |--------------------------------------------------------------------------
        */
        if ($typeFilter !== '') {
            $query->where(
                'complaint_type_id',
                $typeFilter
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Complaint List
        |--------------------------------------------------------------------------
        */
        $complaints = $query
            ->orderByDesc('date_of_complaint')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Complaint Types
        |--------------------------------------------------------------------------
        */
        $complaintTypes = ComplaintType::query()
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Active Staff
        |--------------------------------------------------------------------------
        */
        $staff = Staff::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        // Total complaints
        $totalComplaints = Complaint::count();

        // Pending complaints
        $pendingComplaints = Complaint::where(
            'status',
            'Pending'
        )->count();

        // Closed complaints
        $closedComplaints = Complaint::where(
            'status',
            'Closed'
        )->count();

        // Complaints submitted today
        $todayComplaints = Complaint::whereDate(
            'date_of_complaint',
            today()
        )->count();

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */
        return view(
            'complaints.index',
            compact(
                'complaints',
                'complaintTypes',
                'staff',
                'search',
                'typeFilter',
                'totalComplaints',
                'pendingComplaints',
                'closedComplaints',
                'todayComplaints'
            )
        );
    }


    /**
     * Store / Update complaint.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate([
            'complainant_staff_id' => [
                'required',
                'exists:staff,id',
            ],

            'complaint_type_id' => [
                'required',
                'exists:complaint_types,id',
            ],

            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
                'max:5000',
            ],

            'date_of_complaint' => [
                'required',
                'date',
            ],

            'evidence' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Complaint ID
        |--------------------------------------------------------------------------
        */
        $complaintId = $request->input('complaint_id');

        /*
        |--------------------------------------------------------------------------
        | Evidence Upload
        |--------------------------------------------------------------------------
        */
        $evidencePath = null;

        if ($request->hasFile('evidence')) {
            $evidencePath = $request
                ->file('evidence')
                ->store(
                    'complaints',
                    'public'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE EXISTING COMPLAINT
        |--------------------------------------------------------------------------
        */
        if ($complaintId) {

            $complaint = Complaint::findOrFail(
                $complaintId
            );

            $updateData = [
                'complainant_staff_id' =>
                    $validated['complainant_staff_id'],

                'complaint_type_id' =>
                    $validated['complaint_type_id'],

                'subject' =>
                    $validated['subject'],

                'description' =>
                    $validated['description'],

                'date_of_complaint' =>
                    $validated['date_of_complaint'],
            ];

            /*
            |--------------------------------------------------------------------------
            | Replace Evidence Only If New File Uploaded
            |--------------------------------------------------------------------------
            */
            if ($evidencePath) {

                if ($complaint->evidence_path) {
                    Storage::disk('public')->delete(
                        $complaint->evidence_path
                    );
                }

                $updateData['evidence_path'] =
                    $evidencePath;
            }

            /*
            |--------------------------------------------------------------------------
            | Update
            |--------------------------------------------------------------------------
            */
            $complaint->update(
                $updateData
            );

            $message =
                'Complaint updated successfully.';
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE NEW COMPLAINT
        |--------------------------------------------------------------------------
        */
        else {

            Complaint::create([
                'complainant_staff_id' =>
                    $validated['complainant_staff_id'],

                'complaint_type_id' =>
                    $validated['complaint_type_id'],

                'subject' =>
                    $validated['subject'],

                'description' =>
                    $validated['description'],

                'date_of_complaint' =>
                    $validated['date_of_complaint'],

                /*
                |--------------------------------------------------------------------------
                | New complaints always start as Pending
                |--------------------------------------------------------------------------
                */
                'status' =>
                    'Pending',

                'evidence_path' =>
                    $evidencePath,
            ]);

            $message =
                'Complaint added successfully.';
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->route('complaints.index')
            ->with(
                'success',
                $message
            );
    }


    /**
     * Show complaint details.
     */
    /**
 * Show complaint details.
 */
    public function show(Complaint $complaint)
    {
        $complaint->load([
            'complainantStaff',
            'complaintType',
        ]);

        $evidenceUrl = null;
        $evidenceName = null;
        $evidenceSize = null;

        if ($complaint->evidence_path && Storage::disk('public')->exists($complaint->evidence_path)) {
            $evidenceUrl  = Storage::disk('public')->url($complaint->evidence_path);
            $evidenceName = basename($complaint->evidence_path);

            $bytes = Storage::disk('public')->size($complaint->evidence_path);
            $evidenceSize = $bytes >= 1024
                ? round($bytes / 1024, 1) . ' KB'
                : $bytes . ' B';
        }

        return response()->json(array_merge($complaint->toArray(), [
            'evidence_url'  => $evidenceUrl,
            'evidence_name' => $evidenceName,
            'evidence_size' => $evidenceSize,
        ]));
    }


    /**
     * Close complaint.
     */
    public function close(Request $request, Complaint $complaint)
    {
        if ($complaint->status === 'Closed') {
            if ($request->wantsJson()) {
                return response()->json(['status' => $complaint->status, 'message' => 'Complaint is already closed.']);
            }
            return redirect()->route('complaints.index')->with('success', 'Complaint is already closed.');
        }

        $complaint->update(['status' => 'Closed']);

        if ($request->wantsJson()) {
            return response()->json(['status' => $complaint->status, 'message' => 'Complaint closed successfully.']);
        }

        return redirect()->route('complaints.index')->with('success', 'Complaint closed successfully.');
    }


    /**
     * Delete complaint.
     */
    public function destroy(Complaint $complaint)
    {
        /*
        |--------------------------------------------------------------------------
        | Delete Evidence File
        |--------------------------------------------------------------------------
        */
        if ($complaint->evidence_path) {
            Storage::disk('public')->delete(
                $complaint->evidence_path
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Complaint
        |--------------------------------------------------------------------------
        */
        $complaint->delete();

        return redirect()
            ->route('complaints.index')
            ->with(
                'success',
                'Complaint deleted successfully.'
            );
    }
}