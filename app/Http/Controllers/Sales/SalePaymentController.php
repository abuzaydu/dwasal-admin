<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\AnSale;
use App\Models\SalePayment;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;
use App\Models\Invoice;
use App\Models\CustomerTransaction;
use App\Models\BankDetail;
use App\Models\Customer;
use App\Models\SmsAccount;
use App\Models\SenderId;
use App\Models\SmsTemplate;
use App\Models\CbTransaction;
use Log;

class SalePaymentController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Sales Payments';
        $title = 'Sales Payments';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
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

        $checkpayments = SalePayment::where('shop_id', $shop->id)->get();
        foreach ($checkpayments as $key => $payment) {
            $sale = AnSale::find($payment->an_sale_id);
            if (!is_null($sale)) {
                $customer = Customer::find($sale->customer_id);
                if (!is_null($customer)) {
                    
                }else{
                    Log::info('Customer not Found '.$sale->customer_id.' Invoice No '.$sale->invoice_no);
                    $sale->delete();
                    $payment->delete();
                }
            }else{
                Log::info('Invoice Not found');
                $payment->delete();
            }
        }

        $pay_mode = '';
        $paytypes = ['Cash', 'Bank', 'Mobile Money'];
        if (!empty($request['pay_mode'])) {
            $pay_mode = $request['pay_mode'];

            $payments = SalePayment::where('sale_payments.shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', $pay_mode)->join('an_sales', 'an_sales.id', '=', 'sale_payments.an_sale_id')->where('an_sales.is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('sale_payments.id as id', 'cust_no', 'name', 'invoice_no', 'an_sales.time_created', 'sale_type', 'pay_date', 'pay_mode', 'amount', 'receipt_no', 'cheque_no', 'trans_id')->orderBy('receipt_no', 'desc')->get();
        }else{
            $payments = SalePayment::where('sale_payments.shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('sale_payments.is_deleted', false)->join('an_sales', 'an_sales.id', '=', 'sale_payments.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('sale_payments.id as id', 'cust_no', 'name', 'invoice_no', 'an_sales.time_created', 'sale_type', 'pay_date', 'pay_mode', 'amount', 'receipt_no', 'cheque_no', 'trans_id')->orderBy('receipt_no', 'desc')->get();
        }

        $duration = '';
        // return $payments;
        $excpayments = [];
        $utransactions = CustomerTransaction::where('customer_transactions.shop_id', $shop->id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->join('customers', 'customers.id', '=', 'customer_transactions.customer_id')->get();
        foreach ($utransactions as $key => $trans) {
            $rem_amount = $trans->payment - ($trans->trans_invoice_amount + $trans->trans_ob_amount + $trans->trans_credit_amount);
            if ($rem_amount > 0) {
                if (isset($excpayments['customer_id'])) {
                    $excpayments['customer_id']['amount'] += $rem_amount;
                }else{
                    array_push($excpayments, ['customer_id' => $trans->customer_id, 'name' => $trans->name, 'amount' => $rem_amount]);
                }
            }
        }

        // return $excpayments;
        
        return view('sales.payments.index', compact('page', 'title', 'shop', 'settings', 'payments', 'is_post_query', 'start_date', 'end_date', 'duration', 'paytypes', 'pay_mode', 'excpayments'));
    }

    public function totalSalePayments(Request $request)
    {
        $page = 'Sales Payments';
        $title = 'Total Sales Payments';
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
        $payments = SalePayment::where('sale_payments.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->groupBy('pay_date')->orderBy('pay_date', 'asc')->get([
            \DB::raw('pay_date'),
            \DB::raw('SUM(amount) as amount')
        ]);

        foreach ($payments as $key => $value) {
            $cashpay = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('sale_type', 'cash')->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('pay_date', $value->pay_date)->where('sale_payments.is_deleted', false)->where('pay_date', $value->pay_date)->where('pay_mode', 'Cash')->sum('amount');
            $mobpay = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('sale_type', 'cash')->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('pay_date', $value->pay_date)->where('sale_payments.is_deleted', false)->where('pay_date', $value->pay_date)->where('pay_mode', 'Mobile Money')->sum('amount');
            $bankpay = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('sale_type', 'cash')->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('pay_date', $value->pay_date)->where('sale_payments.is_deleted', false)->where('pay_date', $value->pay_date)->where('pay_mode', 'Bank')->sum('amount');

            // $credits = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('sale_type', 'credit')->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('sale_payments.pay_date', $value->pay_date)->select('sale_payments.pay_mode as pay_mode', 'sale_payments.pay_date as pay_date', 'sale_payments.receipt_no as receipt_no', 'sale_payments.amount as amount', 'an_sales.sale_type as sale_type')->get();;
            // Log::info($credits);
            $credit_cashpay = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('sale_type', 'credit')->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('pay_date', $value->pay_date)->where('sale_payments.is_deleted', false)->where('pay_mode', 'Cash')->sum('amount');

            $credit_mobpay = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('sale_type', 'credit')->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('pay_date', $value->pay_date)->where('sale_payments.is_deleted', false)->where('pay_mode', 'Mobile Money')->sum('amount');
            $credit_bankpay = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('sale_type', 'credit')->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('pay_date', $value->pay_date)->where('sale_payments.is_deleted', false)->where('pay_mode', 'Bank')->sum('amount');

            $netpay = $cashpay+$mobpay+$bankpay;
            $totalcredit = $credit_cashpay+$credit_mobpay+$credit_bankpay;

            array_push($tpayments, ['pay_date' => $value->pay_date, 'Cash' => $cashpay, 'Mobile Money' => $mobpay, 'Bank' => $bankpay, 'netpay' => $netpay, 'Credit Cash' => $credit_cashpay, 'Credit Mobile Money' => $credit_mobpay, 'Credit Bank' => $credit_bankpay, 'total_credit' => $totalcredit, 'total_payments' => $value->amount]);
        }
        
        // return $tpayments;
        return view('sales.payments.total-payments', compact('page', 'title', 'shop', 'tpayments', 'is_post_query', 'start_date', 'end_date', 'duration', 'paytypes', 'pay_mode'));
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

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $page = 'Payment Receipt';
        $title = 'Payment Receipt';
        $title_sw = 'Risiti ya malipo';
        $payment = SalePayment::find(decrypt($id));
        $sale = AnSale::where('an_sales.id', $payment->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'name', 'email', 'phone', 'cust_no', 'tin', 'vrn', 'currency', 'sale_amount', 'sale_discount', 'sale_amount_paid', 'ex_rate')->first();
        $items = AnSaleItem::where('an_sale_id',  $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->get();
        $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->join('services', 'services.id', '=', 'service_sale_items.service_id')->get();
        $date = date("d, M Y H:i:sA", strtotime($sale->time_created));

        return view('sales.payments.show', compact('page', 'title', 'shop', 'payment', 'sale', 'settings', 'items', 'servitems'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $page = 'Edit sale Payments';
        $title = 'Edit Sale Payment';
        $title_sw = 'Hariri Malipo ya Uzo';
        if ($settings->enable_sale_approval) {
            $title = 'Confirm Payment';
        }
        $payment = SalePayment::find(decrypt($id));
        $bdetails = BankDetail::where('shop_id', $shop->id)->get();
        return view('sales.payments.edit', compact('page', 'title', 'title_sw', 'shop', 'settings', 'payment', 'bdetails'));
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
        
    }

    public function airtelTransactions(Request $request)
    {
        $page = 'API Payment Transactions';
        $title = 'API Payment Transactions';
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

        $duration = 'From '.$start_date.' To '.$end_date;
        $transactions = CbTransaction::where('shop_id', $shop->id)->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'DESC')->get();

        return view('sales.payments.api-transactions', compact('page','title', 'shop', 'transactions', 'is_post_query', 'start_date', 'end_date', 'duration'));
    }
}
