<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\PmTransfer;
use App\Models\PmTransferItem;

class PmTransferItemController extends Controller
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
        $pmt = PmTransfer::find($request['pm_transfer_id']);
        if (!is_null($pmt)) {
            $shop = Shop::find($pmt->shop_id);
            $sourcepm = $shop->packingMaterials()->where('packing_material_id', $request['packing_material_id'])->first();
            if (!is_null($sourcepm)) {
                $item = new PmTransferItem();
                $item->shop_id = $shop->id;
                $item->pm_transfer_id = $pmt->id;
                $item->packing_material_id = $request['packing_material_id'];
                $item->qty = $request['qty'];
                $item->src_qty = $sourcepm->pivot->in_store;
                $item->unit_cost = $sourcepm->pivot->unit_cost;
                $item->save();
            }

            return redirect()->route('pm-transfers.create');
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
    public function update(Request $request)
    {
        $item = PmTransferItem::find($request['id']);
        if (!is_null($item)) {
            if ($item->src_qty >= $request['qty']) {
                $item->qty = $request['qty'];
                $item->save(); 

                return response()->json(['success' => 1, 'msg' => 'Item quantity updated successfully']);
            }else{
                return response()->json(['success' => 0, 'msg' => 'LOW Stock!. The quantity you entered is greater than available stock']);
            }
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item Not found']);
        }
    }


    public function updateRec(Request $request)
    {
        $item = PmTransferItem::find($request['id']);
        if (!is_null($item)) {
            $item->rec_qty = $request['rec_qty'];
            $item->save(); 
            return response()->json(['success' => 1, 'msg' => 'Item quantity received updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item Not found']);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        PmTransferItem::destroy(decrypt($id));
        return redirect()->back()->with('success', 'Item removed successfully');
    }
}
