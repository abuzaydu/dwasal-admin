<?php

namespace App\Http\Controllers\Prod\PC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IndirectCost;

class IndirectCostController extends Controller
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
        $indirectcost = new IndirectCost();
        $indirectcost->product_pricing_id = $request['pricing_id'];
        $indirectcost->save();

        return $indirectcost;
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
        $indirectcost = IndirectCost::find($id);
        $indirectcost->description = $request['description'];
        $indirectcost->percent = $request['percent'];
        $indirectcost->amount = $request['amount'];
        $indirectcost->save();

        return $indirectcost;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Indirectcost::destroy($id);
    }
}
