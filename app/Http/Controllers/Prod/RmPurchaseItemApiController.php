<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Input;
use Auth;
use Log;
use Session;
use App\Models\Shop;
use App\Models\User;
use App\Models\RawMaterial;
use App\Models\RmPurchaseItemTemp;
use App\Models\RmPurchaseTemp;
use App\Models\Supplier;
use App\Models\ShopCurrency;

class RmPurchaseItemApiController extends Controller
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
        $rmtemp = RmPurchaseTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->first();
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Raw Materials')->get();
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        if (!is_null($rmtemp)) {
                
            $rms = RmPurchaseItemTemp::where('rm_purchase_temp_id', $rmtemp->id)->with('rawMaterial')->get();
            $itemtemps = [];
     
            foreach($rms as $value){
                $rm=null;
                $rm =[
                    'id' => $value->id,
                    'shop_id' => $value->shop_id,
                    'user_id' => $value->user_id,
                    'raw_material_id'=> $value->raw_material_id,
                    'rm_purchase_temp_id' => $value->rm_purchase_temp_id,
                    'qty' => $value->qty,
                    'unit_cost' => round($value->unit_cost*$rmtemp->ex_rate, 2),
                    'total' => round($value->total*$rmtemp->ex_rate, 2),
                    'name'  => $value->rawMaterial->name,
                    'basic_uom'  => $value->rawMaterial->basic_uom
                ];

                array_push($itemtemps , $rm);
            }

            return Response::json(['itemtemps' => $itemtemps, 'suppliers' => $suppliers, 'currencies' => $currencies , 'rmtemp' => $rmtemp ]);
        }
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

        $sameitems = RmPurchaseItemTemp::where('raw_material_id', $request['raw_material_id'])->where('user_id', $user->id)->count();
        
        if ($sameitems == 0) {
            $raw_material = $shop->rawMaterials()->where('raw_material_id', $request['raw_material_id'])->where('is_deleted' , false)->first();
            $stockItemTemp = new RmPurchaseItemTemp;
            $stockItemTemp->shop_id = $shop->id;
            $stockItemTemp->rm_purchase_temp_id =$request->rm_purchase_temp_id;
            $stockItemTemp->user_id = $user->id;
            $stockItemTemp->raw_material_id = $request['raw_material_id'];
            $stockItemTemp->qty  = 0;
            $stockItemTemp->unit_cost = is_null($raw_material->pivot->unit_cost) ? 0: $raw_material->pivot->unit_cost ;
            $stockItemTemp->total = 0;
            $stockItemTemp->save();
            return $stockItemTemp;
            
        }else{
            $warning = 'Ooops!. The raw_material already in selected items.';
            return redirect('add-stocks')->with('warning', $warning);
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
        $rmtemp = RmPurchaseTemp::where('shop_id' , $shop->id)->where('user_id' , Auth::id())->first();

        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();

        $stockItemTemp =  RmPurchaseItemTemp::where('id', $id)->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();

        if (!is_null($stockItemTemp)) {

            if ($stockItemTemp->qty != $request['qty']) {
                $raw_material = $shop->rawMaterials()->where('is_deleted' , false)->first();
                if ($raw_material->basic_uom == 'pcs' || $raw_material->basic_uom == 'prs' || $raw_material->basic_uom == 'box' || $raw_material->basic_uom == 'btl' || $raw_material->basic_uom == 'pks' || $raw_material->basic_uom == 'gls') {
                    if (!$this->is_decimal($request['qty'])) {
                        $stockItemTemp->qty  = $request['qty'];
                        $stockItemTemp->total = $stockItemTemp->qty*$stockItemTemp->unit_cost;
                        $stockItemTemp->save();
                        return $stockItemTemp;
                    }else{
                        return response()->json(['status' => 'WRONG', 'msg' => 'This raw_material '.$raw_material->name.' can not accept decimal quantity. Please change its basic unit if you want to set decimal for stock quantity values']);
                    }
                }else{
                    $stockItemTemp->qty  = $request['qty'];
                    $stockItemTemp->total = $stockItemTemp->qty*$stockItemTemp->unit_cost;
                    $stockItemTemp->save();
                    return $stockItemTemp;
                }
            }else{
                if ($stockItemTemp->total != round($request['total']/$rmtemp->ex_rate, 2)) {
                    if($rmtemp->currency !== $rmtemp->defcurr){
                        $stockItemTemp->total = $request['total']/$rmtemp->ex_rate;
                    }else{
                        $stockItemTemp->total = $request['total'];
                    }
                    if ($stockItemTemp->qty != 0) {
                        $stockItemTemp->unit_cost = $stockItemTemp->total/$stockItemTemp->qty;
                    }
                    $stockItemTemp->save();
                    return $stockItemTemp;
                }else{
                    if($stockItemTemp->unit_cost != round($request['unit_cost']/$rmtemp->ex_rate, 2)) {
                        if($rmtemp->currency !== $rmtemp->defcurr){ 
                            $stockItemTemp->unit_cost = $request['unit_cost']/$rmtemp->ex_rate;
                        }else{
                            $stockItemTemp->unit_cost = $request['unit_cost']; 
                        }
                        $stockItemTemp->total = $stockItemTemp->qty*$stockItemTemp->unit_cost;
                        $stockItemTemp->save();
                        return $stockItemTemp;
                    }
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
        RmPurchaseItemTemp::destroy($id);
    }

    function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}