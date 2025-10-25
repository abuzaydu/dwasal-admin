<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\PackingMaterial;
use App\Models\PmDamage;
use App\Models\PmItem;
use App\Models\PmUseItem;


class PmDamageController extends Controller
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user(); 

        $packing_material = $shop->packingMaterials()->where('packing_material_id' ,$request['packing_material_id'])->first();
        $pmdamage = PmDamage::create([
            'shop_id' => $shop->id,
            'user_id' => $user->id,
            'packing_material_id' => $packing_material->id,
            'quantity' => $request['quantity'], 
            'unit_cost' => $packing_material->pivot->unit_cost,
            'reason' => $request['reason']
        ]);

        if ($pmdamage) {
            $shop_packing_material = $shop->packingMaterials()->where('packing_material_id', $packing_material->id)->first();

            
            $purchased = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('qty');           
            $used = PmUseItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');
            $damaged = PmDamage::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');

            $instore = $purchased-($used+$damaged); 
            $shop_packing_material->pivot->in_store = $instore;
            $shop_packing_material->pivot->save();
        }

        return redirect()->back()->with('success', 'Packing Materials Damage was recorded successfully');
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
        $page = 'Packing Materials';
        $title = 'Edit Packing Materials Damage';
        $title_sw = 'Hariri Vifungashio iliyoharibika';
        $pmdamage = PmDamage::find(decrypt($id));
        $packing_material = PackingMaterial::find($pmdamage->packing_material_id);
        
        return view('production.packing-materials.damages.edit', compact('page', 'title', 'title_sw', 'pmdamage', 'packing_material'));
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
        $pmdamage = PmDamage::find(decrypt($id));
        $pmdamage->quantity = $request['quantity'];
        $pmdamage->unit_cost = $request['unit_cost'];
        $pmdamage->reason = $request['reason'];
        $pmdamage->save();
        
        $packing_material = PackingMaterial::find($pmdamage->packing_material_id);
        $purchased = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');           
        $used = PmUseItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');
        $damaged = PmDamage::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');

        $shop_packing_material = $shop->packingMaterials()->where('packing_material_id', $packing_material->id)->first();
        $instore = $purchased-($used+$damaged); 
        $shop_packing_material->pivot->in_store = $instore;
        $shop_packing_material->pivot->save();
            
        return redirect()->route('packing-materials.show', encrypt($packing_material->id))->with('success', 'Packing Materials damage was updated successfully');
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
        $pmdamage = PmDamage::find(decrypt($id));
        if (!is_null($pmdamage) && !is_null($shop)) {
            $packing_material = PackingMaterial::find($pmdamage->packing_material_id);
            $pmdamage->delete();

            $purchased = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');           
            $used = PmUseItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');
            $damaged = PmDamage::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');

            $shop_packing_material = $shop->packingMaterials()->where('packing_material_id', $packing_material->id)->first();
            $instore = $purchased-($used+$damaged); 
            $shop_packing_material->pivot->in_store = $instore;
            $shop_packing_material->pivot->save();
            
            return redirect()->back()->with('success', 'The item was deleted successfully');
        }else{
            return redirect()->back()->with('warning', 'Oops! The item your trying to access is no available');
        }
    }
}
