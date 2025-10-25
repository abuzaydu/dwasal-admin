<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\MaterialWipStockTemp;

class MaterialWIPTempController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $temps = MaterialWipStockTemp::where('material_wip_stock_temps.shop_id', $shop->id)->where('user_id', $user->id)->join('material_wips', 'material_wips.id', '=', 'material_wip_stock_temps.material_wip_id')->select('material_wip_stock_temps.id as id','title', 'opening_qty', 'produced', 'used', 'dam_qty', 'closing_qty')->get();
        return response()->json(['temps' => $temps]);
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
        //
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
        $wiptemp = MaterialWipStockTemp::find($id);
        $wiptemp->produced = $request['produced'];
        $wiptemp->used = $request['used'];
        $wiptemp->dam_qty = $request['dam_qty'];
        $wiptemp->closing_qty = ($wiptemp->opening_qty+$wiptemp->produced)-($wiptemp->used+$wiptemp->dam_qty);
        $wiptemp->save();

        return $wiptemp;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        WipTemp::destroy($id);
    }
}