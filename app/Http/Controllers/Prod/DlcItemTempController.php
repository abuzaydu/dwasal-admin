<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Log;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\DlcItem;
use App\Models\DlcItemTemp;
use App\Models\PmUseItemTemp;
use App\Models\RmUseItemTemp;
use App\Models\MroUsedItemTemp;
use App\Models\ProductMadeApiTemp;

class DlcItemTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(DlcItemTemp::where('dlc_item_temps.shop_id', Session::get('shop_id'))->where('user_id', Auth::id())->join('production_stages', 'production_stages.id', '=', 'dlc_item_temps.production_stage_id')->select('dlc_item_temps.id as id', 'dlc_item_temps.quantity as quantity', 'dlc_item_temps.unit_cost as unit_cost', 'dlc_item_temps.total as total' ,'stage')->get());
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
        $sameitems = DlcItemTemp::where('production_stage_id', $request['production_stage_id'])->where('shop_id', $shop->id)->where('user_id', $user->id)->count();
           
        if ($sameitems == 0) {
            $lastitem = DlcItem::where('shop_id', $shop->id)->where('production_stage_id', $request['production_stage_id'])->latest()->first();
            $itemTemp = new DlcItemTemp;
            $itemTemp->shop_id = $shop->id;
            $itemTemp->user_id = $user->id;
            $itemTemp->production_stage_id = $request['production_stage_id'];
            $itemTemp->quantity  = 1;
            if (!is_null($lastitem)) {
                $itemTemp->unit_cost = $lastitem->unit_cost;
                $itemTemp->total = $itemTemp->unit_cost;
            }else{
                $itemTemp->unit_cost = 0;
                $itemTemp->total = 0;
            }
            $itemTemp->save();
            return $itemTemp;  
        }else{
            $warning = 'Ooops!. The Item already in selected items.';
            
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
        $itemTemp =  DlcItemTemp::where('id', $id)->where('user_id', $user->id)->where('shop_id', $shop->id)->first();
        if (!is_null($itemTemp)) {

            if($itemTemp->unit_cost != $request['unit_cost']) {  
                $itemTemp->unit_cost = $request['unit_cost'];
                $itemTemp->total = $itemTemp->quantity*$itemTemp->unit_cost;
                $itemTemp->save();

            }elseif ($itemTemp->quantity != $request['quantity']) {
                $itemTemp->quantity  = $request['quantity'];
                $itemTemp->total = $itemTemp->quantity*$itemTemp->unit_cost;
                $itemTemp->save();

            }
            $this->updateProductMade($shop, $user);
                
            return $itemTemp;
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
         DlcItemTemp::destroy($id);
    }

    function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}
