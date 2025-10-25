<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use App\Models\Setting;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\MPayroll;
use App\Models\PayrollSetting;
use App\Models\User;
use App\Models\Position;

class EmployeeSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $page = 'Employee Salaries';//check if user opted for date range
        $start_date = null;            
        $end_date = null;
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }elseif(!empty($request['month'])){
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
        if (!empty($request['month'])) {
            $curmonth = $request['month'];
        }
        $employees = Employee::get();

        $dbDate = Carbon::parse('2022-01-10');
        $diffMonths = Carbon::now()->diffInMonths($dbDate);
        $data = array();
        for ($i = $diffMonths; $i >= 0; $i--) {
            $month = Carbon::today()->startOfMonth()->subMonth($i);
            $year = Carbon::today()->startOfMonth()->subMonth($i)->format('Y');
            array_push($data, array(
                'month' => $month->monthName,
                'year' => $year
            ));
        }

        $settings = Setting::first();
        $mpayroll = MPayroll::whereBetween('month', [$start, $end])->first();
        if (!is_null($mpayroll)) {
                
            $allpayrolls = Payroll::where('m_payroll_id', $mpayroll->id)->get();
            $total_earns = 0;
            $total_deductions = 0;
            $total_net_pay = 0;
            $payrolls = array();
            foreach ($allpayrolls as $key => $payroll) {
                $employee = Employee::find($payroll->employee_id);
                 //Earnings
                $gross_income = 0;
                $hourly = 0;
                $monthly = 0;
                $overtime = 0;
                if ($employee->is_paid_monthly) {
                    $monthly = $employee->basic_pay_monthly;
                    $hourly = ($monthly/23)/8;
                    $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                    $gross_income = $monthly + $overtime + $payroll->bonuses+$employee->house_allowance+$employee->trans_allowance+$employee->com_allowance;
                }else{    
                    $hourly = $employee->basic_pay_hourly;
                    $monthly = $hourly * 8 * $payroll->days_work;
                    $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
                    $gross_income = $monthly + $overtime + $payroll->bonuses+$employee->house_allowance+$employee->trans_allowance+$employee->com_allowance;;  
                }

                $sscheme = PayrollSetting::where('name', $employee->ssf)->first();
                $hisscheme = PayrollSetting::where('name', $employee->mif)->first();
                $ps_wcf = PayrollSetting::where('name', 'WCF')->first();
                //Deductions
                $per_day = $hourly * 8;
                $late = $payroll->late;
                $absent = $payroll->absences;
                $late_perpay = $hourly / 60;
                $late_overall = $late_perpay * $late;
                $absent_overall = $hourly * 8 * $absent;
                $ssf = 0;
                if (!is_null($sscheme)) {
                    $ssf = round($gross_income * $sscheme->percent_rate/100);
                }
                $mif = 0;
                if (!is_null($hisscheme)) {
                    $mif = round($gross_income * $hisscheme->percent_rate/100);
                }
                $wcf = 0;
                if (!is_null($ps_wcf)) {
                    $wcf = round($gross_income * $ps_wcf->percent_rate/100);
                }
                $heslb = 0;
                if ($employee->pay_heslb) {
                    $ps_heslb = PayrollSetting::where('name', 'HESLB')->first();
                    if (!is_null($ps_heslb)) {
                        $heslb = round($gross_income * $ps_heslb->percent_rate/100);
                    }
                }

                $dect_before_paye = $ssf+$mif+$heslb+$wcf+$late_overall+$absent_overall;
                $total_ern = $gross_income-$dect_before_paye;

                $paygrpvalue = 0;
                $b_paygrpvalue = 0;
                $b1_paygrpvalue = 0;
                $b2_paygrpvalue = 0;
                $b3_paygrpvalue = 0;
                $paygrp = PayrollSetting::where('min_income', '<=', $total_ern)->where('max_income', '>=', $total_ern)->first();

                if(!is_null($paygrp)){
                    $paygrpvalue = ($total_ern-$paygrp->min_income)*$paygrp->percent_rate/100;
                    $b_paygrp = PayrollSetting::where('max_income', $paygrp->min_income)->first();
                    if (!is_null($b_paygrp)) {
                        $b_paygrpvalue = ($b_paygrp->max_income-$b_paygrp->min_income)*$b_paygrp->percent_rate/100;
                        $b1_paygrp  = PayrollSetting::where('max_income', $b_paygrp->min_income)->first();
                        if (!is_null($b1_paygrp)) {
                            $b1_paygrpvalue = ($b1_paygrp->max_income-$b1_paygrp->min_income)*$b1_paygrp->percent_rate/100;
                            $b2_paygrp  = PayrollSetting::where('max_income', $b1_paygrp->min_income)->first();
                            if (!is_null($b2_paygrp)) {
                                $b2_paygrpvalue = ($b2_paygrp->max_income-$b2_paygrp->min_income)*$b2_paygrp->percent_rate/100;
                                $b3_paygrp  = PayrollSetting::where('max_income', $b2_paygrp->min_income)->first();
                                if (!is_null($b3_paygrp)) {
                                    $b3_paygrpvalue = ($b3_paygrp->max_income-$b3_paygrp->min_income)*$b3_paygrp->percent_rate/100;
                                }
                            }
                        }
                    }
                }
                $payevalue = $paygrpvalue+$b_paygrpvalue+$b1_paygrpvalue+$b2_paygrpvalue+$b3_paygrpvalue;

                $net_pay = $total_ern-$payevalue;
                $deduction = $dect_before_paye+$payevalue;
                $position = 'Not Assinged';
                $emppos = Position::find($employee->position_id);
                if (!is_null($emppos)) {
                    $position = $emppos->name;
                }
                array_push($payrolls, ['id' => $payroll->id, 'emp_id' => $employee->id_no, 'name' => $employee->fname.' '.$employee->lname, 'email' => $employee->email, 'join_date' => $employee->start_date, 'mobile' => $employee->mobile, 'position' => $position, 'gross_income' => $gross_income, 'deduction' => $deduction, 'net_pay' => $net_pay]);

                $total_earns += $gross_income;
                $total_deductions += $deduction;
                $total_net_pay += $net_pay; 
            }
            
            return view('hr.employees.salary.index', compact('page', 'data', 'curmonth', 'employees', 'payrolls', 'total_earns', 'total_deductions', 'total_net_pay'));
        }else{
            return redirect()->back()->with('info', 'No Payrolls for the month '.$request['month']);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    
        $settings = Setting::first();
        $payroll = Payroll::find(decrypt($id));
        $employee = Employee::find($payroll->employee_id);
        $position = 'Not Assinged';
        $emppos = Position::find($employee->position_id);
        if (!is_null($emppos)) {
            $position = $emppos->name;
        }
         //Earnings
        $gross_income = 0;
        $hourly = 0;
        $monthly = 0;
        $overtime = 0;
        $hra = $employee->house_allowance;
        $ta = $employee->trans_allowance;
        $com_allowance = $employee->com_allowance;
        if ($employee->is_paid_monthly) {
            $monthly = $employee->basic_pay_monthly;
            $hourly = ($monthly/$payroll->days_work)/8;
            $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
            $gross_income = $employee->basic_pay_monthly + $overtime + $payroll->bonuses+$hra+$ta+$com_allowance;
            
        }else{    
            $hourly = $employee->basic_pay_hourly;
            $monthly = $hourly * 8 * $payroll->days_work;
            $overtime = (($hourly * 0.5) + $hourly ) * $payroll->overtime_hrs;
            $gross_income = $monthly + $overtime + $payroll->bonuses+$hra+$ta+$com_allowance;
            
        }

        $sscheme = PayrollSetting::where('name', $employee->ssf)->first();
        $hisscheme = PayrollSetting::where('name', $employee->mif)->first();
        $ps_wcf = PayrollSetting::where('name', 'WCF')->first();
        //Deductions
        $per_day = $hourly * 8;
        $late = $payroll->late;
        $absent = $payroll->absences;
        $late_perpay = $hourly / 60;
        $late_overall = $late_perpay * $late;
        $absent_overall = $hourly * 8 * $absent;
        $ssf = 0;
        if (!is_null($sscheme)) {
            $ssf = round($gross_income * $sscheme->percent_rate/100);
        }
        $mif = 0;
        if (!is_null($hisscheme)) {
            $mif = round($gross_income * $hisscheme->percent_rate/100);
        }
        $wcf = 0;
        if (!is_null($ps_wcf)) {
            $wcf = round($gross_income * $ps_wcf->percent_rate/100);
        }            
        $ps_heslb = null;
        $heslb = 0;
        if ($employee->pay_heslb) {
            $ps_heslb = PayrollSetting::where('name', 'HESLB')->first();
            if (!is_null($ps_heslb)) {
                $heslb = round($gross_income * $ps_heslb->percent_rate/100);
            }
        }

        $dect_before_paye = $ssf+$mif+$wcf+$heslb+$late_overall+$absent_overall;
        $total_ern = $gross_income-$dect_before_paye;

        $paygrpvalue = 0;
        $b_paygrpvalue = 0;
        $b1_paygrpvalue = 0;
        $b2_paygrpvalue = 0;
        $b3_paygrpvalue = 0;
        $paygrp = PayrollSetting::where('min_income', '<=', $total_ern)->where('max_income', '>=', $total_ern)->first();

        if(!is_null($paygrp)){
            $paygrpvalue = ($total_ern-$paygrp->min_income)*$paygrp->percent_rate/100;
            $b_paygrp = PayrollSetting::where('max_income', $paygrp->min_income)->first();
            if (!is_null($b_paygrp)) {
                $b_paygrpvalue = ($b_paygrp->max_income-$b_paygrp->min_income)*$b_paygrp->percent_rate/100;
                $b1_paygrp  = PayrollSetting::where('max_income', $b_paygrp->min_income)->first();
                if (!is_null($b1_paygrp)) {
                    $b1_paygrpvalue = ($b1_paygrp->max_income-$b1_paygrp->min_income)*$b1_paygrp->percent_rate/100;
                    $b2_paygrp  = PayrollSetting::where('max_income', $b1_paygrp->min_income)->first();
                    if (!is_null($b2_paygrp)) {
                        $b2_paygrpvalue = ($b2_paygrp->max_income-$b2_paygrp->min_income)*$b2_paygrp->percent_rate/100;
                        $b3_paygrp  = PayrollSetting::where('max_income', $b2_paygrp->min_income)->first();
                        if (!is_null($b3_paygrp)) {
                            $b3_paygrpvalue = ($b3_paygrp->max_income-$b3_paygrp->min_income)*$b3_paygrp->percent_rate/100;
                        }
                    }
                }
            }
        }
        $payevalue = $paygrpvalue+$b_paygrpvalue+$b1_paygrpvalue+$b2_paygrpvalue+$b3_paygrpvalue;
        $net_pay = $total_ern-$payevalue;
        $total_deduction = $dect_before_paye+$payevalue;

        return view('hr.employees.salary.show', compact('page', 'settings', 'employee', 'position', 'payroll', 'monthly', 'hra', 'ta', 'com_allowance', 'gross_income', 'overtime', 'payevalue', 'total_deduction', 'net_pay', 'sscheme', 'ssf', 'ps_wcf', 'hisscheme', 'mif', 'wcf', 'ps_heslb', 'heslb', 'late_overall', 'absent_overall'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
