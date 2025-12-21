<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\ImagickEscposImage;
use Log;
use Session;
use Auth;
use \DB;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\ProductUnit;
use App\Models\ServiceSaleItem;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\SalePayment;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\SmsAccount;
use App\Models\SenderId;
use App\Models\SmsTemplate;
use App\Models\CashIn;
use App\Models\CashOut;
use App\Models\Account;
use App\Models\AccountStatement;
use App\Models\Booking;
use App\Models\Contract;
use App\Models\ContractService;
use App\Models\DailyDeposit;
use App\Models\TripLog;

class InvoiceController extends Controller
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
    public function index(Request $request)
    {
        $page = 'Invoices';
        $title = 'Invoices';
        $title_sw = 'Ankara';
        

        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $settings = Setting::where('shop_id', $shop->id)->first();
            $now = Carbon::now();
                $start = null;
                $end = null;
                $start_date = null;            
                $end_date = null;

                $is_post_query = false;
                if (!empty($request['exp_date'])) {
                    $start_date = $request['exp_date'];
                    $end_date = $request['exp_date'];
                    $start = $request['exp_date'].' 00:00:00';
                    $end = $request['exp_date'].' 23:59:59';
                    $is_post_query = true;
                } else if (!empty($request['start_date'])) {
                    $start_date = $request['start_date'];
                    $end_date = $request['end_date'];
                    $start = $request['start_date'].' 00:00:00';
                    $end = $request['end_date'].' 23:59:59';
                    $is_post_query = true;
                }else{
                    $start = $now->startOfDay();
                    $end = \Carbon\Carbon::now();
                    $is_post_query = false;
                }
            $invoices = AnSale::where('an_sales.shop_id', $shop->id)->where('invoices.is_deleted', false)->where('an_sales.is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.id as customer_id', 'customers.name as name', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.sale_amount_paid as sale_amount_paid', 'invoices.status as status', 'invoices.id as id', 'invoices.an_sale_id as an_sale_id', 'invoices.inv_no as inv_no', 'vehicle_no', 'invoices.due_date as due_date', 'an_sales.time_created as time_created', 'an_sales.updated_at as updated_at')->orderBy('an_sales.time_created', 'desc')->get();

            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$start_date, $end_date])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.sale_type as sale_type', 'an_sales.comments as comments', 'users.first_name as first_name', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();

            $customer = null;
            $duration = '';
            $myinvoices = AnSale::where('shop_id', $shop->id)->get();

            return view('sales.invoices.index', compact('page', 'title', 'title_sw', 'invoices', 'sales', 'customer', 'duration', 'settings', 'shop','start_date','end_date'));
        }else{
            return redirect('forbiden');
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
    public function show($id, Request $request)
    {
        $page = 'Invoice';
        $title = 'Invoice Preview';
        $title_sw = 'Hakiki ya ankara';

        $shop = Shop::find(Session::get('shop_id'));
        $company = Company::find($shop->company_id);
        $accounts = Account::where('shop_id', $shop->id)->get();
        $baccounts = Account::where('shop_id', $shop->id)->where('type', 'Bank')->groupBy('bank_name')->groupBy('account_name')->select('bank_name', 'account_name', 'branch_name', 'swift_code')->get(); 
        $settings = Setting::where('shop_id', $shop->id)->first();
        $sale = AnSale::where('an_sales.id', decrypt($id))->join('customers', 'customers.id', '=', 'an_sales.customer_id')->join('users', 'users.id', '=', 'an_sales.user_id')->select('first_name', 'last_name', 'customer_id', 'customers.name as name', 'customers.cust_no as cust_no', 'customers.physical_address as ph_address', 'customers.email as email', 'contact_person', 'customers.phone as phone', 'customers.tin as tin', 'customers.vrn as vrn', 'an_sales.id as id', 'invoice_no', 'lpo_no', 'sale_type', 'pay_type', 'is_paid', 'status', 'is_stock_requested', 'an_sales.time_created as time_created', 'due_date', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.tax_amount as tax_amount', 'an_sales.currency as currency', 'an_sales.defcurr as defcurr', 'an_sales.ex_rate as ex_rate', 'note', 'account_id', 'is_full_shipped')->first();
        if (!is_null($sale)) {
            $items = AnSaleItem::where('an_sale_id', $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->groupBy('slug')->orderBy('an_sale_items.time_created', 'desc')->get([
                DB::raw('product_code as product_code'),
                DB::raw('products.name as name'),
                DB::raw('products.slug as slug'),
                DB::raw('an_sale_items.product_id as product_id'),
                DB::raw('an_sale_items.product_unit_id as product_unit_id'),
                DB::raw('SUM(an_sale_items.quantity_sold) as quantity_sold'),
                DB::raw('an_sale_items.retail_price as retail_price'),
                DB::raw('an_sale_items.disc_percent as disc_percent'),
                DB::raw('an_sale_items.discount as discount'),
                DB::raw('SUM(an_sale_items.price) as price'),
                DB::raw('SUM(an_sale_items.total_discount) as total_discount'),
                DB::raw('an_sale_items.tax_amount as tax_amount')
            ]);
            // return $items;
            $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->join('services', 'services.id', '=', 'service_sale_items.service_id')->groupBy('name')->orderBy('service_sale_items.time_created', 'desc')->get([
                DB::raw('code'),
                DB::raw('services.name as name'),
                DB::raw('description'),
                DB::raw('service_sale_items.service_id as service_id'),
                DB::raw('SUM(service_sale_items.no_of_repeatition) as qty'),
                DB::raw('service_sale_items.price as price'),
                DB::raw('SUM(service_sale_items.total) as total'),
                DB::raw('service_sale_items.disc_percent as disc_percent'),
                DB::raw('service_sale_items.discount as discount'),
                DB::raw('SUM(service_sale_items.total_discount) as total_discount'),
            ]);
            
            $oldbalance = SalePayment::where('an_sale_id', $sale->id)->where('is_fresh_pay', false)->where('is_deleted', false)->sum('amount');
            $newpayments = SalePayment::where('an_sale_id', $sale->id)->where('is_fresh_pay', true)->where('is_deleted', false)->sum('amount');
            $payments = SalePayment::where('an_sale_id', $sale->id)->where('is_deleted', false)->get();
            $tspayment = 0;
            foreach ($payments as $key => $value) {
                $tspayment += $value->amount;
            }

            if ($tspayment > $sale->sale_amount_paid && !$sale->is_paid) {
                Log::info('Updating sale status');
                $msale = AnSale::find($sale->id);
                $msale->sale_amount_paid = $tspayment;
                $msale->save();
                $this->updateSaleStatus($msale);
            }

            // return $servitems;
            $date = Carbon::now()->toDayDateTimeString();

            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();

            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
            $stmtcurrencies = array($defcurr, $sale->currency);
            $stmtcurr = $sale->currency;
            if (!empty($request['stmt_currency'])) {
                $stmtcurr = $request['stmt_currency'];
            }
            $ex_rate = 1;
            if($stmtcurr != $defcurr){
                $ex_rate = $sale->ex_rate;
            }

            $triplogs = TripLog::where('an_sale_id', $sale->id)->select('trip_title', 'shipping', 'bill_no')->get();
            if (!is_null($settings->invoice_temp)) {
                return view('sales.invoices.show.'.$settings->invoice_temp, compact('page', 'title', 'title_sw', 'sale', 'items', 'servitems', 'payments', 'newpayments', 'oldbalance', 'company', 'shop', 'accounts', 'baccounts', 'settings', 'date', 'defcurr', 'stmtcurrencies', 'stmtcurr', 'ex_rate', 'currencies', 'triplogs'));
            }else{
                return view('sales.invoices.show', compact('page', 'title', 'title_sw', 'sale', 'items', 'servitems', 'payments', 'newpayments', 'oldbalance', 'company', 'shop', 'accounts', 'baccounts', 'settings', 'date', 'defcurr', 'stmtcurrencies', 'stmtcurr', 'ex_rate', 'currencies', 'triplogs'));
            }
        }else{
            return redirect()->back()->with('info', 'Invoice Not found');
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

    public function accountStmt($id, Request $request)
    {
        $page = 'Account Statement';
        $title = 'Customer Account Statement';
        $title_sw = 'Taarifa ya Akaunti ya Mteja';
        $shop = Shop::find(Session::get('shop_id')); 
        $accounts = Account::where('shop_id', $shop->id)->get();
        $customers = Customer::where('shop_id', $shop->id)->select('id', 'name')->get();
        $customer = Customer::find(decrypt($id));
        if (!empty($request['customer_id'])) {
            $customer = Customer::find($request['customer_id']);
        }
        if (!is_null($customer)) {
            
            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();

            $invoices = AnSale::where('an_sales.shop_id', $shop->id)->where('is_paid', false)->where('an_sales.is_deleted', false)->where('an_sales.customer_id', $customer->id)->select('id', 'invoice_no')->get();
         
            $utransactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $customer->id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->get();
            if (!is_null($utransactions)) {
                foreach ($utransactions as $key => $trans) {
                    $rem_amount = $trans->payment-($trans->trans_invoice_amount+$trans->trans_ob_amount+$trans->trans_credit_amount);
                    if ($rem_amount > 0) {
                        $pinvoices = AnSale::where('customer_id', $customer->id)->where('is_deleted', false)->where('is_paid', false)->get();
                        $curr_amount = $rem_amount;
                        foreach ($pinvoices as $key => $sale) {
                            $tunpaid = ($sale->sale_amount-($sale->sale_discount+$sale->adjustment+$sale->sale_amount_paid));
                            if ($curr_amount > 0) {
                                if ($curr_amount <= $tunpaid) {
                                    $amountpaid = $curr_amount;
                                    $this->clearOldInvoice($sale, $amountpaid, $trans->payment_mode, $trans->bank_name, $trans->branch_name, $trans->cheque_no, $trans->date, $trans->receipt_no, $trans->currency, $trans->defcurr, $trans->ex_rate, '', $trans);
                                }elseif ($curr_amount > $tunpaid) {
                                    $amountpaid = $tunpaid;
                                    $this->clearOldInvoice($sale, $amountpaid, $trans->payment_mode, $trans->bank_name, $trans->branch_name, $trans->cheque_no, $trans->date, $trans->receipt_no, $trans->currency, $trans->defcurr, $trans->ex_rate, '', $trans);
                                }
                            }
                            $curr_amount -= $tunpaid;
                        }
                    }    
                }
            }

            $now = \Carbon\Carbon::now();
            $start = Carbon::now()->startOfMonth();
            $end = \Carbon\Carbon::now();
            $start_date = date('Y-m-d', strtotime($start));            
            $end_date = date('Y-m-d', strtotime($end));
            $is_post_query = false;
            //check if user opted for date range
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

            $inv_before_start = CustomerTransaction::where('customer_id', $customer->id)->where('is_deleted', false)->whereDate('date', '<', $start)->sum('amount');
            $pay_before_start = CustomerTransaction::where('customer_id', $customer->id)->where('is_deleted', false)->whereDate('date', '<', $start)->sum('payment');
            $adj_before_start = CustomerTransaction::where('customer_id', $customer->id)->where('is_deleted', false)->whereDate('date', '<', $start)->sum('adjustment');
            $bal_before_start = $inv_before_start-$pay_before_start-$adj_before_start;
            // return $bal_before_start;

            $transactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $customer->id)->where('is_deleted', false)->whereBetween('date', [$start, $end])->orderBy('date', 'asc')->get();

            $invtrans = CustomerTransaction::where('amount', '>', 0)->where('shop_id', $shop->id)->where('customer_id', $customer->id)->where('is_deleted', false)->whereBetween('date', [$start, $end])->orderBy('date', 'asc')->get();

            $payments = CustomerTransaction::where('payment', '!=', null)->where('shop_id', $shop->id)->where('customer_id', $customer->id)->whereBetween('created_at', [$start, $end])->orderBy('date', 'desc')->get();
            
            $obal = CustomerTransaction::where('customer_id', $customer->id)->where('is_ob', true)->first();
            $settings = Setting::where('shop_id', $shop->id)->first();
            $bdetails = Account::where('shop_id', $shop->id)->get();

            $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
            $senderids = null;
            if (!is_null($smsacc)) {
                $senderids = $smsacc->senderIds()->get();
            }

            $items = null;
            $itemtotals = null;
            $products = null;
            $is_filling_station = false;
            if ($is_filling_station) {
                $items = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('customer_id', $customer->id)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('discount')->groupBy('products.name')->orderBy('name')->get([
                    \DB::raw('products.name as name'),
                    \DB::raw('SUM(quantity_sold) as quantity'),          
                    \DB::raw('retail_price as retail_price'),
                    \DB::raw('SUM(price) as price'),
                    \DB::raw('discount as discount'),
                    \DB::raw('SUM(total_discount) as total_discount')
                ]);


                $itemtotals = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('customer_id', $customer->id)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('products.name')->orderBy('name')->get([
                    \DB::raw('products.name as name'),
                    \DB::raw('SUM(quantity_sold) as quantity'),          
                    \DB::raw('retail_price as retail_price'),
                    \DB::raw('SUM(price) as price'),
                    \DB::raw('discount as discount'),
                    \DB::raw('SUM(total_discount) as total_discount')
                ]);

                $products = $shop->products()->get();
            }

            $stmtcurrencies = array($defcurr);
            $currs = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $customer->id)->where('is_deleted', false)->groupBy('currency')->select('currency')->get();
            foreach ($currs as $key => $value) {
                array_push($stmtcurrencies, $value->currency);
            }

            $stmtcurr = $defcurr;
            if (!empty($request['stmt_currency'])) {
                $stmtcurr = $request['stmt_currency'];
            }
            $supplier = null;
            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            return view('sales.invoices.account-stmt', compact('page', 'title', 'title_sw', 'shop', 'accounts', 'bal_before_start', 'transactions', 'invtrans', 'payments', 'customers', 'customer', 'supplier', 'invoices', 'is_post_query', 'duration', 'duration_sw', 'start_date', 'end_date', 'reporttime', 'settings', 'is_filling_station', 'obal', 'senderids', 'items','itemtotals','products', 'defcurr', 'currencies', 'stmtcurrencies', 'stmtcurr'));
        }else{
            return redirect('/home');
        }
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
        $acctrans = CustomerTransaction::where('customer_id', $request['customer_id'])->where('is_ob', true)->first();
        if (!is_null($acctrans)) {
            $acctrans->amount = $request['amount'];
            $acctrans->ob_paid = $request['ob_paid'];
            $acctrans->date = $opdate;
            $acctrans->save();
        }else{
            $acctrans = new CustomerTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->customer_id = $request['customer_id'];
            $acctrans->is_ob = true;
            $acctrans->amount = $request['amount'];
            $acctrans->currency = $request['currency'];
            $acctrans->date = $opdate;
            $acctrans->save();
        }

        return redirect()->back()->with('success', 'Opening balance was created successfully');
    }
    
    public function showReceipt($id)
    {
        $page = 'Invoice';
        $title = 'Receipt';
        $title_sw = 'Risiti';

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $accpay = CustomerTransaction::find(decrypt($id));
        $customer = Customer::find($accpay->customer_id);
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $currency = $accpay->currency;
        $amount_in_words = $this->convert_number_to_words($accpay->payment+0).' '.$currency.' Only.';

        $sale_payments = SalePayment::where('trans_id', $accpay->id)->join('an_sales', 'an_sales.id', '=', 'sale_payments.an_sale_id')->where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->select('invoice_no', 'an_sales.time_created as date', 'sale_payments.pay_date as pay_date', 'sale_payments.amount as amount')->get();
        
        return view('sales.invoices.receipt', compact('page', 'title', 'title_sw', 'shop', 'settings', 'accpay', 'customer', 'sale_payments', 'amount_in_words'));
    }

    public function accPayments(Request $request)
    {
        return $this->applyPayment($request);
    }

    public function applyPayment($request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $paydate = \Carbon\Carbon::now();

        if (!empty($request['pay_date'])) {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $paydate = $request['pay_date'].' '.$time;
        }

        if (!empty($request['invoice_id'])) {
            $sale = AnSale::find($request['invoice_id']);
            if (!is_null($sale)) {
                $maxrec_no = SalePayment::where('shop_id', $shop->id)->orderByRaw('CONVERT(receipt_no, SIGNED) desc')->first();
                $receipt_no = 0;
                if (!is_null($maxrec_no)) {
                    $receipt_no = $maxrec_no->receipt_no+1;
                }else{
                    $receipt_no = 1;
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
                        return redirect('customer-account-stmt/'.encrypt($sale->customer_id))->with('error', 'Selected Payment Method has no Account. Please Create Account for Cash Payments!.');
                    }
                }elseif ($pay_mode == 'Bank' || $pay_mode == 'Cheque') {
                    if (!empty($request['bank_acc_id'])) {
                        $account = Account::find($request['bank_acc_id']);
                    }else{
                        return redirect('customer-account-stmt/'.encrypt($sale->customer_id))->with('error', 'Selected Payment Method has no Account. Please Create Account for Bank Payments!.');
                    }
                }elseif ($pay_mode == 'Mobile Money') {
                    if (!empty($request['mob_acc_id'])) {
                        $account = Account::find($request['mob_acc_id']);
                    }else{
                        return redirect('customer-account-stmt/'.encrypt($sale->customer_id))->with('error', 'Selected Payment Method has no Account. Please Create Account for Mobile Payments!.');
                    }
                }
                $bank_name = $account->bank_name;
                $branch_name = $account->branch_name;
                
                $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
                $ex_rate = 1;
                $amount = str_replace(',', '', $request['amount']);
                if ($request['currency'] != $defcurr) {
                    if ($request['ex_rate_mode'] == 'foreign') {
                        $local_ex_rate = $request['local_ex_rate'];
                        $ex_rate = 1/$local_ex_rate;
                    }else{
                        $foreign_ex_rate = $request['foreign_ex_rate'];
                        $ex_rate = $foreign_ex_rate;
                    }
                    $amount = str_replace(',', '', $request['amount'])/$ex_rate;
                }
                $acctrans = new CustomerTransaction();
                $acctrans->shop_id = $shop->id;
                $acctrans->user_id = $user->id;
                $acctrans->customer_id = $sale->customer_id;
                $acctrans->invoice_no = $sale->invoice_no;
                $acctrans->receipt_no = $receipt_no;
                $acctrans->payment = $amount;
                $acctrans->trans_invoice_amount = $amount;
                $acctrans->currency = $request['currency'];
                $acctrans->defcurr = $defcurr;
                $acctrans->ex_rate = $ex_rate;
                $acctrans->payment_mode = $pay_mode;
                $acctrans->bank_name = $bank_name;
                $acctrans->bank_branch = $branch_name;
                $acctrans->cheque_no = $cheque_no;
                $acctrans->expire_date = $request['expire_date'];
                $acctrans->date = $paydate;
                $acctrans->save();

                $astmt = new AccountStatement();
                $astmt->shop_id = $shop->id;
                $astmt->user_id = $user->id;
                $astmt->customer_transaction_id = $acctrans->id;
                $astmt->account_id = $account->id;
                $astmt->date = $paydate;
                $astmt->debit = $acctrans->payment;
                $astmt->credit = 0;
                $astmt->description = 'Sales Payment (Receipt No. '.sprintf('%04d', $acctrans->receipt_no).')';
                $astmt->save();       

                $payment = SalePayment::create([
                    'an_sale_id' => $sale->id,
                    'shop_id' => $shop->id,
                    'trans_id' => $acctrans->id,
                    'receipt_no' => $receipt_no,
                    'pay_mode' => $pay_mode,
                    'bank_name' => $bank_name,
                    'bank_branch' => $branch_name,
                    'pay_date' => $paydate,
                    'cheque_no' => $request['cheque_no'],
                    'amount' => $amount,
                    'currency' => $request['currency'],
                    'defcurr' => $defcurr,
                    'ex_rate' => $ex_rate,
                    'comments' => $request['comments']
                ]);

                if ($payment) {
                    $sale->sale_amount_paid = $sale->sale_amount_paid+$payment->amount;
                    $sale->save();
                    $this->updateSaleStatus($sale);

                    if ($settings->is_cm_business) {
                        $this->updateContractStatus($sale, $payment);
                    }

                    $payment_mode = null;
                    $cheque_no = $request['cheque_no'];
                    if ($request['pay_mode'] == 'Bank') {
                        $payment_mode = $request['deposit_mode'];
                        $cheque_no = $request['slip_no'];
                    }else{
                        $payment_mode = $request['pay_mode'];
                    }

                    
                    //Send SMS to Customer
                    $tamount = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->where('is_deleted', false)->sum('amount');
                    $trefund =  CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->where('is_deleted', false)->sum('refund_amt');
                    $tpayment = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->where('is_deleted', false)->sum('payment');
                    $tadjst = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->where('is_deleted', false)->sum('adjustment');
                    $tbalance = ($tamount+$trefund)-($tpayment+$tadjst);

                    $cust = Customer::where('id', $sale->customer_id)->whereNotNull('phone')->first();
                    if (!is_null($cust)) {
                        
                        if (!is_null($this->formattedNumber($cust->phone))) {
                            $phone = $this->formattedNumber($cust->phone);
                            $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
                            if (!is_null($smsacc)) {
                                $senderid = SenderId::where('sms_account_id', $smsacc->id)->where('auto_sms', true)->first();
                                if (!is_null($senderid)) {
                                    $autotemp = SmsTemplate::where('shop_id', $shop->id)->where('is_auto_sms', true)->where('temp_for', 'cust_pay')->first();
                                    if (!is_null($autotemp)) {
                                        $message = $autotemp->message;
                                        $numbers = [$phone];
                                        $amount_due = $tbalance;
                                        $sms = str_replace('{customer_name}', $cust->name, $message);
                                        $msg = str_replace('{amount_due}', number_format($amount_due), $sms);

                                        $token = '8b49c1406246765709bfdbaa6b8a9232';
                                        $sender = $senderid->name;
                                        $client = new \GuzzleHttp\Client();
                                        $url = "https://ovalbsms.co.tz/api/send-sms";
                                        $data = array(
                                            'form_params' => array(
                                                'username' => $smsacc->username,
                                                'password' => $smsacc->password,
                                                'sender' => $sender,
                                                'receiver' =>array($phone),
                                                'message' => $msg,
                                            ),
                                            'verify' => false,
                                            'headers' => [
                                                'Authorization' => 'Bearer '.$token,
                                                'Accept' => 'application/json',
                                            ],
                                        );
                                        $req = $client->post($url,  $data);
                                        $response = $req->getBody();
                                        $result = json_decode($response, true);
                                        Log::info($result);
                                    }
                                }
                            }
                        }else{
                            Log::info($cust->phone.' is not valid mobile number');
                        }
                    }
                }

                $success = 'Payments were added successful';
                return redirect()->back()->with('success', $success);
            }
        }else{
            
            $pay_mode = null;
            if ($request['pay_mode'] == 'Cheque') {
                $pay_mode = 'Bank';
            }else{
                $pay_mode = $request['pay_mode'];
            }

            $account = null;
            if ($pay_mode == 'Cash') {
                if (!empty($request['cash_acc_id'])) {
                    $account = Account::find($request['cash_acc_id']);
                }else{
                    return redirect('customer-account-stmt/'.encrypt($request['customer_id']))->with('error', 'Selected Payment Method has no Account. Please Create Account for Cash Payments!.');
                }
            }elseif ($pay_mode == 'Bank' || $pay_mode == 'Cheque') {
                if (!empty($request['bank_acc_id'])) {
                    $account = Account::find($request['bank_acc_id']);
                }else{
                    return redirect('customer-account-stmt/'.encrypt($request['customer_id']))->with('error', 'Selected Payment Method has no Account. Please Create Account for Bank Payments!.');
                }
            }elseif ($pay_mode == 'Mobile Money') {
                if (!empty($request['mob_acc_id'])) {
                    $account = Account::find($request['mob_acc_id']);
                }else{
                    return redirect('customer-account-stmt/'.encrypt($request['customer_id']))->with('error', 'Selected Payment Method has no Account. Please Create Account for Mobile Payments!.');
                }
            }
            $bank_name = $account->bank_name;
            $branch_name = $account->branch_name;
            
            $amount = str_replace(',', '', $request['amount']);
            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
            $ex_rate = 1;
            if ($request['currency'] != $defcurr) {
                if ($request['ex_rate_mode'] == 'Foreign' && $request['local_ex_rate'] > 0) {
                    $local_ex_rate = $request['local_ex_rate'];
                    $ex_rate = 1/$local_ex_rate;
                }else{
                    if ($request['foreign_ex_rate'] > 0) {
                        $foreign_ex_rate = $request['foreign_ex_rate'];             
                        $ex_rate = $foreign_ex_rate;
                    }
                }
                $amount = str_replace(',', '', $request['amount'])/$ex_rate;
            }
            
            $payment_mode = null;
            $cheque_no = $request['cheque_no'];
            if ($request['pay_mode'] == 'Bank') {
                $payment_mode = $request['deposit_mode'];
                $cheque_no = $request['slip_no'];
            }else{
                $payment_mode = $request['pay_mode'];
            }
            
            $maxrec_no = SalePayment::where('shop_id', $shop->id)->orderByRaw('CONVERT(receipt_no, SIGNED) desc')->first();
            $receipt_no = 0;
            if (!is_null($maxrec_no)) {                    
                $receipt_no = $maxrec_no->receipt_no+1;
            }else{
                $receipt_no = 1;
            }

            $acctrans = new CustomerTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->customer_id = $request['customer_id'];
            $acctrans->receipt_no = $receipt_no;
            $acctrans->payment = $amount;
            $acctrans->date = $paydate;
            $acctrans->save();

            $astmt = new AccountStatement();
            $astmt->shop_id = $shop->id;
            $astmt->user_id = $user->id;
            $astmt->customer_transaction_id = $acctrans->id;
            $astmt->account_id = $account->id;
            $astmt->date = $paydate;
            $astmt->debit = $acctrans->payment;
            $astmt->credit = 0;
            $astmt->description = 'Sales Payment (Receipt No. '.sprintf('%04d', $acctrans->receipt_no).')';
            $astmt->save();
            
            $rem_amount = 0;
            $trans_ob_amount = 0;
            $obtrans = CustomerTransaction::where('customer_id', $request['customer_id'])->where('is_ob', true)->where('shop_id', $shop->id)->first();
            if (!is_null($obtrans)) {
                $ob_pending = $obtrans->amount-$obtrans->ob_paid;
                if ($ob_pending > 0) {
                    if ($ob_pending >= $amount) {
                        $trans_ob_amount = $amount;
                        $obtrans->ob_paid = $obtrans->ob_paid+$amount;
                        $obtrans->save();
                        $cashin = new CashIn();
                        $cashin->shop_id = $shop->id;
                        $cashin->account_id = $account->id;
                        $cashin->trans_id = $acctrans->id;
                        $cashin->amount = $amount;
                        $cashin->source = 'Customer Opening balance payment';
                        $cashin->in_date = $paydate;
                        $cashin->save();
                    }else{
                        $obtrans->ob_paid = $obtrans->ob_paid+$ob_pending;
                        $obtrans->save();
                        $trans_ob_amount = $ob_pending;
                        $rem_amount = $amount-$ob_pending;
                        $cashin = new CashIn();
                        $cashin->shop_id = $shop->id;
                        $cashin->account_id = $account->id;
                        $cashin->trans_id = $acctrans->id;
                        $cashin->amount = $ob_pending;
                        $cashin->source = 'Customer Opening balance payment';
                        $cashin->in_date = $paydate;
                        $cashin->save();
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
                $cashcredits = CashOut::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->where('is_borrowed', true)->where('status', 'Pending')->get();
                if (!is_null($cashcredits)) {
                    foreach ($cashcredits as $key => $credit) {
                        $pendcr = $credit->amount-$credit->amount_paid;
                        if ($rem_amount > 0) {
                            if ($rem_amount <= $pendcr) {
                                $credit->amount_paid = $credit->amount_paid+$rem_amount;
                                $credit->save();
                                $cashin = new CashIn();
                                $cashin->shop_id = $shop->id;
                                $cashin->account_id = $account->id;
                                $cashin->trans_id = $acctrans->id;
                                $cashin->cash_out_id = $credit->id;
                                $cashin->amount = $rem_amount;
                                $cashin->source = 'Customer Cash Debts payments';
                                $cashin->in_date = $paydate;
                                $cashin->save();
                            }else{
                                $credit->amount_paid = $credit->amount_paid+$pendcr;
                                $credit->save();

                                $cashin = new CashIn();
                                $cashin->shop_id = $shop->id;
                                $cashin->account_id = $account->id;
                                $cashin->trans_id = $acctrans->id;
                                $cashin->cash_out_id = $credit->id;
                                $cashin->amount = $pendcr;
                                $cashin->source = 'Customer Cash Debts payments';
                                $cashin->in_date = $paydate;
                                $cashin->save();
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
            
            $acctrans->trans_ob_amount = $trans_ob_amount;
            $acctrans->trans_credit_amount = $trans_credit_amount;
            $acctrans->is_utilized = false;
            $acctrans->currency = $request['currency'];
            $acctrans->defcurr = $defcurr;
            $acctrans->ex_rate = $ex_rate;
            $acctrans->payment_mode = $payment_mode;
            $acctrans->bank_name = $bank_name;
            $acctrans->bank_branch = $branch_name;
            $acctrans->cheque_no = $cheque_no;
            $acctrans->expire_date = $request['expire_date'];
            $acctrans->date = $paydate;
            $acctrans->save();

            //Pending Invoices
            if ($rem_amount > 0) {
                $pinvoices = AnSale::where('shop_id', $shop->id)->where('is_paid', false)->where('an_sales.is_deleted', false)->where('an_sales.customer_id', $request['customer_id'])->get();
                $curr_amount = $rem_amount;
                foreach ($pinvoices as $key => $sale) {
                    $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                    $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
                    $netsales_amount = $tnetsales-$tnetreturn;
                    $tunpaid = $netsales_amount-$sale->sale_amount_paid;
                    if ($curr_amount > 0) {
                        if ($curr_amount <= $tunpaid) {
                            $amountpaid = $curr_amount;
                            $this->clearOldInvoice($sale, $amountpaid, $pay_mode, $bank_name, $branch_name, $cheque_no, $paydate, $receipt_no, $request['currency'], $defcurr, $ex_rate, $request['comments'], $acctrans);
                        }elseif ($curr_amount > $tunpaid) {
                            $amountpaid = $tunpaid;
                            $this->clearOldInvoice($sale, $amountpaid, $pay_mode, $bank_name, $branch_name, $cheque_no, $paydate, $receipt_no, $request['currency'], $defcurr, $ex_rate, $request['comments'], $acctrans);
                        }
                    }
                    $curr_amount -= $tunpaid;
                }
            }

            //Send SMS to Customer
            $tamount = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->where('is_deleted', false)->sum('amount');
            $trefund =  CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->where('is_deleted', false)->sum('refund_amt');
            $tpayment = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->where('is_deleted', false)->sum('payment');
            $tadjst = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->where('is_deleted', false)->sum('adjustment');
            $tbalance = ($tamount+$trefund)-($tpayment+$tadjst);

            $cust = Customer::find($request['customer_id']);
            if (!is_null($cust)) {
                // Check customer balance and limitation
                $custbalance = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $cust->id)->select(\DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();
                if ($cust->due_amount_limit > 0 && $custbalance->amount < $cust->due_amount_limit) {
                    $cust->is_active = true;
                    $cust->save();
                }
                if (!is_null($this->formattedNumber($cust->phone))) {
                    $phone = $this->formattedNumber($cust->phone);
                    $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
                    if (!is_null($smsacc)) {
                        $senderid = SenderId::where('sms_account_id', $smsacc->id)->where('auto_sms', true)->first();
                        if (!is_null($senderid)) {
                            $autotemp = SmsTemplate::where('shop_id', $shop->id)->where('is_auto_sms', true)->where('temp_for', 'cust_pay')->first();
                            if (!is_null($autotemp)) {
                                $message = $autotemp->message;
                                $numbers = [$phone];
                                $amount_due = $tbalance;
                                $sms = str_replace('{customer_name}', $cust->name, $message);
                                $msg = str_replace('{amount_due}', number_format($amount_due), $sms);

                                $token = '8b49c1406246765709bfdbaa6b8a9232';
                                $sender = $senderid->name;
                                $client = new \GuzzleHttp\Client();
                                $url = "https://ovalbsms.co.tz/api/send-sms";
                                $data = array(
                                    'form_params' => array(
                                        'username' => $smsacc->username,
                                        'password' => $smsacc->password,
                                        'sender' => $sender,
                                        'receiver' =>array($phone),
                                        'message' => $msg,
                                    ),
                                    'verify' => false,
                                    'headers' => [
                                        'Authorization' => 'Bearer '.$token,
                                        'Accept' => 'application/json',
                                    ],
                                );
                                $req = $client->post($url,  $data);
                                $response = $req->getBody();
                                $result = json_decode($response, true);
                                Log::info($result);
                            }
                        }
                    }
                }else{
                    Log::info($cust->phone.' is not valid mobile number');
                }
            }       
            $success = 'Payments were added successful';
            return redirect()->back()->with('success', $success);
        }
    }

    public function clearOldInvoice($sale, $amount, $pay_mode, $bank_name, $bank_branch, $cheque_no, $paydate, $receipt_no, $currency, $defcurr, $ex_rate, $comments, $acctrans)
    {   
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        if (!is_null($sale)) {
            $payment = SalePayment::create([
                'an_sale_id' => $sale->id,
                'shop_id' => $shop->id,
                'trans_id' => $acctrans->id,
                'receipt_no' => $receipt_no,
                'pay_mode' => $pay_mode,
                'bank_name' => $bank_name,
                'bank_branch' => $bank_branch,
                'cheque_no' => $cheque_no,
                'pay_date' => $paydate,
                'amount' => $amount,
                'currency' => $currency,
                'defcurr' => $defcurr,
                'ex_rate' => $ex_rate,
                'comments' => $comments
            ]);

            if ($payment) {
                $transpays = SalePayment::where('trans_id', $acctrans->id)->get();
                $inv_amount = 0;
                foreach ($transpays as $key => $value) {
                    $inv_amount += $value->amount;
                }
                $acctrans->trans_invoice_amount = $inv_amount;
                $acctrans->save();
                if (($acctrans->payment-($acctrans->trans_invoice_amount+$acctrans->trans_ob_amount+$acctrans->trans_credit_amount)) == 0){
                    $acctrans->is_utilized = true;
                    $acctrans->save();
                }

                $sale->sale_amount_paid = $sale->sale_amount_paid+$payment->amount;
                $sale->save();
                $this->updateSaleStatus($sale);

                if ($settings->is_cm_business) {
                    $this->updateContractStatus($sale, $payment);
                }
            }
        }
    }

    public function updateSaleStatus($sale)
    {
        $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
        $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
        $netsales_amount = $tnetsales-$tnetreturn;
        if ($netsales_amount == $sale->sale_amount_paid) {
            $sale->status = 'Paid';
            $sale->is_paid = true;
            $sale->time_paid = \Carbon\Carbon::now();
            $sale->save();
        }elseif ($netsales_amount > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
            $sale->status = 'Partially Paid';
            $sale->time_paid = null;
            $sale->is_paid = false;
            $sale->save();
        }elseif ($netsales_amount < $sale->sale_amount_paid) {
            $sale->status = 'Excess Paid';
            $sale->is_paid = true;
            $sale->time_paid = \Carbon\Carbon::now();
            $sale->save();
        }else{
            $sale->status = 'Unpaid';
            $sale->time_paid = null;
            $sale->is_paid = false;
            $sale->save();
        }

        $booking = Booking::where('an_sale_id', $sale->id)->where('shop_id', $sale->shop_id)->first();
        if (!is_null($booking)) {
            $booking->status = 'Paid';
            $booking->save();
        }
    }

    public function updateContractStatus($sale, $payment)
    {
        $contract = Contract::where('an_sale_id', $sale->id)->first();
        if (!is_null($contract)) {
            $incservices = ContractService::where('contract_id', $contract->id)->where('is_add_on', 1)->sum('total');
            if ($sale->sale_amount_paid > $incservices) {
                $spays = SalePayment::where('an_sale_id', $sale->id)->count();
                if ($spays > 1) {
                    $cservice = ContractService::where('contract_id', $contract->id)->where('is_add_on', 0)->first();
                    if (!is_null($cservice)) {
                        $paiddays = $payment->amount/$cservice->unit_price;
                        $lddeposit = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'desc')->first();
                        if (!is_null($lddeposit)) {
                            $currdate = $lddeposit->date;
                            for ($i=0; $i < $paiddays; $i++) {
                                $date = strtotime("+1 day", strtotime($currdate));
                                $currdate = date("Y-m-d", $date);
                                $deposit = new DailyDeposit();
                                $deposit->contract_id = $contract->id;
                                $deposit->sale_payment_id = $payment->id;
                                $deposit->date = $currdate;
                                $deposit->amount = $cservice->unit_price;
                                $deposit->save();
                            }
                        }else{
                            $currdate = $contract->start_date;
                            for ($i=0; $i < $paiddays; $i++) {
                                $date = strtotime("+".$i." day", strtotime($currdate));
                                if ($i > 0) {
                                    $date = strtotime("+1 day", strtotime($currdate));
                                }
                                $currdate = date("Y-m-d", $date);
                                $deposit = new DailyDeposit();
                                $deposit->contract_id = $contract->id;
                                $deposit->sale_payment_id = $payment->id;
                                $deposit->date = $currdate;
                                $deposit->amount = $cservice->unit_price;
                                $deposit->save();
                            }
                        }

                        $days_worked = DailyDeposit::where('contract_id', $contract->id)->count();
                        $contract->days_worked = $days_worked;
                        $contract->amount_paid = $days_worked*$cservice->unit_price;
                        if ($contract->amount <= $contract->amount_paid) {
                            $lastdeposit = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'desc')->first();
                            $contract->status = 'Graduated';
                            $contract->actual_end_date = $lastdeposit->date;
                            $contract->terminated_at = Carbon::now();
                        }else{
                            $contract->status = 'Working';
                        }
                        $contract->save();
                    }
                }else{
                    $cservice = ContractService::where('contract_id', $contract->id)->where('is_add_on', 0)->first();
                    if (!is_null($cservice)) {
                        $amount = $sale->sale_amount_paid-$incservices;
                        $paiddays = $amount/$cservice->unit_price;
                        $lddeposit = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'desc')->first();
                        if (!is_null($lddeposit)) {
                            $currdate = $lddeposit->date;
                            for ($i=0; $i < $paiddays; $i++) {
                                $date = strtotime("+1 day", strtotime($currdate));
                                $currdate = date("Y-m-d", $date);
                                $deposit = new DailyDeposit();
                                $deposit->contract_id = $contract->id;
                                $deposit->sale_payment_id = $payment->id;
                                $deposit->date = $currdate;
                                $deposit->amount = $cservice->unit_price;
                                $deposit->save();
                            }
                        }else{
                            $currdate = $contract->start_date;
                            for ($i=0; $i < $paiddays; $i++) {
                                $date = strtotime("+".$i." day", strtotime($currdate));
                                if ($i > 0) {
                                    $date = strtotime("+1 day", strtotime($currdate));
                                }
                                $currdate = date("Y-m-d", $date);
                                $deposit = new DailyDeposit();
                                $deposit->contract_id = $contract->id;
                                $deposit->sale_payment_id = $payment->id;
                                $deposit->date = $currdate;
                                $deposit->amount = $cservice->unit_price;
                                $deposit->save();
                            }
                        }

                        $days_worked = DailyDeposit::where('contract_id', $contract->id)->count();
                        $contract->days_worked = $days_worked;
                        $contract->amount_paid = $days_worked*$cservice->unit_price;
                        if ($contract->amount <= $contract->amount_paid) {
                            $lastdeposit = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'desc')->first();
                            $contract->status = 'Graduated';
                            $contract->actual_end_date = $lastdeposit->date;
                            $contract->terminated_at = Carbon::now();
                        }else{
                            $contract->status = 'Working';
                        }
                        $contract->save();
                    }
                }
            }else{
                Log::info('Paid Bond and Registration fee');
                $contract->status = 'Working';
                $contract->save();
            }
        }else{
            Log::info('Contract not Found');
        }
    }

    public function deleteTrans($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $acctrans = CustomerTransaction::find(decrypt($id));
        if (!is_null($acctrans)) {
            $invoice = AnSale::find($acctrans->invoice_id);
            if (is_null($invoice)) {
                $acctrans->delete();
            }
        }

        return redirect()->back()->with('success', 'Record was removed successfully');
    }

    public function editPaymentTrans($id)
    {
        $page = 'Edit Payment';
        $title = 'Edit Payment';
        $shop = Shop::find(Session::get('shop_id'));
        $accounts = Account::where('shop_id', $shop->id)->get();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $trans = CustomerTransaction::find(decrypt($id));
        $astmt = AccountStatement::where('customer_transaction_id', $trans->id)->first();

        return view('sales.payments.edit', compact('page', 'title', 'shop', 'settings', 'accounts', 'defcurr', 'currencies', 'trans', 'astmt'));
    }

    public function updatePaymentTrans(Request $request)
    {
        $acctrans = CustomerTransaction::find($request['id']);
        if (!is_null($acctrans)) {
            $shop = Shop::find($acctrans->shop_id);
            $user = Auth::user();
            $settings = Setting::where('shop_id', $shop->id)->first();
            $paydate = \Carbon\Carbon::now();

            if (!empty($request['pay_date'])) {
                $timenow = Carbon::now();
                $time = date('H:i:s', strtotime($timenow));
                $paydate = $request['pay_date'].' '.$time;
            }


            $pay_mode = null;
            if ($request['pay_mode'] == 'Cheque') {
                $pay_mode = 'Bank';
            }else{
                $pay_mode = $request['pay_mode'];
            }

            $account = null;
            if ($pay_mode == 'Cash') {
                if (!empty($request['cash_acc_id'])) {
                    $account = Account::find($request['cash_acc_id']);
                }else{
                    return redirect('customer-account-stmt/'.encrypt($request['customer_id']))->with('error', 'Selected Payment Method has no Account. Please Create Account for Cash Payments!.');
                }
            }elseif ($pay_mode == 'Bank' || $pay_mode == 'Cheque') {
                if (!empty($request['bank_acc_id'])) {
                    $account = Account::find($request['bank_acc_id']);
                }else{
                    return redirect('customer-account-stmt/'.encrypt($request['customer_id']))->with('error', 'Selected Payment Method has no Account. Please Create Account for Bank Payments!.');
                }
            }elseif ($pay_mode == 'Mobile Money') {
                if (!empty($request['mob_acc_id'])) {
                    $account = Account::find($request['mob_acc_id']);
                }else{
                    return redirect('customer-account-stmt/'.encrypt($request['customer_id']))->with('error', 'Selected Payment Method has no Account. Please Create Account for Mobile Payments!.');
                }
            }
            $bank_name = $account->bank_name;
            $branch_name = $account->branch_name;
            
            $amount = $request['amount'];
            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
            $ex_rate = 1;
            if ($request['currency'] != $defcurr) {
                if ($request['ex_rate_mode'] == 'Foreign' && $request['local_ex_rate'] > 0) {
                    $local_ex_rate = $request['local_ex_rate'];
                    $ex_rate = 1/$local_ex_rate;
                }else{
                    if ($request['foreign_ex_rate'] > 0) {
                        $foreign_ex_rate = $request['foreign_ex_rate'];             
                        $ex_rate = $foreign_ex_rate;
                    }
                }
                $amount = $request['amount']/$ex_rate;
            }
            
            $payment_mode = null;
            $cheque_no = $request['cheque_no'];
            if ($request['pay_mode'] == 'Bank') {
                $payment_mode = $request['deposit_mode'];
                $cheque_no = $request['slip_no'];
            }else{
                $payment_mode = $request['pay_mode'];
            }
            
            $acctrans->date = $paydate;
            $acctrans->payment = $amount;
            $acctrans->save();

            $astmt = AccountStatement::where('customer_transaction_id', $acctrans->id)->first();
            if (is_null($astmt)) {
                $astmt = new AccountStatement();
            }
            $astmt->shop_id = $shop->id;
            $astmt->user_id = $user->id;
            $astmt->customer_transaction_id = $acctrans->id;
            $astmt->account_id = $account->id;
            $astmt->date = $paydate;
            $astmt->debit = $acctrans->payment;
            $astmt->credit = 0;
            $astmt->description = 'Sales Payment (Receipt No. '.sprintf('%04d', $acctrans->receipt_no).')';
            $astmt->save();
            
            if ($acctrans->trans_ob_amount > 0) {
                $obtrans = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $acctrans->customer_id)->where('is_ob', true)->first();
                if (!is_null($obtrans)) {
                    $obtrans->ob_paid = $obtrans->ob_paid-$acctrans->trans_ob_amount;
                    $obtrans->save();
                }
            }

            $cashins = CashIn::where('trans_id', $acctrans->id)->get();
            foreach ($cashins as $key => $ins) {
                $cashout = CashOut::find($ins->cash_out_id);
                if (!is_null($cashout)) {
                    $cashout->amount_paid = $cashout->amount_paid-$ins->amount;
                    $cashout->status = 'Pending';
                    $cashout->save();
                }
                $ins->delete();
            }

            $sale_payments = SalePayment::where('trans_id', $acctrans->id)->where('shop_id', $shop->id)->get();
            if ($sale_payments->count() == 1) {
                $payment = SalePayment::where('trans_id', $acctrans->id)->where('shop_id', $shop->id)->first();
                $payment->amount = $acctrans->payment;
                $payment->pay_date = $paydate;
                $payment->pay_mode = $pay_mode;
                $payment->save();
                
                $sale = AnSale::find($payment->an_sale_id);
                if (!is_null($sale)) {
                    if ($settings->is_cm_business) {
                        $deposits = DailyDeposit::where('sale_payment_id', $payment->id)->get();
                        foreach ($deposits as $key => $value) {
                            $value->delete();
                        }

                        $this->updateContract($sale);

                        $this->updateContractStatus($sale, $payment);
                    }

                    $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                    $amount_paid = 0;
                    foreach ($payments as $key => $pay) {
                        $amount_paid += $pay->amount;
                    }

                    $sale->sale_amount_paid = $amount_paid;
                    $sale->save();
                    $this->updateSaleStatus($sale);
                }
                $acctrans->is_utilized = false;
                $acctrans->currency = $request['currency'];
                $acctrans->defcurr = $defcurr;
                $acctrans->ex_rate = $ex_rate;
                $acctrans->payment_mode = $payment_mode;
                $acctrans->bank_name = $bank_name;
                $acctrans->bank_branch = $branch_name;
                $acctrans->cheque_no = $cheque_no;
                $acctrans->expire_date = $request['expire_date'];
                $acctrans->save();
            }else{
                foreach ($sale_payments as $key => $payment) {
                    $sale = AnSale::find($payment->an_sale_id);
                    if ($settings->is_cm_business) {
                        $deposits = DailyDeposit::where('sale_payment_id', $payment->id)->get();
                        foreach ($deposits as $key => $value) {
                            $value->delete();
                        }

                        $this->updateContract($sale);
                    }

                    $payment->delete();
                    if ($sale->sale_amount_paid > 0) {
                        $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                        $amount_paid = 0;
                        foreach ($payments as $key => $pay) {
                            $amount_paid += $pay->amount;
                        }

                        $sale->sale_amount_paid = $amount_paid;
                        $sale->save();
                        $this->updateSaleStatus($sale);
                    }
                }

                $rem_amount = 0;
                $trans_ob_amount = 0;
                $obtrans = CustomerTransaction::where('customer_id', $request['customer_id'])->where('is_ob', true)->where('shop_id', $shop->id)->first();
                if (!is_null($obtrans)) {
                    $ob_pending = $obtrans->amount-$obtrans->ob_paid;
                    if ($ob_pending > 0) {
                        if ($ob_pending >= $amount) {
                            $trans_ob_amount = $amount;
                            $obtrans->ob_paid = $obtrans->ob_paid+$amount;
                            $obtrans->save();
                            $cashin = new CashIn();
                            $cashin->shop_id = $shop->id;
                            $cashin->account_id = $account->id;
                            $cashin->trans_id = $acctrans->id;
                            $cashin->amount = $amount;
                            $cashin->source = 'Customer Opening balance payment';
                            $cashin->in_date = $paydate;
                            $cashin->save();
                        }else{
                            $obtrans->ob_paid = $obtrans->ob_paid+$ob_pending;
                            $obtrans->save();
                            $trans_ob_amount = $ob_pending;
                            $rem_amount = $amount-$ob_pending;
                            $cashin = new CashIn();
                            $cashin->shop_id = $shop->id;
                            $cashin->account_id = $account->id;
                            $cashin->trans_id = $acctrans->id;
                            $cashin->amount = $ob_pending;
                            $cashin->source = 'Customer Opening balance payment';
                            $cashin->in_date = $paydate;
                            $cashin->save();
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
                    $cashcredits = CashOut::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->where('is_borrowed', true)->where('status', 'Pending')->get();
                    if (!is_null($cashcredits)) {
                        foreach ($cashcredits as $key => $credit) {
                            $pendcr = $credit->amount-$credit->amount_paid;
                            if ($rem_amount > 0) {
                                if ($rem_amount <= $pendcr) {
                                    $credit->amount_paid = $credit->amount_paid+$rem_amount;
                                    $credit->save();
                                    $cashin = new CashIn();
                                    $cashin->shop_id = $shop->id;
                                    $cashin->account_id = $account->id;
                                    $cashin->trans_id = $acctrans->id;
                                    $cashin->cash_out_id = $credit->id;
                                    $cashin->amount = $rem_amount;
                                    $cashin->source = 'Customer Cash Debts payments';
                                    $cashin->in_date = $paydate;
                                    $cashin->save();
                                }else{
                                    $credit->amount_paid = $credit->amount_paid+$pendcr;
                                    $credit->save();

                                    $cashin = new CashIn();
                                    $cashin->shop_id = $shop->id;
                                    $cashin->account_id = $account->id;
                                    $cashin->trans_id = $acctrans->id;
                                    $cashin->cash_out_id = $credit->id;
                                    $cashin->amount = $pendcr;
                                    $cashin->source = 'Customer Cash Debts payments';
                                    $cashin->in_date = $paydate;
                                    $cashin->save();
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
                
                $acctrans->trans_ob_amount = $trans_ob_amount;
                $acctrans->trans_credit_amount = $trans_credit_amount;
                $acctrans->is_utilized = false;
                $acctrans->currency = $request['currency'];
                $acctrans->defcurr = $defcurr;
                $acctrans->ex_rate = $ex_rate;
                $acctrans->payment_mode = $payment_mode;
                $acctrans->bank_name = $bank_name;
                $acctrans->bank_branch = $branch_name;
                $acctrans->cheque_no = $cheque_no;
                $acctrans->expire_date = $request['expire_date'];
                $acctrans->date = $paydate;
                $acctrans->save();

                $receipt_no = $acctrans->receipt_no;
                //Pending Invoices
                if ($rem_amount > 0) {
                    $pinvoices = AnSale::where('shop_id', $shop->id)->where('is_paid', false)->where('an_sales.is_deleted', false)->where('an_sales.customer_id', $acctrans->customer_id)->get();
                    $curr_amount = $rem_amount;
                    foreach ($pinvoices as $key => $sale) {
                        $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                        $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
                        $netsales_amount = $tnetsales-$tnetreturn;
                        $tunpaid = $netsales_amount-$sale->sale_amount_paid;
                        if ($curr_amount > 0) {
                            if ($curr_amount <= $tunpaid) {
                                $amountpaid = $curr_amount;
                                $this->clearOldInvoice($sale, $amountpaid, $pay_mode, $bank_name, $branch_name, $cheque_no, $paydate, $receipt_no, $request['currency'], $defcurr, $ex_rate, $request['comments'], $acctrans);
                            }elseif ($curr_amount > $tunpaid) {
                                $amountpaid = $tunpaid;
                                $this->clearOldInvoice($sale, $amountpaid, $pay_mode, $bank_name, $branch_name, $cheque_no, $paydate, $receipt_no, $request['currency'], $defcurr, $ex_rate, $request['comments'], $acctrans);
                            }
                        }
                        $curr_amount -= $tunpaid;
                    }
                }
            }

            return redirect('customer-account-stmt/'.encrypt($acctrans->customer_id))->with('success', 'Payment updated successfully');
        }
    }

    public function deletePayment($receipt_no)
    {
        $accpay = CustomerTransaction::find(decrypt($receipt_no));
        $this->applyDelete($accpay);

        return redirect()->back()->with('success', 'Payments were deleted successful');
    }

    public function applyDelete($accpay)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        if (!is_null($accpay)) {
            if ($accpay->trans_ob_amount > 0) {
                $obtrans = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $accpay->customer_id)->where('is_ob', true)->first();
                if (!is_null($obtrans)) {
                    $obtrans->ob_paid = $obtrans->ob_paid-$accpay->trans_ob_amount;
                    $obtrans->save();
                }
            }

            $cashins = CashIn::where('trans_id', $accpay->id)->get();
            foreach ($cashins as $key => $ins) {
                $cashout = CashOut::find($ins->cash_out_id);
                if (!is_null($cashout)) {
                    $cashout->amount_paid = $cashout->amount_paid-$ins->amount;
                    $cashout->status = 'Pending';
                    $cashout->save();
                }
                $ins->delete();
            }

            $sale_payments = SalePayment::where('trans_id', $accpay->id)->where('shop_id', $shop->id)->get();
            foreach ($sale_payments as $key => $payment) {

                $sale = AnSale::find($payment->an_sale_id);
                if ($settings->is_cm_business) {
                    $deposits = DailyDeposit::where('sale_payment_id', $payment->id)->get();
                    foreach ($deposits as $key => $value) {
                        $value->delete();
                    }

                    $this->updateContract($sale);
                }
                $payment->delete();
                if ($sale->sale_amount_paid > 0) {
                    $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                    $amount_paid = 0;
                    foreach ($payments as $key => $pay) {
                        $amount_paid += $pay->amount;
                    }

                    $sale->sale_amount_paid = $amount_paid;
                    $sale->save();
                    $this->updateSaleStatus($sale);
                }
            }
            $accpay->delete();
        }
    }

    public function updateContract($sale)
    {
        $contract = Contract::where('an_sale_id', $sale->id)->first();
        if (!is_null($contract)) {
            $deposits = DailyDeposit::where('contract_id', $contract->id)->sum('amount');
            $days_worked = DailyDeposit::where('contract_id', $contract->id)->count();
            
            $contract->amount_paid = $deposits;
            $contract->days_worked = $days_worked;
            $contract->status = 'Working';
            $contract->save();
        }
    }

    public function invoiceReport(Request $request)
    {
        $page = 'Reports';
        $title = 'Invoices Reports';
        $title_sw = 'Ripoti za Ankara';

        $shop = Shop::find(Session::get('shop_id'));
        $customers = Customer::where('shop_id', $shop->id)->get(); 
        $customer = Customer::where('id', $request['customer_id'])->where('shop_id', $shop->id)->first();
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = \Carbon\Carbon::now();
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

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        if (!is_null($customer)) {
            $invoices = AnSale::where('an_sales.shop_id', $shop->id)->whereBetween('an_sales.time_created', [$start, $end])->where('an_sales.is_deleted', false)->where('an_sales.customer_id', $customer->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->join('products', 'products.id', '=', 'an_sale_items.product_id')->select('an_sales.time_created as date', 'invoice_no', 'customers.name as customer', 'vehicle_no', 'due_date', 'products.name as name', 'an_sale_items.quantity_sold as qty', 'an_sale_items.retail_price as price', 'an_sale_items.discount as discount', 'an_sale_items.tax_amount as tax_amount')->orderBy('date', 'desc')->get();

            $allinvoices = AnSale::where('an_sales.shop_id', $shop->id)->whereBetween('an_sales.time_created', [$start, $end])->where('an_sales.is_deleted', false)->where('an_sales.customer_id', $customer->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('invoice_no', 'due_date', 'an_sales.status as status', 'an_sales.time_created as time_created', 'an_sales.sale_amount as amount', 'an_sales.sale_discount as discount', 'an_sales.tax_amount as tax_amount', 'customers.cust_no as cust_no', 'customers.name as name')->get();

            // return json_encode($customer);
            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            return view('sales.invoices.invoice-report', compact('page', 'title', 'title_sw', 'invoices', 'allinvoices', 'customers', 'customer', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date', 'shop', 'settings'));
            
        }else{

            $invoices = AnSale::where('an_sales.shop_id', $shop->id)->whereBetween('an_sales.time_created', [$start, $end])->where('an_sales.is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->join('products', 'products.id', '=', 'an_sale_items.product_id')->select('an_sales.time_created as date', 'invoice_no', 'customers.name as customer', 'vehicle_no', 'due_date', 'products.name as name', 'an_sale_items.quantity_sold as qty', 'an_sale_items.retail_price as price', 'an_sale_items.discount as discount', 'an_sale_items.tax_amount as tax_amount')->orderBy('date', 'asc')->get();

            $allinvoices = AnSale::where('an_sales.shop_id', $shop->id)->whereBetween('an_sales.time_created', [$start, $end])->where('an_sales.is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('invoice_no', 'due_date', 'an_sales.status as status', 'an_sales.time_created as time_created', 'an_sales.sale_amount as amount', 'an_sales.sale_discount as discount', 'an_sales.tax_amount as tax_amount', 'customers.cust_no as cust_no', 'customers.name as name')->get();
            // return json_encode($allinvoices);

            // return json_encode($customer);
            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            return view('sales.invoices.invoice-report', compact('page', 'title', 'title_sw', 'invoices', 'allinvoices', 'customers', 'customer', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date', 'shop', 'settings'));
        }
    }


    public function agingReport(Request $request)
    {
            
        $page = 'Reports';
        $title = 'Aging Reports';
        $title_sw = 'Ripoti za ';
        $shop = Shop::find(Session::get('shop_id'));

        $date0 = \Carbon\Carbon::today()->format('Y-m-d'); 
        $date3 = \Carbon\Carbon::today()->subDays(30)->format('Y-m-d');
        $date6 = \Carbon\Carbon::today()->subDays(60)->format('Y-m-d');
        $date9 = \Carbon\Carbon::today()->subDays(90)->format('Y-m-d');
        $date12 = \Carbon\Carbon::today()->subDays(120)->format('Y-m-d');
        $date15 = \Carbon\Carbon::today()->subDays(150)->format('Y-m-d');
        $date18 = \Carbon\Carbon::today()->subDays(180)->format('Y-m-d');
        $date21 = \Carbon\Carbon::today()->subDays(210)->format('Y-m-d');
        $date24 = \Carbon\Carbon::today()->subDays(240)->format('Y-m-d');
        $date27 = \Carbon\Carbon::today()->subDays(270)->format('Y-m-d');
        $date30 = \Carbon\Carbon::today()->subDays(300)->format('Y-m-d');
        $date33 = \Carbon\Carbon::today()->subDays(330)->format('Y-m-d');
        $date36 = \Carbon\Carbon::today()->subDays(360)->format('Y-m-d');

        // return AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '>=', $date4)->get();
        // (((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))
        $customers = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.id as cuid', 'customers.cust_no as cust_no', 'customers.name as name')->groupBy('name')->get();

        $agings = array();
        foreach ($customers as $key => $customer) {
            $d3 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '>=', $date3)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();

            $d6 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '<=', $date3)->whereDate('an_sales.time_created', '>', $date6)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();

            $d9 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '<=', $date6)->whereDate('an_sales.time_created', '>', $date9)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();
            
            $d12 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '<=', $date9)->whereDate('an_sales.time_created', '>', $date12)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();

            $d15 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '<=', $date12)->whereDate('an_sales.time_created', '>', $date15)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();
            
            $d18 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '<=', $date15)->whereDate('an_sales.time_created', '>', $date18)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();
            
            $d21 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '<=', $date18)->whereDate('an_sales.time_created', '>', $date21)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();
            
            $d24 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '<=', $date21)->whereDate('an_sales.time_created', '>', $date24)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();
            
            $d27 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '<=', $date24)->whereDate('an_sales.time_created', '>', $date27)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();
            
            $d30 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '<=', $date27)->whereDate('an_sales.time_created', '>', $date30)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();
            
            $d33 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '<=', $date30)->whereDate('an_sales.time_created', '>', $date33)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();
            
            $d36 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '<=', $date33)->whereDate('an_sales.time_created', '>', $date36)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();

            $ab360 = AnSale::where('an_sales.shop_id', $shop->id)->whereDate('an_sales.time_created', '<=', $date36)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();

            $ctotal = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();

            array_push($agings, ['cust_no' => $customer->cust_no, 'name' => $customer->name, '0-30' => $d3->amount, '31-60' => $d6->amount, '61-90' => $d9->amount, '91-120' => $d12->amount, '121-150' => $d15->amount, '151-180' => $d18->amount, '181-210' => $d21->amount, '211-240' => $d24->amount, '241-270' => $d27->amount, '271-300' => $d30->amount, '301-330' => $d33->amount, '331-360' => $d36->amount, '>360' => $ab360->amount, 'ctotal' => $ctotal->amount]);
        }

        // return $agings;

        $start_date = null;            
        $end_date = null;
        $is_post_query = false;
        $customer = null;
        $customers = null;
        $crtime = \Carbon\Carbon::now();
        $duration = date('d M, Y', strtotime($crtime));
        $duration_sw = date('d M, Y', strtotime($crtime));
        $reporttime = $crtime->toDayDateTimeString();

        return view('sales.invoices.aging-report', compact('page', 'title', 'title_sw', 'shop', 'agings', 'start_date', 'end_date', 'is_post_query', 'customer', 'customers', 'duration', 'duration_sw', 'reporttime'));
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

    public function changeDiscount(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $start = $request['from_date'].' 00:00:00';
        $end = $request['to_date'].' 23:59:59';
        $sales = AnSale::where('shop_id', $shop->id)->where('customer_id',$request['customer_id'])->whereBetween('time_created', [$start, $end])->where('sale_type', 'Credit')->get();
        foreach ($sales as $key => $sale) {
            $item = AnSaleItem::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->where('product_id', $request['product_id'])->first();
            if (!is_null($item)) {
                    
                $item->discount = $request['discount'];
                $item->total_discount = $item->discount*$item->quantity_sold;
                $item->save();

                $sale->sale_discount = $item->total_discount;
                $sale->save();

                $this->updateSaleStatus($sale);

                $invoice = AnSale::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($invoice)) {
                    $acctrans = CustomerTransaction::where('invoice_no', $invoice->inv_no)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->amount = ($sale->sale_amount-$sale->sale_discount);
                        $acctrans->save();
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Discount was successful changed');
    }
    
    public function formattedNumber($number)
    {
        if ($this->validate_mobile($number)) {
            $num = preg_replace('/^(?:\+?255|0)?/','255', $number);
            return $num;
        } else{
            return null;
        }
    }

    public function validate_mobile($mobile)
    {   
        $mobile = str_replace(' ', '', $mobile);
        $mobile = preg_replace('/^(?:\+?255|0)?/','0', $mobile);
        return preg_match('/^[0-9]{10}+$/', $mobile);
    }

}
