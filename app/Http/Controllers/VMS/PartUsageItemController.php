<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Part;
use App\Models\PartUsageItem;

class PartUsageItemController extends Controller
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
        $puitem = PartUsageItem::where('part_usage_id', $request['part_usage_id'])->where('part_id', $request['part_id'])->first();
        if (is_null($puitem)) {
            $part = Part::find($request['part_id']);
            $puitem = new PartUsageItem();
            $puitem->part_usage_id = $request['part_usage_id'];
            $puitem->part_id = $part->id;
            $puitem->part_category_id = $part->part_category_id;
            $puitem->date = $request['pu_date'];
            $puitem->pu_qty = 1;
            $puitem->save();

            return redirect()->route('parts-usage.create')->with('success', 'Part usage Item added successfully');
        }else{
            return redirect()->route('parts-usage.create')->with('info', 'Part usage Item already selected');
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
        $puitem = PartUsageItem::find($request['id']);
        if (!is_null($puitem)) {
            $puitem->pu_qty = $request['pu_qty'];
            $puitem->save();

            return response()->json(['success' => 1, 'msg' => 'Part Usage Item updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Part Usage Item not Found']);
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
