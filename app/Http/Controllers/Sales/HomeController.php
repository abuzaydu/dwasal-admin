<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Session;
use Auth;
use DNS1D;
use DNS2D;
use App\Models\Company;
use App\Models\User;
use App\Models\Shop;
use App\Models\AnSale;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Stock;
use App\Models\Payment;
use App\Models\ServiceCharge;
use App\Models\SalePayment;
use App\Models\CashIn;
use App\Models\CustomerTransaction;
use App\Models\AccountStatement;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Jobs\DailyClosingStockJob;

class HomeController extends Controller
{
    private $thisdaydate;
    private $lastdaydate;
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $settings = Setting::where('shop_id', $shop->id)->first();
            if ($shop->is_warehouse) {
                return redirect('products');
            }else{
                $user = Auth::user();
                if (Auth::user()->can('view-reports')) {
                    if ($settings->is_cm_business) {
                        return redirect('cm-dashboard');
                    }else{
                        return $this->getDashboardData($request, $shop);
                    }
                }else{
                    return redirect(Auth::user()->default_page);
                }
            }
        }else{
            $user = Auth::user();
            $role = $user->roles[0]['name'];
            if ($role == 'super_admin') {
                return redirect()->intended('admin/home');
            }else{
                if (is_null($user->first_name) && is_null($user->phone)) {
                    return redirect()->intended('signup-complete');
                }else{
                    return redirect('user-profile')->with('info', 'Please setup your Businesses');
                }
            }
        }
    }

    public function getDashboardData($request, $shop)
    {
        $user = Auth::user();
        $company = Company::find(Session::get('company_id'));
        if (!is_null($company)) {
            $shops = $company->shops()->select('id', 'name')->get();
            $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();

            if (!is_null($payment)) {
                $now = Carbon::now();

                $start = null;
                $end = null;
                $start_date = null;
                $end_date = null;
                //check if user opted for date range
                $is_post_query = false;
                if (!empty($request['start_date'])) {
                    $start_date = $request['start_date'];
                    $end_date = $request['end_date'];
                    $start = $request['start_date'].' 00:00:00';
                    $end = $request['end_date'].' 23:59:59';
                    $is_post_query = true;
                }else{
                    $start = $now->startOfDay();
                    $end = \Carbon\Carbon::now();
                    $start_date = date('Y-m-d', strtotime($start));
                    $end_date = date('Y-m-d', strtotime($end));
                    $is_post_query = false;
                }
                $no_customers = 0;
                $total_sales = 0;
                $total_debts = 0;
                $total_collections = 0;
                $total_expenses = 0;
                $gross_profit = 0;
                $today_sales = 0;
                $yesterday_sales = 0;
                $this_month_total = collect([]);
                $last_month_total = collect([]);
                $this_week_amounts = collect([]);
                $last_week_amounts = collect([]);
                $sales = collect([]);
                $expenses = collect([]);
                $products = collect([]);
                $services = collect([]);
                $items = collect([]);
                $servitems = collect([]);
                $currstore = null;
                if ($is_post_query) {
                    $currstore = Shop::find($request['store']);
                }else{
                    $currstore = Shop::find(Session::get('shop_id'));
                }
                if (!is_null($currstore)) {
                    $shop = $currstore;
                    $tsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('sale_amount');
                    $tdiscount = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('sale_discount');
                    $ttax = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('tax_amount');

                    $treturn_amt = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('return_amount');
                    $treturn_disc = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('return_discount');
                    $treturn_tax = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('return_tax');
                    
                    $total_sales = (($tsales-$tdiscount)+$ttax)-(($treturn_amt-$treturn_disc)+$treturn_tax);
                    
                    $total_expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('amount');

                    $total_collections = CustomerTransaction::where('payment', '!=', null)->where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->where('is_deleted', false)->sum('payment');

                    $gpp = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->sum('price')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->sum('total_discount')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->sum('buying_price');
                    $gps = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->sum('total')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->sum('total_discount');

                    $gross_profit = $gpp+$gps;

                    $sales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
                        \DB::raw('SUM(sale_amount) as amount'),
                        \DB::raw('SUM(sale_discount) as discount'),
                        \DB::raw('SUM(tax_amount) as tax'),
                        \DB::raw('SUM(sale_amount_paid) as amount_paid'),
                        \DB::raw('SUM(return_amount) as return_amount'),
                        \DB::raw('SUM(return_discount) as return_discount'),
                        \DB::raw('SUM(return_tax) as return_tax'),
                        \DB::raw('DATE(an_sales.time_created) as date')
                    ]);

                    $total_debts = 0;

                    foreach ($sales as $key => $debt) {
                        $tnetsales = ($debt->amount-$debt->discount)+$debt->tax;
                        $tnetreturn = ($debt->return_amount-$debt->return_discount)+$debt->return_tax;
                        $netsales_amount = $tnetsales-$tnetreturn;
                        $total_debts += $netsales_amount-$debt->amount_paid;
                    }
                    
                    $expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
                            \DB::raw('DATE(time_created) as date'),
                            \DB::raw('SUM(amount) as amount')
                        ]);


                    $products = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'products.id', '=', 'an_sale_items.product_id')->groupBy('name')->orderBy('quantity', 'desc')->take(10)->get([
                      \DB::raw('an_sale_items.product_id as product_id'),
                      \DB::raw('products.name as name'),
                      \DB::raw('an_sale_items.retail_price as unitprice'),
                      \DB::raw('an_sale_items.discount as unitdiscount'),
                      \DB::raw('SUM(quantity_sold) as quantity'),
                      \DB::raw('SUM(price) as price'),
                      \DB::raw('SUM(total_discount) as discount')
                    ]);

                    // return $products;

                    $services = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->join('service_shop', 'service_id', '=', 'services.id')->groupBy('name')->orderBy('total', 'desc')->take(10)->get([
                      \DB::raw('service_sale_items.service_id as service_id'),
                      \DB::raw('services.name as name'),
                      \DB::raw('service_sale_items.price as unitprice'),
                      \DB::raw('service_sale_items.discount as unitdiscount'),
                      \DB::raw('SUM(no_of_repeatition) as quantity'),
                      \DB::raw('SUM(total) as total'),
                      \DB::raw('SUM(total_discount) as discount')
                    ]);
                    // return $products;


                    $items = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
                        \DB::raw('SUM(price) as price'),
                        \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
                        \DB::raw('SUM(total_discount) as discount'),
                        \DB::raw('SUM(buying_price) as buying_price'),
                        \DB::raw('SUM(an_sale_items.input_tax) as input_vat'),
                        \DB::raw('SUM(an_sale_items.tax_amount) as output_vat'),
                        \DB::raw('DATE(an_sales.time_created) as date')
                    ]);

                    $servitems = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
                        \DB::raw('SUM(total) as price'),
                        \DB::raw('SUM(total_discount) as discount'),
                        \DB::raw('SUM(service_sale_items.tax_amount) as output_vat'),
                        \DB::raw('DATE(an_sales.time_created) as date')
                    ]);


                    //Get Total sales today and yesterday
                    $today = Carbon::today();
                    $yesterday = Carbon::yesterday();
                    $today_sales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereDate('time_created', $today)->sum('sale_amount')-AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereDate('time_created', $today)->sum('sale_discount');
                    $yesterday_sales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereDate('time_created', $yesterday)->sum('sale_amount')-AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereDate('time_created', $yesterday)->sum('sale_discount');


                    //Get Weekly sales
                    $weekstart = Carbon::now()->startOfWeek();
                    $weekend = Carbon::now()->endOfWeek();

                    $this_week_amounts = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$weekstart, $weekend])->groupBy('day')->orderBy('day', 'asc')->get([
                      \DB::raw('SUM(sale_amount) as t_amount'),
                      \DB::raw('SUM(sale_discount) as t_discount'),
                      \DB::raw('DAY(an_sales.time_created) as day')
                    ]);
                    
                    $lastweekstart = \Carbon\Carbon::now()->subWeek()->startOfWeek();
                    $lastweekend = \Carbon\Carbon::now()->subWeek()->endOfWeek();
                    
                
                    $last_week_amounts = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$lastweekstart, $lastweekend])->groupBy('day')->orderBy('day', 'asc')->get([
                      \DB::raw('SUM(sale_amount) as t_amount'),
                      \DB::raw('SUM(sale_discount) as t_discount'),
                      \DB::raw('DAY(an_sales.time_created) as day')
                    ]);

                    //This Month Total Sales and Profit
                    $thismonthstart = \Carbon\Carbon::now()->startOfMonth();
                    $thismonthend = \Carbon\Carbon::now();
                    $this_month_total = null;
                    if ($shop->business_type_id == 3) {
                        $this_month_total = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$thismonthstart, $thismonthend])->get([
                            \DB::raw('SUM(price) as price'),
                            \DB::raw('SUM(total_discount) as discount')
                        ]);
                    }else{
                        $this_month_total = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$thismonthstart, $thismonthend])->get([
                            \DB::raw('SUM(price) as price'),
                            \DB::raw('SUM(total_discount) as discount'),
                            \DB::raw('SUM(buying_price) as buying_price')
                        ]);
                    }

                    $lastmonthstart = new \Carbon\Carbon('first day of last month'); 
                    $lastmonthend = new \Carbon\Carbon('last day of last month');
                    $last_month_total = null;
                    if ($shop->business_type_id == 3) {
                        $last_month_total = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$lastmonthstart, $lastmonthend])->get([
                            \DB::raw('SUM(price) as price'),
                            \DB::raw('SUM(total_discount) as discount')
                        ]);
                    }else{
                        $last_month_total = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$lastmonthstart, $lastmonthend])->get([
                            \DB::raw('SUM(price) as price'),
                            \DB::raw('SUM(total_discount) as discount'),
                            \DB::raw('SUM(buying_price) as buying_price')
                        ]);
                    }
                }else{
                    $a_sales = collect([]);
                    $a_expenses = collect([]);
                    $a_products = collect([]);
                    $a_services = collect([]);
                    $a_items = collect([]);
                    $a_servitems = collect([]);
                    $a_this_month_total = collect([]);
                    $a_last_month_total = collect([]);
                    $a_this_week_amounts = collect([]);
                    $a_last_week_amounts = collect([]);
                    foreach ($shops as $key => $shop) {
                        $shop_sales = 0;
                        $shop_collect = 0;
                        $shop_debts = 0;
                        $shop_expenses = 0;

                        $tsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('sale_amount');
                        $tdiscount = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('sale_discount');
                        $ttax = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('tax_amount');

                        $treturn_amt = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('return_amount');
                        $treturn_disc = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('return_discount');
                        $treturn_tax = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('return_tax');
                    
                        $shop_sales = (($tsales-$tdiscount)+$ttax)-(($treturn_amt-$treturn_disc)+$treturn_tax);
                        
                        $shop_expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('amount');
                        $shop_collect = CustomerTransaction::where('payment', '!=', null)->where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->sum('payment');
                        
                        $shop_gpp = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->sum('price')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->sum('total_discount')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->sum('buying_price');
                        $shop_gps = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->sum('total')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->sum('total_discount');

                        $storesales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
                            \DB::raw('SUM(sale_amount) as amount'),
                            \DB::raw('SUM(sale_discount) as discount'),
                            \DB::raw('SUM(tax_amount) as tax'),
                            \DB::raw('SUM(sale_amount_paid) as amount_paid'),
                            \DB::raw('SUM(return_amount) as return_amount'),
                            \DB::raw('SUM(return_discount) as return_discount'),
                            \DB::raw('SUM(return_tax) as return_tax'),
                            \DB::raw('DATE(an_sales.time_created) as date')
                        ]);


                        foreach ($storesales as $key => $debt) {
                            $tnetsales = ($debt->amount-$debt->discount)+$debt->tax;
                            $tnetreturn = ($debt->return_amount-$debt->return_discount)+$debt->return_tax;
                            $netsales_amount = $tnetsales-$tnetreturn;
                            $shop_debts += $netsales_amount-$debt->amount_paid;
                        }
                        
                        $storeexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
                                \DB::raw('DATE(time_created) as date'),
                                \DB::raw('SUM(amount) as amount')
                            ]);

                        $storeproducts = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'products.id', '=', 'an_sale_items.product_id')->groupBy('name')->orderBy('quantity', 'desc')->take(10)->get([
                          \DB::raw('an_sale_items.product_id as product_id'),
                          \DB::raw('products.name as name'),
                          \DB::raw('an_sale_items.retail_price as unitprice'),
                          \DB::raw('an_sale_items.discount as unitdiscount'),
                          \DB::raw('SUM(quantity_sold) as quantity'),
                          \DB::raw('SUM(price) as price'),
                          \DB::raw('SUM(total_discount) as discount')
                        ]);

                        // return $products;

                        $storeservices = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->join('service_shop', 'service_id', '=', 'services.id')->groupBy('name')->orderBy('total', 'desc')->take(10)->get([
                          \DB::raw('service_sale_items.service_id as service_id'),
                          \DB::raw('services.name as name'),
                          \DB::raw('service_sale_items.price as unitprice'),
                          \DB::raw('service_sale_items.discount as unitdiscount'),
                          \DB::raw('SUM(no_of_repeatition) as quantity'),
                          \DB::raw('SUM(total) as total'),
                          \DB::raw('SUM(total_discount) as discount')
                        ]);
                        // return $products;


                        $storeitems = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
                            \DB::raw('SUM(price) as price'),
                            \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
                            \DB::raw('SUM(total_discount) as discount'),
                            \DB::raw('SUM(buying_price) as buying_price'),
                            \DB::raw('SUM(an_sale_items.input_tax) as input_vat'),
                            \DB::raw('SUM(an_sale_items.tax_amount) as output_vat'),
                            \DB::raw('DATE(an_sales.time_created) as date')
                        ]);

                        // return $products;

                        $storeservitems = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
                            \DB::raw('SUM(total) as price'),
                            \DB::raw('SUM(total_discount) as discount'),
                            \DB::raw('SUM(service_sale_items.tax_amount) as output_vat'),
                            \DB::raw('DATE(an_sales.time_created) as date')
                        ]);

                        //Get Total sales today and yesterday
                        $today = Carbon::today();
                        $yesterday = Carbon::yesterday();
                        $storetoday_sales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereDate('time_created', $today)->sum('sale_amount')-AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereDate('time_created', $today)->sum('sale_discount');
                        $storeyesterday_sales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereDate('time_created', $yesterday)->sum('sale_amount')-AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereDate('time_created', $yesterday)->sum('sale_discount');


                        //Get Weekly sales
                        $weekstart = Carbon::now()->startOfWeek();
                        $weekend = Carbon::now()->endOfWeek();

                        $storethis_week_amounts = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$weekstart, $weekend])->groupBy('day')->orderBy('day', 'asc')->get([
                          \DB::raw('SUM(sale_amount) as t_amount'),
                          \DB::raw('SUM(sale_discount) as t_discount'),
                          \DB::raw('DAY(an_sales.time_created) as day')
                        ]);
                        
                        $lastweekstart = \Carbon\Carbon::now()->subWeek()->startOfWeek();
                        $lastweekend = \Carbon\Carbon::now()->subWeek()->endOfWeek();
                        
                    
                        $storelast_week_amounts = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$lastweekstart, $lastweekend])->groupBy('day')->orderBy('day', 'asc')->get([
                          \DB::raw('SUM(sale_amount) as t_amount'),
                          \DB::raw('SUM(sale_discount) as t_discount'),
                          \DB::raw('DAY(an_sales.time_created) as day')
                        ]);

                        //This Month Total Sales and Profit
                        $thismonthstart = \Carbon\Carbon::now()->startOfMonth();
                        $thismonthend = \Carbon\Carbon::now();
                        $storethis_month_total = null;
                        if ($shop->business_type_id == 3) {
                            $storethis_month_total = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$thismonthstart, $thismonthend])->get([
                                \DB::raw('SUM(price) as price'),
                                \DB::raw('SUM(total_discount) as discount')
                            ]);
                        }else{
                            $storethis_month_total = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$thismonthstart, $thismonthend])->get([
                                \DB::raw('SUM(price) as price'),
                                \DB::raw('SUM(total_discount) as discount'),
                                \DB::raw('SUM(buying_price) as buying_price')
                            ]);
                        }

                        $lastmonthstart = new \Carbon\Carbon('first day of last month'); 
                        $lastmonthend = new \Carbon\Carbon('last day of last month');
                        $storelast_month_total = null;
                        if ($shop->business_type_id == 3) {
                            $storelast_month_total = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$lastmonthstart, $lastmonthend])->get([
                                \DB::raw('SUM(price) as price'),
                                \DB::raw('SUM(total_discount) as discount')
                            ]);
                        }else{
                            $storelast_month_total = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$lastmonthstart, $lastmonthend])->get([
                                \DB::raw('SUM(price) as price'),
                                \DB::raw('SUM(total_discount) as discount'),
                                \DB::raw('SUM(buying_price) as buying_price')
                            ]);
                        }

                        $total_sales += $shop_sales;
                        $total_debts += $shop_debts;
                        $total_collections += $shop_collect;
                        $total_expenses += $shop_expenses;
                        $today_sales += $storetoday_sales;
                        $yesterday_sales += $storeyesterday_sales;
                        $gross_profit = $shop_gpp+$shop_gps;

                        $a_sales->push($storesales);
                        $a_expenses->push($storeexpenses);
                        $a_products->push($storeproducts);
                        $a_services->push($storeservices);
                        $a_items->push($storeitems);
                        $a_servitems->push($storeservitems);
                        $a_this_month_total->push($storethis_month_total);
                        $a_last_month_total->push($storelast_month_total);
                        $a_this_week_amounts->push($storethis_week_amounts);
                        $a_last_week_amounts->push($storelast_week_amounts);

                    }

                    //Sales Collection
                    $rsales = [];
                    foreach ($a_sales->flatten(1) as $value) {
                        if (isset($rsales[$value['date']])) {
                            $rsales[$value['date']]['amount'] += $value['amount'];
                            $rsales[$value['date']]['discount'] += $value['discount'];
                            $rsales[$value['date']]['tax'] += $value['tax'];
                            $rsales[$value['date']]['return_amount'] += $value['return_amount'];
                            $rsales[$value['date']]['return_discount'] += $value['return_discount'];
                            $rsales[$value['date']]['return_tax'] += $value['return_tax'];
                            $rsales[$value['date']]['amount_paid'] += $value['amount_paid'];
                        }else{
                            $rsales[$value['date']] = $value;
                        }
                    }

                    foreach ($rsales as $key => $value) {
                        $sales->push($value);
                    }
                    
                    // Expense collections
                    $rexpenses = [];
                    foreach ($a_expenses->flatten(1) as $key => $value) {
                        if (isset($rexpenses[$value['date']])) {
                            $rexpenses[$value['date']]['amount'] += $value['amount'];
                        }else{
                            $rexpenses[$value['date']] = $value;
                        }
                    }

                    foreach ($rexpenses as $key => $value) {
                        $expenses->push($value);
                    }

                    $rproducts = [];
                    foreach ($a_products->flatten(1) as $key => $value) {
                        if (isset($rproducts[$value['name']])) {
                            $rproducts[$value['name']]['quantity'] += $value['quantity'];
                            $rproducts[$value['name']]['price'] += $value['price'];
                            $rproducts[$value['name']]['discount'] += $value['discount'];
                        }else{
                            $rproducts[$value['name']] = $value;
                        }
                    }

                    foreach ($rproducts as $key => $value) {
                        $products->push($value);
                    }

                    $rservices = [];
                    foreach ($a_services->flatten(1) as $key => $value) {
                        if (isset($rservices[$value['name']])) {
                            $rservices[$value['name']]['quantity'] += $value['quantity'];
                            $rservices[$value['name']]['total'] += $value['total'];
                            $rservices[$value['name']]['discount'] += $value['discount'];
                        }else{
                            $rservices[$value['name']] = $value;
                        }
                    }

                    foreach ($rservices as $key => $value) {
                        $services->push($value);
                    }


                    $ritems = [];
                    foreach ($a_items->flatten(1) as $key => $value) {
                        if (isset($ritems[$value['date']])) {
                            $ritems[$value['date']]['buying_price'] += $value['buying_price'];
                            $ritems[$value['date']]['price'] += $value['price'];
                            $ritems[$value['date']]['discount'] += $value['discount'];
                            $ritems[$value['date']]['tax_amount'] += $value['tax_amount'];
                            $ritems[$value['date']]['input_vat'] += $value['input_vat'];
                            $ritems[$value['date']]['output_vat'] += $value['output_vat'];
                        }else{
                            $ritems[$value['date']] = $value;
                        }
                    }

                    foreach ($ritems as $key => $value) {
                        $items->push($value);
                    }

                    $rservitems = [];
                    foreach ($a_servitems->flatten(1) as $key => $value) {
                        if (isset($rservitems[$value['date']])) {
                            $rservitems[$value['date']]['price'] += $value['price'];
                            $rservitems[$value['date']]['discount'] += $value['discount'];
                            $rservitems[$value['date']]['output_vat'] += $value['output_vat'];
                        }else{
                            $rservitems[$value['date']] = $value;
                        }
                    }

                    foreach ($rservitems as $key => $value) {
                        $servitems->push($value);
                    }

                    $rtweeks = [];
                    foreach ($a_this_week_amounts->flatten(1) as $key => $value) {
                        if (isset($rtweeks[$value['day']])) {
                            $rtweeks[$value['day']]['t_amount'] += $value['t_amount'];
                            $rtweeks[$value['day']]['t_discount'] += $value['t_discount'];
                        }else{
                            $rtweeks[$value['day']] = $value;
                        }
                    }

                    foreach ($rtweeks as $key => $value) {
                        $this_week_amounts->push($value);
                    }

                    $rlweeks = [];
                    foreach ($a_last_week_amounts->flatten(1) as $key => $value) {
                        if (isset($rlweeks[$value['day']])) {
                            $rlweeks[$value['day']]['t_amount'] += $value['t_amount'];
                            $rlweeks[$value['day']]['t_discount'] += $value['t_discount'];
                        }else{
                            $rlweeks[$value['day']] = $value;
                        }
                    }

                    foreach ($rlweeks as $key => $value) {
                        $last_week_amounts->push($value);
                    }

                    $this_month_total = $a_this_month_total->flatten(1);

                    $last_month_total = $a_last_month_total->flatten(1);
                }

                $labels = array();
                $gelabels = array();

                $salesdata = array();
                $profitsdata = array();
                $expensesdata = array();
                $netprofit = array();
                
                $grosses = array();

                foreach ($sales as $key => $sale) {
                    array_push($labels, $sale->date);
                    $tnetsales = ($sale->amount-$sale->discount)+$sale->tax;
                    $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
                    $netsales_amount = $tnetsales-$tnetreturn;
                    array_push($salesdata, $netsales_amount);
                }
                
                // foreach ($expenses as $key => $expense) {
                //     array_push($expensesdata, round($expense->amount));
                // }

                $stotals = array();
                foreach ($servitems as $key => $sale) {
                    array_push($stotals, array_merge($sale->toArray(), ['buying_price' => 0, 'input_vat' => 0]));
                }

                $arrays = array_merge($items->toArray(), $stotals);
                $grparr = array();
                foreach ($arrays as $key => $value) {
                    if (isset($grparr[$value['date']])) {
                        $grparr[$value['date']]['price'] += $value['price'];
                        $grparr[$value['date']]['buying_price'] += $value['buying_price'];
                        $grparr[$value['date']]['discount'] += $value['discount'];
                    }else{
                        $grparr[$value['date']] = $value;
                    }
                }

                foreach ($grparr as $key => $value) {
                    array_push($gelabels, $value['date']);
                    array_push($grosses, ($value['price']-$value['discount']-$value['buying_price']));
                    $expense = $expenses->where('date', $value['date'])->first();
                    if (!is_null($expense)) {
                        array_push($expensesdata, round($expense->amount));
                    }else{
                        array_push($expensesdata, 0);
                    }
                }

                // return $expensesdata;
                $date = \Carbon\Carbon::parse($payment->expire_date);
                $now = \Carbon\Carbon::now();
                $status = $date->diffInDays($now);


                $page = "Home";
                $title = "Dashboard";
                $title_sw = "Dashibodi";
                $currency = '';
                $dfc = ShopCurrency::where('shop_id', Session::get('shop_id'))->where('is_default', true)->first();
                if (!is_null($dfc)) {
                    $currency = $dfc->code;
                }else{
                    return redirect('settings')->with('warning', 'Please set your Default Currency to continue');
                }

                $settings = Setting::where('shop_id', Session::get('shop_id'))->first();

                // return $this_month_total;
                return view('home', compact('page', 'title', 'title_sw', 'shop', 'total_sales', 'total_collections', 'total_expenses', 'total_debts', 'no_customers', 'products', 'services', 'is_post_query', 'payment', 'status', 'start_date', 'end_date', 'settings', 'currency', 'currstore', 'shops', 'labels', 'salesdata', 'gelabels', 'grosses', 'expensesdata', 'gross_profit'));
            } else {
                $info = 'Dear customer your account is not activated please make payment and activate now.';
                // Alert::info("Payment Expired", $info);
                return redirect('verify-payment')->with('error', $info);
            }
        }else{
            Auth::logout();
            return redirect('login')->with('info', 'Company info not found. Try Login Again');
        }
    }

    public function pdHome(Request $request)
    {
        $page = 'Home';
        $title = 'Production Dashboard';
        return view('pd-home', compact('page', 'title'));
    }

    static function number_format_short($n)
    {
        $n_format = null;
        $suffix = null;

        if ($n >= 1000000000 || $n <= -1000000000) {
            // 1t+
            $n_format = round($n / 1000000, 1, PHP_ROUND_HALF_DOWN);
            $suffix = ' M+';
        }

        return !empty($n_format . $suffix) ? $n_format . $suffix : number_format($n, 2, '.', ',');
    }

    public function completeSignupForm()
    {
        $user = Auth::user();
        return view('auth.register-finish', compact('user'));
    }

    public function completeSignup(Request $request)
    {
        $user = User::find($request['id']);
        if (!is_null($user)) {
            $phone = $request['phone'];
            if (strlen($request['phone']) == 9) {
                $phone = '0'.$request['phone'];
            }

            $userphone = User::where('phone', $phone)->first();
            if (is_null($userphone)) {
                $user->first_name = $request['first_name'];
                $user->last_name = $request['last_name'];
                $user->phone = $phone;
                $user->country_code = $request['phone_country'];
                $user->dial_code = $request['dial_code'];
                $user->country = $request['country'];
                $user->default_page = 'home';
                $user->save();

                if (!empty($request['agent_code'])) {
                    $smagent = SocialMediaAgent::where('agent_code', $request['agent_code'])->first();
                    if (!is_null($smagent)) {
                        $agent = User::find($smagent->user_id);                        
                        $acustomer = AgentCustomer::create([
                            'agent_id' => $agent->id,
                            'user_id' => $user->id,
                            'agent_code' => $smagent->agent_code
                        ]);
                    }
                }

                $company = $user->companies()->wherePivot('is_default', true)->first();
                if (!is_null($company)) {
                    Session::put('company_id', $company->id);
                }else{
                    $company = $user->companies()->first();
                    $company->pivot->is_default = true;
                    $company->pivot->save();
                    Session::put('company_id', $company->id);
                }

                return redirect('user-profile')->with('success', 'Account successfully created. Please setup your Businesses');
            }else{
                return redirect()->back()->with('error', 'Phone number already used by another user');
            }
        }else{
            return redirect('login')->with('error', 'Registration failed to complete please Login to continue..');
        }
    }

    public function closeShop()
    {
        $shop = Shop::find(Session::get('shop_id'));
        
        dispatch(new DailyClosingStockJob($shop));

        return redirect()->back()->with('info', 'Closing in progress');
    }
}
