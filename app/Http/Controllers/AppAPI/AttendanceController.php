<?php

namespace App\Http\Controllers\AppAPI;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;


class AttendanceController extends Controller
{
     public function punchIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'company_id'  => 'required|integer',
        ]);

        $employeeId = $request->employee_id;
        $companyId  = $request->company_id;

        // Validate employee belongs to this company
        $employee = Employee::where('id', $employeeId)
            ->where('company_id', $companyId)
            ->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Invalid employee or company'
            ], 404);
        }

        $now   = now();
        $today = $now->toDateString();

        // Check if already punched in today
        $attendance = EmployeeAttendance::where('employee_id', $employee->id)
            ->whereDate('created_at', $today)
            ->first();

        if ($attendance && $attendance->start_of_day) {
            return response()->json([
                'message' => 'Employee already punched in today'
            ], 400);
        }

        // Get attendance setting
        $setting = AttendanceSetting::where('company_id', $companyId)->first();
        if (!$setting) {
            return response()->json([
                'message' => 'Attendance setting not configured'
            ], 400);
        }

        // Create new attendance if not exists
        if (!$attendance) {
            $attendance = new EmployeeAttendance();
            $attendance->company_id  = $companyId;
            $attendance->employee_id = $employee->id;
            $attendance->status      = 'Present';
            $attendance->created_at  = $now;
        }

        // Set punch-in
        $attendance->start_of_day = $now;
        $attendance->is_present   = true;
        if($attendance->status == 'Absent'){
            $attendance->status = 'Present';
        }

        // Calculate if late
        $companyStartTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $today . ' ' . $setting->start_of_day
        );
        $attendance->is_late = $now->gt($companyStartTime);

        $attendance->save();

        return response()->json([
            'message'    => 'Punch in successful',
            'employee'   => $employee->fname,
            'time'       => $now->format('H:i:s'),
            'is_late'    => $attendance->is_late
        ]);
    }

     public function punchOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'company_id'  => 'required|integer',
        ]);

        $employeeId = $request->employee_id;
        $companyId  = $request->company_id;

        // Validate employee belongs to this company
        $employee = Employee::where('id', $employeeId)
            ->where('company_id', $companyId)
            ->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Invalid employee or company'
            ], 404);
        }

        $now   = now();
        $today = $now->toDateString();

        // Find today's attendance
        $attendance = EmployeeAttendance::where('employee_id', $employee->id)
            ->whereDate('created_at', $today)
            ->first();

        if (!$attendance || !$attendance->start_of_day) {
            return response()->json([
                'message' => 'Employee has not punched in today'
            ], 400);
        }

        if ($attendance->end_of_day) {
            return response()->json([
                'message' => 'Employee already punched out today'
            ], 400);
        }

        // Set punch out
        $attendance->end_of_day = $now;

        // Get attendance setting
        $setting = AttendanceSetting::where('company_id', $companyId)->first();
        if (!$setting) {
            return response()->json([
                'message' => 'Attendance setting not configured'
            ], 400);
        }

        // Full-day logic
        $companyEndTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $today . ' ' . $setting->end_of_day
        );
        $attendance->is_fullday = $now->gte($companyEndTime);

         $attendance->save();

        return response()->json([
            'message'        => 'Punch out successful',
            'employee'       => $employee->name,
            'time'           => $now->format('H:i:s'),
            'is_fullday'     => $attendance->is_fullday,
        ]);
    }

    

  
    

        }