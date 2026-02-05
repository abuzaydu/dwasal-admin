<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Part;
use App\Models\PartPurchaseItem;
use App\Models\PartPurchase;
use App\Models\PartPurchasePayment;

class PartPurchaseItemController extends Controller
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
        $stock = PartPurchaseItem::find(decrypt($id));
        if (!is_null($stock)) {
            $page = 'Edit Purchase Item';
            $part = Part::find($stock->part_id);

            return view('vms.parts.purchases.items.edit', compact('page', 'stock', 'part'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $stock = PartPurchaseItem::find(decrypt($id));
        if (!is_null($stock)) {

            $part = Part::find($stock->part_id);
            $part->av_qty -= $stock->pp_qty;
            $part->save();

            $stock->pp_qty = $request['pp_qty'];
            $stock->unit_price = $request['unit_price'];
            $stock->total_price = $stock->pp_qty*$stock->unit_price;
            $stock->save();

            $part->av_qty += $stock->pp_qty;
            $part->save();

            $pitems = PartPurchaseItem::where('part_purchase_id', $stock->part_purchase_id)->get();
            $total_amount = 0;
            foreach ($pitems as $key => $item) {
                $total_amount += $item->total_price;
            }
            $purchase = PartPurchase::find($stock->part_purchase_id);
            $purchase->total_amount = $total_amount;
            $purchase->save();

            if ($purchase->purchase_type != 'credit') {
                $pppayment = PartPurchasePayment::where('part_purchase_id', $purchase->id)->first();
                $pppayment->amount = $purchase->total_amount;
                $pppayment->save();

                $purchase->amount_paid = $purchase->total_amount;
                $purchase->save();
            }
    
            if ($purchase->total_amount == $purchase->amount_paid) {
                $purchase->status = 'Paid';
                $purchase->save();
            }elseif ($purchase->total_amount > $purchase->amount_paid && $purchase->amount_paid > 0) {
                $purchase->status = 'Partially Paid';
                $purchase->save();
            }

            return redirect('pp-items/'.encrypt($purchase->id))->with('success', 'Purchase Item updated successfully');             
        }else{
            return redirect()->back()->with('error', 'Item not Found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
