<?php

namespace App\Http\Controllers\Acc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\Account;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanReturn;
use App\Models\AccountStatement;

class EmployeeLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Employee Loans';
        $company = Company::find(Session::get('company_id'));
        $accounts = Account::where('shop_id', Session::get('shop_id'))->get();
        $emploans = EmployeeLoan::where('employee_loans.company_id', $company->id)->join('employees', 'employees.id', 'employee_loans.employee_id')->select('employee_loans.id as id', 'emp_id', 'fname', 'lname', 'loan_date', 'amount', 'return_rate', 'amount_paid', 'status', 'remarks')->get();
        $employees = Employee::where('company_id', $company->id)->get();

        return view('emp-loans.index', compact('page', 'accounts', 'emploans', 'employees'));
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
        $date = Carbon::now();
        if (!empty($request['loan_date'])) {
            $date = $request['loan_date'];
        }

        $emploan = new EmployeeLoan();
        $emploan->company_id = Session::get('company_id');
        $emploan->user_id = Auth::user()->id;
        $emploan->employee_id = $request['employee_id'];
        $emploan->loan_date = $date;
        $emploan->amount = $request['amount'];
        $emploan->return_rate = $request['return_rate'];
        $emploan->remarks = $request['remarks'];
        $emploan->save();

        $account = null;
        $pay_mode = $request['pay_mode'];
        if ($pay_mode == 'Cash') {
            if (!empty($request['cash_acc_id'])) {
                $account = Account::find($request['cash_acc_id']);
            }else{
                return redirect()->back()->with('error', 'Selected Payment Method has no Account. Please Create Account for Cash Payments!.');
            }
        }elseif ($pay_mode == 'Bank' || $pay_mode == 'Cheque') {
            if (!empty($request['bank_acc_id'])) {
                $account = Account::find($request['bank_acc_id']);
            }else{
                return redirect()->back()->with('error', 'Selected Payment Method has no Account. Please Create Account for Bank Payments!.');
            }
        }elseif ($pay_mode == 'Mobile Money') {
            if (!empty($request['mob_acc_id'])) {
                    $account = Account::find($request['mob_acc_id']);
            }else{
                return redirect()->back()->with('error', 'Selected Payment Method has no Account. Please Create Account for Mobile Payments!.');
            }
        }
        if (!is_null($account)) {
            $employee = Employee::find($emploan->employee_id);
            $astmt = new AccountStatement();
            $astmt->shop_id = Session::get('shop_id');
            $astmt->user_id = Auth::user()->id;
            $astmt->employee_loan_id = $emploan->id;
            $astmt->account_id = $account->id;
            $astmt->date = $date;
            $astmt->debit = 0;
            $astmt->credit = $emploan->amount;
            $astmt->description = 'Salary Advance to '.$employee->fname.' '.$employee->lname;
            $astmt->save();
        }

        return redirect('emp-loans')->with('success', 'Employee Loan request submitted successful');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Employee Loan Details';
        $emploan = EmployeeLoan::where('employee_loans.id', decrypt($id))->join('employees', 'employees.id', '=', 'employee_loans.employee_id')->select('employee_loans.id as id', 'emp_id', 'fname', 'lname', 'loan_date', 'amount', 'amount_paid', 'status', 'is_approved', 'approved_by')->first();
        $emploan_returns = EmployeeLoanReturn::where('employee_loan_id', $emploan->id)->get();

        return view('emp-loans.show', compact('page', 'emploan', 'emploan_returns'));
    }

    public function approveLoan($id)
    {
        $emploan = EmployeeLoan::find(decrypt($id));
        $emploan->is_approved = true;
        $emploan->status = 'Approved';
        $emploan->approved_by = Auth::user()->first_name.' '.Auth::user()->last_name;
        $emploan->save();

        return redirect()->route('emp-loans.show', encrypt($emploan->id))->with('success', 'Employee Loan Approved successful');
    }

    public function issueLoan($id)
    {
        $emploan = EmployeeLoan::find(decrypt($id));
        $emploan->status = 'Issued';
        $emploan->save();

        return redirect()->route('emp-loans.show', encrypt($emploan->id))->with('success', 'Employee Loan Approved successful');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Employee Loan';
        $emploan = EmployeeLoan::find(decrypt($id));
        $employees = Employee::all();

        return view('emp-loans.edit', compact('page', 'emploan', 'employees'));
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
        $emploan = EmployeeLoan::find(decrypt($id));
        $emploan->employee_id = $request['employee_id'];
        $emploan->loan_date = $request['loan_date'];
        $emploan->amount = $request['amount'];
        $emploan->return_rate = $request['return_rate'];
        $emploan->remarks = $request['remarks'];
        $emploan->save();

        return redirect('emp-loans')->with('success', 'Employee Loan updated successful');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $emploan = EmployeeLoan::find(decrypt($id));
        if (!is_null($emploan)) {
            $emploan->delete();
        }
        
        return redirect('emp-loans')->with('success', 'Employee Loan updated successful');
    }
}
