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
use Illuminate\Support\Facades\Log;


class AttendanceController extends Controller
{
    /** Minimum cosine similarity to accept a face match (stricter = fewer wrong employee punches). */
    private const FACE_MATCH_THRESHOLD = 0.78;
    /** Runner-up similarity that triggers lookalike / ambiguity checks. */
    private const FACE_AMBIGUITY_RUNNER_UP_MIN = 0.68;
    /** Required gap between 1st and 2nd match when runner-up is strong (similar-looking staff). */
    private const FACE_AMBIGUITY_MIN_GAP = 0.06;
    /** Extra gap required when runner-up is very close to threshold (high collision risk). */
    private const FACE_AMBIGUITY_STRICT_GAP = 0.08;
    private const FACE_AMBIGUITY_STRICT_RUNNER_UP_MIN = 0.72;
    public function punchIn(Request $request)
    {
        // Log::info($request);
        try {
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
                    } else {
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
                } else {
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
            } else {

                // Calculate if late
                $companyStartTime = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $today . ' ' . $setting->start_of_day
                );

                if (is_null($attendance)) {
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
            return response()->json(['success' => 0, 'message' => 'Invalid Payload ' . $request['qr_data']]);
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
                'face_embedding' => 'required_without:face_templates|array|min:64',
                'face_embedding.*' => 'numeric',
                'face_templates' => 'sometimes|array|min:1|max:5',
                'face_templates.*' => 'array|min:64',
                'face_templates.*.*' => 'numeric',
                'face_model_version' => 'nullable|string|max:120',
            ]);

            $employee = $this->resolveEmployeeFromQr($request->input('qr_data'));
            if (!$employee) {
                return response()->json([
                    'message' => 'Invalid employee or company'
                ], 404);
            }

            if (FaceEmbeddingStorage::hasEnrollment($employee->getRawOriginal('face_embedding'))) {
                return response()->json([
                    'success' => 0,
                    'message' => 'This employee ID already has Face ID enrolled. Each QR can only be enrolled once. Remove the existing enrollment in admin to replace it.',
                ], 409);
            }

            $templates = $this->extractEnrollmentTemplates($request);
            if (empty($templates)) {
                return response()->json([
                    'message' => 'Invalid face embedding'
                ], 422);
            }

            if ($request->has('face_templates') && count($templates) < 3) {
                return response()->json([
                    'success' => 0,
                    'message' => 'At least 3 face samples are required for enrollment.',
                ], 422);
            }

