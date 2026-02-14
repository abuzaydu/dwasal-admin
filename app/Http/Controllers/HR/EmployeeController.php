<?php

namespace App\Http\Controllers\HR;

use App\Exports\EmployeeExport;
use App\Http\Controllers\Controller;
use App\Imports\EmployeeImport;
use App\Models\AcademicInfo;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeDoc;
use App\Models\EmployeeMedicalInfo;
use App\Models\LeaveRoster;
use App\Models\NextOfKin;
use App\Models\PayrollSetting;
use App\Models\Position;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Session;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Employees';
        $title = 'Employees';
        $company = Company::find(Session::get('company_id'));
        if (!is_null($company)) {    
            $employees = Employee::where('company_id', $company->id)->select('id', 'emp_id', 'fname', 'mname', 'lname', 'type', 'is_paid_monthly', 'basic_pay_hourly', 'basic_pay_monthly')->orderBy('emp_id', 'asc')->get();
            $positions = Position::all();
            $payroll_settings = PayrollSetting::all();
            return view('hr.employees.index', compact('page', 'title', 'employees', 'positions', 'payroll_settings'));
        }else{
            return view('errors.401');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   $page = 'Add Employee';
        $title = 'Add Employee';
        $positions = Position::all();
        $payroll_settings = PayrollSetting::all();

        return view('hr.employees.create' , compact('page','title','positions', 'payroll_settings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $basic_pay_monthly = 0;
        $basic_pay_hourly = 0;
        $position = Position::find($request['position_id']);

        if (!empty($request['basic_pay_monthly'])) {
            $basic_pay_monthly = $request['basic_pay_monthly'];
        }else{
            $basic_pay_monthly = $position->basic_pay_monthly;
        }
        if (!empty($request['basic_pay_hourly'])) {
            $basic_pay_hourly = $request['basic_pay_hourly'];
        }else{
            $basic_pay_hourly = $position->basic_pay_hourly;
        }
        $emp_id = $this->empID();
        if (!empty($request['emp_id'])) {
            $emp_id = $request['emp_id'];
        }
        $company = Company::find(Session::get('company_id'));
        $emp = Employee::create([
            'company_id' => $company->id,
            'emp_id' => $emp_id,
            'fname' => $request['fname'],
            'mname' => $request['mname'],
            'lname' => $request['lname'],
            'gender' => $request['gender'],
            'address' => $request['address'],
            'mobile' => $request['mobile'],
            'email' => $request['email'],
            'marital_status' => $request['marital_status'],
            'tin' => $request['tin'],
            'nin' => $request['nin'],
            'type' => $request['type'],
            'start_date' => Carbon::parse($request['start_date']),
            'end_date' => Carbon::parse($request['end_date']),
            'account_number' => $request['account_number'],
            'account_name' => $request['account_name'],
            'bank_name' => $request['bank_name'],
            'position_id' => $position->id,
            'trans_allowance' => $request['trans_allowance'],
            'house_allowance' => $request['house_allowance'],
            'com_allowance' => $request['com_allowance'],
            'is_paid_monthly' => $request['is_paid_monthly'],
            'basic_pay_hourly' => $basic_pay_hourly,
            'basic_pay_monthly' => $basic_pay_monthly,
        ]);

        return redirect('employees')->with('success', 'Employee was added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Employee Details';
        $title = 'Employee Details';
        $employee = Employee::with('company')->find(decrypt($id));
        $academic_infos = AcademicInfo::where('employee_id' , $employee->id)->get();
        $medical_infos = EmployeeMedicalInfo::where('employee_id' , $employee->id)->get();
        $employee_docs = EmployeeDoc::where('employee_id' , $employee->id)->get();
        $leave_rosters = LeaveRoster::where('employee_id' , $employee->id)->get();
        $next_of_kins = NextOfKin::where('employee_id' , $employee->id)->get();
        $docs = EmployeeDoc::where('employee_id' , $employee->id)->get();
        $user_photo = null;
        $passport = $docs->where('type' , 'Passport')->first();
        if(!is_null($passport)){
            $user_photo = $passport->link;
        }

        $position = Position::find($employee->position_id);

        return view('hr.employees.show', compact('page', 'title', 'employee', 'position', 'academic_infos' , 'medical_infos' , 'employee_docs' , 'leave_rosters' , 'next_of_kins' , 'docs' , 'user_photo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Employee Details';
        $title = 'Edit Employee Details';
        $employee = Employee::find(decrypt($id));
        $positions = Position::all(); 
        $position = Position::find($employee->position_id);
        $payroll_settings = PayrollSetting::all();
        
        // return $employee;
        return view('hr.employees.edit', compact('page', 'title', 'employee', 'positions' , 'position', 'payroll_settings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $basic_pay_monthly = 0;
        $basic_pay_hourly = 0;
        $position = Position::find($request['position_id']);
        if (!empty($request['basic_pay_monthly']) && $request['basic_pay_monthly'] != 0) {
            $basic_pay_monthly = $request['basic_pay_monthly'];
        }else{
            $basic_pay_monthly = $position->basic_pay_monthly;
        }
        if (!empty($request['basic_pay_hourly']) && $request['basic_pay_hourly'] != 0) {
            $basic_pay_hourly = $request['basic_pay_hourly'];
        }else{
            $basic_pay_hourly = $position->basic_pay_hourly;
        }
        
        $emp = Employee::find(decrypt($id));
        $emp->position_id = $request['position_id'];
        $emp->fname = $request['fname'];
        $emp->mname = $request['mname'];
        $emp->lname = $request['lname'];
        $emp->gender = $request['gender'];
        $emp->marital_status = $request['marital_status'];
        $emp->have_md_condition = empty($request['have_md_condition']) ? false : true ;
        $emp->tin = $request['tin'];
        $emp->nin = $request['nin'];
        $emp->type = $request['type'];
        $emp->start_date = Carbon::parse($request['start_date']);
        $emp->end_date = Carbon::parse($request['end_date']);
        $emp->emp_id = $request['emp_id'];
        $emp->address = $request['address'];
        $emp->mobile = $request['mobile'];
        $emp->email = $request['email'];
        $emp->account_number = $request['account_number'];
        $emp->account_name = $request['account_name'];
        $emp->bank_name = $request['bank_name'];
        $emp->basic_pay_hourly = $basic_pay_hourly;
        $emp->basic_pay_monthly = $basic_pay_monthly;
        $emp->trans_allowance = $request['trans_allowance'];
        $emp->house_allowance = $request['house_allowance'];
        $emp->com_allowance = $request['com_allowance'];
        $emp->is_reg_ssf = $request['is_reg_ssf'];
        $emp->is_reg_mif = $request['is_reg_mif'];
        $emp->is_reg_wcf = $request['is_reg_wcf'];
        $emp->allow_deduct_heslb = $request['allow_deduct_heslb'];
        $emp->save();

        $user = User::where('employee_id', $emp->id)->first();
        if (!is_null($user)) {
            $user->name = $emp->fname.' '.$emp->lname;
            $user->phone = $emp->mobile;
            $user->email = $emp->email;
            $user->save();
        }

        return redirect('employees')->with('success', 'Employee Details was uploaded successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $emp = Employee::find(decrypt($id));
        if (!is_null($emp)) {
            $emp->delete();
        }

        return redirect()->back()->with('success', 'Employee was deleted successfully');
    }


    public function empID(){
        $company = Company::find(Session::get('company_id'));
        $words = preg_split("/[\s,_-]+/", $company->name);
        $acronym = "";
        foreach ($words as $w) {
          $acronym .= mb_substr($w, 0, 1);
        }

        $emp = Employee::latest()->first();
        if (!is_null($emp)) {
            $last =$emp->id  ;
            $id = $acronym.'-'.sprintf('%03d', $last+1);
            return $id;   
        }else{
            $id = $acronym.'-'.sprintf('%03d', 1);
            return $id; 
        }
    }

    public function AutoID(){
        $company = Company::find(Session::get('company_id'));
        $words = preg_split("/[\s,_-]+/", $company->name);
        $acronym = "";
        foreach ($words as $w) {
          $acronym .= mb_substr($w, 0, 1);
        }

        $emp = Employee::latest()->first();
        if (!is_null($emp)) {
            $last =$emp->id  ;
            $id = $acronym.'-'.sprintf('%03d', $last+1);
            return Response::json($id);   
        }else{
            $id = $acronym.'-'.sprintf('%03d', 1);
            return Response::json($id); 
        }
    }

    public function downloadSample()
    {   
        // return Excel::download(new EmployeeExport, 'employees.xlsx');

        if(File::exists(public_path('sample-employees.xlsx'))){
            return response()->download(public_path('sample-employees.xlsx'));
        }else {
            return redirect()->back()->with('error', 'NO such File Exists');
        }
    }

    public function importEmployees(Request $request)
    {
        Excel::import(new EmployeeImport, request()->file('employee_file'));

        return redirect('employees')->with('success', 'Employees Uploaded successfully');
    }

    public function downloadIdCard($id)
    {
        $employee = Employee::with('company','position')->findOrFail($id);
        $position = $employee->position; // Adjust based on your relationship
        $user_photo = $employee->photo; 
        
        // Build QR content
        $qrContent = "FULL NAME: {$employee->fname} {$employee->lname}\nID: " . ($employee->emp_id ?? 'N/A');

        $pdf = Pdf::loadView('hr.employees.id_card_pdf', compact('employee', 'position', 'user_photo', 'qrContent'))
                ->setPaper([0, 0, 400, 600], 'portrait'); // Custom ID card size

        return $pdf->download($employee->fname.'_ID_Card.pdf');
    }
}