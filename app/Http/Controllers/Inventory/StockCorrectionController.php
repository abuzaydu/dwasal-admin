<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\StockCorrection;
use App\Models\CorrectionTemp;
use App\Models\Stock;
use App\Jobs\StockUpdaterJob;


class StockCorrectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Stock Corrections';
        $title = 'Stock Corrections';
        $title_sw = 'Marekebisho ya Stocki';
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

        $shop = Shop::find(Session::get('shop_id'));
        $stockcorrections = StockCorrection::where('stock_corrections.shop_id', $shop->id)->whereBetween('stock_corrections.time_created', [$start, $end])->join('users', 'users.id', '=', 'stock_corrections.user_id')->join('products', 'products.id','=', 'stock_corrections.product_id')->select('stock_corrections.id as id', 'first_name', 'last_name', 'product_id', 'name', 'correction_qty', 'stock_corrections.in_stock as in_stock', 'diff_qty', 'stock_corrections.time_created as time_created', 'reason')->get();
        return view('products.corrections.index', compact('page', 'title', 'title_sw', 'shop', 'stockcorrections', 'start_date', 'end_date', 'is_post_query'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'New Stock Corrections';
        $title = 'New Stock Corrections';
        $title_sw = 'Marekebisho Mapya ya Stocki';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        return view('products.corrections.create', compact('page', 'title','title_sw','shop', 'settings'));
    }

    public function cancel()
    {
        $ctemps = CorrectionTemp::where('shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->get();
        foreach ($ctemps as $key => $value) {
            $value->delete();
        }
        return redirect()->route('stock-corrections.create')->with('success', 'Corrections cancelled successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $ctemps = CorrectionTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        $correction_date = Carbon::now();
        if (!empty($request->correction_date)) {
            $correction_date = $request->correction_date.' '.$correction_date->format('H:i:s');
        }
        foreach ($ctemps as $key => $temp) {
            $product = $shop->products()->where('id', $temp->product_id)->first();
            if (!is_null($product)) {
                $in_stock = 0;
                if (!is_null($product->in_stock)) {
                    $in_stock = $product->in_stock;
                }

                $stock_in = Stock::where('product_id', $temp->product_id)->where('is_deleted', false)->where('shop_id', $temp->shop_id)->sum('quantity_in');
                if($stock_in == 0 && $in_stock < 0){
                    $in_stock = 0;
                }
                $correction = new StockCorrection();
                $correction->shop_id = $temp->shop_id;
                $correction->user_id = $temp->user_id;
                $correction->product_id = $temp->product_id;
                $correction->correction_qty = $temp->correction_qty;
                $correction->in_stock = $in_stock;
                $correction->diff_qty = $correction->in_stock-$correction->correction_qty;
                $correction->reason = $request->reason;
                $correction->time_created = $correction_date;
                $correction->save();

                dispatch(new StockUpdaterJob($shop, $correction->product_id));
            }
        }

        foreach ($ctemps as $key => $value) {
            $value->delete();
        }

        return redirect('stock-corrections')->with('success', 'Stock Corrections created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
        $correction = StockCorrection::find(decrypt($id));
        if (!is_null($correction)) {
            $correction->delete();

            dispatch(new StockUpdaterJob($shop, $correction->product_id));
        }

        return redirect('stock-corrections')->with('success', 'Stock Corrections deleted successfully');        
    }

    public function deleteMultiple(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        foreach ($request->input('ids') as $key => $id) {
            $correction = StockCorrection::find($id);
            if (!is_null($correction)) {
                $correction->delete();

                dispatch(new StockUpdaterJob($shop, $correction->product_id));
            }
        }

        return redirect('stock-corrections')->with('success', 'Stock Corrections deleted successfully'); 
    }
}
