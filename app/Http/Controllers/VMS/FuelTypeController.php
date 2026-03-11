<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\FuelType;
use Illuminate\Http\Request;

class FuelTypeController extends Controller
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
        $companyId = Session('company_id');
        //dd($companyId);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|max:255',
            'active' => 'boolean'
        ]);

        $fuelTypes = FuelType::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'description' => $request->description,
            'active' => $request->input('active',true)
        ]);
        if(!$fuelTypes){
            return redirect()->back()->with('error','An error occured while adding fuel type');
        }
        return redirect()->back()->with('success', 'Fuel type added successfully');
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
        $fuel_types = FuelType::find($id);
        if(!$fuel_types){
            return redirect()->back()->with('error','Fuel type not found!');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'active' => 'boolean'
        ]);
        $fuel_types->update([
            'name' => $request->name,
            'description' => $request->description,
            'active' => $request->input('active', true)
        ]);
        return redirect()->back()->with('success','Fuel type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fuel_types = FuelType::find(decrypt($id));
        //dd($fuel_types);
        if(!$fuel_types){
            return redirect()->back()->with('error', 'Fuel type not found');
        }
        $fuel_types->delete();
        return redirect()->back()->with('success', 'Fuel type deleted successfully');
    }
}
