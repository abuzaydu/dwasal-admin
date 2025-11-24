<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\VehicleType;

class VehicleTypeController extends Controller
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
        $vehtype = VehicleType::where('company_id', Session::get('company_id'))->where('name', $request['name'])->first();
        if (is_null($vehtype)) {
            $vehtype = new VehicleType();
            $vehtype->company_id = Session::get('company_id');
            $vehtype->name = $request['name'];
            $vehtype->description = $request['description'];
            $vehtype->save();
        }

        return redirect('vehicles')->with('success', 'New Vehicle Type added successfully');
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
        $page = 'Edi Vehicle Type';
        $vehtype = VehicleType::find(decrypt($id));

        return view('vms.vehtypes.edit', compact('page', 'vehtype'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vehtype = VehicleType::find(decrypt($id));
        $vehtype->name = $request['name'];
        $vehtype->description = $request['description'];
        $vehtype->save();

        return redirect('vehicles')->with('success', 'Vehicle Type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehtype = VehicleType::find(decrypt($id));
        if (!is_null($vehtype)) {
            $vehtype->delete();

            return redirect('vehicles')->with('success', 'Vehicle Type deleted successfully');
        }
    }
}
