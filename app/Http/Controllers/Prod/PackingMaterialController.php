<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\PackingMaterial;
use App\Models\PmItem;
use App\Models\PmDamage;
use App\Models\PmUseItem;
use App\Models\UnitMeasure;
use App\Models\PmTransferItem;

class PackingMaterialController extends Controller
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
        $expired = Session::get('expired');

        $page = 'Packing Materials';
        $title = 'Packing Materials';
        $title_sw = 'Malighafi';
       
        $units = UnitMeasure::select('unit_name')->get();
        $shop = Shop::find(Session::get('shop_id'));
        $pmaterials = $shop->packingMaterials()->where('is_deleted' , false)->get();
        $products = $shop->products()->get();
        return view('production.packing-materials.index', compact('page', 'title', 'title_sw', 'pmaterials', 'units', 'shop', 'products'));
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

        $now = \Carbon\Carbon::now();
        $material = PackingMaterial::where('name', $request['name'])->where('basic_uom', $request['basic_uom'])->first();

        if (is_null($material)) {
            $material = new PackingMaterial();
            $material->company_id = Session::get('company_id');
            $material->parent_pm_id = $request['parent_pm_id'];
            $material->name = $request['name'];
            $material->basic_uom = $request['basic_uom'];
            $material->save();
        }
 
        if (!empty($request['qty'])) {
            $pmitem = new PmItem();
            $pmitem->shop_id = $shop->id;
            $pmitem->packing_material_id = $material->id;
            $pmitem->qty = $request['qty'];
            if (empty($request['unit_cost'])) {
                $pmitem->unit_cost = 0;
            }else{
                $pmitem->unit_cost = $request['unit_cost'];
            }
            $pmitem->total = $pmitem->qty*$pmitem->unit_cost;
            $pmitem->date = $now;
            $pmitem->save();
        }

        $pmshop = $shop->packingMaterials()->where('is_deleted' , false)->where('packing_material_id', $material->id)->first();

        if (is_null($pmshop)) {
            $shop->packingMaterials()->attach($material, ['in_store' => $request['qty'], 'unit_cost' => $request['unit_cost'], 'description' => $request['description']]);
        }else{
            $pmshop->pivot->is_deleted = false;
            $pmshop->pivot->save();
        }

        return redirect()->back()->with('success', 'Packing Material was registered successful');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Packing Materials';
        $title = 'Packing Material Details';
        $title_sw = 'Maelezo ya Malighafi';
        $shop = Shop::find(Session::get('shop_id'));
        $packing_material = PackingMaterial::find(decrypt($id));

        $material = $shop->packingMaterials()->where('is_deleted' , false)->where('packing_material_id', $packing_material->id)->first();
        $pmitems = PmItem::where('packing_material_id', $packing_material->id)->where('pm_items.shop_id', $shop->id)->where('pm_items.is_deleted' , false)->join('packing_materials', 'packing_materials.id', '=', 'pm_items.packing_material_id')->leftJoin('pm_purchases' , 'pm_purchases.id' , '=' , 'pm_items.pm_purchase_id')->leftJoin('suppliers' ,  'suppliers.id' , '=' , 'pm_purchases.supplier_id')->orderBy('pm_items.date', 'desc')->get([
                'pm_items.id as id',
                'packing_materials.name' , 
                'suppliers.name as sp_name',
                'total',
                'qty',
                'unit_cost',
                'pm_purchase_id',
                'purchase_type',
                'pm_items.date',
             ]);
        $pm_uses = PmUseItem::where('packing_material_id', $material->id)->where('pm_use_items.shop_id', $shop->id)->join('pm_uses'  , 'pm_uses.id' , '=' , 'pm_use_items.pm_use_id' )->get();
        $damages = PmDamage::where('packing_material_id', $material->id)->where('shop_id', $shop->id)->get();
        $pmt_items = PmTransferItem::where('packing_material_id', $material->id)->where('pm_transfer_items.shop_id', $shop->id)->join('pm_transfers', 'pm_transfers.id', '=', 'pm_transfer_items.pm_transfer_id')->select('pm_transfer_items.id as id', 'pm_transfer_id', 'pm_transfer_date', 'pmt_no', 'qty','destin_id')->get();
        $t_dam = PmDamage::where('packing_material_id', $material->id)->where('shop_id', $shop->id)->sum('quantity');
        return view('production.packing-materials.show', compact('page', 'title', 'title_sw', 'material', 'shop', 'pmitems', 't_dam', 'damages' , 'pm_uses', 'pmt_items'));
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
        $title = 'Edit Packing Material';
        $title_sw = 'Hariri Malighafi';
        
        $units = UnitMeasure::select('unit_name')->get();
        $shop = Shop::find(Session::get('shop_id'));
        $material = $shop->packingMaterials()->where('is_deleted' , false)->where('packing_material_id', decrypt($id))->first();
        $pmitem = PmItem::where('packing_material_id', $material->id)->where('shop_id', $shop->id)->first();

        $pmaterials = $shop->packingMaterials()->where('is_deleted' , false)->get();
        return view('production.packing-materials.edit', compact('page', 'title', 'title_sw', 'pmaterials', 'material', 'pmitem', 'units'));
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
        $material = PackingMaterial::find(decrypt($id));
        if (is_null($material->company_id)) {
            $material->company_id = Session::get('company_id');
        }
        $material->parent_pm_id = $request['parent_pm_id'];
        $material->name = $request['name'];
        $material->basic_uom = $request['basic_uom'];
        $material->save();

        return redirect('packing-materials')->with('success', 'Packing Material was updated successful');
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

        $material = PackingMaterial::find(decrypt($id));
        $pmaterials = $shop->packingMaterials()->where('is_deleted' , false)->where('packing_material_id' , $material->id)->get();

        if (!is_null($pmaterials)) {

            foreach($pmaterials as $value){
                $value->pivot->delete();
            }
        }

        return redirect()->back()->with('success', 'Packing Material was deleted successful');
    }

     public function deleteMultiple(Request $request){

        $shop = Shop::find(Session::get('shop_id'));

        $user = Auth::user();
        if (!empty($request->input('id'))) {
                
            foreach ($request->input('id') as $key => $id) {
                $material = PackingMaterial::find($id);
                $pmaterials = $shop->packingMaterials()->where('is_deleted' , false)->where('packing_material_id' , $material->id)->get();
                
                if (!is_null($pmaterials)) {
                    foreach($pmaterials as $value){
                        $value->pivot->delete();
                    }

                }
            }
            
            return redirect()->back()->with('success', 'Packing Material was deleted successful');  
        }else{

            $warning = 'No items selected. Please select at least one item';
            return redirect('packing-materials')->with('warning', $warning); 
        }
    }

     public function newReorderPoint(Request $request)
    {
        
        $shop = Shop::find(Session::get('shop_id'));
        $material = $shop->packingMaterials()->where('packing_material_id', $request['packing_material_id'])->first();


        if (!is_null($material)) {
            $material->pivot->reorder_point = $request['reorder_point'];
            $material->pivot->save();
        }

        return redirect()->back()->with('success', 'New Reorder Point was updated successful');
    }

    public function newBuyPrice(Request $request)
    {

        $shop = Shop::find(Session::get('shop_id'));
        $material = $shop->packingMaterials()->where('packing_material_id', $request['packing_material_id'])->first();

         if (!is_null($material)) {
            $material->pivot->unit_cost = $request['unit_cost'];
            $material->pivot->save();
        }

        $message = 'Price was successfully updated';

        return redirect()->route('packing-materials.show', encrypt($material->id))->with('message', $message);

    }
}
