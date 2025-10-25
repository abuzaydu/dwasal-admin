<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use Response;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\PackingMaterial;
use App\Models\PmUseItemTemp;
use App\Models\RmUseItemTemp;
use App\Models\MroUsedItemTemp;
use App\Models\ProductMadeApiTemp;
use App\Models\Product;
use App\Models\PmItem;
use App\Models\DlcItemTemp;
use Log;

class PmUseItemTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(PmUseItemTemp::where('pm_use_item_temps.shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->join('packing_materials', 'packing_materials.id', '=', 'pm_use_item_temps.packing_material_id')->select('pm_use_item_temps.id as id', 'pm_use_item_temps.quantity as quantity', 'pm_use_item_temps.unit_cost as unit_cost', 'pm_use_item_temps.total as total', 'packing_materials.name as name' , 'pm_use_item_temps.product_packed as product_packed' , 'pm_use_item_temps.unit_packed as unit_packed')->get());
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $pmuItemTemp = PmUseItemTemp::where('packing_material_id', $request['pm_id'])->where('shop_id', $shop->id)->where('user_id', $user->id)->whereNull('product_packed')->first();
        if (is_null($pmuItemTemp)) {
            $packing_material = $shop->packingMaterials()->where('packing_material_id', $request['pm_id'])->where('is_deleted' , false)->first();
            if (!is_null($packing_material)) {
                    
                $pmitem = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->first();
                $pmuItemTemp = new PmUseItemTemp;
                $pmuItemTemp->shop_id = $shop->id;
                $pmuItemTemp->user_id = $user->id;
                $pmuItemTemp->packing_material_id = $request['pm_id'];
                $pmuItemTemp->quantity  = 1;
                $pmuItemTemp->unit_packed = 1;
                if (!is_null($pmitem)) {
                    $pmuItemTemp->unit_cost = $pmitem->unit_cost;
                }else{
                    $pmuItemTemp->unit_cost = is_null($packing_material->pivot->unit_cost) ? 0 :$packing_material->pivot->unit_cost ;
                }
                $pmuItemTemp->total = $pmuItemTemp->unit_cost;
                $pmuItemTemp->save();
                return $pmuItemTemp;
            }
        }else{
            $pmuItemTemp->quantity = $pmuItemTemp->quantity + 1;
            $pmuItemTemp->total = $pmuItemTemp->quantity * $pmuItemTemp->unit_cost;
            $pmuItemTemp->save();
            return $pmuItemTemp;
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
        //
    }

    public function saveProdTemp(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $pmusedItemTemp =  PmUseItemTemp::where('id', $request['tempid'])->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();
        if(!is_null($pmusedItemTemp)){
            $pmusedItemTemp->product_packed = $request['product_id'];
            $pmusedItemTemp->save();
        }

        return response()->json($pmusedItemTemp);
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
        $user = Auth::user();
        $pmuItemTemp =  PmUseItemTemp::where('id', $id)->where('user_id', $user->id)->where('shop_id', $shop->id)->first();
        if (!is_null($pmuItemTemp)) {
            if ($pmuItemTemp->quantity != $request['quantity']) {
                $packing_material  = $shop->packingMaterials()->where('is_deleted', false)->where('packing_materials.id' , $pmuItemTemp->packing_material_id)->first();
                if (is_null($packing_material->pivot->in_store) || $packing_material->pivot->in_store < $request['quantity']) {
                    return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Packing Material '.$packing_material->name.' is currently less than.'.($request['quantity'])]);
                } else {
                    if (!$this->is_decimal($request['quantity'])) {
                        $pmuItemTemp->quantity  = $request['quantity'];
                        $pmuItemTemp->total = $pmuItemTemp->quantity*$pmuItemTemp->unit_cost;
                        $pmuItemTemp->save();
                        $prodmade = ProductMadeApiTemp::where('product_id',$pmuItemTemp->product_packed)->where('shop_id' , $shop->id)->where('user_id', $user->id)->first();
                        if(!is_null($prodmade)) {
                            $prodmade->qty = $request['quantity'];
                            $prodmade->save();

                            $this->updateProductMade($shop, $user);
                        }

                        return $pmuItemTemp;
                    } else{
                        return response()->json(['status' => 'WRONG', 'msg' => 'Packing material can not accept decimal quantity.']);
                    }
                }
            }elseif ($pmuItemTemp->unit_packed != $request['unit_packed'] && $request['unit_packed'] != 0 ) {
                $pmuItemTemp->unit_packed = $request['unit_packed'];
                $pmuItemTemp->save();
                $prodmade = ProductMadeApiTemp::where('product_id',$pmuItemTemp->product_packed)->where('shop_id' , $shop->id)->where('user_id', $user->id)->first();
                if(!is_null($prodmade)){
                    $prodmade->unit_packed = $request['unit_packed'];
                    $prodmade->save();

                    $this->updateProductMade($shop, $user);
                }
                return $pmuItemTemp;

            }elseif ($pmuItemTemp->product_packed != $request['product_packed']) {
                $prod = $shop->products()->where('id', $request['product_packed'])->first();
                $oldset = ProductMadeApiTemp::where('product_id',$pmuItemTemp->product_packed)->where('shop_id' , $shop->id)->where('user_id', $user->id)->where('packing_material_id' , $pmuItemTemp->packing_material_id)->first();
                    
                if (is_null($oldset)) { 
                    $product_made = new ProductMadeApiTemp;
                    $product_made->shop_id = $shop->id;
                    $product_made->user_id = $user->id;
                    $product_made->product_id = $prod->id;
                    $product_made->name = $prod->name;
                    $product_made->packing_material_id = $pmuItemTemp->packing_material_id;
                    $product_made->qty = $pmuItemTemp->quantity;
                    $product_made->cost_per_unit = 0;
                    $product_made->is_by_product = $prod->pivot->is_by_product;
                    $product_made->save();
                }

                $pmuItemTemp->product_packed = $request['product_packed'];
                $pmuItemTemp->save();

                $this->updateProductMade($shop, $user);
                return $pmuItemTemp;
            }
        }
    }

    public function updateProductMade($shop, $user)
    {
        $prod_api = ProductMadeApiTemp::where('shop_id' , $shop->id)->where('user_id', $user->id)->get();
        $total_vol = 0;
        foreach ($prod_api as $key => $pmade) {
            if (!$pmade->is_by_product) {
                $total_vol += $pmade->qty*$pmade->unit_packed;
            }
        }

        // Log::info('updating Product made '.$total_vol);    
        $pm = PmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $rm = RmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $dlc = DlcItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $mro = MroUsedItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        // Log::info('total_cost '.($pm+$rm+$dlc+$mro));
        if ($total_vol > 0) {
            foreach($prod_api as $value){
                if (!$value->is_by_product) {
                    // Log::info('Ratio '.($value->unit_packed/$total_vol)*($pm+$rm+$mro));
                    $value->cost_per_unit = ($value->unit_packed/$total_vol)*($pm+$rm+$dlc+$mro);
                    $value->selling_price = ($value->profit_margin + $value->cost_per_unit);
                    $value->save();
                }else{
                    $value->cost_per_unit = 0;
                    $value->save();
                }
            }
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pmutemp = PmUseItemTemp::find($id);
        if (!is_null($pmutemp)) {
            $prodmade = ProductMadeApiTemp::where('product_id',$pmutemp->product_packed)->where('shop_id' , $pmutemp->shop_id)->where('user_id', $pmutemp->user_id)->where('packing_material_id' , $pmutemp->packing_material_id)->first();
            if (!is_null($prodmade)) {
                $prodmade->delete();
            }
            $pmutemp->delete();
        }
    }

     function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}
