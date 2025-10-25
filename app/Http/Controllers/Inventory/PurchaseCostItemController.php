<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseCostItem;
use App\Models\Stock;

class PurchaseCostItemController extends Controller
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
        $purchase = Purchase::find($request['purchase_id']);
        if (!is_null($purchase)) {
                
            $costitem = new PurchaseCostItem();
            $costitem->purchase_id = $purchase->id;
            $costitem->item_desc = $request['item_desc'];
            $costitem->amount = $request['amount'];
            $costitem->percent = round(($costitem->amount/$purchase->total_amount)*100, 2);
            $costitem->save();

            $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
            $total_cost = 0;
            foreach ($costitems as $key => $item) {
                $total_cost += $item->amount;
            }

            $purchase->total_cost = $total_cost;
            $purchase->save();

            $total_unit_cost = 0;
            $pitems = Stock::where('purchase_id', $purchase->id)->get();
            foreach ($pitems as $key => $value) {
                $total_unit_cost += $value->unit_cost;
            }

            foreach ($pitems as $key => $stock) {
                $unit_ac = round((($stock->unit_cost/$total_unit_cost)*$total_cost)/$stock->quantity_in, 2);
                $stock->unit_cost = $stock->unit_cost+$unit_ac;
                $stock->save();
            }
            return redirect()->back()->with('success', 'Additional Cost added successfully');
        }else{
            return redirect()->back()->with('error', 'Purchase not Found');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $costitem = PurchaseCostItem::find($request['item_id']);
        if (!is_null($costitem)) {
            $purchase = Purchase::find($costitem->purchase_id);
            $costitem->item_desc = $request['item_desc'];
            $costitem->amount = $request['amount'];
            $costitem->percent = round(($costitem->amount/$purchase->total_amount)*100, 2);
            $costitem->save();

            $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
            $total_cost = 0;
            foreach ($costitems as $key => $item) {
                $total_cost += $item->amount;
            }

            $purchase->total_cost = $total_cost;
            $purchase->save();

            $total_unit_cost = 0;
            $pitems = Stock::where('purchase_id', $purchase->id)->get();
            foreach ($pitems as $key => $value) {
                $total_unit_cost += $value->unit_cost;
            }

            foreach ($pitems as $key => $stock) {
                $unit_ac = round((($stock->unit_cost/$total_unit_cost)*$total_cost)/$stock->quantity_in, 2);
                $stock->unit_cost = $stock->unit_cost+$unit_ac;
                $stock->save();
            }
            return redirect()->back()->with('success', 'Additional Cost updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $costitem = PurchaseCostItem::find(decrypt($id));
        if (!is_null($costitem)) {
            $purchase = Purchase::find($costitem->purchase_id);
            $costitem->delete();

            $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
            $total_cost = 0;
            foreach ($costitems as $key => $item) {
                $total_cost += $item->amount;
            }

            $purchase->total_cost = $total_cost;
            $purchase->save();

            $total_unit_cost = 0;
            $pitems = Stock::where('purchase_id', $purchase->id)->get();
            foreach ($pitems as $key => $value) {
                $total_unit_cost += $value->unit_cost;
            }

            foreach ($pitems as $key => $stock) {
                $unit_ac = round((($stock->unit_cost/$total_unit_cost)*$total_cost)/$stock->quantity_in, 2);
                $stock->unit_cost = $stock->unit_cost+$unit_ac;
                $stock->save();
            }
            return redirect()->back()->with('success', 'Additional Cost removed successfully');
        }else{
            return redirect()->back()->with('error', 'Cost item not Found');
        }
    }
}
