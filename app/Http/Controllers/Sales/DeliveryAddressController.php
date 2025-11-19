<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryAddress;

class DeliveryAddressController extends Controller
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
        $delvAddress = DeliveryAddress::where('customer_id', $request['customer_id'])->where('plus_code', $request['plus_code'])->first();
        if (is_null($delvAddress)) {
            $delvAddress = new DeliveryAddress();
            $delvAddress->customer_id = $request['customer_id'];
            $delvAddress->longitude = $request['longitude'];
            $delvAddress->latitude = $request['latitude'];
            $delvAddress->plus_code = $request['plus_code'];
            // $delvAddress->address1 = $request['address1'];
            $delvAddress->postcode = $request['postcode'];
            $delvAddress->locality = $request['locality'];
            $delvAddress->state = $request['state'];
            $delvAddress->country = $request['country'];
            $delvAddress->save();

            return redirect()->back()->with('success', 'Delivery Address added successfully');
        }else{
            return redirect()->back()->with('error', 'Address already exists');
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
