<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeDoc;
use App\Services\FaceEmbeddingStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EmployeeFaceIdController extends Controller
{
    public function index()
    {
        $page = 'Face ID';
        $title = 'Face ID Enrollments';
        $company = Company::find(Session::get('company_id'));

        if (!$company) {
            return view('errors.401');
        }

        $employees = Employee::query()
            ->where('employees.company_id', $company->id)
            ->whereNotNull('employees.face_embedding')
            ->leftJoin('positions', 'positions.id', '=', 'employees.position_id')
            ->select(
                'employees.id',
                'employees.emp_id',
                'employees.fname',
                'employees.mname',
                'employees.lname',
                'employees.face_registered_at',
                'employees.face_model_version',
                'employees.face_embedding',
                'positions.name as position_name'
            )
            ->orderByDesc('employees.face_registered_at')
            ->get()
            ->filter(fn (Employee $e) => FaceEmbeddingStorage::hasEnrollment($e->getRawOriginal('face_embedding')))
            ->values();

        $faceCards = $employees->map(function (Employee $employee) {
            $passport = EmployeeDoc::where('employee_id', $employee->id)
                ->where('type', 'Passport')
                ->first();

            return (object) [
                'employee' => $employee,
                'photo_url' => $passport ? asset('storage/' . $passport->link) : null,
                'full_name' => trim(
                    ($employee->fname ?? '') . ' ' .
                    ($employee->mname ?? '') . ' ' .
                    ($employee->lname ?? '')
                ),
            ];
        });

        return view('hr.employees.face-id.index', compact('page', 'title', 'faceCards'));
    }

    public function destroy(string $id)
    {
        $employeeId = decrypt($id);
        $company = Company::find(Session::get('company_id'));

        $employee = Employee::where('id', $employeeId)
            ->where('company_id', $company?->id)
            ->firstOrFail();

        $employee->face_embedding = null;
        $employee->face_model_version = null;
        $employee->face_registered_at = null;
        $employee->save();

        return redirect()
            ->route('employees.face-id.index')
            ->with('success', 'Face ID removed for ' . trim($employee->fname . ' ' . $employee->lname));
    }
}
