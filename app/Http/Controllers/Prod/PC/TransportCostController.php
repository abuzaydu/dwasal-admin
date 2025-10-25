<?php

namespace App\Http\Controllers\Prod\PC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransportationCost;

class TransportCostController extends Controller
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
        $transpoertcost = new TransportationCost();
        $transpoertcost->product_pricing_id = $request['pricing_id'];
        $transpoertcost->save();

        return $transpoertcost;
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
        $transpoertcost = TransportationCost::find($id);
        $transpoertcost->description = $request['description'];
        $transpoertcost->transport_cost = $request['transport_cost'];
        $transpoertcost->no_of_items = $request['no_of_items'];
        $transpoertcost->cost_per_unit = $transpoertcost->transport_cost/$transpoertcost->no_of_items;
        $transpoertcost->save();

        return $transpoertcost;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        TransportationCost::destroy($id);

        return response()->json(['status' => 'Removed']);
    }
}
