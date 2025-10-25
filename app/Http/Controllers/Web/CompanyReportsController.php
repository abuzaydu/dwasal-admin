<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Session;
use \Carbon\Carbon;
use \Carbon\CarbonPeriod;
use DB;
use App\Models\Company;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Setting;
use App\Models\SalePayment;
use App\Models\ExpensePayment;
use App\Models\PurchasePayment;
use App\Models\RmPurchasePayment;
use App\Models\PmPurchasePayment;
use App\Models\PlcPayment;
use App\Models\MohCostPayment;
use App\Models\CashIn;
use App\Models\CashOut;
use App\Models\Category;
use App\Models\ServCategory;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\BasicBalanceSheet;
use App\Models\MonthlyBalanceSheet;
use App\Models\GeneralLedger;

class CompanyReportsController extends Controller
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
     */
    public function index()
    {
        $page = 'Company Reports';
        $title = 'Company Reports';

        return view('reports.company.index', compact('page', 'title'));
    }

    public function managementReport(Request $request)
    {
        $page = 'Management Report';
        $title = 'Management Report';
        $years = array();
        $dbDate = Carbon::parse('2024-01-10');
        $diffYears = Carbon::now()->diffInYears($dbDate);
        for ($i = $diffYears; $i >= 0; $i--) {
            $year = Carbon::today()->subYears($i)->format('Y');
            array_push($years, array(
                'year' => $year
            ));
        }

        $curyear = Carbon::today()->format('Y');
        if (!empty($request['year'])) {
            $curyear = $request['year'];
        }

        $now = Carbon::now();
        $start = $now->startOfYear();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['year'])){
            $date = Carbon::createFromFormat('d F Y', '05 January '.$request['year']);
            $start = $date->startOfMonth()->format('Y-m-d');
            $start_date = $start;
            $date1 = Carbon::createFromFormat('d F Y', '05 December '.$request['year']);
            $end = $date1->endOfMonth()->format('Y-m-d');
            $end_date = $end;
            $is_post_query = true;
        }elseif (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        $period = CarbonPeriod::create($start_date, $end_date)->month();

        $months = collect($period)->map(function (Carbon $date) {
            return [
                'name' => $date->monthName,
                'year' => $date->year,
                'date' => $date
            ];
        });

        $currshop = null;
        $company = Company::find(Session::get('company_id'));
        $shops = $company->shops()->select('id', 'name')->get();
        $cshops = $shops;
        if (!empty($request['shop_id'])) {
            $currshop = Shop::find($request['shop_id']);
            $shops = $company->shops()->where('id', $currshop->id)->select('id', 'name')->get();
        }

        $msaletotals = array();
        $mcostotals = array();
        $margintotals = array();
        $expensetotals = array();
        $netincometotals = array();
        foreach ($months as $key => $month) {
            $firstday = date('Y-m-01 00:00:00', strtotime($month['date']));
            $lastday = date('Y-m-t 23:59:59', strtotime($month['date']));
            $mtotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $mshopsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->select(
                    \DB::raw('SUM(sale_amount) as sale_amount'),
                    \DB::raw('SUM(sale_discount) as sale_discount'),
                    \DB::raw('SUM(tax_amount) as tax_amount'),
                    \DB::raw('SUM(return_amount) as return_amount'),
                    \DB::raw('SUM(return_discount) as return_discount'),
                    \DB::raw('SUM(return_tax) as return_tax'))->get();
                $netsales_amount = 0;
                foreach ($mshopsales as $key => $sale) {
                    $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                    $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
                    $netsales_amount += $tnetsales-$tnetreturn;
                }
                $mtotal_amt += $netsales_amount;
            }
            array_push($msaletotals, ['total_rev' => $mtotal_amt]);

            $mcso_amt = 0;
            foreach ($shops as $key => $shop) {
                $mcso_amt += AnSaleItem::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->sum('buying_price');
            }

            $mexp_cos = 0;
            foreach ($shops as $key => $shop) {
                $mexp_cos += Expense::where('shop_id', $shop->id)->whereBetween('time_created', [$firstday, $lastday])->where('is_deleted', false)->where('is_cost_of_sale', true)->sum('amount');
            }

            array_push($mcostotals, ['total_cos' => $mcso_amt+$mexp_cos]);

            $total_mag = $mtotal_amt-($mcso_amt+$mexp_cos);
            array_push($margintotals, ['total_mag' => $total_mag]);

            $total_exp = 0;
            foreach ($shops as $key => $shop) {
                $total_exp += Expense::where('shop_id', $shop->id)->whereBetween('time_created', [$firstday, $lastday])->where('is_deleted', false)->where('is_cost_of_sale', false)->sum('amount');
            }

            array_push($expensetotals, ['total_exp' => $total_exp]);

            $total_net = $total_mag-$total_exp;
            array_push($netincometotals, ['total_net' => $total_net]);
        }

        // Log::info($msaletotals);


        $msales = [];
        $mcostofsales = [];
        $mmargins = [];
        
        foreach ($shops as $key => $shop) {
            //Product Categories
            $shopcategories = Category::where('shop_id', $shop->id)->get();
            foreach ($shopcategories as $key => $scat) {
                // Log::info('Prccessing sales for '.$scat->name);
                $netmrev = array();
                $netcos = array();
                $netmargin = array();
                foreach ($months as $key => $month) {
                    $firstday = date('Y-m-01 00:00:00', strtotime($month['date']));
                    $lastday = date('Y-m-t 23:59:59', strtotime($month['date']));

                    $mshopsales = AnSaleItem::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->where('category_id', $scat->id)->get([
                            \DB::raw('SUM(price) as price'),         
                            \DB::raw('SUM(total_discount) as total_discount'),
                            \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount')
                        ]);

                    $netsales_amount = 0;
                    foreach ($mshopsales as $key => $sale) {
                        $tnetsales = ($sale->price-$sale->total_discount)+$sale->tax_amount;
                        $netsales_amount += $tnetsales;
                    }

                    array_push($netmrev, ['month' => date('m-Y',strtotime($month['date'])), 'amount' => $netsales_amount]);


                    $mshopcos = AnSaleItem::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->where('category_id', $scat->id)->sum('buying_price');

                    array_push($netcos, ['month' => date('m-Y',strtotime($month['date'])), 'cos' => $mshopcos]);

                    $mshopmargin = $netsales_amount-$mshopcos;
                    array_push($netmargin, ['month' => date('m-Y',strtotime($month['date'])), 'margin' => $mshopmargin]);

                }

                array_push($msales, ['name' => $scat->name, 'shopsales' => $netmrev]);
                array_push($mcostofsales, ['name' => $scat->name, 'shopcosales' => $netcos]);
                array_push($mmargins, ['name' => $scat->name, 'shopmargins' => $netmargin]);
            }

            // Other Products
            $ucatp = AnSaleItem::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->where('category_id', null)->count();
            if ($ucatp > 0) {
                
                $up_netmrev = array();
                $up_netcos = array();
                $up_netmargin = array();
                foreach ($months as $key => $month) {
                    $firstday = date('Y-m-01 00:00:00', strtotime($month['date']));
                    $lastday = date('Y-m-t 23:59:59', strtotime($month['date']));

                    $mshopsales = AnSaleItem::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->where('category_id', null)->get([
                            \DB::raw('SUM(price) as price'),         
                            \DB::raw('SUM(total_discount) as total_discount'),
                            \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount')
                        ]);
                    $netsales_amount = 0;
                    foreach ($mshopsales as $key => $sale) {
                        $tnetsales = ($sale->price-$sale->total_discount)+$sale->tax_amount;
                        $netsales_amount += $tnetsales;
                    }

                    array_push($up_netmrev, ['month' => date('m-Y',strtotime($month['date'])), 'amount' => $netsales_amount]);

                    $mshopcos = AnSaleItem::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->where('category_id', null)->sum('buying_price');

                    array_push($up_netcos, ['month' => date('m-Y',strtotime($month['date'])), 'cos' => $mshopcos]);

                    $mshopmargin = $netsales_amount-$mshopcos;
                    array_push($up_netmargin, ['month' => date('m-Y',strtotime($month['date'])), 'margin' => $mshopmargin]);
                }

                array_push($msales, ['name' => 'Other Products', 'shopsales' => $up_netmrev]);
                array_push($mcostofsales, ['name' => 'Other Products', 'shopcosales' => $up_netcos]);
                array_push($mmargins, ['name' => 'Other Products', 'shopmargins' => $up_netmargin]);
            }

            // Service Categories
            $shopservcategories = ServCategory::where('shop_id', $shop->id)->get();
            foreach ($shopservcategories as $key => $scat) {
                // Log::info('Prccessing sales for '.$scat->name);
                $netmrev = array();
                $netcos = array();
                $netmargin = array();
                foreach ($months as $key => $month) {
                    $firstday = date('Y-m-01 00:00:00', strtotime($month['date']));
                    $lastday = date('Y-m-t 23:59:59', strtotime($month['date']));

                    $mshopsales = ServiceSaleItem::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->where('serv_category_id', $scat->id)->get([
                            \DB::raw('SUM(total) as total'),         
                            \DB::raw('SUM(total_discount) as total_discount'),
                            \DB::raw('SUM(tax_amount) as tax_amount')
                        ]);

                    $netsales_amount = 0;
                    foreach ($mshopsales as $key => $sale) {
                        $tnetsales = ($sale->total-$sale->total_discount)+$sale->tax_amount;
                        $netsales_amount += $tnetsales;
                    }

                    array_push($netmrev, ['month' => date('m-Y',strtotime($month['date'])), 'amount' => $netsales_amount]);


                    $mshopcos = 0;

                    array_push($netcos, ['month' => date('m-Y',strtotime($month['date'])), 'cos' => $mshopcos]);

                    $mshopmargin = $netsales_amount-$mshopcos;
                    array_push($netmargin, ['month' => date('m-Y',strtotime($month['date'])), 'margin' => $mshopmargin]);

                }

                array_push($msales, ['name' => $scat->name, 'shopsales' => $netmrev]);
                array_push($mcostofsales, ['name' => $scat->name, 'shopcosales' => $netcos]);
                array_push($mmargins, ['name' => $scat->name, 'shopmargins' => $netmargin]);
            }

            // Other Services
            $ucats = ServiceSaleItem::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->whereNull('serv_category_id')->count();
            if ($ucats > 0) {
                $us_netmrev = array();
                $us_netcos = array();
                $us_netmargin = array();
                foreach ($months as $key => $month) {
                    $firstday = date('Y-m-01 00:00:00', strtotime($month['date']));
                    $lastday = date('Y-m-t 23:59:59', strtotime($month['date']));

                    $mshopsales = ServiceSaleItem::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->whereNull('serv_category_id')->get([
                            \DB::raw('SUM(total) as total'),         
                            \DB::raw('SUM(total_discount) as total_discount'),
                            \DB::raw('SUM(tax_amount) as tax_amount')
                        ]);
                    $netsales_amount = 0;
                    foreach ($mshopsales as $key => $sale) {
                        $tnetsales = ($sale->total-$sale->total_discount)+$sale->tax_amount;
                        $netsales_amount += $tnetsales;
                    }

                    array_push($us_netmrev, ['month' => date('m-Y',strtotime($month['date'])), 'amount' => $netsales_amount]);

                    $mshopcos = 0;

                    $mshopmargin = $netsales_amount-$mshopcos;
                    array_push($us_netmargin, ['month' => date('m-Y',strtotime($month['date'])), 'margin' => $mshopmargin]);
                }

                array_push($msales, ['name' => 'Other Services', 'shopsales' => $us_netmrev]);
                array_push($mmargins, ['name' => 'Other Services', 'shopmargins' => $us_netmargin]);
            }
            
            // Expense Cost of Sales
            $shopexpcsales = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('is_cost_of_sale', true)->groupBy('expense_type')->select('expense_type')->get();
            foreach ($shopexpcsales as $key => $expense) {
                // Log::info('Prccessing costs sales for '.$expense->expense_type);
                $netexpcos = array();
                foreach ($months as $key => $month) {
                    $firstday = date('Y-m-01 00:00:00', strtotime($month['date']));
                    $lastday = date('Y-m-t 23:59:59', strtotime($month['date']));
                    $mshopexp_cos = Expense::where('shop_id', $shop->id)->whereBetween('time_created', [$firstday, $lastday])->where('is_deleted', false)->where('is_cost_of_sale', true)->where('expense_type', $expense->expense_type)->sum('amount');
                    array_push($netexpcos, ['month' => date('m-Y',strtotime($month['date'])), 'cos' => $mshopexp_cos]);
                }
                array_push($mcostofsales, ['name' => $expense->expense_type, 'shopcosales' => $netexpcos]);
            }
        }


        $msalesresult = array(); 
        foreach ($msales as $key => $value) {
            if (isset($msalesresult[$value['name']])) {
                $msalesresult[$value['name']]['shopsales'] = array_merge($msalesresult[$value['name']]['shopsales'], $value['shopsales']);
            }else{
                $msalesresult[$value['name']]['name'] = $value['name'];
                $msalesresult[$value['name']]['shopsales'] = $value['shopsales'];
            }
        }

        $sales = array();
        foreach ($msalesresult as $key => $value) {
            $mshopsales = [];
            foreach ($value['shopsales'] as $shkey => $cos) {
                if (isset($mshopsales[$cos['month']])) {
                    $mshopsales[$cos['month']]['amount'] += $cos['amount'];
                }else{
                    $mshopsales[$cos['month']]['month'] = $cos['month'];
                    $mshopsales[$cos['month']]['amount'] = $cos['amount'];
                }
            }
            $rsales = array();
            foreach ($mshopsales as $key => $mcos) {
                array_push($rsales, $mcos);
            }

            array_push($sales, ['name' => $value['name'], 'shopsales' => $rsales]);
        }
        //End Sales sort
        $mcosresult = array(); 
        foreach ($mcostofsales as $key => $value) {
            if (isset($mcosresult[$value['name']])) {
                $mcosresult[$value['name']]['shopcosales'] = array_merge($mcosresult[$value['name']]['shopcosales'], $value['shopcosales']);
            }else{
                $mcosresult[$value['name']]['name'] = $value['name'];
                $mcosresult[$value['name']]['shopcosales'] = $value['shopcosales'];
            }
        }

        $costofsales = array();
        foreach ($mcosresult as $key => $value) {
            $mshopcosales = [];
            foreach ($value['shopcosales'] as $shkey => $cos) {
                if (isset($mshopcosales[$cos['month']])) {
                    $mshopcosales[$cos['month']]['cos'] += $cos['cos'];
                }else{
                    $mshopcosales[$cos['month']]['month'] = $cos['month'];
                    $mshopcosales[$cos['month']]['cos'] = $cos['cos'];
                }
            }
            $rcosales = array();
            foreach ($mshopcosales as $key => $mcos) {
                array_push($rcosales, $mcos);
            }

            array_push($costofsales, ['name' => $value['name'], 'shopcosales' => $rcosales]);
        }
        //End cost of sales sort
        // Log::info($costofsales);


        $mmarginsresult = array(); 
        foreach ($mmargins as $key => $value) {
            if (isset($mmarginsresult[$value['name']])) {
                $mmarginsresult[$value['name']]['shopmargins'] = array_merge($mmarginsresult[$value['name']]['shopmargins'], $value['shopmargins']);
            }else{
                $mmarginsresult[$value['name']]['name'] = $value['name'];
                $mmarginsresult[$value['name']]['shopmargins'] = $value['shopmargins'];
            }
        }

        $margins = array();
        foreach ($mmarginsresult as $key => $value) {
            $mshopmargins = [];
            foreach ($value['shopmargins'] as $shkey => $cos) {
                if (isset($mshopmargins[$cos['month']])) {
                    $mshopmargins[$cos['month']]['margin'] += $cos['margin'];
                }else{
                    $mshopmargins[$cos['month']]['month'] = $cos['month'];
                    $mshopmargins[$cos['month']]['margin'] = $cos['margin'];
                }
            }
            $rmargins = array();
            foreach ($mshopmargins as $key => $mcos) {
                array_push($rmargins, $mcos);
            }

            array_push($margins, ['name' => $value['name'], 'shopmargins' => $rmargins]);
        }
        //End Margins sort

        // $catsales = [];
        // foreach ($sales as $key => $value) {
        //     if (isset($catsales[$value['name']])) {
        //         $catsales[$value['name']]['netcatsales'] += $value['shopsales'];
        //     }else{
        //         $catsales[$value['name']]['name'] = $value['name'];
        //         $catsales[$value['name']]['netcatsales'] = $value['shopsales'];
        //     }
        // }

        // $catcostofsales = [];
        // foreach ($costofsales as $key => $value) {
        //     if (isset($catcostofsales[$value['name']])) {
        //         $catcostofsales[$value['name']]['netcatcosales'] += $value['shopcosales'];
        //     }else{
        //         $catcostofsales[$value['name']]['name'] = $value['name'];
        //         $catcostofsales[$value['name']]['netcatcosales'] = $value['shopcosales'];
        //     }
        // }
        // $catmargins = [];
        // foreach ($margins as $key => $value) {
        //     if (isset($catmargins[$value['name']])) {
        //         $catmargins[$value['name']]['netcatmargins'] += $value['shopmargins'];
        //     }else{
        //         $catmargins[$value['name']]['name'] = $value['name'];
        //         $catmargins[$value['name']]['netcatmargins'] = $value['shopmargins'];
        //     }
        // }

        // Expenses by Categories
        $expenses = [];
        if (!is_null($currshop)) {
            $categories = ExpenseCategory::where('expense_categories.shop_id', $currshop->id)->join('shops', 'shops.id', '=', 'expense_categories.shop_id')->where('company_id', $company->id)->select('expense_categories.name as name')->groupBy('name')->get();
            foreach ($categories as $key => $category) {
                $categexpenses = array();
                foreach ($months as $key => $month) {
                    $firstday = date('Y-m-01 00:00:00', strtotime($month['date']));
                    $lastday = date('Y-m-t 23:59:59', strtotime($month['date']));
                    $mexptotal_amt = 0;
                    foreach ($shops as $key => $shop) {
                        $mexptotal_amt += Expense::where('expenses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$firstday, $lastday])->where('is_cost_of_sale', false)->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->where('expense_categories.name', $category->name)->sum('amount');
                    }

                    array_push($categexpenses, ['amount' => $mexptotal_amt]);
                }
                array_push($expenses, ['category' => $category->name, 'catexpenses' => $categexpenses]);
            }
        }else{
            $categories = ExpenseCategory::join('shops', 'shops.id', '=', 'expense_categories.shop_id')->where('company_id', $company->id)->select('expense_categories.name as name')->groupBy('name')->get();
            foreach ($categories as $key => $category) {
                $categexpenses = array();
                foreach ($months as $key => $month) {
                    $firstday = date('Y-m-01 00:00:00', strtotime($month['date']));
                    $lastday = date('Y-m-t 23:59:59', strtotime($month['date']));
                    $mexptotal_amt = 0;
                    foreach ($shops as $key => $shop) {
                        $mexptotal_amt += Expense::where('expenses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$firstday, $lastday])->where('is_cost_of_sale', false)->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->where('expense_categories.name', $category->name)->sum('amount');
                    }

                    array_push($categexpenses, ['amount' => $mexptotal_amt]);
                }
                array_push($expenses, ['category' => $category->name, 'catexpenses' => $categexpenses]);
            }
        }
        // Log::info($sales);

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.company.management-report', compact('page', 'title', 'years', 'company', 'cshops', 'currshop', 'reporttime', 'is_post_query', 'start_date', 'end_date', 'duration', 'duration_sw', 'months', 'sales', 'msaletotals', 'costofsales', 'mcostotals', 'margins', 'margintotals', 'expenses', 'expensetotals', 'netincometotals'));
    }

    public function incomeStmt(Request $request)
    {
        $page = 'Income Statement';
        $title = 'Income Statement';
        $years = array();
        $dbDate = Carbon::parse('2024-01-10');
        $diffYears = Carbon::now()->diffInYears($dbDate);
        for ($i = $diffYears; $i >= 0; $i--) {
            $year = Carbon::today()->subYears($i)->format('Y');
            array_push($years, array(
                'year' => $year
            ));
        }

        $curyear = Carbon::today()->format('Y');
        if (!empty($request['year'])) {
            $curyear = $request['year'];
        }

        $now = Carbon::now();
        $start = $now->startOfYear();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['year'])){
            $date = Carbon::createFromFormat('d F Y', '05 January '.$request['year']);
            $start = $date->startOfMonth()->format('Y-m-d');
            $start_date = $start;
            $date1 = Carbon::createFromFormat('d F Y', '05 December '.$request['year']);
            $end = $date1->endOfMonth()->format('Y-m-d');
            $end_date = $end;
            $is_post_query = true;
        }elseif (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        //Balance Before
        $period = CarbonPeriod::create($start_date, $end_date)->month();

        $months = collect($period)->map(function (Carbon $date) {
            return [
                'name' => $date->monthName,
                'year' => $date->year,
                'date' => $date
            ];
        });

        $currshop = null;
        $company = Company::find(Session::get('company_id'));
        $shops = $company->shops()->select('id', 'name')->get();
        $cshops = $shops;
        if (!empty($request['shop_id'])) {
            $currshop = Shop::find($request['shop_id']);
            $shops = $company->shops()->where('id', $currshop->id)->select('id', 'name')->get();
        }

        $msaletotals = array();
        $mcostotals = array();
        $margintotals = array();
        $expensetotals = array();
        $netincometotals = array();
        foreach ($months as $key => $month) {
            $firstday = date('Y-m-01 00:00:00', strtotime($month['date']));
            $lastday = date('Y-m-t 23:59:59', strtotime($month['date']));
            $mtotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $mshopsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->select(
                    \DB::raw('SUM(sale_amount) as sale_amount'),
                    \DB::raw('SUM(sale_discount) as sale_discount'),
                    \DB::raw('SUM(tax_amount) as tax_amount'),
                    \DB::raw('SUM(return_amount) as return_amount'),
                    \DB::raw('SUM(return_discount) as return_discount'),
                    \DB::raw('SUM(return_tax) as return_tax'))->get();
                $netsales_amount = 0;
                foreach ($mshopsales as $key => $sale) {
                    $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                    $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
                    $netsales_amount += $tnetsales-$tnetreturn;
                }
                $mtotal_amt += $netsales_amount;
            }
            array_push($msaletotals, ['total_rev' => $mtotal_amt]);

            $mcso_amt = 0;
            foreach ($shops as $key => $shop) {
                $mcso_amt += AnSaleItem::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->sum('buying_price');
            }

            $mexp_cos = 0;
            foreach ($shops as $key => $shop) {
                $mexp_cos += Expense::where('shop_id', $shop->id)->whereBetween('time_created', [$firstday, $lastday])->where('is_deleted', false)->where('is_cost_of_sale', true)->sum('amount');
            }

            array_push($mcostotals, ['total_cos' => $mcso_amt+$mexp_cos]);

            $total_mag = $mtotal_amt-($mcso_amt+$mexp_cos);
            array_push($margintotals, ['total_mag' => $total_mag]);

            $total_exp = 0;
            foreach ($shops as $key => $shop) {
                $total_exp += Expense::where('shop_id', $shop->id)->whereBetween('time_created', [$firstday, $lastday])->where('is_deleted', false)->where('is_cost_of_sale', false)->sum('amount');
            }

            array_push($expensetotals, ['total_exp' => $total_exp]);

            $total_net = $total_mag-$total_exp;
            array_push($netincometotals, ['total_net' => $total_net]);
        }

        // Log::info($msaletotals);


        $sales = [];
        $costofsales = [];
        $margins = [];
        foreach ($shops as $key => $shop) {
            $netmrev = array();
            $netcos = array();
            $netmargin = array();
            foreach ($months as $key => $month) {
                $firstday = date('Y-m-01 00:00:00', strtotime($month['date']));
                $lastday = date('Y-m-t 23:59:59', strtotime($month['date']));

                $mshopsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->select(
                    \DB::raw('SUM(sale_amount) as sale_amount'),
                    \DB::raw('SUM(sale_discount) as sale_discount'),
                    \DB::raw('SUM(tax_amount) as tax_amount'),
                    \DB::raw('SUM(return_amount) as return_amount'),
                    \DB::raw('SUM(return_discount) as return_discount'),
                    \DB::raw('SUM(return_tax) as return_tax'))->get();
                $netsales_amount = 0;
                foreach ($mshopsales as $key => $sale) {
                    $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                    $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
                    $netsales_amount += $tnetsales-$tnetreturn;
                }

                array_push($netmrev, ['amount' => $netsales_amount]);


                $mshopcos = AnSaleItem::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$firstday, $lastday])->sum('buying_price');

                $mshopexp_cos = Expense::where('shop_id', $shop->id)->whereBetween('time_created', [$firstday, $lastday])->where('is_deleted', false)->where('is_cost_of_sale', true)->sum('amount');
                array_push($netcos, ['cos' => $mshopcos+$mshopexp_cos]);

                $mshopmargin = $netsales_amount-($mshopcos+$mshopexp_cos);
                array_push($netmargin, ['margin' => $mshopmargin]);
            }

            // Log::info($netmrev);
            array_push($sales, ['name' => $shop->name, 'shopsales' => $netmrev]);
            array_push($costofsales, ['name' => $shop->name, 'shopcosales' => $netcos]);
            array_push($margins, ['name' => $shop->name, 'shopmargins' => $netmargin]);
        }

        // Expenses by Categories
        $expenses = [];
        if (!is_null($currshop)) {
            $categories = ExpenseCategory::where('expense_categories.shop_id', $currshop->id)->join('shops', 'shops.id', '=', 'expense_categories.shop_id')->where('company_id', $company->id)->select('expense_categories.name as name')->groupBy('name')->get();
            foreach ($categories as $key => $category) {
                $categexpenses = array();
                foreach ($months as $key => $month) {
                    $firstday = date('Y-m-01 00:00:00', strtotime($month['date']));
                    $lastday = date('Y-m-t 23:59:59', strtotime($month['date']));
                    $mexptotal_amt = 0;
                    foreach ($shops as $key => $shop) {
                        $mexptotal_amt += Expense::where('expenses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$firstday, $lastday])->where('is_cost_of_sale', false)->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->where('expense_categories.name', $category->name)->sum('amount');
                    }

                    array_push($categexpenses, ['amount' => $mexptotal_amt]);
                }
                array_push($expenses, ['category' => $category->name, 'catexpenses' => $categexpenses]);
            }
        }else{
            $categories = ExpenseCategory::join('shops', 'shops.id', '=', 'expense_categories.shop_id')->where('company_id', $company->id)->select('expense_categories.name as name')->groupBy('name')->get();
            foreach ($categories as $key => $category) {
                $categexpenses = array();
                foreach ($months as $key => $month) {
                    $firstday = date('Y-m-01 00:00:00', strtotime($month['date']));
                    $lastday = date('Y-m-t 23:59:59', strtotime($month['date']));
                    $mexptotal_amt = 0;
                    foreach ($shops as $key => $shop) {
                        $mexptotal_amt += Expense::where('expenses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$firstday, $lastday])->where('is_cost_of_sale', false)->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->where('expense_categories.name', $category->name)->sum('amount');
                    }

                    array_push($categexpenses, ['amount' => $mexptotal_amt]);
                }
                array_push($expenses, ['category' => $category->name, 'catexpenses' => $categexpenses]);
            }
        }
        // Log::info($sales);

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.company.income-stmt', compact('page', 'title', 'years', 'company', 'cshops', 'currshop', 'reporttime', 'is_post_query', 'start_date', 'end_date', 'duration', 'duration_sw', 'months', 'sales', 'msaletotals', 'costofsales', 'mcostotals', 'margins', 'margintotals', 'expenses', 'expensetotals', 'netincometotals'));
    }

    public function cfStmt(Request $request)
    {
        $page = 'Cash Flow Statement';
        $title = 'Cash Flow Statement';
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
        $endingdate = Carbon::parse($start)->subDays(1);

        //Balance Before
        $totalIn = 0;
        $totalOut = 0;
        $cash_balance = 0;

        //In Range Operations

        $other_payments = 0;
        $invoice_payments = 0;
        $purchase_payments = 0;
        $rm_purchase_payments = 0;
        $pm_purchase_payments = 0;
        $dlc_payments = 0;
        $moh_cost_payments = 0;
        $expcategories = [];
        $gropedcats = [];
        $income_tax = 0;
        $inv_cashins = [];
        $fin_cashins = [];
        $inv_cashouts = [];
        $fin_cashouts = [];

        $currshop = null;
        $company = Company::find(Session::get('company_id'));
        $shops = $company->shops()->select('id', 'name')->get();
        $cshops = $shops;
        if (!empty($request['shop_id'])) {
            $currshop = Shop::find($request['shop_id']);
            $shops = $company->shops()->where('id', $currshop->id)->select('id', 'name')->get();
        }

        foreach ($shops as $key => $shop) {
            $tcashins = CashIn::where('shop_id', $shop->id)->where('in_date' , '<', $start)->sum('amount');
            $tpayments = SalePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');

            $tcashouts = CashOut::where('shop_id', $shop->id)->where('out_date', '<', $start)->sum('amount');
            $texpense = ExpensePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');

            $tppayments = PurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');

            $trm_payments = RmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');
            $tpm_payments = PmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');
            $tdlc_payments = PlcPayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');
            $tmoh_cost_payments = MohCostPayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');
            $totalIn += ($tcashins+$tpayments);
            $totalOut +=($tcashouts+$texpense+$tppayments+$trm_payments+$tpm_payments+$tdlc_payments+$tmoh_cost_payments);

            //Current Range Operations
            $invoice_payments += SalePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('is_deleted', false)->sum('amount');

            $purchase_payments += PurchasePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('is_deleted', false)->sum('amount');

            $rm_purchase_payments += RmPurchasePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('is_deleted', false)->sum('amount');
            $pm_purchase_payments += PmPurchasePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('is_deleted', false)->sum('amount');
            $dlc_payments += PlcPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('is_deleted', false)->sum('amount');
            $moh_cost_payments += MohCostPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('is_deleted', false)->sum('amount');

            $shopexpcategories = ExpensePayment::where('expense_payments.shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('expense_payments.is_deleted', false)->join('expenses', 'expenses.id', '=', 'expense_payments.expense_id')->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->groupBy('name')->get([
                \DB::raw('name as category'),
                \DB::raw('SUM(expense_payments.amount) as amount')
            ]);

            foreach ($shopexpcategories as $key => $value) {
                if (isset($gropedcats[$value->category])) {
                    $gropedcats[$value->category]['amount'] += $value->amount;
                }else{
                    $gropedcats[$value->category]['category'] = $value->category;
                    $gropedcats[$value->category]['amount'] = $value->amount;
                }
            }

            $income_tax = 0;

            $shopinv_cashins = CashIn::where('shop_id', $shop->id)->where('category', 'Investing Activities')->whereBetween('in_date', [$start, $end])->select('source', 'amount')->get();
            foreach ($shopinv_cashins as $key => $value) {
                array_push($inv_cashins, ['source' => $value->source, 'amount' => $value->amount]);
            }

            $shopfin_cashins = CashIn::where('shop_id', $shop->id)->where('category', 'Financing Activities')->whereBetween('in_date', [$start, $end])->get();
            foreach ($shopfin_cashins as $key => $value) {
                array_push($fin_cashins, ['source' => $value->source, 'amount' => $value->amount]);
            }

            $shopinv_cashouts = CashOut::where('shop_id', $shop->id)->where('category', 'Investing Activities')->whereBetween('out_date', [$start, $end])->get();
            foreach ($shopinv_cashouts as $key => $value) {
                array_push($inv_cashouts, ['reason' => $value->reason, 'amount' => $value->amount]);
            }

            $shopfin_cashouts = CashOut::where('shop_id', $shop->id)->where('category', 'Financing Activities')->whereBetween('out_date', [$start, $end])->get();;
            foreach ($shopfin_cashouts as $key => $value) {
                array_push($fin_cashouts, ['reason' => $value->reason, 'amount' => $value->amount]);
            }
        }

        //Previous Balance
        $cash_balance = $totalIn-$totalOut;

        foreach ($gropedcats as $key => $value) {
            array_push($expcategories, $value);
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.company.cf-stmt', compact('page', 'title', 'company', 'cshops', 'currshop', 'reporttime', 'is_post_query', 'start_date', 'end_date', 'endingdate', 'cash_balance', 'invoice_payments', 'other_payments', 'purchase_payments', 'rm_purchase_payments', 'pm_purchase_payments', 'dlc_payments', 'moh_cost_payments', 'expcategories', 'income_tax', 'inv_cashins', 'inv_cashouts', 'fin_cashins', 'fin_cashouts'));
    }


    public function balanceSheet(Request $request)    
    {
        $page = 'Balance Sheet';
        $title = 'Balance Sheet';
        $years = array();
        $dbDate = Carbon::parse('2025-01-10');
        $diffYears = Carbon::now()->diffInYears($dbDate);
        for ($i = $diffYears; $i >= 0; $i--) {
            $year = Carbon::today()->subYears($i)->format('Y');
            array_push($years, array(
                'year' => $year
            ));
        }
        $is_post_query = false;
        $endyear = Carbon::today()->format('Y');
        if (!empty($request['year'])) {
            $endyear = $request['year'];
            if ($request['year'] <= $request['start_year']) {
                $endyear = $request['start_year'];
            }
            $is_post_query = true;
        }

        $startyear = (int)$endyear-1;
        if (!empty($request['start_year'])) {
            $startyear = $request['start_year'];
            if ($request['start_year'] >= $endyear) {
                $startyear = $endyear;
            }
            $is_post_query = true;
        }
        if ($startyear == 2024) {
            $startyear = 2025;
        }
        $no_years = ($endyear-$startyear)+1;
        $curryears = array();
        for ($i=0; $i < $no_years; $i++) { 
            array_push($curryears, ['name' => $startyear+$i]);
        }

        $start_date = $startyear;
        $end_date = $endyear;
        $duration = $startyear.' - '.$endyear.'.';
        $duration_sw = $startyear.' - '.$endyear.'.';

        $currshop = null;
        $company = Company::find(Session::get('company_id'));
        $shops = $company->shops()->select('id', 'name')->get();
        $cshops = $shops;
        $defcurr = ShopCurrency::where('shop_id', Session::get('shop_id'))->where('is_default', true)->first();
        $settings = Setting::where('shop_id', Session::get('shop_id'))->first();
        if (!empty($request['shop_id'])) {
            $currshop = Shop::find($request['shop_id']);
            $shops = $company->shops()->where('id', $currshop->id)->select('id', 'name')->get();
            $defcurr = ShopCurrency::where('shop_id', $currshop->id)->where('is_default', true)->first();
            $settings = Setting::where('shop_id', $currshop->id)->first();
        }

        $mcurrent_assets = [];
        $mfixed_assets = [];
        $mother_assets = [];
        $mcurrent_liabilities = [];
        $mlong_term_liabilities = [];
        $mowners_equity = [];
        foreach ($shops as $key => $shop) {
            $current_assets = BasicBalanceSheet::where('shop_id', $shop->id)->where('item_category', 'CURRENT ASSETS')->select('item_desc')->get();
            foreach ($current_assets as $key => $scat) {
                // Log::info('Prccessing sales for '.$scat->name);
                $monthvalues = array();
                foreach ($curryears as $key => $year) {
                    $lastday = date('Y-m-t', strtotime($year['name'].'-12-12'));
                    
                    $itemvalue = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_desc', $scat->item_desc)->where('item_category', 'CURRENT ASSETS')->first();
                    if (!is_null($itemvalue)) {
                        array_push($monthvalues, ['month' => $lastday, 'amount' => $itemvalue->amount]);
                    }else{
                        array_push($monthvalues, ['month' => $lastday, 'amount' => 0]);
                    }
                }

                array_push($mcurrent_assets, ['name' => $scat->item_desc, 'curryearvalues' => $monthvalues]);
            }
            
            $fixed_assets = BasicBalanceSheet::where('shop_id', $shop->id)->where('item_category', 'FIXED (LONG TERM) ASSETS')->get();
            foreach ($fixed_assets as $key => $fcat) {
                // Log::info('Prccessing sales for '.$scat->name);
                $monthfavalues = array();
                foreach ($curryears as $key => $year) {
                    $lastday = date('Y-m-t', strtotime($year['name'].'-12-12'));
                    
                    $itemvalue = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_desc', $fcat->item_desc)->where('item_category', 'FIXED (LONG TERM) ASSETS')->first();
                    if (!is_null($itemvalue)) {
                        array_push($monthfavalues, ['month' => $lastday, 'amount' => $itemvalue->amount]);
                    }else{
                        array_push($monthfavalues, ['month' => $lastday, 'amount' => 0]);
                    }
                }

                array_push($mfixed_assets, ['name' => $fcat->item_desc, 'curryearfavalues' => $monthfavalues]);
            }

            $other_assets = BasicBalanceSheet::where('shop_id', $shop->id)->where('item_category', 'OTHER ASSETS')->get();
            foreach ($other_assets as $key => $ocat) {
                // Log::info('Prccessing sales for '.$ocat->item_desc);
                $monthoavalues = array();
                foreach ($curryears as $key => $year) {
                    $lastday = date('Y-m-t', strtotime($year['name'].'-12-12'));
                    
                    $itemvalue = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_desc', $ocat->item_desc)->where('item_category', 'OTHER ASSETS')->first();
                    if (!is_null($itemvalue)) {
                        array_push($monthoavalues, ['month' => $lastday, 'amount' => $itemvalue->amount]);
                    }else{
                        array_push($monthoavalues, ['month' => $lastday, 'amount' => 0]);
                    }
                }

                array_push($mother_assets, ['name' => $ocat->item_desc, 'curryearoavalues' => $monthoavalues]);
            }

            $current_liabilities = BasicBalanceSheet::where('shop_id', $shop->id)->where('item_category', 'CURRENT LIABILITIES')->get();
            foreach ($current_liabilities as $key => $lcat) {
                // Log::info('Prccessing sales for '.$scat->name);
                $monthclvalues = array();
                foreach ($curryears as $key => $year) {
                    $lastday = date('Y-m-t', strtotime($year['name'].'-12-12'));
                    
                    $itemvalue = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_desc', $lcat->item_desc)->where('item_category', 'CURRENT LIABILITIES')->first();
                    if (!is_null($itemvalue)) {
                        array_push($monthclvalues, ['month' => $lastday, 'amount' => $itemvalue->amount]);
                    }else{
                        array_push($monthclvalues, ['month' => $lastday, 'amount' => 0]);
                    }
                }

                array_push($mcurrent_liabilities, ['name' => $lcat->item_desc, 'curryearclvalues' => $monthclvalues]);
            }

            $long_term_liabilities = BasicBalanceSheet::where('shop_id', $shop->id)->where('item_category', 'LONG TERM LIABILITIES')->get();
            foreach ($long_term_liabilities as $key => $ltcat) {
                // Log::info('Prccessing sales for '.$ltcat->item_desc);
                $monthltlvalues = array();
                foreach ($curryears as $key => $year) {
                    $lastday = date('Y-m-t', strtotime($year['name'].'-12-12'));
                    
                    $itemvalue = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_desc', $ltcat->item_desc)->where('item_category', 'LONG TERM LIABILITIES')->first();
                    if (!is_null($itemvalue)) {
                        array_push($monthltlvalues, ['month' => $lastday, 'amount' => $itemvalue->amount]);
                    }else{
                        array_push($monthltlvalues, ['month' => $lastday, 'amount' => 0]);
                    }
                }

                array_push($mlong_term_liabilities, ['name' => $ltcat->item_desc, 'curryearltlvalues' => $monthltlvalues]);
            }

            $owners_equity = BasicBalanceSheet::where('shop_id', $shop->id)->where('item_category', "OWNER'S EQUITY")->get();
            foreach ($owners_equity as $key => $oecat) {
                // Log::info('Prccessing sales for '.$scat->name);
                $monthoevalues = array();
                foreach ($curryears as $key => $year) {
                    $lastday = date('Y-m-t', strtotime($year['name'].'-12-12'));
                    
                    $itemvalue = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_desc', $oecat->item_desc)->where('item_category', "OWNER'S EQUITY")->first();
                    if (!is_null($itemvalue)) {
                        array_push($monthoevalues, ['month' => $lastday, 'amount' => $itemvalue->amount]);
                    }else{
                        array_push($monthoevalues, ['month' => $lastday, 'amount' => 0]);
                    }
                }

                array_push($mowners_equity, ['name' => $oecat->item_desc, 'curryearoevalues' => $monthoevalues]);
            }
        }

        $mcatotals = array();
        $mfatotals = array();
        $moatotals = array();
        $m_a_totals = array();
        $mcltotals = array();
        $mltltotals = array();
        $moetotals = array();
        $m_lao_totals = array();

        $mdebtratios = array();
        $mcurrentratios = array();
        $mworking_capitals = array();
        $massets_to_equity_ratios = array();
        $mdebt_to_equity_ratios = array();
        foreach ($curryears as $key => $year) {
            $lastday = date('Y-m-t', strtotime($year['name'].'-12-12'));
            $mtotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $totalcurrent_assets = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_category', 'CURRENT ASSETS')->sum('amount');
                $mtotal_amt += $totalcurrent_assets;
            }
            array_push($mcatotals, ['total_ca' => $mtotal_amt]);

            $mfatotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $totalfixed_assets = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_category', 'FIXED (LONG TERM) ASSETS')->sum('amount');
                $mfatotal_amt += $totalfixed_assets;
            }
            array_push($mfatotals, ['total_fa' => $mfatotal_amt]);

            $moatotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $totalother_assets = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_category', 'OTHER ASSETS')->sum('amount');
                $moatotal_amt += $totalother_assets;
            }
            array_push($moatotals, ['total_oa' => $moatotal_amt]);

            $total_assets = $mtotal_amt+$mfatotal_amt+$moatotal_amt;
            array_push($m_a_totals, ['total_a' => $total_assets]);

            $mcltotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $totalcurrent_liabilities = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_category', 'CURRENT LIABILITIES')->sum('amount');
                $mcltotal_amt += $totalcurrent_liabilities;
            }
            array_push($mcltotals, ['total_cl' => $mcltotal_amt]);

            $mltltotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $total_lt_liabilities = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_category', 'LONG TERM LIABILITIES')->sum('amount');
                $mltltotal_amt += $total_lt_liabilities;
            }
            array_push($mltltotals, ['total_ltl' => $mltltotal_amt]);

            $moetotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $totalowners_equity = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_category', "OWNER'S EQUITY")->sum('amount');
                $moetotal_amt += $totalowners_equity;
            }
            array_push($moetotals, ['total_oe' => $moetotal_amt]);

            $total_lao = $mcltotal_amt+$mltltotal_amt+$moetotal_amt;
            array_push($m_lao_totals, ['total_lao' => $total_lao]);


            $total_liabilities = $mcltotal_amt+$mltltotal_amt;

            // Log::info($lastday);
            // Log::info('Total Assets '.$total_assets);
            // Log::info('Total Liabilities '.$total_liabilities);
            $debt_ratio = 0;
            if ($total_assets > 0) {
                $debt_ratio = round($total_liabilities/$total_assets, 2);
            }

            array_push($mdebtratios, ['debt_ratio' => $debt_ratio]);

            $current_ratio = 0;
            if ($mcltotal_amt > 0) {
                $current_ratio = round($mtotal_amt/$mcltotal_amt, 2);
            }
            array_push($mcurrentratios, ['current_ratio' => $current_ratio]);

            $working_capital = $mtotal_amt-$mcltotal_amt;
            array_push($mworking_capitals, ['working_capital' => $working_capital]);

            $assets_to_equity_ratio = 0;
            if ($moetotal_amt > 0) {
                $assets_to_equity_ratio = round($total_assets/$moetotal_amt, 2);
            }

            array_push($massets_to_equity_ratios, ['assets_to_equity_ratio' => $assets_to_equity_ratio]);

            $debt_to_equity_ratio = 0;
            if ($moetotal_amt > 0) {
                $debt_to_equity_ratio = round($total_liabilities/$moetotal_amt, 2);
            }

            array_push($mdebt_to_equity_ratios, ['debt_to_equity_ratio' => $debt_to_equity_ratio]);

        }
        if (is_null($defcurr)) {
            return redirect('settings')->with('warning', 'Please set your Default Currency to continue');
        }

        $currency = $defcurr->code;
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.company.balance-sheet', compact('page', 'title', 'company', 'cshops', 'currshop', 'currency', 'settings', 'reporttime', 'is_post_query', 'start_date', 'end_date', 'duration', 'duration_sw', 'years', 'startyear', 'endyear', 'curryears', 'mcurrent_assets', 'mcatotals', 'mfixed_assets', 'mfatotals', 'mother_assets', 'moatotals', 'm_a_totals', 'mcurrent_liabilities', 'mcltotals', 'mlong_term_liabilities', 'mltltotals', 'mowners_equity', 'moetotals', 'm_lao_totals', 'mdebtratios', 'mcurrentratios', 'mworking_capitals', 'massets_to_equity_ratios', 'mdebt_to_equity_ratios'));
    }


    public function monthlyBalanceSheet(Request $request)
    {
        $page = 'Monthly Balance Sheet';
        $title = 'Monthly Balance Sheet';
        $years = array();
        $dbDate = Carbon::parse('2025-01-10');
        $diffYears = Carbon::now()->diffInYears($dbDate);
        for ($i = $diffYears; $i >= 0; $i--) {
            $year = Carbon::today()->subYears($i)->format('Y');
            array_push($years, array(
                'year' => $year
            ));
        }

        $curyear = Carbon::today()->format('Y');
        if (!empty($request['year'])) {
            $curyear = $request['year'];
        }

        $now = Carbon::now();
        $start = $now->startOfYear();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['year'])){
            $date = Carbon::createFromFormat('d F Y', '05 January '.$request['year']);
            $start = $date->startOfMonth()->format('Y-m-d');
            $start_date = $start;
            $date1 = Carbon::createFromFormat('d F Y', '05 December '.$request['year']);
            $end = $date1->endOfMonth()->format('Y-m-d');
            $end_date = $end;
            $is_post_query = true;
        }elseif (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        
        $duration = 'From '.date('d M Y', strtotime($start)).' To '.date('d M Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d M Y', strtotime($start)).' Mpaka '.date('d M Y', strtotime($end)).'.';

        $period = CarbonPeriod::create($start_date, $end_date)->month();

        $months = collect($period)->map(function (Carbon $date) {
            return [
                'name' => date('M', strtotime($date)),
                'year' => $date->year,
                'date' => $date
            ];
        });

        $currshop = null;
        $company = Company::find(Session::get('company_id'));
        $shops = $company->shops()->select('id', 'name')->get();
        $cshops = $shops;
        $defcurr = ShopCurrency::where('shop_id', Session::get('shop_id'))->where('is_default', true)->first();
        $settings = Setting::where('shop_id', Session::get('shop_id'))->first();
        if (!empty($request['shop_id'])) {
            $currshop = Shop::find($request['shop_id']);
            $shops = $company->shops()->where('id', $currshop->id)->select('id', 'name')->get();
            $defcurr = ShopCurrency::where('shop_id', $currshop->id)->where('is_default', true)->first();
            $settings = Setting::where('shop_id', $currshop->id)->first();
        }

        $mcurrent_assets = [];
        $mfixed_assets = [];
        $mother_assets = [];
        $mcurrent_liabilities = [];
        $mlong_term_liabilities = [];
        $mowners_equity = [];
        foreach ($shops as $key => $shop) {
            $current_assets = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('item_category', 'CURRENT ASSETS')->select('item_desc')->get();
            foreach ($current_assets as $key => $scat) {
                // Log::info('Prccessing sales for '.$scat->name);
                $monthvalues = array();
                foreach ($months as $key => $month) {
                    $lastday = date('Y-m-t', strtotime($month['date']));
                    
                    $itemvalue = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_desc', $scat->item_desc)->where('item_category', 'CURRENT ASSETS')->first();
                    if (!is_null($itemvalue)) {
                        array_push($monthvalues, ['month' => $lastday, 'amount' => $itemvalue->amount]);
                    }else{
                        array_push($monthvalues, ['month' => $lastday, 'amount' => 0]);
                    }
                }

                array_push($mcurrent_assets, ['name' => $scat->item_desc, 'monthvalues' => $monthvalues]);
            }
            
            $fixed_assets = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('item_category', 'FIXED (LONG TERM) ASSETS')->get();
            foreach ($fixed_assets as $key => $fcat) {
                // Log::info('Prccessing sales for '.$scat->name);
                $monthfavalues = array();
                foreach ($months as $key => $month) {
                    $lastday = date('Y-m-t', strtotime($month['date']));
                    
                    $itemvalue = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_desc', $fcat->item_desc)->where('item_category', 'FIXED (LONG TERM) ASSETS')->first();
                    if (!is_null($itemvalue)) {
                        array_push($monthfavalues, ['month' => $lastday, 'amount' => $itemvalue->amount]);
                    }else{
                        array_push($monthfavalues, ['month' => $lastday, 'amount' => 0]);
                    }
                }

                array_push($mfixed_assets, ['name' => $fcat->item_desc, 'monthfavalues' => $monthfavalues]);
            }

            $other_assets = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('item_category', 'OTHER ASSETS')->get();
            foreach ($other_assets as $key => $ocat) {
                // Log::info('Prccessing sales for '.$ocat->item_desc);
                $monthoavalues = array();
                foreach ($months as $key => $month) {
                    $lastday = date('Y-m-t', strtotime($month['date']));
                    
                    $itemvalue = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_desc', $ocat->item_desc)->where('item_category', 'OTHER ASSETS')->first();
                    if (!is_null($itemvalue)) {
                        array_push($monthoavalues, ['month' => $lastday, 'amount' => $itemvalue->amount]);
                    }else{
                        array_push($monthoavalues, ['month' => $lastday, 'amount' => 0]);
                    }
                }

                array_push($mother_assets, ['name' => $ocat->item_desc, 'monthoavalues' => $monthoavalues]);
            }

            $current_liabilities = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('item_category', 'CURRENT LIABILITIES')->get();
            foreach ($current_liabilities as $key => $lcat) {
                // Log::info('Prccessing sales for '.$scat->name);
                $monthclvalues = array();
                foreach ($months as $key => $month) {
                    $lastday = date('Y-m-t', strtotime($month['date']));
                    
                    $itemvalue = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_desc', $lcat->item_desc)->where('item_category', 'CURRENT LIABILITIES')->first();
                    if (!is_null($itemvalue)) {
                        array_push($monthclvalues, ['month' => $lastday, 'amount' => $itemvalue->amount]);
                    }else{
                        array_push($monthclvalues, ['month' => $lastday, 'amount' => 0]);
                    }
                }

                array_push($mcurrent_liabilities, ['name' => $lcat->item_desc, 'monthclvalues' => $monthclvalues]);
            }

            $long_term_liabilities = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('item_category', 'LONG TERM LIABILITIES')->get();
            foreach ($long_term_liabilities as $key => $ltcat) {
                // Log::info('Prccessing sales for '.$ltcat->item_desc);
                $monthltlvalues = array();
                foreach ($months as $key => $month) {
                    $lastday = date('Y-m-t', strtotime($month['date']));
                    
                    $itemvalue = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_desc', $ltcat->item_desc)->where('item_category', 'LONG TERM LIABILITIES')->first();
                    if (!is_null($itemvalue)) {
                        array_push($monthltlvalues, ['month' => $lastday, 'amount' => $itemvalue->amount]);
                    }else{
                        array_push($monthltlvalues, ['month' => $lastday, 'amount' => 0]);
                    }
                }

                array_push($mlong_term_liabilities, ['name' => $ltcat->item_desc, 'monthltlvalues' => $monthltlvalues]);
            }

            $owners_equity = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('item_category', "OWNER'S EQUITY")->get();
            foreach ($owners_equity as $key => $oecat) {
                // Log::info('Prccessing sales for '.$scat->name);
                $monthoevalues = array();
                foreach ($months as $key => $month) {
                    $lastday = date('Y-m-t', strtotime($month['date']));
                    
                    $itemvalue = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_desc', $oecat->item_desc)->where('item_category', "OWNER'S EQUITY")->first();
                    if (!is_null($itemvalue)) {
                        array_push($monthoevalues, ['month' => $lastday, 'amount' => $itemvalue->amount]);
                    }else{
                        array_push($monthoevalues, ['month' => $lastday, 'amount' => 0]);
                    }
                }

                array_push($mowners_equity, ['name' => $oecat->item_desc, 'monthoevalues' => $monthoevalues]);
            }
        }

        $mcatotals = array();
        $mfatotals = array();
        $moatotals = array();
        $m_a_totals = array();
        $mcltotals = array();
        $mltltotals = array();
        $moetotals = array();
        $m_lao_totals = array();

        $mdebtratios = array();
        $mcurrentratios = array();
        $mworking_capitals = array();
        $massets_to_equity_ratios = array();
        $mdebt_to_equity_ratios = array();
        foreach ($months as $key => $month) {
            $lastday = date('Y-m-t', strtotime($month['date']));
            $mtotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $totalcurrent_assets = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_category', 'CURRENT ASSETS')->sum('amount');
                $mtotal_amt += $totalcurrent_assets;
            }
            array_push($mcatotals, ['total_ca' => $mtotal_amt]);

            $mfatotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $totalfixed_assets = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_category', 'FIXED (LONG TERM) ASSETS')->sum('amount');
                $mfatotal_amt += $totalfixed_assets;
            }
            array_push($mfatotals, ['total_fa' => $mfatotal_amt]);

            $moatotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $totalother_assets = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_category', 'OTHER ASSETS')->sum('amount');
                $moatotal_amt += $totalother_assets;
            }
            array_push($moatotals, ['total_oa' => $moatotal_amt]);

            $total_assets = $mtotal_amt+$mfatotal_amt+$moatotal_amt;
            array_push($m_a_totals, ['total_a' => $total_assets]);

            $mcltotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $totalcurrent_liabilities = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_category', 'CURRENT LIABILITIES')->sum('amount');
                $mcltotal_amt += $totalcurrent_liabilities;
            }
            array_push($mcltotals, ['total_cl' => $mcltotal_amt]);

            $mltltotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $total_lt_liabilities = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_category', 'LONG TERM LIABILITIES')->sum('amount');
                $mltltotal_amt += $total_lt_liabilities;
            }
            array_push($mltltotals, ['total_ltl' => $mltltotal_amt]);

            $moetotal_amt = 0;
            foreach ($shops as $key => $shop) {
                $totalowners_equity = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $lastday)->where('item_category', "OWNER'S EQUITY")->sum('amount');
                $moetotal_amt += $totalowners_equity;
            }
            array_push($moetotals, ['total_oe' => $moetotal_amt]);

            $total_lao = $mcltotal_amt+$mltltotal_amt+$moetotal_amt;
            array_push($m_lao_totals, ['total_lao' => $total_lao]);


            $total_liabilities = $mcltotal_amt+$mltltotal_amt;

            // Log::info($lastday);
            // Log::info('Total Assets '.$total_assets);
            // Log::info('Total Liabilities '.$total_liabilities);
            $debt_ratio = 0;
            if ($total_assets > 0) {
                $debt_ratio = round($total_liabilities/$total_assets, 2);
            }

            array_push($mdebtratios, ['debt_ratio' => $debt_ratio]);

            $current_ratio = 0;
            if ($mcltotal_amt > 0) {
                $current_ratio = round($mtotal_amt/$mcltotal_amt, 2);
            }
            array_push($mcurrentratios, ['current_ratio' => $current_ratio]);

            $working_capital = $mtotal_amt-$mcltotal_amt;
            array_push($mworking_capitals, ['working_capital' => $working_capital]);

            $assets_to_equity_ratio = 0;
            if ($moetotal_amt > 0) {
                $assets_to_equity_ratio = round($total_assets/$moetotal_amt, 2);
            }

            array_push($massets_to_equity_ratios, ['assets_to_equity_ratio' => $assets_to_equity_ratio]);

            $debt_to_equity_ratio = 0;
            if ($moetotal_amt > 0) {
                $debt_to_equity_ratio = round($total_liabilities/$moetotal_amt, 2);
            }

            array_push($mdebt_to_equity_ratios, ['debt_to_equity_ratio' => $debt_to_equity_ratio]);

        }
        if (is_null($defcurr)) {
            return redirect('settings')->with('warning', 'Please set your Default Currency to continue');
        }
        $currency = $defcurr->code;
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.company.monthly-balance-sheet', compact('page', 'title', 'company', 'cshops', 'currshop', 'currency', 'settings', 'reporttime', 'is_post_query', 'start_date', 'end_date', 'duration', 'duration_sw', 'years', 'months', 'mcurrent_assets', 'mcatotals', 'mfixed_assets', 'mfatotals', 'mother_assets', 'moatotals', 'm_a_totals', 'mcurrent_liabilities', 'mcltotals', 'mlong_term_liabilities', 'mltltotals', 'mowners_equity', 'moetotals', 'm_lao_totals', 'mdebtratios', 'mcurrentratios', 'mworking_capitals', 'massets_to_equity_ratios', 'mdebt_to_equity_ratios'));
    }

    
    public function generalLedger(Request $request)
    {
        $page = 'General Ledger';
        $title = 'General Ledger';

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

        $duration = 'From '.date('d/m/Y', strtotime($start)).' To '.date('d/m.Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';
        $endingdate = Carbon::parse($start)->subDays(1);

        $currshop = null;
        $company = Company::find(Session::get('company_id'));
        $shops = $company->shops()->select('id', 'name')->get();
        $cshops = $shops;
        if (!empty($request['shop_id'])) {
            $currshop = Shop::find($request['shop_id']);
            $shops = $company->shops()->where('id', $currshop->id)->select('id', 'name')->get();
        }

        $defcurr = ShopCurrency::where('shop_id', Session::get('shop_id'))->where('is_default', true)->first();

        $gledgers = [];


        foreach ($shops as $key => $shop) {
            $shopledgers = GeneralLedger::where('shop_id', $shop->id)->join('transaction_accounts', 'transaction_accounts.id', '=', 'general_ledgers.transaction_account_id')->select('date', 'reference', 'transaction_description', 'type', 'account_number', 'account_name', 'debit_amount', 'credit_amount')->get();
            foreach ($shopledgers as $key => $value) {
                array_push($gledgers, ['date' => $value->date, 'branch' => $shop->name, 'reference' => $value->reference, 'transaction_description' => $value->transaction_description, 'type' => $value->type, 'account_number' => $value->account_number, 'account_name' => $value->account_name, 'debit_amount' => $value->debit_amount, 'credit_amount' => $value->credit_amount]);
            }
        }

        // Create an array to hold the 'date' values
        $dates = array();
        foreach ($gledgers as $key => $row) {
           $dates[$key] = strtotime($row['date']);
        }

        // Sort the multidimensional array based on the 'date' values using array_multisort() with a callback
        array_multisort($dates, SORT_ASC, $gledgers);
        
        $cash_balance = 0;

        return view('reports.company.general-ledger', compact('page', 'title', 'start_date', 'end_date', 'is_post_query', 'company', 'cshops', 'currshop', 'defcurr', 'duration', 'endingdate', 'cash_balance', 'gledgers'));
    }

    
    public function trialBalance(Request $request)
    {
        $page = 'Trial Balance';
        $title = 'Trial Balance';

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

        $duration = 'From '.date('d/m/Y', strtotime($start)).' To '.date('d/m.Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';
        $endingdate = Carbon::parse($start)->subDays(1);

        $currshop = null;
        $company = Company::find(Session::get('company_id'));
        $shops = $company->shops()->select('id', 'name')->get();
        $cshops = $shops;
        if (!empty($request['shop_id'])) {
            $currshop = Shop::find($request['shop_id']);
            $shops = $company->shops()->where('id', $currshop->id)->select('id', 'name')->get();
        }

        $defcurr = ShopCurrency::where('shop_id', Session::get('shop_id'))->where('is_default', true)->first();

        $gledgers = [];


        foreach ($shops as $key => $shop) {
            $shopledgers = GeneralLedger::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->join('transaction_accounts', 'transaction_accounts.id', '=', 'general_ledgers.transaction_account_id')->groupBy('transaction_account_id')->orderBy('account_number', 'asc')->get([
                \DB::raw('transaction_account_id as id'),
                \DB::raw('account_number'),
                \DB::raw('account_name'),
                \DB::raw('type'),
                \DB::raw('SUM(debit_amount) as debit_amount'),
                \DB::raw('SUM(credit_amount) as credit_amount')
            ]);
            foreach ($shopledgers as $key => $value) {
                $tda_before = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $value->id)->whereDate('date', '<', $start)->sum('debit_amount');
                $tca_before = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $value->id)->whereDate('date', '<', $start)->sum('credit_amount');
                $bgbalance = $tda_before-$tca_before;
                $endbalance = $bgbalance+$value->debit_amount-$value->credit_amount;
                array_push($gledgers, ['account_number' => $value->account_number, 'account_name' => $value->account_name, 'type' => $value->type, 'beginning_balance' => $bgbalance, 'debit_amount' => $value->debit_amount, 'credit_amount' => $value->credit_amount, 'ending_balance' => $endbalance]);
            }
        }

        // Create an array to hold the 'date' values
        $trialBalances = array();
        foreach ($gledgers as $key => $row) {
            if (isset($trialBalances[$row['account_number']])) {
                $trialBalances[$row['account_number']]['beginning_balance'] += $row['beginning_balance'];
                $trialBalances[$row['account_number']]['debit_amount'] += $row['debit_amount'];
                $trialBalances[$row['account_number']]['credit_amount'] += $row['credit_amount'];
                $trialBalances[$row['account_number']]['ending_balance'] += $row['ending_balance'];
            }else{
                $trialBalances[$row['account_number']]['account_number'] = $row['account_number'];
                $trialBalances[$row['account_number']]['account_name'] = $row['account_name'];
                $trialBalances[$row['account_number']]['type'] = $row['type'];
                $trialBalances[$row['account_number']]['beginning_balance'] = $row['beginning_balance'];
                $trialBalances[$row['account_number']]['debit_amount'] = $row['debit_amount'];
                $trialBalances[$row['account_number']]['credit_amount'] = $row['credit_amount'];
                $trialBalances[$row['account_number']]['ending_balance'] = $row['ending_balance'];
            }
        }

        // return $trialBalances;

        return view('reports.company.trial-balance', compact('page', 'title', 'start_date', 'end_date', 'is_post_query', 'company', 'cshops', 'currshop', 'defcurr', 'duration', 'endingdate', 'trialBalances'));
    }
}
