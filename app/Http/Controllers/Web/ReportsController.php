<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Log;
use Session;
use DB;
use Auth;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Setting;
use App\Models\User;
use App\Models\AnSale;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\Service;
use App\Models\Customer;
use App\Models\CashOut;
use App\Models\CashIn;
use App\Models\SaleReturn;
use App\Models\Device;
use App\Models\DeviceSale;
use App\Models\DeviceExpense;
use App\Models\Purchase;
use App\Models\AccountTransaction;
use App\Models\TransferOrderItem;
use App\Models\Grade;
use App\Models\OCAmount;
use App\Models\CustomerTransaction;
use App\Models\SupplierTransaction;
use App\Models\SalePayment;
use App\Models\ExpensePayment;
use App\Models\BusinessValue;
use App\Models\Category;
use App\Models\PurchasePayment;
use App\Models\AccountStatement;

class ReportsController extends Controller
{


    function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
      $page = 'Reporting';
      $title = 'Reports';
      $title_sw = 'Ripoti';

      $shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $shop->id)->first();
      $products = $shop->products()->count();
      $services = $shop->services()->count();

      return view('reports.index', compact('page', 'title', 'title_sw', 'shop','settings', 'products', 'services'));
    }


    public function detailedProfitLoss(Request $request)
    {
      $page = 'Reports';
      $title = 'Detailed Daily Profit/Loss Report';
      $title_sw = 'Ripoti ya Kina Faida/Hasara';

      $shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $shop->id)->first();
      $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
      $customers = Customer::where('shop_id', $shop->id)->get();
      $users = $shop->users()->get();

      $now = Carbon::now();
      $start = $now->startOfDay();
      $end = \Carbon\Carbon::now();
      $start_date = $start->format('Y-m-d');            
      $end_date = $end->format('Y-m-d');
      $devices = null;
      $servsales = null;
      $expenses = null;
      $ctnexpenses = null;
      $all_expenses = null;
      $all_ctnexpenses = null;
      $total_serv_selling = null;
      $tsales = null;
      $total_paid = null;
      $total_vat = null;
      $total_expenses = null;
      if ($shop->business_type_id == 3) {
        $devices = Device::where('shop_id', $shop->id)->get();
      }

      $device = null;
      if (!empty($request['device_id'])) {
        $device = Device::find($request['device_id']);
      }
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

      $categories = Category::where('shop_id', $shop->id)->get();
      $category = Category::find($request['category_id']);

      $sales = null; $returns = null;

      $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('retail_price')->groupBy('discount')->groupBy('unit_cost')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
          \DB::raw('products.name as name'),
          \DB::raw('SUM(quantity_sold) as quantity'),          
          \DB::raw('retail_price as retail_price'),
          \DB::raw('SUM(price) as price'),
          \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('unit_cost as unit_cost'),
          \DB::raw('SUM(buying_price) as buying_price'),          
          \DB::raw('an_sale_items.time_created as created_at')
      ]);

      $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('an_sales.time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->groupBy('retail_price')->groupBy('unit_cost')->groupBy('products.name')->orderBy('sale_return_items.created_at', 'desc')->get([
          \DB::raw('products.name as name'),
          \DB::raw('SUM(quantity) as quantity'),      
          \DB::raw('retail_price as retail_price'),
          \DB::raw('SUM(price) as price'),
          \DB::raw('SUM(sale_return_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('unit_cost as unit_cost'),
          \DB::raw('SUM(buying_price) as buying_price'), 
          \DB::raw('sale_return_items.created_at as created_at')
      ]);

      if (!is_null($device)) {
        $servsales = DeviceSale::where('device_id', $device->id)->join('an_sales', 'an_sales.id', '=', 'device_sales.an_sale_id')->where('an_sales.shop_id', $shop->id)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->groupBy('price')->groupBy('services.name')->orderBy('service_sale_items.time_created', 'desc')->get([
          \DB::raw('services.name as name'),
          \DB::raw('SUM(no_of_repeatition) as repeatition'),
          \DB::raw('price as price'),
          \DB::raw('SUM(total) as total'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('service_sale_items.time_created as created_at')
        ]);
      }else{

        $servsales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->groupBy('price')->groupBy('services.name')->orderBy('service_sale_items.time_created', 'desc')->get([
          \DB::raw('services.name as name'),
          \DB::raw('SUM(no_of_repeatition) as repeatition'),
          \DB::raw('price as price'),
          \DB::raw('SUM(total) as total'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('service_sale_items.time_created as created_at')
        ]);
      }

      $expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->groupBy('expense_type')->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('SUM(amount) as amount'),
            \DB::raw('SUM(qty) as qty'),
            \DB::raw('SUM(exp_vat) as exp_vat')
      ]);

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.gr-report', compact('page', 'title', 'title_sw', 'shop','settings', 'reporttime', 'duration', 'duration_sw', 'sales', 'returns', 'servsales', 'devices', 'device', 'expenses', 'is_post_query', 'start_date', 'end_date', 'start', 'end', 'defcurr'));
    }

    public function profitReports(Request $request)
    {

      $page = 'Reports';
      $title = 'Profit Reports';
      $title_sw = 'Ripoti ya Faida';
      
      $shop = Shop::find(Session::get('shop_id'));
      
      $products = $shop->products()->get();
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

      $sales = null;
      $total_selling = 0;
      $total_buying = 0;
     
      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('retail_price')->groupBy('unit_cost')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
          \DB::raw('products.id as id'),
          \DB::raw('products.name as name'),          
          \DB::raw('SUM(quantity_sold) as quantity'),
          \DB::raw('unit_cost as unit_cost'),
          \DB::raw('SUM(buying_price) as buying_price'),
          \DB::raw('retail_price as retail_price'),
          \DB::raw('SUM(price) as price'),
          \DB::raw('discount as discount'),          
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('an_sale_items.time_created as created_at')]);

          
        $total_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('price')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('total_discount');
                  
        $total_buying = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('buying_price');

        $total_return_selling = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('an_sales.time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->sum('price')-SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('an_sales.time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->sum('total_discount');
                  
        $total_return_buying = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('an_sales.time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->sum('buying_price');

        $total_gprofit = ($total_selling-$total_buying);

        $total_return_rprofit = ($total_return_selling-$total_return_buying);

        $total_gross_profit = $total_gprofit-$total_return_rprofit;
                
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.profits', compact('page', 'title', 'title_sw', 'shop', 'reporttime', 'duration', 'duration_sw', 'sales', 'total_selling', 'total_buying', 'total_gross_profit', 'start', 'end', 'is_post_query', 'start_date', 'end_date'));
    }

    public function salesByProduct(Request $request)
    {
      $page = 'Reports';
      $title = 'Sales By Product Reports';
      $title_sw = 'Ripoti ya Mauzo kwa Bidhaa';

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
      $sales = null;
      $total_selling = 0;
      $total_buying = 0;
      $input_tax = 0;
      $output_tax = 0;
      $vat_payable = 0;
     
      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $customer = null;
      $product = null;
      if (!empty($request['customer_id'])) {
        $customer = Customer::find($request['customer_id']);
        if (!empty($request['product_id']) && $request['product_id'] != 0) {
          $product = Product::find($request['product_id']);

          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('customer_id', $customer->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->where('products.id', $request['product_id'])->groupBy('retail_price')->groupBy('unit_cost')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
            \DB::raw('products.id as id'),
            \DB::raw('products.name as name'),          
            \DB::raw('SUM(quantity_sold) as quantity'),
            \DB::raw('retail_price as retail_price'),
            \DB::raw('SUM(price) as price'),
            \DB::raw('discount as discount'),          
            \DB::raw('SUM(total_discount) as total_discount'),
            \DB::raw('unit_cost as unit_cost'),
            \DB::raw('SUM(buying_price) as buying_price'),
            \DB::raw('SUM(input_tax) as input_tax'),
            \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
            \DB::raw('an_sale_items.time_created as created_at')]);

          $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->where('an_sales.customer_id', $customer->id)->whereBetween('an_sales.time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->where('products.id', $request['product_id'])->groupBy('retail_price')->groupBy('unit_cost')->groupBy('products.name')->orderBy('sale_return_items.created_at', 'desc')->get([
            \DB::raw('products.name as name'),
            \DB::raw('SUM(quantity) as quantity'),      
            \DB::raw('retail_price as retail_price'),
            \DB::raw('sale_return_items.tax_amount as tax'),
            \DB::raw('SUM(price) as price'),
            \DB::raw('SUM(sale_return_items.tax_amount) as tax_amount'),
            \DB::raw('discount as discount'),
            \DB::raw('SUM(total_discount) as total_discount'),
            \DB::raw('unit_cost as unit_cost'),
            \DB::raw('SUM(buying_price) as buying_price'), 
            \DB::raw('sale_return_items.created_at as created_at')
          ]);
        }else{

          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('customer_id', $customer->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('retail_price')->groupBy('unit_cost')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
            \DB::raw('products.id as id'),
            \DB::raw('products.name as name'),          
            \DB::raw('SUM(quantity_sold) as quantity'),
            \DB::raw('retail_price as retail_price'),
            \DB::raw('SUM(price) as price'),\DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
            \DB::raw('discount as discount'),          
            \DB::raw('SUM(total_discount) as total_discount'),
            \DB::raw('unit_cost as unit_cost'),
            \DB::raw('SUM(buying_price) as buying_price'),
            \DB::raw('SUM(input_tax) as input_tax'),
            \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
            \DB::raw('an_sale_items.time_created as created_at')]);

          $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->where('an_sales.customer_id', $customer->id)->whereBetween('an_sales.time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->groupBy('retail_price')->groupBy('unit_cost')->groupBy('products.name')->orderBy('sale_return_items.created_at', 'desc')->get([
            \DB::raw('products.name as name'),
            \DB::raw('SUM(quantity) as quantity'),      
            \DB::raw('retail_price as retail_price'),
            \DB::raw('sale_return_items.tax_amount as tax'),
            \DB::raw('SUM(price) as price'),
            \DB::raw('SUM(sale_return_items.tax_amount) as tax_amount'),
            \DB::raw('discount as discount'),
            \DB::raw('SUM(total_discount) as total_discount'),
            \DB::raw('unit_cost as unit_cost'),
            \DB::raw('SUM(buying_price) as buying_price'), 
            \DB::raw('sale_return_items.created_at as created_at')
          ]);
        }
      }else{
        if (!empty($request['product_id']) && $request['product_id'] != 0) {
          $product = Product::find($request['product_id']);

          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->where('products.id', $request['product_id'])->groupBy('retail_price')->groupBy('unit_cost')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
            \DB::raw('products.id as id'),
            \DB::raw('products.name as name'),          
            \DB::raw('SUM(quantity_sold) as quantity'),
            \DB::raw('retail_price as retail_price'),
            \DB::raw('SUM(price) as price'),\DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
            \DB::raw('discount as discount'),          
            \DB::raw('SUM(total_discount) as total_discount'),
            \DB::raw('unit_cost as unit_cost'),
            \DB::raw('SUM(buying_price) as buying_price'),
            \DB::raw('SUM(input_tax) as input_tax'),
            \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
            \DB::raw('an_sale_items.time_created as created_at')]);

          $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('an_sales.time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->where('products.id', $request['product_id'])->groupBy('retail_price')->groupBy('unit_cost')->groupBy('products.name')->orderBy('sale_return_items.created_at', 'desc')->get([
            \DB::raw('products.name as name'),
            \DB::raw('SUM(quantity) as quantity'),      
            \DB::raw('retail_price as retail_price'),
            \DB::raw('sale_return_items.tax_amount as tax'),
            \DB::raw('SUM(price) as price'),
            \DB::raw('SUM(sale_return_items.tax_amount) as tax_amount'),
            \DB::raw('discount as discount'),
            \DB::raw('SUM(total_discount) as total_discount'),
            \DB::raw('unit_cost as unit_cost'),
            \DB::raw('SUM(buying_price) as buying_price'), 
            \DB::raw('sale_return_items.created_at as created_at')
          ]);
        }else{

          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('retail_price')->groupBy('unit_cost')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
            \DB::raw('products.id as id'),
            \DB::raw('products.name as name'),          
            \DB::raw('SUM(quantity_sold) as quantity'),
            \DB::raw('retail_price as retail_price'),
            \DB::raw('SUM(price) as price'),\DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
            \DB::raw('discount as discount'),          
            \DB::raw('SUM(total_discount) as total_discount'),
            \DB::raw('unit_cost as unit_cost'),
            \DB::raw('SUM(buying_price) as buying_price'),
            \DB::raw('SUM(input_tax) as input_tax'),
            \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
            \DB::raw('an_sale_items.time_created as created_at')]);

          $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('an_sales.time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->groupBy('retail_price')->groupBy('unit_cost')->groupBy('products.name')->orderBy('sale_return_items.created_at', 'desc')->get([
            \DB::raw('products.name as name'),
            \DB::raw('SUM(quantity) as quantity'),      
            \DB::raw('retail_price as retail_price'),
            \DB::raw('sale_return_items.tax_amount as tax'),
            \DB::raw('SUM(price) as price'),
            \DB::raw('SUM(sale_return_items.tax_amount) as tax_amount'),
            \DB::raw('discount as discount'),
            \DB::raw('SUM(total_discount) as total_discount'),
            \DB::raw('unit_cost as unit_cost'),
            \DB::raw('SUM(buying_price) as buying_price'), 
            \DB::raw('sale_return_items.created_at as created_at')
          ]);
        }
      }
               
      $customers = Customer::where('shop_id', $shop->id)->get(); 
      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.sales-by-product', compact('page', 'title', 'title_sw', 'shop', 'reporttime', 'duration', 'duration_sw', 'customer', 'customers', 'sales', 'returns', 'is_post_query', 'start_date', 'end_date', 'product', 'settings'));
    }

    

    public function topSellingProducts(Request $request)
    {
      $page = 'Reports';
      $title = 'Top Selling Products Reports';
      $title_sw = 'Ripoti ya Bidhaa Zinazotoka Zaidi';

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
      $sales = null;
      $total_selling = 0;
      $total_buying = 0;
      $input_tax = 0;
      $output_tax = 0;
      $vat_payable = 0;
     
      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('quantity_sold')->groupBy('products.name')->orderBy('quantity', 'desc')->get([
            \DB::raw('products.id as id'),
            \DB::raw('products.name as name'),          
            \DB::raw('SUM(quantity_sold) as quantity'),
            \DB::raw('SUM(price) as price'),
            \DB::raw('SUM(total_discount) as total_discount')
      ]);

               
      $customers = Customer::where('shop_id', $shop->id)->get(); 
      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.top-selling', compact('page', 'title', 'title_sw', 'shop', 'reporttime', 'duration', 'duration_sw', 'sales', 'is_post_query', 'start_date', 'end_date', 'settings'));
    }

    

    public function salesByService(Request $request)
    {
      $page = 'Reports';
      $title = 'Sales By Service Reports';
      $title_sw = 'Ripoti ya Mauzo kwa Huduma';

      $shop = Shop::find(Session::get('shop_id'));
      $customers = Customer::where('shop_id', $shop->id)->select('id', 'name')->get();
      $services = $shop->services()->select('services.id as id', 'name')->get();
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

      $sales = null;
      $total_selling = 0;
      $total_buying = 0;
     
      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $customer = null;
      $service = null;
      if (!empty($request['customer_id'])) {
        $customer = Customer::find($request['customer_id']);
        if (!empty($request['service_id'])) {
          $service = Service::find($request['service_id']);
          
          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('customer_id', $customer->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->where('services.id', $service->id)->groupBy('price')->groupBy('services.name')->groupBy('service_sale_items.time_created')->orderBy('service_sale_items.time_created', 'desc')->get([
            \DB::raw('services.name as name'),
            \DB::raw('SUM(no_of_repeatition) as quantity'),
            \DB::raw('price as price'),
            \DB::raw('SUM(total) as total'),
            \DB::raw('discount as discount'),
            \DB::raw('SUM(total_discount) as total_discount'),
            \DB::raw('SUM(service_sale_items.tax_amount) as tax_amount'),
            \DB::raw('service_sale_items.time_created as created_at')
          ]);

          $total_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('customer_id', $customer->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->where('services.id', $service->id)->sum('total')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->where('services.id', $service->id)->sum('total_discount');                

          $total_gross_profit = $total_selling;
        }else{

          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('customer_id', $customer->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->groupBy('price')->groupBy('services.name')->groupBy('service_sale_items.time_created')->orderBy('service_sale_items.time_created', 'desc')->get([
            \DB::raw('services.name as name'),
            \DB::raw('SUM(no_of_repeatition) as quantity'),
            \DB::raw('price as price'),
            \DB::raw('SUM(total) as total'),
            \DB::raw('discount as discount'),
            \DB::raw('SUM(total_discount) as total_discount'),
            \DB::raw('SUM(service_sale_items.tax_amount) as tax_amount'),
            \DB::raw('service_sale_items.time_created as created_at')
          ]);

          $total_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('customer_id', $customer->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total_discount');                


          $total_gross_profit = $total_selling;
        }
      }else{
        if (!empty($request['service_id'])) {
          $service = Service::find($request['service_id']);
          
          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->where('services.id', $service->id)->groupBy('price')->groupBy('services.name')->groupBy('service_sale_items.time_created')->orderBy('service_sale_items.time_created', 'desc')->get([
            \DB::raw('services.name as name'),
            \DB::raw('SUM(no_of_repeatition) as quantity'),
            \DB::raw('price as price'),
            \DB::raw('SUM(total) as total'),
            \DB::raw('discount as discount'),
            \DB::raw('SUM(total_discount) as total_discount'),
            \DB::raw('SUM(service_sale_items.tax_amount) as tax_amount'),
            \DB::raw('service_sale_items.time_created as created_at')
          ]);

          $total_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->where('services.id', $service->id)->sum('total')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->where('services.id', $service->id)->sum('total_discount');                

          $total_gross_profit = $total_selling;
        }else{

          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->groupBy('price')->groupBy('services.name')->groupBy('service_sale_items.time_created')->orderBy('service_sale_items.time_created', 'desc')->get([
            \DB::raw('services.name as name'),
            \DB::raw('SUM(no_of_repeatition) as quantity'),
            \DB::raw('price as price'),
            \DB::raw('SUM(total) as total'),
            \DB::raw('discount as discount'),
            \DB::raw('SUM(total_discount) as total_discount'),
            \DB::raw('SUM(service_sale_items.tax_amount) as tax_amount'),
            \DB::raw('service_sale_items.time_created as created_at')
          ]);

          $total_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total_discount');                


          $total_gross_profit = $total_selling;
        }
      }

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.sales-by-service', compact('page', 'title', 'title_sw', 'shop', 'reporttime', 'duration', 'duration_sw', 'sales', 'total_selling', 'total_gross_profit', 'service', 'services', 'customer', 'customers', 'is_post_query', 'start_date', 'end_date'));
    }

    public function debts(Request $request)
    {
      $page = 'Reports';
      $title = 'Debts Reports';
      $title_sw = 'Ripoti ya Madeni';

      $this->shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $this->shop->id)->first();
      $customers = Customer::where('shop_id', $this->shop->id)->get();
      $users = $this->shop->users()->get();

      $now = Carbon::now();
      $this->start = $now->startOfDay();
      $this->end = \Carbon\Carbon::now();
      $start_date = date('Y-m-d', strtotime($this->start));
      $end_date = date('Y-m-d', strtotime($this->end));
      
      //check if user opted for date range
      $is_post_query = false;
      if (!empty($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $this->start = $request['start_date'].' 00:00:00';
        $this->end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }

      $duration = 'From '.date('d-m-Y', strtotime($this->start)).' To '.date('d-m-Y', strtotime($this->end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($this->start)).' Mpaka '.date('d-m-Y', strtotime($this->end)).'.';

      $debts = null;
      
      $debts = AnSale::where('an_sales.shop_id', $this->shop->id)->where('an_sales.is_deleted', false)->where('status', 'Unpaid')->whereBetween('an_sales.time_created', [$this->start, $this->end])->orWhere(function($query){
          $query->where('an_sales.shop_id', $this->shop->id)->where('an_sales.is_deleted', false)->where('an_sales.status', 'Partially Paid')->whereBetween('an_sales.time_created', [$this->start, $this->end]);
        })->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id',  'an_sales.time_created as time_created', 'customers.id as customer_id', 'customers.name as name', 'invoice_no', 'sale_amount', 'sale_discount', 'tax_amount',  'return_amount', 'return_discount', 'return_tax', 'sale_amount_paid', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.sale_type as sale_type', 'an_sales.status as status', 'an_sales.comments as comments', 'grade_id', 'year')->orderBy('an_sales.time_created', 'desc')->get();

      $totaldebts = array();
      $total_ob = 0;
      $total_invoices = 0;
      foreach ($customers as $key => $customer) {
        $obtrans = CustomerTransaction::where('customer_id', $customer->id)->where('is_ob', true)->where('shop_id', $this->shop->id)->first();
        $opening_balance = 0;
        if (!is_null($obtrans)) {
          $opening_balance = $obtrans->amount-$obtrans->ob_paid;
        }

        $totalsales = AnSale::where('shop_id', $this->shop->id)->where('is_deleted', false)->where('customer_id', $customer->id)->get([
          DB::raw('SUM(sale_amount) as sale_amount'),
          DB::raw('SUM(sale_discount) as sale_discount'),
          DB::raw('SUM(tax_amount) as tax_amount'),
          DB::raw('SUM(return_amount) as return_amount'),
          DB::raw('SUM(return_discount) as return_discount'),
          DB::raw('SUM(return_tax) as return_tax'),
          DB::raw('SUM(sale_amount_paid) as amount_paid')
        ]);
        $new_invoices = 0;
        foreach ($totalsales as $key => $sale) {
          $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
          $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
          $netsales_amount = $tnetsales-$tnetreturn;
          $new_invoices += $netsales_amount-$sale->amount_paid;
        }

        $total_d = $opening_balance+$new_invoices;

        $total_ob += $opening_balance;
        $total_invoices += $new_invoices;

        if ($total_d > 0) {
          array_push($totaldebts, ['customer_id' => $customer->id, 'cust_no' => $customer->cust_no, 'name' => $customer->name, 'phone' => $customer->phone, 'opening_balance' => $opening_balance, 'new_invoices' => $new_invoices, 'total' =>  $total_d]);
        }
      }
      
      $shop = $this->shop;
      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.debts', compact('page', 'title', 'title_sw', 'shop', 'debts', 'totaldebts', 'total_ob', 'total_invoices', 'duration', 'duration_sw', 'reporttime', 'is_post_query', 'start_date', 'end_date', 'shop', 'settings'));
    }

    public function sales(Request $request)
    {
      $page = 'Reports';
      $title = 'Sales Reports';
      $title_sw = 'Ripoti ya Mauzo';

      $shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $shop->id)->first();
      $customers = Customer::where('shop_id', $shop->id)->get();
      $grades = Grade::where('shop_id', $shop->id)->get();
      $users = $shop->users()->permission('create-invoice')->get();

      $years = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereNotNull('year')->select('year')->groupBy('year')->orderBy('year', 'asc')->get();

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

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $sales = null;
      $customer = null;
      $grade = null;
      $year = null;
      $user = null;
      if (empty($request['customer_id'])) {
        if (!empty($request['user_id'])) {
          $user = User::find($request['user_id']);
          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->where('users.id', $user->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id',  'an_sales.time_created as time_created', 'customers.id as customer_id', 'customers.name as name', 'invoice_no', 'sale_amount', 'sale_discount', 'tax_amount',  'return_amount', 'return_discount', 'return_tax', 'sale_amount_paid', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.sale_type as sale_type', 'an_sales.status as status', 'an_sales.comments as comments', 'grade_id', 'year')->orderBy('an_sales.time_created', 'desc')->get();
        }else{
          if (!empty($request['grade_id'])) {
            $grade = Grade::find($request['grade_id']);
            if (!empty($request['year'])) {
              $year = $request['year'];
              $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->where('grade_id', $request['grade_id'])->where('year', $year)->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id',  'an_sales.time_created as time_created', 'customers.id as customer_id', 'customers.name as name', 'invoice_no', 'sale_amount', 'sale_discount', 'tax_amount',  'return_amount', 'return_discount', 'return_tax', 'sale_amount_paid', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.sale_type as sale_type', 'an_sales.status as status', 'an_sales.comments as comments', 'grade_id', 'year')->orderBy('an_sales.time_created', 'desc')->get();
            }else{
              $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->where('grade_id', $request['grade_id'])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id',  'an_sales.time_created as time_created', 'customers.id as customer_id', 'customers.name as name', 'invoice_no', 'sale_amount', 'sale_discount', 'tax_amount',  'return_amount', 'return_discount', 'return_tax', 'sale_amount_paid', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.sale_type as sale_type', 'an_sales.status as status', 'an_sales.comments as comments', 'grade_id', 'year')->orderBy('an_sales.time_created', 'desc')->get();
            }
          }else{
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id',  'an_sales.time_created as time_created', 'customers.id as customer_id', 'customers.name as name', 'invoice_no', 'sale_amount', 'sale_discount', 'tax_amount',  'return_amount', 'return_discount', 'return_tax', 'sale_amount_paid', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.sale_type as sale_type', 'an_sales.status as status', 'an_sales.comments as comments', 'grade_id', 'year')->orderBy('an_sales.time_created', 'desc')->get();
          }
        }
      }else{
        $customer = Customer::find($request['customer_id']);
        if (!empty($request['user_id'])) {
          $user = User::find($request['user_id']);
          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->where('users.id', $user->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->select('an_sales.id as id', 'invoice_no', 'an_sales.time_created as time_created', 'sale_amount', 'sale_discount', 'sale_amount_paid', 'tax_amount', 'sale_type', 'status', 'an_sales.updated_at as updated_at', 'first_name', 'last_name', 'name')->get();
        }else{
          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->select('an_sales.id as id',  'an_sales.time_created as time_created', 'customers.id as customer_id', 'customers.name as name', 'invoice_no', 'sale_amount', 'sale_discount', 'tax_amount',  'return_amount', 'return_discount', 'return_tax', 'sale_amount_paid', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.sale_type as sale_type', 'an_sales.status as status', 'an_sales.comments as comments', 'grade_id', 'year')->orderBy('an_sales.time_created', 'desc')->get();
        }
      }

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.sales', compact('page', 'title', 'title_sw', 'shop', 'sales', 'duration', 'duration_sw', 'reporttime', 'customers', 'customer', 'users', 'user', 'is_post_query', 'start_date', 'end_date', 'settings', 'grade', 'grades', 'year', 'years'));
    }

    public function salesReturns(Request $request)
    {
      $page = 'Reports';
      $title = 'Sales Return Reports';
      $title_sw = 'Ripoti ya Mauzo yaliyorudishwa';

      $shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $shop->id)->first();
      $customers = Customer::where('shop_id', $shop->id)->get();
      $users = $shop->users()->permission('create-invoice')->get();

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

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $returns = null;
      $customer = null;
      $user = null;
      if (empty($request['customer_id'])) {
        if (!empty($request['user_id'])) {
          $user = User::find($request['user_id']);
          $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->whereBetween('sale_returns.created_at', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('users', 'users.id', '=', 'an_sales.user_id')->where('users.id', $user->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.return_tax_amount as return_tax_amount', 'sale_returns.reason as reason', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'users.first_name as first_name', 'last_name')->orderBy('sale_returns.created_at', 'desc')->get();
        }else{

          $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->whereBetween('sale_returns.created_at', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.return_tax_amount as return_tax_amount', 'sale_returns.reason as reason', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'users.first_name as first_name', 'last_name')->orderBy('sale_returns.created_at', 'desc')->get();
        }
      }else{
        $customer = Customer::find($request['customer_id']);
        if (!empty($request['user_id'])) {
          $user = User::find($request['user_id']);

          $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->whereBetween('sale_returns.created_at', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('users', 'users.id', '=', 'an_sales.user_id')->where('users.id', $user->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->select('customers.name as name', 'customers.id as customer_id', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.return_tax_amount as return_tax_amount', 'sale_returns.reason as reason', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'users.first_name as first_name', 'last_name')->orderBy('sale_returns.created_at', 'desc')->get();
        }else{
          $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->whereBetween('sale_returns.created_at', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->select('customers.name as name', 'customers.id as customer_id', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.return_tax_amount as return_tax_amount', 'sale_returns.reason as reason', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'users.first_name as first_name','last_name')->orderBy('sale_returns.created_at', 'desc')->get();
        }
      }

      $total_amount = 0;
      $total_discount = 0;
      $total_tax = 0;

      foreach ($returns as $key => $value) {
        $total_amount += $value->sale_return_amount;
        $total_discount += $value->sale_return_discount;
        $total_tax += $value->return_tax_amount;
      }

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.sales-returns', compact('page', 'title', 'title_sw', 'shop', 'returns', 'total_amount', 'total_discount', 'total_tax', 'duration', 'duration_sw', 'reporttime', 'customers', 'customer', 'users', 'user', 'is_post_query', 'start_date', 'end_date', 'settings'));
    }

    public function expenses(Request $request)
    {
      $page = 'Reports';
      $title = 'Operating Expenses Reports';
      $title_sw = 'Ripoti ya Gharama za uendeshaji';
      
      $shop = Shop::find(Session::get('shop_id'));
      $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();

      $exptypes = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->groupBy('expense_type')->get();
      $users = $shop->users()->get();

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

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $qty_produced = 0;
      $texpenses = null;
      $expenses = null;
      $expcat = null;
      $expense1 = null;
      $expcategories = ExpenseCategory::where('shop_id', $shop->id)->get();

      if (!empty($request['expense_category_id'])) {
        $expcat = ExpenseCategory::find($request['expense_category_id']);
        if (!empty($request['expense'])) {
          $expense1 = Expense::where('expense_type', $request['expense'])->first();
          $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expenses.expense_type', $request['expense'])->whereBetween('expenses.time_created', [$start, $end])->where('expense_category_id', $request['expense_category_id'])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expense_category_id', 'expenses.supplier_id as supplier_id', 'expenses.expense_type as expense_type', 'expenses.amount as amount', 'expenses.description as description', 'expenses.exp_vat as exp_vat', 'expenses.wht_rate as wht_rate', 'expenses.wht_amount as wht_amount', 'expenses.exp_type as exp_type', 'expenses.status as status', 'expenses.time_created as created_at')->orderBy('time_created', 'desc')->get();
        }else{
          $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$start, $end])->where('expense_category_id', $request['expense_category_id'])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expense_category_id', 'expenses.supplier_id as supplier_id', 'expenses.expense_type as expense_type', 'expenses.amount as amount', 'expenses.description as description', 'expenses.exp_vat as exp_vat', 'expenses.wht_rate as wht_rate', 'expenses.wht_amount as wht_amount', 'expenses.exp_type as exp_type', 'expenses.status as status', 'expenses.time_created as created_at')->orderBy('time_created', 'desc')->get();
        }

        $texpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$start, $end])->where('expense_category_id', $request['expense_category_id'])->groupBy('expense_type')->get([
          \DB::raw('expense_type as expense_type'),
          \DB::raw('SUM(amount) as amount'),
          \DB::raw('SUM(exp_vat) as exp_vat'),
          \DB::raw('wht_rate as wht_rate'),
          \DB::raw('SUM(wht_amount) as wht_amount')
        ]);

        if ($expcat->is_included_in_prod_cost) {   
          //Get quantity of product produced
          $qty_produced = 1230;
        }
      }else{
        if (!empty($request['expense'])) {
          $expense1 = Expense::where('expense_type', $request['expense'])->first();
          $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expenses.expense_type', $request['expense'])->whereBetween('expenses.time_created', [$start, $end])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expense_category_id', 'expenses.supplier_id as supplier_id', 'expenses.expense_type as expense_type', 'expenses.amount as amount', 'expenses.description as description', 'expenses.exp_vat as exp_vat', 'expenses.wht_rate as wht_rate', 'expenses.wht_amount as wht_amount', 'expenses.exp_type as exp_type', 'expenses.status as status', 'expenses.time_created as created_at')->orderBy('time_created', 'desc')->get();
        }else{
          $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$start, $end])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expense_category_id', 'expenses.supplier_id as supplier_id', 'expenses.expense_type as expense_type', 'expenses.amount as amount', 'expenses.description as description', 'expenses.exp_vat as exp_vat', 'expenses.wht_rate as wht_rate', 'expenses.wht_amount as wht_amount', 'expenses.exp_type as exp_type', 'expenses.status as status', 'expenses.time_created as created_at')->orderBy('time_created', 'desc')->get();
        }

        $texpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$start, $end])->groupBy('expense_type')->get([
          \DB::raw('expense_type as expense_type'),
          \DB::raw('SUM(amount) as amount'),
          \DB::raw('SUM(exp_vat) as exp_vat'),
          \DB::raw('wht_rate as wht_rate'),
          \DB::raw('SUM(wht_amount) as wht_amount')
        ]);
      }

      $total = 0; 
      $total_vat = 0;
      $total_wht = 0;


      foreach ($expenses as $key => $value) {
        $total += $value->amount;
        $total_vat += $value->exp_vat;
        $total_wht += $value->wht_amount;
      }

      $settings = Setting::where('shop_id', $shop->id)->first();
      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.expenses', compact('page', 'title', 'title_sw', 'shop', 'settings', 'exptypes', 'expenses', 'expense1', 'expcategories', 'expcat', 'total', 'total_vat', 'total_wht', 'duration', 'duration_sw', 'reporttime', 'is_post_query', 'start_date', 'end_date', 'texpenses', 'defcurr', 'qty_produced'));
    }

    public function singleExpenseReport(Request $request, $type)
    {
      $page = 'Reports';
      $title = 'Operating Expenses Reports';
      $title_sw = 'Ripoti ya Gharama za uendeshaji';
      
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

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';
        
      $texpenses = Expense::where('expense_type', $type)->where('shop_id', $shop->id)->whereBetween('expenses.time_created', [$start, $end])->groupBy('time_created')->get([
        \DB::raw('DATE(time_created) as date'),
        \DB::raw('SUM(amount) as amount'),
        \DB::raw('description'),
        \DB::raw('SUM(exp_vat) as exp_vat'),
        \DB::raw('wht_rate as wht_rate'),
        \DB::raw('SUM(wht_amount) as wht_amount')]);

      $total = 0; 
      $total_vat = 0;
      $total_wht = 0;


      foreach ($texpenses as $key => $value) {
        $total += $value->amount;
        $total_vat += $value->exp_vat;
        $total_wht += $value->wht_amount;
      }
      

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.single-expense', compact('page', 'title', 'title_sw', 'shop', 'settings', 'total', 'total_vat', 'total_wht', 'duration', 'duration_sw', 'reporttime', 'is_post_query', 'start_date', 'end_date', 'texpenses', 'type'));
    }

    public function totalAmounts(Request $request)
    {
      
      $shop = Shop::find(Session::get('shop_id'));
      $user = Auth::user();
      $settings = Setting::where('shop_id', $shop->id)->first();
      $now = Carbon::now();
      $start = $now->startOfDay();
      $end = \Carbon\Carbon::now();
      $start_date = $start->format('Y-m-d');            
      $end_date = $end->format('Y-m-d');
      
      //check if user opted for date range
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

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $totals = array();

      $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
        \DB::raw('SUM(price) as price'),
        \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
        \DB::raw('SUM(total_discount) as discount'),
        \DB::raw('SUM(buying_price) as buying_price'),
        \DB::raw('DATE(an_sales.time_created) as date')
      ]);

      $ptotals = array();
      foreach ($sales as $key => $sale) {
        $return_price = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('time_created', [$sale->date.' 00:00:00', $sale->date.' 23:59:59'])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->sum('price');
        $return_discount = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('time_created', [$sale->date.' 00:00:00', $sale->date.' 23:59:59'])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->sum('total_discount');
        $return_tax = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('time_created', [$sale->date.' 00:00:00', $sale->date.' 23:59:59'])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->sum('sale_return_items.tax_amount');
        $return_cost = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('time_created', [$sale->date.' 00:00:00', $sale->date.' 23:59:59'])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->sum('buying_price');
        
        $expenseamount = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$sale->date.' 00:00:00', $sale->date.' 23:59:59'])->sum('amount');
      
        array_push($ptotals, array_merge($sale->toArray(), ['return_price' => $return_price, 'return_discount' => $return_discount, 'return_tax' => $return_tax, 'return_cost' => $return_cost, 'amount' => $expenseamount]));
      }

      $servsales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
        \DB::raw('SUM(total) as price'),
        \DB::raw('SUM(total_discount) as discount'),
        \DB::raw('SUM(service_sale_items.tax_amount) as tax_amount'),
        \DB::raw('DATE(an_sales.time_created) as date')
      ]);

      $stotals = array();
      foreach ($servsales as $key => $sale) {
        array_push($stotals, array_merge($sale->toArray(), ['buying_price' => 0, 'amount' => 0, 'return_price' => 0, 'return_discount' => 0, 'return_tax' => 0, 'return_cost' => 0, 'amount' => 0]));
      }

      $arrays = array_merge($ptotals, $stotals);

      $sum = array();
      foreach ($arrays as $array) {
        if (isset($sum[$array['date']])) {
          $sum[$array['date']]['price'] += ($array['price']-$array['return_price']);
          $sum[$array['date']]['discount'] += ($array['discount']-$array['return_discount']);
          $sum[$array['date']]['buying_price'] += ($array['buying_price']-$array['return_cost']);
          $sum[$array['date']]['tax_amount'] += ($array['tax_amount']-$array['return_tax']);
          $sum[$array['date']]['amount'] += $array['amount'];
        } else {
          $sum[$array['date']]['date'] = $array['date'];
          $sum[$array['date']]['price'] = ($array['price']-$array['return_price']);
          $sum[$array['date']]['discount'] = ($array['discount']-$array['return_discount']);
          $sum[$array['date']]['buying_price'] = ($array['buying_price']-$array['return_cost']);
          $sum[$array['date']]['tax_amount'] = ($array['tax_amount']-$array['return_tax']);
          $sum[$array['date']]['amount'] = $array['amount'];
        }
      } 

      foreach ($sum as $key => $value) {
        array_push($totals, $value);
      }
      

      usort($totals, function($dateArray1, $dateArray2) {
          $date1 = strtotime(date("Y-m-t", strtotime($dateArray1['date'])));
          $date2 = strtotime(date("Y-m-t", strtotime($dateArray2['date'])));
          // Log::info($date1);
          // Log::info($date2);
          return $date1 - $date2;
      });      
      // return $totals;

      $tsales = 0;
      $tcsales = 0;
      $texpenses = 0;
      $labels = array();
      $grosses = array();
      $expensesdata = [];
      $netprofits = array();

      foreach ($totals as $total) {
        array_push($labels, $total['date']);
        array_push($grosses, (($total['price']-$total['discount'])+$total['tax_amount'])-$total['buying_price']);
        array_push($expensesdata, $total['amount']+0);
        array_push($netprofits, ((($total['price']-$total['discount'])+$total['tax_amount'])-$total['buying_price'])-$total['amount']);

        $tsales += (($total['price']-$total['discount'])+$total['tax_amount']);
        $tcsales += $total['buying_price'];
        $texpenses += $total['amount'];
      }

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      $page = "Reports";
      $title = 'Daily profit or loss report';
      $title_sw = 'Ripoti ya Faida au Hasara ya Kila siku';
      return view('reports.daily-profit', compact('page', 'title', 'title_sw', 'totals', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date', 'shop', 'user', 'settings', 'tsales', 'tcsales', 'texpenses', 'labels', 'grosses', 'expensesdata', 'netprofits'));
    }
    
    public function monthlyTotalSales(Request $request)
    {
      $shop = Shop::find(Session::get('shop_id'));
      $user = Auth::user();
      $settings = Setting::where('shop_id', $shop->id)->first();
      $now = Carbon::now();
      $start = $now->startOfYear();
      $end = \Carbon\Carbon::now();
      $start_date = $start->format('Y-m-d');            
      $end_date = $end->format('Y-m-d');
      
      //check if user opted for date range
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

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $shops = $user->shops()->where('is_warehouse', false)->get();

      $data = array();
      $databynames = array();
      foreach ($shops as $key => $mshop) {
        $mshopsales = AnSale::where('shop_id', $mshop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->select(
          \DB::raw('SUM(sale_amount) as sale_amount'),
          \DB::raw('SUM(sale_discount) as sale_discount'),
          \DB::raw('SUM(tax_amount) as tax_amount'),
          \DB::raw('SUM(return_amount) as return_amount'),
          \DB::raw('SUM(return_discount) as return_discount'),
          \DB::raw('SUM(return_tax) as return_tax'),
          \DB::raw("CONCAT_WS(', ',MONTHNAME(time_created),YEAR(time_created)) as date"))->groupBy('date')->get();

        foreach ($mshopsales as $key => $sale) {
          $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
          $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
          $netsales_amount = $tnetsales-$tnetreturn;

          array_push($data, ['date' => $sale->date, 'netamount' => $netsales_amount]);

          $firstday = date('Y-m-01', strtotime($sale->date));
          $lastday = date('Y-m-t', strtotime($sale->date));
          // Log::info($firstday.' - '.$lastday);
          $mshop_cashsales = AnSale::where('shop_id', $mshop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->where('sale_type', 'Cash')->select(
            \DB::raw('SUM(sale_amount) as sale_amount'),
            \DB::raw('SUM(sale_discount) as sale_discount'),
            \DB::raw('SUM(tax_amount) as tax_amount'),
            \DB::raw('SUM(return_amount) as return_amount'),
            \DB::raw('SUM(return_discount) as return_discount'),
            \DB::raw('SUM(return_tax) as return_tax'),
            \DB::raw("CONCAT_WS(', ',MONTHNAME(time_created),YEAR(time_created)) as date"))->groupBy('date')->get();
          $netcash = 0;
          foreach ($mshop_cashsales as $key => $sale) {
            $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
            $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
            $netsales_amount = $tnetsales-$tnetreturn;
            $netcash += $netsales_amount;
          }

          $mshop_creditsales = AnSale::where('shop_id', $mshop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->where('sale_type', 'Credit')->select(
          \DB::raw('SUM(sale_amount) as sale_amount'),
          \DB::raw('SUM(sale_discount) as sale_discount'),
          \DB::raw('SUM(tax_amount) as tax_amount'),
          \DB::raw('SUM(return_amount) as return_amount'),
          \DB::raw('SUM(return_discount) as return_discount'),
          \DB::raw('SUM(return_tax) as return_tax'),
          \DB::raw("CONCAT_WS(', ',MONTHNAME(time_created),YEAR(time_created)) as date"))->groupBy('date')->get();
          $netcredit = 0;
          foreach ($mshop_creditsales as $key => $sale) {
            $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
            $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
            $netsales_amount = $tnetsales-$tnetreturn;
            $netcredit += $netsales_amount;
          }

          array_push($databynames, ['name' => $mshop->name, 'date' => $sale->date, 'netcash' => $netcash, 'netcredit' => $netcredit]);
        }
      }

      // return $databynames;
      $result = array();
      foreach ($data as $key => $value) {
        if (isset($result[$value['date']])) {
          $result[$value['date']]['netamount'] += $value['netamount'];
        }else{
          $result[$value['date']] = $value;
        }
      }

      $totalsales = array();
      foreach ($result as $key => $value) {
        $shoptotal = array();
        foreach ($shops as $key => $tshop) {
          foreach ($databynames as $key => $dbm) {
            if (isset($shoptotal[$value['date'].'-'.$tshop->name])) {
              if ($dbm['name'] == $tshop->name && $dbm['date'] == $value['date']) {
                $shoptotal[$value['date'].'-'.$tshop->name]['netcash'] += $dbm['netcash'];
                $shoptotal[$value['date'].'-'.$tshop->name]['netcredit'] += $dbm['netcredit'];
              }else{
                $shoptotal[$value['date'].'-'.$tshop->name]['netcash'] += 0;
                $shoptotal[$value['date'].'-'.$tshop->name]['netcredit'] += 0;
              }
            }else{
              if ($dbm['name'] == $tshop->name && $dbm['date'] == $value['date']) {
                $shoptotal[$value['date'].'-'.$tshop->name]['netcash'] = $dbm['netcash'];
                $shoptotal[$value['date'].'-'.$tshop->name]['netcredit'] = $dbm['netcredit'];
              }else{
                $shoptotal[$value['date'].'-'.$tshop->name]['netcash'] = 0;
                $shoptotal[$value['date'].'-'.$tshop->name]['netcredit'] = 0;
              }
            }
          }
        }
        // Log::info($shoptotal);
        $newshoptotal =array();
        foreach ($shoptotal as $key => $tot) {
          $newkey = str_replace($value['date'].'-', '', $key);
          array_push($newshoptotal, [$newkey => $tot]);
        }
        // Log::info($newshoptotal);
        array_push($totalsales, array_merge($value, $newshoptotal));
      }

      usort($totalsales, function($dateArray1, $dateArray2) {
          $date1 = strtotime(date("Y-m-t", strtotime($dateArray1['date'])));
          $date2 = strtotime(date("Y-m-t", strtotime($dateArray2['date'])));
          // Log::info($date1);
          // Log::info($date2);
          return $date1 - $date2;
      });

      // return $totalsales;
    
      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      $page = "Reports";
      $title = 'Monthy Sales Report';
      $title_sw = 'Ripoti ya Mauzo ya Mwezi';

      return view('reports.monthly-sales-report', compact('page', 'title', 'title_sw', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date', 'shop', 'settings', 'totalsales', 'shops'));
    }

    public function dailyTotalSales(Request $request)
    {
      $shop = Shop::find(Session::get('shop_id'));
      $user = Auth::user();
      $settings = Setting::where('shop_id', $shop->id)->first();
      $now = Carbon::now();
      $start = $now->startOfMonth();
      $end = \Carbon\Carbon::now();
      $start_date = $start->format('Y-m-d');            
      $end_date = $end->format('Y-m-d');
      
      //check if user opted for date range
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

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $shops = $user->shops()->where('is_warehouse', false)->get();

      $data = array();
      $databynames = array();
      foreach ($shops as $key => $mshop) {
        $mshopsales = AnSale::where('shop_id', $mshop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->select(
          \DB::raw('SUM(sale_amount) as sale_amount'),
          \DB::raw('SUM(sale_discount) as sale_discount'),
          \DB::raw('SUM(tax_amount) as tax_amount'),
          \DB::raw('SUM(return_amount) as return_amount'),
          \DB::raw('SUM(return_discount) as return_discount'),
          \DB::raw('SUM(return_tax) as return_tax'),
          \DB::raw("DATE(time_created) as date"))->groupBy('date')->get();

        foreach ($mshopsales as $key => $sale) {
          $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
          $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
          $netsales_amount = $tnetsales-$tnetreturn;

          array_push($data, ['date' => $sale->date, 'netamount' => $netsales_amount]);

          $mshop_cashsales = AnSale::where('shop_id', $mshop->id)->where('is_deleted', false)->whereBetween('time_created', [$sale->date.' 00:00:00', $sale->date.' 23:59:59'])->where('sale_type', 'Cash')->select(
            \DB::raw('SUM(sale_amount) as sale_amount'),
            \DB::raw('SUM(sale_discount) as sale_discount'),
            \DB::raw('SUM(tax_amount) as tax_amount'),
            \DB::raw('SUM(return_amount) as return_amount'),
            \DB::raw('SUM(return_discount) as return_discount'),
            \DB::raw('SUM(return_tax) as return_tax'),
            \DB::raw("DATE(time_created) as date"))->groupBy('date')->get();
          $netcash = 0;
          foreach ($mshop_cashsales as $key => $sale) {
            $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
            $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
            $netsales_amount = $tnetsales-$tnetreturn;
            $netcash += $netsales_amount;
          }

          $mshop_creditsales = AnSale::where('shop_id', $mshop->id)->where('is_deleted', false)->whereBetween('time_created', [$sale->date.' 00:00:00', $sale->date.' 23:59:59'])->where('sale_type', 'Credit')->select(
          \DB::raw('SUM(sale_amount) as sale_amount'),
          \DB::raw('SUM(sale_discount) as sale_discount'),
          \DB::raw('SUM(tax_amount) as tax_amount'),
          \DB::raw('SUM(return_amount) as return_amount'),
          \DB::raw('SUM(return_discount) as return_discount'),
          \DB::raw('SUM(return_tax) as return_tax'),
          \DB::raw("DATE(time_created) as date"))->groupBy('date')->get();
          $netcredit = 0;
          foreach ($mshop_creditsales as $key => $sale) {
            $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
            $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
            $netsales_amount = $tnetsales-$tnetreturn;
            $netcredit += $netsales_amount;
          }

          array_push($databynames, ['name' => $mshop->name, 'date' => $sale->date, 'netcash' => $netcash, 'netcredit' => $netcredit]);
        }
      }

      // return $databynames;
      $result = array();
      foreach ($data as $key => $value) {
        if (isset($result[$value['date']])) {
          $result[$value['date']]['netamount'] += $value['netamount'];
        }else{
          $result[$value['date']] = $value;
        }
      }

      $totalsales = array();
      foreach ($result as $key => $value) {
        $shoptotal = array();
        foreach ($shops as $key => $tshop) {
          foreach ($databynames as $key => $dbm) {
            if (isset($shoptotal[$value['date'].'-'.$tshop->name])) {
              if ($dbm['name'] == $tshop->name && $dbm['date'] == $value['date']) {
                $shoptotal[$value['date'].'-'.$tshop->name]['netcash'] += $dbm['netcash'];
                $shoptotal[$value['date'].'-'.$tshop->name]['netcredit'] += $dbm['netcredit'];
              }else{
                $shoptotal[$value['date'].'-'.$tshop->name]['netcash'] += 0;
                $shoptotal[$value['date'].'-'.$tshop->name]['netcredit'] += 0;
              }
            }else{
              if ($dbm['name'] == $tshop->name && $dbm['date'] == $value['date']) {
                $shoptotal[$value['date'].'-'.$tshop->name]['netcash'] = $dbm['netcash'];
                $shoptotal[$value['date'].'-'.$tshop->name]['netcredit'] = $dbm['netcredit'];
              }else{
                $shoptotal[$value['date'].'-'.$tshop->name]['netcash'] = 0;
                $shoptotal[$value['date'].'-'.$tshop->name]['netcredit'] = 0;
              }
            }
          }
        }
        // Log::info($shoptotal);
        $newshoptotal =array();
        foreach ($shoptotal as $key => $tot) {
          $newkey = str_replace($value['date'].'-', '', $key);
          array_push($newshoptotal, [$newkey => $tot]);
        }
        // Log::info($newshoptotal);
        array_push($totalsales, array_merge($value, $newshoptotal));
      }

      usort($totalsales, function($dateArray1, $dateArray2) {
          $date1 = strtotime(date("Y-m-d", strtotime($dateArray1['date'])));
          $date2 = strtotime(date("Y-m-d", strtotime($dateArray2['date'])));
          // Log::info($date1);
          // Log::info($date2);
          return $date1 - $date2;
      });

      // return $totalsales;
    
      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      $page = "Reports";
      $title = 'Daily Total Sales Report';
      $title_sw = 'Ripoti ya Mauzo ya Mwezi';

      return view('reports.daily-total-sales-report', compact('page', 'title', 'title_sw', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date', 'shop', 'settings', 'totalsales', 'shops'));
    }

    public function consolidated(Request $request)
    {
      $shop = Shop::find(Session::get('shop_id'));
      $user = Auth::user();
      $settings = Setting::where('shop_id', $shop->id)->first();
      $now = Carbon::now();
      $start = $now->startOfDay();
      $end = \Carbon\Carbon::now();
      $start_date = $start->format('Y-m-d');            
      $end_date = $end->format('Y-m-d');
      
      //check if user opted for date range
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

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $shops = $user->shops()->where('is_warehouse', false)->get();

      $data = array();
      foreach ($shops as $key => $mshop) {
        $mshopsales = AnSale::where('an_sales.shop_id', $mshop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->get([
          \DB::raw('SUM(price) as price'),
          \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('SUM(total_discount) as discount'),
          \DB::raw('SUM(buying_price) as buying_price')
        ]);

        foreach ($mshopsales as $sale) {
          $return_price = SaleReturn::where('sale_returns.shop_id', $mshop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->sum('price');
          $return_discount = SaleReturn::where('sale_returns.shop_id', $mshop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->sum('total_discount');
          $return_tax = SaleReturn::where('sale_returns.shop_id', $mshop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->sum('sale_return_items.tax_amount');
          $return_cost = SaleReturn::where('sale_returns.shop_id', $mshop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->whereBetween('time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->join('products', 'products.id', '=', 'sale_return_items.product_id')->sum('buying_price');

          $expenseamount = Expense::where('shop_id', $mshop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('amount');
          
          array_push($data, array_merge($sale->toArray(), ['return_price' => $return_price, 'return_discount' => $return_discount, 'return_tax' => $return_tax, 'return_cost' => $return_cost, 'amount' => $expenseamount],  ['bizname' => $mshop->name]));
        }

        $mshopsservsales = AnSale::where('an_sales.shop_id', $mshop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
          \DB::raw('SUM(total) as price'),
          \DB::raw('SUM(total_discount) as discount'),
          \DB::raw('SUM(service_sale_items.tax_amount) as tax_amount')
        ]);

        foreach ($mshopsservsales as $msale) {
          array_push($data, array_merge($msale->toArray(), ['buying_price' => 0, 'return_price' => 0, 'return_discount' => 0, 'return_tax' => 0, 'return_cost' => 0, 'amount' => 0], ['bizname' => $mshop->name]));
        }
      }

      $sum = array();
      foreach ($data as $array) {
        if (isset($sum[$array['bizname']])) {
          $sum[$array['bizname']]['price'] += ($array['price']-$array['return_price']);
          $sum[$array['bizname']]['discount'] += ($array['discount']-$array['return_discount']);
          $sum[$array['bizname']]['buying_price'] += ($array['buying_price']-$array['return_cost']);
          $sum[$array['bizname']]['tax_amount'] += ($array['tax_amount']-$array['return_tax']);
          $sum[$array['bizname']]['amount'] += $array['amount'];
        } else {
          $sum[$array['bizname']]['bizname'] = $array['bizname'];
          $sum[$array['bizname']]['price'] = ($array['price']-$array['return_price']);
          $sum[$array['bizname']]['discount'] = ($array['discount']-$array['return_discount']);
          $sum[$array['bizname']]['buying_price'] = ($array['buying_price']-$array['return_cost']);
          $sum[$array['bizname']]['tax_amount'] = ($array['tax_amount']-$array['return_tax']);
          $sum[$array['bizname']]['amount'] = $array['amount'];
        }
      } 

      $totals = array();
      foreach ($sum as $key => $value) {
        array_push($totals, $value);
      }
      
      $tsales = 0;
      $tcsales = 0;
      $texpenses = 0;
      foreach ($totals as $total) {
        $tsales += (($total['price']-$total['discount'])+$total['tax_amount']);
        $tcsales += ($total['buying_price']);
        $texpenses += $total['amount'];
      }

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      $page = "Reports";
      $title = 'Consolidated profit or loss report';
      $title_sw = 'Ripoti ya Faida au Hasara iliyojuishwa';

      return view('reports.consolidated', compact('page', 'title', 'title_sw', 'totals', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date', 'shop', 'settings', 'tsales', 'tcsales', 'texpenses'));
    }
    public function collectionsReport(Request $request)
    {
      $page = 'Reports';
      $title = 'Collections Reports';
      $title_sw = 'Ripoti za Makusanyo';
      $shop = Shop::find(Session::get('shop_id'));
      $customers = Customer::where('shop_id', $shop->id)->get(); 
      $customer = Customer::where('id', $request['customer_id'])->where('shop_id', $shop->id)->first(); 
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
     
      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      if (!is_null($customer)) {
        $collections = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereBetween('sale_payments.pay_date', [$start, $end])->select('customers.cust_no as cust_no', 'customers.name as name', 'sale_payments.pay_mode as pay_mode', 'sale_payments.bank_name as bank_name', 'sale_payments.cheque_no as cheque_no', 'sale_payments.pay_date as pay_date', 'sale_payments.receipt_no as receipt_no', 'sale_payments.amount as amount', 'an_sales.sale_type as sale_type')->get();

        $debt_collections = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('sale_type', 'Credit')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereBetween('sale_payments.pay_date', [$start, $end])->select('customers.cust_no as cust_no', 'customers.name as name', 'sale_payments.pay_mode as pay_mode', 'sale_payments.bank_name as bank_name', 'sale_payments.cheque_no as cheque_no', 'sale_payments.pay_date as pay_date', 'sale_payments.receipt_no as receipt_no', 'sale_payments.amount as amount', 'an_sales.sale_type as sale_type')->get();

      }else{
        $collections = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereBetween('sale_payments.pay_date', [$start, $end])->select('customers.cust_no as cust_no', 'customers.name as name', 'sale_payments.pay_mode as pay_mode', 'sale_payments.bank_name as bank_name', 'sale_payments.cheque_no as cheque_no', 'sale_payments.pay_date as pay_date', 'sale_payments.receipt_no as receipt_no', 'sale_payments.amount as amount', 'an_sales.sale_type as sale_type')->get();

        $debt_collections = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('sale_type', 'Credit')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereBetween('sale_payments.pay_date', [$start, $end])->select('customers.cust_no as cust_no', 'customers.name as name', 'sale_payments.pay_mode as pay_mode', 'sale_payments.bank_name as bank_name', 'sale_payments.cheque_no as cheque_no', 'sale_payments.pay_date as pay_date', 'sale_payments.receipt_no as receipt_no', 'sale_payments.amount as amount', 'an_sales.sale_type as sale_type')->get();
      }

      // return $collections;

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.financial.collections-report', compact('page', 'title', 'title_sw', 'shop', 'duration', 'duration_sw', 'start_date', 'end_date', 'is_post_query', 'collections', 'debt_collections', 'reporttime', 'customers', 'customer'));

    }

    public function summaryReport(Request $request)
    {
      $page = 'Reports';
      $title = 'summary Report';
      $title_sw = 'Ripoti ya Muhtasari';

      $shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $shop->id)->first();

      $customers = Customer::where('shop_id', $shop->id)->get();
      $users = $shop->users()->get();

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

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $tsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('sale_amount');
      $tdiscount = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('sale_discount');
      $ttax = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('tax_amount');

      $treturn_amt = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('return_amount');
      $treturn_disc = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('return_discount');
      $treturn_tax = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('return_tax');
                
      $total_sales = (($tsales-$tdiscount)+$ttax);
      $total_returns = (($treturn_amt-$treturn_disc)+$treturn_tax);
      
      $total_expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('amount');
      
      $cashpayments = SalePayment::where('sale_payments.shop_id', $shop->id)->where('sale_payments.is_deleted', false)->join('an_sales', 'an_sales.id', '=', 'sale_payments.an_sale_id')->whereBetween('time_created', [$start, $end])->sum('amount');
      $debtpayments = SalePayment::where('sale_payments.shop_id', $shop->id)->where('sale_payments.is_deleted', false)->whereBetween('pay_date', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'sale_payments.an_sale_id')->whereDate('an_sales.time_created', '<', $start)->sum('amount');

      $prepayments = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereDate('pay_date', '<', $start)->sum('amount');

      $npres = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereDate('pay_date', '<', $start)->count();

      $dpsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->get([
        \DB::raw('sale_amount as amount'),
        \DB::raw('sale_discount as discount'),
        \DB::raw('tax_amount as tax'),
        \DB::raw('sale_amount_paid as amount_paid'),
        \DB::raw('return_amount as return_amount'),
        \DB::raw('return_discount as return_discount'),
        \DB::raw('return_tax as return_tax')
      ]);

      $paid_expenses = ExpensePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->sum('amount');

      $purchase_payments = PurchasePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->sum('amount');

      $total_cashout = CashOut::where('shop_id', $shop->id)->whereBetween('out_date', [$start, $end])->sum('amount');
      $ncouts = CashOut::where('shop_id', $shop->id)->whereBetween('out_date', [$start, $end])->count();
      $purchpays = PurchasePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->count();
      $paidexps = ExpensePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->count();
      $tcols = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->count();
      $nsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->count();

      $tpaids = 0;
      $nreturns = 0;
      $ndebts = 0;
      $total_debts = 0;

      foreach ($dpsales as $key => $debt) {
        $tnetsales = ($debt->amount-$debt->discount)+$debt->tax;
        $tnetreturn = ($debt->return_amount-$debt->return_discount)+$debt->return_tax;
        $netsales_amount = $tnetsales-$tnetreturn;
        if ($netsales_amount-$debt->amount_paid > 0) {
          $total_debts += $netsales_amount-$debt->amount_paid;
          $ndebts++;
        }
        if ($debt->amount_paid > 0) {
          $tpaids++;
        }
        if ($tnetreturn > 0) {
          $nreturns++;
        }
      }

      $tpdebts = $tcols-$tpaids;
      $total_collections = $cashpayments+$debtpayments;
      $closing_balance = $total_collections-$paid_expenses-$purchase_payments-$total_cashout;

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.report-summary', compact('page', 'title', 'title_sw', 'shop', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date', 'settings', 'total_sales', 'total_returns', 'total_debts','cashpayments', 'debtpayments', 'prepayments', 'total_expenses', 'paid_expenses', 'purchase_payments', 'total_collections', 'total_cashout', 'closing_balance', 'start', 'end', 'nsales', 'ndebts', 'nreturns', 'npres', 'tcols', 'paidexps', 'purchpays', 'tpaids', 'tpdebts', 'ncouts'));
    }
    
    public function discountBySales(Request $request)
    {
        $page = 'Reports';
        $title = 'Discount By Sales Reports';
        $title_sw = 'Ripoti ya Punguzo Kwa Mauzo';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $customers = Customer::where('shop_id', $shop->id)->get();
        $grades = Grade::where('shop_id', $shop->id)->get();
        $users = $shop->users()->permission('create-invoice')->get();

        $years = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereNotNull('year')->select('year')->groupBy('year')->orderBy('year', 'asc')->get();

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

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $sales = null;
      $customer = null;
      $grade = null;
      $year = null;
      $user = null;
      if (empty($request['customer_id'])) {
        if (!empty($request['user_id'])) {
          $user = User::find($request['user_id']);
          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->where('users.id', $user->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'invoice_no', 'an_sales.time_created as time_created', 'sale_amount', 'sale_discount', 'sale_amount_paid', 'tax_amount', 'return_amount', 'return_discount', 'return_tax', 'sale_type', 'status', 'first_name', 'last_name', 'name')->orderBy('an_sales.time_created', 'desc')->get();
        }else{
          if (!empty($request['grade_id'])) {
            $grade = Grade::find($request['grade_id']);
            if (!empty($request['year'])) {
              $year = $request['year'];
              $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->where('grade_id', $request['grade_id'])->where('year', $year)->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'invoice_no', 'an_sales.time_created as time_created', 'sale_amount', 'sale_discount', 'sale_amount_paid', 'tax_amount', 'return_amount', 'return_discount', 'return_tax', 'sale_type', 'status', 'first_name', 'last_name', 'name')->orderBy('an_sales.time_created', 'desc')->get();
            }else{
              $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->where('grade_id', $request['grade_id'])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'invoice_no', 'an_sales.time_created as time_created', 'sale_amount', 'sale_discount', 'sale_amount_paid', 'tax_amount', 'return_amount', 'return_discount', 'return_tax', 'sale_type', 'status', 'first_name', 'last_name', 'name')->orderBy('an_sales.time_created', 'desc')->get();
            }
          }else{
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'invoice_no', 'an_sales.time_created as time_created', 'sale_amount', 'sale_discount', 'sale_amount_paid', 'tax_amount', 'return_amount', 'return_discount', 'return_tax', 'sale_type', 'status', 'first_name', 'last_name', 'name')->orderBy('an_sales.time_created', 'desc')->get();
          }
        }
      }else{
        $customer = Customer::find($request['customer_id']);
        if (!empty($request['user_id'])) {
          $user = User::find($request['user_id']);
          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->where('users.id', $user->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->select('an_sales.id as id', 'invoice_no', 'an_sales.time_created as time_created', 'sale_amount', 'sale_discount', 'sale_amount_paid', 'tax_amount', 'sale_type', 'status', 'first_name', 'last_name', 'name')->get();
        }else{
          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->select('an_sales.id as id', 'invoice_no', 'an_sales.time_created as time_created', 'sale_amount', 'sale_discount', 'sale_amount_paid', 'tax_amount', 'return_amount', 'return_discount', 'return_tax', 'sale_type', 'status', 'first_name', 'last_name', 'name')->orderBy('an_sales.time_created', 'desc')->get();
        }
      }

      // return $sales;
      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.discounts.discount-by-sales', compact('page', 'title', 'title_sw', 'shop', 'sales', 'duration', 'duration_sw', 'reporttime', 'customers', 'customer', 'users', 'user', 'is_post_query', 'start_date', 'end_date', 'settings', 'grade', 'grades', 'year', 'years'));
   
    }
}
