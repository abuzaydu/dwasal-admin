<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\User;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Position;
use App\Models\Holiday;
use App\Models\Employee;
use App\Models\PayrollTemp;
use App\Models\MPayroll;
use App\Models\Payroll;
use App\Models\PayrollSetting;
use App\Models\Expense;
use App\Models\EmployeeAttendance;
use App\Models\AttendanceSetting;
use App\Models\EmployeeLoan;
use App\Models\AdvanceSalary;

class PayrollController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('permission:payroll-list|payroll-create|payroll-edit|payroll-delete', ['only' => ['index','store']]);
        // $this->middleware('permission:payroll-create', ['only' => ['create','store']]);
        // $this->middleware('permission:payroll-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:payroll-delete', ['only' => ['destroy']]);
    }

    public function dashboard(Request $request)
    {
        $page = 'Dashboard';
        $data = array();
        $dbDate = Carbon::parse('2022-01-10');
        $diffYears = Carbon::now()->diffInYears($dbDate);
        for ($i = $diffYears; $i >= 0; $i--) {
            $year = Carbon::today()->subYears($i)->format('Y');
            array_push($data, array(
                'year' => $year
            ));
        }

        $curyear = Carbon::today()->format('Y');
        if (!is_null($request['year'])) {
            $curyear = $request['year'];
        }
        $company = Company::find(Session::get('company_id'));
        $employees = Employee::where('company_id', $company->id)->count();

        //check if user opted for date range
        $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->endOfYear();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }elseif(!is_null($request['year'])){
            $date = Carbon::createFromFormat('d F Y', '05 January '.$request['year']);
            $start = $date->startOfMonth()->format('Y-m-d');
            $start_date = $start;
            $date1 = Carbon::createFromFormat('d F Y', '05 December '.$request['year']);
            $end = $date1->endOfMonth()->format('Y-m-d');
            $end_date = $end;
            $is_post_query = true;
        }
        $mpayrolls = MPayroll::where('company_id', $company->id)->whereBetween('month', [$start, $end])->orderBy('month', 'asc')->get();


        $total_earns = 0;
        $total_deductions = 0;
        $total_net_pay = 0;
        $months = array();
        $earns = array();
        $deducts = array();
        $nets = array();
        foreach ($mpayrolls as $key => $mpayroll) {
            $month = Carbon::createFromFormat('Y-m-d', $mpayroll->month)->format('F');
            $earnings = ($mpayroll->basic_salaries+$mpayroll->house_allowance+$mpayroll->trans_allowance+$mpayroll->com_allowance+$mpayroll->overtime+$mpayroll->bonuses);
            $deductions = ($mpayroll->paye+$mpayroll->ssf+$mpayroll->heslb+$mpayroll->absences+$mpayroll->lates+$mpayroll->other_deductions);
            $net_pay = $earnings-$deductions;

            array_push($months, $month);
            array_push($earns, $earnings);
            array_push($deducts, $deductions);
            array_push($nets, $net_pay);

            $total_earns += $earnings;
            $total_deductions += $deductions;
            $total_net_pay += $net_pay;
        }

        return view('payrolls.dash', compact('page', 'data', 'curyear', 'employees', 'months', 'earns', 'deducts', 'nets', 'total_earns', 'total_deductions', 'total_net_pay', 'is_post_query', 'start_date', 'end_date'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Payrolls';
        
        //check if user opted for date range
        $start_date = null;            
        $end_date = null;
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }elseif(!is_null($request['month'])){
            $date = Carbon::createFromFormat('d F Y', '05 '.$request['month']);
            $start = $date->firstOfMonth()->format('Y-m-d');
            $start_date = $start;
            $end = $date->endOfMonth()->format('Y-m-d');
            $end_date = $end;
            $is_post_query = true;
        }else{
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $is_post_query = false;
            $start_date = $start->format('Y-m-d');
            $end_date = $end->format('Y-m-d');
        }

        $m = Carbon::today()->startOfMonth();
        $y = Carbon::today()->startOfMonth()->format('Y');
        $curmonth = $m->monthName.' '.$y;
        if (!is_null($request['month'])) {
            $curmonth = $request['month'];
        }

        $company = Company::find(Session::get('company_id'));
        $mpayrolls = MPayroll::where('company_id', $company->id)->whereBetween('month', [$start, $end])->get();
        $payrolls = null;
        $employee = null;
        if (!is_null($request['employee_id'])) {
            $employee = Employee::find($request['employee_id']);
            $payrolls = MPayroll::where('company_id', $company->id)->whereBetween('m_payrolls.month', [$start, $end])->join('payrolls', 'payrolls.m_payroll_id', '=', 'm_payrolls.id')->join('employees', 'employees.id', '=', 'payrolls.employee_id')->where('employee_id', $employee->id)->select('payrolls.id as id', 'emp_id', 'fname', 'lname', 'payrolls.created_at as created_at', 'payrolls.updated_at as updated_at')->get();
        }else{
            $payrolls = MPayroll::where('m_payrolls.company_id', $company->id)->whereBetween('m_payrolls.month', [$start, $end])->join('payrolls', 'payrolls.m_payroll_id', '=', 'm_payrolls.id')->join('employees', 'employees.id', '=', 'payrolls.employee_id')->select('payrolls.id as id', 'emp_id', 'fname', 'lname', 'payrolls.created_at as created_at', 'payrolls.updated_at as updated_at')->get();
        }
        $employees = Employee::where('company_id', $company->id)->get();
        $data = array();
        for ($i = 12; $i >= 0; $i--) {
            $month = Carbon::today()->startOfMonth()->subMonth($i);
            $year = Carbon::today()->startOfMonth()->subMonth($i)->format('Y');
            array_push($data, array(
                'month' => $month->monthName,
                'year' => $year
            ));
        }

        return view('payrolls.index', compact('page', 'mpayrolls', 'payrolls', 'employees', 'data', 'curmonth', 'employee', 'is_post_query', 'start_date', 'end_date'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $page = 'New PayRoll';
        $prsetting = PayrollSetting::where('company_id', Session::get('company_id'))->count();
        if ($prsetting == 0) {
            return redirect('payroll-settings')->with('warning', 'Payroll settings are not checked before. Please confirm the default created settings List to continue.');
        }
        $employees = Employee::where('company_id', Session::get('company_id'))->get();
        $data = array();
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::today()->startOfMonth()->subMonth($i);
            $year = \Carbon\Carbon::today()->startOfMonth()->subMonth($i)->format('Y');
            array_push($data, array(
                'month' => $month->monthName,
                'year' => $year
            ));
        }
        $m = Carbon::today()->startOfMonth();
        $y = Carbon::today()->startOfMonth()->format('Y');
        $start = Carbon::now()->startOfMonth()->format('Y-m-d');
        $end = Carbon::now()->endOfMonth()->format('Y-m-d');
        $curmonth = $m->monthName.' '.$y;

        if (!is_null($request['month'])) {
            if ($request['month'] != Session::get('curmonth')) {
                session()->forget('curmonth');
                // Cancel user payroll temps before creating new ones
                $payrolltemps = PayrollTemp::where('company_id', Session::get('company_id'))->where('user_id', Auth::user()->id)->get();
                foreach ($payrolltemps as $key => $temp) {
                    $temp->delete();
                }
            }

            $curmonth = $request['month'];
            $date = Carbon::createFromFormat('d F Y', '05 '.$curmonth);
            $start = $date->firstOfMonth()->format('Y-m-d');
            $end = $date->endOfMonth()->format('Y-m-d');
        }

        session()->put('curmonth', $curmonth);
        // create new temps
        $mholidays = Holiday::whereBetween('date', [$start, $end])->get(['date']);
        $holidays = array();
        foreach ($mholidays as $key => $value) {
            $dt = Carbon::parse($value->date);
            if (!$dt->isWeekend()) {
                array_push($holidays, $value->date);
            }
        }

        $mworkdays = $this->getWorkingDays($start, $end, $holidays);
        $attsetting = AttendanceSetting::where('company_id', Session::get('company_id'))->first();
        foreach ($employees as $key => $employee) {
            $absences = 0;
            $late = 0;
            $payrollTemp = PayrollTemp::where('employee_id', $employee->id)->where('user_id', Auth::user()->id)->first();
            if (is_null($payrollTemp)) {
                $payrollTemp = new PayrollTemp();
                $payrollTemp->company_id = Session::get('company_id');
                $payrollTemp->user_id = Auth::user()->id;
                $payrollTemp->employee_id = $employee->id;
                $payrollTemp->days_work = $mworkdays;
                $payrollTemp->absences = $absences;
                $payrollTemp->late = $late;
                $payrollTemp->save();
            }
        }

        return view('payrolls.create', compact('page', 'employees', 'data', 'curmonth'));
    }

    //The function returns the no. of business days between two dates and it skips the holidays
    function getWorkingDays($startDate,$endDate,$holidays){
        // do strtotime calculations just once
        $endDate = strtotime($endDate);
        $startDate = strtotime($startDate);

        //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
        //We add one to inlude both dates in the interval.
        $days = round(($endDate - $startDate) / 86400+1);
        // $days = ($endDate - $startDate) / 86400 + 1;
        // return $days;
        $no_full_weeks = floor($days / 7);
        $no_remaining_days = fmod($days, 7);

        //It will return 1 if it's Monday,.. ,7 for Sunday
        $the_first_day_of_week = date("N", $startDate);
        $the_last_day_of_week = date("N", $endDate);

        //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
        //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
        if ($the_first_day_of_week <= $the_last_day_of_week) {
            if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
            if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
        }else {
            // (edit by Tokes to fix an edge case where the start day was a Sunday
            // and the end day was NOT a Saturday)

            // the day of the week for start is later than the day of the week for end
            if ($the_first_day_of_week == 7) {
                // if the start date is a Sunday, then we definitely subtract 1 day
                $no_remaining_days--;

                if ($the_last_day_of_week == 6) {
                    // if the end date is a Saturday, then we subtract another day
                    $no_remaining_days--;
                }
            }
            else {
                // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
                // so we skip an entire weekend and subtract 2 days
                $no_remaining_days -= 2;
            }
        }

        //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
        //---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
        $workingDays = $no_full_weeks * 5;
        if ($no_remaining_days > 0 )
        {
          $workingDays += $no_remaining_days;
        }

        //We subtract the holidays
        foreach($holidays as $holiday){
            $time_stamp=strtotime($holiday);
            //If the holiday doesn't fall in weekend
            if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
                $workingDays--;
        }

        return $workingDays;
    }


    public function cancelPayroll()
    {
        $payrolltemps = PayrollTemp::where('company_id', Session::get('company_id'))->where('user_id', Auth::user()->id)->get();

        foreach ($payrolltemps as $key => $temp) {
            $temp->delete();
        }

        return redirect('payrolls')->with('success', 'Payroll cancelled successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $date = Carbon::createFromFormat('d F Y', '01 '.$request['month']);
        $month = $date->endOfMonth()->format('Y-m-d');
        $company = Company::find(Session::get('company_id'));
        $mpayroll = MPayroll::where('company_id', $company->id)->where('month', $month)->first();
        if (is_null($mpayroll)) {
            $mpayroll = new MPayroll();
            $mpayroll->company_id = Session::get('company_id');
            $mpayroll->user_id = Auth::user()->id;
            $mpayroll->month = $month;
            $mpayroll->save();
            $payrolltemps = PayrollTemp::where('user_id', Auth::user()->id)->get();

            foreach ($payrolltemps as $key => $temp) {
                $payroll = new Payroll();
                $payroll->m_payroll_id = $mpayroll->id;
                $payroll->month = $mpayroll->month;
                $payroll->employee_id = $temp->employee_id;
                $payroll->payid = $this->getUniqueID();
                $payroll->days_work = $temp->days_work;
                $payroll->bonuses = $temp->bonuses;
                $payroll->recovery = $temp->recovery;
                $payroll->late = $temp->late;
                $payroll->absences = $temp->absences;
                $payroll->note = $temp->note;
                $payroll->save();

                //Remove from Temps
                $temp->delete();
            }
        
            $allpayrolls = Payroll::where('m_payroll_id', $mpayroll->id)->get();
            $total_basic_salary = 0;
            $total_transport = 0;
            $total_house_allowance = 0;
            $total_com_allowance = 0;
            $total_overtime = 0;
            $total_bonuses = 0;
            $total_gross_other_earnings = 0;
            $total_gross_pay = 0;
            $total_ssf = 0;
            $total_taxable_income = 0;
            $total_paye = 0;
            $total_net_pay = 0;
            $total_mif = 0;
            $total_wcf = 0;
            $total_heslb = 0;
            $total_advance_salary = 0;
            $total_emp_loan = 0;
            $total_attendance_deduction = 0;
            $total_recovery = 0;
            $total_adjusted_net_pay = 0;
            $payrolls = array();
            foreach ($allpayrolls as $key => $payroll) {
                $employee = Employee::find($payroll->employee_id);
                 //Earnings
                $hourly = 0;
                $monthly = 0;
                $basic_salary = 0;
                $trans_allowance = 0;
                $com_allowance = 0;
                $house_allowance = 0;
                $overtime = 0;
                $bonuses = 0;

                if ($employee->is_paid_monthly) {
                    $monthly = $employee->basic_pay_monthly;
                    $basic_salary = $monthly;
                    $hourly = ($monthly/$payroll->days_work)/8;
                    $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                    $trans_allowance = $employee->trans_allowance;
                    $house_allowance = $employee->house_allowance;
                    $com_allowance = $employee->com_allowance;
                    $overtime = $overtime;
                    $bonuses = $payroll->bonuses;
                }else{    
                    $hourly = $employee->basic_pay_hourly;
                    $monthly = $hourly * 8 * $payroll->days_work;
                    $basic_salary = $monthly;
                    $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                    $trans_allowance = $employee->trans_allowance;
                    $house_allowance = $employee->house_allowance;
                    $com_allowance = $employee->com_allowance;
                    $overtime = $overtime;
                    $bonuses = $payroll->bonuses; 
                }

                $gross_other_earnings = $trans_allowance+$house_allowance+$com_allowance+$overtime+$bonuses;
                $gross_pay = $basic_salary+$gross_other_earnings;

                $sscheme = PayrollSetting::where('company_id', $company->id)->where('name', $employee->ssf)->first();
                $hisscheme = PayrollSetting::where('company_id', $company->id)->where('name', $employee->mif)->first();
                $ps_wcf = PayrollSetting::where('company_id', $company->id)->where('name', 'WCF')->first();

                //Deductions
                $ssf = 0;
                if ($employee->is_reg_ssf) {       
                    if (!is_null($sscheme)) {
                        $ssf = round($gross_pay * $sscheme->percent_rate/100);
                    }
                }

                $taxable_income = $gross_pay-$ssf;

                $payevalue = 0;
                $paygrp = PayrollSetting::where('company_id', Session::get('company_id'))->where('min_income', '<=', $taxable_income)->where('max_income', '>=', $taxable_income)->first();
                if(!is_null($paygrp)){
                    $payevalue = $paygrp->fixed_paye_value+(($taxable_income-$paygrp->min_income)*$paygrp->percent_rate/100);

                }

                $net_pay = $taxable_income-$payevalue;

                $mif = 0;
                if ($employee->is_reg_mif) {       
                    if (!is_null($hisscheme)) {
                        $mif = round($gross_pay * $hisscheme->percent_rate/100);
                    }
                }

                $wcf = 0;
                if ($employee->is_reg_wcf) {       
                    if (!is_null($ps_wcf)) {
                        $wcf = round($gross_pay * $ps_wcf->percent_rate/100);
                    }
                }
                $heslb = 0;
                if ($employee->allow_deduct_heslb) {
                    $ps_heslb = PayrollSetting::where('company_id', $company->id)->where('name', 'HESLB')->first();
                    if (!is_null($ps_heslb)) {
                        $heslb = round($net_pay * $ps_heslb->percent_rate/100);
                    }
                }

                $emploan_amount = 0;
                $emploan = EmployeeLoan::where('employee_id', $employee->id)->whereRaw('amount > amount_paid')->first();
                if (!is_null($emploan)) {
                    $emploan_amount = round($emploan->amount*($emploan->return_rate/100), 2);
                }

                $advance_salary = 0;
                $empadvancesalary = AdvanceSalary::where('employee_id', $employee->id)->whereRaw('amount > amount_paid')->first();
                if (!is_null($empadvancesalary)) {
                    $advance_salary = $empadvancesalary->amount-$empadvancesalary->amount_paid;
                }

                $per_day = $hourly * 8;
                $late = $payroll->late;
                $absent = $payroll->absences;
                $late_perpay = $hourly / 60;
                $late_overall = $late_perpay * $late;
                $absent_overall = $hourly * 8 * $absent;

                $attendance_deduction = $late_overall+$absent_overall;

                $recovery = $payroll->recovery;

                $other_deduction = $advance_salary+$emploan_amount+$late_overall+$absent_overall+$recovery;

                $adjusted_net_pay = $net_pay-($heslb+$other_deduction);

                $total_basic_salary += $basic_salary;
                $total_transport += $trans_allowance;                
                $total_house_allowance += $house_allowance;
                $total_com_allowance += $com_allowance;
                $total_overtime += $overtime;
                $total_bonuses += $bonuses;
                $total_gross_other_earnings += $gross_other_earnings;
                $total_gross_pay += $gross_pay;
                $total_ssf += $ssf;
                $total_taxable_income += $taxable_income;
                $total_paye += $payevalue;
                $total_net_pay += $net_pay;
                $total_mif += $mif;
                $total_wcf += $wcf;
                $total_heslb += $heslb;
                $total_advance_salary += $advance_salary;
                $total_emp_loan += $emploan_amount;
                $total_attendance_deduction += $attendance_deduction;
                $total_recovery += $recovery;
                $total_adjusted_net_pay += $adjusted_net_pay;
            }

            $other_deductions = $total_advance_salary+$total_emp_loan+$total_attendance_deduction+$total_recovery;

            $mpayroll->basic_salaries = $total_basic_salary;
            $mpayroll->house_allowance = $total_house_allowance;
            $mpayroll->trans_allowance = $total_transport;
            $mpayroll->com_allowance += $total_com_allowance;
            $mpayroll->overtime = $total_overtime;
            $mpayroll->bonuses = $total_bonuses;
            $mpayroll->paye = $total_paye;
            $mpayroll->ssf = $total_ssf;
            $mpayroll->wcf = $total_wcf;
            $mpayroll->heslb = $total_heslb;
            $mpayroll->other_deductions = $other_deductions;
            $mpayroll->save(); 

            return redirect('payrolls')->with('success', 'Payroll created successfully');
        }else{
            return redirect('payrolls')->with('info', 'Payroll for '.date('M Y', strtotime($month)).' already created');           
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Payslip';
        $title = 'Payslip';
        
        $company = Company::find(Session::get('company_id'));
        $payroll = Payroll::find(decrypt($id));
        if (!is_null($payroll)) {
                
            $employee = Employee::find($payroll->employee_id);
            $shop = Shop::find($employee->shop_id);
            $position = 'Not Assinged';
            $emppos = Position::find($employee->position_id);
            if (!is_null($emppos)) {
                $position = $emppos->name;
            }

            //Earnings
            $hourly = 0;
            $monthly = 0;
            $basic_salary = 0;
            $trans_allowance = 0;
            $com_allowance = 0;
            $house_allowance = 0;
            $overtime = 0;
            $bonuses = 0;

            if ($employee->is_paid_monthly) {
                $monthly = $employee->basic_pay_monthly;
                $basic_salary = $monthly;
                $hourly = ($monthly/$payroll->days_work)/8;
                $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                $trans_allowance = $employee->trans_allowance;
                $house_allowance = $employee->house_allowance;
                $com_allowance = $employee->com_allowance;
                $overtime = $overtime;
                $bonuses = $payroll->bonuses;
            }else{    
                $hourly = $employee->basic_pay_hourly;
                $monthly = $hourly * 8 * $payroll->days_work;
                $basic_salary = $monthly;
                $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                $trans_allowance = $employee->trans_allowance;
                $house_allowance = $employee->house_allowance;
                $com_allowance = $employee->com_allowance;
                $overtime = $overtime;
                $bonuses = $payroll->bonuses; 
            }

            $gross_other_earnings = $trans_allowance+$house_allowance+$com_allowance+$overtime+$bonuses;
            $gross_pay = $basic_salary+$gross_other_earnings;

            $sscheme = PayrollSetting::where('company_id', $company->id)->where('name', $employee->ssf)->first();
            $hisscheme = PayrollSetting::where('company_id', $company->id)->where('name', $employee->mif)->first();
            $ps_wcf = PayrollSetting::where('company_id', $company->id)->where('name', 'WCF')->first();

            //Deductions
            $ssf = 0;
            if ($employee->is_reg_ssf) {       
                if (!is_null($sscheme)) {
                    $ssf = round($gross_pay * $sscheme->percent_rate/100);
                }
            }

            $taxable_income = $gross_pay-$ssf;

            $payevalue = 0;
            $paygrp = PayrollSetting::where('company_id', Session::get('company_id'))->where('min_income', '<=', $taxable_income)->where('max_income', '>=', $taxable_income)->first();
            if(!is_null($paygrp)){
                $payevalue = $paygrp->fixed_paye_value+(($taxable_income-$paygrp->min_income)*$paygrp->percent_rate/100);
            }

            $net_pay = $taxable_income-$payevalue;

            $mif = 0;
            if ($employee->is_reg_mif) {       
                if (!is_null($hisscheme)) {
                    $mif = round($gross_pay * $hisscheme->percent_rate/100);
                }
            }

            $wcf = 0;
            if ($employee->is_reg_wcf) {       
                if (!is_null($ps_wcf)) {
                    $wcf = round($gross_pay * $ps_wcf->percent_rate/100);
                }
            }
            $ps_heslb = null;
            $heslb = 0;
            if ($employee->allow_deduct_heslb) {
                $ps_heslb = PayrollSetting::where('company_id', $company->id)->where('name', 'HESLB')->first();
                if (!is_null($ps_heslb)) {
                    $heslb = round($net_pay * $ps_heslb->percent_rate/100);
                }
            }

            $emploan_amount = 0;
            $emploan = EmployeeLoan::where('employee_id', $employee->id)->whereRaw('amount > amount_paid')->first();
            if (!is_null($emploan)) {
                $emploan_amount = round($emploan->amount*($emploan->return_rate/100), 2);
            }

            $advance_salary = 0;
            $empadvancesalary = AdvanceSalary::where('employee_id', $employee->id)->whereRaw('amount > amount_paid')->first();
            if (!is_null($empadvancesalary)) {
                $advance_salary = $empadvancesalary->amount-$empadvancesalary->amount_paid;
            }

            $per_day = $hourly * 8;
            $late = $payroll->late;
            $absent = $payroll->absences;
            $late_perpay = $hourly / 60;
            $late_overall = $late_perpay * $late;
            $absent_overall = $hourly * 8 * $absent;

            $attendance_deduction = $late_overall+$absent_overall;

            $recovery = $payroll->recovery;

            $other_deduction = $advance_salary+$emploan_amount+$late_overall+$absent_overall+$recovery;
            $total_deductions = $heslb+$other_deduction;
            $adjusted_net_pay = $net_pay-$total_deductions;

                
            $defcurr = '';
            $shopcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', 1)->first();
            if (!is_null($shopcurr)) {
                $defcurr = $shopcurr->code;
            }

            $departments = '';

            $depts = $employee->department()->get();
            foreach ($depts as $key => $value) {
                if($key > 0) {
                    $departments .= ', '.$value->name;
                }else{
                    $departments .= ''.$value->name;
                }
            }
            return view('payrolls.show', compact('page', 'title', 'company', 'shop', 'employee', 'position', 'departments', 'payroll', 'basic_salary', 'trans_allowance', 'com_allowance', 'house_allowance', 'overtime', 'bonuses', 'gross_pay', 'sscheme', 'ssf', 'payevalue', 'net_pay', 'hisscheme', 'mif', 'ps_heslb', 'heslb', 'advance_salary', 'emploan', 'emploan_amount', 'attendance_deduction', 'recovery', 'total_deductions', 'adjusted_net_pay', 'defcurr'));
        }else{
            return redirect()->back()->with('error', 'Payroll not found');
        }
        
    }

    public function editPayroll($id)
    {
        $page = 'Edit Payroll Month';
        $mpayroll = MPayroll::find(decrypt($id));

        $data = array();
        for ($i = 12; $i >= 0; $i--) {
            $month = \Carbon\Carbon::today()->startOfMonth()->subMonth($i);
            $year = \Carbon\Carbon::today()->startOfMonth()->subMonth($i)->format('Y');
            array_push($data, array(
                'month' => $month->monthName,
                'year' => $year
            ));
        }

        $date = Carbon::createFromFormat('Y-m-d', $mpayroll->month);
        $curmonth = $date->monthName.' '.$date->endOfMonth()->format('Y');

        return view('payrolls.edit-payroll', compact('page', 'mpayroll', 'data', 'curmonth'));
    }

    public function updatePayroll(Request $request)
    {
        $date = Carbon::createFromFormat('d F Y', '01 '.$request['month']);
        $month = $date->endOfMonth()->format('Y-m-d');

        $mpayroll = MPayroll::find($request['id']);
        $mpayroll->month = $month;
        $mpayroll->save();

        $payrolls = Payroll::where('m_payroll_id', $mpayroll->id)->get();
        foreach ($payrolls as $key => $payroll) {
            $payroll->month = $mpayroll->month;
            $payroll->save();
        }

        return redirect('payrolls')->with('success', 'Payroll updated successfully');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Payroll';
        $employees = Employee::where('company_id', Session::get('company_id'))->get();
        $payroll = Payroll::find(decrypt($id));
        $data = array();
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::today()->startOfMonth()->subMonth($i);
            $year = \Carbon\Carbon::today()->startOfMonth()->subMonth($i)->format('Y');
            array_push($data, array(
                'month' => $month->monthName,
                'year' => $year
            ));
        }

        $date = Carbon::createFromFormat('Y-m-d', $payroll->month);
        $curmonth = $date->monthName.' '.$date->endOfMonth()->format('Y');
        
        return view('payrolls.edit', compact('page', 'payroll', 'data', 'employees', 'curmonth'));
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
        $payroll = Payroll::find(decrypt($id));
        $payroll->note = $request->note;
        $payroll->bonuses = $request->bonuses;
        $payroll->recovery = $request->recovery;
        $payroll->save();

        $mpayroll = MPayroll::find($payroll->m_payroll_id);
        if (!is_null($mpayroll)) {
            $company = Company::find($mpayroll->company_id);
            $allpayrolls = Payroll::where('m_payroll_id', $mpayroll->id)->get();
            $total_basic_salary = 0;
            $total_transport = 0;
            $total_house_allowance = 0;
            $total_com_allowance = 0;
            $total_overtime = 0;
            $total_bonuses = 0;
            $total_gross_other_earnings = 0;
            $total_gross_pay = 0;
            $total_ssf = 0;
            $total_taxable_income = 0;
            $total_paye = 0;
            $total_net_pay = 0;
            $total_mif = 0;
            $total_wcf = 0;
            $total_heslb = 0;
            $total_advance_salary = 0;
            $total_emp_loan = 0;
            $total_attendance_deduction = 0;
            $total_recovery = 0;
            $total_adjusted_net_pay = 0;
            $payrolls = array();
            foreach ($allpayrolls as $key => $payroll) {
                $employee = Employee::find($payroll->employee_id);
                 //Earnings
                $hourly = 0;
                $monthly = 0;
                $basic_salary = 0;
                $trans_allowance = 0;
                $com_allowance = 0;
                $house_allowance = 0;
                $overtime = 0;
                $bonuses = 0;

                if ($employee->is_paid_monthly) {
                    $monthly = $employee->basic_pay_monthly;
                    $basic_salary = $monthly;
                    $hourly = ($monthly/$payroll->days_work)/8;
                    $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                    $trans_allowance = $employee->trans_allowance;
                    $house_allowance = $employee->house_allowance;
                    $com_allowance = $employee->com_allowance;
                    $overtime = $overtime;
                    $bonuses = $payroll->bonuses;
                }else{    
                    $hourly = $employee->basic_pay_hourly;
                    $monthly = $hourly * 8 * $payroll->days_work;
                    $basic_salary = $monthly;
                    $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                    $trans_allowance = $employee->trans_allowance;
                    $house_allowance = $employee->house_allowance;
                    $com_allowance = $employee->com_allowance;
                    $overtime = $overtime;
                    $bonuses = $payroll->bonuses; 
                }

                $gross_other_earnings = $trans_allowance+$house_allowance+$com_allowance+$overtime+$bonuses;
                $gross_pay = $basic_salary+$gross_other_earnings;

                $sscheme = PayrollSetting::where('company_id', $company->id)->where('name', $employee->ssf)->first();
                $hisscheme = PayrollSetting::where('company_id', $company->id)->where('name', $employee->mif)->first();
                $ps_wcf = PayrollSetting::where('company_id', $company->id)->where('name', 'WCF')->first();

                //Deductions
                $ssf = 0;
                if ($employee->is_reg_ssf) {       
                    if (!is_null($sscheme)) {
                        $ssf = round($gross_pay * $sscheme->percent_rate/100);
                    }
                }

                $taxable_income = $gross_pay-$ssf;

                $payevalue = 0;
                $paygrp = PayrollSetting::where('company_id', Session::get('company_id'))->where('min_income', '<=', $taxable_income)->where('max_income', '>=', $taxable_income)->first();
                if(!is_null($paygrp)){
                    $payevalue = $paygrp->fixed_paye_value+(($taxable_income-$paygrp->min_income)*$paygrp->percent_rate/100);

                }

                $net_pay = $taxable_income-$payevalue;

                $mif = 0;
                if ($employee->is_reg_mif) {       
                    if (!is_null($hisscheme)) {
                        $mif = round($gross_pay * $hisscheme->percent_rate/100);
                    }
                }

                $wcf = 0;
                if ($employee->is_reg_wcf) {       
                    if (!is_null($ps_wcf)) {
                        $wcf = round($gross_pay * $ps_wcf->percent_rate/100);
                    }
                }
                $heslb = 0;
                if ($employee->allow_deduct_heslb) {
                    $ps_heslb = PayrollSetting::where('company_id', $company->id)->where('name', 'HESLB')->first();
                    if (!is_null($ps_heslb)) {
                        $heslb = round($net_pay * $ps_heslb->percent_rate/100);
                    }
                }

                $emploan_amount = 0;
                $emploan = EmployeeLoan::where('employee_id', $employee->id)->whereRaw('amount > amount_paid')->first();
                if (!is_null($emploan)) {
                    $emploan_amount = round($emploan->amount*($emploan->return_rate/100), 2);
                }

                $advance_salary = 0;
                $empadvancesalary = AdvanceSalary::where('employee_id', $employee->id)->whereRaw('amount > amount_paid')->first();
                if (!is_null($empadvancesalary)) {
                    $advance_salary = $empadvancesalary->amount-$empadvancesalary->amount_paid;
                }

                $per_day = $hourly * 8;
                $late = $payroll->late;
                $absent = $payroll->absences;
                $late_perpay = $hourly / 60;
                $late_overall = $late_perpay * $late;
                $absent_overall = $hourly * 8 * $absent;

                $attendance_deduction = $late_overall+$absent_overall;

                $recovery = $payroll->recovery;

                $other_deduction = $advance_salary+$emploan_amount+$late_overall+$absent_overall+$recovery;

                $adjusted_net_pay = $net_pay-($heslb+$other_deduction);

                $total_basic_salary += $basic_salary;
                $total_transport += $trans_allowance;                
                $total_house_allowance += $house_allowance;
                $total_com_allowance += $com_allowance;
                $total_overtime += $overtime;
                $total_bonuses += $bonuses;
                $total_gross_other_earnings += $gross_other_earnings;
                $total_gross_pay += $gross_pay;
                $total_ssf += $ssf;
                $total_taxable_income += $taxable_income;
                $total_paye += $payevalue;
                $total_net_pay += $net_pay;
                $total_mif += $mif;
                $total_wcf += $wcf;
                $total_heslb += $heslb;
                $total_advance_salary += $advance_salary;
                $total_emp_loan += $emploan_amount;
                $total_attendance_deduction += $attendance_deduction;
                $total_recovery += $recovery;
                $total_adjusted_net_pay += $adjusted_net_pay;
            }

            $other_deductions = $total_advance_salary+$total_emp_loan+$total_attendance_deduction+$total_recovery;

            $mpayroll->basic_salaries = $total_basic_salary;
            $mpayroll->house_allowance = $total_house_allowance;
            $mpayroll->trans_allowance = $total_transport;
            $mpayroll->com_allowance += $total_com_allowance;
            $mpayroll->overtime = $total_overtime;
            $mpayroll->bonuses = $total_bonuses;
            $mpayroll->paye = $total_paye;
            $mpayroll->ssf = $total_ssf;
            $mpayroll->wcf = $total_wcf;
            $mpayroll->heslb = $total_heslb;
            $mpayroll->other_deductions = $other_deductions;
            $mpayroll->save();
            return redirect('payrolls')->with('success', 'Payroll updated successfully');
        }else{
            return redirect('payrolls')->with('error', 'Payroll data not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payroll = Payroll::find(decrypt($id));
        if (!is_null($payroll)) {
            $payroll->delete();
        }

        return redirect('payrolls')->with('success', 'Payroll was deleted successfully');
    }

    public function getUniqueID()
    {
        return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, 6);
    }

    public function viewPayroll($id)
    {
        $page = 'Payroll Report';
        $title = 'Payroll Report';
        $company = Company::find(Session::get('company_id'));
        $mpayroll = MPayroll::find(decrypt($id));
        if (!is_null($mpayroll)) {
                
            $allpayrolls = Payroll::where('m_payroll_id', $mpayroll->id)->get();
            $total_basic_salary = 0;
            $total_transport = 0;
            $total_house_allowance = 0;
            $total_com_allowance = 0;
            $total_overtime = 0;
            $total_bonuses = 0;
            $total_gross_other_earnings = 0;
            $total_gross_pay = 0;
            $total_ssf = 0;
            $total_taxable_income = 0;
            $total_paye = 0;
            $total_net_pay = 0;
            $total_mif = 0;
            $total_wcf = 0;
            $total_heslb = 0;
            $total_advance_salary = 0;
            $total_emp_loan = 0;
            $total_attendance_deduction = 0;
            $total_recovery = 0;
            $total_adjusted_net_pay = 0;
            $payrolls = array();
            foreach ($allpayrolls as $key => $payroll) {
                $employee = Employee::find($payroll->employee_id);
                 //Earnings
                $hourly = 0;
                $monthly = 0;
                $basic_salary = 0;
                $trans_allowance = 0;
                $com_allowance = 0;
                $house_allowance = 0;
                $overtime = 0;
                $bonuses = 0;

                if ($employee->is_paid_monthly) {
                    $monthly = $employee->basic_pay_monthly;
                    $basic_salary = $monthly;
                    $hourly = ($monthly/$payroll->days_work)/8;
                    $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                    $trans_allowance = $employee->trans_allowance;
                    $house_allowance = $employee->house_allowance;
                    $com_allowance = $employee->com_allowance;
                    $overtime = $overtime;
                    $bonuses = $payroll->bonuses;
                }else{    
                    $hourly = $employee->basic_pay_hourly;
                    $monthly = $hourly * 8 * $payroll->days_work;
                    $basic_salary = $monthly;
                    $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                    $trans_allowance = $employee->trans_allowance;
                    $house_allowance = $employee->house_allowance;
                    $com_allowance = $employee->com_allowance;
                    $overtime = $overtime;
                    $bonuses = $payroll->bonuses; 
                }

                $gross_other_earnings = $trans_allowance+$house_allowance+$com_allowance+$overtime+$bonuses;
                $gross_pay = $basic_salary+$gross_other_earnings;

                $sscheme = PayrollSetting::where('company_id', $company->id)->where('name', $employee->ssf)->first();
                $hisscheme = PayrollSetting::where('company_id', $company->id)->where('name', $employee->mif)->first();
                $ps_wcf = PayrollSetting::where('company_id', $company->id)->where('name', 'WCF')->first();

                //Deductions
                $ssf = 0;
                if ($employee->is_reg_ssf) {       
                    if (!is_null($sscheme)) {
                        $ssf = round($gross_pay * $sscheme->percent_rate/100);
                    }
                }

                $taxable_income = $gross_pay-$ssf;

                $payevalue = 0;
                $paygrp = PayrollSetting::where('company_id', Session::get('company_id'))->where('min_income', '<=', $taxable_income)->where('max_income', '>=', $taxable_income)->first();
                if(!is_null($paygrp)){
                    $payevalue = $paygrp->fixed_paye_value+(($taxable_income-$paygrp->min_income)*$paygrp->percent_rate/100);

                }

                $net_pay = $taxable_income-$payevalue;

                $mif = 0;
                if ($employee->is_reg_mif) {       
                    if (!is_null($hisscheme)) {
                        $mif = round($gross_pay * $hisscheme->percent_rate/100);
                    }
                }

                $wcf = 0;
                if ($employee->is_reg_wcf) {       
                    if (!is_null($ps_wcf)) {
                        $wcf = round($gross_pay * $ps_wcf->percent_rate/100);
                    }
                }
                $heslb = 0;
                if ($employee->allow_deduct_heslb) {
                    $ps_heslb = PayrollSetting::where('company_id', $company->id)->where('name', 'HESLB')->first();
                    if (!is_null($ps_heslb)) {
                        $heslb = round($net_pay * $ps_heslb->percent_rate/100);
                    }
                }

                $emploan_amount = 0;
                $emploan = EmployeeLoan::where('employee_id', $employee->id)->whereRaw('amount > amount_paid')->first();
                if (!is_null($emploan)) {
                    $emploan_amount = round($emploan->amount*($emploan->return_rate/100), 2);
                }

                $advance_salary = 0;
                $empadvancesalary = AdvanceSalary::where('employee_id', $employee->id)->whereRaw('amount > amount_paid')->first();
                if (!is_null($empadvancesalary)) {
                    $advance_salary = $empadvancesalary->amount-$empadvancesalary->amount_paid;
                }

                $per_day = $hourly * 8;
                $late = $payroll->late;
                $absent = $payroll->absences;
                $late_perpay = $hourly / 60;
                $late_overall = $late_perpay * $late;
                $absent_overall = $hourly * 8 * $absent;

                $attendance_deduction = $late_overall+$absent_overall;

                $recovery = $payroll->recovery;

                $other_deduction = $advance_salary+$emploan_amount+$late_overall+$absent_overall+$recovery;

                $adjusted_net_pay = $net_pay-($heslb+$other_deduction);

                array_push($payrolls, ['name' => $employee->fname.' '.$employee->mname.' '.$employee->lname, 'emp_id' => $employee->emp_id, 'ssf_no' => $employee->ssf_no, 'tin' => $employee->tin, 'basic_salary' => $basic_salary, 'transport_allowance' => $trans_allowance, 'house_allowance' => $house_allowance, 'com_allowance' => $com_allowance, 'overtime' => $overtime, 'bonuses' => $bonuses, 'gross_other_earnings' => $gross_other_earnings, 'gross_pay' => $gross_pay, 'ssf' => $ssf, 'taxable_income' => $taxable_income, 'paye' => $payevalue, 'net_pay' => $net_pay, 'heslb' => $heslb, 'advance_salary' => $advance_salary, 'emploan_amount' => $emploan_amount, 'attendance_deduction' => $attendance_deduction, 'recovery' => $recovery, 'adjusted_net_pay' => $adjusted_net_pay, 'bank_acc' => $employee->account_number, 'bank_name' => $employee->bank_name]);

                // asort($payrolls);

                $total_basic_salary += $basic_salary;
                $total_transport += $trans_allowance;                
                $total_house_allowance += $house_allowance;
                $total_com_allowance += $com_allowance;
                $total_overtime += $overtime;
                $total_bonuses += $bonuses;
                $total_gross_other_earnings += $gross_other_earnings;
                $total_gross_pay += $gross_pay;
                $total_ssf += $ssf;
                $total_taxable_income += $taxable_income;
                $total_paye += $payevalue;
                $total_net_pay += $net_pay;
                $total_mif += $mif;
                $total_wcf += $wcf;
                $total_heslb += $heslb;
                $total_advance_salary += $advance_salary;
                $total_emp_loan += $emploan_amount;
                $total_attendance_deduction += $attendance_deduction;
                $total_recovery += $recovery;
                $total_adjusted_net_pay += $adjusted_net_pay;
            }

            $user = User::find($mpayroll->user_id);
            return view('payrolls.preview-payroll', compact('page', 'title', 'company', 'mpayroll', 'payrolls', 'total_basic_salary', 'total_transport', 'total_house_allowance', 'total_com_allowance', 'total_overtime', 'total_bonuses', 'total_gross_other_earnings', 'total_gross_pay', 'total_ssf', 'total_taxable_income', 'total_paye', 'total_net_pay', 'total_heslb', 'total_advance_salary', 'total_emp_loan', 'total_attendance_deduction', 'total_recovery', 'total_adjusted_net_pay', 'total_mif', 'total_wcf', 'user'));
        }else{
            return redirect('payrolls');
        }
    }

    public function deletePayroll($id)
    {
        $mpayroll = MPayroll::find(decrypt($id));
        if (!is_null($mpayroll)) {
            $payrolls = Payroll::where('m_payroll_id', $mpayroll->id)->get();
            foreach ($payrolls as $key => $value) {
                $value->delete();
            }

            $mpayroll->delete();
        }

        return redirect('payrolls')->with('success', 'Payroll deleted successfully');
    }

    public function allPayrolls(Request $request)
    {
        $page = 'Payroll list Preview';
        $title = 'Payroll list Preview';
        $company = Company::find(Session::get('company_id'));
        $data = array();
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::today()->startOfMonth()->subMonth($i);
            $year = \Carbon\Carbon::today()->startOfMonth()->subMonth($i)->format('Y');
            array_push($data, array(
                'month' => $month->monthName,
                'year' => $year
            ));
        }

        $m = Carbon::today()->startOfMonth();
        $y = Carbon::today()->startOfMonth()->format('Y');
        $curmonth = $m->monthName.' '.$y;
        if (!is_null($request['month'])) {
            $curmonth = $request['month'];
        }

        $allpayroll = MPayroll::where('company_id', $company->id)->where('m_payrolls.month', $curmonth)->join('payrolls', 'payrolls.m_payroll_id', '=', 'm_payrolls.id')->get();
        $total_gross_income = 0;
        $total_deductions = 0;
        $total_net_pay = 0;
        $payrolls = array();
        foreach ($allpayroll as $key => $payroll) {
            $employee = Employee::find($payroll->employee_id);
            $position = 'Not Assinged';
            $emppos = Position::find($employee->position_id);
            if (!is_null($emppos)) {
                $position = $emppos->name;
            };
             //Earnings
            $gross_income = 0;
            $hourly = 0;
            $monthly = 0;
            $overtime = 0;
            if ($employee->is_paid_monthly) {
                $monthly = $position->basic_pay_monthly;
                $hourly = ($monthly/$payroll->days_work)/8;
                $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                $gross_income = $position->basic_pay_monthly + $overtime + $payroll->bonuses;
                
            }else{    

                $hourly = $position->basic_pay_hourly;
                $monthly = $hourly * 8 * $payroll->days_work;
                $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                $gross_income = $monthly + $overtime + $payroll->bonuses;
                
            }

            //Deductions
            $per_day = $hourly * 8;
            $late = $payroll->late;
            $absent = $payroll->absences;
            $late_perpay = $hourly / 60;
            $late_overall = $late_perpay * $late;
            $absent_overall = $hourly * 8 * $absent;
            $ssf = 0;
            $sscheme = PayrollSetting::where('company_id', $company->id)->where('name', $employee->ssf)->first();
            if (!is_null($sscheme)) {
                $ssf = round($gross_income * $sscheme->percent_rate/100);
            }

            $wcf = 0;
            $ps_wcf = PayrollSetting::where('company_id', $company->id)->where('name', 'WCF')->first();
            if (!is_null($ps_wcf)) {
                $wcf = round($gross_income * $ps_wcf->percent_rate/100);
            }

            $heslb = 0;
            $ps_heslb = PayrollSetting::where('company_id', $company->id)->where('name', 'HESLB')->first();
            if (!is_null($ps_heslb)) {
                $heslb = round($gross_income * $ps_heslb->percent_rate/100);
            }
            
            $dect_before_paye = $ssf+$heslb+$late_overall+$absent_overall;
            $total_ern = $gross_income-$dect_before_paye;

            $payevalue = 0;
            $paygrp = PayrollSetting::where('company_id', Session::get('company_id'))->where('min_income', '<=', $total_ern)->where('max_income', '>=', $total_ern)->first();
            if(!is_null($paygrp)){
                $payevalue = $paygrp->fixed_paye_value+(($total_ern-$paygrp->min_income)*$paygrp->percent_rate/100);

            }

            $net_pay = $total_ern-$payevalue;
            $deduction = $dect_before_paye+$payevalue;

            array_push($payrolls, ['payid' => $payroll->payid, 'name' => $employee->fname.' '.$employee->lname, 'position' => $position, 'gross_income' => $gross_income, 'deduction' => $deduction, 'net_pay' => $net_pay]);

            $total_gross_income += $gross_income;
            $total_deductions += $deduction;
            $total_net_pay += $net_pay; 
        }

        $expdate = null;
        $expd = Carbon::createFromFormat('M Y', $curmonth) ->endOfMonth();
        $now = Carbon::now();
        $result = $now->gt($expd);
        if ($result) {
            $expdate = $expd->format('Y-m-d');
        }else{
            $expdate = $now->format('Y-m-d');
        }
        $curmonthsalary = Expense::where('company_id', $company->id)->where('item', 'SALARIES')->where('date', $expdate)->first();

        return view('payrolls.list', compact('page', 'settings', 'data', 'payrolls', 'total_gross_income', 'total_deductions', 'total_net_pay', 'curmonth', 'curmonthsalary'));
    }

    public function reports(Request $request)
    {   
        $page = 'Payrolls Summary Report';
        $company = Company::find(Session::get('company_id'));
        //check if user opted for date range
        $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->endOfMonth();
        $is_post_query = false;
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');
        
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From '.date('d M Y', strtotime($start_date)).' To '.date('d M Y', strtotime($end_date));

        $mpayrolls = MPayroll::where('company_id', $company->id)->whereBetween('month', [$start, $end])->get();
        
        $payrolls = array();
        $total_gross_income = 0;
        $total_paye = 0;
        $total_ssf = 0;
        $total_mif = 0;
        $total_wcf = 0;
        $total_heslb = 0;
        $total_emp_loan = 0;
        $total_net_pay = 0;
        foreach ($mpayrolls as $key => $mpayroll) {
            $allpayrolls = Payroll::where('m_payroll_id', $mpayroll->id)->join('employees', 'employees.id', '=', 'payrolls.employee_id')->get();
            $m_gross_income = 0;
            $m_paye = 0;
            $m_ssf = 0;
            $m_mif = 0;
            $m_wcf = 0;
            $m_heslb = 0;
            $m_emp_loan = 0;
            $m_net_pay = 0;
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

                $sscheme = PayrollSetting::where('company_id', $company->id)->where('name', $employee->ssf)->first();
                $hisscheme = PayrollSetting::where('company_id', $company->id)->where('name', $employee->mif)->first();
                $ps_wcf = PayrollSetting::where('company_id', $company->id)->where('name', 'WCF')->first();
                //Deductions
                $per_day = $hourly * 8;
                $late = $payroll->late;
                $absent = $payroll->absences;
                $late_perpay = $hourly / 60;
                $late_overall = $late_perpay * $late;
                $absent_overall = $hourly * 8 * $absent;
                $ssf = 0;
                if ($employee->is_reg_ssf) {       
                    if (!is_null($sscheme)) {
                        $ssf = round($gross_income * $sscheme->percent_rate/100);
                    }
                }

                $mif = 0;
                if ($employee->is_reg_mif) {       
                    if (!is_null($hisscheme)) {
                        $mif = round($gross_income * $hisscheme->percent_rate/100);
                    }
                }
                $wcf = 0;
                if ($employee->is_reg_wcf) {       
                    if (!is_null($ps_wcf)) {
                        $wcf = round($gross_income * $ps_wcf->percent_rate/100);
                    }
                }
                $heslb = 0;
                if ($employee->allow_deduct_heslb) {
                    $ps_heslb = PayrollSetting::where('company_id', $company->id)->where('name', 'HESLB')->first();
                    if (!is_null($ps_heslb)) {
                        $heslb = round($gross_income * $ps_heslb->percent_rate/100);
                    }
                }

                $emploan_amount = 0;
                $emploan = EmployeeLoan::where('employee_id', $employee->id)->whereRaw('amount > amount_paid')->first();
                if (!is_null($emploan)) {
                    $emploan_amount = round($emploan->amount*($emploan->return_rate/100), 2);
                }

                $dect_before_paye = $ssf+$mif+$heslb+$late_overall+$absent_overall;
                $total_ern = $gross_income-$dect_before_paye;

                $payevalue = 0;
                $paygrp = PayrollSetting::where('company_id', Session::get('company_id'))->where('min_income', '<=', $total_ern)->where('max_income', '>=', $total_ern)->first();
                if(!is_null($paygrp)){
                    $payevalue = $paygrp->fixed_paye_value+(($total_ern-$paygrp->min_income)*$paygrp->percent_rate/100);

                }

                $net_pay = $total_ern-$payevalue-$emploan_amount;
                $deduction = $dect_before_paye+$payevalue;
                $position = 'Not Assinged';
                $emppos = Position::find($employee->position_id);
                if (!is_null($emppos)) {
                    $position = $emppos->name;
                }

                $m_gross_income += $gross_income;
                $m_paye += $payevalue;
                $m_ssf += $ssf;
                $m_mif += $mif;
                $m_wcf += $wcf;
                $m_heslb += $heslb;
                $m_net_pay += $net_pay; 
                $m_emp_loan += $emploan_amount;
            }
            array_push($payrolls, ['month' => $mpayroll->month, 'gross_income' => $m_gross_income, 'paye' => $m_paye, 'ssf' => $m_ssf, 'mif' => $m_mif, 'wcf' => $m_wcf, 'heslb' => $m_heslb, 'nst_loan' => $m_emp_loan, 'net_pay' => $m_net_pay]);

            $total_gross_income += $m_gross_income;
            $total_paye += $m_paye;
            $total_ssf += $m_ssf;
            $total_mif += $m_mif;
            $total_wcf += $m_wcf;
            $total_heslb += $m_heslb;
            $total_emp_loan += $m_emp_loan;
            $total_net_pay += $m_net_pay;
        }

        return view('payrolls.reports', compact('page', 'company', 'payrolls', 'total_gross_income', 'total_paye', 'total_ssf', 'total_mif', 'total_wcf', 'total_heslb', 'total_emp_loan', 'total_net_pay', 'duration', 'is_post_query', 'start_date', 'end_date'));
    }
}