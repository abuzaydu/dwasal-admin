<?php

namespace App\Http\Controllers\SandProd;

use App\Http\Controllers\Controller;
use App\Models\MaintenancePhoto;
use App\Models\MaintenanceRecord;
use App\Models\Shop;
use App\Models\WashingEquipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class MaintenanceRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Maintenance Records';
        $shopId = Session::get('shop_id');

        $records = MaintenanceRecord::with(['washingEquipment', 'photos'])
            ->where('shop_id', $shopId)
            ->orderByDesc('created_at')
            ->get();

        $equipments = WashingEquipment::where('shop_id', $shopId)->select('id', 'equipment_name', 'equipment_type')->get();

        return view('production.sand.maintenance-records.index', compact('page', 'records', 'equipments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Maintenance Record';
        $shopId = Session::get('shop_id');
        $equipments = WashingEquipment::where('shop_id', $shopId)->select('id', 'equipment_name', 'equipment_type')->latest()->get();
        return view('production.sand.maintenance-records.create', compact('page', 'equipments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shopId = Session::get('shop_id');

        $request->validate([
            'washing_equipment_id' => 'required|exists:washing_equipments,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'maintenance_type' => 'required|string|max:255',
            'description_of_wo' => 'nullable|string|max:5000',
            'technician' => 'nullable|string|max:255',
            'inspection_findings' => 'nullable|string|max:5000',
            'parts_used' => 'nullable|string|max:5000',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:100',
            'notes' => 'nullable|string|max:5000',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:10240',
        ]);

        DB::transaction(function () use ($request, $shopId) {
            $record = new MaintenanceRecord();
            $record->shop_id = $shopId;
            $record->user_id = Auth::id();
            $record->washing_equipment_id = $request->washing_equipment_id;
            $record->start_time = $request->start_time;
            $record->end_time = $request->end_time;
            $record->maintenance_type = $request->maintenance_type;
            $record->description_of_wo = $request->description_of_wo;
            $record->technician = $request->technician;
            $record->inspection_findings = $request->inspection_findings;
            $record->parts_used = $request->parts_used;
            $record->cost = $request->cost ?? null;
            $record->status = $request->status;
            $record->notes = $request->notes;
            $record->save();

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos', []) as $photo) {
                    if (!$photo->isValid()) {
                        continue;
                    }

                    $path = $photo->store('maintenance/equipment', 'public');

                    $maintenancePhoto = new MaintenancePhoto();
                    $maintenancePhoto->maintenance_record_id = $record->id;
                    $maintenancePhoto->photo_url = $path;
                    $maintenancePhoto->save();
                }
            }
        });

        return redirect('maintenance-records')->with('success', 'Maintenance record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Maintenance Record Details';
        $shopId = Session::get('shop_id');

        $record = MaintenanceRecord::with(['washingEquipment', 'photos'])
            ->where('shop_id', $shopId)
            ->findOrFail(decrypt($id));

        return view('production.sand.maintenance-records.show', compact('page', 'record'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Maintenance Record';
        $shopId = Session::get('shop_id');

        $record = MaintenanceRecord::with(['washingEquipment', 'photos'])
            ->where('shop_id', $shopId)
            ->findOrFail(decrypt($id));

        $equipments = WashingEquipment::where('shop_id', $shopId)->select('id', 'equipment_name', 'equipment_type')->latest()->get();

        return view('production.sand.maintenance-records.edit', compact('page', 'record', 'equipments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shopId = Session::get('shop_id');
        $record = MaintenanceRecord::where('shop_id', $shopId)->findOrFail(decrypt($id));

        $request->validate([
            'washing_equipment_id' => 'required|exists:washing_equipments,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'maintenance_type' => 'required|string|max:255',
            'description_of_wo' => 'nullable|string|max:5000',
            'technician' => 'nullable|string|max:255',
            'inspection_findings' => 'nullable|string|max:5000',
            'parts_used' => 'nullable|string|max:5000',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:100',
            'notes' => 'nullable|string|max:5000',
            'delete_photo_ids' => 'nullable|array',
            'delete_photo_ids.*' => 'integer|distinct',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:10240',
        ]);

        DB::transaction(function () use ($request, $record) {
            $record->washing_equipment_id = $request->washing_equipment_id;
            $record->start_time = $request->start_time;
            $record->end_time = $request->end_time;
            $record->maintenance_type = $request->maintenance_type;
            $record->description_of_wo = $request->description_of_wo;
            $record->technician = $request->technician;
            $record->inspection_findings = $request->inspection_findings;
            $record->parts_used = $request->parts_used;
            $record->cost = $request->cost ?? null;
            $record->status = $request->status;
            $record->notes = $request->notes;
            $record->save();

            $deleteIds = $request->input('delete_photo_ids', []);
            if (!empty($deleteIds)) {
                $photosToDelete = MaintenancePhoto::whereIn('id', $deleteIds)
                    ->where('maintenance_record_id', $record->id)
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

                    $path = $photo->store('maintenance/equipment', 'public');

                    $maintenancePhoto = new MaintenancePhoto();
                    $maintenancePhoto->maintenance_record_id = $record->id;
                    $maintenancePhoto->photo_url = $path;
                    $maintenancePhoto->save();
                }
            }
        });

        return redirect('maintenance-records')->with('success', 'Maintenance record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shopId = Session::get('shop_id');
        $record = MaintenanceRecord::where('shop_id', $shopId)->findOrFail(decrypt($id));

        DB::transaction(function () use ($record) {
            $photos = $record->photos()->get();
            foreach ($photos as $photo) {
                if (!empty($photo->photo_url)) {
                    Storage::disk('public')->delete($photo->photo_url);
                }
                $photo->delete();
            }

            $record->delete();
        });

        return redirect('maintenance-records')->with('success', 'Maintenance record deleted successfully.');
    }
}
