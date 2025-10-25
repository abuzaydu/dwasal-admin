<?php

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Company;
use App\Models\DepreciationMethod;

class DepreciationMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Depreciation Methods';
        $title = 'Depreciation Methods';
        $company = Company::find(Session::get('company_id'));
        $depmethods = DepreciationMethod::where('company_id', $company->id)->get();

        return view('accounting.assets.methods.index', compact('page', 'title', 'depmethods'));
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
        $company = Company::find(Session::get('company_id'));
        $depmethod = new DepreciationMethod();
        $depmethod->company_id = $company->id;
        $depmethod->dep_method = $request['dep_method'];
        $depmethod->abbreviation = $request['abbreviation'];
        $depmethod->description = $request['description'];
        $depmethod->save();

        return redirect('dep-methods')->with('success', 'Method added successfully');
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
        $page = 'Edit Depreciation Method';
        $title = 'Edit Depreciation Method';
        $depmethod = DepreciationMethod::find(decrypt($id));

        return view('accounting.assets.methods.edit', compact('page', 'title', 'depmethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $depmethod = DepreciationMethod::find(decrypt($id));
        $depmethod->dep_method = $request['dep_method'];
        $depmethod->abbreviation = $request['abbreviation'];
        $depmethod->description = $request['description'];
        $depmethod->save();

        return redirect('dep-methods')->with('success', 'Method updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $depmethod = DepreciationMethod::find(decrypt($id));
        $record = AssetRecord::where('dep_method', $depmethod->abbreviation)->first();
        if (!is_null($record)) {
            return redirect()->back()->with('info', 'Method cannot be deleted because has records associated with');
        }else{
            $depmethod->delete();
            return redirect()->back()->with('success', 'Method deleted successfully');
        }
    }
}
