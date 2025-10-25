<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\Device;
use Log;

class DeviceController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Devices/Properties';
        $title = 'My Devices/Properties';
        $title_sw = 'Vifaa Vyangu';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $devices = Device::where('shop_id', $shop->id)->get();
        foreach ($devices as $key => $value) {
            if ( preg_match('/\s/',$value->device_number) ){
               Log::info("The name (".$value->device_number.") has the space");
               $device_number = str_replace(' ', '', $value->device_number);
               $value->device_number = $device_number;
               $value->save();
            } else {
               Log::info("The Name (".$value->device_number.") has not the space");
            }
        }
        return view('services.devices.index', compact('page', 'title', 'title_sw', 'settings', 'devices'));    
    }

    public function autoSearch(Request $request)
    {
        if ($request->ajax()) {
            $shop = Shop::find(Session::get('shop_id'));
            if (!empty($request->search_key) && strlen($request->search_key) >= 2) {
                $data = Device::where('shop_id', $shop->id)->where(\DB::raw('CONCAT_WS(" ", `device_number`, `device_name`)'),'LIKE', '%'.$request->search_key.'%')->select('id', 'device_number', 'device_name')->get();

                return $data;
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        $shop = Shop::find(Session::get('shop_id'));
        $device = Device::where('device_number', $request['device_number'])->where('shop_id', $shop->id)->first();
        if (is_null($device)) {
            $device = new Device();
            $device->shop_id = $shop->id;
            $device->device_number = $request['device_number'];
            $device->device_name = $request['device_name'];
            $device->device_cost = $request['device_cost'];
            $device->save();
            return redirect()->back()->with('success', 'Your Device registered successfully');
        }else{
            return redirect()->back()->with('info', 'Device already registered');
        }
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
        $device = Device::find(decrypt($id));
        $page = 'Services';
        $title = 'Edit Device info';
        $title_sw = 'Hariri Maelezo ya Kifaa';

        return view('services.edit-device', compact('page', 'title', 'title_sw', 'device'));
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
        $device = Device::find($id);
        $device->device_number = $request['device_number'];
        $device->device_name = $request['device_name'];
        $device->device_cost = $request['device_cost'];
        $device->save();

        return redirect('devices')->with('success', 'Device successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $device = Device::find(decrypt($id));
        if (!is_null($device)) {
            $device->delete();
        }

        return redirect()->back()->with('success', 'Device successfully deleted');
    }
}
