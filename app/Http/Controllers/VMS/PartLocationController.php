<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\PartLocation;

class PartLocationController extends Controller
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
        $location = PartLocation::where('company_id', Session::get('company_id'))->where('name', $request->name)->first();
        if (is_null($location)) {
            $location = new PartLocation();
            $location->company_id = Session::get('company_id');
            $location->name = $request->name;
            $location->room = $request->room;
            $location->self = $request->self;
            $location->drawer = $request->drawer;
            $location->dimension = $request->dimension;
            $location->capacity = $request->capacity;
            $location->save();

            return redirect('parts')->with('success', 'Part Category added successfully');
        }else{
            return redirect('parts')->with('info', 'Part Category already added');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
