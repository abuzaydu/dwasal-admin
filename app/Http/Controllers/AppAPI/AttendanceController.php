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
use Illuminate\Validation\ValidationException;
use Log;


class AttendanceController extends Controller
{
     public function punchIn(Request $request)
    {
        // Log::info($request);
        try{
            // Policy: attendance is recorded only after face match. QR identifies the employee; it does not replace face.
            $request->validate([
                'qr_data' => 'required|string',
                'face_embedding' => 'required|array|min:64',
                'face_embedding.*' => 'numeric',
            ]);

            $employee = $this->resolveEmployeeFromQr($request->input('qr_data'));
            if (!$employee) {
                return response()->json([
                    'message' => 'Invalid employee or company'
                ], 404);
            }

            if (empty($employee->face_embedding) || !is_array($employee->face_embedding)) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Employee has no face enrolled. Register face in the admin kiosk first.',
                ], 422);
            }

            $matchResult = $this->verifyFaceMatch(
                $employee->face_embedding,
                $request->input('face_embedding', [])
            );
            if (!$matchResult['matched']) {
                return response()->json([
                    'message' => 'Face verification failed',
                    'score' => $matchResult['similarity'],
                ], 401);
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
        } catch (ValidationException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Invalid face payload',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function registerFaceTemplate(Request $request)
    {
        try {
            $request->validate([
                'qr_data' => 'required|string',
                'face_embedding' => 'required|array|min:64',
                'face_embedding.*' => 'numeric',
                'face_model_version' => 'nullable|string|max:120',
            ]);

            $employee = $this->resolveEmployeeFromQr($request->input('qr_data'));
            if (!$employee) {
                return response()->json([
                    'message' => 'Invalid employee or company'
                ], 404);
            }

            $normalized = $this->normalizeEmbedding($request->input('face_embedding', []));
            if (empty($normalized)) {
                return response()->json([
                    'message' => 'Invalid face embedding'
                ], 422);
            }

            $employee->face_embedding = $normalized;
            $employee->face_model_version = $request->input('face_model_version', 'facenet');
            $employee->face_registered_at = now();
            $employee->save();

            return response()->json([
                'message' => 'Face template saved successfully',
                'employee_id' => $employee->id,
            ]);
        } catch (DecryptException $e) {
            return response()->json(['success' => 0, 'message' => 'Invalid Payload '.$request['qr_data']]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Invalid face payload',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function verifyEmployeeQr(Request $request)
    {
        try {
            $request->validate([
                'qr_data' => 'required|string',
            ]);

            $employee = $this->resolveEmployeeFromQr($request->input('qr_data'));
            if (!$employee) {
                return response()->json([
                    'message' => 'Invalid employee or company'
                ], 404);
            }

            return response()->json([
                'message' => 'Employee verified',
                'employee_id' => $employee->id,
                'employee_name' => trim(($employee->fname ?? '').' '.($employee->lname ?? '')),
                'company_id' => $employee->company_id,
                'face_registered' => !empty($employee->face_embedding),
            ]);
        } catch (DecryptException $e) {
            return response()->json(['success' => 0, 'message' => 'Invalid Payload '.$request['qr_data']]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Invalid request',
                'errors' => $e->errors(),
            ], 422);
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

    /**
     * QR payload after decrypt is historically one of:
     * - "{employee_id}&{company_id}" (legacy attendance payloads)
     * - "{employee_id}&{emp_id}"     (printed ID cards: employee-id-card.blade.php uses encrypt(id.'&'.emp_id))
     *
     * We load by primary key, then confirm the second segment matches company_id OR emp_id.
     */
    private function resolveEmployeeFromQr(string $encryptedQrData): ?Employee
    {
        $data = decrypt($encryptedQrData);
        $parts = explode('&', $data, 3);
        $employeeId = isset($parts[0]) ? trim($parts[0]) : null;
        $second = isset($parts[1]) ? trim($parts[1]) : null;

        if ($employeeId === null || $employeeId === '') {
            return null;
        }

        $employee = Employee::find($employeeId);
        if (!$employee) {
            return null;
        }

        if ($second === null || $second === '') {
            return $employee;
        }

        if ((string) $employee->company_id === (string) $second) {
            return $employee;
        }

        if ((string) $employee->emp_id === (string) $second) {
            return $employee;
        }

        return null;
    }

    private function verifyFaceMatch(array $storedEmbedding, array $probeEmbedding): array
    {
        $stored = $this->normalizeEmbedding($storedEmbedding);
        $probe = $this->normalizeEmbedding($probeEmbedding);

        if (empty($stored) || empty($probe) || count($stored) !== count($probe)) {
            return ['matched' => false, 'similarity' => 0];
        }

        $dot = 0.0;
        foreach ($stored as $idx => $value) {
            $dot += $value * $probe[$idx];
        }

        $similarity = round($dot, 6);
        $threshold = 0.75;

        return [
            'matched' => $similarity >= $threshold,
            'similarity' => $similarity,
        ];
    }

    private function normalizeEmbedding(array $embedding): array
    {
        if (empty($embedding)) {
            return [];
        }

        $vector = array_map(fn($v) => (float) $v, $embedding);
        $sumSquares = 0.0;
        foreach ($vector as $value) {
            $sumSquares += ($value * $value);
        }

        $norm = sqrt($sumSquares);
        if ($norm <= 0.0) {
            return [];
        }

        return array_map(fn($value) => $value / $norm, $vector);
    }
}