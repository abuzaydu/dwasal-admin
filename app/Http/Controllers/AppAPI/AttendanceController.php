<?php

namespace App\Http\Controllers\AppAPI;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    

    public function punchOut(Request $request)
{
    $request->validate([
        'end_of_day' => 'required|date',
        'employee' => 'required|array|min:1'
    ]);

    $user = auth()->user();
    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized'
        ], 401);
    }
    $company = $user->companies()
    ->where('companies.id', $request->company_id)->first();

    if (!$company) {
        return response()->json([
            'status' => false,
            'message' => 'Company not found'
        ], 404);
    }

    $setting = AttendanceSetting::where('company_id', $company->id)->first();

    if (!$setting) {
        return response()->json([
            'status' => false,
            'message' => 'Attendance setting not configured'
        ], 404);
    }

    DB::beginTransaction();

    try {

        $date = Carbon::parse($request->end_of_day)->format('Y-m-d');
        $leave = Carbon::parse($request->end_of_day);

        foreach ($request->employee as $employeeId) {

            $attendance = EmployeeAttendance::whereDate('created_at', $date)
                ->where('employee_id', $employeeId)
                ->first();

            if (!$attendance) {
                continue;
            }

            $attendance->end_of_day = $request->end_of_day;

            $officialEnd = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $date . ' ' . $setting->end_of_day
            );

            $attendance->is_fullday = $leave->greaterThanOrEqualTo($officialEnd);

            $attendance->save();
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Punch out recorded successfully'
        ], 200);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function punchIn(Request $request)
    {
    $request->validate([
        'company_id'   => 'required|integer',
        'start_of_day' => 'required|date',
        'employee'     => 'required|array|min:1'
    ]);

    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized'
        ], 401);
    }

    // Verify user belongs to company
    $company = $user->companies()
        ->where('companies.id', $request->company_id)
        ->first();

    if (!$company) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized for this company'
        ], 403);
    }

    $setting = AttendanceSetting::where('company_id', $company->id)->first();

    if (!$setting) {
        return response()->json([
            'status' => false,
            'message' => 'Attendance setting not found'
        ], 404);
    }

     DB::beginTransaction();

    try {
    $startDate = Carbon::parse($request->start_of_day)->format('Y-m-d');
    $arrive = Carbon::parse($request->start_of_day);

    foreach ($request->employee as $employeeId) {

        //  Fetch existing attendance row
        $attendance = EmployeeAttendance::whereDate('created_at', $startDate)
            ->where('employee_id', $employeeId)
            ->where('company_id', $company->id)
            ->first();

        if (!$attendance) {
            // Do NOT create new row
            return response()->json([
                'status' => false,
                'message' => "Attendance record not found for employee ID {$employeeId}"
            ], 404);
        }

        //  Update the columns like browser controller
        $attendance->start_of_day = $request->start_of_day;

        if ($attendance->status === 'Absent') {
            $attendance->status = 'Present';
        }

        $attendance->is_present = true;

        // Late check
        $officialStart = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $startDate . ' ' . $setting->start_of_day
        );

        $attendance->is_late = $arrive->greaterThan($officialStart);

        // Full day check if end_of_day exists
        if ($attendance->end_of_day) {
            $leave = Carbon::parse($attendance->end_of_day);
            $officialEnd = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $leave->format('Y-m-d') . ' ' . $setting->end_of_day
            );

            $attendance->is_fullday = $leave->greaterThanOrEqualTo($officialEnd);
        }

        $attendance->save();
         DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Punch in recorded successfully'
        ], 200);

    }
    } catch (\Exception $e) {

        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);;
}
    }


    }