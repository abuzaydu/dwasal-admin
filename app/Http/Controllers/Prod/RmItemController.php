<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\RmItem;
use App\Models\RmPurchase;
use App\Models\RawMaterial;
use App\Models\RmUseItem;
use App\Models\RmDamage;

class RmItemController extends Controller
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
        //
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
        $shop = Shop::find(Session::get('shop_id'));
        $rmitem = RmItem::where('id', decrypt($id))->where('shop_id', $shop->id)->where('is_deleted' , false)->first();
        if (is_null($rmitem)) {
            return redirect('forbiden');
        }else{
            $raw_material = RawMaterial::find($rmitem->raw_material_id);
            $page = 'Raw Materials';
            $title = 'Edit Stock';
            $title_sw = 'Hariri Stock';

            return view('production.raw-materials.edit-rmitem', compact('page', 'title', 'title_sw', 'rmitem', 'raw_material', 'shop'));
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
        $rmitem = RmItem::find($request['id']);
        $rmitem->qty = $request['qty'];
        $rmitem->unit_cost = $request['unit_cost'];
        $rmitem->save();

        $raw_material = RawMaterial::find($rmitem->raw_material_id);
        $shop_raw_material = $shop->rawMaterials()->where('is_deleted' , false)->where('raw_material_id', $raw_material->id)->first();

        
        $purchased = RmItem::where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->where('shop_id', $shop->id)->sum('qty');           
        $used = RmUseItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
        $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');

        $instore = $purchased-($used+$damaged); 
        if(!is_null($shop_raw_material)){
            $shop_raw_material->pivot->in_store = $instore;
            $shop_raw_material->pivot->save();
        }
       
        
        $purchase = RmPurchase::where('id', $rmitem->rm_purchase_id)->where('shop_id', $shop->id)->where('is_deleted' , false)->first();
        if (!is_null($purchase)) {
            $pitems = RmItem::where('rm_purchase_id', $purchase->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->get();
            $total_amount = 0;
            foreach ($pitems as $key => $item) {
                $total_amount += ($item->qty*$item->unit_cost);
            }

            $purchase->total_amount = $total_amount;
            $purchase->save();

        }

        $message = 'Material Stock was successfully updated';
        return redirect()->route('raw-materials.show' , encrypt($raw_material->id))->with('message', $message);
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
        $rmitem = RmItem::find(decrypt($id));

        if (!is_null($rmitem) && !is_null($shop)) {
                
            $raw_material = RawMaterial::find($rmitem->raw_material_id);
            $rmitem->is_deleted = true;
            $rmitem->save();

            $purchased = RmItem::where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->where('shop_id', $shop->id)->sum('qty');           
            $used = RmUseItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
            $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');

            $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();
            $instore = $purchased-($used+$damaged);
            if(!is_null($shop_raw_material)){
                $shop_raw_material->pivot->in_store = $instore;
                $shop_raw_material->pivot->save();
            } 
            
            
            $purchase = RmPurchase::where('id', $rmitem->rm_purchase_id)->where('shop_id', $shop->id)->where('is_deleted' , false)->first();
            if (!is_null($purchase)) {
                $pitems = RmItem::where('rm_purchase_id', $purchase->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->get();
                if ($pitems->count() > 0) {
                     
                    $total_amount = 0;
                    foreach ($pitems as $key => $item) {
                        $total_amount += ($item->qty*$item->unit_cost);
                    }

                    $purchase->total_amount = $total_amount;
                    $purchase->save();
                }else{
                    $purchase->is_deleted = true;
                    $purchase->save();
                }
            }
            return redirect()->back()->with('success', 'The item was deleted successfully');
        }else{
            return redirect()->back()->with('warning', 'Oops! The item your trying to access is no available');
        }
    }
}
