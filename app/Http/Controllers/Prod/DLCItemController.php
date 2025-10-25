<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use App\Models\ProductionCost;
use App\Models\DlcItem;
use App\Models\DirectLabourCost;
use App\Models\ProductionStage;


class DLCItemController extends Controller
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
        $prod_cost = ProductionCost::find($request['production_cost_id']);
        if (!is_null($prod_cost)) {
            $shop = Shop::find($prod_cost->shop_id);
            $user = User::find($prod_cost->user_id);
            $lcost = DirectLabourCost::where('production_cost_id', $prod_cost->id)->first();
            if (is_null($lcost)) {
                $lcost = new DirectLabourCost();    
                $lcost->shop_id = $shop->id;
                $lcost->user_id = $user->id;
                $lcost->production_cost_id = $prod_cost->id;
                $lcost->total_cost = 0;
                $lcost->date = $prod_cost->date;
                $lcost->prod_batch = $prod_cost->prod_batch;
                $lcost->save();
            }

            $lci = DlcItem::where('direct_labour_cost_id', $lcost->id)->where('production_stage_id', $request['production_stage_id'])->first();
            if (is_null($lci)) {
                $lci = new DlcItem;
                $lci->shop_id = $shop->id;
                $lci->direct_labour_cost_id = $lcost->id;
                $lci->production_stage_id = $request['production_stage_id'];
                $lci->qty = $request['qty'];
                $lci->unit_cost = $request['unit_cost'];
                $lci->total = $lci->qty*$lci->unit_cost;
                $lci->date = $prod_cost->date;
                $lci->save();
            }

            $total_cost = 0;
            $items = DlcItem::where('direct_labour_cost_id', $lcost->id)->get();
            foreach ($items as $key => $value) {
                $total_cost += $value->total;
            }

            $lcost->total_cost = $total_cost;
            $lcost->save();

            return redirect()->route('prod-costs.edit', encrypt($prod_cost->id))->with('Labour Cost Item added successfully');
        }
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
        $page = 'Edit Labour Cost Item';
        $title = 'Edit Labour Cost Item';
        $lci = DlcItem::find(decrypt($id));
        $stages = ProductionStage::where('shop_id', $lci->shop_id)->get();

        return view('production.labour-costs.items.edit', compact('page', 'title', 'lci', 'stages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lci = DlcItem::find(decrypt($id));
        if (!is_null($lci)) {
            $dlc = DirectLabourCost::find($lci->direct_labour_cost_id);
            $lci->production_stage_id = $request['production_stage_id'];
            $lci->qty = $request['qty'];
            $lci->unit_cost = $request['unit_cost'];
            $lci->total = $lci->qty*$lci->unit_cost;
            $lci->save();

            $total_cost = 0;
            $items = DlcItem::where('direct_labour_cost_id', $dlc->id)->get();
            foreach ($items as $key => $value) {
                $total_cost += $value->total;
            }

            $dlc->total_cost = $total_cost;
            $dlc->save();
            
            return redirect()->route('prod-costs.edit', encrypt($dlc->production_cost_id))->with('success', 'Labour Cost Item Updated Successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DlcItem::destroy(decrypt($id));

        return redirect()->back();
    }
}
