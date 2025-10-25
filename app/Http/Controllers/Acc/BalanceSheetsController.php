<?php

namespace App\Http\Controllers\Acc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\MonthlyBalanceSheet;
use App\Jobs\MonthlyBalanceSheetJob;
use App\Models\BasicBalanceSheet;
use App\Jobs\BasicBalanceSheetJob;

class BalanceSheetsController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Monthly Balance Sheet Records';
        $title = 'Monthy Balance Sheet Records';

        $shop = Shop::find(Session::get('shop_id'));
        $months = MonthlyBalanceSheet::where('shop_id', $shop->id)->groupBy('date')->orderBy('date', 'desc')->select('date')->get();
        $lastday = new Carbon('last day of last month');
        $end = $lastday->endOfDay();
        // Log::info($end);
        $bs_date = date('Y-m-d', strtotime($end));
        $month = $bs_date;
        if (!empty($request['month'])) {
            $month = $request['month'];
        }

        $mbs = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $month)->count();

        if ($mbs > 0) {
                
            $current_assets = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $month)->where('item_category', 'CURRENT ASSETS')->get();

            $fixed_assets = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $month)->where('item_category', 'FIXED (LONG TERM) ASSETS')->get();

            $other_assets = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $month)->where('item_category', 'OTHER ASSETS')->get();

            $current_liabilities = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $month)->where('item_category', 'CURRENT LIABILITIES')->get();

            $long_term_liabilities = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $month)->where('item_category', 'LONG TERM LIABILITIES')->get();
            $owners_equity = MonthlyBalanceSheet::where('shop_id', $shop->id)->where('date', $month)->where('item_category', "OWNER'S EQUITY")->get();

            return view('accounting.balance-sheets.index', compact('page', 'title', 'shop', 'months', 'month', 'mbs', 'current_assets', 'fixed_assets', 'other_assets', 'current_liabilities', 'long_term_liabilities', 'owners_equity'));
        }else{
            return view('accounting.balance-sheets.index', compact('page', 'title', 'shop', 'months', 'month','mbs'));
        }
    }

    public function basic(Request $request)
    {
        $page = 'Balance Sheet Records';
        $title = 'Business Sheet Records';

        $shop = Shop::find(Session::get('shop_id'));
        $months = BasicBalanceSheet::where('shop_id', $shop->id)->groupBy('date')->orderBy('date', 'desc')->select('date')->get();
        $lastday = Carbon::now()->subYear(1)->endOfYear();
        $end = $lastday->endOfDay();
        // Log::info($end);
        $bs_date = date('Y-m-d', strtotime($end));
        $year = $bs_date;
        if (!empty($request['year'])) {
            $year = $request['year'];
        }

        $mbs = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $year)->count();

        if ($mbs > 0) {
                
            $current_assets = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $year)->where('item_category', 'CURRENT ASSETS')->get();

            $fixed_assets = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $year)->where('item_category', 'FIXED (LONG TERM) ASSETS')->get();

            $other_assets = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $year)->where('item_category', 'OTHER ASSETS')->get();

            $current_liabilities = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $year)->where('item_category', 'CURRENT LIABILITIES')->get();

            $long_term_liabilities = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $year)->where('item_category', 'LONG TERM LIABILITIES')->get();
            $owners_equity = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $year)->where('item_category', "OWNER'S EQUITY")->get();

            return view('accounting.balance-sheets.basic', compact('page', 'title', 'shop', 'months', 'year', 'mbs', 'current_assets', 'fixed_assets', 'other_assets', 'current_liabilities', 'long_term_liabilities', 'owners_equity'));
        }else{
            return view('accounting.balance-sheets.basic', compact('page', 'title', 'shop', 'months', 'year','mbs'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $shop = Shop::find(Session::get('shop_id'));

        dispatch(new MonthlyBalanceSheetJob($shop));

        return redirect('balance-sheets');
    }

    public function createBS()
    {

        $shop = Shop::find(Session::get('shop_id'));

        dispatch(new BasicBalanceSheetJob($shop));

        return redirect('basic-balance-sheets');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $mbs_item = MonthlyBalanceSheet::find($request['id']);
        if (!is_null($mbs_item)) {
            $mbs_item->amount = $request['amount'];
            $mbs_item->save();
            return response()->json(['success' => 1, 'msg' => 'Item Updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item not Found']);
        }
    }

    public function updateBSItem(Request $request)
    {
        $mbs_item = BasicBalanceSheet::find($request['id']);
        if (!is_null($mbs_item)) {
            $mbs_item->amount = $request['amount'];
            $mbs_item->save();
            return response()->json(['success' => 1, 'msg' => 'Item Updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item not Found']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
