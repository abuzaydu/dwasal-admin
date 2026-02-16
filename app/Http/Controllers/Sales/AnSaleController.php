<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \GuzzleHttp\Client;
use Session;
use Auth;
use \Carbon\Carbon;
use \DB;
use App\Models\Company;
use App\Models\Shop;
use App\Models\Account;
use App\Models\ShopCurrency;
use App\Models\User;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;
use App\Jobs\StockUpdaterJob;
use App\Models\Customer;
use App\Models\Stock;
use App\Models\SalePayment;
use App\Models\ActionHistory;
use App\Models\Setting;
use App\Models\Device;
use App\Models\DeviceSale;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ServiceCharge;
use App\Models\SmsAccount;
use App\Models\BankDetail;
use App\Models\CustomerTransaction;
use App\Models\AccountStatement;
use App\Models\EfdmsRctInfo;
use App\Models\EfdmsRegInfo;
use App\Models\EfdmsRctItem;
use App\Models\EfdmsZReport;
use App\Models\EfdmsRctPayment;
use App\Models\EfdmsRctVatTotal;
use App\Models\Taxcode;
use App\Models\ActionLog;
use App\Models\DailyDeposit;
use App\Models\TripLog;
use Log;

class AnSaleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
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
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            if ($shop->subscription_type_id == 2) {
                $lastpay = Payment::where('shop_id', $shop->id)->where('amount_paid', '>', 0)->where('status', 'Activated')->where('is_for_module', false)->latest()->first();

                $premserv = ServiceCharge::where('type', 2)->where('duration', 'Monthly')->first();
                if (!is_null($lastpay)) {
                    if ($lastpay->amount_paid % $premserv->initial_pay == 0 && $lastpay->subscr_type == 2) {
                        return $this->getSales($request, $shop);
                    }else{
                        $wrn_en = 'You have Upgraded your account  to have PREMIUM subscription but no any payments verified after this changes.Please make payment for any amount for PREMIUM subscription as shown in table below then enter your verification code here inorder to continue using our service. Thank you for using SmartMauzo service.';
                        $wrn_sw = 'Umeboresha akaunti yako kuwa na usajili wa PREMIUM lakini hakuna malipo yoyote yaliyothibitishwa baada ya mabadiliko haya. Tafadhali fanya malipo kwa kiasi chochote cha usajili wa PREMIUM kama inavyoonyeshwa kwenye jedwali hapa chini kisha ingiza nambari yako ya uthibitishaji hapa ili kuendelea kutumia huduma yetu. Asante kwa kutumia huduma ya SmartMauzo.';
                        if (app()->getLocale() == 'en') {
                            return redirect('verify-payment')->with('info', $wrn_en);
                        }else{
                            return redirect('verify-payment')->with('info', $wrn_sw);
                        }
                    }
                }else{
                    return $this->getSales($request, $shop);
                }
            }else{
                return $this->getSales($request, $shop);
            }
        }else{
            return view('errors.401');
        }
    }

    public function getSales($request, $shop)
    {
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        
        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
        $is_post_query = false;
        
        if (!empty($request['sale_date'])) {
            $start_date = $request['sale_date'];
            $end_date = $request['sale_date'];
            $start = $request['sale_date'].' 00:00:00';
            $end = $request['sale_date'].' 23:59:59';
            $is_post_query = true;
        }else if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $sales = null;
        $customer = null;
        if (!empty($request['search_key'])) {
            $searchkey = ltrim($request['search_key'], '0');
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', false)->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where(\DB::raw('CONCAT_WS(" ", `invoice_no`, `name`, `first_name`)'), 'like', '%' . $searchkey . '%')->select('an_sales.id as id',  'an_sales.time_created as time_created', 'customers.id as customer_id', 'customers.name as name', 'invoice_no', 'sale_amount', 'sale_discount', 'tax_amount',  'return_amount', 'return_discount', 'return_tax', 'sale_amount_paid', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.sale_type as sale_type', 'an_sales.status as status', 'an_sales.comments as comments', 'users.first_name as first_name', 'grade_id', 'year')->orderBy('an_sales.time_created', 'desc')->get();
        }else{
            if (Auth::user()->can('view-all-invoice')) {
                $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id',  'an_sales.time_created as time_created', 'customers.id as customer_id', 'customers.name as name', 'invoice_no', 'sale_amount', 'sale_discount', 'tax_amount',  'return_amount', 'return_discount', 'return_tax', 'sale_amount_paid', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.sale_type as sale_type', 'an_sales.status as status', 'an_sales.comments as comments', 'users.first_name as first_name', 'grade_id', 'year')->orderBy('an_sales.time_created', 'desc')->get();
            }else{
                $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', false)->where('an_sales.user_id', $user->id)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id',  'an_sales.time_created as time_created', 'customers.id as customer_id', 'customers.name as name', 'invoice_no', 'sale_amount', 'sale_discount', 'tax_amount',  'return_amount', 'return_discount', 'return_tax', 'sale_amount_paid', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.sale_type as sale_type', 'an_sales.status as status', 'an_sales.comments as comments', 'users.first_name as first_name', 'grade_id', 'year')->orderBy('an_sales.time_created', 'desc')->get();                
            }
        }

        $page = 'Sales';
        $title = 'My Sales';
        $title_sw = 'Mauzo Yangu';
        return view('sales.invoices.index', compact('page', 'title', 'title_sw', 'shop', 'settings', 'sales', 'start_date', 'end_date', 'is_post_query'));  
    }

   
    public function rentalStatus(Request $request)
    {
        $page = 'Rental Status Report';
        $title = 'Rental Status Report';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();            
        $code = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
        $now = Carbon::now();
        $start = $now->startOfYear();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
        $is_post_query = false;
        
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $devices = Device::where('shop_id', $shop->id)->select('id', 'device_number', 'device_name')->get();
        $currdevice = null;
        $sales = null;
        if (!empty($request['device_id'])) {
            $currdevice = Device::find($request['device_id']);
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->whereBetween('an_sales.time_created', [$start, $end])->join('device_sales', 'device_sales.an_sale_id', '=', 'an_sales.id')->where('device_id', $currdevice->id)->join('devices', 'devices.id', '=', 'device_sales.device_id')->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('name', 'phone', 'device_number', 'device_name', 'an_sales.time_created as rent_start_date', 'rent_end_date', 'price', 'no_of_repeatition as qty', 'total', 'total_discount', 'service_sale_items.tax_amount as tax_amount')->get();

        }else{
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->whereBetween('an_sales.time_created', [$start, $end])->join('device_sales', 'device_sales.an_sale_id', '=', 'an_sales.id')->join('devices', 'devices.id', '=', 'device_sales.device_id')->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('name', 'phone', 'device_number', 'device_name', 'an_sales.time_created as rent_start_date', 'rent_end_date', 'price', 'no_of_repeatition as qty', 'total', 'total_discount', 'service_sale_items.tax_amount as tax_amount')->get();
        }
        $duration = 'From '.date('d M, Y', strtotime($start)).' To '.date('d M, Y', strtotime($end));
        return view('reports.rentals', compact('page', 'title', 'shop', 'code', 'is_post_query', 'start_date', 'end_date', 'duration', 'devices', 'currdevice', 'sales'));
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
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $accounts = Account::where('shop_id', $shop->id)->get();
        $products = $shop->products()->get();
        $services = $shop->services()->get();
        $sale = AnSale::where('an_sales.id', decrypt($id))->where('an_sales.shop_id', $shop->id)->join('customers', 'an_sales.customer_id', '=', 'customers.id')->select('an_sales.id as id',  'an_sales.time_created as time_created', 'customers.id as customer_id', 'customers.name as name', 'invoice_no', 'sale_amount', 'sale_discount', 'tax_amount',  'return_amount', 'return_discount', 'return_tax', 'sale_amount_paid', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.sale_type as sale_type', 'an_sales.status as status', 'an_sales.comments as comments',)->first();
        if (!is_null($sale)) {
            $page = 'Sale items';
            $title = "Sale details";
            $title_sw = 'Maelezo ya Uzo';
            $payments = SalePayment::where('an_sale_id', $sale->id)->get();

            // foreach ($payments as $key => $payment) {
            //     $deposits = DailyDeposit::where('sale_payment_id', $payment->id)->sum('amount');
            //     if ($payment->amount != $deposits) {
            //         // if ($payment->amount == 1062000) {
            //             Log::info('This payment does not match deposits Pay date :'.$payment->pay_date.' Receipt '.$payment->receipt_no.' Amount : '.$payment->amount.' Deposit AMT : '.$deposits);
            //             // Log::info('delete this');
            //             // $payment->delete();
            //         // }
                    
            //     }
            // }

            $total_paid = SalePayment::where('an_sale_id', $sale->id)->sum('amount');

            $serv_items = ServiceSaleItem::where('an_sale_id', $sale->id)->join('services', 'services.id', '=', 'service_sale_items.service_id')->select('services.id as s_id', 'services.name as name', 'service_sale_items.service_id as service_id', 'service_sale_items.id as id', 'service_sale_items.no_of_repeatition as no_of_repeatition', 'service_sale_items.price as price', 'service_sale_items.discount as discount', 'service_sale_items.total_discount as total_discount', 'service_sale_items.total as total','service_sale_items.tax_amount as tax_amount', 'service_sale_items.time_created as created_at')->orderBy('service_sale_items.time_created', 'desc')->get();
                
            $sale_items = AnSaleItem::where('an_sale_id', $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->select('products.id as p_id', 'products.name as name', 'products.basic_uom as basic_uom', 'an_sale_items.product_id as product_id', 'an_sale_items.id as id', 'an_sale_items.quantity_sold as quantity_sold', 'an_sale_items.unit_cost as unit_cost', 'an_sale_items.buying_price as buying_price', 'an_sale_items.retail_price as retail_price', 'an_sale_items.discount as discount', 'an_sale_items.price as price', 'an_sale_items.total_discount as total_discount', 'an_sale_items.tax_amount as tax_amount', 'an_sale_items.time_created as created_at')->orderBy('an_sale_items.time_created', 'desc')->get();

            return view('sales.invoices.sale-details', compact('page', 'title', 'title_sw', 'serv_items', 'sale_items', 'sale', 'shop', 'accounts', 'defcurr', 'currencies', 'payments', 'total_paid', 'settings', 'products', 'services'));
        }
    }

    public function printReceipt($id)
    {
        $page = 'Invoice Receipt view';
        $title = 'Invoice Receipt view';
        $title_sw = 'Risiti ys Kawaida';

        $shop = Shop::find(Session::get('shop_id'));
        $company = Company::find($shop->company_id);
        $accounts = Account::where('shop_id', $shop->id)->get();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $sale = AnSale::where('an_sales.id', decrypt($id))->join('customers', 'customers.id', '=', 'an_sales.customer_id')->join('users', 'users.id', '=', 'an_sales.user_id')->select('first_name', 'last_name', 'customer_id', 'customers.name as name', 'customers.cust_no as cust_no', 'customers.postal_address as po_address', 'customers.physical_address as ph_address', 'customers.street as street', 'customers.email as email', 'customers.phone as phone', 'customers.tin as tin', 'customers.vrn as vrn', 'an_sales.id as id', 'invoice_no', 'lpo_no', 'sale_type', 'pay_type', 'is_paid', 'status', 'is_stock_requested', 'an_sales.time_created as time_created', 'due_date', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.tax_amount as tax_amount', 'an_sales.currency as currency', 'an_sales.defcurr as defcurr', 'an_sales.ex_rate as ex_rate', 'note', 'bank_detail_id')->first();
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
            $ex_rate = 1;
            if($stmtcurr != $defcurr){
                $ex_rate = $sale->ex_rate;
            }

            return view('sales.invoices.receipt', compact('page', 'title', 'title_sw', 'sale', 'items', 'servitems', 'payments', 'newpayments', 'oldbalance', 'company', 'shop', 'accounts', 'settings', 'date', 'defcurr', 'stmtcurrencies', 'stmtcurr', 'ex_rate', 'currencies'));
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
        $shop = Shop::find(Session::get('shop_id'));
        $sale = AnSale::find(decrypt($id));

        if (!is_null($sale)) {
            $customer = Customer::find($sale->customer_id);
            $customers = Customer::where('shop_id', $shop->id)->select('id', 'name')->get();
            $page = 'Edit Invoice';
            $title = "Edit Invoice";
            $title_sw = "Hariri Ankara";

            $settings = Setting::where('shop_id', $shop->id)->first();
            $devices = Device::where('shop_id', $shop->id)->get();
            $dsale = DeviceSale::where('an_sale_id', $sale->id)->first();
            $accounts = Account::where('shop_id', $shop->id)->get();
            return view('sales.invoices.edit', compact('page', 'sale', 'title', 'title_sw', 'customers', 'customer', 'settings', 'devices', 'dsale', 'accounts'));
        }else{
            return redirect()->back()->with('error', 'Sales Record not Found');
        }
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
        $sale = AnSale::find(decrypt($id));
        $now = $sale->time_created;
        if (!empty($request['sale_date'])) {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $now = $request['sale_date'] . ' ' . $time;
        }
        $sale->customer_id = $request['customer_id'];
        $sale->time_created = $now;
        $sale->vehicle_no = $request['vehicle_no'];
        $sale->due_date = $request['due_date'];
        $sale->bank_detail_id = $request['bank_detail_id'];
        $sale->sale_type = $request['sale_type'];
        $sale->comments = $request['comments'];
        $sale->rent_end_date = $request['rent_end_date'];
        $sale->save();

        $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
        foreach ($items as $key => $item) {
            $item->time_created = $sale->time_created;
            $item->save();
        }

        $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
        foreach ($servitems as $key => $item) {
            $item->time_created = $sale->time_created;
            $item->save();
        }
        
        $payments = SalePayment::where('an_sale_id', $sale->id)->get();
        foreach ($payments as $key => $payment) {
            $payacctrans = CustomerTransaction::find($payment->trans_id);
            if (!is_null($payacctrans)) {
                $payacctrans->customer_id = $sale->customer_id;
                $payacctrans->save();
            }
        }

        if (!empty($request['device_id'])) {
            $dsale = DeviceSale::where('an_sale_id', $sale->id)->first();
            if (!is_null($dsale)) {
                $dsale->device_id = $request['device_id'];
                $dsale->save();
            }else{
                DeviceSale::create([
                    'device_id' => $request['device_id'],
                    'an_sale_id' => $sale->id
                ]);
            }
        }
        $success = 'Your sale was succesfuly updated';
        return redirect('an-sales')->with('success', $success);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('delete-invoice') || Auth::user()->can('cancel-invoice')) {
            $sale = AnSale::find(decrypt($id));
            if (!is_null($sale)) {
                $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($items)) {
                    foreach ($items as $key => $item) {
                        $product = $shop->products()->where('id', $item->product_id)->first();
                        $stock = Stock::find($item->stock_id);
                        if (!is_null($stock)) {
                            $stock->quantity_out = $stock->quantity_out-$item->quantity_sold;
                            if ($stock->quantity_in == $stock->quantity_out) {
                                $stock->is_utilized = true;
                            }else{
                                $stock->is_utilized = false;
                            }
                            $stock->save();
                        }
                        $item->is_deleted = true;
                        $item->del_by = Auth::user()->first_name.'('.Carbon::now().')';
                        $item->save();
                        // $item->delete();
                        dispatch(new StockUpdaterJob($shop, $item->product_id));
                    }
                }

                $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($servitems)) {
                    foreach ($servitems as $key => $sitem) {
                        $sitem->is_deleted = true;
                        $sitem->del_by = Auth::user()->first_name.'('.Carbon::now().')';
                        $sitem->save();
                        // $sitem->delete();
                    }
                }

                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                if (!is_null($payments)) {
                    foreach ($payments as $key => $payment) {
                        $payment->is_deleted = true;
                        $payment->save();
                        $acctrans = CustomerTransaction::find($payment->trans_id);
                        if (!is_null($acctrans)) {
                            $acctrans->trans_invoice_amount = $acctrans->trans_invoice_amount-$payment->amount;
                            $acctrans->is_utilized = false;
                            $acctrans->save();
                        }
                    }
                }

                $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->is_deleted = true;
                    $acctrans->save();
                }

                $triplog = TripLog::where('shop_id', $shop->id)->where('an_sale_id', $sale->id)->first();
                if (!is_null($triplog)) {
                    $triplog->an_sale_id = null;
                    $triplog->save();
                }

                $sale->is_deleted = true;
                $sale->del_by = Auth::user()->first_name.' ('.Carbon::now().')';
                $sale->save();
                // $sale->delete();

                $actlog = new ActionLog();
                $actlog->shop_id = $shop->id;
                $actlog->user_id = Auth::user()->id;
                $actlog->action_type = 'Cancel Invoice';
                $actlog->log_message = 'Invoice No '.sprintf('%04d', $sale->invoice_no).' has been cancelled';
                $actlog->save();

                $success = 'Your sale was succesfuly deleted';
                return redirect('an-sales')->with('success', $success);
            }
        }else{
            return view('errors.401');
        }
    }

    public function deleteMultiple(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('delete-invoice') || Auth::user()->can('cancel-invoice')) {
            if (!empty($request->input('ids'))) {
                foreach ($request->input('ids') as $key => $id) {
                    $sale = AnSale::where('id', $id)->where('shop_id', $shop->id)->first();
                    // return $id;

                    if (!is_null($sale)) {
                        $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                        if (!is_null($items)) {
                            foreach ($items as $key => $item) {
                                
                                $product = $shop->products()->where('id', $item->product_id)->first();
                                $stock = Stock::find($item->stock_id);
                                if (!is_null($stock)) {
                                    $stock->quantity_out = $stock->quantity_out-$item->quantity_sold;
                                    if ($stock->quantity_in == $stock->quantity_out) {
                                        $stock->is_utilized = true;
                                    }else{
                                        $stock->is_utilized = false;
                                    }
                                    $stock->save();
                                }
                                $item->is_deleted = true;
                                $item->del_by = Auth::user()->first_name.' ('.Carbon::now().')';
                                $item->save();
                                // $item->delete();
                                dispatch(new StockUpdaterJob($shop, $item->product_id));
                            }
                        }

                        $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                        if (!is_null($servitems)) {
                            foreach ($servitems as $key => $sitem) {
                                $sitem->is_deleted  = true;
                                $sitem->del_by = Auth::user()->first_name.'('.Carbon::now().')';
                                $sitem->save();
                                // $sitem->delete();
                            }
                        }

                        $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                        if (!is_null($acctrans)) {
                            $acctrans->is_deleted = true;
                            $acctrans->save();
                        }

                        $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                        if (!is_null($payments)) {
                            foreach ($payments as $key => $payment) {
                                $payment->is_deleted = true;
                                $payment->save();
                                $acctrans = CustomerTransaction::find($payment->trans_id);
                                if (!is_null($acctrans)) {
                                    if ($acctrans->payment == $payment->amount) {
                                        $acctrans->is_deleted = true;
                                        $acctrans->save();

                                        $astmt = AccountStatement::where('customer_transaction_id', $acctrans->id)->first();
                                        if (!is_null($astmt)) {
                                            $astmt->is_deleted = true;
                                            $astmt->save();
                                        }
                                    }else{
                                        $acctrans->trans_invoice_amount = $acctrans->trans_invoice_amount-$payment->amount;
                                        $acctrans->is_utilized = false;
                                        $acctrans->save();
                                    }
                                }
                            }
                        }
                        
                        $triplog = TripLog::where('shop_id', $shop->id)->where('an_sale_id', $sale->id)->first();
                        if (!is_null($triplog)) {
                            $triplog->an_sale_id = null;
                            $triplog->save();
                        }

                        $sale->is_deleted = true;
                        $sale->del_by = Auth::user()->first_name.' ('.Carbon::now().')';
                        $sale->save();
                        // $sale->delete();

                        $actlog = new ActionLog();
                        $actlog->shop_id = $shop->id;
                        $actlog->user_id = Auth::user()->id;
                        $actlog->action_type = 'Cancel Invoice';
                        $actlog->log_message = 'Invoice No '.sprintf('%04d', $sale->invoice_no).' has been cancelled';
                        $actlog->save();
                    }
                }
                
                $success = 'Sales were deleted successfully';
                return redirect('an-sales')->with('success', $success);
            }else{
                return redirect('an-sales')->with('info', 'No Sales selected to Delete');
            }
        }else{
            return view('errors.401');
        }
    }

    public function issueVFD($id)
    {
        $sale = AnSale::find(decrypt($id));
        if (!is_null($sale)) {
            $now = Carbon::now();
            $shop = Shop::find(Session::get('shop_id'));
            $reginfo = EfdmsRegInfo::where('shop_id', $shop->id)->first();
            $customer = Customer::find($sale->customer_id);
            $zreport = EfdmsZReport::where('shop_id', $shop->id)->where('status', 'Not Submitted')->first();
            $znum = null;
            if (!is_null($zreport)) {
                $znum = $zreport->znum;
            }else{
                $lastzr_sub = EfdmsZReport::where('shop_id', $shop->id)->latest()->first();
                if (!is_null($lastzr_sub)) {
                    $znum = $lastzr_sub->znum+1;
                }else{
                    $znum = 1;
                }

                $znumber = date('Ymd', strtotime($now));
                $zreport = new EfdmsZReport();
                $zreport->shop_id = $shop->id;
                $zreport->date = $now;
                $zreport->tin = $reginfo->tin;
                $zreport->vrn = $reginfo->vrn;
                $zreport->taxoffice = $reginfo->taxoffice;
                $zreport->regid = $reginfo->regid;
                $zreport->znum = $znum;
                $zreport->znumber = $znumber;
                $zreport->efdserial = $reginfo->serial;
                $zreport->registration_date = date('Y-m-d', strtotime($reginfo->created_at));
                $zreport->simimsi = "WEBAPI";
                $zreport->fwversion = '3.0';
                $zreport->fwchecksum = 'WEBAPI';
                $zreport->save();
            }
            $lastrct = EfdmsRctInfo::where('shop_id', $shop->id)->latest()->first();
            $rctnum = 1;
            if (!is_null($lastrct)) {
                $rctnum = $lastrct->rctnum+1;
            }
            $ldc = EfdmsRctInfo::where('shop_id', $shop->id)->whereDate('created_at', Carbon::today())->count();
            $lgc = EfdmsRctInfo::where('shop_id', $shop->id)->count();


            $taxcode = Taxcode::where('value', $reginfo->taxcode)->first();
            if (!is_null($reginfo)) {
                $rectvnum = $reginfo->receiptcode.''.($lgc+1);
                $rctinfo = new EfdmsRctInfo();
                $rctinfo->shop_id = $shop->id;
                $rctinfo->an_sale_id = $sale->id;
                $rctinfo->efdms_z_report_id = $zreport->id;
                $rctinfo->date = $now;
                $rctinfo->tin = $reginfo->tin;
                $rctinfo->regid = $reginfo->regid;
                $rctinfo->efdserial = $reginfo->serial;
                $rctinfo->custidtype = $customer->cust_id_type;
                $rctinfo->custid = $customer->custid;
                $rctinfo->custname = $customer->name;
                $rctinfo->mobilenum = $customer->phone;
                $rctinfo->rctnum = $rctnum;
                $rctinfo->dc = $ldc+1;
                $rctinfo->gc = $ldc+1;
                $rctinfo->znum = $znum;
                $rctinfo->rctvnum = $rectvnum;
                $rctinfo->total_tax_excl = ($sale->sale_amount-$sale->sale_discount);
                $rctinfo->total_tax_incl = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                $rctinfo->discount = $sale->sale_discount;
                $rctinfo->save();

                $saleitems = AnSaleItem::where('an_sale_id', $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->select('an_sale_items.id as id', 'name', 'quantity_sold', 'price', 'total_discount')->get();

                $code_a_netamount = 0; $code_a_taxamount = 0;
                $code_b_netamount = 0; $code_b_taxamount = 0;
                $code_c_netamount = 0; $code_c_taxamount = 0;
                foreach ($saleitems as $key => $item) {
                    $rctitem = new EfdmsRctItem();
                    $rctitem->efdms_rct_info_id = $rctinfo->id;
                    $rctitem->item_code = $item->id;
                    $rctitem->desc = $item->name;
                    $rctitem->qty = $item->quantity_sold;
                    $rctitem->taxcode = $taxcode->id;
                    $rctitem->amt = $item->price+$item->tax_amount;
                    $rctitem->save();

                    $code_a_netamount += ($item->price-$item->total_discount)+$item->tax_amount;
                    $code_a_taxamount += $item->tax_amount;
                }

                $cashpayment = 0;
                $chequepayment = 0;
                $ccardpayment = 0;
                $emoneypayment = 0;
                $invoicepayment = (($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount)-$sale->sale_amount_paid;
                $spayments = SalePayment::where('an_sale_id', $sale->id)->get();
                
                foreach ($spayments as $key => $spay) {
                     if ($spay->pay_mode == 'Cash') {
                        $cashpayment += $spay->amount;
                    }elseif ($spay->pay_mode == 'Bank' || $spay->pay_mode == 'Cheque') {
                        $chequepayment += $spay->amount;
                    }elseif ($spay->pay_mode == 'Mobile Money') {
                        $ccardpayment += $spay->amount;
                    }
                }

                // Payment Types
                $pmttypes = array(
                    ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'CHEQUE',  'pmtamount' => $chequepayment],
                    ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'CCARD', 'pmtamount' => $ccardpayment],
                    ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'CASH', 'pmtamount' => $cashpayment],
                    ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'EMONEY', 'pmtamount' => $emoneypayment],
                    ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'INVOICE', 'pmtamount' => $invoicepayment]
                );

                foreach ($pmttypes as $key => $pmt) {
                    EfdmsRctPayment::create($pmt);
                }

                // VAT Totals
                $vattotals = array(
                    ['efdms_rct_info_id' => $rctinfo->id, 'vatrate' => 'A',  'netamount' => $code_a_netamount, 'taxamount' => $code_a_taxamount],
                    ['efdms_rct_info_id' => $rctinfo->id, 'vatrate' => 'B', 'netamount' => $code_b_netamount, 'taxamount' => $code_b_taxamount],
                    ['efdms_rct_info_id' => $rctinfo->id, 'vatrate' => 'C', 'netamount' => $code_c_netamount, 'taxamount' => $code_c_taxamount]
                );

                foreach ($vattotals as $key => $vatt) {
                    EfdmsRctVatTotal::create($vatt);
                }

                $this->sendReceiptReq($rctinfo);
                // return redirect()->back()->with('success', 'VFD Receipt sent successfully');
                return redirect('vfd-rct-infos')->with('success', 'Your receipt submitted successfully');
            }else{
                return redirect()->back()->with('error', 'Sorry!. Your registration for VFD not Acknowledged yet or Something went wrong please check registration status and try again');
            }
        }
    }

    public function sendReceiptReq($rctinfo)
    {
        $rctitems = EfdmsRctItem::where('efdms_rct_info_id', $rctinfo->id)->get();

        $xmldoc =  "<?xml version='1.0' encoding='UTF-8'?>";
        $efdms_open = "<EFDMS>";
        $efdms_close = "</EFDMS>";
        $efdms_signatureOpen="<EFDMSSIGNATURE>";
        $efdms_signatureClose="</EFDMSSIGNATURE>";

        $rctitemsxmlopen = '<ITEMS>';
        $rctitemsxmlclose = '</ITEMS>'; 
        $xmlitems = '';
        foreach ($rctitems as $key => $rctitem) {
            $xmlitems.= '<ITEM> 
                            <ID>'.$rctitem->item_code.'</ID> 
                            <DESC>'.$rctitem->desc.'</DESC> 
                            <QTY>'.$rctitem->qty.'</QTY> 
                            <TAXCODE>'.$rctitem->taxcode.'</TAXCODE> 
                            <AMT>'.$rctitem->amt.'</AMT> 
                        </ITEM>';
        }

        $rctitemsxml = $rctitemsxmlopen.$xmlitems.$rctitemsxmlclose;

        $xmlpayments = '';
        $rctpayments = EfdmsRctPayment::where('efdms_rct_info_id', $rctinfo->id)->get();
        foreach ($rctpayments as $key => $rctp) {
            $xmlpayments.= '<PMTTYPE>'.$rctp->pmttype.'</PMTTYPE> 
                            <PMTAMOUNT>'.$rctp->pmtamount.'</PMTAMOUNT>';
        }

        $xmlvattotals = '';
        $vattotals = EfdmsRctVatTotal::where('efdms_rct_info_id', $rctinfo->id)->get();
        foreach ($vattotals as $key => $vatt) {
            $xmlvattotals.= '<VATRATE>'.$vatt->vattotals.'</VATRATE> 
                            <NETTAMOUNT>'.$vatt->netamount.'</NETTAMOUNT> 
                            <TAXAMOUNT>'.$vatt->taxamount.'</TAXAMOUNT>';
        }

        $rctxml = '<RCT> 
                        <DATE>'.date('Y-m-d', strtotime($rctinfo->date)).'</DATE> 
                        <TIME>'.date('H:i:s', strtotime($rctinfo->date)).'</TIME> 
                        <TIN>'.$rctinfo->tin.'</TIN> 
                        <REGID>'.$rctinfo->regid.'</REGID> 
                        <EFDSERIAL>'.$rctinfo->efdserial.'</EFDSERIAL> 
                        <CUSTIDTYPE>'.$rctinfo->cust_id_type.'</CUSTIDTYPE> 
                        <CUSTID>'.$rctinfo->custid.'</CUSTID> 
                        <CUSTNAME>'.$rctinfo->custname.'</CUSTNAME> 
                        <MOBILENUM>'.$rctinfo->mobilenum.'</MOBILENUM> 
                        <RCTNUM>'.$rctinfo->rctnum.'</RCTNUM> 
                        <DC>'.$rctinfo->dc.'</DC> 
                        <GC>'.$rctinfo->gc.'</GC> 
                        <ZNUM>'.$rctinfo->znum.'</ZNUM> 
                        <RCTVNUM>'.$rctinfo->rctvnum.'</RCTVNUM>'.$rctitemsxml.'
                        <TOTALS>
                            <TOTALTAXEXCL>'.$rctinfo->total_tax_excl.'</TOTALTAXEXCL> 
                            <TOTALTAXINCL>'.$rctinfo->total_tax_incl.'</TOTALTAXINCL> 
                            <DISCOUNT>'.$rctinfo->discount.'</DISCOUNT> 
                        </TOTALS> 
                        <PAYMENTS>'.$xmlpayments.'</PAYMENTS>
                        <VATTOTALS>'.$xmlvattotals.'</VATTOTALS> 
                    </RCT>';
        $certbase = base64_encode('');
        $rctsignature = base64_encode(hash('sha1', $rctxml));
        $xmlbody = $xmldoc.$efdms_open.$rctxml.$efdms_signatureOpen.$rctsignature.$efdms_signatureClose.$efdms_close;

        $client = new Client();
        $url = 'http://localhost/smartmauzo/public/efdms-rct-ack-infos';

        $createRequest = new \GuzzleHttp\Psr7\Request(
            'POST', 
            $url, 
            [
                'X-CSRF-Token'=> csrf_token(),
                'Content-Type' => 'Application\xml',
                'Cert-Serial' => $certbase,
                'Client' => 'WEBAPI'
            ],
            $xmlbody,
            '1.1'
        );

        $response = $client->sendRequest($createRequest);
        $rctinfo->is_submitted = true;
        $rctinfo->save();
         // configure options
        // $options = [
        //     'headers' => [
        //         'Content-Type' => 'text/xml; charset=UTF8',
        //         'X-CSRF-TOKEN' => csrf_token(),
        //         // 'Content-Type' => 'Application\xml',
        //         'Cert-Serial' => $certbase,
        //         'Client' => 'WEBAPI'
        //     ],
        //     'body' => $xmlbody
        // ];

        // $response = $client->request('POST', $url, $options);

        // return redirect('vfd-rct-infos')->with('success', 'Your receipt submitted successfully');
    }
}
