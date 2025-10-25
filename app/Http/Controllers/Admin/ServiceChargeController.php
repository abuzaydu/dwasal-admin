<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCharge;
use App\Models\SubscriptionType;

class ServiceChargeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Service Charges';
        $title = 'Service Charges';
        $service_charges = ServiceCharge::join('subscription_types', 'subscription_types.id', 'service_charges.subscription_type_id')->select('service_charges.id as id', 'title', 'initial_pay', 'duration')->get();
        $subscriptions = SubscriptionType::select('id', 'title')->get();

        return view('admin.service-charges.index', compact('page', 'title', 'service_charges', 'subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = "New Service Charge";
        $title = "New Service Charge";
        return view('admin.service-charges.add', compact('page', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $service_charge = ServiceCharge::create([
            'subscription_type_id' => $request['type'],
            'initial_pay' => $request['initial_pay'],
            'next_pay' => $request['initial_pay'],
            'duration' => $request['duration']
        ]);

        return redirect('admin/service-charges')->with('success', 'Service Charge added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $charge = ServiceCharge::find(decrypt($id));
        $subscriptions = SubscriptionType::select('id', 'title')->get();
        $page = 'Edit Service Charge';
        $title = 'Edit Service Charge';
        return view('admin.service-charges.edit', compact('page', 'title', 'charge', 'subscriptions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $service_charge = ServiceCharge::find(decrypt($id));
        $service_charge->subscription_type_id = $request['type'];
        $service_charge->initial_pay = $request['initial_pay'];
        $service_charge->next_pay = $service_charge->initial_pay;
        $service_charge->duration = $request['duration'];
        $service_charge->save();

        return redirect('admin/service-charges')->with('success', 'Service Charge updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ServiceCharge::destroy(decrypt($id));
        return redirect('admin/service-charges')->with('success', 'Service Charge removed successfully');
    }
}
