<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\LicenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drivers       = Driver::orderBy('created_at', 'desc')->get();
        $license_types = LicenseType::all();
        $page          = 'Drivers';

        return view('vms.drivers.index', compact('page', 'drivers', 'license_types'));
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
        $companyId = session('company_id');

        if (!$companyId) {
            return redirect()->back()->with('error', 'Company session not found.');
        }

        $request->validate([
            'license_type_id'    => 'required|exists:license_types,id',
            'full_name'          => 'required|string|max:255',
            'mobile'             => 'required|string|max:20',
            'license_no'         => 'required|string|max:255',
            'license_issue_date' => 'required|date',
            'nid'                => 'required|string|max:255',
            'join_date'          => 'required|date',
            'working_time_slot'  => 'required|string|max:255',
            'date_of_birth'      => 'required|date',
            'present_address'    => 'nullable|string|max:255',
            'permanent_address'  => 'nullable|string|max:255',
            'is_active'          => 'boolean',
            'driver_photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('driver_photo')) {
            $photoPath = $request->file('driver_photo')->store('drivers', 'public');
        }

        Driver::create([
            'company_id'         => $companyId,
            'license_type_id'    => $request->license_type_id,
            'full_name'          => $request->full_name,
            'mobile'             => $request->mobile,
            'license_no'         => $request->license_no,
            'license_issue_date' => $request->license_issue_date,
            'nid'                => $request->nid,
            'join_date'          => $request->join_date,
            'working_time_slot'  => $request->working_time_slot,
            'date_of_birth'      => $request->date_of_birth,
            'present_address'    => $request->present_address,
            'permanent_address'  => $request->permanent_address,
            'is_active'          => $request->input('is_active', true),
            'driver_photo'       => $photoPath,
        ]);

        return redirect()->back()->with('success', 'Driver added successfully');
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
        $driver = Driver::find($id);

        if (!$driver) {
            return redirect()->back()->with('error', 'Driver not found!');
        }

        $request->validate([
            'license_type_id'    => 'required|exists:license_types,id',
            'full_name'          => 'required|string|max:255',
            'mobile'             => 'required|string|max:20',
            'license_no'         => 'required|string|max:255',
            'license_issue_date' => 'required|date',
            'nid'                => 'required|string|max:255',
            'join_date'          => 'required|date',
            'working_time_slot'  => 'required|string|max:255',
            'date_of_birth'      => 'required|date',
            'present_address'    => 'nullable|string|max:255',
            'permanent_address'  => 'nullable|string|max:255',
            'is_active'          => 'boolean',
            'driver_photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $photoPath = $driver->driver_photo;
        if ($request->hasFile('driver_photo')) {
            if($driver->driver_photo && Storage::disk('public')->exists($driver->driver_photo)){
                Storage::disk('public')->delete($driver->driver_photo);
            }
            $photoPath = $request->file('driver_photo')->store('drivers', 'public');
        }

        $driver->update([
            'license_type_id'    => $request->license_type_id,
            'full_name'          => $request->full_name,
            'mobile'             => $request->mobile,
            'license_no'         => $request->license_no,
            'license_issue_date' => $request->license_issue_date,
            'nid'                => $request->nid,
            'join_date'          => $request->join_date,
            'working_time_slot'  => $request->working_time_slot,
            'date_of_birth'      => $request->date_of_birth,
            'present_address'    => $request->present_address,
            'permanent_address'  => $request->permanent_address,
            'is_active'          => $request->input('is_active', true),
            'driver_photo'       => $photoPath,
        ]);

        return redirect()->back()->with('success', 'Driver updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $driver = Driver::find($id);

        if (!$driver) {
            return redirect()->back()->with('error', 'Driver not found!');
        }

        $driver->delete();

        return redirect()->back()->with('success', 'Driver deleted successfully');
    }
}
