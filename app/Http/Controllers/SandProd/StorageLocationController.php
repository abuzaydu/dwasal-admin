<?php

namespace App\Http\Controllers\SandProd;

use App\Http\Controllers\Controller;
use App\Models\ProductionRun;
use App\Models\RmSourcing;
use App\Models\Stock;
use App\Models\StorageLocation;
use App\Models\UnitMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class StorageLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Storage Locations';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
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
        $duration = '';
        $units = UnitMeasure::select('unit_name')->get();
        $slocations = StorageLocation::where('shop_id', Session::get('shop_id'))->whereBetween('created_at', [$start, $end])->get();

        return view('production.sand.storage-locations.index', compact('page', 'units', 'slocations', 'is_post_query', 'start_date', 'end_date', 'duration'));
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
        $slocation = StorageLocation::where('location_name', $request['location_name'])->where('shop_id', Session::get('shop_id'))->first();
        if (is_null($slocation)) {
            $slocation = new StorageLocation();
            $slocation->shop_id = Session::get('shop_id');
            $slocation->location_name = $request['location_name'];
            $slocation->longitude = $request['longitude'];
            $slocation->latitude = $request['latitude'];
            $slocation->storage_for = $request['storage_for'];
            $slocation->capacity = $request['capacity'];
            $slocation->unit = $request['unit'];
            $slocation->save();

            return redirect('storage-locations')->with('success', 'Storage Location added successfully');
        }else{
            return redirect('storage-locations')->with('info', 'Storage Location with same name already exists');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Storage Location Details';
        $slocation = StorageLocation::find(decrypt($id));
        $rmsourcings = RmSourcing::where('storage_location_id', $slocation->id)->join('raw_material_sources', 'raw_material_sources.id', '=', 'rm_sourcings.raw_material_source_id')->join('users', 'users.id', '=', 'rm_sourcings.user_id')->select('rm_sourcings.id as id', 'sourcing_date', 'qty_received', 'unit_of_measure', 'source_name', 'first_name', 'last_name')->get();
        $prodruns = ProductionRun::where('storage_location_id', $slocation->id)->join('users', 'users.id', '=', 'production_runs.user_id')->join('washing_plants', 'washing_plants.id', '=', 'production_runs.washing_plant_id')->select('production_runs.id as id', 'plant_name', 'first_name', 'last_name', 'pr_no', 'start_time', 'end_time', 'input_quantity', 'output_quantity', 'waste_water_quantity')->get();

        $total_received = RmSourcing::where('storage_location_id', $slocation->id)->sum('qty_received');
        $total_used = ProductionRun::where('storage_location_id', $slocation->id)->sum('input_quantity');

        $stocks = Stock::where('stocks.storage_location_id', $slocation->id)->join('products', 'products.id', '=', 'stocks.product_id')->select('stocks.id as id', 'name', 'quantity_in', 'stock_date')->get();

        $total_in = Stock::where('storage_location_id', $slocation->id)->sum('quantity_in');
        $total_sold = 0;

        return view('production.sand.storage-locations.show', compact('page', 'slocation', 'rmsourcings', 'total_received', 'total_used', 'total_in', 'total_sold'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Storage Location';
        $units = UnitMeasure::select('unit_name')->get();
        $slocation = StorageLocation::find(decrypt($id));

        return view('production.sand.storage-locations.edit', compact('page', 'units', 'slocation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $slocation = StorageLocation::find(decrypt($id));
        if (!is_null($slocation)) {
            $slocation->location_name = $request['location_name'];
            $slocation->longitude = $request['longitude'];
            $slocation->latitude = $request['latitude'];
            $slocation->storage_for = $request['storage_for'];
            $slocation->capacity = $request['capacity'];
            $slocation->unit = $request['unit'];
            $slocation->save();

            return redirect('storage-locations')->with('success', 'Storage Location updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $slocation = StorageLocation::find(decrypt($id));
        if (!is_null($slocation)) {
            $slocation->delete();

            return redirect('storage-locations')->with('success', 'Storage Location deleted successfully');
        }
    }
}
