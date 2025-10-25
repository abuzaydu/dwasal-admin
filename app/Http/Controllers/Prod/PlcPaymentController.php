<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\Account;
use App\Models\PlcPayment;
use App\Models\ProdLabourCost;
use App\Models\PlcItemPayment;
use App\Models\AccountStatement;
use App\Models\PaymentVoucher;

class PlcPaymentController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
  
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = $start_date.' to '.$end_date;
        $page = 'Labour Cost Payments';
        $title = 'Labour Costs Payments';
        $title_sw = 'Labour Costs Payments';

        $shop = Shop::find(Session::get('shop_id'));
        $accounts = Account::where('shop_id', $shop->id)->get();
        $plc_payments = PlcPayment::where('plc_payments.shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->join('users', 'users.id', '=', 'plc_payments.user_id')->select('plc_payments.id as id', 'pay_date', 'amount', 'pay_mode', 'first_name')->get();
        $mros = $shop->mro()->where('is_deleted' , false)->get([
            \DB::raw('id'),
            \DB::raw('name'),]);
        return view('production.labour-costs.plc-payments.index', compact('page', 'title', 'title_sw','shop', 'accounts', 'mros', 'plc_payments', 'duration', 'start_date', 'end_date', 'is_post_query'));
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
        $paydate = \Carbon\Carbon::now();

        if (!empty($request['pay_date'])) {
            $paydate = $request['pay_date'];
        }

        $pvno = 0;
        $max_pv_no = PaymentVoucher::where('shop_id', $shop->id)->orderByRaw('CONVERT(pv_no, SIGNED) desc')->first();
        if (!is_null($max_pv_no)) {
            $pvno = $max_pv_no->pv_no+1;
        }else{
            $pvno = 1;
        }
        $pay_mode = null;
        if ($request['pay_mode'] == 'Cheque') {
            $pay_mode = 'Bank';
        }else{
            $pay_mode = $request['pay_mode'];
        }
        $cheque_no = $request['cheque_no'];
        if ($request['pay_mode'] == 'Bank') {
            $cheque_no = $request['slip_no'];
        }
        $account = null;
        if ($pay_mode == 'Cash') {
            if (!empty($request['cash_acc_id'])) {
                $account = Account::find($request['cash_acc_id']);
            }else{
                return redirect('supplier-account-stmt/'.encrypt($purchase->supplier_id))->with('error', 'Selected Payment Method has no Account. Please Create Account for Cash Payments!.');
            }
        }elseif ($pay_mode == 'Bank' || $pay_mode == 'Cheque') {
            if (!empty($request['bank_acc_id'])) {
                $account = Account::find($request['bank_acc_id']);
            }else{
                return redirect('supplier-account-stmt/'.encrypt($purchase->supplier_id))->with('error', 'Selected Payment Method has no Account. Please Create Account for Bank Payments!.');
            }
        }elseif ($pay_mode == 'Mobile Money') {
            if (!empty($request['mob_acc_id'])) {
                $account = Account::find($request['mob_acc_id']);
            }else{
                return redirect('supplier-account-stmt/'.encrypt($purchase->supplier_id))->with('error', 'Selected Payment Method has no Account. Please Create Account for Mobile Payments!.');
            }
        }

        $bank_name = '';
        $branch_name = '';
        if (!is_null($account)) {
            $bank_name = $account->bank_name;
            $branch_name = $account->branch_name;
        }
        $amount = $request['amount'];

        $pv = new PaymentVoucher();
        $pv->shop_id = $shop->id;
        $pv->user_id = $user->id;
        $pv->pv_no = $pvno;
        $pv->amount = $amount;                
        $pv->voucher_for = 'Direct Labour Cost Payments';
        $pv->account = $pay_mode;
        $pv->save();
            
        $payment = new PlcPayment();
        $payment->shop_id = $shop->id;
        $payment->user_id = $user->id;
        $payment->pv_no = $pvno;
        $payment->pay_mode = $pay_mode;
        $payment->pay_date = $paydate;
        $payment->amount = $amount;
        $payment->bank_name = $bank_name;
        $payment->bank_branch = $branch_name;
        $payment->cheque_no = $cheque_no;
        $payment->pay_date = $paydate;
        $payment->comments = $request['comments'];
        $payment->save();
        if (!is_null($account)) {
            $astmt = new AccountStatement();
            $astmt->shop_id = $shop->id;
            $astmt->user_id = $user->id;
            $astmt->plc_payment_id = $payment->id;
            $astmt->account_id = $account->id;
            $astmt->date = $paydate;
            $astmt->debit = 0;
            $astmt->credit = $payment->amount;
            $astmt->description = 'Direct Labour Cost Payments';
            $astmt->save();
        }

        $pplcitems = ProdLabourCost::where('shop_id', $shop->id)->whereRaw('amount-amount_paid > 0')->get();
        if ($pplcitems->count() > 0) {
            $curr_amount = $payment->amount;
            foreach ($pplcitems as $key => $plc) {
                $tunpaid = $plc->amount-$plc->amount_paid;
                if ($curr_amount > 0) {
                    if ($curr_amount <= $tunpaid) {
                        $amountpaid = $curr_amount;
                        $this->clearOldItem($plc, $amountpaid, $payment);
                    }elseif ($curr_amount > $tunpaid) {
                        $amountpaid = $tunpaid;
                        $this->clearOldItem($plc, $amountpaid, $payment);
                    }
                }
                $curr_amount -= $tunpaid;
            }
        }
        return redirect('plc-payments')->with('success', 'Payment record Created successfully');
    }

    public function clearOldItem($plc, $payamount, $mpay)
    {
        $plcpay = new PlcItemPayment();
        $plcpay->prod_labour_cost_id = $plc->id;
        $plcpay->plc_payment_id = $mpay->id;
        $plcpay->pay_date = $mpay->pay_date;
        $plcpay->paid_amt = $payamount;
        $plcpay->save();

        $mpay->utilized_amt = $mpay->utilized_amt+$payamount;
        $mpay->is_utilized = false;
        $mpay->save();

        $plc->amount_paid = $plc->amount_paid+$payamount;
        $plc->save();
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
        $page = 'Edit Labour Costs Payments';
        $title = 'Edit Labour Costs Payments';
        $title_sw = 'Edit Labour Costs Payments';
        $shop = Shop::find(Session::get('shop_id'));
        $accounts = Account::where('shop_id', $shop->id)->get();
        $plcpay = PlcPayment::find(decrypt($id));

        $mros = $shop->mro()->where('is_deleted' , false)->get([
            \DB::raw('id'),
            \DB::raw('name'),]);
        return view('production.labour-costs.plc-payments.edit', compact('page', 'title', 'title_sw', 'shop', 'accounts', 'plcpay', 'mros'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $plcpay = PlcPayment::find(decrypt($id));
        if (!is_null($plcpay)) {
            $shop = Shop::find($plcpay->shop_id);
            $mipays = PlcItemPayment::where('plc_payment_id', $plcpay->id)->get();
            foreach ($mipays as $key => $mipay) {
                $plc = ProdLabourCost::find($mipay->prod_labour_cost_id);
                if (!is_null($plc)) {
                    $plc->amount_paid = $plc->amount_paid-$mipay->paid_amt;
                    $plc->save();
                }
                $mipay->delete();
            }

            $astmt = AccountStatement::where('plc_payment_id', $plcpay->id)->first();
            if (!is_null($astmt)) {
                $astmt->delete();
            }

            $paydate = \Carbon\Carbon::now();

            if (!empty($request['pay_date'])) {
                $paydate = $request['pay_date'];
            }

            $pay_mode = null;
            if ($request['pay_mode'] == 'Cheque') {
                $pay_mode = 'Bank';
            }else{
                $pay_mode = $request['pay_mode'];
            }
            $cheque_no = $request['cheque_no'];
            if ($request['pay_mode'] == 'Bank') {
                $cheque_no = $request['slip_no'];
            }

            $account = null;
            if ($pay_mode == 'Cash') {
                if (!empty($request['cash_acc_id'])) {
                    $account = Account::find($request['cash_acc_id']);
                }else{
                    return redirect('supplier-account-stmt/'.encrypt($purchase->supplier_id))->with('error', 'Selected Payment Method has no Account. Please Create Account for Cash Payments!.');
                }
            }elseif ($pay_mode == 'Bank' || $pay_mode == 'Cheque') {
                if (!empty($request['bank_acc_id'])) {
                    $account = Account::find($request['bank_acc_id']);
                }else{
                    return redirect('supplier-account-stmt/'.encrypt($purchase->supplier_id))->with('error', 'Selected Payment Method has no Account. Please Create Account for Bank Payments!.');
                }
            }elseif ($pay_mode == 'Mobile Money') {
                if (!empty($request['mob_acc_id'])) {
                    $account = Account::find($request['mob_acc_id']);
                }else{
                    return redirect('supplier-account-stmt/'.encrypt($purchase->supplier_id))->with('error', 'Selected Payment Method has no Account. Please Create Account for Mobile Payments!.');
                }
            }

            $bank_name = '';
            $branch_name = '';
            if (!is_null($account)) {       
                $bank_name = $account->bank_name;
                $branch_name = $account->branch_name;
            }
            $amount = $request['amount'];

            $pv = PaymentVoucher::where('pv_no', $plcpay->pv_no)->where('shop_id', $plcpay->shop_id)->first();
            if (!is_null($pv)) {
                $pv->amount = $amount;
                $pv->account = $pay_mode;
                $pv->save();
            }

            $plcpay->pay_mode = $pay_mode;
            $plcpay->pay_date = $paydate;
            $plcpay->amount = $amount;
            $plcpay->bank_name = $bank_name;
            $plcpay->bank_branch = $branch_name;
            $plcpay->cheque_no = $cheque_no;
            $plcpay->pay_date = $paydate;
            $plcpay->comments = $request['comments'];
            $plcpay->save();
            if (!is_null($account)) {
                $astmt = new AccountStatement();
                $astmt->shop_id = $shop->id;
                $astmt->user_id = $plcpay->user_id;
                $astmt->plc_payment_id = $plcpay->id;
                $astmt->account_id = $account->id;
                $astmt->date = $paydate;
                $astmt->debit = 0;
                $astmt->credit = $plcpay->amount;
                $astmt->description = 'Labour Cost Payments';
                $astmt->save();
            }

            $pplcitems = ProdLabourCost::where('shop_id', $shop->id)->whereRaw('amount-amount_paid > 0')->get();
            if ($pplcitems->count() > 0) {
                $curr_amount = $plcpay->amount;
                foreach ($pplcitems as $key => $plc) {
                    $tunpaid = $plc->amount-$plc->amount_paid;
                    if ($curr_amount > 0) {
                        if ($curr_amount <= $tunpaid) {
                            $amountpaid = $curr_amount;
                            $this->clearOldItem($plc, $amountpaid, $plcpay);
                        }elseif ($curr_amount > $tunpaid) {
                            $amountpaid = $tunpaid;
                            $this->clearOldItem($plc, $amountpaid, $plcpay);
                        }
                    }
                    $curr_amount -= $tunpaid;
                }
            }

            return redirect('plc-payments')->with('success', 'Payment record Created successfully');
        }else{
            return redirect()->back()->with('error', 'Record not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $plcpay = PlcPayment::find(decrypt($id));
        if (!is_null($plcpay)) {
            $mipays = PlcItemPayment::where('plc_payment_id', $plcpay->id)->get();
            foreach ($mipays as $key => $mipay) {
                $plc = ProdLabourCost::find($mipay->prod_labour_cost_id);
                if (!is_null($plc)) {
                    $plc->amount_paid = $plc->amount_paid-$mipay->paid_amt;
                    $plc->save();
                }
                $mipay->delete();
            }

            $astmt = AccountStatement::where('plc_payment_id', $plcpay->id)->first();
            if (!is_null($astmt)) {
                $astmt->delete();
            }

            $pv = PaymentVoucher::where('pv_no', $plcpay->pv_no)->where('shop_id', $plcpay->shop_id)->first();
            if (!is_null($pv)) {
                $pv->delete();
            }

            $plcpay->delete();

            return redirect('plc-payments')->with('success', 'Payment deleted successfully');
        }else{
            return redirect()->back()->with('error', 'Record not found');
        }
    }
}
