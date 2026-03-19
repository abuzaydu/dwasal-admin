<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Ownership;
use App\Models\UnitMeasure;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VehicleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function dashboard(Request $request)
    {
        $page = 'VMS Dashboard';
        return view('vms.index', compact('page'));
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Vehicles';
        $units = UnitMeasure::select('unit_name')->get();
        $vehicles = Vehicle::where('vehicles.company_id', Session::get('company_id'))->join('vehicle_types', 'vehicle_types.id', '=', 'vehicles.vehicle_type_id')->join('ownerships', 'ownerships.id', '=', 'vehicles.ownership_id')->select('vehicles.id as id', 'plate_no', 'vehicle_name',  'reg_date', 'name as type', 'type as ownership', 'status', 'capacity', 'uom')->get();
        $vehtypes = VehicleType::where('company_id', Session::get('company_id'))->get();
        $ownerships = Ownership::where('company_id', Session::get('company_id'))->get();
        $departments = Department::where('company_id', Session::get('company_id'))->get();

        return view('vms.vehicles.index', compact('page', 'vehicles', 'units', 'vehtypes', 'ownerships', 'departments'));
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
        $vehicle = Vehicle::where('company_id', Session::get('company_id'))->where('plate_no', $request['plate_no'])->first();
        if (is_null($vehicle)) {
            $vehicle = new Vehicle();
            $vehicle->company_id = Session::get('company_id');
            $vehicle->vehicle_type_id = $request['vehicle_type_id'];
            $vehicle->ownership_id = $request['ownership_id'];
            $vehicle->department_id = $request['department_id'];
            $vehicle->plate_no = $request['plate_no'];
            $vehicle->vehicle_name = $request['vehicle_name'];
            $vehicle->chassis_no = $request['chassis_no'];
            $vehicle->capacity = $request['capacity'];
            $vehicle->uom = $request['uom'];
            $vehicle->reg_date = $request['reg_date'];
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
        $codetype = 'QRCODE';
        $vehicle = Vehicle::where('vehicles.id', decrypt($id))->join('vehicle_types', 'vehicle_types.id', '=', 'vehicles.vehicle_type_id')->join('ownerships', 'ownerships.id', '=', 'vehicles.ownership_id')->select('vehicles.id as id', 'plate_no', 'vehicle_name', 'reg_date', 'name as type', 'type as ownership', 'status', 'capacity', 'uom')->first();
        return view('vms.vehicles.show', compact('page', 'vehicle', 'codetype'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edi Vehicle Details';
        $units = UnitMeasure::select('unit_name')->get();
        $vehicle = Vehicle::find(decrypt($id));
        $vehtypes = VehicleType::where('company_id', Session::get('company_id'))->get();
        $ownerships = Ownership::where('company_id', Session::get('company_id'))->get();
        $departments = Department::where('company_id', Session::get('company_id'))->get();
        return view('vms.vehicles.edit', compact('page', 'vehicle', 'units', 'vehtypes', 'ownerships', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vehicle = Vehicle::find(decrypt($id));
        if (!is_null($vehicle)) {
            $vehicle->vehicle_type_id = $request['vehicle_type_id'];
            $vehicle->ownership_id = $request['ownership_id'];
            $vehicle->department_id = $request['department_id'];
            $vehicle->plate_no = $request['plate_no'];
            $vehicle->vehicle_name = $request['vehicle_name'];
            $vehicle->chassis_no = $request['chassis_no'];
            $vehicle->capacity = $request['capacity'];
            $vehicle->uom = $request['uom'];
            $vehicle->reg_date = $request['reg_date'];
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
            // $orderdeliveries = DeliveryNode::where('vehicle_id', $vehicle->id)->count();
            // if ($orderdeliveries > 0) {
            //     return redirect()->back()->with('info', "Vehicle with Order details can't be deleted");
            // }else{
               // $vehicle->delete();
               $vehicle->status = 'UnAvailable';
               $vehicle->save();
                return redirect('vehicles')->with('success', 'Vehicle deleted successfully');
            //}
        }
    }
}
