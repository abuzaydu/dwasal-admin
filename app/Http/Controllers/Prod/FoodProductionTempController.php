<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Log;
use App\Models\Shop;
use App\Models\RmUseItemTemp;

class FoodProductionTempController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $rms = $shop->rawMaterials()->where('is_deleted' , false)->get([
            \DB::raw('raw_material_id as id'),
            \DB::raw('name'),
            \DB::raw('in_store'),
            \DB::raw('unit_cost'),
            \DB::raw('description')]);
        $temps = RmUseItemTemp::where('rm_use_item_temps.shop_id', $shop->id)->where('user_id', Auth::user()->id)->where('is_food_production', true)->join('raw_materials', 'raw_materials.id', '=', 'rm_use_item_temps.raw_material_id')->select('rm_use_item_temps.id as id', 'rm_use_item_temps.quantity as quantity', 'rm_use_item_temps.unit_cost as unit_cost', 'rm_use_item_temps.total as total', 'raw_materials.name as name', 'basic_uom', 'rm_use_item_temps.created_at as created_at')->orderBy('created_at', 'desc')->get();

        return response()->json(['rms' => $rms, 'temps' => $temps]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $rmuItemTemp = RmUseItemTemp::where('raw_material_id', $request['rm_id'])->where('user_id', $user->id)->where('shop_id', $shop->id)->where('is_food_production', true)->first();
        if (is_null($rmuItemTemp)) {
            $raw_material = $shop->rawMaterials()->where('raw_material_id', $request['rm_id'])->where('is_deleted' , false)->first();
            if (!is_null($raw_material)) {
                $rmuItemTemp = new RmUseItemTemp;
                $rmuItemTemp->shop_id = $shop->id;
                $rmuItemTemp->user_id = $user->id;
                $rmuItemTemp->raw_material_id = $request['rm_id'];
                $rmuItemTemp->quantity  = 1;
                $rmuItemTemp->unit_cost = is_null($raw_material->pivot->unit_cost) ? 0 : $raw_material->pivot->unit_cost;
                $rmuItemTemp->total = $rmuItemTemp->unit_cost;
                $rmuItemTemp->is_food_production = true;
                $rmuItemTemp->save();
                return $rmuItemTemp;
            }else{
                Log::info($request['rm_id']);
            }                
        }else{
            $rmuItemTemp->quantity =  $rmuItemTemp->quantity + 1;
            $rmuItemTemp->total = ($rmuItemTemp->quantity * $rmuItemTemp->unit_cost);
            $rmuItemTemp->save();
            return $rmuItemTemp;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $stockItemTemp =  RmUseItemTemp::where('id', $id)->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();
        if (!is_null($stockItemTemp)) {
            $stockItemTemp->quantity  = $request['quantity'];
            $stockItemTemp->total = $stockItemTemp->quantity*$stockItemTemp->unit_cost;
            $stockItemTemp->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        RmUseItemTemp::destroy($id);
    }
}
