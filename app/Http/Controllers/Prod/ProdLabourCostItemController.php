<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\PlcItem;
use App\Models\ProdLabourCost;
use Log;

class ProdLabourCostItemController extends Controller
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $plc = ProdLabourCost::find($request['prod_labour_cost_id']);
        $item = PlcItem::where('prod_labour_cost_id', $plc->id)->where('production_stage_id', $request['production_stage_id'])->first();
        if (is_null($item)) {
            $item = new PlcItem;
            $item->shop_id = $plc->shop_id;
            $item->user_id = $plc->user_id;
            $item->prod_labour_cost_id = $plc->id;
            $item->production_stage_id = $request['production_stage_id'];
            $item->quantity = 0;
            $item->unit_cost = 0;
            $item->total = $item->quantity*$item->unit_cost;
            $item->save();
            
            return redirect()->route('prod-labour-costs.edit', encrypt($plc->id))->with('success', 'Item added successfully');
        }else{
            return redirect()->route('prod-labour-costs.edit', encrypt($plc->id))->with('info', 'Item already selected');
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
        $item = PlcItem::find(decrypt($id));

        return view('production.labour-costs.items.edit', compact('page', 'title', 'item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $item = PlcItem::find($request['id']);
        if (!is_null($item)) {
                
            if ($request['field_name'] == 'quantity') {
                $item->quantity = $request['value'];
                $item->total = $item->unit_cost*$item->quantity;
            }elseif($request['field_name'] == 'unitcost'){
                $item->unit_cost = $request['value'];
                $item->total = $item->unit_cost*$item->quantity;
            }elseif ($request['field_name'] == 'total') {
                $item->total = $request['value'];
                $item->unit_cost = $item->total/$item->quantity;
            }
            $item->save();

            $plc = ProdLabourCost::find($item->prod_labour_cost_id);
            $items = PlcItem::where('prod_labour_cost_id', $plc->id)->get();
            $amount =0;
            foreach ($items as $key => $item) {
                $amount += $item->total;
            }

            $plc->amount = $amount;
            $plc->save();

            return response()->json(['success' => 1, 'msg' => 'Item Updated Successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item Not found']);
        }

        // return redirect()->route('prod-labour-costs.show', encrypt($plc->id))->with('success', 'Labour Cost Item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = PlcItem::find(decrypt($id));
        if (!is_null($item)) {
            $plc = ProdLabourCost::find($item->prod_labour_cost_id);
            $item->delete();
            $items = PlcItem::where('prod_labour_cost_id', $plc->id)->get();
            $amount =0;
            foreach ($items as $key => $item) {
                $amount += $item->total;
            }
            
            $plc->amount = $amount;
            $plc->save();

            return redirect()->route('prod-labour-costs.edit', encrypt($plc->id))->with('success', 'Labour Cost Item deleted successfully');
        }else{
            return redirect()->with('error', 'Labour Cost Item found');
        }
    }
}
