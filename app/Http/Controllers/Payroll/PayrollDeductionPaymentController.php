<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\PayrollDeduction;
use App\Models\PayrollDeductionPayment;
use App\Models\Account;
use App\Models\AccountStatement;

class PayrollDeductionPaymentController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $paydate = \Carbon\Carbon::now();
        if (!empty($request['pay_date'])) {
            $paydate = $request['pay_date'];
        }
        $pdeduction = PayrollDeduction::find($request['payroll_deduction_id']);
        if (!is_null($pdeduction)) {
            $user = Auth::user();
            $shop = Shop::find(Session::get('shop_id'));
            $pdpayment = PayrollDeductionPayment::where('payroll_deduction_id', $pdeduction->id)->first();
            if (is_null($pdpayment)) {
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

                $pdpayment = new PayrollDeductionPayment();
                $pdpayment->shop_id = $shop->id;
                $pdpayment->user_id = $user->id;
                $pdpayment->payroll_deduction_id = $request['payroll_deduction_id'];
                $pdpayment->pay_date = $paydate;
                $pdpayment->amount_paid = $request['amount_paid'];
                $pdpayment->pay_mode = $pay_mode;
                $pdpayment->reference = $request['reference'];
                $pdpayment->save();

                if (!is_null($account)) {
                    $astmt = new AccountStatement();
                    $astmt->shop_id = $shop->id;
                    $astmt->user_id = $user->id;
                    $astmt->payroll_deduction_payment_id = $pdpayment->id;
                    $astmt->account_id = $account->id;
                    $astmt->date = $paydate;
                    $astmt->debit = 0;
                    $astmt->credit = $pdpayment->amount;
                    $astmt->description = $pdeduction->name.' Payment for '.date('M Y', strtotime($pdeduction->date));
                    $astmt->save();
                }

                return redirect()->route('payroll-deductions.show', encrypt($pdeduction->id))->with('success', 'Payroll Deduction Payment Added successfully');
            }else{
                return redirect()->route('payroll-deductions.show', encrypt($pdeduction->id))->with('info', 'Payroll Deduction Payment already Added');
            }
        }else{
            return redirect('payroll-deductions')->with('error', 'Payroll Deduction Item not Found');
        }
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
