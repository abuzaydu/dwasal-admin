<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\DeliveryRate;

class DeliveryRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $page = 'Delivery Rates';
        $drates = DeliveryRate::all();

        return view('settings.drates.index', compact('page', 'drates'));
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
        $drate = new DeliveryRate();
        $drate->company_id = Session::get('company_id');
        $drate->distance = $request['distance'];
        $drate->weight = $request['weight'];
        $drate->rate_amount = $request['rate_amount'];
        $drate->save();

        return redirect('delivery-rates')->with('success', 'Rate Added successfully');
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
        $page = 'Edit Delivery Rate';
        $drate = DeliveryRate::find(decrypt($id));

        return view('settings.drates.edit', compact('page', 'drate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $drate = DeliveryRate::find(decrypt($id));
        $drate->distance = $request['distance'];
        $drate->weight = $request['weight'];
        $drate->rate_amount = $request['rate_amount'];
        $drate->save();

        return redirect('delivery-rates')->with('success', 'Rate Added successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
