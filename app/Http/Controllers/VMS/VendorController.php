<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Validator;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Vendor;
use App\Models\VendorTransaction;
use App\Models\SmsAccount;
use App\Models\PartPurchase;
use App\Models\PartPurchasePayment;
use App\Models\Setting;
use App\Models\VmsExpense;
use App\Models\Account;
use App\Models\AccountStatement;
use App\Models\PaymentVoucher;
use App\Imports\VendorImport;
use App\Models\CashIn;
use App\Models\CashOut;

class VendorController extends Controller
{    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = Company::find(Session::get('company_id')); 
        $vendors = Vendor::where('company_id', $company->id)->where('vendor_for', 'Parts')->get();
        $page = 'vendors';
        $title = 'My vendors';
        $title_sw = 'Wauzaji Wangu';
        return view('vms.vendors.index', compact('page', 'title', 'title_sw', 'vendors'));
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
        $company = Company::find(Session::get('company_id'));
        $vendor = Vendor::where('vendor_name', $request['name'])->where('vendor_for', $request['vendor_for'])->where('company_id' , $company->id)->first();
        if (!is_null($vendor)) {
            return redirect()->back()->with('info', 'This vendor has been added earlier');   
        }else{
            $vendor = new vendor();
            $vendor->company_id = $company->id;
            $vendor->vendor_name = $request['name'];
            $vendor->phone = $request['phone'];
            $vendor->email = $request['email'];
            $vendor->address = $request['address'];
            $vendor->vendor_for = $request['vendor_for'];
            $vendor->save();
            
            return redirect()->back()->with('message', 'Your vendor was added successfully.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id, Request $request)
    {
        $page = 'Vendor Account Statement';
        $title = 'Vendor Account Statement';
        $title_sw = 'Taarifa ya Akaunti ya vendor';
        $vendor = Vendor::find(decrypt($id));
        if (!is_null($vendor)) {
            $company = Company::find($vendor->company_id);
            $shop = Shop::find(Session::get('shop_id'));
            $accounts = Account::where('shop_id', $shop->id)->get();

            $now = Carbon::now();
            // $ftrans = VendorTransaction::where('company_id', $vendor->company_id)->where('vendor_id', $vendor->id)->orderBy('id', 'asc')->first();
            // $sdate = date('Y-m-d', strtotime($vendor->created_at)).' 00:00:00';
            // if (!is_null($ftrans)) {
            //     $sdate = $ftrans->date.' 00:00:00';
            // }

            $start = $vendor->created_at;
            $end = \Carbon\Carbon::now();
            $start_date = $start->format('Y-m-d');     
            $end_date = $end->format('Y-m-d');
            //check if user opted for date range
            // return $request;
            $is_post_query = false;
            if (!empty($request['start_date'])) {
                $start_date = $request['start_date'];
                $end_date = $request['end_date'];
                $start = $request['start_date'].' 00:00:00';
                $end = $request['end_date'].' 23:59:59';
                $is_post_query = true;
            }

            $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
            $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';


            $transactions = VendorTransaction::where('vendor_id', $vendor->id)->where('is_deleted', false)->whereBetween('date', [$start, $end])->orderBy('date', 'asc')->get();
            $invtrans = VendorTransaction::where('vendor_id', $vendor->id)->where('is_deleted', false)->whereNotNull('amount',)->whereBetween('date', [$start, $end])->orderBy('date', 'asc')->get();
            $payments = VendorTransaction::whereNotNull('payment')->where('vendor_id', $vendor->id)->where('is_deleted', false)->whereBetween('date', [$start, $end])->orderBy('date', 'desc')->get();
            $purchases = PartPurchase::where('company_id', $company->id)->where('vendor_id', $vendor->id)->whereRaw('(total_amount-amount_paid) > 0')->orderBy('pp_date', 'desc')->get();
            $obal = VendorTransaction::where('vendor_id', $vendor->id)->where('is_ob', true)->first();

            $settings = Setting::where('shop_id', $shop->id)->first();
            $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
            $senderids = null;
            if (!is_null($smsacc)) {
                $senderids = $smsacc->senderIds()->get();
            }

            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            return view('vms.vendors.show', compact('page', 'title', 'title_sw', 'shop', 'accounts', 'transactions', 'payments', 'purchases', 'vendor', 'is_post_query', 'duration', 'duration_sw', 'start_date', 'end_date', 'start', 'end', 'reporttime', 'settings', 'obal', 'invtrans', 'senderids', 'defcurr', 'currencies')); 
        }else{
            return redirect()->back()->with('error', 'vendor not Found');
        }
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit vendor';
        $title = 'Edit vendor Info';
        $title_sw = 'Hariri tarifa za Muuzaji';
        $vendor = Vendor::find(decrypt($id));

        return view('vms.vendors.edit', compact('page', 'title', 'title_sw', 'vendor'));
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
        $company = Company::find(Session::get('company_id'));
        $vendor = Vendor::find(decrypt($id));
        $vendor->vendor_name = $request['vendor_name'];
        $vendor->phone = $request['phone'];
        $vendor->email = $request['email'];
        $vendor->address = $request['address'];
        $vendor->save();

        return redirect('vendors')->with('message', 'Your vendor was updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        
        $company = Company::find(Session::get('company_id'));
        $vendor = Vendor::find(decrypt($id));
        if (!is_null($vendor)) {
            $expenses = VmsExpense::where('vendor_id', $vendor->id)->count();
            $purchases = PartPurchase::where('vendor_id', $vendor->id)->count();
            if ($expenses > 0) {
                return redirect()->back()->with('info', 'vendor has related recods in Expenses so cannot be deleted');
            }elseif ($purchases > 0) {
                return redirect()->back()->with('info', 'vendor has related recods in Purchases so cannot be deleted');
            }else{
                $vendor->delete();
                return redirect()->back()->with('success', 'vendor deleted successfully');
            }
        }
    }

    public function downloadSample()
    {
        return response()->download(public_path('sample-vendors.xlsx'));
    }


    public function import(Request $request) 
    {
         $rules = array(
            'file' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        // process the form
        if ($validator->fails()) 
        {
            return \Redirect::to('vendors')->withErrors($validator);
        }else{
            Excel::import(new vendorImport, request()->file('file'));
            return redirect('vendors')->with('success', 'vendors were imported successfully!');
        }
    }


    public function accPayments(Request $request)
    {
        $company = Company::find(Session::get('company_id'));
        if (!is_null($company)) {
            $shop = Shop::find(Session::get('shop_id'));
            $user = Auth::user();
            $paydate = \Carbon\Carbon::now();

            if (!empty($request['pay_date']) && $request['pay_date'] != "") {
                $now = Carbon::now();
                $time = date('H:i:s', strtotime($now));
                $paydate = $request['pay_date'] . ' ' . $time;
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
                    return redirect('vendor-account-stmt/'.encrypt($purchase->vendor_id))->with('error', 'Selected Payment Method has no Account. Please Create Account for Cash Payments!.');
                }
            }elseif ($pay_mode == 'Bank' || $pay_mode == 'Cheque') {
                if (!empty($request['bank_acc_id'])) {
                    $account = Account::find($request['bank_acc_id']);
                }else{
                    return redirect('vendor-account-stmt/'.encrypt($purchase->vendor_id))->with('error', 'Selected Payment Method has no Account. Please Create Account for Bank Payments!.');
                }
            }elseif ($pay_mode == 'Mobile Money') {
                if (!empty($request['mob_acc_id'])) {
                    $account = Account::find($request['mob_acc_id']);
                }else{
                    return redirect('vendor-account-stmt/'.encrypt($purchase->vendor_id))->with('error', 'Selected Payment Method has no Account. Please Create Account for Mobile Payments!.');
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
            $pv->shop_id = Session::get('shop_id');
            $pv->user_id = $user->id;
            $pv->pv_no =$pvno;
            $pv->amount = $amount;
            $pv->account = $pay_mode;
            $pv->voucher_for = 'Purchase';
            $pv->save();
                        
            $acctrans = new VendorTransaction();
            $acctrans->company_id = $company->id;
            $acctrans->user_id = $user->id;
            $acctrans->vendor_id = $request['vendor_id'];
            $acctrans->pv_no = $pvno;
            $acctrans->payment = $amount;
            $acctrans->date = $paydate;
            $acctrans->save();
            if (!is_null($account)) {
                $astmt = new AccountStatement();
                $astmt->shop_id = Session::get('shop_id');
                $astmt->user_id = $user->id;
                $astmt->vendor_transaction_id = $acctrans->id;
                $astmt->account_id = $account->id;
                $astmt->date = $paydate;
                $astmt->debit = 0;
                $astmt->credit = $acctrans->payment;
                $astmt->description = 'Purchase Payments';
                $astmt->save();
            }

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

            if ($amount > 0) {
                $pps = PartPurchase::where('company_id', $company->id)->where('vendor_id', $request['vendor_id'])->whereRaw('(total_amount-amount_paid) > 0')->get();
                
                $curr_amount = $amount;
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
            return redirect()->route('vendors.show', encrypt($request['vendor_id']))->with('success', $success);
        }
    }

    public function clearOldInvoice($purch, $amount, $pay_mode, $paydate, $pv_no, $bank_name, $branch_name, $cheque_no, $currency, $defcurr, $ex_rate, $comments, $acctrans)
    {   
        $payment = new PartPurchasePayment();
        $payment->company_id = Session::get('company_id');
        $payment->part_purchase_id = $purch->id;
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

            $ppays = PartPurchasePayment::where('part_purchase_id', $purch->id)->get();
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
        $accpay = VendorTransaction::find(decrypt($id));
        if (!is_null($accpay)) {
            if ($accpay->trans_ob_amount > 0) {
                $obtrans = VendorTransaction::where('vendor_id', $accpay->vendor_id)->where('is_ob', true)->first();
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
            
            $ppays = PartPurchasePayment::where('trans_id', $accpay->id)->get();
            foreach ($ppays as $key => $payment) {
                $purchase = PartPurchase::find($payment->part_purchase_id);
                $payment->delete();
                if ($purchase->amount_paid > 0) {
                    $ppays = PartPurchasePayment::where('part_purchase_id', $purchase->id)->get();
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

            return redirect()->route('vendors.show', encrypt($accpay->vendor_id))->with('success', 'Payments were deleted successful');
        }else{
            return redirect('vendors')->with('info', 'Transaction was not Found');
        }
    }

    public function deleteTrans($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $accpay = VendorTransaction::find(decrypt($id));
        if (!is_null($accpay)) {            
            $accpay->delete();

            return redirect()->route('vendors.show', encrypt($accpay->vendor_id))->with('success', 'Payments were deleted successful');
        }else{
            return redirect('vendors')->with('info', 'Transaction was not Found');
        }
    }
}
