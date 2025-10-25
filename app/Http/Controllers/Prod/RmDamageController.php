<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\RawMaterial;
use App\Models\RmDamage;
use App\Models\RmItem;
use App\Models\RmUseItem;

class RmDamageController extends Controller
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

        $raw_material = $shop->rawMaterials()->where('raw_material_id' ,$request['raw_material_id'])->first();
        $rmdamage = RmDamage::create([
            'shop_id' => $shop->id,
            'user_id' => $user->id,
            'raw_material_id' => $raw_material->id,
            'quantity' => $request['quantity'], 
            'unit_cost' => $raw_material->pivot->unit_cost,
            'reason' => $request['reason']
        ]);

        if ($rmdamage) {
            $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->first();

            
            $purchased = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('qty');           
            $used = RmUseItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
            $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');

            $instore = $purchased-($used+$damaged); 
            $shop_raw_material->pivot->in_store = $instore;
            $shop_raw_material->pivot->save();
        }

        return redirect()->back()->with('success', 'Raw Materials Damage was recorded successfully');
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
        $title = 'Edit Raw Materials Damage';
        $title_sw = 'Hariri Malighafi iliyoharibika';
        $rmdamage = RmDamage::find(decrypt($id));
        $raw_material = RawMaterial::find($rmdamage->raw_material_id);
        
        return view('production.raw-materials.damages.edit', compact('page', 'title', 'title_sw', 'rmdamage', 'raw_material'));
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
        $rmdamage = RmDamage::find(decrypt($id));
        $rmdamage->quantity = $request['quantity'];
        $rmdamage->unit_cost = $request['unit_cost'];
        $rmdamage->reason = $request['reason'];
        $rmdamage->save();
        
        $raw_material = RawMaterial::find($rmdamage->raw_material_id);
        $purchased = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('qty');           
        $used = RmUseItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
        $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');

        $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->first();
        $instore = $purchased-($used+$damaged); 
        $shop_raw_material->pivot->in_store = $instore;
        $shop_raw_material->pivot->save();
            
        return redirect()->route('raw-materials.show', encrypt($raw_material->id))->with('success', 'Raw Materials damage was updated successfully');
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
        $rmdamage = RmDamage::find(decrypt($id));
        if (!is_null($rmdamage) && !is_null($shop)) {
            $raw_material = RawMaterial::find($rmdamage->raw_material_id);
            $rmdamage->delete();

            $purchased = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('qty');           
            $used = RmUseItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
            $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');

            $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->first();
            $instore = $purchased-($used+$damaged); 
            $shop_raw_material->pivot->in_store = $instore;
            $shop_raw_material->pivot->save();
            
            return redirect()->back()->with('success', 'The item was deleted successfully');
        }else{
            return redirect()->back()->with('warning', 'Oops! The item your trying to access is no available');
        }
    }
}
