<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display attendance records.
     */
    public function index(Request $request)
    {
        $currentYear = (int) $request->input(
            'year',
            now()->year
        );

        $currentMonth = (int) $request->input(
            'month',
            now()->month
        );

        $search = trim(
            $request->input('search', '')
        );

        /*
        |--------------------------------------------------------------------------
        | Validate selected month/year
        |--------------------------------------------------------------------------
        */

        if ($currentMonth < 1 || $currentMonth > 12) {
            $currentMonth = now()->month;
        }

        if ($currentYear < 2020 || $currentYear > 2099) {
            $currentYear = now()->year;
        }

        /*
        |--------------------------------------------------------------------------
        | Attendance Query
        |--------------------------------------------------------------------------
        */

        $query = StaffAttendance::with('staff')
            ->where('year', $currentYear)
            ->where('month', $currentMonth);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {
            $query->whereHas('staff', function ($q) use ($search) {
                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Attendance Records
        |--------------------------------------------------------------------------
        */

        $attendances = $query
            ->join(
                'staff',
                'staff_attendances.staff_id',
                '=',
                'staff.id'
            )
            ->orderBy('staff.name')
            ->select('staff_attendances.*')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Active Staff
        |--------------------------------------------------------------------------
        */

        $staff = Staff::where(
            'status',
            'active'
        )
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalStaff = Staff::where(
            'status',
            'active'
        )->count();

        $attendanceRecords = StaffAttendance::where(
            'year',
            $currentYear
        )
            ->where(
                'month',
                $currentMonth
            )
            ->count();

        /*
        | Total calendar days in selected month.
        |
        | This is calculated automatically and is NOT taken
        | from the database/browser.
        */

        $totalDaysInMonth = Carbon::create(
            $currentYear,
            $currentMonth,
            1
        )->daysInMonth;

        /*
        | Total working days currently stored
        | across attendance records.
        */

        $totalWorkingDays = StaffAttendance::where(
            'year',
            $currentYear
        )
            ->where(
                'month',
                $currentMonth
            )
            ->sum('total_working_days');

        /*
        | Total present days.
        */

        $totalPresentDays = StaffAttendance::where(
            'year',
            $currentYear
        )
            ->where(
                'month',
                $currentMonth
            )
            ->sum('present_days');

        /*
        | Total absent days.
        */

        $totalAbsentDays = StaffAttendance::where(
            'year',
            $currentYear
        )
            ->where(
                'month',
                $currentMonth
            )
            ->sum('absent_days');

        /*
        |--------------------------------------------------------------------------
        | Available Years
        |--------------------------------------------------------------------------
        */

        $availableYears = range(
            now()->year,
            now()->year - 3
        );

        /*
        |--------------------------------------------------------------------------
        | Month Name
        |--------------------------------------------------------------------------
        */

        $monthName = Carbon::create(
            $currentYear,
            $currentMonth,
            1
        )->format('F');

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'attendance.index',
            compact(
                'attendances',
                'staff',
                'currentYear',
                'currentMonth',
                'availableYears',
                'totalStaff',
                'attendanceRecords',
                'totalWorkingDays',
                'totalPresentDays',
                'totalAbsentDays',
                'totalDaysInMonth',
                'search',
                'monthName'
            )
        );
    }


    /**
     * Store attendance.
     *
     * One attendance record per staff/month/year.
     *
     * Total working days are automatically calculated
     * from the selected month and year.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => [
                'required',
                'exists:staff,id',
            ],

            'year' => [
                'required',
                'integer',
                'min:2020',
                'max:2099',
            ],

            'month' => [
                'required',
                'integer',
                'min:1',
                'max:12',
            ],

            'present_days' => [
                'required',
                'integer',
                'min:0',
            ],

            'absent_days' => [
                'required',
                'integer',
                'min:0',
            ],
        ], [
            'staff_id.required' =>
                'Please select a staff member.',

            'staff_id.exists' =>
                'Selected staff member does not exist.',

            'year.required' =>
                'Year is required.',

            'month.required' =>
                'Month is required.',

            'present_days.required' =>
                'Present days is required.',

            'present_days.integer' =>
                'Present days must be a whole number.',

            'present_days.min' =>
                'Present days cannot be negative.',

            'absent_days.required' =>
                'Absent days is required.',

            'absent_days.integer' =>
                'Absent days must be a whole number.',

            'absent_days.min' =>
                'Absent days cannot be negative.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Automatically Calculate Total Days
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | January   = 31
        | February  = 28/29
        | April     = 30
        | August    = 31
        |
        */

        $totalWorkingDays = Carbon::create(
            $validated['year'],
            $validated['month'],
            1
        )->daysInMonth;

        /*
        |--------------------------------------------------------------------------
        | Validate Attendance
        |--------------------------------------------------------------------------
        */

        $attendanceDays =
            $validated['present_days']
            + $validated['absent_days'];

        if ($attendanceDays > $totalWorkingDays) {
            return back()
                ->withInput()
                ->withErrors([
                    'present_days' =>
                        "Present days ({$validated['present_days']}) + " .
                        "Absent days ({$validated['absent_days']}) " .
                        "cannot exceed {$totalWorkingDays} days.",
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Create / Update
        |--------------------------------------------------------------------------
        */

        StaffAttendance::updateOrCreate(
            [
                'staff_id' => $validated['staff_id'],
                'year' => $validated['year'],
                'month' => $validated['month'],
            ],
            [
                'total_working_days' => $totalWorkingDays,
                'present_days' => $validated['present_days'],
                'absent_days' => $validated['absent_days'],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'attendance.index',
                [
                    'year' => $validated['year'],
                    'month' => $validated['month'],
                ]
            )
            ->with(
                'success',
                'Attendance record saved successfully.'
            );
    }


    /**
     * Update attendance.
     *
     * Total working days are recalculated automatically
     * based on the record's month and year.
     */
    public function update(
        Request $request,
        StaffAttendance $attendance
    ) {
        $validated = $request->validate([
            'present_days' => [
                'required',
                'integer',
                'min:0',
            ],

            'absent_days' => [
                'required',
                'integer',
                'min:0',
            ],
        ], [
            'present_days.required' =>
                'Present days is required.',

            'present_days.integer' =>
                'Present days must be a whole number.',

            'present_days.min' =>
                'Present days cannot be negative.',

            'absent_days.required' =>
                'Absent days is required.',

            'absent_days.integer' =>
                'Absent days must be a whole number.',

            'absent_days.min' =>
                'Absent days cannot be negative.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Automatically Recalculate Total Working Days
        |--------------------------------------------------------------------------
        */

        $totalWorkingDays = Carbon::create(
            $attendance->year,
            $attendance->month,
            1
        )->daysInMonth;

        /*
        |--------------------------------------------------------------------------
        | Validate Attendance
        |--------------------------------------------------------------------------
        */

        $attendanceDays =
            $validated['present_days']
            + $validated['absent_days'];

        if ($attendanceDays > $totalWorkingDays) {
            return back()
                ->withInput()
                ->withErrors([
                    'present_days' =>
                        "Present days ({$validated['present_days']}) + " .
                        "Absent days ({$validated['absent_days']}) " .
                        "cannot exceed {$totalWorkingDays} days.",
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $attendance->update([
            'total_working_days' => $totalWorkingDays,
            'present_days' => $validated['present_days'],
            'absent_days' => $validated['absent_days'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'attendance.index',
                [
                    'year' => $attendance->year,
                    'month' => $attendance->month,
                ]
            )
            ->with(
                'success',
                'Attendance record updated successfully.'
            );
    }


    /**
     * Show attendance record.
     */
    public function show(
        StaffAttendance $attendance
    ) {
        $attendance->load('staff');

        /*
        |--------------------------------------------------------------------------
        | Ensure total days is always correct
        |--------------------------------------------------------------------------
        */

        $totalWorkingDays = Carbon::create(
            $attendance->year,
            $attendance->month,
            1
        )->daysInMonth;

        /*
        |--------------------------------------------------------------------------
        | Return Attendance Data
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'id' => $attendance->id,

            'staff_id' => $attendance->staff_id,

            'staff' => $attendance->staff,

            'year' => $attendance->year,

            'month' => $attendance->month,

            'total_working_days' => $totalWorkingDays,

            'present_days' => (int) $attendance->present_days,

            'absent_days' => (int) $attendance->absent_days,

            /*
            | Remaining days can be used as leave/unaccounted days.
            */

            'remaining_days' => max(
                0,
                $totalWorkingDays
                - $attendance->present_days
                - $attendance->absent_days
            ),
        ]);
    }


    /**
     * Delete attendance record.
     */
    public function destroy(
        StaffAttendance $attendance
    ) {
        $attendance->delete();

        return response()->json([
            'success' => true,

            'message' =>
                'Attendance record deleted successfully.',
        ]);
    }
}