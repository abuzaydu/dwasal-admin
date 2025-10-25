<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Log;
use Session;
use Auth;
use App\Models\Mro;
use App\Models\Shop;
use App\Models\MroItem;
use App\Models\MroUsedItemTemp;
use App\Models\ProductMadeApiTemp;
use App\Models\DlcItemTemp;
use App\Models\PmUseItemTemp;
use App\Models\RmUseItemTemp;

class MroUsedItemTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(MroUsedItemTemp::where('mro_used_item_temps.shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->join('mros', 'mros.id', '=', 'mro_used_item_temps.mro_id')->select('mro_used_item_temps.id as id', 'mro_used_item_temps.quantity as quantity', 'mro_used_item_temps.unit_cost as unit_cost', 'mro_used_item_temps.total as total' ,'mros.name as name')->get());
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
        $sameitems = MroUsedItemTemp::where('mro_id', $request['mro_id'])->where('user_id', $user->id)->count();
           
        if ($sameitems == 0) {
            $lastitem = MroItem::where('shop_id', $shop->id)->where('mro_id', $request['mro_id'])->latest()->first();
            $mroItemTemp = new MroUsedItemTemp;
            $mroItemTemp->shop_id = $shop->id;
            $mroItemTemp->user_id = $user->id;
            $mroItemTemp->mro_id = $request['mro_id'];
            $mroItemTemp->quantity  = 1;
            if (!is_null($lastitem)) {
                $lastitem->unit_cost = $lastitem->unit_cost;
                $lastitem->total = $lastitem->unit_cost;
            }else{
                $mroItemTemp->unit_cost = 0;
                $mroItemTemp->total = 0;
            }

            $mroItemTemp->save();
            return $mroItemTemp;  
        }else{
            $warning = 'Ooops!. The Mro Item already in selected items.';
            return redirect('add-mros')->with('warning', $warning);
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
        $mroItemTemp =  mroUsedItemTemp::where('id', $id)->where('user_id', $user->id)->where('shop_id', $shop->id)->first();
        if (!is_null($mroItemTemp)) {

            if($mroItemTemp->unit_cost != $request['unit_cost']) {  
                $mroItemTemp->unit_cost = $request['unit_cost'];
                $mroItemTemp->total = $mroItemTemp->quantity*$mroItemTemp->unit_cost;
                $mroItemTemp->save();
            }elseif ($mroItemTemp->quantity != $request['quantity']) {
                $mroItemTemp->quantity  = $request['quantity'];
                $mroItemTemp->total = $mroItemTemp->quantity*$mroItemTemp->unit_cost;
                $mroItemTemp->save();
            }

            $this->updateProductMade($shop, $user);

            return $mroItemTemp;
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
         MroUsedItemTemp::destroy($id);
    }

    function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}
