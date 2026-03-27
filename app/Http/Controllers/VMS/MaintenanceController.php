<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Maintenance;
use App\Models\MaintenanceItem;
use App\Models\MaintenancePhoto;
use App\Models\MaintenanceType;
use App\Models\Part;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class MaintenanceController extends Controller
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
    public function index()
    {
        $page = 'Vehicle Maintanance';
        $companyId = Session::get('company_id');

        $maintenances = Maintenance::with(['vehicle', 'maintenanceType', 'employee'])
            ->where('company_id', $companyId)
            ->where('is_deleted', false)
            ->latest()
            ->get();

        $maintenanceTypes = MaintenanceType::where('company_id', $companyId)
            ->orderByDesc('id')
            ->get();

        return view('vms.maintenance.index', compact('page', 'maintenances', 'maintenanceTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Maintenance';
        $companyId = Session::get('company_id');

        $vehicles = Vehicle::where('company_id', $companyId)->select('id', 'plate_no', 'vehicle_name')->latest()->get();
        $employees = Employee::where('company_id', $companyId)->select('id', 'fname', 'lname')->latest()->get();
        $maintenanceTypes = MaintenanceType::where('company_id', $companyId)->where('active', true)->select('id', 'type')->latest()->get();

        return view('vms.maintenance.create', compact('page', 'vehicles', 'employees', 'maintenanceTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $companyId = Session::get('company_id');

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'employee_id' => 'required|exists:employees,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'date' => 'required|date',
            'maintenance_code' => 'required|string|max:255',
            'req_type' => 'required|string|max:255',
            'priority' => 'required|string|max:100',
            'service_title' => 'required|string|max:255',
            'charge_bear_by' => 'nullable|string|max:255',
            'charge' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:2000',
            'status' => 'required|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.part_id' => 'required|exists:parts,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:10240',
        ]);

        $items = $request->input('items', []);
        $status = $request->input('status', 'Pending');

        DB::transaction(function () use ($request, $companyId, $items, $status) {
            $maintenance = new Maintenance();
            $maintenance->company_id = $companyId;
            $maintenance->user_id = Auth::id();
            $maintenance->employee_id = $request->employee_id;
            $maintenance->vehicle_id = $request->vehicle_id;
            $maintenance->maintenance_type_id = $request->maintenance_type_id;
            $maintenance->date = $request->date;
            $maintenance->maintenance_code = $request->maintenance_code;
            $maintenance->req_type = $request->req_type;
            $maintenance->priority = $request->priority;
            $maintenance->service_title = $request->service_title;
            $maintenance->charge_bear_by = $request->charge_bear_by;
            $maintenance->charge = $request->charge ?? 0;
            $maintenance->remarks = $request->remarks;
            $maintenance->status = $status;
            $maintenance->is_deleted = false;
            $maintenance->save();

            foreach ($items as $item) {
                $part = Part::where('company_id', $companyId)->findOrFail($item['part_id']);

                $maintenanceItem = new MaintenanceItem();
                $maintenanceItem->maintenance_id = $maintenance->id;
                $maintenanceItem->part_category_id = $part->part_category_id;
                $maintenanceItem->part_id = $part->id;
                $maintenanceItem->date = $request->date . ' 00:00:00';
                $maintenanceItem->qty = $item['qty'];
                $maintenanceItem->unit_price = $item['unit_price'];
                $maintenanceItem->total_price = round(((float) $item['qty']) * ((float) $item['unit_price']), 2);
                $maintenanceItem->is_deleted = false;
                $maintenanceItem->save();
            }

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos', []) as $photo) {
                    if (!$photo->isValid()) {
                        continue;
                    }

                    $path = $photo->store('maintenance/vehicle', 'public');

                    $maintenancePhoto = new MaintenancePhoto();
                    $maintenancePhoto->maintenance_record_id = $maintenance->id;
                    $maintenancePhoto->photo_url = $path;
                    $maintenancePhoto->save();
                }
            }
        });

        return redirect('maintenance')->with('success', 'Maintenance record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $companyId = Session::get('company_id');
        $maintenance = Maintenance::with(['vehicle', 'employee', 'maintenanceType', 'items.part', 'photos'])
            ->where('company_id', $companyId)
            ->where('is_deleted', false)
            ->findOrFail(decrypt($id));

        $page = 'Maintenance Details';
        return view('vms.maintenance.show', compact('page', 'maintenance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $companyId = Session::get('company_id');
        $maintenance = Maintenance::with(['vehicle', 'employee', 'maintenanceType', 'items.part', 'photos'])
            ->where('company_id', $companyId)
            ->where('is_deleted', false)
            ->findOrFail(decrypt($id));

        $page = 'Edit Maintenance';
        $vehicles = Vehicle::where('company_id', $companyId)->select('id', 'plate_no', 'vehicle_name')->latest()->get();
        $employees = Employee::where('company_id', $companyId)->select('id', 'fname', 'lname')->latest()->get();
        $maintenanceTypes = MaintenanceType::where('company_id', $companyId)->where('active', true)->select('id', 'type')->latest()->get();

        return view('vms.maintenance.edit', compact('page', 'maintenance', 'vehicles', 'employees', 'maintenanceTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $companyId = Session::get('company_id');
        $maintenance = Maintenance::where('company_id', $companyId)
            ->where('is_deleted', false)
            ->findOrFail(decrypt($id));

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'employee_id' => 'required|exists:employees,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'date' => 'required|date',
            'maintenance_code' => 'required|string|max:255',
            'req_type' => 'required|string|max:255',
            'priority' => 'required|string|max:100',
            'service_title' => 'required|string|max:255',
            'charge_bear_by' => 'nullable|string|max:255',
            'charge' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:2000',
            'status' => 'required|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.part_id' => 'required|exists:parts,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'delete_photo_ids' => 'nullable|array',
            'delete_photo_ids.*' => 'integer|distinct',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:10240',
        ]);

        $items = $request->input('items', []);

        DB::transaction(function () use ($request, $maintenance, $companyId, $items) {
            $maintenance->vehicle_id = $request->vehicle_id;
            $maintenance->employee_id = $request->employee_id;
            $maintenance->maintenance_type_id = $request->maintenance_type_id;
            $maintenance->date = $request->date;
            $maintenance->maintenance_code = $request->maintenance_code;
            $maintenance->req_type = $request->req_type;
            $maintenance->priority = $request->priority;
            $maintenance->service_title = $request->service_title;
            $maintenance->charge_bear_by = $request->charge_bear_by;
            $maintenance->charge = $request->charge ?? 0;
            $maintenance->remarks = $request->remarks;
            $maintenance->status = $request->status;
            $maintenance->save();

            // Non-destructive update: mark old items deleted, then recreate new active items.
            $maintenance->items()->where('is_deleted', false)->update(['is_deleted' => true]);

            foreach ($items as $item) {
                $part = Part::where('company_id', $companyId)->findOrFail($item['part_id']);

                $maintenanceItem = new MaintenanceItem();
                $maintenanceItem->maintenance_id = $maintenance->id;
                $maintenanceItem->part_category_id = $part->part_category_id;
                $maintenanceItem->part_id = $part->id;
                $maintenanceItem->date = $request->date . ' 00:00:00';
                $maintenanceItem->qty = $item['qty'];
                $maintenanceItem->unit_price = $item['unit_price'];
                $maintenanceItem->total_price = round(((float) $item['qty']) * ((float) $item['unit_price']), 2);
                $maintenanceItem->is_deleted = false;
                $maintenanceItem->save();
            }

            $deleteIds = $request->input('delete_photo_ids', []);
            if (!empty($deleteIds)) {
                $photosToDelete = MaintenancePhoto::whereIn('id', $deleteIds)
                    ->where('maintenance_record_id', $maintenance->id)
                    ->get();

                foreach ($photosToDelete as $photo) {
                    if (!empty($photo->photo_url)) {
                        Storage::disk('public')->delete($photo->photo_url);
                    }
                    $photo->delete();
                }
            }

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos', []) as $photo) {
                    if (!$photo->isValid()) {
                        continue;
                    }

                    $path = $photo->store('maintenance/vehicle', 'public');

                    $maintenancePhoto = new MaintenancePhoto();
                    $maintenancePhoto->maintenance_record_id = $maintenance->id;
                    $maintenancePhoto->photo_url = $path;
                    $maintenancePhoto->save();
                }
            }
        });

        return redirect('maintenance')->with('success', 'Maintenance updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $maintenance = Maintenance::where('is_deleted', false)->findOrFail(decrypt($id));

        DB::transaction(function () use ($maintenance) {
            // Delete physical photos from storage.
            $photos = $maintenance->photos()->get();
            foreach ($photos as $photo) {
                if (!empty($photo->photo_url)) {
                    Storage::disk('public')->delete($photo->photo_url);
                }
                $photo->delete();
            }

            $maintenance->items()->where('is_deleted', false)->update(['is_deleted' => true]);
            $maintenance->is_deleted = true;
            $maintenance->save();
        });

        return redirect('maintenance')->with('success', 'Maintenance deleted successfully.');
    }
}
