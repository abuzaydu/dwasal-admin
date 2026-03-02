<?php

namespace App\Http\Controllers\AppAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;
use App\Models\AttendanceSetting;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\AttendanceEntry;
use Log;


class AttendanceController extends Controller
{
     public function punchIn(Request $request)
    {
        // Log::info($request);
        try{
            
            $data = decrypt($request['qr_data']);

            $emp =explode('&', $data);
            // Log::info($emp);

            $employee = Employee::find($emp[0]);
            if (!$employee) {
                return response()->json([
                    'message' => 'Invalid employee or company'
                ], 404);
            }

            // Get attendance setting
            $setting = AttendanceSetting::where('company_id', $employee->company_id)->first();
            if (!$setting) {
                return response()->json([
                    'message' => 'Attendance setting not configured'
                ], 400);
            }

            $now   = now();
            $today = $now->toDateString();

            // Check if already punched in today
            $attendance = EmployeeAttendance::where('employee_id', $employee->id)
                ->whereDate('created_at', $today)
                ->first();

            if ($attendance && $attendance->start_of_day) {
                // return response()->json([
                //     'message' => 'Employee already punched in today'
                // ], 400);

                $companyEndTime = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $today . ' ' . $setting->end_of_day
                );
                if (!is_null($attendance->end_of_day)) {
                    $att_entry = AttendanceEntry::where('employee_attendance_id', $attendance->id)->whereNull('time_out')->first();
                    if (!is_null($att_entry)) {
                        $att_entry->time_out = $now;
                        $att_entry->save();

                        $attendance->end_of_day = $now;
                        $attendance->is_fullday = $now->gte($companyEndTime);
                        $attendance->save();

                        return response()->json([
                            'message'        => 'Punch out successful',
                            'employee'       => $employee->name,
                            'time'           => $now->format('H:i:s'),
                            'is_fullday'     => $attendance->is_fullday,
                        ]);
                    }else{
                        $att_entry = new AttendanceEntry();
                        $att_entry->employee_attendance_id = $attendance->id;
                        $att_entry->time_in = $now;
                        $att_entry->save();

                        return response()->json([
                            'message'    => 'Punch in successful',
                            'employee'   => $employee->fname,
                            'time'       => $now->format('H:i:s'),
                            'is_late'    => $attendance->is_late
                        ]);
                    }
                }else{
                    $att_entry = AttendanceEntry::where('employee_attendance_id', $attendance->id)->first();
                    $att_entry->time_out = $now;
                    $att_entry->save();

                    $attendance->end_of_day = $now;
                    $attendance->is_fullday = $now->gte($companyEndTime);
                    $attendance->save();

                    return response()->json([
                        'message'        => 'Punch out successful',
                        'employee'       => $employee->name,
                        'time'           => $now->format('H:i:s'),
                        'is_fullday'     => $attendance->is_fullday,
                    ]);
                }
            }else{

                // Calculate if late
                $companyStartTime = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $today . ' ' . $setting->start_of_day
                );

                if(is_null($attendance)){
                    $attendance = new EmployeeAttendance();
                    $attendance->company_id  = $employee->company_id;
                    $attendance->employee_id = $employee->id;
                    $attendance->save();
                }
                // Set punch-in
                $attendance->start_of_day = $now;
                $attendance->is_present   = true;
                $attendance->status = 'Present';
                $attendance->is_late = $now->gt($companyStartTime);
                $attendance->save();

                $att_entry = new AttendanceEntry();
                $att_entry->employee_attendance_id = $attendance->id;
                $att_entry->time_in = $now;
                $att_entry->save();

                return response()->json([
                    'message'    => 'Punch in successful',
                    'employee'   => $employee->fname,
                    'time'       => $now->format('H:i:s'),
                    'is_late'    => $attendance->is_late
                ]);
            }
        } catch (DecryptException $e) {
            // Handle the error (e.g., log it, return the raw value, or show a user-friendly error)
            return response()->json(['success' => 0, 'message' => 'Invalid Payload '.$request['qr_data']]); 
        }
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