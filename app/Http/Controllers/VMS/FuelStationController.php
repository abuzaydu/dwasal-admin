<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\FuelStation;
use Illuminate\Http\Request;

class FuelStationController extends Controller
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

        $request->validate([
            'station_name' => 'required|string|max:255',
            'vendor_id' => 'required|exists:vendors,id',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'address' => 'required|string',
            'active' => 'boolean',
        ]);
        
       try {
         FuelStation::create([
            'company_id' => $companyId,
            'station_name' => $request->station_name,
            'vendor_id' =>  $request->vendor_id,
            'contact_person' => $request->contact_person,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'active' => $request->input('active', true),
        ]);
            return redirect()->back()->with('success', 'Fuel station added successfully');
       } catch (\Throwable $e) {
         return redirect()->back()->with('error', $e->getMessage());
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
        $fuelStation = FuelStation::find($id);

        if (!$fuelStation) {
            return redirect()->back()->with('error', 'Fuel station not found!');
        }

        $request->validate([
            'station_name'   => 'required|string|max:255',
            'vendor_id'      => 'required|exists:vendors,id',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:255',
            'active'         => 'boolean',
        ]);

        $fuelStation->update([
            'station_name'   => $request->station_name,
            'vendor_id'      => $request->vendor_id,
            'contact_person' => $request->contact_person,
            'contact_number' => $request->contact_number,
            'address'        => $request->address,
            'active'         => $request->input('active', true),
        ]);

        return redirect()->back()->with('success', 'Fuel station updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       
        $fuelStation = FuelStation::find(decrypt($id));

        if (!$fuelStation) {
            return redirect()->back()->with('error', 'Fuel station not found!');
        }

        $fuelStation ->active = false;
        $fuelStation->save();

        return redirect()->back()->with('success', 'Fuel station deleted successfully');
    
    }
}
