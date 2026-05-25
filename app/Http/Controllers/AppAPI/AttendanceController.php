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
use App\Models\User;
use App\Services\FaceEmbeddingStorage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Log;


class AttendanceController extends Controller
{
    private const FACE_MATCH_THRESHOLD = 0.78;
    private const FACE_AMBIGUITY_SECOND_MIN = 0.76;
    private const FACE_AMBIGUITY_GAP = 0.04;

    // NOTE:
    // System currently supports face-only punch-in.
    // QR + Face binding was partially implemented but removed for stability.
    // Future upgrade can reintroduce secure 1:1 verification using pending_token.

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

            if (!FaceEmbeddingStorage::hasEnrollment($employee->getRawOriginal('face_embedding'))) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Employee has no face enrolled. Register face in the admin kiosk first.',
                ], 422);
            }

            $matchResult = $this->matchProbeAgainstEmployee(
                $employee,
                $request->input('face_embedding', [])
            );
            if (!$matchResult['matched']) {
                return response()->json([
                    'message' => 'Face verification failed',
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

            // Prevent one face being enrolled under multiple employees (same company).
            $conflict = $this->findConflictingFaceOwner(
                (int) $employee->company_id,
                $normalized,
                (int) $employee->id
            );
            if ($conflict) {
                $conflictName = trim(
                    ($conflict->fname ?? '') . ' ' .
                    ($conflict->mname ?? '') . ' ' .
                    ($conflict->lname ?? '')
                );
                return response()->json([
                    'success' => 0,
                    'message' => $conflictName !== ''
                        ? "This face is already registered for {$conflictName} (ID: {$conflict->emp_id}). Each employee must use their own face."
                        : "This face is already registered for employee ID {$conflict->emp_id}. Each employee must use their own face.",
                ], 409);
            }

            // JSON column: store wrapped encrypted object (avoids MySQL 3140 invalid JSON).
            $employee->face_embedding = FaceEmbeddingStorage::packForStorage($normalized);
            $employee->face_model_version = $request->input('face_model_version', 'facenet');
            $employee->face_registered_at = now();
            $employee->save();

            return response()->json([
                'message' => 'Face template saved successfully',
                'employee_id' => $employee->id,
            ]);
        } catch (DecryptException $e) {
            return response()->json(['success' => 0, 'message' => 'Invalid Payload '.$request['qr_data']]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('registerFaceTemplate DB error: '.$e->getMessage());
            return response()->json([
                'success' => 0,
                'message' => 'Could not save face data. Please contact admin.',
            ], 500);
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

            /** @var User|null $user */
            $user = auth('api')->user();
            $companyId = $this->resolveCompanyIdFromAuthUser($user);
            if ($companyId && (int)$employee->company_id !== (int)$companyId) {
                return response()->json([
                    'message' => 'Employee does not belong to your company'
                ], 403);
            }

            // pending_token kept for API compatibility; attendance_pending_verifications is deprecated.
            // TODO: remove after full migration to QR+Face binding
            $pendingToken = Str::uuid()->toString();

            return response()->json([
                'message' => 'Employee verified',
                'employee_id' => $employee->id,
                'employee_name' => trim(($employee->fname ?? '').' '.($employee->lname ?? '')),
                'company_id' => $employee->company_id,
                'face_registered' => FaceEmbeddingStorage::hasEnrollment(
                    $employee->getRawOriginal('face_embedding')
                ),
                'pending_token' => $pendingToken,
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

    /**
     * Face-only punch-in for already enrolled employees (no QR required).
     */
    public function punchInByFace(Request $request)
    {
        try {
            $request->validate([
                'face_embedding' => 'required|array|min:64',
                'face_embedding.*' => 'numeric',
            ]);

            /** @var User|null $user */
            $user = auth('api')->user();
            $companyId = $this->resolveCompanyIdFromAuthUser($user);
            if (!$companyId) {
                return response()->json([
                    'message' => 'No default company configured for this account'
                ], 403);
            }

            $employees = Employee::where('company_id', $companyId)
                ->whereNotNull('face_embedding')
                ->get();

            if ($employees->isEmpty()) {
                return response()->json([
                    'message' => 'No enrolled employees found'
                ], 404);
            }

            $bestEmployee = null;
            $bestSimilarity = -1.0;
            $secondSimilarity = -1.0;
            foreach ($employees as $employee) {
                if (!FaceEmbeddingStorage::hasEnrollment($employee->getRawOriginal('face_embedding'))) {
                    continue;
                }
                $result = $this->matchProbeAgainstEmployee(
                    $employee,
                    $request->input('face_embedding', [])
                );
                $sim = (float) ($result['similarity'] ?? 0.0);
                if ($sim > $bestSimilarity) {
                    $secondSimilarity = $bestSimilarity;
                    $bestSimilarity = $sim;
                    $bestEmployee = $employee;
                } elseif ($sim > $secondSimilarity) {
                    $secondSimilarity = $sim;
                }
            }

            if (!$bestEmployee || $bestSimilarity < self::FACE_MATCH_THRESHOLD) {
                return response()->json([
                    'message' => 'Face not recognized',
                ], 401);
            }

            if ($secondSimilarity >= self::FACE_AMBIGUITY_SECOND_MIN
                && ($bestSimilarity - $secondSimilarity) < self::FACE_AMBIGUITY_GAP) {
                return response()->json([
                    'message' => 'Face match ambiguous. Please retry.',
                ], 409);
            }

            $qrData = encrypt($bestEmployee->id . '&' . $bestEmployee->company_id);
            return $this->punchIn(new Request([
                'qr_data' => $qrData,
                'face_embedding' => $request->input('face_embedding', []),
            ]));
        } catch (ValidationException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Invalid face payload',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    private function resolveCompanyIdFromAuthUser(?User $user): ?int
    {
        if (!$user) {
            return null;
        }
        $company = $user->companies()->orderByPivot('is_default', 'desc')->first();
        if (!$company) {
            return null;
        }

        return (int) $company->id;
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

    /**
     * Returns another employee (same company) who already owns this face, if any.
     */
    private function findConflictingFaceOwner(
        int $companyId,
        array $probeEmbedding,
        int $excludeEmployeeId
    ): ?Employee {
        $employees = Employee::where('company_id', $companyId)
            ->where('id', '!=', $excludeEmployeeId)
            ->whereNotNull('face_embedding')
            ->get();

        $bestMatch = null;
        $bestSimilarity = 0.0;

        foreach ($employees as $other) {
            if (!FaceEmbeddingStorage::hasEnrollment($other->getRawOriginal('face_embedding'))) {
                continue;
            }

            $result = $this->matchProbeAgainstEmployee($other, $probeEmbedding);
            $sim = (float) ($result['similarity'] ?? 0.0);

            if ($sim > $bestSimilarity) {
                $bestSimilarity = $sim;
                $bestMatch = $other;
            }
        }

        if ($bestMatch && $bestSimilarity >= self::FACE_MATCH_THRESHOLD) {
            return $bestMatch;
        }

        return null;
    }

    /**
     * Best similarity across one or more stored templates for an employee.
     */
    private function matchProbeAgainstEmployee(Employee $employee, array $probeEmbedding): array
    {
        $templates = FaceEmbeddingStorage::templatesFromStored(
            $employee->getRawOriginal('face_embedding')
        );
        $probe = FaceEmbeddingStorage::normalizeVector($probeEmbedding);

        if (empty($probe) || empty($templates)) {
            return ['matched' => false, 'similarity' => 0.0];
        }

        $bestSimilarity = 0.0;
        foreach ($templates as $stored) {
            $result = $this->verifyFaceMatch($stored, $probe);
            if ($result['similarity'] > $bestSimilarity) {
                $bestSimilarity = $result['similarity'];
            }
        }

        return [
            'matched' => $bestSimilarity >= self::FACE_MATCH_THRESHOLD,
            'similarity' => $bestSimilarity,
        ];
    }

    private function verifyFaceMatch(array $storedEmbedding, array $probeEmbedding): array
    {
        if (count($storedEmbedding) !== count($probeEmbedding)) {
            return ['matched' => false, 'similarity' => 0.0];
        }

        $dot = 0.0;
        foreach ($storedEmbedding as $idx => $value) {
            $dot += $value * $probeEmbedding[$idx];
        }

        $similarity = round($dot, 6);

        return [
            'matched' => $similarity >= self::FACE_MATCH_THRESHOLD,
            'similarity' => $similarity,
        ];
    }

    private function normalizeEmbedding(array $embedding): array
    {
        return FaceEmbeddingStorage::normalizeVector($embedding);
    }
}