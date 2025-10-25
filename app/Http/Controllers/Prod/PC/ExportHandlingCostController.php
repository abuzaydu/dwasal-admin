<?php

namespace App\Http\Controllers\Prod\PC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExportHandlingCost;

class ExportHandlingCostController extends Controller
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
        $handlingcost = new ExportHandlingCost();
        $handlingcost->product_pricing_id = $request['pricing_id'];
        $handlingcost->save();

        return $handlingcost;
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
        $handlingcost = ExportHandlingCost::find($id);
        $handlingcost->description = $request['description'];
        $handlingcost->amount = $request['amount'];
        $handlingcost->save();

        return $handlingcost;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ExportHandlingCost::destroy($id);
    }
}
