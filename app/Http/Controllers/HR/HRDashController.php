<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\Setting;
use App\Models\Employee;
use App\Models\MPayroll;
use App\Models\Payroll;
use App\Models\Department;
use App\Models\Event;

class HRDashController extends Controller
{ 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $page = 'Dashboard';
        //check if user opted for date range
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $company = Company::find(Session::get('company_id'));
        $new_employees = Employee::where('company_id', $company->id)->where('created_at', '>=', Carbon::now()->subDays(31)->toDateTimeString())->count();
        $total_employees = Employee::where('company_id', $company->id)->count();

        $mpayrolls = MPayroll::where('company_id', $company->id)->whereBetween('month', [$start, $end])->get();
        
        $labels = array();        
        $payrolls = array();
        $total_gross_income = 0;
        foreach ($mpayrolls as $key => $mpayroll) {
            $allpayrolls = Payroll::where('m_payroll_id', $mpayroll->id)->join('employees', 'employees.id', '=', 'payrolls.employee_id')->get();
            $m_gross_income = 0;

            foreach ($allpayrolls as $key => $payroll) {
                $employee = Employee::find($payroll->employee_id);
                 //Earnings
                $gross_income = 0;
                $hourly = 0;
                $monthly = 0;
                $overtime = 0;
                if ($employee->is_paid_monthly) {
                    $monthly = $employee->basic_pay_monthly;
                    $hourly = ($monthly/$payroll->days_work)/8;
                    $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                    $gross_income = $monthly + $overtime + $payroll->bonuses+$employee->house_allowance+$employee->trans_allowance+$employee->com_allowance;
                }else{    
                    $hourly = $employee->basic_pay_hourly;
                    $monthly = $hourly * 8 * $payroll->days_work;
                    $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                    $gross_income = $monthly + $overtime + $payroll->bonuses+$employee->house_allowance+$employee->trans_allowance+$employee->com_allowance;;  
                }



                $m_gross_income += $gross_income;
            }
            array_push($labels, date('M', strtotime($mpayroll->month)));
            array_push($payrolls, $m_gross_income);
            $total_gross_income += $m_gross_income;
        }

        $series = array();
        $depts = Department::where('company_id', $company->id)->get();
        foreach ($depts as $key => $dept) {
            $deptsalaries = array();
            foreach ($mpayrolls as $key => $mpayroll) {
                $allpayrolls = Payroll::where('m_payroll_id', $mpayroll->id)->join('employees', 'employees.id', '=', 'payrolls.employee_id')->get();
                $m_gross_income = 0;

                foreach ($allpayrolls as $key => $payroll) {
                    $employee = Employee::find($payroll->employee_id);
                    $emp_dept = $employee->department()->first();
                    if (!is_null($emp_dept) && $emp_dept->id == $dept->id) {
                         
                        //Earnings
                        $gross_income = 0;
                        $hourly = 0;
                        $monthly = 0;
                        $overtime = 0;
                        if ($employee->is_paid_monthly) {
                            $monthly = $employee->basic_pay_monthly;
                            $hourly = ($monthly/$payroll->days_work)/8;
                            $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                            $gross_income = $monthly + $overtime + $payroll->bonuses+$employee->house_allowance+$employee->trans_allowance+$employee->com_allowance;
                        }else{    
                            $hourly = $employee->basic_pay_hourly;
                            $monthly = $hourly * 8 * $payroll->days_work;
                            $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                            $gross_income = $monthly + $overtime + $payroll->bonuses+$employee->house_allowance+$employee->trans_allowance+$employee->com_allowance;;  
                        }

                        $m_gross_income += $gross_income;
                    }
                }
                array_push($deptsalaries, $m_gross_income);
            }
            array_push($series, ['name' => $dept->name, 'data' => $deptsalaries]);
        }

        $total_salary = $total_gross_income;
        $avarage_salary = 0;
        if($mpayrolls->count() > 0 && $total_employees > 0){
            $avarage_salary = ($total_gross_income/$mpayrolls->count())/$total_employees;
        }

        $yesterday = Carbon::yesterday();
        $events = Event::where('company_id', $company->id)->whereDate('start', '>=', $yesterday)->get();
        return view('hr.index', compact('page', 'is_post_query', 'start_date', 'end_date', 'new_employees', 'total_employees', 'total_salary', 'avarage_salary', 'labels', 'payrolls', 'series', 'events'));
    }
}
