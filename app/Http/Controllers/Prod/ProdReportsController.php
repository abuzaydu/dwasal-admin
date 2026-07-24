<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\RawMaterial;
use App\Models\PackingMaterial;
use App\Models\RmItem;
use App\Models\PmItem;
use App\Models\RmUseItem;
use App\Models\PmUseItem;
use App\Models\RmUse;
use App\Models\PmUse;
use App\Models\PmDamage;
use App\Models\RmDamage;
use App\Models\ProductionCost;
use App\Models\ProductionCostItem;
use App\Models\MroItem;
use App\Models\DlcItem;
use Log;

class ProdReportsController extends Controller
{
      public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function PmPurchases(Request $request){
        $shop = Shop::find(Session::get('shop_id'));
        $packing_materials = $shop->packingMaterials()->where('is_deleted', false)->get();

        $now = Carbon::now();
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));            
        $end_date = date('Y-m-d', strtotime($end));
  
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['stock_date'])) {
            $start_date = $request['stock_date'];
            $end_date = $request['stock_date'];
            $start = $request['stock_date'].' 00:00:00';
            $end = $request['stock_date'].' 23:59:59';
            $is_post_query = true;
        }else if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        
        $packing_material = null;

        if (!empty($request['pm_id'])) {

            $packing_material = PackingMaterial::find($request['pm_id']);
            $pm_stocks = PmItem::where('pm_items.shop_id', $shop->id)->where('pm_items.is_deleted', false)->where('packing_material_id', $packing_material->id)->whereBetween('pm_items.date', [$start, $end])->join('packing_materials', 'packing_materials.id', '=', 'pm_items.packing_material_id')->join('pm_purchases', 'pm_purchases.id', '=', 'pm_items.pm_purchase_id')->leftJoin('suppliers',  'suppliers.id', '=', 'pm_purchases.supplier_id')->orderBy('pm_items.date', 'desc')->get([
                'packing_materials.name', 
                'suppliers.name as sp_name',
                'total',
                'qty',
                'unit_cost',
                'pm_purchase_id',
                'purchase_type',
                'pm_items.date',
             ]);
 
        }else{

            $pm_stocks = PmItem::where('pm_items.shop_id', $shop->id)->where('pm_items.is_deleted', false)->whereBetween('pm_items.date', [$start, $end])->join('packing_materials', 'packing_materials.id', '=', 'pm_items.packing_material_id')->join('pm_purchases', 'pm_purchases.id', '=', 'pm_items.pm_purchase_id')->leftJoin('suppliers',  'suppliers.id', '=', 'pm_purchases.supplier_id')->orderBy('pm_items.date', 'desc')->get([
                'packing_materials.name', 
                'suppliers.name as sp_name',
                'total',
                'qty',
                'unit_cost',
                'pm_purchase_id',
                'purchase_type',
                'pm_items.date',
             ]);
        }


        $total_buying_pm = 0;
        

        foreach ($pm_stocks as $key => $pm_stock) {
            $total_buying_pm += $pm_stock->total;
        }
      
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Reports';
        $title = 'Packaging Materials Purchasing Report';
        $title_sw = 'Ripoti ya Manunuzi Vifungashio';
        return view('production.reports.pm-purchases', compact('page', 'title', 'title_sw',  'pm_stocks', 'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date',  'reporttime', 'shop', 'total_buying_pm',  'packing_materials',  'packing_material'));
    }

    public function RmPurchases(Request $request){
        $shop = Shop::find(Session::get('shop_id'));

        $raw_materials = $shop->rawMaterials()->where('is_deleted', false)->get();

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['stock_date'])) {
            $start_date = $request['stock_date'];
            $end_date = $request['stock_date'];
            $start = $request['stock_date'].' 00:00:00';
            $end = $request['stock_date'].' 23:59:59';
            $is_post_query = true;
        }else if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        
        $raw_material = null;

        if (!empty($request['rm_id'])) {
            $raw_material = RawMaterial::find($request['rm_id']);

            $rm_stocks = RmItem::where('rm_items.shop_id', $shop->id)->where('rm_items.is_deleted', false)->where('raw_material_id', $raw_material->id)->whereBetween('rm_items.date', [$start, $end])->join('raw_materials', 'raw_materials.id', '=', 'rm_items.raw_material_id')->join('rm_purchases', 'rm_purchases.id', '=', 'rm_items.rm_purchase_id')->leftJoin('suppliers',  'suppliers.id', '=', 'rm_purchases.supplier_id')->orderBy('rm_items.date', 'desc')->get([
                'raw_materials.name', 
                'suppliers.name as sp_name',
                'total',
                'qty',
                'unit_cost',
                'rm_purchase_id',
                'purchase_type',
                'rm_items.date',
             ]);

        }else{

          $rm_stocks = RmItem::where('rm_items.shop_id', $shop->id)->where('rm_items.is_deleted', false)->whereBetween('rm_items.date', [$start, $end])->join('raw_materials', 'raw_materials.id', '=', 'rm_items.raw_material_id')->join('rm_purchases', 'rm_purchases.id', '=', 'rm_items.rm_purchase_id')->leftJoin('suppliers',  'suppliers.id', '=', 'rm_purchases.supplier_id')->orderBy('rm_items.date', 'desc')->get([
                'raw_materials.name', 
                'suppliers.name as sp_name',
                'total',
                'qty',
                'unit_cost',
                'rm_purchase_id',
                'purchase_type',
                'rm_items.date',
             ]);
        }

        $total_buying_rm = 0;

        foreach ($rm_stocks as $key => $rm_stock) {
            $total_buying_rm += $rm_stock->total;
        }


      
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Reports';
        $title = 'Raw Materials Purchasing Report';
        $title_sw = 'Ripoti ya Manunuzi Malighafi';
        return view('production.reports.rm-purchases', compact('page', 'title', 'title_sw', 'rm_stocks',  'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date', 'total_buying_rm', 'reporttime', 'shop', 'raw_materials', 'raw_material' ));
    }

    public function StockStatus(){
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Stock Status Report';
        $title = 'Stock Status Report';
        $title_sw = 'Repoti ya Hali ya Stock';
        $shop = Shop::find(Session::get('shop_id'));
  

        $packing_materials = $shop->packingMaterials()->where('is_deleted', false)->get();
        $raw_materials = $shop->rawMaterials()->where('is_deleted', false)->get();


       
        $rm_stocks = RmItem::where('rm_items.shop_id', $shop->id)->where('is_deleted', false)->join('raw_materials', 'raw_materials.id', '=', 'rm_items.raw_material_id')->groupBy('rm_items.raw_material_id')->get([
            \DB::raw('name as name'),
            \DB::raw('SUM(qty) as total_qty'),
            \DB::raw('rm_items.unit_cost as unit_cost'),
            \DB::raw('raw_material_id as raw_material_id')
        ]);

        $pm_stocks = PmItem::where('pm_items.shop_id', $shop->id)->where('pm_items.is_deleted', false)->join('packing_materials', 'packing_materials.id', '=', 'pm_items.packing_material_id')->groupBy('pm_items.packing_material_id')->get([
            \DB::raw('name as name'),
            \DB::raw('unit_cost as unit_cost'),
            \DB::raw('SUM(qty) as total_qty'),
            \DB::raw('packing_material_id as packing_material_id')
        ]);

   
         $pm_status = [];
         $rm_status = [];
         $total_pm = 0;
         $total_rm = 0;
        foreach($packing_materials as $key => $value){
            
            $pm = collect(['name'=> $value->name, 'in_store' => ($value->pivot->in_store == null) ? 0 : $value->pivot->in_store, 'cost' => ($value->pivot->unit_cost == null) || ($value->pivot->in_store == null)  ? 0 :  ($value->pivot->unit_cost *$value->pivot->in_store) ]);
            $total_pm = $total_pm + ( ($value->pivot->unit_cost == null) || ($value->pivot->in_store == null)  ? 0 :  ($value->pivot->unit_cost *$value->pivot->in_store) );


            array_push($pm_status, $pm);
         }

         foreach($raw_materials as $value){
            $rm = collect(['name'=> $value->name, 'in_store' => ($value->pivot->in_store == null) ? 0 : $value->pivot->in_store ,'cost' => ($value->pivot->unit_cost == null)|| ($value->pivot->in_store == null)  ? 0 :  ($value->pivot->unit_cost *$value->pivot->in_store) ]);
            $total_rm = $total_rm +  (($value->pivot->unit_cost == null)|| ($value->pivot->in_store == null)  ? 0 :  ($value->pivot->unit_cost *$value->pivot->in_store));
            array_push($rm_status, $rm);
         }  


        return view('production.reports.stock-status-report', compact(['reporttime', 'page', 'title', 'title_sw' , 'shop', 'pm_status', 'rm_status', 'total_pm', 'total_rm' ]));
        
    }

     public function RmUsesReport(Request $request){

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Reports';
        $title = 'Raw Materials Uses Report';
        $title_sw = 'Ripoti ya Matumizi ya Malighafi';

        $shop = Shop::find(Session::get('shop_id'));

        $raw_materials = $shop->rawMaterials()->where('is_deleted', false)->get();

        $now = Carbon::now();
        $start = carbon::now()->startOfMonth();
        $end = carbon::now();
        $start_date = date('Y-m-d', strtotime($start));            
        $end_date = date('Y-m-d', strtotime($end));
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['stock_date'])) {
            $start_date = $request['stock_date'];
            $end_date = $request['stock_date'];
            $start = $request['stock_date'].' 00:00:00';
            $end = $request['stock_date'].' 23:59:59';
            $is_post_query = true;
        }else if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        $raw_material = null;

        if (!empty($request['rm_id'])){
            $raw_material = RawMaterial::find($request['rm_id']);
            $rm_uses = RmUse::where('rm_uses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('rm_uses.date', [$start, $end])->join('rm_use_items', 'rm_use_items.rm_use_id', '=', 'rm_uses.id')->join('raw_materials', 'raw_materials.id' ,'rm_use_items.raw_material_id')->where('raw_materials.id', $raw_material->id)->get();
            $total = $rm_uses->sum('total_cost'); 

        }else{

            $rm_uses = RmUse::where('rm_uses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('rm_uses.date', [$start, $end])->join('rm_use_items', 'rm_use_items.rm_use_id', '=', 'rm_uses.id')->join('raw_materials', 'raw_materials.id' ,'rm_use_items.raw_material_id')->get();
            $total = $rm_uses->sum('total_cost');
        }

       return view('production.reports.rm-uses', compact(['rm_uses', 'crtime', 'reporttime', 'page', 'title', 'title_sw', 'start_date', 'end_date', 'start', 'end', 'is_post_query', 'raw_material', 'raw_materials' , 'shop' ,'duration', 'duration_sw', 'total']));
    }

    public function PmUsesReport(Request $request){

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Reports';
        $title = 'Packaging Materials Uses Report';
        $title_sw = 'Ripoti ya Matumizi ya Vifungashio';

        $shop = Shop::find(Session::get('shop_id'));

        $packing_materials = $shop->packingMaterials()->where('is_deleted', false)->get();

        $now = Carbon::now();
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));            
        $end_date = date('Y-m-d', strtotime($end));
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['stock_date'])) {
            $start_date = $request['stock_date'];
            $end_date = $request['stock_date'];
            $start = $request['stock_date'].' 00:00:00';
            $end = $request['stock_date'].' 23:59:59';
            $is_post_query = true;
        }else if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        $packing_material = null;

        if (!empty($request['pm_id'])){
            $packing_material = PackingMaterial::find($request['pm_id']);
            $pm_uses = PmUse::where('pm_uses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pm_uses.date', [$start, $end])->join('pm_use_items', 'pm_use_items.pm_use_id', '=', 'pm_uses.id')->join('packing_materials', 'packing_materials.id' ,'pm_use_items.packing_material_id')->where('packing_materials.id', $packing_material->id)->get();

            $total = $pm_uses->sum('total_cost'); 

        }else{
           
            $pm_uses =  PmUse::where('pm_uses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pm_uses.date', [$start, $end])->join('pm_use_items', 'pm_use_items.pm_use_id', '=', 'pm_uses.id')->join('packing_materials', 'packing_materials.id' ,'pm_use_items.packing_material_id')->get();
            $total = $pm_uses->sum('total_cost');

        }

       return view('production.reports.pm-uses', compact(['pm_uses', 'crtime', 'reporttime', 'page', 'title', 'title_sw', 'start_date', 'end_date', 'start', 'end', 'is_post_query', 'packing_material', 'packing_materials' , 'shop' ,'duration', 'duration_sw', 'total']));
    }


    public function generalReport(Request $request){
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Reports';
        $title = 'General Production Report';
        $title_sw = 'Ripoti ya Jumla ya Uzalishaji';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
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

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';


        $packing_materials = $shop->packingMaterials()->where('is_deleted', false)->get();
        $raw_materials = $shop->rawMaterials()->where('is_deleted', false)->get();

        $raw_col = collect([]);
        $pack_col = collect([]);
        
        foreach($raw_materials as $raw){
            $rmu =  RmUse::where('rm_uses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('rm_uses.date', [$start, $end])->join('rm_use_items', 'rm_use_items.rm_use_id', '=', 'rm_uses.id')->join('raw_materials', 'raw_materials.id' ,'rm_use_items.raw_material_id')->where('raw_materials.name', $raw->name)->get();

          $rmp = RmItem::where('rm_items.shop_id', $shop->id)->where('rm_items.is_deleted', false)->whereBetween('rm_items.date', [$start, $end])->join('raw_materials', 'raw_materials.id', '=', 'rm_items.raw_material_id')->where('raw_materials.name', $raw->name)->get();

          $rmd = RmDamage::where('rm_damages.shop_id', $shop->id)->whereBetween('rm_damages.created_at', [$start, $end])->where('raw_material_id', $raw->id)->get();

          $v = collect(['name' => $raw->name, 'purchased_qty' => $rmp->sum('qty'), 'used_qty' => $rmu->sum('quantity'), 'rmu_cost' => $rmu->sum('total'), 'damaged' => $rmd->sum('quantity')]);
          $raw_col->push($v);
        }

        foreach($packing_materials as $pack){
          $pmu = RmUse::where('rm_uses.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('rm_uses.date', [$start, $end])->join('rm_use_items', 'rm_use_items.rm_use_id', '=', 'rm_uses.id')->join('raw_materials', 'raw_materials.id' ,'rm_use_items.raw_material_id')->where('name', $pack->name)->get();
          $pmp = PmItem::where('pm_items.shop_id', $shop->id)->where('pm_items.is_deleted', false)->whereBetween('pm_items.date', [$start, $end])->join('packing_materials', 'packing_materials.id', '=', 'pm_items.packing_material_id')->where('packing_materials.name', $pack->name)->get();
          $pmd = PmDamage::where('pm_damages.shop_id', $shop->id)->whereBetween('pm_damages.created_at', [$start, $end])->join('packing_materials', 'packing_materials.id', '=', 'pm_damages.packing_material_id')->where('name', $pack->name)->get();
          $V = collect(['name' => $pack->name, 'purchased_qty' => $pmp->sum('qty'), 'used_qty' => $pmu->sum('quantity'), 'pmu_cost' => $pmp->sum('total'), $pmd->sum('quantity'), 'damaged' => $pmd->sum('quantity')]);
          $pack_col->push($V);
        }

        $mros = MroItem::where('mro_items.shop_id', $shop->id)->where('mro_items.is_deleted', false)->whereBetween('date', [$start, $end])->join('mros', 'mros.id', '=', 'mro_items.mro_id')->orderBy('name')->groupBy('name')->get([
            \DB::raw('name'),
            \DB::raw('SUM(total) as total')
        ]);

        $dlcitems = DlcItem::where('dlc_items.shop_id', $shop->id)->whereBetween('date', [$start, $end])->join('production_stages', 'production_stages.id', '=', 'dlc_items.production_stage_id')->orderBy('stage')->groupBy('stage')->get([
            \DB::raw('stage'),
            \DB::raw('SUM(qty) as qty'),
            \DB::raw('SUM(total) as total')
        ]);

        $prod = ProductionCost::where('production_costs.is_deleted', false)->where('production_costs.shop_id', $shop->id)->whereBetween('production_costs.date', [$start, $end])->join('production_cost_items', 'production_cost_items.production_cost_id', '=', 'production_costs.id')->join('products' , 'production_cost_items.product_id', '=', 'products.id');

          
        $production_logs = $prod->orderBy('date', 'desc')->get();

        $production = $prod->groupBy('name')->get([
            \DB::raw('name'),
            \DB::raw('SUM(quantity) as quantity'),
        ]);
        
        return view('production.reports.index', compact(['crtime', 'reporttime', 'page', 'title', 'title_sw','shop', 'settings', 'start_date', 'end_date', 'start', 'end', 'is_post_query', 'duration', 'duration_sw', 'production', 'production_logs', 'pack_col', 'raw_col', 'mros', 'dlcitems']));
    }
}
