<?php

namespace App\Http\Controllers\Prod\PC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PackagingCost;

class PackagingCostController extends Controller
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
        $packagecost = new PackagingCost();
        $packagecost->product_pricing_id = $request['pricing_id'];
        $packagecost->save();

        return $packagecost;
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
        $packagecost = PackagingCost::find($id);
        $packagecost->item_desc = $request['item_desc'];
        $packagecost->package_cost = $request['package_cost'];
        $packagecost->no_of_items = $request['no_of_items'];
        $packagecost->unit_cost = $packagecost->package_cost/$packagecost->no_of_items;
        $packagecost->save();

        return $packagecost;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        PackagingCost::destroy($id);
    }
}
