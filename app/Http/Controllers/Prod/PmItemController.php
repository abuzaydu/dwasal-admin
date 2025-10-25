<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use App\Models\Shop;
use App\Models\PmItem;
use App\Models\PmPurchase;
use App\Models\PackingMaterial;
use App\Models\PmUseItem;
use App\Models\PmDamage;
use App\Models\PmTransferItem;
use App\Jobs\PMUpdaterJob;

class PmItemController extends Controller
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
        $pmitem = PmItem::where('id', decrypt($id))->where('shop_id', $shop->id)->where('is_deleted' , false)->first();
        if (is_null($pmitem)) {
            return redirect('forbiden');
        }else{
            $packing_material = PackingMaterial::find($pmitem->packing_material_id);
            $page = 'Packing Materials';
            $title = 'Edit Stock';
            $title_sw = 'Hariri Stock';

            return view('production.packing-materials.edit-pmitem', compact('page', 'title', 'title_sw', 'pmitem', 'packing_material', 'shop'));
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
        $pmitem = PmItem::find(decrypt($id));
        $pmitem->qty = $request['qty'];
        $pmitem->unit_cost = $request['unit_cost'];
        $pmitem->save();
 
        dispatch(new PMUpdaterJob($pmitem->packing_material_id, $shop));

        $purchase = PmPurchase::where('id', $pmitem->pm_purchase_id)->where('shop_id', $shop->id)->where('is_deleted' , false)->first();
        if (!is_null($purchase)) {
            $pitems = PmItem::where('pm_purchase_id', $purchase->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->get();
            $total_amount = 0;
            foreach ($pitems as $key => $item) {
                $total_amount += ($item->qty*$item->unit_cost);
            }

            $purchase->total_amount = $total_amount;
            $purchase->save();

        }

        $message = 'Material Stock was successfully updated';
        return redirect()->route('packing-materials.show', encrypt($pmitem->packing_material_id))->with('message', $message);   
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
        $pmitem = PmItem::find(decrypt($id));
        if (!is_null($pmitem) && !is_null($shop)) {
                
            $packing_material = PackingMaterial::find($pmitem->packing_material_id);
            $pmitem->is_deleted = true;
            $pmitem->save();
            
            dispatch(new PMUpdaterJob($pmitem->packing_material_id, $shop));

            $purchase = PmPurchase::where('id', $pmitem->pm_purchase_id)->where('shop_id', $shop->id)->where('is_deleted' , false)->first();
            if (!is_null($purchase)) {
                $pitems = PmItem::where('pm_purchase_id', $purchase->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->get();
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
