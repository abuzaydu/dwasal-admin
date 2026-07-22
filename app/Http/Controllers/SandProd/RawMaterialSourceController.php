<?php

namespace App\Http\Controllers\SandProd;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\RawMaterialSource;
use App\Models\RMSourcing;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class RawMaterialSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $page = 'Raw Material Sources';
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
        $company = Company::find(Session::get('company_id'));

        $rmsources = RawMaterialSource::where('company_id', $company->id)->whereBetween('created_at', [$start, $end])->get();

        return view('production.sand.rm-sources.index', compact('page', 'rmsources', 'is_post_query', 'start_date', 'end_date', 'duration'));
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
        $rmsource = RawMaterialSource::where('company_id', Session::get('company_id'))->where('source_name', $request['source_name'])->first();
        if (is_null($rmsource)) {
            $rmsource = new RawMaterialSource();
            $rmsource->company_id = Session::get('company_id');
            $rmsource->source_name = $request['source_name'];
            $rmsource->source_location = $request['source_location'];
            $rmsource->contact_person = $request['contact_person'];
            $rmsource->contact_number = $request['contact_number'];
            $rmsource->material_type = $request['material_type'];
            $rmsource->save();

            return redirect('raw-material-sources')->with('success', 'Raw Material Source add successfully');
        }else{
            return redirect('raw-material-sources')->with('info', 'Source with sane name already registered');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Source Details';
        $rmsource = RawMaterialSource::find(decrypt($id));

        return view('production.sand.rm-sources.show', compact('page', 'rmsource'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Raw Material Source';
        $rmsource = RawMaterialSource::find(decrypt($id));

        return view('production.sand.rm-sources.edit', compact('page', 'rmsource'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rmsource = RawMaterialSource::find(decrypt($id));
        if (!is_null($rmsource)) {
            $rmsource->source_name = $request['source_name'];
            $rmsource->source_location = $request['source_location'];
            $rmsource->contact_person = $request['contact_person'];
            $rmsource->contact_number = $request['contact_number'];
            $rmsource->material_type = $request['material_type'];
            $rmsource->save();

            return redirect('raw-material-sources')->with('success', 'Raw Material Source updated successfully');
        }else{

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rmsource = RawMaterialSource::find(decrypt($id));
        if (!is_null($rmsource)) {
            $sourcings = RMSourcing::where('raw_material_source_id', $rmsource->id)->count();
            if ($sourcings > 0) {
                return redirect()->back()->with('info', 'Raw Material Source with Sourcing data cannot be deleted');
            }else{
                $rmsource->delete();
                return redirect()->back()->with('success', 'Raw Material Source deleted successfully');
            }
        }else{
            return redirect()->back()->with('error', 'Item not found');
        }
    }
}
