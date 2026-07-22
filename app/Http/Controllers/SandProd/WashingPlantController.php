<?php

namespace App\Http\Controllers\SandProd;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\WashingPlant;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class WashingPlantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Washing Plants';
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
        $plants = WashingPlant::where('shop_id', $shop->id)->whereBetween('created_at', [$start, $end])->select('id', 'plant_name', 'plant_location', 'capacity_per_day', 'unit_of_measure', 'operating_hours', 'launch_date', 'photo_url')->get();
        return view('production.sand.plants.index', compact('page', 'plants', 'is_post_query', 'start_date', 'end_date', 'duration'));
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
                'plant_name'             => 'required|string|max:255',
                'plant_location'         => 'nullable|string|max:255',
                'capacity_per_day'       => 'nullable|numeric',
                'unit_of_measure'        => 'nullable|string|max:50',
                'operating_hours'        => 'nullable|string|max:255',
                'launch_date'            => 'nullable|date',
                'last_maintenance_date'  => 'nullable|date',
                'image'                  => 'nullable|image|mimes:jpeg,png,webp,gif,jfif,avif|max:1024',
            ]);

            $shop = Shop::find(Session::get('shop_id'));
            $wplant = WashingPlant::where('shop_id', $shop->id)->where('plant_name', $request->plant_name)->first();

            if (!is_null($wplant)) {
                return redirect()->back()->with('info', 'Plant with same name ('.$request->plant_name.') already exists');
            }

            $wplant = new WashingPlant();
            $wplant->shop_id = $shop->id;
            $wplant->plant_name = $validated['plant_name'];
            $wplant->plant_location = $validated['plant_location'] ?? null;
            $wplant->capacity_per_day = $validated['capacity_per_day'] ?? null;
            $wplant->unit_of_measure = $validated['unit_of_measure'] ?? null;
            $wplant->operating_hours = $validated['operating_hours'] ?? null;
            $wplant->launch_date = $validated['launch_date'] ?? null;
            $wplant->last_maintenance_date = $validated['last_maintenance_date'] ?? null;
            $wplant->save();

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $extension = $request->file('image')->extension();
                $filename = $wplant->id.'_wplant.'.$extension;

                $request->file('image')->storeAs('plants', $filename, 'public');

                $wplant->photo_url = 'plants/'.$filename;
                $wplant->save();
            }

            return redirect('washing-plants')->with('success', 'Washing Plant added successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();

            return redirect()->back()->with('error', $firstError)->withInput();
        } catch (\Exception $e) {
            Log::error('WashingPlant store failed: '.$e->getMessage(), [
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
        $page = 'Plant Details';
        $wplant = WashingPlant::find(decrypt($id));
        return view('production.sand.plants.show', compact('page', 'wplant'));
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
        try {
            $validated = $request->validate([
                'plant_name'             => 'required|string|max:255',
                'plant_location'         => 'nullable|string|max:255',
                'capacity_per_day'       => 'nullable|numeric',
                'unit_of_measure'        => 'nullable|string|max:50',
                'operating_hours'        => 'nullable|string|max:255',
                'launch_date'            => 'nullable|date',
                'last_maintenance_date'  => 'nullable|date',
                'image'                  => 'nullable|image|mimes:jpeg,png,webp,gif,jfif,avif|max:1024',
            ]);

            $wplant = WashingPlant::find(decrypt($id));

            if (is_null($wplant)) {
                return redirect()->back()->with('error', 'Plant not found');
            }

            $wplant->plant_name = $validated['plant_name'];
            $wplant->plant_location = $validated['plant_location'] ?? null;
            $wplant->capacity_per_day = $validated['capacity_per_day'] ?? null;
            $wplant->unit_of_measure = $validated['unit_of_measure'] ?? null;
            $wplant->operating_hours = $validated['operating_hours'] ?? null;
            $wplant->launch_date = $validated['launch_date'] ?? null;
            $wplant->last_maintenance_date = $validated['last_maintenance_date'] ?? null;

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if ($wplant->photo_url && Storage::disk('public')->exists($wplant->photo_url)) {
                    Storage::disk('public')->delete($wplant->photo_url);
                }

                $extension = $request->file('image')->extension();
                $filename = $wplant->id.'_wplant.'.$extension;

                $request->file('image')->storeAs('plants', $filename, 'public');

                $wplant->photo_url = 'plants/'.$filename;
            }

            $wplant->save();

            return redirect('washing-plants')->with('success', 'Washing Plant updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();

            return redirect()->back()->with('error', $firstError)->withInput();
        } catch (\Exception $e) {
            Log::error('WashingPlant update failed: '.$e->getMessage(), [
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
        //
    }
}
