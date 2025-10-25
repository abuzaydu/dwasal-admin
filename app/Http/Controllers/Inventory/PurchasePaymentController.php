<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PurchasePayment;
use App\Models\Purchase;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Setting;
use App\Models\SupplierTransaction;
use App\Models\Supplier;
use App\Models\PaymentVoucher;
use App\Models\CashIn;
use App\Models\CashOut;
use App\Models\Account;
use App\Models\AccountStatement;

class PurchasePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
       public function index(Request $request)
    {
        $page = 'Purchases Payments';
        $title = 'Purchases Payments';
        $shop = Shop::find(Session::get('shop_id'));
        $now = Carbon::now();
        $start = $now->startOfDay();
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

        $pay_mode = '';
        $paytypes = ['Cash', 'Bank', 'Mobile Money'];
        if (!empty($request['pay_mode'])) {
            $pay_mode = $request['pay_mode'];

            $payments = PurchasePayment::where('purchase_payments.shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', $pay_mode)->join('purchases', 'purchases.id', '=', 'purchase_payments.purchase_id')->where('purchases.is_deleted', false)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select('purchase_payments.id as id', 'supp_id', 'name', 'invoice_no', 'purchases.time_created', 'purchase_type', 'pay_date', 'account', 'amount', 'pv_no', 'trans_id')->orderBy('pv_no', 'desc')->get();
        }else{
            $payments = PurchasePayment::where('purchase_payments.shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->join('purchases', 'purchases.id', '=', 'purchase_payments.purchase_id')->where('purchases.is_deleted', false)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select('purchase_payments.id as id', 'supp_id', 'name', 'invoice_no', 'purchases.time_created', 'purchase_type', 'pay_date', 'account', 'amount', 'pv_no', 'trans_id')->orderBy('pv_no', 'desc')->get();
        }
        $duration = '';
        // return $payments;
        $excpayments = [];
        $utransactions = SupplierTransaction::where('supplier_transactions.shop_id', $shop->id)->whereNotNull('pv_no')->where('is_utilized', false)->where('is_deleted', false)->join('suppliers', 'suppliers.id', '=', 'supplier_transactions.supplier_id')->get();
        foreach ($utransactions as $key => $trans) {
            $rem_amount = $trans->payment - ($trans->trans_invoice_amount + $trans->trans_ob_amount + $trans->trans_credit_amount);
            if ($rem_amount > 0) {
                if (isset($excpayments['supplier_id'])) {
                    $excpayments['supplier_id']['amount'] += $rem_amount;
                }else{
                    array_push($excpayments, ['supplier_id' => $trans->supplier_id, 'name' => $trans->name, 'amount' => $rem_amount]);
                }
            }
        }

        // return $excpayments;
        
        return view('products.purchases.payments.index', compact('page', 'title', 'shop', 'payments', 'is_post_query', 'start_date', 'end_date', 'duration', 'paytypes', 'pay_mode', 'excpayments'));
    }

    public function totalPurchasePayments(Request $request)
    {
        $page = 'Purchases Payments';
        $title = 'Total Purchases Payments';
        $shop = Shop::find(Session::get('shop_id'));
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
        
        $duration = 'From '.date('d F, Y', strtotime($start)).' To '.date('d F, Y', strtotime($end)).'.';
        if ($start_date == $end_date) {
            $duration = date('l, d F, Y', strtotime($start));
        }
      
        $pay_mode = '';
        $paytypes = ['Cash', 'Bank', 'Mobile Money'];
        $tpayments = [];
        $payments = PurchasePayment::where('purchase_payments.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->groupBy('pay_date')->orderBy('pay_date', 'asc')->get([
            \DB::raw('pay_date'),
            \DB::raw('SUM(amount) as amount')
        ]);

        foreach ($payments as $key => $value) {
            $cashpay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->where('purchase_type', 'cash')->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_date', $value->pay_date)->where('purchase_payments.is_deleted', false)->where('pay_date', $value->pay_date)->where('account', 'Cash')->sum('amount');
            $mobpay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->where('purchase_type', 'cash')->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_date', $value->pay_date)->where('purchase_payments.is_deleted', false)->where('pay_date', $value->pay_date)->where('account', 'Mobile Money')->sum('amount');
            $bankpay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->where('purchase_type', 'cash')->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_date', $value->pay_date)->where('purchase_payments.is_deleted', false)->where('pay_date', $value->pay_date)->where('account', 'Bank')->sum('amount');

            $credit_cashpay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->where('purchase_type', 'credit')->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_date', $value->pay_date)->where('purchase_payments.is_deleted', false)->where('account', 'Cash')->sum('amount');

            $credit_mobpay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->where('purchase_type', 'credit')->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_date', $value->pay_date)->where('purchase_payments.is_deleted', false)->where('account', 'Mobile Money')->sum('amount');
            $credit_bankpay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->where('purchase_type', 'credit')->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_date', $value->pay_date)->where('purchase_payments.is_deleted', false)->where('account', 'Bank')->sum('amount');

            $netpay = $cashpay+$mobpay+$bankpay;
            $totalcredit = $credit_cashpay+$credit_mobpay+$credit_bankpay;

            array_push($tpayments, ['pay_date' => $value->pay_date, 'Cash' => $cashpay, 'Mobile Money' => $mobpay, 'Bank' => $bankpay, 'netpay' => $netpay, 'Credit Cash' => $credit_cashpay, 'Credit Mobile Money' => $credit_mobpay, 'Credit Bank' => $credit_bankpay, 'total_credit' => $totalcredit, 'total_payments' => $value->amount]);
        }
        
        // return $tpayments;
        return view('products.purchases.payments.total-payments', compact('page', 'title', 'shop', 'tpayments', 'is_post_query', 'start_date', 'end_date', 'duration', 'paytypes', 'pay_mode'));
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $purchase = Purchase::find($request['purchase_id']);
    
        if (!is_null($purchase)) {            
            $paydate = \Carbon\Carbon::now();
            if (!empty($request['pay_date'])) {
                $paydate = $request['pay_date'];
            }

            $pay_mode = 'Cash';
            $bank_name = null;
            $branch_name = null;
            $account = Account::find($request['account_id']);
            if (!is_null($account)) {
                $pay_mode = $account->type;
                $bank_name = $account->bank_name;
                $branch_name = $account->branch_name;
            }

            $cheque_no = null;
            if (!empty($request['slip_no'])) {
                $cheque_no = $request['slip_no'];
            }else{
                $cheque_no = $request['cheque_no'];
            }
            $amount = 0;
            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
            $ex_rate = 1;
            if ($request['currency'] != $defcurr) {
                if ($request['ex_rate_mode'] == 'foreign') {
                    $local_ex_rate = $request['local_ex_rate'];
                    $ex_rate = 1/$local_ex_rate;
                }else{
                    $foreign_ex_rate = $request['foreign_ex_rate'];             
                    $ex_rate = $foreign_ex_rate;
                }
                $amount = $request['amount']/$ex_rate;
            }
            $pvno = 0;
            $max_pv_no = PurchasePayment::where('shop_id', $shop->id)->orderBy('pv_no', 'desc')->first();
            if (!is_null($max_pv_no)) {
                $pvno = $max_pv_no->pv_no+1;
            }else{
                $pvno = 1;
            }
            $payment = PurchasePayment::create([
                'shop_id' => $shop->id,
                'purchase_id' => $purchase->id,
                'pv_no' => $pvno,
                'pay_mode' => $pay_mode,
                'pay_date' => $paydate,
                'amount' => $request['amount'],
                'bank_name' => $bank_name,
                'bank_branch' => $bank_branch,
                'cheque_no' => $cheque_no,
                'pay_date' => $paydate,
                'currency' => $request['currency'],
                'defcurr' => $defcurr,
                'ex_rate' => $ex_rate,
                'comments' => $request['comments']
            ]);

            if ($payment) {
                $pv = new PaymentVoucher();
                $pv->shop_id = $shop->id;
                $pv->user_id = $user->id;
                $pv->pv_no =$pvno;
                $pv->amount = $request['amount'];
                $pv->account = $pay_mode;
                $pv->voucher_for = 'Purchase';
                $pv->save();

                    $payment->pv_no = $pv->pv_no;
                    $payment->save();


                    $payment_mode = null;
                    if ($request['pay_mode'] == 'Bank') {
                        $payment_mode = $request['deposit_mode'];
                    }else{
                        $payment_mode = $request['pay_mode'];
                    }

                if (!is_null($purchase->supplier_id)) {
                    $acctrans = new SupplierTransaction();
                    $acctrans->shop_id = $shop->id;
                    $acctrans->user_id = $user->id;
                    $acctrans->supplier_id = $purchase->supplier_id;
                    $acctrans->pv_no = $pvno;
                    $acctrans->payment = $payment->amount;
                    $acctrans->currency = $payment->currency;
                    $acctrans->defcurr = $payment->defcurr;
                    $acctrans->ex_rate = $payment->ex_rate;
                    $acctrans->payment_mode = $payment_mode;
                    $acctrans->bank_name = $bank_name;
                    $acctrans->bank_branch = $request['bank_branch'];
                    $acctrans->cheque_no = $cheque_no;
                    $acctrans->expire_date = $request['expire_date'];
                    $acctrans->date = $paydate;
                    $acctrans->save();

                    $astmt = new AccountStatement();
                    $astmt->shop_id = $shop->id;
                    $astmt->user_id = $user->id;
                    $astmt->supplier_transaction_id = $acctrans->id;
                    $astmt->account_id = $account->id;
                    $astmt->date = $paydate;
                    $astmt->debit = 0;
                    $astmt->credit = $acctrans->payment;
                    $astmt->description = 'Purchase Payments';
                    $astmt->save();
                }

                $ppays = PurchasePayment::where('purchase_id', $purchase->id)->get();
                $amount_paid = 0;
                foreach ($ppays as $key => $pay) {
                    $amount_paid += $pay->amount;
                }

                $purchase->amount_paid = $amount_paid;
                if (($purchase->total_amount-$purchase->amount_paid) == 0) {
                    $purchase->status = 'Paid';
                }else{
                    $purchase->status = 'Pending';
                }
                $purchase->save();
            }
        
            $success = 'Payments were added successfully';
            return redirect()->back()->with('success', $success);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Purchase Payment';
        $title = 'Edit Purchase Payment';
        $title_sw = 'Hariri Malipo ya Uzo';
        $is_post_query = false;
        $start_date = '';
        $end_date = '';
        $payment = PurchasePayment::find(decrypt($id));

        return view('products.purchases.edit-payment', compact('page', 'title', 'title_sw', 'payment', 'start_date', 'end_date', 'is_post_query'));
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
        $payment = PurchasePayment::find(decrypt($id));
        $purchase = Purchase::find($payment->purchase_id);
        
        $payment->pay_mode = $request['pay_mode'];
        $payment->pay_date = $request['pay_date'];
        $payment->amount = $request['amount'];
        $payment->save();

        if ($payment) {
            $ppays = PurchasePayment::where('purchase_id', $purchase->id)->get();
            $amount_paid = 0;
            foreach ($ppays as $key => $pay) {
                $amount_paid += $pay->amount;
            }

            $purchase->amount_paid = $amount_paid;
            if (($purchase->total_amount-$purchase->amount_paid) == 0) {
                $purchase->status = 'Paid';
            }else{
                $purchase->status = 'Pending';
            }
            $purchase->save();
        }

        $success = 'Payments was updated successfully';
        return redirect('purchases')->with('success', $success);
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

     public function setOpeningBalance(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $opdate = null;
        if (!empty($request['open_date'])) {
            $opdate = $request['open_date'];
        }else{
            $opdate = Carbon::now();
        }
                
            $acctrans = SupplierTransaction::where('supplier_id', $request['supplier_id'])->where('trans_for', 'Stock Purchase')->where('invoice_no', 'OB')->first();
            if (!is_null($acctrans)) {
                $acctrans->amount = $request['amount'];
                $acctrans->ob_paid = $request['ob_paid'];
                $acctrans->date = $opdate;
                $acctrans->save();
            }else{
                $acctrans = new SupplierTransaction();
                $acctrans->shop_id = $shop->id;
                $acctrans->user_id = $user->id;
                $acctrans->supplier_id = $request['supplier_id'];
                $acctrans->invoice_no = 'OB';
                $acctrans->amount = $request['amount'];
                $acctrans->date = $opdate;
                $acctrans->trans_for = 'Stock Purchase';
                $acctrans->save();
            }
       
        

        return redirect()->route('suppliers.show', encrypt($request['supplier_id']))->with('success', 'Opening balance was created successfully');
    }
    

    public function showVoucher($id)
    {
        $page = 'Payment Voucher';
        $title = 'Payment Voucher';
        $title_sw = 'Vocha ya Malipo';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $voucher = null;
        $ppays = null;

        $voucher = SupplierTransaction::find(decrypt($id));
        $ppays = PurchasePayment::where('pv_no', $voucher->pv_no)->where('purchase_payments.shop_id', $shop->id)->join('purchases', 'purchases.id', '=', 'purchase_payments.purchase_id')->select('purchase_payments.pay_date as pay_date', 'purchase_payments.amount as amount', 'purchase_payments.pv_no as pv_no', 'purchases.time_created as date', 'purchases.invoice_no as invoice_no')->get();
       
        $user = User::find($voucher->user_id);
        $supplier = Supplier::find($voucher->supplier_id);
        $amount_in_words = $this->convert_number_to_words($voucher->payment+0).' '.$settings->currency_words.' Only.';

        // return $ppays;
        return view('products.purchases.pv', compact('page', 'title', 'title_sw', 'shop', 'settings', 'user', 'voucher', 'supplier', 'amount_in_words', 'ppays'));
    }

    public function updateAdjustment(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $now = Carbon::now();
        if (!empty($request['adjust_date'])) {
            $now = $request['adjust_date'];
        }
        $acctrans = new SupplierTransaction();
        $acctrans->shop_id = $shop->id;
        $acctrans->user_id = $user->id;
        $acctrans->supplier_id = $request['supplier_id'];
        $acctrans->invoice_no = $request['invoice_no'];
        $acctrans->cn_no = $request['cn_no'];
        $acctrans->adjustment = $request['adjustment'];
        $acctrans->reason = $request['reason'];
        $acctrans->date = $now;
        $acctrans->trans_for = 'Stock Purchase';
        $acctrans->save();

        return redirect()->route('suppliers.show', encrypt($request['supplier_id']))->with('success', 'Adjustment was updated successfully');
    }

    public function accPayments(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $paydate = \Carbon\Carbon::now();

        if (!empty($request['pay_date']) && $request['pay_date'] != "") {
            $now = Carbon::now();
            $time = date('H:i:s', strtotime($now));
            $paydate = $request['pay_date'] . ' ' . $time;
        }

        $purchase = Purchase::find($request['purchase_id']);
        if (!is_null($purchase)) {
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

            $bank_name = $account->bank_name;
            $branch_name = $account->branch_name;

            $amount = $request['amount'];
            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
            $ex_rate = 1;
            $currency = $defcurr;
            if (!empty($request['currency'])) {
                $currency = $request['currency'];
                if ($request['currency'] != $defcurr) {
                    if ($request['ex_rate_mode'] == 'foreign') {
                        $local_ex_rate = $request['local_ex_rate'];
                        $ex_rate = 1/$local_ex_rate;
                    }else{
                        $foreign_ex_rate = $request['foreign_ex_rate'];             
                        $ex_rate = $foreign_ex_rate;
                    }
                }
            }
            if ($ex_rate == 0) {
                $ex_rate = 1;
            }

            $amount = $request['amount']/$ex_rate;
            $comments = $request['comments'];
                
            $payment_mode = null;
            $cheque_no = $request['cheque_no'];
            if ($request['pay_mode'] == 'Bank') {
                $payment_mode = $request['deposit_mode'];
                $cheque_no = $request['slip_no'];
            }else{
                $payment_mode = $request['pay_mode'];
            }


            $pv = new PaymentVoucher();
            $pv->shop_id = $shop->id;
            $pv->user_id = $user->id;
            $pv->pv_no =$pvno;
            $pv->amount = $amount;
            $pv->account = $pay_mode;
            $pv->voucher_for = 'Purchase';
            $pv->save();
                    
            $acctrans = new SupplierTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->supplier_id = $purchase->supplier_id;
            $acctrans->pv_no = $pvno;
            $acctrans->payment = $amount;
            $acctrans->trans_invoice_amount = $amount;
            $acctrans->currency = $currency;
            $acctrans->defcurr = $defcurr;
            $acctrans->ex_rate = $ex_rate;
            $acctrans->payment_mode = $payment_mode;
            $acctrans->bank_name = $bank_name;
            $acctrans->bank_branch = $branch_name;
            $acctrans->cheque_no = $cheque_no;
            $acctrans->expire_date = $request['expire_date'];
            $acctrans->date = $paydate;
            $acctrans->save();
                
            $payment = new PurchasePayment();
            $payment->shop_id = $shop->id;
            $payment->purchase_id = $purchase->id;
            $payment->pv_no = $pvno;
            $payment->pay_mode = $pay_mode;
            $payment->pay_date = $paydate;
            $payment->amount = $amount;
            $payment->bank_name = $bank_name;
            $payment->bank_branch = $branch_name;
            $payment->cheque_no = $cheque_no;
            $payment->pay_date = $paydate;
            $payment->currency = $currency;
            $payment->defcurr = $defcurr;
            $payment->ex_rate = $ex_rate;
            $payment->comments = $comments;
            $payment->save();
                
            if (!is_null($account)) {
                $astmt = new AccountStatement();
                $astmt->shop_id = $shop->id;
                $astmt->user_id = $user->id;
                $astmt->supplier_transaction_id = $acctrans->id;
                $astmt->account_id = $account->id;
                $astmt->date = $paydate;
                $astmt->debit = 0;
                $astmt->credit = $acctrans->payment;
                $astmt->description = 'Purchase Payments';
                $astmt->save();
            }

            $ppays = PurchasePayment::where('purchase_id', $purchase->id)->get();
            $amount_paid = 0;
            foreach ($ppays as $key => $pay) {
                $amount_paid += $pay->amount;
            }

            $purchase->amount_paid = $amount_paid;
            $purchase->save();
                
            $success = 'Payments were added successfully';

            return redirect()->route('suppliers.show', encrypt($request['supplier_id']))->with('success', $success);
        }else{

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
            $bank_name = $account->bank_name;
            $branch_name = $account->branch_name;

            $amount = $request['amount'];
            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
            $ex_rate = 1;
            $currency = $defcurr;
            if (!empty($request['currency'])) {
                $currency = $request['currency'];
                if ($request['currency'] != $defcurr) {
                    if ($request['ex_rate_mode'] == 'foreign') {
                        $local_ex_rate = $request['local_ex_rate'];
                        $ex_rate = 1/$local_ex_rate;
                    }else{
                        $foreign_ex_rate = $request['foreign_ex_rate'];             
                        $ex_rate = $foreign_ex_rate;
                    }
                }
            }

            if ($ex_rate == 0) {
                $ex_rate = 1;
            }

            $amount = $request['amount']/$ex_rate;
            
            $payment_mode = null;
            $cheque_no = $request['cheque_no'];
            if ($request['pay_mode'] == 'Bank') {
                $payment_mode = $request['deposit_mode'];
                $cheque_no = $request['slip_no'];
            }else{
                $payment_mode = $request['pay_mode'];
            }
            
            
            $pvno = 0;
            $max_pv_no = PaymentVoucher::where('shop_id', $shop->id)->orderByRaw('CONVERT(pv_no, SIGNED) desc')->first();
            if (!is_null($max_pv_no)) {
                $pvno = $max_pv_no->pv_no+1;
            }else{
                $pvno = 1;
            }
            
            $pv = new PaymentVoucher();
            $pv->shop_id = $shop->id;
            $pv->user_id = $user->id;
            $pv->pv_no =$pvno;
            $pv->amount = $amount;
            $pv->account = $pay_mode;
            $pv->voucher_for = 'Purchase';
            $pv->save();
                        
            $acctrans = new SupplierTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->supplier_id = $request['supplier_id'];
            $acctrans->pv_no = $pvno;
            $acctrans->payment = $amount;
            $acctrans->date = $paydate;
            $acctrans->save();
            if (!is_null($account)) {
                $astmt = new AccountStatement();
                $astmt->shop_id = $shop->id;
                $astmt->user_id = $user->id;
                $astmt->supplier_transaction_id = $acctrans->id;
                $astmt->account_id = $account->id;
                $astmt->date = $paydate;
                $astmt->debit = 0;
                $astmt->credit = $acctrans->payment;
                $astmt->description = 'Purchase Payments';
                $astmt->save();
            
                $rem_amount = 0;
                $trans_ob_amount = 0;
                $obtrans = SupplierTransaction::where('supplier_id', $request['supplier_id'])->where('is_ob', true)->where('shop_id', $shop->id)->first();
                if (!is_null($obtrans)) {
                    $ob_pending = $obtrans->amount-$obtrans->ob_paid;
                    if ($ob_pending > 0) {
                        if ($ob_pending >= $amount) {
                            $trans_ob_amount = $amount;
                            $obtrans->ob_paid = $obtrans->ob_paid+$amount;
                            $obtrans->save();
                            $cashout = CashOut::create([
                                'shop_id' => $shop->id,
                                'trans_id' => $acctrans->id,
                                'account_id' => $account->id,
                                'amount' => $amount,
                                'reason' => 'Supplier Opening balance payment',
                                'out_date' => $paydate
                            ]);
                        }else{
                            $obtrans->ob_paid = $obtrans->ob_paid+$ob_pending;
                            $obtrans->save();
                            $trans_ob_amount = $ob_pending;
                            $rem_amount = $amount-$ob_pending;
                            $cashout = CashOut::create([
                                'shop_id' => $shop->id,
                                'trans_id' => $acctrans->id,
                                'account_id' => $account->id,
                                'amount' => $amount,
                                'reason' => 'Supplier Opening balance payment',
                                'out_date' => $paydate
                            ]);
                        }
                    }else{
                        $rem_amount = $amount;
                    }
                }else{
                    $rem_amount = $amount;
                }

                // Pending Cash credits
                $trans_credit_amount = 0;
                if ($rem_amount > 0) {
                    $cashcredits = CashIn::where('shop_id', $shop->id)->where('supplier_id', $request['supplier_id'])->where('is_loan', true)->where('status', 'Pending')->get();
                    if (!is_null($cashcredits)) {
                        foreach ($cashcredits as $key => $credit) {
                            $pendcr = $credit->amount-$credit->amount_paid;
                            if ($rem_amount > 0) {
                                if ($rem_amount <= $pendcr) {
                                    $credit->amount_paid = $credit->amount_paid+$rem_amount;
                                    $credit->save();
                                    $cashin = CashOut::create([
                                        'shop_id' => $shop->id,
                                        'trans_id' => $acctrans->id,
                                        'cash_in_id' => $credit->id,
                                        'account_id' => $account->id,
                                        'amount' => $rem_amount,
                                        'source' => 'Supplier Cash Debts payments',
                                        'in_date' => $paydate
                                    ]);
                                }else{
                                    $credit->amount_paid = $credit->amount_paid+$pendcr;
                                    $credit->save();
                                    $cashin = CashOut::create([
                                        'shop_id' => $shop->id,
                                        'trans_id' => $acctrans->id,
                                        'cash_in_id' => $credit->id,
                                        'account_id' => $account->id,
                                        'amount' => $pendcr,
                                        'source' => 'Supplier Cash Debts payments',
                                        'in_date' => $paydate
                                    ]);
                                }
                                if ($credit->amount-$credit->amount_paid <= 0) {
                                    $credit->status = 'Paid';
                                    $credit->save();
                                }
                            }
                            $trans_credit_amount += $pendcr;
                            $rem_amount -= $pendcr;
                        }
                    }
                }
            }

            $acctrans->trans_ob_amount = $trans_ob_amount;
            $acctrans->trans_credit_amount = $trans_credit_amount;
            $acctrans->is_utilized = false;
            $acctrans->currency = $currency;
            $acctrans->defcurr = $defcurr;
            $acctrans->ex_rate = $ex_rate;
            $acctrans->payment_mode = $payment_mode;
            $acctrans->bank_name = $bank_name;
            $acctrans->bank_branch = $branch_name;
            $acctrans->cheque_no = $cheque_no;
            $acctrans->expire_date = $request['expire_date'];
            $acctrans->save();

            if ($rem_amount > 0) {
                $pps = Purchase::where('shop_id', $shop->id)->where('supplier_id', $request['supplier_id'])->where('is_deleted', false)->whereRaw('(total_amount-amount_paid) > 0')->get();
                     
                $curr_amount = $rem_amount;
                foreach ($pps as $key => $purch) {
                    $tunpaid = ($purch->total_amount-$purch->amount_paid);
                    if ($curr_amount > 0) {
                        if ($curr_amount <= $tunpaid) {
                            $amountpaid = $curr_amount;
                            $this->clearOldInvoice($purch, $amountpaid, $pay_mode, $paydate, $pvno, $bank_name, $branch_name, $cheque_no, $currency, $defcurr, $ex_rate, $request['comments'], $acctrans);
                        }elseif ($curr_amount > $tunpaid) {
                            $amountpaid = $tunpaid;
                            $this->clearOldInvoice($purch, $amountpaid, $pay_mode, $paydate, $pvno, $bank_name, $branch_name, $cheque_no, $currency, $defcurr, $ex_rate, $request['comments'], $acctrans);
                        }
                    }
                    $curr_amount -= $tunpaid;
                }
            }
            $success = 'Payments were added successfully';
            return redirect()->route('suppliers.show', encrypt($request['supplier_id']))->with('success', $success);
        }
    }

    public function clearOldInvoice($purch, $amount, $pay_mode, $paydate, $pv_no, $bank_name, $branch_name, $cheque_no, $currency, $defcurr, $ex_rate, $comments, $acctrans)
    {   
        $shop = Shop::find(Session::get('shop_id'));
        $payment = new PurchasePayment();
        $payment->shop_id = $shop->id;
        $payment->purchase_id = $purch->id;
        $payment->trans_id = $acctrans->id;
        $payment->pv_no = $pv_no;
        $payment->pay_mode = $pay_mode;
        $payment->bank_name = $bank_name;
        $payment->bank_branch = $branch_name;
        $payment->cheque_no = $cheque_no;
        $payment->pay_date = $paydate;
        $payment->amount = $amount;
        $payment->currency = $currency;
        $payment->defcurr = $defcurr;
        $payment->ex_rate = $ex_rate;
        $payment->comments = $comments;
        $payment->save();

        if ($payment) {
            $acctrans->trans_invoice_amount = $acctrans->trans_invoice_amount+$payment->amount;
            $acctrans->save();
            if (($acctrans->payment-($acctrans->trans_invoice_amount+$acctrans->trans_ob_amount+$acctrans->trans_credit_amount)) == 0){
                $acctrans->is_utilized = true;
                $acctrans->save();
            }

            $ppays = PurchasePayment::where('purchase_id', $purch->id)->get();
            $amount_paid = 0;
            foreach ($ppays as $key => $pay) {
                $amount_paid += $pay->amount;
            }

            $purch->amount_paid = $amount_paid;
            if (($purch->total_amount-$purch->amount_paid) == 0) {
                $purch->status = 'Paid';
            }
            $purch->save();
        }
    }

    public function deletePayment($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $accpay = SupplierTransaction::find(decrypt($id));
        if (!is_null($accpay)) {
            if ($accpay->trans_ob_amount > 0) {
                $obtrans = SupplierTransaction::where('shop_id', $shop->id)->where('supplier_id', $accpay->supplier_id)->where('is_ob', true)->first();
                if (!is_null($obtrans)) {
                    $obtrans->ob_paid = $obtrans->ob_paid-$accpay->trans_ob_amount;
                    $obtrans->save();
                }
            }

            $cashouts = CashOut::where('trans_id', $accpay->id)->get();
            foreach ($cashouts as $key => $out) {
                $cashin = CashIn::find($out->cash_in_id);
                if (!is_null($cashin)) {
                    $cashin->amount_paid = $cashin->amount_paid-$ins->amount;
                    $cashin->status = 'Pending';
                    $cashin->save();
                }
                $out->delete();
            }
            
            $ppays = PurchasePayment::where('trans_id', $accpay->id)->where('shop_id', $shop->id)->get();
            foreach ($ppays as $key => $payment) {
                $purchase = Purchase::find($payment->purchase_id);
                $payment->delete();
                if ($purchase->amount_paid > 0) {
                    $ppays = PurchasePayment::where('purchase_id', $purchase->id)->get();
                    $amount_paid = 0;
                    foreach ($ppays as $key => $pay) {
                        $amount_paid += $pay->amount;
                    }

                    $purchase->amount_paid = $amount_paid;
                    if (($purchase->total_amount-$purchase->amount_paid) == 0) {
                        $purchase->status = 'Paid';
                    }else{
                        $purchase->status = 'Pending';
                    }
                    $purchase->save();
                }
            }
            
            $accpay->delete();

            return redirect()->route('suppliers.show', encrypt($accpay->supplier_id))->with('success', 'Payments were deleted successful');
        }else{
            return redirect('suppliers')->with('info', 'Transaction was not Found');
        }
    }


    function convert_number_to_words($number) {
   
        $hyphen      = '-';
        $conjunction = '  ';
        $separator   = ' ';
        $negative    = 'negative ';
        $decimal     = ' and ';
        $dictionary  = array(
            0                   => 'Zero',
            1                   => 'One',
            2                   => 'Two',
            3                   => 'Three',
            4                   => 'Four',
            5                   => 'Five',
            6                   => 'Six',
            7                   => 'Seven',
            8                   => 'Eight',
            9                   => 'Nine',
            10                  => 'Ten',
            11                  => 'Eleven',
            12                  => 'Twelve',
            13                  => 'Thirteen',
            14                  => 'Fourteen',
            15                  => 'Fifteen',
            16                  => 'Sixteen',
            17                  => 'Seventeen',
            18                  => 'Eighteen',
            19                  => 'Nineteen',
            20                  => 'Twenty',
            30                  => 'Thirty',
            40                  => 'Fourty',
            50                  => 'Fifty',
            60                  => 'Sixty',
            70                  => 'Seventy',
            80                  => 'Eighty',
            90                  => 'Ninety',
            100                 => 'Hundred',
            1000                => 'Thousand',
            1000000             => 'Million',
            1000000000          => 'Billion',
            1000000000000       => 'Trillion',
            1000000000000000    => 'Quadrillion',
            1000000000000000000 => 'Quintillion'
        );
       
        if (!is_numeric($number)) {
            return false;
        }
       
        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $this->convert_number_to_words(abs($number));
        }
       
        $string = $fraction = null;
       
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
       
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convert_number_to_words($remainder);
                }
                break;
        }
       
        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }
        
        return $string;
    }

    function convert_number_to_wordsSW($number, $shop) {
   
        $hyphen      = '-';
        $conjunction = '  ';
        $separator   = ' ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'Sifuri',
            1                   => 'Moja',
            2                   => 'Mbili',
            3                   => 'Tatu',
            4                   => 'Nne',
            5                   => 'Tano',
            6                   => 'Sita',
            7                   => 'Saba',
            8                   => 'Nane',
            9                   => 'Tisa',
            10                  => 'Kumi',
            11                  => 'Kumi na moja',
            12                  => 'Kumi na mbili',
            13                  => 'Kumi na tatu',
            14                  => 'Kumi na nne',
            15                  => 'Kumi na tano',
            16                  => 'Kumi na sita',
            17                  => 'Kumi na saba',
            18                  => 'Kumi na nane',
            19                  => 'Kumi na tisa',
            20                  => 'Ishirini',
            30                  => 'Thelathini',
            40                  => 'Arobaini',
            50                  => 'Hamsini',
            60                  => 'Sitini',
            70                  => 'Sabini',
            80                  => 'Themanini',
            90                  => 'Tisini',
            100                 => 'Mia',
            1000                => 'Elfu',
            1000000             => 'Milioni',
            1000000000          => 'Bilioni',
            1000000000000       => 'Trillioni',
            1000000000000000    => 'Quadrillioni',
            1000000000000000000 => 'Quintillioni'
        );
       
        if (!is_numeric($number)) {
            return false;
        }
       
        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $this->convert_number_to_wordsSW(abs($number), $shop);
        }
       
        $string = $fraction = null;
       
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
       
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->convert_number_to_wordsSW($remainder, $shop);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convert_number_to_wordsSW($numBaseUnits, $shop) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convert_number_to_words($remainder, $shop);
                }
                break;
        }
       
        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }
        
        $settings = Settings::where('shop_id', $shop->id)->first();
        return $string.' '.$settings->currency.' tu';
    }
}
