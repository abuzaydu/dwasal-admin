<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\Models\Shop;
use App\Models\User;
use App\Models\ProductionCostItem;
use App\Models\ProductionCost;
use App\Models\PmUseItem;
use App\Models\PmUse;

class ProdCostItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $prod_cost = ProductionCost::find($request['production_cost_id']);   
        $shop = Shop::find($prod_cost->shop_id);
        $user = User::find($prod_cost->user_id);
        $product = $shop->products()->where('id', $request['product_id'])->first();
        $prod_cost_item = ProductionCostItem::where('production_cost_id', $prod_cost->id)->where('product_id', $product->id)->first();
        if (is_null($prod_cost_item)) {
            $prod_cost_item = new ProductionCostItem();
            $prod_cost_item->product_id = $product->product_id;
            $prod_cost_item->unit_packed = $request['unit_packed'];
            $prod_cost_item->quantity = $request['quantity'];
            $prod_cost_item->cost_per_unit = 0;
            $prod_cost_item->profit_margin = 0;
            $prod_cost_item->selling_price = $product->retail_price;
            $prod_cost_item->production_cost_id = $prod_cost->id;
            $prod_cost_item->is_by_product = $product->is_by_product;
            $prod_cost_item->save();

            return redirect()->route('prod-costs.edit', encrypt($prod_cost->id))->with('success' , "Item added Successful");
        }else{
            return redirect()->route('prod-costs.edit', encrypt($prod_cost->id))->with('info' , "Ite already selected");
        }
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
        $page = 'Edit Product Made';
        $title = 'Edit Product Made';
        $proditem = ProductionCostItem::where('production_cost_items.id', decrypt($id))->join('products', 'products.id', '=', 'production_cost_items.product_id')->select('production_cost_items.id as id', 'product_id', 'name', 'quantity', 'unit_packed', 'cost_per_unit')->first();

        return view('production.edit-prod-cost-item', compact('page', 'title', 'proditem'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $proditem = ProductionCostItem::find(decrypt($id));
        if (!is_null($proditem)) {
            $prod_cost = ProductionCost::find($proditem->production_cost_id);
            $proditem->quantity = $request['quantity'];
            $proditem->unit_packed = $request['unit_packed'];
            $proditem->save();

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
                        $value->cost_per_unit = (($percent/100)*$prod_cost->total_cost)/$value->quantity;
                        $value->save();
                    }else{
                        $value->cost_per_unit = 0;
                        $value->save();
                    }
                }
            }

            $prod_cost->total_prod_qty = $total_qty;
            $prod_cost->total_vol = $total_vol;
            $prod_cost->save();

            return redirect()->route('prod-costs.edit', encrypt($prod_cost->id))->with('success' , "Item Deleted Successful");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pci = ProductionCostItem::find(decrypt($id));
        if (!is_null($pci)) {
            $prod_cost = ProductionCost::find($pci->production_cost_id);
            $shop = Shop::find($prod_cost->shop_id);
            $pmitem = PmUseItem::find($pci->packing_material_id);
            if (!is_null($pmitem)) {
                    
                $pmuse = PmUse::find($pmitem->pm_use_id);

                $pm = $shop->packingMaterials()->where('packing_material_id' , $pmitem->packing_material_id)->first();
                $pm->pivot->in_store =  $pm->pivot->in_store + $pmitem->quantity;
                $pm->pivot->save();
                $pmitem->delete();

                 $total_use = PmUseItem::where('shop_id', $shop->id)->where('pm_use_id' , $pmuse->id)->sum('total');
                  $pmuse->total_cost = $total_use;
                  $pmuse->save();

                $left_pmitem = PmUseItem::where('shop_id' , $shop->id)->where('pm_use_id' , $pmuse->id)->first();

                if(is_null($left_pmitem)){
                    $pmuse->delete();
                }
            }
            $pci->delete();

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
                        $value->cost_per_unit = (($percent/100)*$prod_cost->total_cost)/$value->quantity;
                        $value->save();
                    }else{
                        $value->cost_per_unit = 0;
                        $value->save();
                    }
                }
            }

            $prod_cost->total_prod_qty = $total_qty;
            $prod_cost->total_vol = $total_vol;
            $prod_cost->save();

            return redirect()->route('prod-costs.edit', encrypt($prod_cost->id))->with('success' , "Item Deleted Successful");
        }else{
            return redirect()->back()->with('error' , "Item no found");
        }
    }
}
