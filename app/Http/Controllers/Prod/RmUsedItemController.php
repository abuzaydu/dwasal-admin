<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\User;
use App\Models\ProductionCost;
use App\Models\RmUse;
use App\Models\RmItem;
use App\Models\RawMaterial;
use App\Models\RmUseItem;
use App\Models\RmDamage;


class RmUsedItemController extends Controller
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
        $rm = $shop->rawMaterials()->where('raw_material_id' , $request['raw_material_id'])->first();

        if($rm->pivot->in_store < $request['quantity']){
            return redirect()->route('prod-costs.edit', encrypt($prod_cost->id))->with('error' , "Stock Available is less than edited amount");
        }else{
            $rmuse = RmUse::where('production_cost_id', $prod_cost->id)->first();
            if (is_null($rmuse)) {
                $rmuse = new RmUse();
                $rmuse->shop_id = $shop->id;
                $rmuse->user_id = $user->id;
                $rmuse->production_cost_id = $prod_cost->id;
                $rmuse->total_cost = 0;
                $rmuse->date = $prod_cost->date;
                $rmuse->prod_batch = $prod_cost->prod_batch;
                $rmuse->save();
            }

            $useditem = RmUseItem::where('rm_use_id', $rmuse->id)->first();
            if (is_null($useditem)) {
                $useditem  = new RmUseItem;
                $useditem->rm_use_id = $rmuse->id;;
                $useditem->shop_id = $shop->id;
                $useditem->raw_material_id = $rm->pivot->raw_material_id;
                $useditem->quantity = $request['quantity'];
                $useditem->unit_cost = $rm->pivot->unit_cost;
                $useditem->total = $useditem->unit_cost*$useditem->quantity;
                $useditem->date = $prod_cost->date;
                $useditem->save();
            }

            $uitems = RmUseItem::where('rm_use_id', $rmuse->id)->get();
            $total_cost = 0;
            foreach ($uitems as $key => $item) {
                
                $total_cost += $item->total;
                $raw_material = RawMaterial::find($item->raw_material_id);
                $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();
                $purchased = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');
                $used = RmUseItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
                $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
                                        
                $instore = $purchased-($damaged+$used); 
                                 
                $shop_raw_material->pivot->in_store = $instore;                    
                $shop_raw_material->pivot->save();
            }

            $rmuse->total_cost = $total_cost;
            $rmuse->save();

            return redirect()->route('prod-costs.edit', encrypt($rmuse->production_cost_id))->with('success', 'Raw Materials used was updated successfully');
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
         $page = 'Raw Materials';
        $title = 'Edit Raw Materials used';
        $title_sw = 'Hariri Malighafi iliyotumika';
        $rmuseditem = RmUseItem::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        $raw_materials = $shop->rawMaterials()->where('is_deleted' , false)->get();
        return view('production.raw-materials.rm-uses.edit-used-item', compact('page', 'title', 'title_sw', 'rmuseditem', 'raw_materials'));
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
        $rmuseditem = RmUseItem::find(decrypt($id));

        $rm = $shop->rawMaterials()->where('raw_material_id' , $rmuseditem->raw_material_id)->first();

        if( ($rm->pivot->in_store + $rmuseditem->quantity)-($request['quantity']) < 0 ){
            return redirect()->route('rm-uses.show', encrypt($rmuseditem->rm_use_id))->with('error' , "Stock Available is less than edited amount");
        }else{

            $rmuseditem->raw_material_id = $request['raw_material_id'];
            $rmuseditem->quantity = $request['quantity'];
            $rmuseditem->total = $rmuseditem->unit_cost*$request['quantity'];
            $rmuseditem->save();

            $uitems = RmUseItem::where('rm_use_id', $rmuseditem->rm_use_id)->get();
            $total_cost = 0;
            foreach ($uitems as $key => $item) {
                
                $total_cost += $item->total;
                $raw_material = RawMaterial::find($item->raw_material_id);
                $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();
                $purchased = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');
                $used = RmUseItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
                $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
                                        
                $instore = $purchased-($damaged+$used); 
                                 
                $shop_raw_material->pivot->in_store = $instore;                    
                $shop_raw_material->pivot->save();
            }

            $rmuse = RmUse::find($rmuseditem->rm_use_id);
            $rmuse->total_cost = $total_cost;
            $rmuse->save();

            return redirect()->route('prod-costs.edit', encrypt($rmuse->production_cost_id))->with('success', 'Raw Materials used was updated successfully');
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
        $shop = Shop::find(Session::get('shop_id'));
        $rmitem = RmUseItem::find(decrypt($id));
        $rmuse = RmUse::find($rmitem->rm_use_id);

        $rm = $shop->rawMaterials()->where('raw_material_id' , $rmitem->raw_material_id)->first();
        $rm->pivot->in_store =  $rm->pivot->in_store + $rmitem->quantity;
        $rm->pivot->save();
        $rmitem->delete();

         $total_use = RmUseItem::where('shop_id' , $shop->id)->where('rm_use_id' , $rmuse->id)->sum('total');
          $rmuse->total_cost = $total_use;
          $rmuse->save();

        $left_rmitem = RmUseItem::where('shop_id' , $shop->id)->where('rm_use_id' , $rmuse->id)->first();

        if(is_null($left_rmitem)){
            $rmuse->delete();
        }

        return redirect()->route('prod-costs.edit', encrypt($rmuse->production_cost_id))->with('Success' , "Item Deleted Successful");
    }
}
