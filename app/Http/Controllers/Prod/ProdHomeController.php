<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Payment;
use App\Models\RmUse;
use App\Models\RmUseItem;
use App\Models\PmUseItem;
use App\Models\PmUse;
use App\Models\MroUse;
use App\Models\Setting;
use App\Models\ProductionCost;
use App\Models\ProductionCostItem;
use App\Models\DirectLabourCost;

class ProdHomeController extends Controller
{

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

        $page = "Home";
        $title = "Dashboard";
        $title_sw = "Dashibodi";
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $currency = '';
        $dfc = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (!is_null($dfc)) {                
            $currency = $dfc->code;
        }else{
            return redirect('settings')->with('warning', 'Please set your Default Currency to continue');
        }
        $now = Carbon::now();
        $start = $now->startOfMonth();
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

        if (app()->getLocale() == 'en') {
            $raw_material_l = "Raw Materials";
            $packing_material_l = "Packing Materials";
            $raw_material_s = "Raw Materials In Stock";
            $packing_material_s = "Packing In Stock";
        }else{
            $raw_material_l = "Malighafi";
            $packing_material_l = "vifungashio";
            $raw_material_s = "Stock ya Malighafi";
            $packing_material_s = " Stock ya vifungashio";
        }

        $rm_labels = array();
        $rm_uses_data = array();

        $pm_labels = array();
        $pm_uses_data = array();

        $pm_stock_labels = array();
        $pm_stock_data = array();

        $rm_stock_labels = array();
        $rm_stock_data = array();

        $product_lables = array();
        $product_qty = array();

