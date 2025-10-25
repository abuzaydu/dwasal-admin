<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Session;
use \Carbon\Carbon;
use \Carbon\CarbonPeriod;
use App\Models\Company;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Setting;
use App\Models\Device;
use App\Models\Contract;
use App\Models\DailyDeposit;
use App\Models\ContractService;
use App\Models\SalePayment;

class CReportsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'Riders Dashboard';
        $title = 'Riders Dashboard';

        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = Carbon::now()->endOfDay();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $currency = '';
        $dfc = ShopCurrency::where('shop_id', Session::get('shop_id'))->where('is_default', true)->first();
        if (!is_null($dfc)) {
            $currency = $dfc->code;
        }else{
            return redirect('settings')->with('warning', 'Please set your Default Currency to continue');
        }

        $firstday = $start;
        $lastday = $end;
        $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->whereDate('actual_end_date', '>', $firstday)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->get();
        
        $deposits = array();
        $tworkingdays = 0;
        $texpected_amt = 0;
        $tpaiddays = 0;
        $tpaid_amt = 0;
        $tpendingdays = 0;
        $tpending_amt = 0;
        $tpaidamt = 0;
        $tpendingamt = 0;
        foreach ($contracts as $key => $contract) {
            $diff = strtotime($lastday)-strtotime($contract->start_date);
            $checkdays = round($diff / (60 * 60 * 24));
            if ($checkdays > 0) {
                $datediff = strtotime($lastday)-strtotime($firstday);
                $workingdays = round($datediff / (60 * 60 * 24));
                $wr_start_date = date('d/m/Y', strtotime($firstday));
                $diffsf = strtotime($firstday)-strtotime($contract->start_date);
                
                if ($diffsf <= 0) {
                    $wr_start_date = date('d/m/Y', strtotime($contract->start_date));
                    $cstartdate = date('Y-m-d 00:00:00', strtotime($contract->start_date));
                    $datediff = strtotime($lastday)-strtotime($cstartdate);
                    $workingdays = round($datediff / (60 * 60 * 24));
                }
                
                $pdays = DailyDeposit::where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->count();
                $pending_to_date = $workingdays-$pdays;

                $depperday = ContractService::where('contract_id', $contract->id)->where('is_add_on', false)->sum('unit_price');

                $tpaid_amt += ($pdays*$depperday);
                if ($pending_to_date > 0) {
                    $tpending_amt += ($pending_to_date*$depperday);
                }
                $tpaiddays += $pdays;
                $tpendingdays += $pending_to_date;
                $tworkingdays += $workingdays;
                $texpected_amt = $tworkingdays*$depperday;
            }
        }

        $registered = Contract::where('shop_id', $shop->id)->count();
        $working = Contract::where('shop_id', $shop->id)->where('status', 'Working')->count();
        $graduations = Contract::where('shop_id', $shop->id)->where('status', 'Graduated')->count();
        $terminated = Contract::where('shop_id', $shop->id)->where('status', 'Terminated')->count();
        $replaced = Contract::where('shop_id', $shop->id)->where('type', 'Replacement')->count();

        return view('reports.contracts.index', compact('page', 'title', 'company', 'shop', 'currency', 'is_post_query', 'start_date', 'end_date', 'tworkingdays', 'texpected_amt', 'tpaiddays', 'tpaid_amt', 'tpendingdays', 'tpending_amt', 'registered', 'working', 'graduations', 'terminated', 'replaced'));
    }

    public function dailyDeposits(Request $request)
    {
        $page = 'Daily Deposits Report';
        $title = 'Daily Deposits Report';
        
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = Carbon::now()->endOfDay();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $period = new CarbonPeriod($start_date, '1 month', $end_date);
        $months = array();
        $lastmonth = null;
        foreach($period as $month) { 
            array_push($months, array(
                'month' => $month->format('M Y')
            ));
            if ($period->last()) {
                $lastmonth = $month->format('M Y');
            }
        }


        $firstday = $start;
        $lastday = $end;
        $curmonth = null;
        if (!empty($request['month'])) {
            $curmonth = $request['month'];

            $firstday = Carbon::parse(' 07'.$curmonth)->startOfMonth()->format('Y-m-d 00:00:00');
            $lastday = Carbon::parse(' 07'.$curmonth)->endOfMonth()->format('Y-m-d 23:59:59');
        }

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $tleaders = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->groupBy('tl_name')->select('tl_name')->get();
        $ctl_name = null;
        $sort_by = null;
        $contracts = [];
        if (!empty($request['tl_name'])) {
            $ctl_name = $request['tl_name'];
            if (!empty($request['sort_by'])) {
                $sort_by = $request['sort_by'];
                $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->whereDate('actual_end_date', '>', $firstday)->where('tl_name', $ctl_name)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->orderBy($request['sort_by'], 'asc')->get();
            }else{
                $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->whereDate('actual_end_date', '>', $firstday)->where('tl_name', $ctl_name)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->get();
            }
        }else{
            if (!empty($request['sort_by'])) {
                $sort_by = $request['sort_by'];
                $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->whereDate('actual_end_date', '>', $firstday)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->orderBy($request['sort_by'], 'asc')->get();
            }else{
                $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->whereDate('actual_end_date', '>', $firstday)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->get();
            }
        }

        $deposits = array();
        $tpaidamt = 0;
        $tpendingamt = 0;
        foreach ($contracts as $key => $contract) {
            $diff = strtotime($lastday)-strtotime($contract->start_date);
            $checkdays = round($diff / (60 * 60 * 24));
            if ($checkdays > 0) {
                $datediff = strtotime($lastday)-strtotime($firstday);
                $workingdays = round($datediff / (60 * 60 * 24));
                $wr_start_date = date('d/m/Y', strtotime($firstday));
                $diffsf = strtotime($firstday)-strtotime($contract->start_date);
                // Log::info($diffsf);
                if ($diffsf <= 0) {
                    $wr_start_date = date('d/m/Y', strtotime($contract->start_date));
                    $cstartdate = date('Y-m-d 00:00:00', strtotime($contract->start_date));
                    $datediff = strtotime($lastday)-strtotime($cstartdate);
                    $workingdays = round($datediff / (60 * 60 * 24));
                }
                
                $pdays = DailyDeposit::where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->count();
                $pending_to_date = $workingdays-$pdays;

                $depperday = ContractService::where('contract_id', $contract->id)->where('is_add_on', false)->sum('unit_price');

                $tpaidamt += ($pdays*$depperday);
                if ($pending_to_date > 0) {
                    $tpendingamt += ($pending_to_date*$depperday);
                }

                array_push($deposits, ['tl_name' => $contract->tl_name, 'driver' => $contract->name, 'start_date' => $wr_start_date, 'month' => $curmonth, 'working_days' => $workingdays, 'paid_days' => $pdays, 'pending_to_date' => $pending_to_date, 'depperday' => $depperday]);
            }
        }

        // return $contracts;
        return view('reports.contracts.daily-deposit', compact('page', 'title', 'company', 'shop', 'months', 'curmonth', 'start_date', 'end_date', 'is_post_query', 'deposits', 'tleaders', 'ctl_name', 'sort_by', 'tpaidamt', 'tpendingamt'));
    }

    public function monthlyDeposits(Request $request)
    {   
        $page = 'Monthly Deposits Report';
        $title = 'Monthly Deposits Report';
        
        $now = Carbon::now();
        $start = $now->startOfYear();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $period = new CarbonPeriod($start_date, '1 month', $end_date);
        $months = array();
        $lastmonth = null;
        foreach($period as $month) { 
            array_push($months, array(
                'month' => $month->format('M Y')
            ));
            if ($period->last()) {
                $lastmonth = $month->format('M Y');
            }
        }

        $curmonth = $lastmonth;
        if (!empty($request['month'])) {
            $curmonth = $request['month'];
        }

        $firstday = Carbon::parse(' 07'.$curmonth)->startOfMonth()->format('Y-m-d 00:00:00');
        $lastday = Carbon::parse(' 07'.$curmonth)->endOfMonth()->format('Y-m-d 23:59:59');

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $tleaders = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->groupBy('tl_name')->select('tl_name')->get();
        $ctl_name = null;
        $sort_by = null;
        $contracts = [];
        if (!empty($request['tl_name'])) {
            $ctl_name = $request['tl_name'];
            if (!empty($request['sort_by'])) {
                $sort_by = $request['sort_by'];
                $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->whereDate('actual_end_date', '>', $firstday)->where('tl_name', $ctl_name)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->orderBy($request['sort_by'], 'asc')->get();
            }else{
                $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->whereDate('actual_end_date', '>', $firstday)->where('tl_name', $ctl_name)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->get();
            }
        }else{
            if (!empty($request['sort_by'])) {
                $sort_by = $request['sort_by'];
                $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->whereDate('actual_end_date', '>', $firstday)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->orderBy($request['sort_by'], 'asc')->get();
            }else{
                $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->whereDate('actual_end_date', '>', $firstday)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->get();
            }
        }

        $depperday = 0;
        $deposits = array();
        foreach ($contracts as $key => $contract) {
            $diff = strtotime($lastday)-strtotime($contract->start_date);
            $checkdays = round($diff / (60 * 60 * 24));
            if ($checkdays > 0) {
                $cstartdate = date('Y-m-d 00:00:00', strtotime($contract->start_date));
                $cenddate = date('Y-m-d 23:59:59', strtotime($contract->end_date));
                $workingdays = Carbon::parse(' 07'.$curmonth)->daysInMonth;
                $startmonth = Carbon::parse($contract->start_date)->format('M Y');
                $endmonth = Carbon::parse($contract->end_date)->format('M Y');
                if ($startmonth == $curmonth) {
                    $diff = strtotime($lastday)-strtotime($cstartdate);
                    $workingdays = round($diff / (60 * 60 * 24)); 
                }elseif($endmonth == $curmonth) {
                    $diff = strtotime($cenddate)-strtotime($firstday);
                    $workingdays = round($diff / (60 * 60 * 24)); 
                }
                $wr_start_date = date('d/m/Y', strtotime($firstday));
                $diffsf = strtotime($firstday)-strtotime($contract->start_date);
                // Log::info($diffsf);
                if ($diffsf <= 0) {
                    $wr_start_date = date('d/m/Y', strtotime($contract->start_date));
                }

                $pdays = DailyDeposit::where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->count();
                $depperday = ContractService::where('contract_id', $contract->id)->where('is_add_on', false)->sum('unit_price');
                $exp_amont = $workingdays*$depperday;
                $initialdep = ContractService::where('contract_id', $contract->id)->where('is_add_on', true)->sum('total');

                $salepays = SalePayment::where('an_sale_id', $contract->an_sale_id)->whereBetween('pay_date', [$firstday, $lastday])->get();
                $deposited = 0;
                $paid_amt = 0;
                $paid_days = 0;
                foreach ($salepays as $key => $value) {
                    $deposited += DailyDeposit::where('sale_payment_id', $value->id)->where('contract_id', $contract->id)->sum('amount');
                    $paid_amt += DailyDeposit::where('sale_payment_id', $value->id)->where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->sum('amount');
                    $paid_days += DailyDeposit::where('sale_payment_id', $value->id)->where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->count();
                }
                $predays = $pdays-$paid_days;
                $prepaid = $predays*$depperday;
                array_push($deposits, ['tl_name' => $contract->tl_name, 'driver' => $contract->name, 'start_date' => $wr_start_date, 'month' => $curmonth, 'working_days' => $workingdays, 'paid_days' => $pdays, 'exp_deposit' => $exp_amont, 'pre_paid_amt' => $prepaid, 'paid_amt' => ($paid_amt+$prepaid), 'deposited' => $deposited]);
            }
        }

        $totalexp = 0; $totalprepaid = 0; $totalpaid = 0; $totaldeposited = 0;
        foreach($deposits as $index =>  $dep) {
            $totalexp += $dep['exp_deposit'];
            $totalpaid += $dep['paid_amt'];
            $totaldeposited += $dep['deposited'];
        }

        return view('reports.contracts.monthly-deposit', compact('page', 'title', 'company', 'shop', 'months', 'curmonth', 'start_date', 'end_date', 'is_post_query', 'deposits', 'depperday', 'tleaders', 'ctl_name', 'totalexp', 'totalpaid', 'totaldeposited', 'sort_by'));
    }

    public function monthlyCollection(Request $request)
    {   
        $page = 'Monthly Collections Report';
        $title = 'Monthly Collections Report';
        
        $now = Carbon::now();
        $start = $now->startOfYear();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $period = new CarbonPeriod($start_date, '1 month', $end_date);
        $months = array();
        $lastmonth = null;
        foreach($period as $month) { 
            array_push($months, array(
                'month' => $month->format('M Y')
            ));
            if ($period->last()) {
                $lastmonth = $month->format('M Y');
            }
        }

        $curmonth = $lastmonth;
        if (!empty($request['month'])) {
            $curmonth = $request['month'];
        }

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $tleaders = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->groupBy('tl_name')->select('tl_name')->get();
        $ctl_name = null;
        $contracts = [];
        if (!empty($request['tl_name'])) {
            $ctl_name = $request['tl_name'];
            $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->where('tl_name', $ctl_name)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'an_sale_id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->get();
        }else{
            $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'an_sale_id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->get();
        }

        $firstday = Carbon::parse(' 07'.$curmonth)->startOfMonth()->format('Y-m-d 00:00:00');
        $lastday = Carbon::parse(' 07'.$curmonth)->endOfMonth()->format('Y-m-d 23:59:59');
        $depperday = 0;
        $deposits = array();
        foreach ($contracts as $key => $contract) {
            $diff = strtotime($lastday)-strtotime($contract->start_date);
            $checkdays = round($diff / (60 * 60 * 24));
            if ($checkdays > 0) {
                $start_date = date('Y-m-d 00:00:00', strtotime($contract->start_date));
                $end_date = date('Y-m-d 23:59:59', strtotime($contract->end_date));
                $workingdays = Carbon::parse(' 07'.$curmonth)->daysInMonth;
                $startmonth = Carbon::parse($contract->start_date)->format('M Y');
                $endmonth = Carbon::parse($contract->end_date)->format('M Y');
                if ($startmonth == $curmonth) {
                    $diff = strtotime($lastday)-strtotime($start_date);
                    $workingdays = round($diff / (60 * 60 * 24)); 
                }elseif($endmonth == $curmonth) {
                    $diff = strtotime($end_date)-strtotime($firstday);
                    $workingdays = round($diff / (60 * 60 * 24)); 
                }
                $pdays = DailyDeposit::where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->count();
                $depperday = ContractService::where('contract_id', $contract->id)->where('is_add_on', false)->sum('unit_price');
                $exp_amont = $workingdays*$depperday;
                $initialdep = ContractService::where('contract_id', $contract->id)->where('is_add_on', true)->sum('total');

                $salepays = SalePayment::where('an_sale_id', $contract->an_sale_id)->whereBetween('pay_date', [$firstday, $lastday])->get();
                $deposited = 0;
                $paid_amt = 0;
                $paid_days = 0;
                foreach ($salepays as $key => $value) {
                    $deposited += DailyDeposit::where('sale_payment_id', $value->id)->where('contract_id', $contract->id)->sum('amount');
                    $paid_amt += DailyDeposit::where('sale_payment_id', $value->id)->where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->sum('amount');
                    $paid_days += DailyDeposit::where('sale_payment_id', $value->id)->where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->count();
                }
                $predays = $pdays-$paid_days;
                $prepaid = $predays*$depperday;
                array_push($deposits, ['tl_name' => $contract->tl_name, 'driver' => $contract->name, 'start_date' => $contract->start_date, 'month' => $curmonth, 'working_days' => $workingdays, 'paid_days' => $pdays, 'exp_deposit' => $exp_amont, 'pre_paid_amt' => $prepaid, 'paid_amt' => $paid_amt, 'deposited' => $deposited]);
            }
        }

        $totalexp = 0; $totalprepaid = 0; $totalpaid = 0; $totaldeposited = 0;
        foreach($deposits as $index =>  $dep) {
            $totalexp += $dep['exp_deposit'];
            $totalprepaid += $dep['pre_paid_amt'];
            $totalpaid += $dep['paid_amt'];
            $totaldeposited += $dep['deposited'];
        }

        return view('reports.contracts.monthly-collect', compact('page', 'title', 'company', 'shop', 'months', 'curmonth', 'start_date', 'end_date', 'is_post_query', 'deposits', 'depperday', 'tleaders', 'ctl_name', 'totalexp', 'totalprepaid', 'totalpaid', 'totaldeposited'));
    }

    public function tlDailyPerformance(Request $request)
    {
        $page = 'TL Daily Performance Report';
        $title = 'TL Daily Performance Report';
        
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = Carbon::now()->endOfDay();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $period = new CarbonPeriod($start_date, '1 month', $end_date);
        $months = array();
        $lastmonth = null;
        foreach($period as $month) { 
            array_push($months, array(
                'month' => $month->format('M Y')
            ));
            if ($period->last()) {
                $lastmonth = $month->format('M Y');
            }
        }


        $firstday = $start;
        $lastday = $end;
        $curmonth = null;
        if (!empty($request['month'])) {
            $curmonth = $request['month'];

            $firstday = Carbon::parse(' 07'.$curmonth)->startOfMonth()->format('Y-m-d 00:00:00');
            $lastday = Carbon::parse(' 07'.$curmonth)->endOfMonth()->format('Y-m-d 23:59:59');
            $start_date = $firstday;
            $end_date = $lastday;
        }

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $tleaders = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->groupBy('tl_name')->select('tl_name')->get();
        $ctl_name = null;
        $sort_by = null;
        $contracts = [];
        if (!empty($request['tl_name'])) {
            $ctl_name = $request['tl_name'];
            $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->whereDate('actual_end_date', '>', $firstday)->where('tl_name', $ctl_name)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->orderBy('tl_name', 'asc')->get();
        }else{
            $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->whereDate('actual_end_date', '>', $firstday)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->orderBy('tl_name', 'asc')->get();
        }

        $deposits = array();
        foreach ($contracts as $key => $contract) {
            $diff = strtotime($lastday)-strtotime($contract->start_date);
            $checkdays = round($diff / (60 * 60 * 24));
            if ($checkdays > 0) {
                $datediff = strtotime($lastday)-strtotime($firstday);
                $workingdays = round($datediff / (60 * 60 * 24));
                $wr_start_date = date('d/m/Y', strtotime($firstday));
                $diffsf = strtotime($firstday)-strtotime($contract->start_date);
                // Log::info($diffsf);
                if ($diffsf <= 0) {
                    $wr_start_date = date('d/m/Y', strtotime($contract->start_date));
                    $cstartdate = date('Y-m-d 00:00:00', strtotime($contract->start_date));
                    $datediff = strtotime($lastday)-strtotime($cstartdate);
                    $workingdays = round($datediff / (60 * 60 * 24));
                }
                
                $pdays = DailyDeposit::where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->count();
                $pending_to_date = $workingdays-$pdays;

                $depperday = ContractService::where('contract_id', $contract->id)->where('is_add_on', false)->sum('unit_price');

                array_push($deposits, ['tl_name' => $contract->tl_name, 'start_date' => $wr_start_date, 'month' => $curmonth, 'working_days' => $workingdays, 'paid_days' => $pdays, 'pending_to_date' => $pending_to_date, 'depperday' => $depperday]);
            }
        }

        $tldresult = array();
        foreach ($deposits as $key => $dep) {
            $expected_amt = $dep['working_days']*$dep['depperday'];
            $paid_amt = $dep['paid_days']*$dep['depperday'];
            $pending_amt = $dep['pending_to_date']*$dep['depperday'];
            if (isset($tldresult[$dep['tl_name']])) {
                $tldresult[$dep['tl_name']]['expected_amt'] += $expected_amt;
                $tldresult[$dep['tl_name']]['collected_amt'] += $paid_amt;
                $tldresult[$dep['tl_name']]['pending_amt'] += $pending_amt;
            }else{
                $tldresult[$dep['tl_name']]['tl_name'] = $dep['tl_name'];
                $tldresult[$dep['tl_name']]['expected_amt'] = $expected_amt;
                $tldresult[$dep['tl_name']]['collected_amt'] = $paid_amt;
                $tldresult[$dep['tl_name']]['pending_amt'] = $pending_amt;
            }
        }


        $texpectedamt = 0;
        $tpaidamt = 0;
        $tpendingamt = 0;
        $tperformance = 0;
        $tlperformances = array();
        foreach ($tldresult as $key => $value) {
            $texpectedamt += $value['expected_amt'];
            $tpaidamt += $value['collected_amt'];
            $tpendingamt += $value['pending_amt'];
            $performance = 0;
            if ($value['expected_amt'] > 0) {
                $performance = round(($value['collected_amt']/$value['expected_amt'])*100);
            }
            array_push($tlperformances, ['tl_name' => $value['tl_name'], 'start_date' => $start_date, 'up_to_date' => $end_date, 'expected_amt' => $value['expected_amt'], 'collected_amt' => $value['collected_amt'], 'pending_amt' => $value['pending_amt'], 'performance' => $performance]);
        }
        if ($texpectedamt > 0) {
            $tperformance = round(($tpaidamt/$texpectedamt)*100);
        }

        $performance = array_column($tlperformances, 'performance');
        array_multisort($tlperformances, SORT_ASC, $performance);
        return view('reports.contracts.tl-daily-performance', compact('page', 'title', 'company', 'shop', 'months', 'curmonth', 'start_date', 'end_date', 'is_post_query', 'tlperformances', 'tleaders', 'ctl_name', 'sort_by', 'texpectedamt', 'tpaidamt', 'tpendingamt', 'tperformance'));
    }

    public function tlMonthlyPerformance(Request $request)
    {   
        $page = 'TL Monthly Performance Report';
        $title = 'TL Monthly Performance Report';
        
        $now = Carbon::now();
        $start = $now->startOfYear();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $period = new CarbonPeriod($start_date, '1 month', $end_date);
        $months = array();
        $lastmonth = null;
        foreach($period as $month) { 
            array_push($months, array(
                'month' => $month->format('M Y')
            ));
            if ($period->last()) {
                $lastmonth = $month->format('M Y');
            }
        }

        $curmonth = $lastmonth;
        if (!empty($request['month'])) {
            $curmonth = $request['month'];
        }
        $firstday = Carbon::parse(' 07'.$curmonth)->startOfMonth()->format('Y-m-d 00:00:00');
        $lastday = Carbon::parse(' 07'.$curmonth)->endOfMonth()->format('Y-m-d 23:59:59');

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $tleaders = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->groupBy('tl_name')->select('tl_name')->get();
        $ctl_name = null;
        $contracts = [];
        if (!empty($request['tl_name'])) {
            $ctl_name = $request['tl_name'];
            $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->where('tl_name', $ctl_name)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'an_sale_id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->get();
        }else{
            $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'an_sale_id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->get();
        }

        $depperday = 0;
        $deposits = array();
        foreach ($contracts as $key => $contract) {
            $diff = strtotime($lastday)-strtotime($contract->start_date);
            $checkdays = round($diff / (60 * 60 * 24));
            if ($checkdays > 0) {
                $cstartdate = date('Y-m-d 00:00:00', strtotime($contract->start_date));
                $cenddate = date('Y-m-d 23:59:59', strtotime($contract->end_date));
                $workingdays = Carbon::parse(' 07'.$curmonth)->daysInMonth;
                $startmonth = Carbon::parse($contract->start_date)->format('M Y');
                $endmonth = Carbon::parse($contract->end_date)->format('M Y');
                if ($startmonth == $curmonth) {
                    $diff = strtotime($lastday)-strtotime($cstartdate);
                    $workingdays = round($diff / (60 * 60 * 24)); 
                }elseif($endmonth == $curmonth) {
                    $diff = strtotime($cenddate)-strtotime($firstday);
                    $workingdays = round($diff / (60 * 60 * 24)); 
                }
                $pdays = DailyDeposit::where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->count();
                $paid_amt = DailyDeposit::where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->sum('amount');
                $depperday = ContractService::where('contract_id', $contract->id)->where('is_add_on', false)->sum('unit_price');
                $exp_amont = $workingdays*$depperday;

                array_push($deposits, ['tl_name' => $contract->tl_name, 'driver' => $contract->name, 'start_date' => $contract->start_date, 'month' => $curmonth, 'working_days' => $workingdays, 'paid_days' => $pdays, 'exp_deposit' => $exp_amont, 'paid_amt' => $paid_amt]);
            }
        }

        $tlresult = [];
        foreach ($deposits as $key => $dep) {
            if (isset($tlresult[$dep['tl_name']])) {
                $tlresult[$dep['tl_name']]['working_days'] += $dep['working_days'];
                $tlresult[$dep['tl_name']]['paid_days'] += $dep['paid_days'];
                $tlresult[$dep['tl_name']]['exp_deposit'] += $dep['exp_deposit'];
                $tlresult[$dep['tl_name']]['paid_amt'] += $dep['paid_amt'];
            }else{
                $tlresult[$dep['tl_name']]['tl_name'] = $dep['tl_name'];
                $tlresult[$dep['tl_name']]['month'] = $dep['month'];
                $tlresult[$dep['tl_name']]['working_days'] = $dep['working_days'];
                $tlresult[$dep['tl_name']]['paid_days'] = $dep['paid_days'];
                $tlresult[$dep['tl_name']]['exp_deposit'] = $dep['exp_deposit'];
                $tlresult[$dep['tl_name']]['paid_amt'] = $dep['paid_amt'];
            }
        }

        $texpectedamt = 0;
        $tpaidamt = 0;
        $tpendingamt = 0;
        $tperformance = 0;
        $tlcollections = array();
        foreach ($tlresult as $key => $value) {
            $texpectedamt += $value['exp_deposit'];
            $tpaidamt += $value['paid_amt'];
            $pending_amt = ($value['exp_deposit']-$value['paid_amt']);
            $tpendingamt += $pending_amt;

            $performance = 0;
            if ($value['exp_deposit'] > 0) {
                $performance = round(($value['paid_amt']/$value['exp_deposit'])*100); 
            }
            array_push($tlcollections, ['tl_name' => $value['tl_name'], 'month' => $value['month'], 'expected_amt' => $value['exp_deposit'], 'paid_amt' => $value['paid_amt'], 'pending_amt' => $pending_amt, 'performance' => $performance]);
        }

        if ($texpectedamt > 0) {
            $tperformance = round(($tpaidamt/$texpectedamt)*100);
        }


        $performance = array_column($tlcollections, 'performance');
        array_multisort($tlcollections, SORT_ASC, $performance);
        // return $tlcollections;
        return view('reports.contracts.tl-monthly-performance', compact('page', 'title', 'company', 'shop', 'months', 'curmonth', 'start_date', 'end_date', 'is_post_query', 'tlcollections', 'tleaders', 'ctl_name', 'texpectedamt', 'tpaidamt', 'tpendingamt', 'tperformance'));    
    }

    public function tlMonthlyCollection(Request $request)
    {   
        $page = 'TL Monthly Collections Report';
        $title = 'TL Monthly Collections Report';
        
        $now = Carbon::now();
        $start = $now->startOfYear();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $period = new CarbonPeriod($start_date, '1 month', $end_date);
        $months = array();
        $lastmonth = null;
        foreach($period as $month) { 
            array_push($months, array(
                'month' => $month->format('M Y')
            ));
            if ($period->last()) {
                $lastmonth = $month->format('M Y');
            }
        }

        $curmonth = $lastmonth;
        if (!empty($request['month'])) {
            $curmonth = $request['month'];
        }

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'an_sale_id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->get();

        $firstday = Carbon::parse(' 07'.$curmonth)->startOfMonth()->format('Y-m-d 00:00:00');
        $lastday = Carbon::parse(' 07'.$curmonth)->endOfMonth()->format('Y-m-d 23:59:59');
        $depperday = 0;
        $deposits = array();
        foreach ($contracts as $key => $contract) {
            $diff = strtotime($lastday)-strtotime($contract->start_date);
            $checkdays = round($diff / (60 * 60 * 24));
            if ($checkdays > 0) {
                $start_date = date('Y-m-d 00:00:00', strtotime($contract->start_date));
                $end_date = date('Y-m-d 23:59:59', strtotime($contract->end_date));
                $workingdays = Carbon::parse(' 07'.$curmonth)->daysInMonth;
                $startmonth = Carbon::parse($contract->start_date)->format('M Y');
                $endmonth = Carbon::parse($contract->end_date)->format('M Y');
                if ($startmonth == $curmonth) {
                    $diff = strtotime($lastday)-strtotime($start_date);
                    $workingdays = round($diff / (60 * 60 * 24)); 
                }elseif($endmonth == $curmonth) {
                    $diff = strtotime($end_date)-strtotime($firstday);
                    $workingdays = round($diff / (60 * 60 * 24)); 
                }
                $pdays = DailyDeposit::where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->count();
                $depperday = ContractService::where('contract_id', $contract->id)->where('is_add_on', false)->sum('unit_price');
                $exp_amont = $workingdays*$depperday;
                $initialdep = ContractService::where('contract_id', $contract->id)->where('is_add_on', true)->sum('total');

                $salepays = SalePayment::where('an_sale_id', $contract->an_sale_id)->whereBetween('pay_date', [$firstday, $lastday])->get();
                $deposited = 0;
                $paid_amt = 0;
                $paid_days = 0;
                foreach ($salepays as $key => $value) {
                    $deposited += DailyDeposit::where('sale_payment_id', $value->id)->where('contract_id', $contract->id)->sum('amount');
                    $paid_amt += DailyDeposit::where('sale_payment_id', $value->id)->where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->sum('amount');
                    $paid_days += DailyDeposit::where('sale_payment_id', $value->id)->where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->count();
                }
                $predays = $pdays-$paid_days;
                $prepaid = $predays*$depperday;
                array_push($deposits, ['tl_name' => $contract->tl_name, 'driver' => $contract->name, 'start_date' => $contract->start_date, 'month' => $curmonth, 'working_days' => $workingdays, 'paid_days' => $pdays, 'exp_deposit' => $exp_amont, 'pre_paid_amt' => $prepaid, 'paid_amt' => $paid_amt, 'deposited' => $deposited]);
            }
        }

        $tlresult = [];
        foreach ($deposits as $key => $dep) {
            if (isset($tlresult[$dep['tl_name']])) {
                $tlresult[$dep['tl_name']]['working_days'] += $dep['working_days'];
                $tlresult[$dep['tl_name']]['paid_days'] += $dep['paid_days'];
                $tlresult[$dep['tl_name']]['exp_deposit'] += $dep['exp_deposit'];
                $tlresult[$dep['tl_name']]['pre_paid_amt'] += $dep['pre_paid_amt'];
                $tlresult[$dep['tl_name']]['paid_amt'] += $dep['paid_amt'];
                $tlresult[$dep['tl_name']]['deposited'] += $dep['deposited'];
            }else{
                $tlresult[$dep['tl_name']]['tl_name'] = $dep['tl_name'];
                $tlresult[$dep['tl_name']]['month'] = $dep['month'];
                $tlresult[$dep['tl_name']]['working_days'] = $dep['working_days'];
                $tlresult[$dep['tl_name']]['paid_days'] = $dep['paid_days'];
                $tlresult[$dep['tl_name']]['exp_deposit'] = $dep['exp_deposit'];
                $tlresult[$dep['tl_name']]['pre_paid_amt'] = $dep['pre_paid_amt'];
                $tlresult[$dep['tl_name']]['paid_amt'] = $dep['paid_amt'];
                $tlresult[$dep['tl_name']]['deposited'] = $dep['deposited'];
            }
        }

        $tlcollections = array();
        foreach ($tlresult as $key => $value) {
            array_push($tlcollections, $value);
        }
        // return $tlcollections;
        return view('reports.contracts.tl-monthly-deposit', compact('page', 'title', 'company', 'shop', 'months', 'curmonth', 'start_date', 'end_date', 'is_post_query', 'tlcollections', 'depperday'));    
    }


    public function monthlyProfit(Request $request)
    {
        $page = 'Monthly Profit Report';
        $title = 'Monthly Profit Report';

        $now = Carbon::now();
        $start = $now->startOfYear();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['start_date'])) {
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

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'an_sale_id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status', 'device_id')->get();

        $depperday = 0;
        $deposits = array();
        $totalwdays = 0;
        $totalpaiddays = 0;
        foreach ($contracts as $key => $contract) {
            foreach ($months as $key => $month) {
                $firstday = date('Y-m-01', strtotime($month['date']));
                $lastday = date('Y-m-t', strtotime($month['date']));
                $curmonth = date('M Y', strtotime($month['date']));
                $diff = strtotime($lastday)-strtotime($contract->start_date);
                $checkdays = round($diff / (60 * 60 * 24));
                if ($checkdays > 0) {
                    $workingdays = Carbon::parse(' 07'.$curmonth)->daysInMonth;
                    $startmonth = Carbon::parse($contract->start_date)->format('M Y');
                    $endmonth = Carbon::parse($contract->end_date)->format('M Y');
                    if ($startmonth == $curmonth) {
                        $diff = strtotime($lastday)-strtotime($contract->start_date);
                        $workingdays = round($diff / (60 * 60 * 24)); 
                    }elseif($endmonth == $curmonth) {
                        $diff = strtotime($contract->end_date)-strtotime($firstday);
                        $workingdays = round($diff / (60 * 60 * 24)); 
                    }

                    $pdays = DailyDeposit::where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->count();

                    $depperday = ContractService::where('contract_id', $contract->id)->where('is_add_on', false)->sum('unit_price');
                    $device = Device::find($contract->device_id);
                    $dcperday = round($device->device_cost/$settings->dc_no_days,2);

                    $exp_amont = $workingdays*$depperday;
                    $exp_cost = $workingdays*$dcperday;

                    $paid_amt = $pdays*$depperday;
                    $costof_paid_amt = $pdays*$dcperday;

                    $salepays = SalePayment::where('an_sale_id', $contract->an_sale_id)->whereBetween('pay_date', [$firstday, $lastday])->get();
                    $deposited = 0;
                    foreach ($salepays as $key => $value) {
                        $deposited += DailyDeposit::where('sale_payment_id', $value->id)->where('contract_id', $contract->id)->sum('amount');
                    }

                    $depositdays = $deposited/$depperday;
                    $costof_deposited = $depositdays*$dcperday;

                    array_push($deposits, ['month' => $lastday, 'working_days' => $workingdays, 'exp_deposit' => $exp_amont, 'exp_cost' => $exp_cost, 'paid_days' => $pdays, 'paid_amt' => $paid_amt, 'costof_paid_amt' => $costof_paid_amt, 'deposited' => $deposited, 'costof_deposited' => $costof_deposited, 'depositdays' => $depositdays]);
                }
            }
        }

        $mresult = array();
        foreach ($deposits as $key => $value) {
            if (isset($mresult[$value['month']])) {
                $mresult[$value['month']]['working_days'] += $value['working_days'];
                $mresult[$value['month']]['exp_deposit'] += $value['exp_deposit'];
                $mresult[$value['month']]['exp_cost'] += $value['exp_cost'];
                $mresult[$value['month']]['paid_days'] += $value['paid_days'];
                $mresult[$value['month']]['paid_amt'] += $value['paid_amt'];
                $mresult[$value['month']]['costof_paid_amt'] += $value['costof_paid_amt'];
                $mresult[$value['month']]['deposited'] += $value['deposited'];
                $mresult[$value['month']]['costof_deposited'] += $value['costof_deposited'];
                $mresult[$value['month']]['depositdays'] += $value['depositdays'];
            }else{
                $mresult[$value['month']]['month'] = $value['month'];
                $mresult[$value['month']]['working_days'] = $value['working_days'];
                $mresult[$value['month']]['exp_deposit'] = $value['exp_deposit'];
                $mresult[$value['month']]['exp_cost'] = $value['exp_cost'];
                $mresult[$value['month']]['paid_days'] = $value['paid_days'];
                $mresult[$value['month']]['paid_amt'] = $value['paid_amt'];
                $mresult[$value['month']]['costof_paid_amt'] = $value['costof_paid_amt'];
                $mresult[$value['month']]['deposited'] = $value['deposited'];
                $mresult[$value['month']]['costof_deposited'] = $value['costof_deposited'];
                $mresult[$value['month']]['depositdays'] = $value['depositdays'];
            }
        }

        sort($mresult);
        $mprofits = [];
        foreach ($mresult as $key => $value) {
            array_push($mprofits, $value);
        }
        // Log::info($mprofits);
        return view('reports.contracts.monthly-profit-report', compact('page', 'title', 'company', 'shop', 'months', 'start_date', 'end_date', 'is_post_query', 'duration', 'mprofits'));
    }

    public function contractStatusReport(Request $request)
    {
        $page = 'Contracts Status Report';
        $title = 'Contracts Status Report';
        
        $now = Carbon::now();
        $start = $now->startOfYear();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $statuses = ['Created', 'Working', 'Terminated', 'Graduated', 'Registered', 'Cancelled'];
        $currstatus = '';
        $contracts = null;
        $columns = array(
            ['name' => 'tl_name', 'value' => 'TL Name'], 
            ['name' => 'name', 'value' => 'Rider'], 
            ['name' => 'start_date', 'value' => 'Start Date'],
            ['name' =>'amount', 'value' => 'Amount to Contract'],
            ['name' => 'days_worked', 'value' => 'Days Worked'],
            ['name' => 'amount_paid', 'value' => 'Amount Paid'],
            ['name' => 'status', 'value' => 'Status']
        );

        $value = array_column($columns, 'value');
        array_multisort($columns, SORT_ASC, $value);

        $sort_by = 'start_date';
        if (!empty($request['sort_by'])) {
            $sort_by = $request['sort_by'];
        }
        $sort_mode = 'asc';
        if (!empty($request['sort_mode'])) {
            $sort_mode = $request['sort_mode'];
        }
        if (!empty($request['status'])) {
            $currstatus = $request['status'];
            $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', $currstatus)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'amount', 'days_worked', 'amount_paid', 'status', 'contracts.created_at as created_at', 'contracts.updated_at as updated_at', 'is_deleted')->orderBy($sort_by, $sort_mode)->get();
        }else{
            $contracts = Contract::where('contracts.shop_id', $shop->id)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'amount', 'days_worked', 'amount_paid', 'status', 'contracts.created_at as created_at', 'contracts.updated_at as updated_at', 'is_deleted')->orderBy($sort_by, $sort_mode)->get();
        }


        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.contracts.contract-status-report', compact('page', 'title', 'company', 'shop', 'start_date', 'end_date', 'is_post_query', 'reporttime', 'contracts', 'statuses', 'currstatus', 'columns', 'sort_by', 'sort_mode'));
    }

    public function monthlyRegReport(Request $request)
    {
        $page = 'Monthly Registration Report';
        $title = 'Monthly Registration Report';
        
        $now = Carbon::now();
        $start = $now->startOfYear();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $period = new CarbonPeriod($start_date, '1 month', $end_date);
        $months = array();
        $lastmonth = null;
        foreach($period as $month) { 
            array_push($months, array(
                'month' => $month->format('M Y')
            ));
            if ($period->last()) {
                $lastmonth = $month->format('M Y');
            }
        }

        $curmonth = $lastmonth;
        if (!empty($request['month'])) {
            $curmonth = $request['month'];
        }

        $firstday = Carbon::parse(' 07'.$curmonth)->startOfMonth()->format('Y-m-d');
        $lastday = Carbon::parse(' 07'.$curmonth)->endOfMonth()->format('Y-m-d');
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));

        $contracts = Contract::where('contracts.shop_id', $shop->id)->whereBetween('start_date', [$firstday, $lastday])->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'amount', 'days_worked', 'amount_paid', 'status', 'contracts.created_at as created_at', 'contracts.updated_at as updated_at', 'is_deleted')->get();
        $today = Carbon::now()->format('Y-m-d');
        return view('reports.contracts.monthly-reg-report', compact('page', 'title', 'company', 'shop', 'start_date', 'end_date', 'is_post_query', 'contracts', 'months', 'curmonth', 'today'));
    }


    public function workingRiders(Request $request)
    {
        $page = 'Working Riders';
        $title = 'Working Riders';

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $columns = array(
            ['name' => 'tl_name', 'value' => 'TL Name'], 
            ['name' => 'name', 'value' => 'Rider'], 
            ['name' => 'start_date', 'value' => 'Contract Start Date'],
            ['name' =>'end_date', 'value' => 'Contract End Date'],
            ['name' => 'days_worked', 'value' => 'Days Worked'],
        );

        // $value = array_column($columns, 'value');
        // array_multisort($columns, SORT_ASC, $value);

        $sort_by = 'start_date';
        if (!empty($request['sort_by'])) {
            $sort_by = $request['sort_by'];
        }
        $sort_mode = 'asc';
        if (!empty($request['sort_mode'])) {
            $sort_mode = $request['sort_mode'];
        }

        $tleaders = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->groupBy('tl_name')->select('tl_name')->get();
        $ctl_name = null;
        if (!empty($request['tl_name'])) {
            $ctl_name = $request['tl_name'];
            $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->where('tl_name', $ctl_name)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'amount', 'days_worked', 'amount_paid', 'status', 'contracts.created_at as created_at', 'contracts.updated_at as updated_at', 'is_deleted')->orderBy($sort_by, $sort_mode)->get();
        }else{
            $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'amount', 'days_worked', 'amount_paid', 'status', 'contracts.created_at as created_at', 'contracts.updated_at as updated_at', 'is_deleted')->orderBy($sort_by, $sort_mode)->get();
        }

        $workingriders = [];
        foreach ($contracts as $key => $contract) {
            $diff = strtotime($contract->end_date) - strtotime($contract->start_date);
            $tworkingdays = round($diff/(60 * 60 * 24));
            $days_worked = $contract->days_worked;
            $leftdays = $tworkingdays-$days_worked;
            array_push($workingriders, ['id' => $contract->id, 'tl_name' => $contract->tl_name, 'name' => $contract->name, 'start_date' => $contract->start_date, 'days_worked' => $days_worked, 'leftdays' => $leftdays]);
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.contracts.working-riders', compact('page', 'title', 'company', 'shop', 'tleaders', 'ctl_name', 'workingriders', 'reporttime', 'columns', 'sort_by', 'sort_mode'));
    }


    public function upcomingGraduation(Request $request)
    {
        $page = 'Expected Graduates';
        $title = 'Expected Graduates';

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $columns = array(
            ['name' => 'tl_name', 'value' => 'TL Name'], 
            ['name' => 'name', 'value' => 'Rider'], 
            ['name' => 'start_date', 'value' => 'Contract Start Date'],
            ['name' =>'end_date', 'value' => 'Contract End Date'],
            ['name' => 'days_worked', 'value' => 'Days Worked'],
        );

        // $value = array_column($columns, 'value');
        // array_multisort($columns, SORT_ASC, $value);

        $sort_by = 'start_date';
        if (!empty($request['sort_by'])) {
            $sort_by = $request['sort_by'];
        }
        $sort_mode = 'asc';
        if (!empty($request['sort_mode'])) {
            $sort_mode = $request['sort_mode'];
        }

        $tleaders = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->groupBy('tl_name')->select('tl_name')->get();
        $ctl_name = null;
        if (!empty($request['tl_name'])) {
            $ctl_name = $request['tl_name'];
            $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')>where('tl_name', $ctl_name)->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'amount', 'days_worked', 'amount_paid', 'status', 'contracts.created_at as created_at', 'contracts.updated_at as updated_at', 'is_deleted')->orderBy($sort_by, $sort_mode)->get();
        }else{
            $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'amount', 'days_worked', 'amount_paid', 'status', 'contracts.created_at as created_at', 'contracts.updated_at as updated_at', 'is_deleted')->orderBy($sort_by, $sort_mode)->get();
        }
        $expgraduations = []; $abovetwentydays = 0; $tentwentydays = 0; $fivetendays = 0; $lessfivedays = 0;
        foreach ($contracts as $key => $contract) {
            $diff = strtotime($contract->end_date) - strtotime($contract->start_date);
            $tworkingdays = round($diff/(60 * 60 * 24));
            $days_worked = $contract->days_worked;
            $leftdays = $tworkingdays-$days_worked;
            if ($leftdays > 20) {
                $abovetwentydays += 1;
            }elseif ($leftdays <= 20 && $leftdays > 10) {
                $tentwentydays += 1;
            }elseif ($leftdays <= 10 && $leftdays > 5) {
                $fivetendays += 1;
            }else{
                $lessfivedays += 1;
            }
            array_push($expgraduations, ['id' => $contract->id, 'tl_name' => $contract->tl_name, 'name' => $contract->name, 'start_date' => $contract->start_date, 'end_date' => $contract->end_date, 'leftdays' => $leftdays]);
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.contracts.expected-graduation', compact('page', 'title', 'company', 'shop', 'tleaders', 'ctl_name', 'expgraduations', 'reporttime', 'abovetwentydays', 'tentwentydays', 'fivetendays', 'lessfivedays', 'columns', 'sort_by', 'sort_mode'));
    }

    public function overDeposited(Request $request)
    {
        $page = 'Over Deposited';
        $title = 'Over Deposited';

        $now = Carbon::now();
        $start = $now->startOfYear();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if(!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $period = new CarbonPeriod($start_date, '1 month', $end_date);
        $months = array();
        $lastmonth = null;
        foreach($period as $month) { 
            array_push($months, array(
                'month' => $month->format('M Y')
            ));
            if ($period->last()) {
                $lastmonth = $month->format('M Y');
            }
        }

        $curmonth = $lastmonth;
        if (!empty($request['month'])) {
            $curmonth = $request['month'];
        }

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'an_sale_id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->get();

        $firstday = Carbon::parse(' 07'.$curmonth)->startOfMonth()->format('Y-m-d');
        $lastday = Carbon::parse(' 07'.$curmonth)->endOfMonth()->format('Y-m-d');
        $depperday = 0;
        $deposits = array();
        $toverpaiddays = 0;
        foreach ($contracts as $key => $contract) {
            $diff = strtotime($lastday)-strtotime($contract->start_date);
            $checkdays = round($diff / (60 * 60 * 24));
            if ($checkdays > 0) {
                $workingdays = Carbon::parse(' 07'.$curmonth)->daysInMonth;
                $startmonth = Carbon::parse($contract->start_date)->format('M Y');
                $endmonth = Carbon::parse($contract->end_date)->format('M Y');
                if ($startmonth == $curmonth) {
                    $diff = strtotime($lastday)-strtotime($contract->start_date);
                    $workingdays = round($diff / (60 * 60 * 24)); 
                }elseif($endmonth == $curmonth) {
                    $diff = strtotime($contract->end_date)-strtotime($firstday);
                    $workingdays = round($diff / (60 * 60 * 24)); 
                }

                $pdays = DailyDeposit::where('contract_id', $contract->id)->whereBetween('date', [$firstday, $lastday])->count();
                $depperday = ContractService::where('contract_id', $contract->id)->where('is_add_on', false)->sum('unit_price');

                $salepays = SalePayment::where('an_sale_id', $contract->an_sale_id)->whereBetween('pay_date', [$firstday, $lastday])->get();
                $deposited = 0;
                foreach ($salepays as $key => $value) {
                    $deposited += DailyDeposit::where('sale_payment_id', $value->id)->where('contract_id', $contract->id)->sum('amount');
                }
                $tpaidays = $deposited/$depperday;
                $overpaiddays = $tpaidays-$pdays;
                if ($overpaiddays > 0) {       
                    $toverpaiddays += $overpaiddays;
                    array_push($deposits, ['tl_name' => $contract->tl_name, 'driver' => $contract->name, 'start_date' => $contract->start_date, 'month' => $curmonth, 'overpaiddays' => $overpaiddays]);
                }
            }
        }

        return view('reports.contracts.over-deposited', compact('page', 'title', 'company', 'shop', 'months', 'curmonth', 'start_date', 'end_date', 'is_post_query', 'deposits', 'depperday', 'toverpaiddays'));
    }

    public function contractToTerminate()
    {
        $page = 'Contract To Terminate';
        $title = 'Contract To Terminate';

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $contracts = Contract::where('contracts.shop_id', $shop->id)->where('status', 'Working')->join('customers', 'customers.id', '=', 'contracts.customer_id')->select('contracts.id as id', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'start_date', 'end_date', 'status')->get();
        $cterminates = []; $tdays = 0;
        foreach ($contracts as $key => $contract) {
            $lastdeposit = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'desc')->first();
            if (!is_null($lastdeposit)) {
                $now = Carbon::now()->format('Y-m-d');
                $diff = strtotime($now) - strtotime($lastdeposit->date);
                $unpaiddays = round($diff / (60 * 60 * 24));
                if ($unpaiddays > 4) {
                    array_push($cterminates, ['id' => $contract->id, 'tl_name' => $contract->tl_name, 'name' => $contract->name, 'start_date' => $contract->start_date, 'end_date' => $contract->end_date, 'date' => $lastdeposit->date, 'unpaiddays' => $unpaiddays]);
                    $tdays += $unpaiddays;
                }
            }
        }

        $total = count($cterminates);
        $average = 0;
        if ($total > 0) {
            $average = round($tdays/$total);
        }
        $today = Carbon::now()->format('d-m-Y');

        return view('reports.contracts.contract-to-terminate', compact('page', 'title', 'company', 'shop', 'cterminates', 'today', 'total', 'tdays', 'average'));
    }
}
