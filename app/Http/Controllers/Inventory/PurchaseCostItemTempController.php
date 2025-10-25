<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseCostItemTemp;

class PurchaseCostItemTempController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $costtemp = new PurchaseCostItemTemp();
        $costtemp->purchase_temp_id = $request['purchase_temp_id'];
        $costtemp->save();

        return $costtemp;
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
        $costtemp = PurchaseCostItemTemp::find($id);
        $costtemp->item_desc = $request['item_desc'];
        if ($costtemp->percent != $request['percent']) {
            $costtemp->percent = $request['percent'];
            $costtemp->amount = round(($costtemp->percent/100)*$request['total_amount'], 2);
        }elseif ($costtemp->amount != $request['amount']) {
            $costtemp->amount = $request['amount'];
            $costtemp->percent = round(($costtemp->amount/$request['total_amount'])*100, 2);
        }
        $costtemp->save();

        return $costtemp;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        PurchaseCostItemTemp::destroy($id);
    }
}