        $rm_uses = RmUse::where('rm_uses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('rm_uses.date', [$start, $end])->join('rm_use_items', 'rm_use_items.rm_use_id', '=', 'rm_uses.id')->join('raw_materials', 'raw_materials.id', '=', 'rm_use_items.raw_material_id')->get([
            \DB::raw('name as name'),
            \DB::raw('basic_uom as basic_uom'),
            \DB::raw('quantity as quantity'),
            \DB::raw('total_cost as total'),
            \DB::raw('rm_uses.date')
        ]);

        $rm_uses_group = RmUse::where('rm_uses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('rm_uses.date', [$start, $end])->join('rm_use_items', 'rm_use_items.rm_use_id', '=', 'rm_uses.id')->join('raw_materials', 'raw_materials.id', '=', 'rm_use_items.raw_material_id')->get([
            \DB::raw('name as name'),
            \DB::raw('basic_uom as basic_uom'),
            \DB::raw('SUM(quantity) as quantity')
        ]);

        $total_rm = RmUse::where('rm_uses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('rm_uses.date', [$start, $end])->sum('total_cost');
            $total_pm = PmUse::where('pm_uses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pm_uses.date', [$start, $end])->sum('total_cost');

        $total_dlc = DirectLabourCost::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('date', [$start, $end])->sum('total_cost');

        $pm_uses = PmUse::where('pm_uses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pm_uses.date', [$start, $end])->join('pm_use_items', 'pm_use_items.pm_use_id', '=', 'pm_uses.id')->join('packing_materials', 'packing_materials.id', '=', 'pm_use_items.packing_material_id')->get([
            \DB::raw('name as name'),
            \DB::raw('basic_uom as basic_uom'),
            \DB::raw('quantity as quantity'),
            \DB::raw('total_cost as total'),
            \DB::raw('pm_uses.date')
        ]);

        $products = ProductionCost::where('production_costs.shop_id', $shop->id)->where('production_costs.is_deleted', false)->whereBetween('production_costs.date', [$start, $end])->join('production_cost_items', 'production_cost_items.id', '=', 'production_costs.id')->join('products', 'products.id', '=', 'production_cost_items.product_id')->groupBy('production_cost_items.product_id')->get([
            \DB::raw('name as name'),
            \DB::raw('basic_uom as basic_uom'),
            \DB::raw('SUM(quantity) as quantity'),
        ]);
                

        $pm_stocks = $shop->packingMaterials()->get();
        $rm_stocks = $shop->rawMaterials()->get();
            

        foreach($rm_stocks as $key => $rm_stock ){
            array_push($rm_stock_labels, ($rm_stock->name).' ('.$rm_stock->basic_uom.')');
            array_push($rm_stock_data, $rm_stock->pivot->in_store);
        }

        foreach($pm_stocks as $key => $pm_stock ){
            array_push($pm_stock_labels, ($pm_stock->name).' ('.$pm_stock->basic_uom.')');
            array_push($pm_stock_data, $pm_stock->pivot->in_store);
        }

        $rm_use_set= collect([]);
        foreach ($rm_uses as $key => $rm_use) {
            array_push($rm_labels, ($rm_use->name).' ('.$rm_use->basic_uom.')');
            array_push($rm_uses_data, round($rm_use->quantity));

            $v = collect([
                'name' => ($rm_use->name).' ('.$rm_use->basic_uom.')',
                'qty' => round($rm_use->quantity),
                'date' => $rm_use->date ]);
             $rm_use_set->push($v);   
        }

        $rm_use_date = $rm_use_set->pluck('date');
        $rm_use_set = $rm_use_set->groupby('name');

        $rm_use_series = collect([]);
        $rm_use_lable = [];
        $rm_use_qty = [];
        foreach($rm_use_set as $key => $value){
                
            $u = ([
                'name' => $value->pluck('name')->unique(),
                'data' => $value->pluck('qty'),
                'date' => $value->pluck('date')
            ]);
            $rm_use_series->push($u);
            array_push($rm_use_lable, $value->pluck('name')->first());
            array_push($rm_use_qty, $value->sum('qty'));
        }

        $pm_use_set =collect([]);
        foreach ($pm_uses as $key => $pm_use) {

            array_push($pm_labels, ($pm_use->name).' ('.$pm_use->basic_uom.')');
            array_push($pm_uses_data, round($pm_use->quantity));

            $p = collect([
                'name' => ($pm_use->name).' ('.$pm_use->basic_uom.')',
                'qty' => round($pm_use->quantity),
                'date' => $pm_use->date ]);
             $pm_use_set->push($p); 
        }

        $pm_use_date = $pm_use_set->pluck('date');
        $pm_use_set = $pm_use_set->groupby('name');

        $pm_use_series = collect([]);
        foreach($pm_use_set as $key => $value){
                
            $u = ([
                'name' => $value->pluck('name')->unique(),
                'data' => $value->pluck('qty'),
                'date' => $value->pluck('date')
            ]);
 
            $pm_use_series->push($u);
        }


        $products_made = [];
        foreach($products as $key => $product){

            array_push($product_lables, ($product->name).' ('.$product->basic_uom.')');
            array_push($product_qty, round($product->quantity));

            $prod = collect([
                'name' => $product->name.'('.$product->basic_uom.')',
                'y' => round($product->quantity),
                'color' => 'Highcharts.getOptions().colors['.$key.']'
            ]);

            array_push($products_made, $prod);
        }

        $start = date('d-m-Y', strtotime($start));
        $end = date('d-m-Y', strtotime($end));
          
        return view('production.dashboard', compact('page', 'title', 'title_sw', 'start_date', 'end_date', 'is_post_query', 'settings', 'currency', 'shop', 'start', 'end' , 'total_rm', 'total_pm','total_dlc', 'pm_stock_labels','pm_stock_data', 'rm_stock_labels', 'rm_stock_data', 'rm_labels', 'rm_uses_data', 'pm_labels', 'pm_uses_data', 'rm_use_set','pm_use_set', 'product_qty', 'product_lables',  'products_made', 'products', 'rm_use_series', 'rm_use_date','pm_use_series', 'pm_use_date', 'rm_use_lable', 'rm_use_qty'));
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
        //
    }
}
