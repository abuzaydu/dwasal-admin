<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Input;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\PackingMaterial;
use App\Models\PmPurchaseItemTemp;
use App\Models\PmPurchaseTemp;
use App\Models\ShopCurrency;
use App\Models\Currency;
use App\Models\Supplier;

class PmPurchaseItemApiController extends Controller
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
        $pmtemp = PmPurchaseTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->first();
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Packing Materials')->get();
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        if (!is_null($pmtemp)) {
            
            $pms = PmPurchaseItemTemp::where('pm_purchase_temp_id', $pmtemp->id)->with('packingMaterial')->get();

            $itemtemps = [];

            foreach($pms as $value){
                $pm=null;
                $pm =[
                    'id' => $value->id,
                    'shop_id' => $value->shop_id,
                    'user_id' => $value->user_id,
                    'packing_material_id'=> $value->packing_material_id,
                    'pm_purchase_temp_id' => $value->pm_purchase_temp_id,
                    'qty' => $value->qty,
                    'unit_cost' => $pmtemp->currency !== $pmtemp->defcurr ? round(($value->unit_cost*$pmtemp->ex_rate) , 2) : round( $value->unit_cost , 2) ,
                    'total' =>$pmtemp->currency !== $pmtemp->defcurr ? ($value->total*$pmtemp->ex_rate):$value->total,
                    'name'  => $value->packingMaterial->name,
                    'basic_uom'  => $value->packingMaterial->basic_uom
                ];

                array_push($itemtemps , $pm);
            }

            return Response::json(['itemtemps' => $itemtemps, 'suppliers' => $suppliers, 'currencies' => $currencies , 'pmtemp' => $pmtemp]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $sameitems = PmPurchaseItemTemp::where('packing_material_id', $request['packing_material_id'])->where('user_id', $user->id)->count();
        
        if ($sameitems == 0) {
            $packing_material = $shop->packingMaterials()->where('is_deleted' , false)->where('packing_material_id', $request['packing_material_id'])->first();
            $stockItemTemp = new PmPurchaseItemTemp;
            $stockItemTemp->shop_id = $shop->id;
            $stockItemTemp->user_id = $user->id;
            $stockItemTemp->packing_material_id = $request['packing_material_id'];
            $stockItemTemp->qty  = 0;
            $stockItemTemp->unit_cost = is_null($packing_material->pivot->unit_cost) ? 0 : $packing_material->pivot->unit_cost;
            $stockItemTemp->total = 0;
            $stockItemTemp->save();
            return $stockItemTemp;
            
        }else{
            $warning = 'Ooops!. The packing material already in selected items.';
            return redirect('add-stocks')->with('warning', $warning);
        }
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
        $sameitems = PmPurchaseItemTemp::where('packing_material_id', $request['packing_material_id'])->where('user_id', $user->id)->count();
        
        if ($sameitems == 0) {
            $packing_material = $shop->packingMaterials()->where('is_deleted' , false)->where('packing_material_id', $request['packing_material_id'])->first();
            $stockItemTemp = new PmPurchaseItemTemp;
            $stockItemTemp->shop_id = $shop->id;
            $stockItemTemp->user_id = $user->id;
            $stockItemTemp->packing_material_id = $request['packing_material_id'];
            $stockItemTemp->pm_purchase_temp_id =$request->pm_purchase_temp_id;
            $stockItemTemp->qty  = 0;
            $stockItemTemp->unit_cost = is_null($packing_material->pivot->unit_cost) ? 0 : $packing_material->pivot->unit_cost;
            $stockItemTemp->total = 0;
            $stockItemTemp->save();
            return $stockItemTemp;
            
        }else{
            $warning = 'Ooops!. The packing material already in selected items.';
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
        $pmtemp = PmPurchaseTemp::where('shop_id' , $shop->id)->where('user_id' , Auth::id())->first();
         $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();

        $stockItemTemp =  PmPurchaseItemTemp::where('id', $id)->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();
        if (!is_null($stockItemTemp)) {

            if($pmtemp->currency !== $pmtemp->defcurr){

                if ($stockItemTemp->total != ($request['total']/$pmtemp->ex_rate)) {

                    $stockItemTemp->total = ($request['total']/$pmtemp->ex_rate);
                   

                    if ($stockItemTemp->qty != 0) {

                        $stockItemTemp->unit_cost = $stockItemTemp->total/$stockItemTemp->qty;
                    }

                    $stockItemTemp->save();

                    return $stockItemTemp;
                
            }elseif($stockItemTemp->unit_cost != ($request['unit_cost']/$pmtemp->ex_rate)) { 
                
                    
                $stockItemTemp->unit_cost = $request['unit_cost']/$pmtemp->ex_rate;
                
                $stockItemTemp->total = $stockItemTemp->qty*$stockItemTemp->unit_cost;
                $stockItemTemp->save();

                return $stockItemTemp;
            }elseif ($stockItemTemp->qty != $request['qty']) {

                $packing_material = PackingMaterial::find($stockItemTemp->packing_material_id);

                if ($packing_material->basic_uom == 'pcs' || $packing_material->basic_uom == 'prs' || $packing_material->basic_uom == 'box' || $packing_material->basic_uom == 'btl' || $packing_material->basic_uom == 'pks' || $packing_material->basic_uom == 'gls') {
                    if (!$this->is_decimal($request['qty'])) {
                        $stockItemTemp->qty  = $request['qty'];
                        $stockItemTemp->total = $stockItemTemp->qty*$stockItemTemp->unit_cost;
                        $stockItemTemp->save();

                        return $stockItemTemp;
                    }else{

                        return response()->json(['status' => 'WRONG', 'msg' => 'This packing_material '.$packing_material->name.' can not accept decimal quantity. Please change its basic unit if you want to set decimal for stock quantity values']);
                    }
                }else{
                    $stockItemTemp->qty  = $request['qty'];
                    $stockItemTemp->total = $stockItemTemp->qty*$stockItemTemp->unit_cost;
                    $stockItemTemp->save();

                    return $stockItemTemp;
                }
            }

            }else{ 

                if ($stockItemTemp->total != $request['total']) {

                    $stockItemTemp->total = $request['total']; 

                    if ($stockItemTemp->qty != 0) {

                        $stockItemTemp->unit_cost = $stockItemTemp->total/$stockItemTemp->qty;
                    }

                    $stockItemTemp->save();

                    return $stockItemTemp;
                    
                }elseif($stockItemTemp->unit_cost != $request['unit_cost']) {
                    $stockItemTemp->unit_cost = $request['unit_cost']; 
                    
                    $stockItemTemp->total = $stockItemTemp->qty*$stockItemTemp->unit_cost;
                    $stockItemTemp->save();

                    return $stockItemTemp;
                }elseif ($stockItemTemp->qty != $request['qty']) {

                    $packing_material = PackingMaterial::find($stockItemTemp->packing_material_id);

                    if ($packing_material->basic_uom == 'pcs' || $packing_material->basic_uom == 'prs' || $packing_material->basic_uom == 'box' || $packing_material->basic_uom == 'btl' || $packing_material->basic_uom == 'pks' || $packing_material->basic_uom == 'gls') {
                        if (!$this->is_decimal($request['qty'])) {
                            $stockItemTemp->qty  = $request['qty'];
                            $stockItemTemp->total = $stockItemTemp->qty*$stockItemTemp->unit_cost;
                            $stockItemTemp->save();

                            return $stockItemTemp;
                        }else{

                            return response()->json(['status' => 'WRONG', 'msg' => 'This packing_material '.$packing_material->name.' can not accept decimal quantity. Please change its basic unit if you want to set decimal for stock quantity values']);
                        }
                    }else{
                        $stockItemTemp->qty  = $request['qty'];
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
        PmPurchaseItemTemp::destroy($id);
    }

     function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}
