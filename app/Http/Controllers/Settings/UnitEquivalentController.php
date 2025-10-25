<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\UnitMeasure;
use App\Models\UnitEquivalent;

class UnitEquivalentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Unit Equivalents';
        $units = UnitMeasure::select('unit_name')->orderBy('unit_name', 'asc')->get();
        $uequivalents = UnitEquivalent::all();

        return view('settings.unit-equis.index', compact('page', 'units', 'uequivalents'));
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
        $uequivalent = UnitEquivalent::where('unit_a', $request['unit_a'])->where('unit_b', $request['unit_b'])->first();
        if (is_null($uequivalent)) {
            $uequivalent = new UnitEquivalent();
            $uequivalent->company_id = Session::get('company_id');
            $uequivalent->unit_a = $request['unit_a'];
            $uequivalent->unit_a_value = 1;
            $uequivalent->unit_b = $request['unit_b'];
            $uequivalent->unit_b_value = $request['unit_b_value'];
            $uequivalent->save();
        }

        return redirect('unit-equivalents')->with('success', 'Unit Equivalent added successfully');
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
        $page = 'Edit Unit Equivalent';
        $uequivalent = UnitEquivalent::find(decrypt($id));
        $units = UnitMeasure::select('unit_name')->orderBy('unit_name', 'asc')->get();
        return view('settings.unit-equis.edit', compact('page', 'uequivalent', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    
        $uequivalent = UnitEquivalent::find(decrypt($id));
        if (!is_null($uequivalent)) {
            $uequivalent->unit_a = $request['unit_a'];
            $uequivalent->unit_a_value = 1;
            $uequivalent->unit_b = $request['unit_b'];
            $uequivalent->unit_b_value = $request['unit_b_value'];
            $uequivalent->save();
            return redirect('unit-equivalents')->with('success', 'Unit Equivalent added successfully');
        }else {
            return redirect('unit-equivalents')->with('request', 'Unit Equivalent not found');
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
