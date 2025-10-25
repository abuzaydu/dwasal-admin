<?php

namespace App\Http\Controllers\SandProd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use Session;
use App\Models\Shop;
use App\Models\WashingPlant;
use App\Models\WashingEquipment;

class WashingEquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Washing Equipments';
        $shop = Shop::find(Session::get('shop_id'));
        $wplants = WashingPlant::where('shop_id', Session::get('shop_id'))->get();
        $equipments = WashingEquipment::where('shop_id', $shop->id)->select('id', 'equipment_name', 'equipment_type', 'manufacturer', 'model', 'installation_date', 'photo_url', 'next_maintenance_date')->get();
        return view('production.sand.equipments.index', compact('page', 'wplants', 'equipments'));
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
        $shop = Shop::find(Session::get('shop_id'));

        $equipment = WashingEquipment::where('shop_id', $shop->id)->where('equipment_code', $request['equipment_code'])->first();
        if (is_null($equipment)) {
            $equipment = new WashingEquipment();
            $equipment->shop_id = $shop->id;
            $equipment->washing_plant_id = $request['washing_plant_id'];
            $equipment->equipment_code = $request['equipment_code'];
            $equipment->equipment_name = $request['equipment_name'];
            $equipment->equipment_type = $request['equipment_type'];
            $equipment->manufacturer = $request['manufacturer'];
            $equipment->model = $request['model'];
            $equipment->installation_date = $request['installation_date'];
            $equipment->maintenance_schedule = $request['maintenance_schedule'];
            $equipment->last_maintenance_date = $request['last_maintenance_date'];
            $equipment->next_maintenance_date = $request['next_maintenance_date'];
            $equipment->save();

            $location = null;
            if ($request->hasFile('image')) {
                //  Let's do everything here
                if ($request->file('image')->isValid()) {
                    //
                    $validated = $request->validate([
                        'image' => 'mimes:jpeg,png,webp,gif,jfif,avif|max:1014',
                    ]);

                    $img_path = storage_path('/equipments/'.$equipment->photo_url);
                    if (File::exists($img_path)) {
                        unlink($img_path);
                    }

                    $extension = $request->image->extension();
                    $request->image->storeAs('/equipments', $equipment->id.'_equipment.'.$extension);
                    $location = 'equipments/'.$equipment->id.'_equipment.'.$extension;
                }
            }

            $equipment->photo_url = $location;
            $equipment->save();

            return redirect('washing-equipments')->with('success', 'Washing Equipment added successfully');
        }else {
            return redirect()->back()->with('info', 'Equipment with same Code ('.$request['equipment_code'].') already exists');
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
        $equipment = WashingEquipment::find(decrypt($id));
        if (!is_null($equipment)) {
            $equipment->washing_plant_id = $request['washing_plant_id'];
            $equipment->equipment_code = $request['equipment_code'];
            $equipment->equipment_name = $request['equipment_name'];
            $equipment->equipment_type = $request['equipment_type'];
            $equipment->manufacturer = $request['manufacturer'];
            $equipment->model = $request['model'];
            $equipment->installation_date = $request['installation_date'];
            $equipment->maintenance_schedule = $request['maintenance_schedule'];
            $equipment->last_maintenance_date = $request['last_maintenance_date'];
            $equipment->next_maintenance_date = $request['next_maintenance_date'];
            $equipment->save();

            $location = null;
            if ($request->hasFile('image')) {
                //  Let's do everything here
                if ($request->file('image')->isValid()) {
                    //
                    $validated = $request->validate([
                        'image' => 'mimes:jpeg,png,webp,gif,jfif,avif|max:1014',
                    ]);

                    $img_path = storage_path('/equipments/'.$equipment->photo_url);
                    if (File::exists($img_path)) {
                        unlink($img_path);
                    }

                    $extension = $request->image->extension();
                    $request->image->storeAs('/equipments', $equipment->id.'_equipment.'.$extension);
                    $location = 'equipments/'.$equipment->id.'_equipment.'.$extension;
                }
            }else{
                $location = $equipment->photo_url;
            }

            $equipment->photo_url = $location;
            $equipment->save();

            return redirect('washing-equipments')->with('success', 'Washing Equipment updated successfully');
        }else {
            return redirect()->back()->with('error', 'Equipment not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $equipment = WashingEquipment::find(decrypt($id));
        if (!is_null($equipment)) {
            
        }else{
            return redirect()->back()->with('error', 'Equipment not found');            
        }
    }
}
