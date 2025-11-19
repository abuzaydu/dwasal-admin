<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\UnitMeasure;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Vehicles';
        $units = UnitMeasure::select('unit_name')->get();
        $vehicles = Vehicle::where('shop_id', Session::get('shop_id'))->get();

        return view('shop.vehicles.index', compact('page', 'vehicles', 'units'));
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
        $vehicle = Vehicle::where('shop_id', Session::get('shop_id'))->where('plate_no', $request['plate_no'])->first();
        if (is_null($vehicle)) {
            $vehicle = new Vehicle();
            $vehicle->shop_id = Session::get('shop_id');
            $vehicle->plate_no = $request['plate_no'];
            $vehicle->chassis_no = $request['chassis_no'];
            $vehicle->type = $request['type'];
            $vehicle->capacity = $request['capacity'];
            $vehicle->uom = $request['uom'];
            $vehicle->ownership = $request['ownership'];
            $vehicle->save();
        }

        return redirect('vehicles')->with('success', 'New Vehicle added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Vehicle Details';
        $vehicle = Vehicle::find(decrypt($id));
        $codetype = 'QRCODE';
        return view('shop.vehicles.show', compact('page', 'vehicle', 'codetype'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edi Vehicle Details';
        $units = UnitMeasure::select('unit_name')->get();
        $vehicle = Vehicle::find(decrypt($id));
        return view('shop.vehicles.edit', compact('page', 'vehicle', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vehicle = Vehicle::find(decrypt($id));
        if (!is_null($vehicle)) {
            $vehicle->plate_no = $request['plate_no'];
            $vehicle->chassis_no = $request['chassis_no'];
            $vehicle->type = $request['type'];
            $vehicle->capacity = $request['capacity'];
            $vehicle->uom = $request['uom'];
            $vehicle->ownership = $request['ownership'];
            $vehicle->save();

            return redirect('vehicles')->with('success', 'Vehicle Details updated successfully');
        }else {
            return redirect('vehicles')->with('error', 'Vehicle not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehicle = Vehicle::find(decrypt($id));
        if (!is_null($vehicle)) {
            $orderdeliveries = OrderDelivery::where('vehicle_id', $vehicle->id)->count();
            if ($orderdeliveries > 0) {
                return redirect()->back()->with('info', "Vehicle with Order details can't be deleted");
            }else{
                return redirect('vehicles')->with('success', 'Vehicle deleted successfully');
            }
        }
    }
}
