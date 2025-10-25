<?php

namespace App\Http\Controllers\Prod\PC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductPricing;
use App\Models\MaterialCost;
use App\Models\LabourCost;
use App\Models\TransportationCost;
use App\Models\IndirectCost;
use App\Models\LocalIndirectCost;
use App\Models\PackagingCost;
use App\Models\LocalPackagingCost;
use App\Models\ExportHandlingCost;

class MaterialCostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $pricing = ProductPricing::find($id);
        $materialcosts = MaterialCost::where('product_pricing_id', $pricing->id)->orderBy('id', 'desc')->get();
        $labourcosts = LabourCost::where('product_pricing_id', $pricing->id)->get();
        $transportcosts = TransportationCost::where('product_pricing_id', $pricing->id)->get();
        $indirectcosts = IndirectCost::where('product_pricing_id', $pricing->id)->get();
        $localindirectcosts = LocalIndirectCost::where('product_pricing_id', $pricing->id)->get();
        $packagecosts = PackagingCost::where('product_pricing_id', $pricing->id)->get();
        $localpackagecosts = LocalPackagingCost::where('product_pricing_id', $pricing->id)->get();
        $handlingcosts = ExportHandlingCost::where('product_pricing_id', $pricing->id)->get();

        return response()->json(['pricing' => $pricing, 'materialcosts' => $materialcosts, 'labourcosts' => $labourcosts, 'transportcosts' => $transportcosts, 'indirectcosts' => $indirectcosts, 'localindirectcosts' => $localindirectcosts, 'packagecosts' => $packagecosts, 'localpackagecosts' => $localpackagecosts, 'handlingcosts' => $handlingcosts]);
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
        $mcost = MaterialCost::find($id);
        $mcost->unit_cost = $request['unit_cost'];
        $mcost->no_of_piece_made = $request['no_of_piece_made'];
        $mcost->cost_per_piece = $mcost->unit_cost/$mcost->no_of_piece_made;
        $mcost->save();

        return $mcost;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        MaterialCost::destroy($id);
    }
}
