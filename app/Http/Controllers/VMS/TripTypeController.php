<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\TripType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TripTypeController extends Controller
{
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
        try {
            $companyId = Session::get('company_id');
            $request->validate([
                'trip_type' => 'required|string',
                'active' => 'boolean'
            ]);

            TripType::create([
                'company_id' => $companyId,
                'trip_type' => $request->trip_type,
                'active' => $request->input('active',true)
            ]);
            return redirect()->back()->with('success','Trip type created successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'trip_type' => 'required|string',
                'active' => 'boolean'
            ]);
            $tripType = TripType::find($id);
            if(!$tripType){
                return redirect()->back()->with('error','Trip type not found!');
            }
            
            $tripType->update([
                'trip_type' => $request->trip_type,
                'active' => $request->input('active',true)
            ]);
            return redirect()->back()->with('success','Trip type updated successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $tripType = TripType::find($id);
        if(!$tripType){
            return redirect()->back()->with('Trip type Not found');
        }
        //$tripType->delete();
        $tripType -> active = false;
        $tripType->save();
            return redirect()->back()->with('success','Trip type deleted successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
