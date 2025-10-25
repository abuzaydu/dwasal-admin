<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Response;
use Auth;
use App\Models\Shop;
use App\Models\Mro;
use App\Models\MohItemTemp;
use App\Models\MohItem;
use Log;

class MohCostTempController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $mros = Mro::where('shop_id', $shop->id)->select('id', 'name')->get();
        $temps = MohItemTemp::where('moh_item_temps.shop_id', $shop->id)->where('user_id', Auth::user()->id)->join('mros', 'mros.id', '=', 'moh_item_temps.mro_id')->select('moh_item_temps.id as id', 'name', 'quantity', 'unit_cost', 'total')->get();

        return Response::json(['mros' => $mros, 'temps' => $temps]);
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
        $itemTemp = MohItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('mro_id', $request['mro_id'])->first();
        if (is_null($itemTemp)) {
            $lastitem = MohItem::where('shop_id', $shop->id)->where('mro_id', $request['mro_id'])->latest()->first();
            $itemTemp = new MohItemTemp;
            $itemTemp->shop_id = $shop->id;
            $itemTemp->user_id = $user->id;
            $itemTemp->mro_id = $request['mro_id'];
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
        $itemTemp = MohItemTemp::find($id);
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
        MohItemTemp::destroy($id);
    }
}
