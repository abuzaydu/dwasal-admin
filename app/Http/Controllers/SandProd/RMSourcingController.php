<?php

namespace App\Http\Controllers\SandProd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\UnitMeasure;
use App\Models\Shop;
use App\Models\StorageLocation;
use App\Models\RawMaterialSource;
use App\Models\RmSourcing;

class RMSourcingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Raw Material Sourcings';
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

        $today = Carbon::now()->format('Y-m-d');
        $shop = Shop::find(Session::get('shop_id'));
        $rmsources = RawMaterialSource::where('company_id', Session::get('company_id'))->select('id', 'source_name', 'source_location')->get();
        $slocations = StorageLocation::where('shop_id', $shop->id)->where('storage_for', 'Raw Material')->select('id', 'location_name')->get();
        $units = UnitMeasure::select('unit_name')->get();
        $rmsourcings = RmSourcing::where('rm_sourcings.shop_id', $shop->id)->whereBetween('sourcing_date', [$start, $end])->join('raw_material_sources', 'raw_material_sources.id', '=', 'rm_sourcings.raw_material_source_id')->join('storage_locations', 'storage_locations.id', '=', 'rm_sourcings.storage_location_id')->join('users', 'users.id', '=', 'rm_sourcings.user_id')->select('rm_sourcings.id as id', 'sourcing_date', 'qty_received', 'unit_of_measure', 'source_name', 'location_name', 'first_name', 'last_name')->get();

        return view('production.sand.rm-sourcings.index', compact('page', 'today', 'units', 'rmsources', 'slocations', 'rmsourcings', 'is_post_query', 'start_date', 'end_date', 'duration'));
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
        $rmsourcing = new RmSourcing();
        $rmsourcing->shop_id = Session::get('shop_id');
        $rmsourcing->user_id = Auth::user()->id;
        $rmsourcing->raw_material_source_id = $request['raw_material_source_id'];
        $rmsourcing->storage_location_id = $request['storage_location_id'];
        $rmsourcing->sourcing_date = $request['sourcing_date'];
        $rmsourcing->qty_received = $request['qty_received'];
        $rmsourcing->unit_of_measure = $request['unit_of_measure'];
        $rmsourcing->save();

        return redirect('rm-sourcings')->with('success', 'Raw Material Sourcing added successfully');
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
        $page = 'Edit Raw Material Sourcing';
        $shop = Shop::find(Session::get('shop_id'));
        $rmsources = RawMaterialSource::where('company_id', Session::get('company_id'))->select('id', 'source_name', 'source_location')->get();
        $slocations = StorageLocation::where('shop_id', $shop->id)->select('id', 'location_name')->get();
        $units = UnitMeasure::select('unit_name')->get();
        $rmsourcing = RmSourcing::find(decrypt($id));

        return view('production.sand.rm-sourcings.edit', compact('page', 'rmsources', 'slocations', 'units', 'rmsourcing'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rmsourcing = RmSourcing::find(decrypt($id));
        if (!is_null($rmsourcing)) {
            $rmsourcing->raw_material_source_id = $request['raw_material_source_id'];
            $rmsourcing->storage_location_id = $request['storage_location_id'];
            $rmsourcing->sourcing_date = $request['sourcing_date'];
            $rmsourcing->qty_received = $request['qty_received'];
            $rmsourcing->unit_of_measure = $request['unit_of_measure'];
            $rmsourcing->save();
            return redirect('rm-sourcings')->with('success', 'Raw Material Sourcing updated successfully');
        }else{
            return redirect('rm-sourcings')->with('error', 'Item not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rmsourcing = RmSourcing::find(decrypt($id));
        if (!is_null($rmsourcing)) {
            $rmsourcing->delete();
        }
    }
}
