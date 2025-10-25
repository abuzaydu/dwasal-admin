<?php

namespace App\Http\Controllers\SandProd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use Session;
use App\Models\Shop;
use App\Models\WashingPlant;

class WashingPlantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Washing Plants';
        $shop = Shop::find(Session::get('shop_id'));
        $plants = WashingPlant::where('shop_id', $shop->id)->select('id', 'plant_name', 'plant_location', 'capacity_per_day', 'unit_of_measure', 'operating_hours', 'launch_date', 'photo_url')->get();
        return view('production.sand.plants.index', compact('page', 'plants'));
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

        $wplant = WashingPlant::where('shop_id', $shop->id)->where('plant_name', $request['plant_name'])->first();
        if (is_null($wplant)) {
            $wplant = new WashingPlant();
            $wplant->shop_id = $shop->id;
            $wplant->plant_name = $request['plant_name'];
            $wplant->plant_location = $request['plant_location'];
            $wplant->capacity_per_day = $request['capacity_per_day'];
            $wplant->unit_of_measure = $request['unit_of_measure'];
            $wplant->operating_hours = $request['operating_hours'];
            $wplant->launch_date = $request['launch_date'];
            $wplant->last_maintenance_date = $request['last_maintenance_date'];
            $wplant->save();

            $location = null;
            if ($request->hasFile('image')) {
                //  Let's do everything here
                if ($request->file('image')->isValid()) {
                    //
                    $validated = $request->validate([
                        'image' => 'mimes:jpeg,png,webp,gif,jfif,avif|max:1014',
                    ]);

                    $img_path = storage_path('/plants/'.$wplant->photo_url);
                    if (File::exists($img_path)) {
                        unlink($img_path);
                    }

                    $extension = $request->image->extension();
                    $request->image->storeAs('/plants', $wplant->id.'_wplant.'.$extension);
                    $location = 'plants/'.$wplant->id.'_wplant.'.$extension;
                }
            }

            $wplant->photo_url = $location;
            $wplant->save();

            return redirect('washing-plants')->with('success', 'Washing Plant added successfully');
        }else {
            return redirect()->back()->with('info', 'Plant with same name ('.$request['plant_name'].') already exists');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Washing Plant';
        $wplant = WashingPlant::find(decrypt($id));

        return view('production.sand.plants.edit', compact('page', 'wplant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $wplant = WashingPlant::find(decrypt($id));
        if (!is_null($wplant)) {
            $wplant->plant_name = $request['plant_name'];
            $wplant->plant_location = $request['plant_location'];
            $wplant->capacity_per_day = $request['capacity_per_day'];
            $wplant->unit_of_measure = $request['unit_of_measure'];
            $wplant->operating_hours = $request['operating_hours'];
            $wplant->launch_date = $request['launch_date'];
            $wplant->last_maintenance_date = $request['last_maintenance_date'];
            $wplant->save();

            $location = null;
            if ($request->hasFile('image')) {
                //  Let's do everything here
                if ($request->file('image')->isValid()) {
                    //
                    $validated = $request->validate([
                        'image' => 'mimes:jpeg,png,webp,gif,jfif,avif|max:1014',
                    ]);

                    $img_path = storage_path('/plants/'.$wplant->photo_url);
                    if (File::exists($img_path)) {
                        unlink($img_path);
                    }

                    $extension = $request->image->extension();
                    $request->image->storeAs('/plants', $wplant->id.'_wplant.'.$extension);
                    $location = 'plants/'.$wplant->id.'_wplant.'.$extension;
                }
            }

            $wplant->photo_url = $location;
            $wplant->save();

            return redirect('washing-plants')->with('success', 'Washing Plant updated successfully');
        }else {
            return redirect()->back()->with('error', 'Plant not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
