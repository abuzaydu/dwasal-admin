<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Ownership;

class OwnershipController extends Controller
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
        //
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
        $ownership = Ownership::where('company_id', Session::get('company_id'))->where('type', $request['type'])->first();
        if (is_null($ownership)) {
            $ownership = new Ownership();
            $ownership->company_id = Session::get('company_id');
            $ownership->type = $request['type'];
            $ownership->description = $request['description'];
            $ownership->save();
        }

        return redirect('vehicles')->with('success', 'Ownership Type added successfully');
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
        $page = 'Edit Ownership';
        $ownership = Ownership::find(decrypt($id));

        return view('vms.ownerships.edit', compact('page', 'ownership'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ownership = Ownership::find(decrypt($id));
        $ownership->type = $request['type'];
        $ownership->description = $request['description'];
        $ownership->save();

        return redirect('vehicles')->with('success', 'Ownership Type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ownership = Ownership::find(decrypt($id));
        if (!is_null($ownership)) {
            $ownership->delete();
        
            return redirect('vehicles')->with('success', 'Ownership Type deleted successfully');
        }
    }
}
