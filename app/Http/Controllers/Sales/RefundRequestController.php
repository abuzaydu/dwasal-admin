<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Account;
use App\Models\AccountStatement;
use App\Models\CashOut;
use App\Models\AnSale;
use App\Models\CustomerTransaction;

class RefundRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Refund Requests';
        $title = 'Refund Requests';
        $shop = Shop::find(Session::get('shop_id'));
        $refunds = CustomerTransaction::where('customer_transactions.shop_id', $shop->id)->where('is_refund', true)->join('customers', 'customers.id', '=', 'customer_transactions.customer_id')->join('users', 'users.id', '=', 'customer_transactions.user_id')->select('customer_transactions.id as id', 'name', 'first_name', 'date', 'refund_no', 'refund_amt', 'status', 'approved_by', 'approved_time', 'confirmed_by', 'confirm_time', 'customer_transactions.created_at as created_at', 'customer_transactions.updated_at as updated_at')->get();
        return view('sales.returns.refunds.index', compact('page', 'title', 'refunds')); 
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $sale = AnSale::find($request['an_sale_id']);
        if (!is_null($sale)) {    
            $maxrec_no = CustomerTransaction::where('shop_id', $shop->id)->orderByRaw('CONVERT(refund_no, SIGNED) desc')->first();
            $refund_no = 1;
            if (!is_null($maxrec_no)) {
                $refund_no = $maxrec_no->refund_no+1;
            }


            $amount = $request['refund_amt'];
            $date = Carbon::now();
            if (!empty($request['date'])) {
                $date = $request['date'];
            }

            $acctrans = new CustomerTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->customer_id = $sale->customer_id;
            $acctrans->invoice_no = $sale->invoice_no;
            $acctrans->is_refund = true;
            $acctrans->refund_no = $refund_no;
            $acctrans->refund_amt = $amount;
            $acctrans->date = $date;
            $acctrans->remarks = $request['remarks'];
            $acctrans->save();

            $success = 'Refund Request created successfully';
            return redirect('refund-requests')->with('success', $success);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Refund Request';
        $title = 'Refund Request';

        $shop = Shop::find(Session::get('shop_id'));
        $accounts = Account::where('shop_id', $shop->id)->get();
        $refund = CustomerTransaction::find(decrypt($id));

        $astmt = AccountStatement::where('customer_transaction_id', $refund->id)->first();
        $account = null;
        if (!is_null($astmt)) {
            $account = Account::find($astmt->account_id);
        }

        return view('sales.returns.refunds.show', compact('page', 'title', 'refund', 'accounts', 'account'));
    }

    public function rejectRefund(Request $request)
    {
        $refund = CustomerTransaction::find($request['id']);
        $refund->status = 'Rejected';
        if (!is_null($refund->remarks)) {
            $refund->remarks = $refund->remarks.'<br> Reject Reason: '.$request['remarks'];
        }else{
            $refund->remarks = $request['remarks'];
        }
        $refund->save();

        return redirect()->route('refund-requests.show', encrypt($refund->id))->with('success', 'Request Rejected successfully');
    }

    public function approveRefund($id)
    {
        $refund = CustomerTransaction::find(decrypt($id));
        if (!is_null($refund)) {
            $user = Auth::user();
            $refund->status = 'Approved';
            $refund->approved_time = Carbon::now();
            $refund->approved_by = $user->first_name.''.$user->last_name;
            $refund->save();

            return redirect()->route('refund-requests.show', encrypt($refund->id))->with('success', 'Request Approved successfully');
        }else{
            return redirect()->route('refund-requests.show', encrypt($refund->id))->with('error', 'Item not Found');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Refund Request';
        $title = 'Edit Refund Request';
        $refund = CustomerTransaction::find(decrypt($id));

        return view('sales.returns.refunds.edit', compact('page', 'title', 'refund'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $refund = CustomerTransaction::find(decrypt($id));
        if (!is_null($refund)) {
            $user = Auth::user();
            $shop = Shop::find($refund->shop_id);
            $account = Account::find($request['account_id']);
            if (!is_null($account)) {
                $refund->status = 'Refunded';
                $refund->confirm_time = Carbon::now();
                $refund->confirmed_by = $user->first_name.' '.$user->last_name;
                $refund->payment_mode = $account->type;
                $refund->save();

                $astmt = new AccountStatement();
                $astmt->shop_id = $shop->id;
                $astmt->user_id = $user->id;
                $astmt->customer_transaction_id = $refund->id;
                $astmt->account_id = $account->id;
                $astmt->date = $refund->date;
                $astmt->debit = 0;
                $astmt->credit = $refund->refund_amt;
                $astmt->description = 'Cash Refund (Refund No. '.sprintf('%04d', $refund->refund_no).')';
                $astmt->save();     

                $utransactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $refund->customer_id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->get();
                if (!is_null($utransactions)) {
                    $curr_amount = $refund->refund_amt;
                    foreach ($utransactions as $key => $trans) {
                        $rem_amount = $trans->payment - ($trans->trans_invoice_amount + $trans->trans_ob_amount + $trans->trans_credit_amount);
                        if ($rem_amount > 0) {
                            $paidamount = 0;
                            if ($rem_amount > $curr_amount) {
                                $paidamount = $curr_amount;
                                $trans->trans_credit_amount = $trans->trans_credit_amount+$paidamount;
                                $trans->save();
                            } else {
                                $paidamount = $rem_amount;
                                $trans->trans_credit_amount = $trans->trans_credit_amount+$paidamount;
                                $trans->is_utilized = true;
                                $trans->save();
                            }

                            $cashout = new CashOut();
                            $cashout->shop_id = $shop->id;
                            $cashout->trans_id = $trans->id;
                            $cashout->refund_trans_id = $refund->id;
                            $cashout->account_id = $account->id;
                            $cashout->amount = $paidamount;
                            $cashout->reason = 'Cash Refund';
                            $cashout->out_date = $refund->date;
                            $cashout->save();

                            $curr_amount -= $paidamount;
                        }
                    }
                }
                return redirect('refund-requests')->with('success', 'Refund Confirmed successfully'); 
            }else{
                $refund->date = $request['date'];
                $refund->refund_amt = $request['refund_amt'];
                $refund->remarks = $request['remarks'];
                $refund->save();

                return redirect('refund-requests')->with('success', 'Refund Request updated successfully'); 
            }
           
        }else{
            return redirect('refund-requests')->with('success', 'Item not Found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $refund = CustomerTransaction::find(decrypt($id));
        if (!is_null($refund)) {
            $astmt = AccountStatement::where('customer_transaction_id', $refund->id)->first();
            if (!is_null($astmt)) {
                $astmt->delete();
            }

            $cashouts = CashOut::where('refund_trans_id', $refund->id)->get();
            foreach ($cashouts as $key => $cashout) {
                $trans = CustomerTransaction::find($cashout->trans_id);
                if (!is_null($trans)) {
                    $trans->trans_credit_amount = $trans->trans_credit_amount-$cashout->amount;
                    $trans->is_utilized = false;
                    $trans->save();
                }

                $cashout->delete();
            }

            $refund->delete();
            return redirect('refund-requests')->with('success', 'Refund Cancelled successfully');
        }else{
            return redirect('refund-requests')->with('error', 'Refund Item not Found');
        }
    }
}
