<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\Shop;
use App\Models\PayrollSetting;
use App\Models\MPayroll;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Position;
use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanReturn;
use App\Models\ExpenseCategory;
use App\Models\ExpenseTemp;
use App\Models\ExpenseItem;
use App\Models\TransactionAccount;
use App\Models\Supplier;
use App\Models\Expense;
use App\Models\ExpSupplierTransaction;
use App\Models\PayrollDeduction;

class PayrollToExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $page = 'Add To Expenses';
        $company = Company::find(Session::get('company_id'));
        $shop = $company->shops()->where('is_hq', true)->first();
        $mpayroll = MPayroll::find(decrypt($id));
        if (!is_null($mpayroll)) {
                
            $allpayrolls = Payroll::where('m_payroll_id', $mpayroll->id)->join('employees', 'employees.id', '=', 'payrolls.employee_id')->get();
            $total_gross_income = 0;
            $total_paye = 0;
            $total_ssf = 0;
            $ssf_name = '';
            $total_mif = 0;
            $mif_name = '';
            $total_wcf = 0;
            $total_heslb = 0;
            $total_emp_loan = 0;
            $total_net_pay = 0;
            $other_deductions = 0;
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
                        $ssf_name = $sscheme->name;
                        $ssf = round($gross_income * $sscheme->percent_rate/100);
                    }
                }

                $mif = 0;
                if ($employee->is_reg_mif) {       
                    if (!is_null($hisscheme)) {
                        $mif_name = $hisscheme->name;
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

                    $emploan_return = new EmployeeLoanReturn();
                    $emploan_return->user_id = Auth::user()->id;
                    $emploan_return->employee_loan_id = $emploan->id;
                    $emploan_return->employee_id = $employee->id;
                    $emploan_return->return_date = $mpayroll->month;
                    $emploan_return->amount = $emploan_amount;
                    $emploan_return->save();

                    $emploan->amount_paid = $emploan->amount_paid+$emploan_amount;
                    if ($emploan->amount == $emploan->amount_paid) {
                        $emploan->status = 'Completed';
                    }
                    $emploan->save();
                }

                $penalty = $payroll->penalty;

                $dect_before_paye = $ssf+$mif+$heslb+$late_overall+$absent_overall;
                $total_ern = $gross_income-$dect_before_paye;


                $paygrpvalue = 0;
                $b_paygrpvalue = 0;
                $b1_paygrpvalue = 0;
                $b2_paygrpvalue = 0;
                $b3_paygrpvalue = 0;
                $paygrp = PayrollSetting::where('company_id', $company->id)->where('min_income', '<=', $total_ern)->where('max_income', '>=', $total_ern)->first();

                if(!is_null($paygrp)){
                    $paygrpvalue = ($total_ern-$paygrp->min_income)*$paygrp->percent_rate/100;
                    $b_paygrp = PayrollSetting::where('company_id', $company->id)->where('max_income', $paygrp->min_income)->first();
                    if (!is_null($b_paygrp)) {
                        $b_paygrpvalue = ($b_paygrp->max_income-$b_paygrp->min_income)*$b_paygrp->percent_rate/100;
                        $b1_paygrp  = PayrollSetting::where('company_id', $company->id)->where('max_income', $b_paygrp->min_income)->first();
                        if (!is_null($b1_paygrp)) {
                            $b1_paygrpvalue = ($b1_paygrp->max_income-$b1_paygrp->min_income)*$b1_paygrp->percent_rate/100;
                            $b2_paygrp  = PayrollSetting::where('company_id', $company->id)->where('max_income', $b1_paygrp->min_income)->first();
                            if (!is_null($b2_paygrp)) {
                                $b2_paygrpvalue = ($b2_paygrp->max_income-$b2_paygrp->min_income)*$b2_paygrp->percent_rate/100;
                                $b3_paygrp  = PayrollSetting::where('company_id', $company->id)->where('max_income', $b2_paygrp->min_income)->first();
                                if (!is_null($b3_paygrp)) {
                                    $b3_paygrpvalue = ($b3_paygrp->max_income-$b3_paygrp->min_income)*$b3_paygrp->percent_rate/100;
                                }
                            }
                        }
                    }
                }
                $payevalue = $paygrpvalue+$b_paygrpvalue+$b1_paygrpvalue+$b2_paygrpvalue+$b3_paygrpvalue;
                $net_pay = $total_ern-$payevalue-$emploan_amount-$penalty;
                $deduction = $dect_before_paye+$payevalue;
                $position = 'Not Assinged';
                $emppos = Position::find($employee->position_id);
                if (!is_null($emppos)) {
                    $position = $emppos->name;
                }
                array_push($payrolls, ['payid' => $payroll->payid, 'name' => $payroll->fname.' '.$payroll->lname, 'position' => $position, 'gross_income' => $gross_income, 'paye' => $payevalue, 'ssf' => $ssf, 'mif' => $mif, 'wcf' => $wcf, 'heslb' => $heslb, 'nst_loan' => $emploan_amount, 'penalty' => $penalty, 'net_pay' => $net_pay]);

                $total_gross_income += $gross_income;
                $total_paye += $payevalue;
                $total_ssf += $ssf;
                $total_mif += $mif;
                $total_wcf += $wcf;
                $total_heslb += $heslb;
                $total_net_pay += $net_pay; 
                $total_emp_loan += $emploan_amount;
                $other_deductions += $penalty;
            }

            return $this->store($company, $shop, $mpayroll, $total_gross_income, $total_paye, $total_ssf, $ssf_name, $total_mif, $mif_name, $total_wcf, $total_heslb, $other_deductions);
        }else{
            return redirect('payrolls');
        }
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($company, $shop, $mpayroll, $total_gross_income, $total_paye, $total_ssf, $ssf_name, $total_mif, $mif_name, $total_wcf, $total_heslb, $other_deductions)
    {
        $salarycategory = ExpenseCategory::where('shop_id', $shop->id)->where('name', 'SALARIES & WAGES')->first();
        if (is_null($salarycategory)) {
            $transacc = TransactionAccount::where('company_id', $company->id)->where('account_number', 5030)->first();
            $salarycategory = new ExpenseCategory();
            $salarycategory->shop_id = $shop->id;
            $salarycategory->transaction_account_id = $transacc->id;
            $salarycategory->name = 'SALARIES & WAGES';
            $salarycategory->save();
        }

        $expitem = ExpenseItem::where('shop_id', $shop->id)->where('expense_type', 'Salaries')->first();
        if (is_null($expitem)) {
            $expitem = new ExpenseItem();
            $expitem->shop_id = $shop->id;
            $expitem->expense_category_id = $salarycategory->id;
            $expitem->expense_type = 'Salaries';
            $expitem->save();
        }

        $expense = Expense::where('shop_id', $shop->id)->where('m_payroll_id', $mpayroll->id)->first();
        if (is_null($expense)) {
            $supplier = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Expense')->first();

            $now = Carbon::now();
            $time = date('H:i:s', strtotime($now));
            $expdate = $mpayroll->month.' ' . $time;

            $acctrans = new ExpSupplierTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = Auth::user()->id;
            $acctrans->supplier_id = $supplier->id;
            $acctrans->date = $expdate;
            $acctrans->save();

            $expense = new Expense();
            $expense->shop_id = $shop->id;
            $expense->user_id = Auth::user()->id;
            $expense->expense_category_id = $expitem->expense_category_id;
            $expense->expense_item_id = $expitem->id;
            $expense->expense_type = $expitem->expense_type;
            $expense->unit_cost = $total_gross_income;
            $expense->amount = $total_gross_income;
            $expense->qty = 1;
            $expense->description = 'Payroll for the month : '.date('M Y', strtotime($mpayroll->month));
            $expense->time_created = $expdate;
            $expense->exp_type = 'credit';
            $expense->status = 'Pending';
            $expense->supplier_id = $supplier->id;
            $expense->trans_id = $acctrans->id;
            $expense->save();
        }

        $mpayroll->is_added_to_expense = true;
        $mpayroll->save();


        if ($total_paye > 0) {
            $monthpaye = PayrollDeduction::where('shop_id', $shop->id)->where('m_payroll_id', $mpayroll->id)->where('name', 'PAYE')->first();
            if (is_null($monthpaye)) {
                $monthpaye = new PayrollDeduction();
                $monthpaye->shop_id = $shop->id;
                $monthpaye->user_id = Auth::user()->id;
                $monthpaye->m_payroll_id = $mpayroll->id;
                $monthpaye->date = $mpayroll->month;
                $monthpaye->name = 'PAYE';
                $monthpaye->amount = $total_paye;
                $monthpaye->save();
            }else{
                $monthpaye->date = $mpayroll->month;
                $monthpaye->amount = $total_paye;
                $monthpaye->save();
            }
        }

        if ($total_ssf > 0) {
            $monthssf = PayrollDeduction::where('shop_id', $shop->id)->where('m_payroll_id', $mpayroll->id)->where('name', $ssf_name)->first();
            if (is_null($monthssf)) {
                $monthssf = new PayrollDeduction();
                $monthssf->shop_id = $shop->id;
                $monthssf->user_id = Auth::user()->id;
                $monthssf->m_payroll_id = $mpayroll->id;
                $monthssf->date = $mpayroll->month;
                $monthssf->name = $ssf_name;
                $monthssf->amount = $total_ssf*2;
                $monthssf->save();
            }else{
                $monthssf->date = $mpayroll->month;
                $monthssf->amount = $total_ssf*2;
                $monthssf->save();
            }
        }

        if ($total_mif > 0) {
            $monthmif = PayrollDeduction::where('shop_id', $shop->id)->where('m_payroll_id', $mpayroll->id)->where('name', $mif_name)->first();
            if (is_null($monthmif)) {
                $monthmif = new PayrollDeduction();
                $monthmif->shop_id = $shop->id;
                $monthmif->user_id = Auth::user()->id;
                $monthmif->m_payroll_id = $mpayroll->id;
                $monthmif->date = $mpayroll->month;
                $monthmif->name = $mif_name;
                $monthmif->amount = $total_mif*2;
                $monthmif->save();
            }else{
                $monthmif->date = $mpayroll->month;
                $monthmif->amount = $total_mif*2;
                $monthmif->save();
            }
        }

        if ($total_wcf > 0) {
            $monthwcf = PayrollDeduction::where('shop_id', $shop->id)->where('m_payroll_id', $mpayroll->id)->where('name', 'WCF')->first();
            if (is_null($monthwcf)) {
                $monthwcf = new PayrollDeduction();
                $monthwcf->shop_id = $shop->id;
                $monthwcf->user_id = Auth::user()->id;
                $monthwcf->m_payroll_id = $mpayroll->id;
                $monthwcf->date = $mpayroll->month;
                $monthwcf->name = 'WCF';
                $monthwcf->amount = $total_wcf;
                $monthwcf->save();
            }else{
                $monthwcf->date = $mpayroll->month;
                $monthwcf->amount = $total_wcf;
                $monthwcf->save();
            }
        }

        if ($total_heslb > 0) {
            $monthheslb = PayrollDeduction::where('shop_id', $shop->id)->where('m_payroll_id', $mpayroll->id)->where('name', 'HESLB')->first();
            if (is_null($monthheslb)) {
                $monthheslb = new PayrollDeduction();
                $monthheslb->shop_id = $shop->id;
                $monthheslb->user_id = Auth::user()->id;
                $monthheslb->m_payroll_id = $mpayroll->id;
                $monthheslb->date = $mpayroll->month;
                $monthheslb->name = 'HESLB';
                $monthheslb->amount = $total_heslb;
                $monthheslb->save();
            }else{
                $monthheslb->date = $mpayroll->month;
                $monthheslb->amount = $total_heslb;
                $monthheslb->save();
            }
        }

         if ($other_deductions > 0) {
            $month_other = PayrollDeduction::where('shop_id', $shop->id)->where('m_payroll_id', $mpayroll->id)->where('name', 'Employee Recovery')->first();
            if (is_null($month_other)) {
                $month_other = new PayrollDeduction();
                $month_other->shop_id = $shop->id;
                $month_other->user_id = Auth::user()->id;
                $month_other->m_payroll_id = $mpayroll->id;
                $month_other->date = $mpayroll->month;
                $month_other->name = 'Employee Recovery';
                $month_other->amount = $other_deductions;
                $month_other->save();
            }else{
                $month_other->date = $mpayroll->month;
                $month_other->amount = $other_deductions;
                $month_other->save();
            }
        }

        return redirect()->back()->with('success', 'Payroll added to Expense successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
