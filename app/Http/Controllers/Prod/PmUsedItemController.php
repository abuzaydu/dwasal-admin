<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\ProductionCost;
use App\Models\PmUseItem;
use App\Models\PmItem;
use App\Models\PmUse;
use App\Models\PackingMaterial;
use App\Models\Product;
use App\Models\ProductionCostItem;
use Log;

class PmUsedItemController extends Controller
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
    public function index()
    {
        //
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
        $prod_cost = ProductionCost::find($request['production_cost_id']);   
        $shop = Shop::find($prod_cost->shop_id);
        $user = User::find($prod_cost->user_id);
        $pm = $shop->packingMaterials()->where('packing_material_id' , $request['packing_material_id'])->first();
        if($pm->pivot->in_store < $request['quantity']){
            return redirect()->route('prod-costs.edit', encrypt($prod_cost->id))->with('error' , "Stock Available is less than edited amount");
        }else{
            $pmuse = PmUse::where('production_cost_id', $prod_cost->id)->first();
            if (is_null($pmuse)) {
                $pmuse = new PmUse();
                $pmuse->shop_id = $shop->id;
                $pmuse->user_id = $user->id;
                $pmuse->production_cost_id = $prod_cost->id;
                $pmuse->total_cost = 0;
                $pmuse->date = $prod_cost->date;
                $pmuse->prod_batch = $prod_cost->prod_batch;
                $pmuse->save();
            }

            $pmuseditem = PmUseItem::where('pm_use_id', $pmuse->id)->where('packing_material_id', $pm->pivot->packing_material_id)->first();
            if (is_null($pmuseditem)) {
                $pmuseditem  = new PmUseItem;
                $pmuseditem->pm_use_id = $pmuse->id;
                $pmuseditem->shop_id = $shop->id;
                $pmuseditem->user_id = $user->id;
                $pmuseditem->packing_material_id = $pm->pivot->packing_material_id;
                $pmuseditem->product_packed  = $request['produt_id'];
                $pmuseditem->unit_packed = $request['unit_packed'];
                $pmuseditem->quantity = $request['quantity'];
                $pmuseditem->unit_cost = $pm->pivot->unit_cost;
                $pmuseditem->total = $pmuseditem->quantity*$pmuseditem->unit_cost;
                $pmuseditem->save();
                 
                $pm->pivot->in_store = ($pm->pivot->in_store - $pmuseditem->quantity);
                $pm->pivot->save();
                
                $apmitems = PmItem::where('packing_material_id', $pm->pivot->packing_material_id)->where('shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->get();
                $uqty = $pmuseditem->quantity;
                foreach ($apmitems as $key => $pmitem) {
                    Log::info('Unit cost '.$pmitem->unit_cost);
                    $remqty = ($pmitem->qty-$pmitem->qty_out);
                    if ($uqty <= $remqty) {
                        $pmitem->qty_out = $pmitem->qty_out+$uqty;
                        if ($pmitem->qty == $pmitem->qty_out) {
                            $pmitem->is_utilized = true;
                        }else{
                            $pm->pivot->unit_cost = $pmitem->unit_cost;
                        }
                        $pmitem->save();
                    }else{
                        $pmitem->qty_out = $pmitem->qty_out+$remqty;
                        if ($pmitem->qty == $pmitem->qty_out) {
                            $pmitem->is_utilized = true;
                        }else{
                            $pm->pivot->unit_cost = $pmitem->unit_cost;
                        }
                        $pmitem->save();
                    }
                    $uqty -= $remqty;
                }
                $pm->pivot->save();
            }
            
            $prod_cost_item = ProductionCostItem::where('production_cost_id', $prod_cost->id)->where('product_id', $pmuseditem->product_packed)->first();
            if (is_null($prod_cost_item)) {       
                $prod_cost_item = new ProductionCostItem();
                $prod_cost_item->production_cost_id = $prod_cost->id;
                $prod_cost_item->product_id = $pmuseditem->product_packed;
                $prod_cost_item->packing_material_id = $pmuseditem->packing_material_id;
                $prod_cost_item->unit_packed = $pmuseditem->unit_packed;
                $prod_cost_item->quantity = $pmuseditem->quantity;
                $prod_cost_item->cost_per_unit = 0;
                $prod_cost_item->save();
            }

            $total_use = PmUseItem::where('shop_id' , $shop->id)->where('pm_use_id' , $pmuse->id)->sum('total');
            $pmuse->total_cost = $total_use;
            $pmuse->save();

            return redirect()->route('prod-costs.edit', encrypt($pmuse->production_cost_id))->with('success' , "Item Updated Successful");
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
        $page = 'Packing Materials';
        $title = 'Edit Item Used';
        $title_sw = 'Hariri Kilichotumika';
        
        $shop = Shop::find(Session::get('shop_id'));
        $pmuseditem = PmUseItem::where('pm_use_items.id', decrypt($id))->where('pm_use_items.shop_id', $shop->id)->join('packing_materials' , 'packing_materials.id' , '=' , 'pm_use_items.packing_material_id')->select('pm_use_items.id as id', 'name', 'quantity', 'unit_cost', 'product_packed', 'unit_packed')->first();
        if (!is_null($pmuseditem)) {
            $products = $shop->products()->get();
            $prod = Product::where('id' ,  $pmuseditem->product_packed)->first();

            return view('production.packing-materials.pm-uses.edit-used-item', compact('page', 'title', 'title_sw', 'pmuseditem', 'products', 'prod', 'shop'));
        }
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
        $shop = Shop::find(Session::get('shop_id'));
        $pmuseditem = PmUseItem::find(decrypt($id));
        $pmuse = PmUse::find($pmuseditem->pm_use_id);
        $pm = $shop->packingMaterials()->where('packing_material_id' , $pmuseditem->packing_material_id)->first();
        if( ($pm->pivot->in_store + $pmuseditem->quantity)-($request['quantity']) < 0 ){
            return redirect()->route('pm-uses.edit', encrypt($pmuse->production_cost_id))->with('error' , "Stock Available is less than edited amount");
        }else{
            $diff = $pmuseditem->quantity - $request['quantity'];
            $pmuseditem->product_packed  = $request['produt_id'];
            $pmuseditem->unit_packed = $request['unit_packed'];
            $pmuseditem->quantity = $request['quantity'];
            $pmuseditem->unit_cost = $request['unit_cost'];
            $pmuseditem->total = $pmuseditem->quantity*$pmuseditem->unit_cost;
            $pmuseditem->save();
             
            $pm->pivot->in_store = ($pm->pivot->in_store + $diff);
            $pm->pivot->save();

            $prod_cost_item = ProductionCostItem::where('production_cost_id', $pmuse->production_cost_id)->where('product_id', $pmuseditem->product_packed)->first();
            if (is_null($prod_cost_item)) {       
                $prod_cost_item = new ProductionCostItem();
                $prod_cost_item->production_cost_id = $pmuse->production_cost_id;
                $prod_cost_item->product_id = $pmuseditem->product_packed;
                $prod_cost_item->packing_material_id = $pmuseditem->packing_material_id;
                $prod_cost_item->unit_packed = $pmuseditem->unit_packed;
                $prod_cost_item->quantity = $pmuseditem->quantity;
                $prod_cost_item->cost_per_unit = 0;
                $prod_cost_item->save();
            }else{
                $prod_cost_item->product_id = $pmuseditem->product_packed;
                $prod_cost_item->packing_material_id = $pmuseditem->packing_material_id;
                $prod_cost_item->unit_packed = $pmuseditem->unit_packed;
                $prod_cost_item->quantity = $pmuseditem->quantity;
                $prod_cost_item->cost_per_unit = 0;
                $prod_cost_item->save();
            }
        }

        $total_use = PmUseItem::where('shop_id' , $shop->id)->where('pm_use_id' , $pmuse->id)->sum('total');
        $pmuse->total_cost = $total_use;
        $pmuse->save();

        return redirect()->route('prod-costs.edit', encrypt($pmuse->production_cost_id))->with('success' , "Item Updated Successful");
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
        $pmitem = PmUseItem::find(decrypt($id));
        $pmuse = PmUse::find($pmitem->pm_use_id);

        $pm = $shop->packingMaterials()->where('packing_material_id' , $pmitem->packing_material_id)->first();
        $pm->pivot->in_store =  $pm->pivot->in_store + $pmitem->quantity;
        $pm->pivot->save();

        $pci = ProductionCostItem::where('production_cost_id', $pmuse->production_cost_id)->where('packing_material_id', $pmitem->packing_material_id)->first();
        if (!is_null($pci)) {
            $prod_cost = ProductionCost::find($pci->production_cost_id);
            $pci->delete();

            $prod_cost_items = ProductionCostItem::where('production_cost_id', $pmuse->production_cost_id)->get();
            $total_vol = 0;
            $total_qty = 0;
            foreach ($prod_cost_items as $key => $pmade) {
                $total_vol += $pmade->quantity*$pmade->unit_packed;
                $total_qty += $pmade->quantity;
            }

            Log::info('TOTAL VOLUME : '.$total_vol);
            if ($total_vol > 0) {
                foreach ($prod_cost_items as $key => $value) {
                    $percent = round((($value->quantity*$value->unit_packed)/$total_vol)*100, 2);
                    $prod_cost_item = ProductionCostItem::find($value->id);
                    $prod_cost_item->cost_per_unit = (($percent/100)*$prod_cost->total_cost)/$value->quantity;
                    $prod_cost_item->save();
                }
            }

            $prod_cost->total_prod_qty = $total_qty;
            $prod_cost->total_vol = $total_vol;
            $prod_cost->save();
        }

        $pmitem->delete();

        $total_use = PmUseItem::where('shop_id', $shop->id)->where('pm_use_id' , $pmuse->id)->sum('total');
        $pmuse->total_cost = $total_use;
        $pmuse->save();

        $left_pmitem = PmUseItem::where('shop_id' , $shop->id)->where('pm_use_id' , $pmuse->id)->first();

        if(is_null($left_pmitem)){
            $pmuse->delete();
        }

        return redirect()->route('prod-costs.edit', encrypt($pmuse->production_cost_id))->with('success' , "Item Deleted Successful");
    }
}
