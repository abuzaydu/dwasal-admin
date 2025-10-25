<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use \Carbon\Carbon;
use \DB;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\Purchase;
use App\Models\SupplierTransaction;


class PurchaseReportController extends Controller
{
    public function index(Request $request)
    {
        $page = 'Reports';
        $title = 'Purchase Reports';
        $title_sw = 'Ripoti za Manunuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $suppliers = $shop->suppliers()->get();
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
          
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }
        $currsupp = '';
        $purchases = null;
        if (!empty($request['supplier_id'])) {
            $purchases = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->where('supplier_id', $request['supplier_id'])->whereBetween('purchases.time_created', [$start, $end])->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select('purchases.id as id', 'grn_no', 'invoice_no', 'purchases.time_created as time_created', 'name', 'total_amount', 'amount_paid', 'total_cost', 'purchases.created_at as created_at')->orderBy('purchases.time_created', 'desc')->get();
        }else{
            $purchases = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('purchases.time_created', [$start, $end])->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select('purchases.id as id', 'grn_no', 'invoice_no', 'purchases.time_created as time_created', 'name', 'total_amount', 'amount_paid', 'total_cost', 'purchases.created_at as created_at')->orderBy('purchases.time_created', 'desc')->get();
        }

        return view('reports.inventory.purchases', compact('page', 'title', 'title_sw', 'shop', 'suppliers', 'purchases', 'is_post_query', 'start_date', 'end_date'));
    }

    public function credits(Request $request)
    {
        $page = 'Reports';
        $title = 'Supplier Credit Reports';
        $title_sw = 'Ripoti za Mikopo ya wasambazaji';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $suppliers = $shop->suppliers()->get();
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
          
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

        $purchases = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->where('status', 'Pending')->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->orderBy('purchases.time_created', 'desc')->get();

        $totalsupdebts = array();
        $total_sup_ob = 0;
        $total_sup_invoices = 0;
        foreach ($suppliers as $key => $supplier) {
            $supobtrans = SupplierTransaction::where('supplier_id', $supplier->id)->where('invoice_no', 'OB')->where('shop_id', $shop->id)->first();
            $supopening_balance = 0;
            if (!is_null($supobtrans)) {
                $supopening_balance = $supobtrans->amount-$supobtrans->ob_paid;
            }

            $totalpurchases = Purchase::where('shop_id', $shop->id)->where('is_deleted', false)->where('supplier_id', $supplier->id)->get([
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(amount_paid) as amount_paid')
            ]);
            $new_sup_invoices = 0;
            foreach ($totalpurchases as $key => $value) {
                $new_sup_invoices += $value->total_amount-$value->amount_paid;
            }

            $total_supd = $supopening_balance+$new_sup_invoices;

            $total_sup_ob += $supopening_balance;
            $total_sup_invoices += $new_sup_invoices;

            array_push($totalsupdebts, ['supplier_id' => $supplier->id, 'supp_no' => $supplier->supp_id, 'name' => $supplier->name, 'contact_no' => $supplier->contact_no, 'opening_balance' => $supopening_balance, 'new_invoices' => $new_sup_invoices, 'total' =>  $total_supd]);
        }


        return view('reports.inventory.credits', compact('page', 'title', 'title_sw', 'shop', 'settings', 'duration', 'duration_sw', 'suppliers', 'purchases', 'totalsupdebts', 'total_sup_ob', 'total_sup_invoices', 'is_post_query', 'start_date', 'end_date'));
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

        $suppliers = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereRaw('(total_amount-amount_paid) > 0')->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select('suppliers.id as id', 'suppliers.supp_id as supp_id', 'suppliers.name as name')->groupBy('name')->get();

        $agings = array();
        foreach ($suppliers as $key => $supplier) {
            $d3 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '>=', $date3)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();

            $d6 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date3)->whereDate('purchases.created_at', '>', $date6)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();

            $d9 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date6)->whereDate('purchases.created_at', '>', $date9)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d12 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date9)->whereDate('purchases.created_at', '>', $date12)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();

            $d15 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date12)->whereDate('purchases.created_at', '>', $date15)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d18 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date15)->whereDate('purchases.created_at', '>', $date18)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d21 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date18)->whereDate('purchases.created_at', '>', $date21)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d24 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date21)->whereDate('purchases.created_at', '>', $date24)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d27 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date24)->whereDate('purchases.created_at', '>', $date27)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d30 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date27)->whereDate('purchases.created_at', '>', $date30)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d33 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date30)->whereDate('purchases.created_at', '>', $date33)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d36 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date33)->whereDate('purchases.created_at', '>', $date36)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();

            $ab360 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date36)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();

            $ctotal = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();

            array_push($agings, ['supp_id' => $supplier->supp_id, 'name' => $supplier->name, '0-30' => $d3->amount, '31-60' => $d6->amount, '61-90' => $d9->amount, '91-120' => $d12->amount, '121-150' => $d15->amount, '151-180' => $d18->amount, '181-210' => $d21->amount, '211-240' => $d24->amount, '241-270' => $d27->amount, '271-300' => $d30->amount, '301-330' => $d33->amount, '331-360' => $d36->amount, '>360' => $ab360->amount, 'ctotal' => $ctotal->amount]);
        }

        $start_date = null;            
        $end_date = null;
        $is_post_query = false;
        $customer = null;
        $customers = null;
        $crtime = \Carbon\Carbon::now();
        $duration = date('d M, Y', strtotime($crtime));
        $duration_sw = date('d M, Y', strtotime($crtime));
        $reporttime = $crtime->toDayDateTimeString();

        return view('reports.inventory.aging-report', compact('page', 'title', 'title_sw', 'shop', 'agings', 'start_date', 'end_date', 'is_post_query', 'customer', 'customers', 'duration', 'duration_sw', 'reporttime'));
    }


}
