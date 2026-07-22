<?php

namespace App\Http\Controllers\SandProd;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\WashingEquipment;
use App\Models\WashingPlant;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class WashingEquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Washing Equipments';
         $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
        $is_post_query = false;
        $duration = '';
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }
        $duration = '';
        $shop = Shop::find(Session::get('shop_id'));
        $wplants = WashingPlant::where('shop_id', Session::get('shop_id'))->get();
        $equipments = WashingEquipment::where('shop_id', $shop->id)->whereBetween('created_at', [$start, $end])->select('id', 'equipment_name', 'equipment_type', 'manufacturer', 'model', 'installation_date', 'photo_url', 'next_maintenance_date')->get();
        return view('production.sand.equipments.index', compact('page', 'wplants', 'equipments', 'is_post_query', 'start_date', 'end_date', 'duration'));
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
        try {
            $validated = $request->validate([
                'washing_plant_id'       => 'required|integer',
                'equipment_code'         => 'required|string|max:255',
                'equipment_name'         => 'required|string|max:255',
                'equipment_type'         => 'required|string|max:255',
                'manufacturer'           => 'required|string|max:255',
                'model'                  => 'required|string|max:255',
                'installation_date'      => 'required|date',
                'maintenance_schedule'   => 'nullable|string|max:50',
                'last_maintenance_date'  => 'nullable|date',
                'next_maintenance_date'  => 'nullable|date',
                'image'                  => 'nullable|image|mimes:jpeg,png,webp,gif,jfif,avif|max:1024',
            ]);

            $shop = Shop::find(Session::get('shop_id'));

            $equipment = WashingEquipment::where('shop_id', $shop->id)
                ->where('equipment_code', $request->equipment_code)
                ->first();

            if (!is_null($equipment)) {
                return redirect()->back()->with('info', 'Equipment with same Code ('.$request->equipment_code.') already exists');
            }

            $equipment = new WashingEquipment();
            $equipment->shop_id = $shop->id;
            $equipment->washing_plant_id = $validated['washing_plant_id'];
            $equipment->equipment_code = $validated['equipment_code'];
            $equipment->equipment_name = $validated['equipment_name'];
            $equipment->equipment_type = $validated['equipment_type'];
            $equipment->manufacturer = $validated['manufacturer'];
            $equipment->model = $validated['model'];
            $equipment->installation_date = $validated['installation_date'];
            $equipment->maintenance_schedule = $validated['maintenance_schedule'] ?? null;
            $equipment->last_maintenance_date = $validated['last_maintenance_date'] ?? null;
            $equipment->next_maintenance_date = $validated['next_maintenance_date'] ?? null;
            $equipment->save();

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $extension = $request->file('image')->extension();
                $filename = $equipment->id.'_equipment.'.$extension;

                $request->file('image')->storeAs('equipments', $filename, 'public');

                $equipment->photo_url = 'equipments/'.$filename;
                $equipment->save();
            }

            return redirect('washing-equipments')->with('success', 'Washing Equipment added successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();

            return redirect()->back()->with('error', $firstError)->withInput();
        } catch (\Exception $e) {
            Log::error('WashingEquipment store failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Equipment Details';
        $equipment = WashingEquipment::find(decrypt($id));
        $wplant = WashingPlant::find($equipment->washing_plant_id);
        $wplants = WashingPlant::where('shop_id', Session::get('shop_id'))->get();

        return view('production.sand.equipments.show', compact('page', 'equipment', 'wplant', 'wplants'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {        
        $page = 'Equipment Details';
        $equipment = WashingEquipment::find(decrypt($id));
        $wplants = WashingPlant::where('shop_id', Session::get('shop_id'))->get();
        return view('production.sand.equipments.edit', compact('page', 'equipment', 'wplants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'washing_plant_id'       => 'required|integer',
                'equipment_code'         => 'required|string|max:255',
                'equipment_name'         => 'required|string|max:255',
                'equipment_type'         => 'required|string|max:255',
                'manufacturer'           => 'required|string|max:255',
                'model'                  => 'required|string|max:255',
                'installation_date'      => 'required|date',
                'maintenance_schedule'   => 'nullable|string|max:50',
                'last_maintenance_date'  => 'nullable|date',
                'next_maintenance_date'  => 'nullable|date',
                'image'                  => 'nullable|image|mimes:jpeg,png,webp,gif,jfif,avif|max:1024',
            ]);

            $equipment = WashingEquipment::find(decrypt($id));

            if (is_null($equipment)) {
                return redirect()->back()->with('error', 'Equipment not found');
            }

            $equipment->washing_plant_id = $validated['washing_plant_id'];
            $equipment->equipment_code = $validated['equipment_code'];
            $equipment->equipment_name = $validated['equipment_name'];
            $equipment->equipment_type = $validated['equipment_type'];
            $equipment->manufacturer = $validated['manufacturer'];
            $equipment->model = $validated['model'];
            $equipment->installation_date = $validated['installation_date'];
            $equipment->maintenance_schedule = $validated['maintenance_schedule'] ?? null;
            $equipment->last_maintenance_date = $validated['last_maintenance_date'] ?? null;
            $equipment->next_maintenance_date = $validated['next_maintenance_date'] ?? null;

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if ($equipment->photo_url && Storage::disk('public')->exists($equipment->photo_url)) {
                    Storage::disk('public')->delete($equipment->photo_url);
                }

                $extension = $request->file('image')->extension();
                $filename = $equipment->id.'_equipment.'.$extension;

                $request->file('image')->storeAs('equipments', $filename, 'public');

                $equipment->photo_url = 'equipments/'.$filename;
            }
            $equipment->save();

            return redirect('washing-equipments')->with('success', 'Washing Equipment updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();

            return redirect()->back()
                ->with('error', $firstError)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('WashingEquipment update failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $equipment = WashingEquipment::find(decrypt($id));
        if (!is_null($equipment)) {
            $equipment->delete();
            return redirect('washing-equipments')->with('success', 'Washing Equipment deleted successfully');
        }else{
            return redirect()->back()->with('error', 'Equipment not found');            
        }
    }
}
