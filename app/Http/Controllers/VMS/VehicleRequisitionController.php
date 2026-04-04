<?php

namespace App\Http\Controllers\VMS;

use \Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Employee;
use App\Models\RequisitionPurpose;
use App\Models\RequsitionTripLogs;
use App\Models\Vehicle;
use App\Models\VehicleRequisition;
use App\Models\VehicleType;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class VehicleRequisitionController extends Controller
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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Vehicle Requisitions';
        $now = Carbon::now(); 
        $start = $now->copy()->startOfMonth()->format('Y-m-d');
        $end = \Carbon\Carbon::now()->format('Y-m-d');
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));

        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $company = Company::find(Session::get('company_id'));
        $requisitionPurpose = RequisitionPurpose::where('company_id', $company->id)->latest()->get();
        $employees = Employee::where('company_id', $company->id)->latest()->get();
        $vehicleTypes = VehicleType::where('company_id', $company->id)->latest()->get();
        $drivers = Driver::where('company_id', $company->id)->where('is_active',1)->latest()->get();
        $requisitions = VehicleRequisition::where('company_id', $company->id)->latest()->get();

        $vehicles = Vehicle::where('company_id', $company->id)->where('status','Available')->get();
        $vrequisitions = VehicleRequisition::where('company_id', $company->id)->whereBetween('requisition_date', [$start, $end])->latest()->get();

        return view('vms.requisitions.index', compact('page','drivers','vehicleTypes','requisitionPurpose','employees','requisitions', 'is_post_query', 'start_date', 'end_date', 'vehicles', 'vrequisitions'));
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
       //dd($request->all());
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'vehicle_type_id' => 'required|exists:vehicle_types,id',
                'requisition_purpose_id' => 'required|exists:requisition_purposes,id',
                'from' => 'required|string|max:255',
                'to' => 'required|string|max:255',
                'pick_up' => 'required|string|max:255',
                'requisition_date' => 'required|date',
                'time_from' => 'required',
                'time_to' => 'required',
                'no_of_passenger' => 'nullable|integer|min:1',
                'tolerance_duration' => 'nullable|string|max:255',
                'details' => 'nullable|string|max:500',
            ]);

             VehicleRequisition::create([
                'company_id' => Session::get('company_id'),
                'user_id' => auth()->id(),
                'employee_id' => $request->employee_id,
                'vehicle_type_id' => $request->vehicle_type_id,
                'requisition_purpose_id' => $request->requisition_purpose_id,
                'driver_id' => null, 
                'from' => $request->from,
                'to' => $request->to,
                'pick_up' => $request->pick_up,
                'requisition_date' => $request->requisition_date,
                'time_from' => $request->time_from,
                'time_to' => $request->time_to,
                'no_of_passenger' => $request->no_of_passenger,
                'tolerance_duration' => $request->tolerance_duration,
                'details' => $request->details,
            ]);
            //dd($requisition);

            return redirect()->back()->with('success', 'Vehicle requisition created successfully.');

        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {     
        $page = 'Show page';
        $company = Company::find(Session::get('company_id'));
        $requisition = VehicleRequisition::with([
                'employee',
                'vehicleType',
                'purpose',
                'vehicle'
            ])->findOrFail($id);
            //dd($requisition);
        $drivers = Driver::where('company_id', $company->id)->where('is_active',1)->get();
        $requisitions = VehicleRequisition::where('company_id', $company->id)->get();
        $employees = Employee::where('company_id', $company->id)->latest()->get();
        $vehicleTypes = VehicleType::where('company_id', $company->id)->get();
        $requisitionPurpose = RequisitionPurpose::where('company_id', $company->id)->get();


        $vehicles = Vehicle::where('company_id', $company->id)->where('status','Available')->get();

        return view('vms.requisitions.show',compact('page','company','requisition','drivers','requisitionPurpose','vehicles','employees','vehicleTypes'));
    }

    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $requisition = VehicleRequisition::findOrFail($id);

        $requisition->update([
            'driver_id' => $request->driver_id,
            'vehicle_id' => $request->vehicle_id,
            'status' => 'Approved'
        ]);

        Driver::where('id', $request->driver_id)
            ->update(['is_active' => 'inactive']);

        Vehicle::where('id', $request->vehicle_id)
            ->update(['status' => 'UnAvailable']);

        return back()->with('success', 'Driver and vehicle assigned successfully');
    }

    public function rejectRequisition(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:255',
        ]);

        $requisition = VehicleRequisition::findOrFail($id);

        $requisition->update([
            'status' => 'Rejected',
            'rejection_reason' => $request->rejection_reason ?? null,
        ]);

        return back()->with('success', 'Vehicle requisition has been rejected successfully.');
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
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'vehicle_type_id' => 'required|exists:vehicle_types,id',
                'requisition_purpose_id' => 'required|exists:requisition_purposes,id',
                'from' => 'required|string|max:255',
                'to' => 'required|string|max:255',
                'pick_up' => 'required|string|max:255',
                'requisition_date' => 'required|date',
                'time_from' => 'required',
                'time_to' => 'required',
                'no_of_passenger' => 'nullable|integer|min:1',
                'tolerance_duration' => 'nullable|string|max:255',
                'details' => 'nullable|string|max:500',
            ]);

            $requisition = VehicleRequisition::findOrFail($id);

            $requisition->update([
                'employee_id' => $request->employee_id,
                'vehicle_type_id' => $request->vehicle_type_id,
                'requisition_purpose_id' => $request->requisition_purpose_id,
                'from' => $request->from,
                'to' => $request->to,
                'pick_up' => $request->pick_up,
                'requisition_date' => $request->requisition_date,
                'time_from' => $request->time_from,
                'time_to' => $request->time_to,
                'no_of_passenger' => $request->no_of_passenger,
                'tolerance_duration' => $request->tolerance_duration,
                'details' => $request->details,
            ]);

            return redirect()->back()->with('success', 'Vehicle requisition updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function resubmit(Request $request, string $id)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'vehicle_type_id' => 'required|exists:vehicle_types,id',
                'requisition_purpose_id' => 'required|exists:requisition_purposes,id',
                'from' => 'required|string|max:255',
                'to' => 'required|string|max:255',
                'pick_up' => 'required|string|max:255',
                'requisition_date' => 'required|date',
                'time_from' => 'required',
                'time_to' => 'required',
                'no_of_passenger' => 'nullable|integer|min:1',
                'tolerance_duration' => 'nullable|string|max:255',
                'details' => 'nullable|string|max:500',
            ]);

            $requisition = VehicleRequisition::findOrFail($id);

            $requisition->update([
                'employee_id' => $request->employee_id,
                'vehicle_type_id' => $request->vehicle_type_id,
                'requisition_purpose_id' => $request->requisition_purpose_id,
                'from' => $request->from,
                'to' => $request->to,
                'pick_up' => $request->pick_up,
                'requisition_date' => $request->requisition_date,
                'time_from' => $request->time_from,
                'time_to' => $request->time_to,
                'no_of_passenger' => $request->no_of_passenger,
                'tolerance_duration' => $request->tolerance_duration,
                'details' => $request->details,
                'status' => 'Awaiting for Approval',
                'rejection_reason' => null
            ]);

            return redirect()->back()->with('success', 'Vehicle requisition resubmited successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $vehicleRequisition = VehicleRequisition::find(decrypt($id));
            if(!$vehicleRequisition){
                return redirect()->back()->with('error', 'Vehicle requisition not found');
            }
            $vehicleRequisition -> delete();
            return redirect()-> back()->with('success','Requisition deleted successfully');
        } catch (\Throwable $e) {
          return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
