<?php

namespace App\Http\Controllers\Prod\PC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LabourCost;

class LabourCostController extends Controller
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
        $labourcost = new LabourCost();
        $labourcost->product_pricing_id = $request['product_pricing_id'];
        $labourcost->save();

        return $labourcost;
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
        $labourcost = LabourCost::find($id);
        $labourcost->stage = $request['stage'];
        $labourcost->daily_wage_rate = $request['daily_wage_rate'];
        $labourcost->no_of_piece = $request['no_of_piece'];
        $labourcost->cost_per_piece = $labourcost->daily_wage_rate/$labourcost->no_of_piece;
        $labourcost->save();

        return $labourcost;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        LabourCost::destroy($id);
    }
}
