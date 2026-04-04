<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\FuelStation;
use App\Models\FuelType;
use App\Models\LicenseType;
use App\Models\Refuel;
use App\Models\Vehicle;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class RefuelingController extends Controller
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
        $companyId = Session::get('company_id');
        $page = 'Vehicle Refueling';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
        
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }
        $refuels = Refuel::with('driver','fuelType','fuelStation','vehicle')->where('company_id',$companyId)->whereBetween('date', [$start, $end])->latest()->get();
        $fuel_types = FuelType::where('company_id',$companyId)->latest()->get();
        $vehicles = Vehicle::where('company_id',$companyId)->latest()->get();;
        $drivers = Driver::where('company_id',$companyId)->with('licenseType')->latest()->get();
        $vendors = Vendor::where('company_id',$companyId)->latest()->get();
        $fuel_stations = FuelStation::where('company_id',$companyId)->latest()->get();
        $license_types = LicenseType::where('company_id',$companyId)->with('company')->latest()->get();
        return view('vms.refuling.index',compact('page','is_post_query', 'start_date', 'end_date','license_types','refuels','vendors','drivers','fuel_stations','vehicles','fuel_types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Refuel';
        $companyId = Session::get('company_id');
        $fuel_types = FuelType::where('company_id',$companyId)->latest()->get();
        $vehicles = Vehicle::where('company_id',$companyId)->latest()->get();;
        $drivers = Driver::where('company_id',$companyId)->with('licenseType')->latest()->get();
        $vendors = Vendor::where('company_id',$companyId)->latest()->get();
        $fuel_stations = FuelStation::where('company_id',$companyId)->latest()->get();
        $license_types = LicenseType::where('company_id',$companyId)->with('company')->latest()->get();

        return view('vms.refuling.create',compact('page','license_types','vendors','drivers','fuel_stations','vehicles','fuel_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $companyId  = session('company_id');
        $total_cost = $request->fuel_qty * $request->price;

        if (!$companyId) {
            return redirect()->back()->with('error', 'Company session not found.');
        }

        $rules = [
            'vehicle_id'     => 'required|exists:vehicles,id',
            'fuel_type_id'   => 'required|exists:fuel_types,id',
            'driver_id'      => 'required|exists:drivers,id',
            'odometer'       => 'required|numeric|min:0',
            'fuel_qty'       => 'required|numeric|min:0',
            'price'          => 'required|numeric|min:0',
            'date'           => 'required|date',
            'time'           => 'required',
            'note'           => 'nullable|string|max:500',
            'doc_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        if ($request->station_mode === 'new') {
            $rules['new_station_name']      = 'required|string|max:255';
            $rules['new_station_vendor_id'] = 'nullable|exists:vendors,id';
        } else {
            $rules['fuel_station_id'] = 'required|exists:fuel_stations,id';
        }

        $request->validate($rules);

        try {

            if ($request->station_mode === 'new') {
                $station = FuelStation::create([
                    'company_id'     => $companyId,
                    'station_name'   => $request->new_station_name,
                    'vendor_id'      => $request->new_station_vendor_id,
                    'contact_person' => $request->new_station_contact_person,
                    'contact_number' => $request->new_station_contact_number,
                    'address'        => $request->new_station_address,
                    'active'         => true,
                ]);
                $stationId = $station->id;
            } else {
                $stationId = $request->fuel_station_id;
            }

            $attachmentPath = null;
            if ($request->hasFile('doc_attachment')) {
                $attachmentPath = $request->file('doc_attachment')->store('refuels', 'public');
            }

            Refuel::create([
                'company_id'      => $companyId,
                'user_id'         => Auth::id(),
                'vehicle_id'      => $request->vehicle_id,
                'fuel_type_id'    => $request->fuel_type_id,
                'fuel_station_id' => $stationId,
                'driver_id'       => $request->driver_id,
                'odometer'        => $request->odometer,
                'fuel_qty'        => $request->fuel_qty,
                'price'           => $request->price,
                'total_cost'      => $total_cost,
                'date'            => $request->date,
                'time'            => $request->time,
                'note'            => $request->note,
                'doc_attachment'  => $attachmentPath,
            ]);

            return redirect()->back()->with('success', 'Refuel record added successfully');

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
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
        $refuel = Refuel::find($id);

        if (!$refuel) {
            return redirect()->back()->with('error', 'Refuel record not found!');
        }
        $total_cost = $request->fuel_qty * $request->price; 

        $request->validate([
            'vehicle_id'      => 'required|exists:vehicles,id',
            'fuel_type_id'    => 'required|exists:fuel_types,id',
            'fuel_station_id' => 'required|exists:fuel_stations,id',
            'driver_id'       => 'required|exists:drivers,id',
            'odometer'        => 'required|numeric|min:0',
            'fuel_qty'        => 'required|numeric|min:0',
            'price'           => 'required|numeric|min:0',
            'date'            => 'required|date',
            'time'            => 'required',
            'note'            => 'nullable|string|max:500',
            'doc_attachment'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $attachmentPath = $refuel->doc_attachment;
        if ($request->hasFile('doc_attachment')) {

            if ($refuel->doc_attachment && Storage::disk('public')->exists($refuel->doc_attachment)) {
                Storage::disk('public')->delete($refuel->doc_attachment);
            }
            $attachmentPath = $request->file('doc_attachment')->store('refuels', 'public');
        }

        $refuel->update([
            'vehicle_id'      => $request->vehicle_id,
            'fuel_type_id'    => $request->fuel_type_id,
            'fuel_station_id' => $request->fuel_station_id,
            'driver_id'       => $request->driver_id,
            'odometer'        => $request->odometer,
            'fuel_qty'        => $request->fuel_qty,
            'price'           => $request->price,
            'total_cost'      => $total_cost,
            'date'            => $request->date,
            'time'            => $request->time,
            'note'            => $request->note,
            'doc_attachment'  => $attachmentPath,
        ]);

        return redirect()->back()->with('success', 'Refuel record updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         $refuel = Refuel::find(decrypt($id));

        if (!$refuel) {
            return redirect()->back()->with('error', 'Refuel record not found!');
        }

        $refuel->delete();

        return redirect()->back()->with('success', 'Refuel record deleted successfully');
    }
}
