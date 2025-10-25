<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Response;
use Input;
use Session;
use App\Models\Shop;
use App\Models\User;
use App\Models\RmUseItemTemp;
use App\Models\RawMaterial;
use App\Models\ProductMadeApiTemp;
use App\Models\DlcItemTemp;
use App\Models\MroUsedItemTemp;
use App\Models\PmUseItemTemp;
use App\Models\RmItem;
use Log;

class RmUseItemTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(RmUseItemTemp::where('rm_use_item_temps.shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->join('raw_materials', 'raw_materials.id', '=', 'rm_use_item_temps.raw_material_id')->select('rm_use_item_temps.id as id', 'rm_use_item_temps.quantity as quantity', 'rm_use_item_temps.unit_cost as unit_cost', 'rm_use_item_temps.total as total', 'raw_materials.name as name')->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
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
        $rmuItemTemp = RmUseItemTemp::where('raw_material_id', $request['rm_id'])->where('user_id', $user->id)->where('shop_id', $shop->id)->first();
        if (is_null($rmuItemTemp)) {
            $raw_material = $shop->rawMaterials()->where('raw_material_id', $request['rm_id'])->where('is_deleted' , false)->first();
            $unit_cost_used = is_null($raw_material->pivot->unit_cost) ? 0 : $raw_material->pivot->unit_cost;
            // Log::info('Current Stock : '.$raw_material->pivot->in_store);
            $rmitems = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->orderBy('date', 'desc')->select('date', 'qty', 'unit_cost')->get();
            $currstock = $raw_material->pivot->in_store;
            foreach ($rmitems as $key => $rmitem) {
                // Log::info($rmitem->date.' : '.$rmitem->qty.' : '.$rmitem->unit_cost);
                if ($currstock > 0) {
                    $currstock -= $rmitem->qty;
                    // Log::info('Curr : '.$currstock);
                    if ($currstock <= $rmitem->qty) {
                        $unit_cost_used = $rmitem->unit_cost;
                    }
                }
            }

            $rmuItemTemp = new RmUseItemTemp;
            $rmuItemTemp->shop_id = $shop->id;
            $rmuItemTemp->user_id = $user->id;
            $rmuItemTemp->raw_material_id = $request['rm_id'];
            $rmuItemTemp->quantity  = 1;
            $rmuItemTemp->unit_cost =  $unit_cost_used;
            $rmuItemTemp->total = $rmuItemTemp->unit_cost;
            $rmuItemTemp->save();
            return $rmuItemTemp;                
        }else{
            $rmuItemTemp->quantity =  $rmuItemTemp->quantity + 1;
            $rmuItemTemp->total = ($rmuItemTemp->quantity * $rmuItemTemp->unit_cost);
            $rmuItemTemp->save();
            return $rmuItemTemp;
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
        $stockItemTemp =  RmUseItemTemp::where('id', $id)->where('user_id', $user->id)->where('shop_id', $shop->id)->first();
        if (!is_null($stockItemTemp)) {

            if ($stockItemTemp->total != $request['total']) {
                $stockItemTemp->total = $request['total'];
                if ($stockItemTemp->quantity != 0) {
                    $stockItemTemp->unit_cost = $stockItemTemp->total/$stockItemTemp->quantity;
                }
                $stockItemTemp->save();

                $this->updateProductMade($shop, $user);
                return $stockItemTemp;     
            }elseif($stockItemTemp->unit_cost != $request['unit_cost']) { 
                $stockItemTemp->unit_cost = $request['unit_cost'];
                $stockItemTemp->total = $stockItemTemp->quantity*$stockItemTemp->unit_cost;
                $stockItemTemp->save();

                $this->updateProductMade($shop, $user);
                return $stockItemTemp;
            }elseif ($stockItemTemp->quantity != $request['quantity']) {

                $raw_material = $shop->rawMaterials()->where('is_deleted', false)->where('raw_materials.id' , $stockItemTemp->raw_material_id)->first();

                if (is_null($raw_material->pivot->in_store) || $raw_material->pivot->in_store < $request['quantity']) {

                      return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Product '.$raw_material->name.' is currently less than.'.($request['quantity'])]);
                } else {

                    if ($raw_material->basic_uom == 'pcs' || $raw_material->basic_uom == 'prs' || $raw_material->basic_uom == 'box' || $raw_material->basic_uom == 'btl' || $raw_material->basic_uom == 'pks' || $raw_material->basic_uom == 'gls') {
                        if (!$this->is_decimal($request->quantity)) {
                            $stockItemTemp->quantity  = $request['quantity'];
                            $stockItemTemp->total = $stockItemTemp->quantity*$stockItemTemp->unit_cost;
                            $stockItemTemp->save();

                            $this->updateProductMade($shop, $user);
                            return $stockItemTemp;
                        }else{

                            return response()->json(['status' => 'WRONG', 'msg' => 'This raw_material '.$raw_material->name.' can not accept decimal quantity '.$request->quantity.'. Please change its basic unit if you want to set decimal for stock quantity values']);
                        }
                    }else{

                        $raw_material = RawMaterial::find($stockItemTemp->raw_material_id);
                        $raw_material_detail = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();
                        $stockItemTemp->quantity  = $request['quantity'];
                        $stockItemTemp->total = $stockItemTemp->quantity*$stockItemTemp->unit_cost;
                        $stockItemTemp->save();

                        $this->updateProductMade($shop, $user);
                        return $stockItemTemp;
                    }

                }
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
        RmUseItemTemp::destroy($id);
    }


      function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}
