<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeDoc;
use App\Services\FingerprintTemplateStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EmployeeFingerprintController extends Controller
{
    public function index()
    {
        $page = 'Fingerprint';
        $title = 'Fingerprint Enrollments';
        $company = Company::find(Session::get('company_id'));

        if (!$company) {
            return view('errors.401');
        }

        $employees = Employee::query()
            ->where('employees.company_id', $company->id)
            ->whereNotNull('employees.fingerprint_template')
            ->where('employees.fingerprint_enabled', true)
            ->leftJoin('positions', 'positions.id', '=', 'employees.position_id')
            ->select(
                'employees.id',
                'employees.emp_id',
                'employees.fname',
                'employees.mname',
                'employees.lname',
                'employees.fingerprint_registered_at',
                'employees.fingerprint_model_version',
                'employees.fingerprint_algorithm_version',
                'employees.fingerprint_finger',
                'employees.fingerprint_last_verified_at',
                'positions.name as position_name'
            )
            ->orderByDesc('employees.fingerprint_registered_at')
            ->get()
            ->filter(fn (Employee $e) => FingerprintTemplateStorage::hasEnrollment($e->getRawOriginal('fingerprint_template')))
            ->values();

        $fingerprintCards = $employees->map(function (Employee $employee) {
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

        return view('hr.employees.fingerprint.index', compact('page', 'title', 'fingerprintCards'));
    }

    public function destroy(string $id)
    {
        $employeeId = decrypt($id);
        $company = Company::find(Session::get('company_id'));

        $employee = Employee::where('id', $employeeId)
            ->where('company_id', $company?->id)
            ->firstOrFail();

        $employee->fingerprint_template = null;
        $employee->fingerprint_model_version = null;
        $employee->fingerprint_algorithm_version = null;
        $employee->fingerprint_registered_at = null;
        $employee->fingerprint_last_verified_at = null;
        $employee->fingerprint_finger = null;
        $employee->fingerprint_enabled = false;
        $employee->save();

        return redirect()
            ->route('employees.fingerprint.index')
            ->with('success', 'Fingerprint removed for ' . trim($employee->fname . ' ' . $employee->lname));
    }

    public function toggle(string $id)
    {
        $employeeId = decrypt($id);
        $company = Company::find(Session::get('company_id'));

        $employee = Employee::where('id', $employeeId)
            ->where('company_id', $company?->id)
            ->firstOrFail();

        $employee->fingerprint_enabled = !$employee->fingerprint_enabled;
        $employee->save();

        $status = $employee->fingerprint_enabled ? 'enabled' : 'disabled';

        return redirect()
            ->route('employees.fingerprint.index')
            ->with('success', 'Fingerprint access ' . $status . ' for ' . trim($employee->fname . ' ' . $employee->lname));
    }
}
