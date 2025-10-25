<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\MohItem;
use App\Models\MohCost;
use Log;

class MohCostItemController extends Controller
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
        $moh = MohCost::find($request['moh_cost_id']);
        $item = MohItem::where('moh_cost_id', $moh->id)->where('mro_id', $request['mro_id'])->first();
        if (is_null($item)) {
            $item = new MohItem;
            $item->shop_id = $moh->shop_id;
            $item->user_id = $moh->user_id;
            $item->moh_cost_id = $moh->id;
            $item->mro_id = $request['mro_id'];
            $item->quantity = 0;
            $item->unit_cost = 0;
            $item->total = $item->quantity*$item->unit_cost;
            $item->save();
            
            return redirect()->route('moh-costs.edit', encrypt($moh->id))->with('success', 'Item added successfully');
        }else{
            return redirect()->route('moh-costs.edit', encrypt($moh->id))->with('info', 'Item already selected');
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
        $page = 'Edit MOH Cost Item';
        $title = 'Edit MOH Cost Item';
        $item = MohItem::find(decrypt($id));

        return view('production.labour-costs.items.edit', compact('page', 'title', 'item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $item = MohItem::find($request['id']);
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

            $moh = MohCost::find($item->moh_cost_id);
            $items = MohItem::where('moh_cost_id', $moh->id)->get();
            $amount =0;
            foreach ($items as $key => $item) {
                $amount += $item->total;
            }

            $moh->amount = $amount;
            $moh->save();

            return response()->json(['success' => 1, 'msg' => 'Item Updated Successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item Not found']);
        }

        // return redirect()->route('moh-costs.show', encrypt($moh->id))->with('success', 'Labour Cost Item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = MohItem::find(decrypt($id));
        if (!is_null($item)) {
            $moh = MohCost::find($item->moh_cost_id);
            $item->delete();
            $items = MohItem::where('moh_cost_id', $moh->id)->get();
            $amount =0;
            foreach ($items as $key => $item) {
                $amount += $item->total;
            }
            
            $moh->amount = $amount;
            $moh->save();

            return redirect()->route('moh-costs.edit', encrypt($moh->id))->with('success', 'Labour Cost Item deleted successfully');
        }else{
            return redirect()->with('error', 'Labour Cost Item found');
        }
    }
}