            if (!FaceEmbeddingStorage::templatesAreConsistent($templates)) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Face samples were not consistent. Please enroll again with steady lighting and face centered.',
                ], 422);
            }

            // Prevent one face being enrolled under multiple employees (same company).
            $conflict = $this->findConflictingFaceOwner(
                (int) $employee->company_id,
                $templates,
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

            $packed = FaceEmbeddingStorage::packTemplatesForStorage($templates);
            if (empty($packed)) {
                return response()->json([
                    'message' => 'Invalid face embedding'
                ], 422);
            }

            $employee->face_embedding = $packed;
            $employee->face_model_version = $request->input('face_model_version', 'facenet');
            $employee->face_registered_at = now();
            $employee->save();

            return response()->json([
                'message' => 'Face template saved successfully',
                'employee_id' => $employee->id,
            ]);
        } catch (DecryptException $e) {
            return response()->json(['success' => 0, 'message' => 'Invalid Payload ' . $request['qr_data']]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('registerFaceTemplate DB error: ' . $e->getMessage());
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
            $pendingToken = Str::uuid()->toString();

            return response()->json([
                'message' => 'Employee verified',
                'employee_id' => $employee->id,
                'employee_name' => trim(($employee->fname ?? '') . ' ' . ($employee->lname ?? '')),
                'company_id' => $employee->company_id,
                'face_registered' => FaceEmbeddingStorage::hasEnrollment(
                    $employee->getRawOriginal('face_embedding')
                ),
                'pending_token' => $pendingToken,
            ]);
        } catch (DecryptException $e) {
            return response()->json(['success' => 0, 'message' => 'Invalid Payload ' . $request['qr_data']]);
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

            $ranked = $this->rankEmployeesByFaceSimilarity(
                $employees,
                $request->input('face_embedding', [])
            );

            if (empty($ranked)) {
                return response()->json([
                    'message' => 'Face not recognized',
                ], 401);
            }

            $selection = $this->selectEmployeeFromRankedMatches($ranked);
            if (!empty($selection['error'])) {
                return response()->json([
                    'message' => $selection['error'],
                ], (int) ($selection['status'] ?? 409));
            }

            $bestEmployee = $selection['employee'];
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

    /**
     * @return list<array{employee: Employee, similarity: float}>
     */
    private function rankEmployeesByFaceSimilarity($employees, array $probeEmbedding): array
    {
        $ranked = [];
        foreach ($employees as $employee) {
            if (!FaceEmbeddingStorage::hasEnrollment($employee->getRawOriginal('face_embedding'))) {
                continue;
            }
            $result = $this->matchProbeAgainstEmployee($employee, $probeEmbedding);
            $sim = (float) ($result['similarity'] ?? 0.0);
            if ($sim <= 0.0) {
                continue;
            }
            $ranked[] = [
                'employee' => $employee,
                'similarity' => $sim,
            ];
        }

        usort($ranked, static function (array $a, array $b): int {
            return $b['similarity'] <=> $a['similarity'];
        });

        return $ranked;
    }

    /**
     * Picks one employee or returns an error when scores are too close (lookalikes).
     *
     * @param list<array{employee: Employee, similarity: float}> $ranked
     * @return array{employee?: Employee, error?: string, status?: int}
     */
    private function selectEmployeeFromRankedMatches(array $ranked): array
    {
        $best = $ranked[0];
        $bestSim = (float) $best['similarity'];

        if ($bestSim < self::FACE_MATCH_THRESHOLD) {
            return [
                'error' => 'Face not recognized',
                'status' => 401,
            ];
        }

        if (!isset($ranked[1])) {
            return ['employee' => $best['employee']];
        }

        $secondSim = (float) $ranked[1]['similarity'];
        $gap = $bestSim - $secondSim;

        if ($secondSim >= self::FACE_AMBIGUITY_RUNNER_UP_MIN && $gap < self::FACE_AMBIGUITY_MIN_GAP) {
            return [
                'error' => 'Face match ambiguous: more than one similar employee. Use Staff Entrance QR or retry with better lighting.',
                'status' => 409,
            ];
        }

        if ($secondSim >= self::FACE_AMBIGUITY_STRICT_RUNNER_UP_MIN && $gap < self::FACE_AMBIGUITY_STRICT_GAP) {
            return [
                'error' => 'Face match ambiguous: more than one similar employee. Use Staff Entrance QR or retry with better lighting.',
                'status' => 409,
            ];
        }

        if (isset($ranked[2])) {
            $thirdSim = (float) $ranked[2]['similarity'];
            if ($thirdSim >= 0.65 && ($secondSim - $thirdSim) < 0.04 && $gap < 0.10) {
                return [
                    'error' => 'Face match ambiguous: more than one similar employee. Use Staff Entrance QR or retry with better lighting.',
                    'status' => 409,
                ];
            }
        }

        return ['employee' => $best['employee']];
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
    /**
     * @param list<array<int, float>>|array<int, float> $probeTemplates
     */
    private function findConflictingFaceOwner(
        int $companyId,
        array $probeTemplates,
        int $excludeEmployeeId
    ): ?Employee {
        $probes = $this->coerceTemplateList($probeTemplates);
        if (empty($probes)) {
            return null;
        }

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

            $sim = $this->maxSimilarityAgainstEmployee($other, $probes);

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
     * @return list<array<int, float>>
     */
    private function extractEnrollmentTemplates(Request $request): array
    {
        $templates = [];
        $rawList = $request->input('face_templates');
        if (is_array($rawList)) {
            foreach ($rawList as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $normalized = $this->normalizeEmbedding($item);
                if (!empty($normalized)) {
                    $templates[] = $normalized;
                }
            }
        }

        if (!empty($templates)) {
            return $templates;
        }

        $single = $this->normalizeEmbedding($request->input('face_embedding', []));
        return empty($single) ? [] : [$single];
    }

    /**
     * @param list<array<int, float>>|array<int, float> $templates
     * @return list<array<int, float>>
     */
    private function coerceTemplateList(array $templates): array
    {
        if (isset($templates[0]) && is_numeric($templates[0])) {
            $one = $this->normalizeEmbedding($templates);
            return empty($one) ? [] : [$one];
        }

        $out = [];
        foreach ($templates as $item) {
            if (!is_array($item)) {
                continue;
            }
            $normalized = $this->normalizeEmbedding($item);
            if (!empty($normalized)) {
                $out[] = $normalized;
            }
        }

        return $out;
    }

    /**
     * Best match across all probe templates vs all stored templates for one employee.
     *
     * @param list<array<int, float>> $probeTemplates
     */
    private function maxSimilarityAgainstEmployee(Employee $employee, array $probeTemplates): float
    {
        $best = 0.0;
        foreach ($probeTemplates as $probe) {
            $result = $this->matchProbeAgainstEmployee($employee, $probe);
            $sim = (float) ($result['similarity'] ?? 0.0);
            if ($sim > $best) {
                $best = $sim;
            }
        }

        return $best;
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
