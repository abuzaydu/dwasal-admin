<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Response;
use Auth;
use App\Models\Shop;
use App\Models\ProductionStage;
use App\Models\PlcItemTemp;
use App\Models\PlcItem;
use Log;

class ProdLabourCostTempController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $stages = ProductionStage::where('shop_id', $shop->id)->select('id', 'stage')->get();
        $temps = PlcItemTemp::where('plc_item_temps.shop_id', $shop->id)->where('user_id', Auth::user()->id)->join('production_stages', 'production_stages.id', '=', 'plc_item_temps.production_stage_id')->select('plc_item_temps.id as id', 'stage', 'quantity', 'unit_cost', 'total')->get();

        return Response::json(['stages' => $stages, 'temps' => $temps]);
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
        $itemTemp = PlcItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('production_stage_id', $request['production_stage_id'])->first();
        if (is_null($itemTemp)) {
            $lastitem = PlcItem::where('shop_id', $shop->id)->where('production_stage_id', $request['production_stage_id'])->latest()->first();
            $itemTemp = new PlcItemTemp;
            $itemTemp->shop_id = $shop->id;
            $itemTemp->user_id = $user->id;
            $itemTemp->production_stage_id = $request['production_stage_id'];
            if (!is_null($lastitem)) {
                $itemTemp->unit_cost = $lastitem->unit_cost;
                $itemTemp->total = $itemTemp->unit_cost;
            }
            $itemTemp->save();
        }

        return $itemTemp;
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
        Log::info($request);
        $itemTemp = PlcItemTemp::find($id);
        if ($itemTemp->quantity != $request['quantity']) {
            $itemTemp->quantity = $request['quantity'];
            $itemTemp->total = $itemTemp->unit_cost*$itemTemp->quantity;
        }elseif($itemTemp->unit_cost != $request['unit_cost']){
            $itemTemp->unit_cost = $request['unit_cost'];
            $itemTemp->total = $itemTemp->unit_cost*$itemTemp->quantity;
        }elseif ($itemTemp->total != $request['total']) {
            $itemTemp->total = $request['total'];
            $itemTemp->unit_cost = $itemTemp->total/$itemTemp->quantity;
        }
        $itemTemp->save();

        return $itemTemp;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        PlcItemTemp::destroy($id);
    }
}
