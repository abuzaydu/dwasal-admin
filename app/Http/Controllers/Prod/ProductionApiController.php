<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Response;
use Auth;
use Log;
use App\Models\Shop;
use App\Models\User;
use App\Models\RmUseItemTemp;
use App\Models\PmUseItemTemp;
use App\Models\MroUsedItemTemp;
use App\Models\DlcItemTemp;
use App\Models\Product;
use App\Models\ProductMadeApiTemp;
use App\Models\ProductPricing;
use App\Models\MaterialCost;
use App\Models\LabourCost;
use App\Models\Mro;
use App\Models\ProductionStage;


class ProductionApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $mros = $shop->mro()->where('is_deleted' , false)->get([
            \DB::raw('id'),
            \DB::raw('name'),]);
        $pms = $shop->packingMaterials()->whereNull('parent_pm_id')->where('is_deleted' , false)->get([
            \DB::raw('packing_material_id as id'),
            \DB::raw('name'),
            \DB::raw('in_store'),
            \DB::raw('unit_cost'),
            \DB::raw('description')]);
        $rms = $shop->rawMaterials()->where('is_deleted' , false)->get([
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
            \DB::raw('name'),
            \DB::raw('is_by_product')
        ]);

        $product_made = ProductMadeApiTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->get();

        return Response::json(['mros' => $mros, 'stages' => $stages, 'pms' => $pms, 'rms' => $rms, 'products' => $products, 'product_made' => $product_made]);
    }

    public function product_made(){
         $shop = Shop::find(Session::get('shop_id'));
         $user = Auth::user();
         $product_made = ProductMadeApiTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->get();
        return Response::json(['product_made' => $product_made]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();

        $product = $shop->products()->where('id', $request->product_packed)->first();
        $sameitems = ProductMadeApiTemp::where('product_id', $product->id)->where('shop_id' , $shop->id)->where('user_id', $user->id)->count();
        if ($sameitems == 0) {
            $product_made = new ProductMadeApiTemp;
            $product_made->shop_id = $shop->id;
            $product_made->user_id = $user->id;
            $product_made->product_id = $product->id;
            $product_made->name = $product->name;
            $product_made->qty = 1;
            $product_made->cost_per_unit = 0;
            $product_made->is_by_product = $product->is_by_product;
            $product_made->save();
 
            $pricing = ProductPricing::where('shop_id', $shop->id)->where('product_id', $product->id)->latest()->first();
            if (!is_null($pricing)) {
                $materialcosts = MaterialCost::where('product_pricing_id', $pricing->id)->select('item_desc', 'cost_per_piece')->get();
                foreach ($materialcosts as $key => $mcost) {
                    $material = $shop->rawMaterials()->where('name', $mcost->item_desc)->first();
                    if (!is_null($material)) {
                        $sameitem = RmUseItemTemp::where('raw_material_id', $material->id)->where('user_id', $user->id)->first();
                        if (is_null($sameitem)) {
                            $rmTemp = new RmUseItemTemp;
                            $rmTemp->shop_id = $shop->id;
                            $rmTemp->user_id = $user->id;
                            $rmTemp->raw_material_id = $material->id;
                            $rmTemp->quantity  = 1;
                            $rmTemp->unit_cost = $mcost->cost_per_piece;
                            $rmTemp->total = $rmTemp->quantity*$rmTemp->unit_cost;
                            $rmTemp->save();
                        }
                    }
                }

                $labourcosts = LabourCost::where('product_pricing_id', $pricing->id)->select('stage', 'cost_per_piece')->get();
                foreach ($labourcosts as $key => $lcost) {
                    $mro = $shop->mro->where('name', $lcost->stage)->first();
                    if (is_null($mro)) {
                        $mro = new Mro();
                        $mro->shop_id = $shop->id;
                        $mro->name = $lcost->stage;
                        $mro->save();
                    }

                    $sameitems = MroUsedItemTemp::where('mro_id', $mro->id)->where('user_id', $user->id)->count();
                    if ($sameitems == 0) {
                        $mroItemTemp = new MroUsedItemTemp;
                        $mroItemTemp->shop_id = $shop->id;
                        $mroItemTemp->user_id = $user->id;
                        $mroItemTemp->mro_id = $mro->id;
                        $mroItemTemp->quantity  = 1;
                        $mroItemTemp->unit_cost = $lcost->cost_per_piece;
                        $mroItemTemp->total = $mroItemTemp->quantity*$mroItemTemp->unit_cost;
                        $mroItemTemp->save();
                    }
                }
            }
        }else{
            return Response()->json(['status' => 'warning' , 'msg' => 'This product is Selected Aready']);  
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     $shop = Shop::find(Session::get('shop_id'));
    //     $user = Auth::user();
    //     if($request->for == 'rm'){
    //         $rmuItemTemp = RmUseItemTemp::where('raw_material_id', $request['rm_id'])->where('user_id', $user->id)->where('shop_id', $shop->id)->first();
    //         if (is_null($rmuItemTemp)) {
    //             $raw_material = $shop->rawMaterials()->where('raw_material_id', $request['rm_id'])->where('is_deleted' , false)->first();

    //             $rmuItemTemp = new RmUseItemTemp;
    //             $rmuItemTemp->shop_id = $shop->id;
    //             $rmuItemTemp->user_id = $user->id;
    //             $rmuItemTemp->raw_material_id = $request['rm_id'];
    //             $rmuItemTemp->quantity  = 1;
    //             $rmuItemTemp->unit_cost = is_null($raw_material->pivot->unit_cost) ? 0 : $raw_material->pivot->unit_cost ;
    //             $rmuItemTemp->total = $rmuItemTemp->unit_cost;
    //             $rmuItemTemp->save();
    //             return $rmuItemTemp;                
    //         }else{
    //             $rmuItemTemp->quantity =  $rmitems->quantity + 1;
    //             $rmuItemTemp->total = ($rmuItemTemp->quantity * $rmuItemTemp->unit_cost);
    //             $rmuItemTemp->save();
    //             return $rmuItemTemp;
    //         }
    //     }elseif($request->for == 'pm'){
    //         $pmuItemTemp = PmUseItemTemp::where('packing_material_id', $request['pm_id'])->where('shop_id', $shop->id)->where('user_id', $user->id)->whereNull('product_packed')->first();
    //         if (is_null($pmuItemTemp)) {
    //             $packing_material = $shop->packingMaterials()->where('packing_material_id', $request['pm_id'])->where('is_deleted' , false)->first();
    //             Log::info($packing_material);
    //             $pmuItemTemp = new PmUseItemTemp;
    //             $pmuItemTemp->shop_id = $shop->id;
    //             $pmuItemTemp->user_id = $user->id;
    //             $pmuItemTemp->packing_material_id = $request['pm_id'];
    //             $pmuItemTemp->quantity  = 1;
    //             $pmuItemTemp->unit_packed = 1;
    //             $pmuItemTemp->unit_cost = is_null($packing_material->pivot->unit_cost) ? 0 :$packing_material->pivot->unit_cost ;
    //             $pmuItemTemp->total = is_null($packing_material->pivot->unit_cost) ? 0 :$packing_material->pivot->unit_cost;
    //             $pmuItemTemp->save();
    //             return $pmuItemTemp;
    //         }else{
    //             $pmuItemTemp->quantity = $pmuItemTemp->quantity + 1;
    //             $pmuItemTemp->total = $pmuItemTemp->quantity * $pmuItemTemp->unit_cost;
    //             $pmuItemTemp->save();
    //             return $pmuItemTemp;
    //         }
    //     }elseif($request->for = 'mro'){
    //         $mroItemTemp = MroUsedItemTemp::where('mro_id', $request['mro_id'])->where('user_id', $user->id)->where('shop_id', $shop->id)->count();
           
    //         if (is_null($mroItemTemp)) {
    //             $mroItemTemp = new MroUsedItemTemp;
    //             $mroItemTemp->shop_id = $shop->id;
    //             $mroItemTemp->user_id = $user->id;
    //             $mroItemTemp->mro_id = $request['mro_id'];
    //             $mroItemTemp->quantity  = 1;
    //             $mroItemTemp->unit_cost = 0;
    //             $mroItemTemp->total = 0;
    //             $mroItemTemp->save();
    //             return $mroItemTemp;  
    //         }else{
    //             $mroItemTemp->quantity = $mroItemTemp->quantity +1 ;
    //             $mroItemTemp->total = $mroItemTemp->quantity * $mroItemTemp->unit_cost;
    //             $mroItemTemp->save();

    //             return $mroItemTemp;
    //         }
    //     }
    // }

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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $product_made = ProductMadeApiTemp::find($id);

        if ($product_made->qty !== $request->qty) {
            $product_made->qty = $request->qty;
            $product_made->save();

            // $rm = RmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->get();
            // foreach ($rm as $key => $rmTemp) {
            //     $rmTemp->quantity  = $product_made->qty;
            //     $rmTemp->total = $rmTemp->quantity*$rmTemp->unit_cost;
            //     $rmTemp->save();
            // }
            // $mro = MroUsedItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->get();
            // foreach ($mro as $key => $mroItemTemp) {
            //     $mroItemTemp->quantity = $product_made->qty;
            //     $mroItemTemp->total = $mroItemTemp->quantity*$mroItemTemp->unit_cost;
            //     $mroItemTemp->save();
            // }

            $this->updateProductMade($shop, $user);

        }elseif($product_made->selling_price !== $request->selling_price){
            $product_made->profit_margin = $request->selling_price - $product_made->cost_per_unit;
            $product_made->selling_price = $request->selling_price;
            $product_made->save();
        }elseif ($product_made->profit_margin !== $request->profit_margin) {

            $product_made->profit_margin = $request->profit_margin;
            $product_made->selling_price = $product_made->cost_per_unit + $request->profit_margin;
            $product_made->save();
        }elseif ($product_made->unit_packed != $request->unit_packed) {
            $product_made->unit_packed = $request->unit_packed;
            $product_made->save();

            $this->updateProductMade($shop, $user);
            if (!is_null($product_made->packing_material_id)) {
                $pm = PmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('product_packed', $product_made->product_id)->where('packing_material_id', $product_made->packing_material_id)->first();
                $pm->unit_packed = $product_made->unit_packed;
                $pm->save();
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

        Log::info('updating Product made '.$total_vol);    
        $pm = PmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $rm = RmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $dlc = DlcItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $mro = MroUsedItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        Log::info('total_cost '.($pm+$rm+$dlc+$mro));
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
    {   $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
       
        $producttemp = ProductMadeApiTemp::find($id);
        if(!is_null($producttemp)){
            $pm = PmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->where('product_packed', $producttemp->product_id)->get();
            foreach ($pm as $key => $value) {
                $value->delete();
            }
            $producttemp->delete();
        }
    }

    public function recalculate() {

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $pm = PmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $rm = RmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $dlc = DlcItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $mro = MroUsedItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $prod_api = ProductMadeApiTemp::where('shop_id' , $shop->id)->where('user_id', $user->id);
        $qty_made = $prod_api->sum('qty') ;
        if($qty_made == 0){
            $cost_per_unit = 0;
        }else{
            $cost_per_unit = ($pm+$rm+$dlc+$mro)/$qty_made; 
        }
            
        foreach($prod_api->get()  as $value){
            $value->cost_per_unit = $cost_per_unit;
            $value->selling_price = ($value->profit_margin + $value->cost_per_unit);
            $value->save();
        }
    }

}
