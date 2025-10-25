<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\WipTemp;

class WIPTempController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $temps = WipTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->join('products', 'products.id', '=', 'wip_temps.product_id')->select('wip_temps.id as id','name', 'bf_balance', 'produced', 'finished_qty', 'wip_damage', 'closing_qty')->get();
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
        $wiptemp = WipTemp::find($id);
        $wiptemp->produced = $request['produced'];
        $wiptemp->finished_qty = $request['finished_qty'];
        $wiptemp->wip_damage = $request['wip_damage'];
        $wiptemp->closing_qty = ($wiptemp->bf_balance+$wiptemp->produced)-($wiptemp->finished_qty+$wiptemp->wip_damage);
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
