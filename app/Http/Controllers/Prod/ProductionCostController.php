<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use Response;
use Session;

use Auth;
use Log;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\MroItem;
use App\Models\MroUse;
use App\Models\MroUsedItemTemp;

use App\Models\DlcItemTemp;
use App\Models\DlcItem;
use App\Models\DirectLabourCost;

use App\Models\RawMaterial;
use App\Models\RmUse;
use App\Models\RmItem;
use App\Models\RmUseItem;
use App\Models\RmUseItemTemp;
use App\Models\RmDamage;

use App\Models\PackingMaterial;
use App\Models\PmUse;
use App\Models\PmUseItem;
use App\Models\PmItem;
use App\Models\PmUseItemTemp;
use App\Models\PmDamage;

use App\Models\ProductionCost;
use App\Models\ProductionCostItem;
use App\Models\Payment;

use App\Models\Product;
use App\Models\ProductMadeApiTemp;
use App\Jobs\PMUpdaterJob;
use App\Models\ProductionStage;

class ProductionCostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Production Records';
        $title = 'Production Records';
        $title_sw = 'Taarifa za Uzalishaji';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = Carbon::now();
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

        $shop = Shop::find(Session::get('shop_id'));
        $prod_records = ProductionCost::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('date', [$start , $end])->get(); 

         return view('production.index', compact('page', 'title', 'title_sw', 'prod_records', 'start', 'end', 'is_post_query', 'start_date', 'end_date'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $page = 'Production Section';
        $title = 'Production Section';
        $title_sw = 'Sehemu Uzalishaji';
        $prod_record = ProductionCost::where('shop_id', $shop->id)->max('prod_batch');

        $prod_batch = $prod_record+1;

        $mros = $shop->mro()->where('is_deleted', false)->get();
        $pms = $shop->packingMaterials()->where('is_deleted', false)->get();
        $rms = $shop->rawMaterials()->where('is_deleted', false)->get();

        return view('production.create', compact('page', 'title', 'title_sw', 'shop', 'settings', 'mros',  'pms', 'rms', 'prod_batch'));
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
        $now = Carbon::now();
        if (!empty($request['date'])) {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $now = $request['date'] . ' ' . $time;
        }

        $prod_cost = new ProductionCost();
        $prod_cost->user_id = $user->id;
        $prod_cost->shop_id = $shop->id;
        $prod_cost->total_prod_qty =  1;
        $prod_cost->total_cost = 0; 
        $prod_cost->prod_batch = $request['prod_batch'];
        $prod_cost->date = $now;
        $prod_cost->remarks = $request['remarks'];
        $prod_cost->save();

        $pm = PmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        $rm = RmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        $dlc = DlcItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get(); 
        $mrotemps = MroUsedItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();

        $product_made = ProductMadeApiTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();

        $pmutotal = 0;
        if (!is_null($pm)) {
            $pmuse = new PmUse();
            $pmuse->shop_id = $shop->id;
            $pmuse->user_id = $user->id;
            $pmuse->production_cost_id = $prod_cost->id;
            $pmuse->total_cost = $pm->sum('total');
            $pmuse->date = $now;
            $pmuse->prod_batch = $prod_cost->prod_batch;
            $pmuse->save();

            foreach ($pm as $key => $item) {
                $packing_material = PackingMaterial::find($item->packing_material_id);
                $useditem  = new PmUseItem;
                $useditem->pm_use_id = $pmuse->id;
                $useditem->shop_id = $shop->id;
                $useditem->user_id = $user->id;
                $useditem->packing_material_id = $packing_material->id;
                $useditem->quantity = $item->quantity;
                $useditem->unit_cost = $item->unit_cost;
                $useditem->total = $item->total;
                $useditem->unit_packed = $item->unit_packed;
                $useditem->date = $now;
                $useditem->product_packed = $item->product_packed;
                $useditem->save();

                $this->addSubPackingMaterial($item, $pmuse, $user, $shop, $now);
                $item->delete();

                dispatch(new PMUpdaterJob($packing_material->id, $shop));

                $shop_packing_material = $shop->packingMaterials()->where('packing_material_id', $packing_material->id)->where('is_deleted', false)->first();
                
                $apmitems = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->get();
                $uqty = $item->quantity;
                foreach ($apmitems as $key => $pmitem) {
                    Log::info('Unit cost '.$pmitem->unit_cost);
                    $remqty = ($pmitem->qty-$pmitem->qty_out);
                    if ($uqty <= $remqty) {
                        $pmitem->qty_out = $pmitem->qty_out+$uqty;
                        if ($pmitem->qty == $pmitem->qty_out) {
                            $pmitem->is_utilized = true;
                        }else{
                            $shop_packing_material->pivot->unit_cost = $pmitem->unit_cost;
                        }
                        $pmitem->save();
                    }else{
                        $pmitem->qty_out = $pmitem->qty_out+$remqty;
                        if ($pmitem->qty == $pmitem->qty_out) {
                            $pmitem->is_utilized = true;
                        }else{
                            $shop_packing_material->pivot->unit_cost = $pmitem->unit_cost;
                        }
                        $pmitem->save();
                    }
                    $uqty -= $remqty;
                }
                $shop_packing_material->pivot->save();
            }

            $pmutotal = PmUseItem::where('pm_use_id', $pmuse->id)->sum('total');
            $pmuse->total_cost = $pmutotal;
            $pmuse->save();
        }

        if (!is_null($rm)) {
            $rmuse = new RmUse();
            $rmuse->shop_id = $shop->id;
            $rmuse->user_id = $user->id;
            $rmuse->production_cost_id = $prod_cost->id;
            $rmuse->total_cost = $rm->sum('total');
            $rmuse->date = $now;
            $rmuse->prod_batch = $prod_cost->prod_batch;
            $rmuse->save();

            foreach ($rm as $key => $item){ 
                $raw_material = RawMaterial::find($item->raw_material_id);
                $useditem  = new RmUseItem;
                $useditem->rm_use_id = $rmuse->id;
                $useditem->raw_material_id = $raw_material->id;
                $useditem->shop_id = $shop->id;
                $useditem->quantity = $item->quantity;
                $useditem->unit_cost = $item->unit_cost;
                $useditem->total = $item->total;
                $useditem->date = $now;
                $useditem->save();

                $item->delete();

                $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted', false)->first();

                $purchased = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('qty');
                $used = RmUseItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
                $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
                                    
                $instore = $purchased-($damaged +$used); 
                                 
                $shop_raw_material->pivot->in_store = $instore;
                $armitems = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->get();
                $uqty = $item->quantity;
                foreach ($armitems as $key => $rmitem) {
                    $remqty = ($rmitem->qty-$rmitem->qty_out);
                    if ($uqty <= $remqty) {
                        $rmitem->qty_out = $rmitem->qty_out+$uqty;
                        if ($rmitem->qty == $rmitem->qty_out) {
                            $rmitem->is_utilized = true;
                        }else{
                            $shop_raw_material->pivot->unit_cost = $rmitem->unit_cost;
                        }
                        $rmitem->save();
                    }else{
                        $rmitem->qty_out = $rmitem->qty_out+$remqty;
                        if ($rmitem->qty == $rmitem->qty_out) {
                            $rmitem->is_utilized = true;
                        }else{
                            $shop_raw_material->pivot->unit_cost = $rmitem->unit_cost;
                        }
                        $rmitem->save();
                    }
                    $uqty -= $remqty;
                }
                $shop_raw_material->pivot->save();
            }
        }

        if (!is_null($mrotemps)) {
            $mrouse = new MroUse();
            $mrouse->shop_id = $shop->id;
            $mrouse->user_id = $user->id;
            $mrouse->production_cost_id = $prod_cost->id;
            $mrouse->total_cost = $mrotemps->sum('total');
            $mrouse->date = $now;
            $mrouse->prod_batch = $prod_cost->prod_batch;
            $mrouse->save();

            foreach ($mrotemps as $key => $item) {
                $mro = MroItem::create([
                    'mro_use_id' => $mrouse->id,
                    'mro_id' => $item->mro_id,
                    'shop_id' => $shop->id,
                    'qty' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'total' => $item->total,
                    'date' => $now,
                ]);

                $item->delete();
            }
        }

        if (!is_null($dlc)) {
            $lcost = new DirectLabourCost();
            $lcost->shop_id = $shop->id;
            $lcost->user_id = $user->id;
            $lcost->production_cost_id = $prod_cost->id;
            $lcost->total_cost = $dlc->sum('total');
            $lcost->date = $now;
            $lcost->prod_batch = $prod_cost->prod_batch;
            $lcost->save();

            foreach ($dlc as $key => $item) {
                $lci = new DlcItem;
                $lci->shop_id = $shop->id;
                $lci->direct_labour_cost_id = $lcost->id;
                $lci->production_stage_id = $item->production_stage_id;
                $lci->qty = $item->quantity;
                $lci->unit_cost = $item->unit_cost;
                $lci->total = $item->total;
                $lci->date = $now;
                $lci->save();

                $item->delete();
            }
        }

        Log::info('PM cost :  .'.$pmutotal.' RM cost : '.$rm->sum('total').' DLC cost : '.$dlc->sum('total').' MOH Cost : '.$mrotemps->sum('total'));
        $total_cost = $pmutotal+$rm->sum('total')+$dlc->sum('total')+$mrotemps->sum('total');
        Log::info('TOTAL COST '.$total_cost);
        if ($total_cost > 0) { 
            if(!is_null($product_made)){
                $prod_cost->total_prod_qty =  $product_made->sum('qty');
                foreach ($product_made as $key => $value) {
                    $prod_cost_item = new ProductionCostItem();
                    $prod_cost_item->product_id = $value->product_id;
                    $prod_cost_item->packing_material_id = $value->packing_material_id;
                    $prod_cost_item->unit_packed = $value->unit_packed;
                    $prod_cost_item->quantity = $value->qty;
                    $prod_cost_item->cost_per_unit = $value->cost_per_unit;
                    $prod_cost_item->profit_margin = $value->profit_margin;
                    $prod_cost_item->selling_price = $value->selling_price;
                    $prod_cost_item->production_cost_id = $prod_cost->id;
                    $prod_cost_item->is_by_product = $value->is_by_product;
                    $prod_cost_item->save();

                    $value->delete();
                }
            }

            $prod_cost_items = ProductionCostItem::where('production_cost_id', $prod_cost->id)->get();
            $total_vol = 0;
            $total_qty = 0;
            foreach ($prod_cost_items as $key => $pmade) {
                if (!$pmade->is_by_product) {
                    $total_vol += $pmade->quantity*$pmade->unit_packed;
                    $total_qty += $pmade->quantity;
                }
            }

            Log::info('TOTAL VOLUME : '.$total_vol);
            if ($total_vol > 0) {
                foreach ($prod_cost_items as $key => $value) {
                    if (!$value->is_by_product) {
                        $percent = round((($value->quantity*$value->unit_packed)/$total_vol)*100, 2);
                        $value->cost_per_unit = (($percent/100)*$total_cost)/$value->quantity;
                        $value->save();
                    }else{
                        $value->cost_per_unit = 0;
                        $value->save();
                    }
                }
            }

            $prod_cost->total_cost = $total_cost;
            $prod_cost->total_prod_qty = $total_qty;
            $prod_cost->total_vol = $total_vol;
            $prod_cost->save();
            
        }

        return redirect()->back()->with('success', 'Production Record created successfully'); 
    }

    public function addSubPackingMaterial($item, $pmuse, $user, $shop, $now)
    {
        $packing_material = PackingMaterial::where('parent_pm_id', $item->packing_material_id)->first();
        if (!is_null($packing_material)) {
            $shop_packing_material = $shop->packingMaterials()->where('packing_material_id', $packing_material->id)->where('is_deleted', false)->first();
            $useditem  = new PmUseItem;
            $useditem->pm_use_id = $pmuse->id;
            $useditem->user_id = $user->id;
            $useditem->packing_material_id = $packing_material->id;
            $useditem->shop_id = $shop->id;
            $useditem->quantity = $item->quantity;
            $useditem->unit_cost = is_null($shop_packing_material->pivot->unit_cost) ? 0 :$shop_packing_material->pivot->unit_cost ;;
            $useditem->total = $useditem->unit_cost*$useditem->quantity;
            $useditem->date = $now;
            $useditem->product_packed = $item->product_packed;
            $useditem->save();

            $purchased = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('qty');
            $used = PmUseItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');
            $damaged = PmDamage::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');
                                
            $instore = $purchased-($used+$damaged); 
                                 
            $shop_packing_material->pivot->in_store = $instore;
                
            $apmitems = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->get();
            $uqty = $item->quantity;
            foreach ($apmitems as $key => $pmitem) {
                Log::info('Unit cost '.$pmitem->unit_cost);
                $remqty = ($pmitem->qty-$pmitem->qty_out);
                if ($uqty <= $remqty) {
                    $pmitem->qty_out = $pmitem->qty_out+$uqty;
                    if ($pmitem->qty == $pmitem->qty_out) {
                        $pmitem->is_utilized = true;
                    }else{
                        $shop_packing_material->pivot->unit_cost = $pmitem->unit_cost;
                    }
                    $pmitem->save();
                }else{
                    $pmitem->qty_out = $pmitem->qty_out+$remqty;
                    if ($pmitem->qty == $pmitem->qty_out) {
                        $pmitem->is_utilized = true;
                    }else{
                        $shop_packing_material->pivot->unit_cost = $pmitem->unit_cost;
                    }
                    $pmitem->save();
                }
                $uqty -= $remqty;
            }
            $shop_packing_material->pivot->save();
        }
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
        $page = 'Production';
        $title = 'Production Details';
        $title_sw = 'Harifa za Uzalishaji';
        $prod_cost = ProductionCost::find(decrypt($id));

        $total_vol = 0;
        $prod_cost_items = ProductionCostItem::where('production_cost_id', $prod_cost->id)->join('products', 'products.id', '=', 'production_cost_items.product_id')->get();
        foreach ($prod_cost_items as $key => $value) {
            $total_vol += $value->quantity*$value->unit_packed;
        }

        $mrouse = MroUse::where('shop_id', $shop->id)->where('prod_batch', $prod_cost->prod_batch)->where('is_deleted', false)->first();
        $mros = null;
        if (!is_null($mrouse)) {
            $mros = MroItem::where('mro_use_id', $mrouse->id)->where('mro_items.is_deleted', false)->join('mros', 'mros.id', '=', 'mro_items.mro_id')->get();
        }

        $rmuse = RmUse::where('shop_id', $shop->id)->where('prod_batch', $prod_cost->prod_batch)->first();
        $rms = null;
        if (!is_null($rmuse)) {
            $rms = RmUseItem::where('rm_use_id', $rmuse->id)->join('raw_materials', 'raw_materials.id', '=', 'rm_use_items.raw_material_id')->select('rm_use_items.id as id', 'rm_use_items.quantity as quantity', 'rm_use_items.unit_cost as unit_cost', 'rm_use_items.total as total', 'rm_use_items.date as date', 'raw_materials.name as name', 'raw_materials.basic_uom as basic_uom')->get();
        }


        $dlc = DirectLabourCost::where('shop_id', $shop->id)->where('prod_batch', $prod_cost->prod_batch)->where('is_deleted', false)->first();
        $dlcitems = null;
        if (!is_null($dlc)) {
            $dlcitems = DlcItem::where('direct_labour_cost_id', $dlc->id)->join('production_stages', 'production_stages.id', '=', 'dlc_items.production_stage_id')->get();
        }

        $pmuse = PmUse::where('shop_id', $shop->id)->where('prod_batch', $prod_cost->prod_batch)->where('is_deleted', false)->first();
        $pms = null;
        if (!is_null($pmuse)) {
            $pms = PmUseItem::where('pm_use_id', $pmuse->id)->join('packing_materials', 'packing_materials.id', '=', 'pm_use_items.packing_material_id')->select('pm_use_items.id as id', 'pm_use_items.quantity as quantity', 'pm_use_items.unit_cost as unit_cost', 'pm_use_items.total as total', 'pm_use_items.date as date', 'packing_materials.name as name', 'packing_materials.basic_uom as package_unit', 'pm_use_items.unit_packed as unit_packed', 'pm_use_items.packing_material_id as packing_material_id')->get();

        }

        return view('production.show', compact(['page', 'title', 'title_sw', 'prod_cost', 'prod_cost_items', 'total_vol', 'mrouse', 'pmuse', 'rmuse', 'dlc', 'mros', 'pms', 'rms', 'dlcitems']));
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
        $page = 'Edit Production';
        $title = 'Edit Production';
        $title_sw = 'Hariri Uzalishaji';
        $prod_cost = ProductionCost::find(decrypt($id));

        $total_vol = 0;
        $prod_cost_items = ProductionCostItem::where('production_cost_id', $prod_cost->id)->join('products', 'products.id', '=', 'production_cost_items.product_id')->select('production_cost_items.id as id', 'name', 'quantity', 'unit_packed', 'cost_per_unit')->get();
        foreach ($prod_cost_items as $key => $value) {
            $total_vol += $value->quantity*$value->unit_packed;
        }

        $mrouse = MroUse::where('shop_id', $shop->id)->where('prod_batch', $prod_cost->prod_batch)->where('is_deleted', false)->first();
        $mros = null;
        if (!is_null($mrouse)) {
            if (is_null($mrouse->production_cost_id)) {
                $mrouse->production_cost_id = $prod_cost->id;
                $mrouse->save();
            }
            $mros = MroItem::where('mro_use_id', $mrouse->id)->where('mro_items.is_deleted', false)->join('mros', 'mros.id', '=', 'mro_items.mro_id')->get();
        }

        $rmuse = RmUse::where('shop_id', $shop->id)->where('prod_batch', $prod_cost->prod_batch)->first();
        $rms = null;
        if (!is_null($rmuse)) {
            if (is_null($rmuse->production_cost_id)) {
                $rmuse->production_cost_id = $prod_cost->id;
                $rmuse->save();
            }
            $rms = RmUseItem::where('rm_use_id', $rmuse->id)->join('raw_materials', 'raw_materials.id', '=', 'rm_use_items.raw_material_id')->select('rm_use_items.id as id', 'rm_use_items.quantity as quantity', 'rm_use_items.unit_cost as unit_cost', 'rm_use_items.total as total', 'rm_use_items.date as date', 'raw_materials.name as name', 'raw_materials.basic_uom as basic_uom')->get();
        }


        $dlc = DirectLabourCost::where('shop_id', $shop->id)->where('prod_batch', $prod_cost->prod_batch)->where('is_deleted', false)->first();
        $dlitems = null;
        if (!is_null($dlc)) {
            if (is_null($dlc->production_cost_id)) {
                $dlc->production_cost_id = $prod_cost->id;
                $dlc->save();
            }

            $dlcitems = DlcItem::where('direct_labour_cost_id', $dlc->id)->join('production_stages', 'production_stages.id', '=', 'dlc_items.production_stage_id')->select('dlc_items.id as id', 'stage', 'qty', 'unit_cost', 'total')->get();
        }

        $pmuse = PmUse::where('shop_id', $shop->id)->where('prod_batch', $prod_cost->prod_batch)->where('is_deleted', false)->first();
        $pms = null;
        if (!is_null($pmuse)) {
            if (is_null($pmuse->production_cost_id)) {
                $pmuse->production_cost_id = $prod_cost->id;
                $pmuse->save();
            }
            $pms = PmUseItem::where('pm_use_id', $pmuse->id)->join('packing_materials', 'packing_materials.id', '=', 'pm_use_items.packing_material_id')->select('pm_use_items.id as id', 'pm_use_items.quantity as quantity', 'pm_use_items.unit_cost as unit_cost', 'pm_use_items.total as total', 'pm_use_items.date as date', 'packing_materials.name as name', 'packing_materials.basic_uom as package_unit', 'pm_use_items.unit_packed as unit_packed', 'pm_use_items.packing_material_id as packing_material_id')->get();
        }


        $mohs = $shop->mro()->where('is_deleted' , false)->get([
            \DB::raw('id'),
            \DB::raw('name'),]);
        $pmaterials = $shop->packingMaterials()->whereNull('parent_pm_id')->where('is_deleted' , false)->get([
            \DB::raw('packing_material_id as id'),
            \DB::raw('name'),
            \DB::raw('in_store'),
            \DB::raw('unit_cost'),
            \DB::raw('description')]);
        $rmaterials = $shop->rawMaterials()->where('is_deleted' , false)->get([
            \DB::raw('raw_material_id as id'),
            \DB::raw('name'),
            \DB::raw('in_store'),
            \DB::raw('unit_cost'),
            \DB::raw('description')]);
        $stages = ProductionStage::where('shop_id', $shop->id)->select('id', 'stage')->get();
        $products = $shop->products()->get([
            \DB::raw('product_id as id'),
            \DB::raw('product_code'),
            \DB::raw('barcode'),
            \DB::raw('name')]);

        return view('production.edit', compact(['page', 'title', 'title_sw', 'prod_cost', 'prod_cost_items', 'total_vol', 'mrouse', 'pmuse', 'rmuse', 'dlc', 'mros', 'pms', 'rms', 'dlcitems', 'mohs', 'pmaterials', 'rmaterials', 'stages', 'products']));
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
        $prod_cost = ProductionCost::find(decrypt($id));
        $shop = Shop::find($prod_cost->shop_id);

        $date = $prod_cost->date;
        if ($request['date'] != $date) {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $date = $request['date'] . ' ' . $time;
        }
        $totalrm = 0; $totaldlc = 0; $totalpm = 0; $totalmro = 0;
        $rmuse = RmUse::where('shop_id', $shop->id)->where('prod_batch', $prod_cost->prod_batch)->where('is_deleted', false)->first();
        if (!is_null($rmuse)) {
            $totalrm = $rmuse->total_cost;
        }
        $mrouse = MroUse::where('shop_id', $shop->id)->where('prod_batch', $prod_cost->prod_batch)->where('is_deleted', false)->first();
        if (!is_null($mrouse)) {
            $totalmro = $mrouse->total_cost;
        }
        $dlc = DirectLabourCost::where('shop_id', $shop->id)->where('prod_batch', $prod_cost->prod_batch)->where('is_deleted', false)->first();
        if (!is_null($dlc)) {
            $totaldlc = $dlc->total_cost;
        }
        $pmuse = PmUse::where('shop_id', $shop->id)->where('prod_batch', $prod_cost->prod_batch)->where('is_deleted', false)->first();

        if (!is_null($pmuse)) {
            $totalpm = $pmuse->total_cost;
        }

        $total_cost = $totalrm+$totalpm+$totalmro+$totaldlc;
        
        $prod_cost_items = ProductionCostItem::where('production_cost_id', $prod_cost->id)->get();
        $total_vol = 0;
        $total_qty = 0;
        foreach ($prod_cost_items as $key => $pmade) {
            if (!$pmade->is_by_product) {
                $total_vol += $pmade->quantity*$pmade->unit_packed;
                $total_qty += $pmade->quantity;
            }
        }

        // Log::info('TOTAL VOLUME : '.$total_vol);
        if ($total_vol > 0) {
            foreach ($prod_cost_items as $key => $value) {
                if (!$value->is_by_product) {
                    $percent = round((($value->quantity*$value->unit_packed)/$total_vol)*100, 2);
                    // Log::info($percent);
                    $value->cost_per_unit = (($percent/100)*$total_cost)/$value->quantity;
                    $value->save();
                }else{
                    $value->cost_per_unit = 0;
                    $value->save();
                }
            }
        }


        $prod_cost->date = $date;
        $prod_cost->remarks = $request['remarks'];
        $prod_cost->total_cost = $total_cost;
        $prod_cost->total_prod_qty = $total_qty;
        $prod_cost->total_vol = $total_vol;
        $prod_cost->save();
        return redirect()->route('prod-costs.show', encrypt($prod_cost->id))->with('success', 'Production Records updated successfully');
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
        $prod_record = ProductionCost::find(decrypt($id));
        if(!$prod_record->is_transferred){
            $prod_items = ProductionCostItem::where('production_cost_id', $prod_record->id)->get();
            foreach($prod_items as $prod_item){
                $prod_item->delete();
            }

            $mrouse = MroUse::where('shop_id', $shop->id)->where('prod_batch', $prod_record->prod_batch)->first();
            if (!is_null($mrouse)) {
                $mros = MroItem::where('mro_use_id', $mrouse->id)->get();
                foreach ($mros as $key => $value) {
                    $value->delete();
                }

                $mrouse->delete();
            }

            $rmuse = RmUse::where('shop_id', $shop->id)->where('prod_batch', $prod_record->prod_batch)->first();
            if (!is_null($rmuse)) {
                $rms = RmUseItem::where('rm_use_id', $rmuse->id)->get();
                foreach ($rms as $key => $value) {
                    $value->delete();
                }
                $rmuse->delete();
            }

            $dlc = DirectLabourCost::where('shop_id', $shop->id)->where('prod_batch', $prod_record->prod_batch)->first();
            if (!is_null($dlc)) {
                $dlcitems = DlcItem::where('direct_labour_cost_id', $dlc->id)->get();
                foreach ($dlcitems as $key => $value) {
                    $value->delete();
                }

                $dlc->delete();
            }

            $pmuse = PmUse::where('shop_id', $shop->id)->where('prod_batch', $prod_record->prod_batch)->first();
            if (!is_null($pmuse)) {
                $pms = PmUseItem::where('pm_use_id', $pmuse->id)->get();
                foreach ($pms as $key => $value) {
                    $value->delete();
                }

                $pmuse->delete();
            }

            $prod_record->delete();

            return redirect()->back()->with('success', "Production Records Deleted Successful");
        }else{
              return redirect()->back()->with('error', " Fail to Delete ,Production Records Is Already Transferred ");
        }
    }

    public function cancelProduction()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        
        $pm = PmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        foreach ($pm as $key => $value) {
            $value->delete();
        }
        $rm = RmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        foreach ($rm as $key => $value) {
            $value->delete();
        }
        $mro = MroUsedItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        foreach ($mro as $key => $value) {
            $value->delete();
        }
        $dlc = DlcItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        foreach ($dlc as $key => $value) {
            $value->delete();
        }
        $product_made = ProductMadeApiTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        foreach ($product_made as $key => $value) {
            $value->delete();
        }

        return redirect()->route('prod-costs.create')->with('success', 'Production Records Cancelled successful');
    }
}