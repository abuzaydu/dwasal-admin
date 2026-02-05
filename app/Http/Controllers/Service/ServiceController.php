<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Log;
use Response;
use App\Models\Company;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Models\Service;
use App\Models\Device;
use App\Models\Grade;
use \Carbon\Carbon;

class ServiceController extends Controller
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
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Services';
        $title = 'My Services';
        $title_sw = 'Huduma Yangu';
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
    
            $services = Service::where('shop_id', $shop->id)->get();
            $settings = Setting::where('shop_id', $shop->id)->first();
            $devices = Device::where('shop_id', $shop->id)->get();
            $grades = Grade::where('shop_id', $shop->id)->get();
            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            return view('services.index', compact('page', 'title', 'title_sw', 'services', 'devices', 'grades', 'settings', 'defcurr'));
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

    public function AutoCode(){
        $shop = Shop::find(Session::get('shop_id'));
        $v = '';
        if(preg_match_all('/\b(\w)/',strtoupper($shop->name),$m)) {
            // Log::info($m);
            $v = implode('',$m[1]); // $v is now SOQTU
        }
        $service = $shop->services()->orderBy('id', 'desc')->first();
        if (!is_null($service)) {
            $last = str_replace($v.'/S-', '', $service->code);
            $lastcode = (int)$last;
            // Log::info($last);
            $id = $v.'/S-'.sprintf('%03d', $lastcode+1);
            return Response::json($id);   
        }else{
            $id = $v.'/S-'.sprintf('%03d', 1);
            return Response::json($id); 
        }
    }

    public function recreateCodes()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $company = Company::find(Session::get('company_id'));
        $v = '';
        if(preg_match_all('/\b(\w)/',strtoupper($company->name),$m)) {
            // Log::info($m);
            $v = implode('',$m[1]); // $v is now SOQTU
        }

        $services = $shop->services()->get();
        foreach ($services as $key => $service) {
            $code = $v.'/SERV-'.sprintf('%03d', $key+1);
            $service->pivot->code = $code;
            $service->pivot->save();
               
        }
    }


    public function getAutoCode()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $company = Company::find(Session::get('company_id'));
        $v = '';
        if(preg_match_all('/\b(\w)/',strtoupper($company->name),$m)) {
            // Log::info($m);
            $v = implode('',$m[1]); // $v is now SOQTU
        }
        $service = $shop->services()->select('code')->orderBy('services.id', 'desc')->first();
        if (!is_null($service)) {
            if (!empty($service->code)) {
                $last = str_replace($v.'/S-', '', $service->code);
                $lastcode = (int)$last;
                Log::info($last);
                $id = $v.'/S-'.sprintf('%03d', $lastcode+1);
                return $id;
            }else{
                $id = $v.'/S-'.sprintf('%03d', 1);
                return $id; 
            }   
        }else{
            $id = $v.'/S-'.sprintf('%03d', 1);
            return $id; 
        }
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
        $service = Service::where('name', $request['name'])->where('shop_id', $shop->id)->first();

        $code = $this->getAutoCode();
        if (!empty($request['code']) && $request['code'] != '') {
            $code = $request['code'];
        }
        
        if (is_null($service)) {
            $service = new Service();
            $service->shop_id = $shop->id;
            $service->code = $code;
            $service->name = $request['name'];
            $service->description = $request['description'];
            $service->price = $request['price'];
            $service->time_created = Carbon::now();
            $service->save();
        
            $message = 'This Service already was succesfully added to your business service list';
            return redirect()->back()->with('success', $message);
        }else{
            $message = 'This Service already exists in your business service list';
            return redirect()->back()->with('info', $message);
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
        $service = Service::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($service)) {
            $page = 'Services';
            $title = 'Service Details';
            $title_sw = 'Maelezo ya Huduma';

            return view('services.show', compact('page', 'title', 'title_sw', 'service'));
        }else{
            return redirect('forbiden');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = Service::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));

        if (!is_null($service)) {

            $page = 'Services';
            $title = 'Edit Service';
            $title_sw = 'Hariri Huduma';

            return view('services.edit', compact('page', 'title', 'title_sw', 'service',));
        }else{
            return redirect('forbiden');
        }
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
        $shop = Shop::find(Session::get('shop_id'));
        $service = Service::find(decrypt($id));
        $service->name = $request['name'];
        $service->code = $request['code'];
        $service->description = $request['description'];
        $service->price = $request['price'];
        $service->save();

        $message = 'This Service  was succesfully updated.';
        return redirect('services')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $service  = Service::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));

        if (!is_null($service)) {
            $service->delete();
            $message = 'This Service  was succesfully removed.';
            return redirect('services')->with('success', $message);
        }else{
            return redirect()->back()->with('error', 'Service not Found');
        }
    }

}
