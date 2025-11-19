<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Log;
use Response;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Models\User;
use App\Models\AnSale;
use App\Models\Customer;
use App\Models\Service;
use App\Models\SaleTemp;
use App\Models\ServiceItemTemp;

class ServiceSaleItemTempController extends Controller
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
    public function index($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $saletemp = SaleTemp::find($id);
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        if (!is_null($saletemp)) {
            $servtemps = ServiceItemTemp::where('sale_temp_id', $saletemp->id)->join('services', 'services.id', '=', 'service_item_temps.service_id')->select('service_item_temps.id as id', 'sale_temp_id', 'service_id', 'name', 'no_of_repeatition', 'service_item_temps.price as price', 'total', 'disc_percent', 'discount', 'total_discount', 'with_vat', 'vat_amount')->get();
            $temps = array();
            foreach ($servtemps as $key => $temp) {
                array_push($temps, [
                    'id' => $temp->id, 
                    'sale_temp_id' => $temp->sale_temp_id,
                    'service_id' => $temp->service_id,
                    'name' => $temp->name,
                    'no_of_repeatition' => $temp->no_of_repeatition,
                    'price' => round($temp->price/$saletemp->ex_rate, 2),
                    'total' => round($temp->total/$saletemp->ex_rate, 2),
                    'disc_percent' => $temp->disc_percent,
                    'discount' => round($temp->discount/$saletemp->ex_rate, 2),
                    'total_discount' => round($temp->total_discount/$saletemp->ex_rate, 2),
                    'with_vat' => $temp->with_vat,
                    'vat_amount' => round($temp->vat_amount/$saletemp->ex_rate, 2),
                ]);
            }
            return Response::json(['saletemp' => $saletemp, 'items' => $temps, 'currencies' => $currencies]);
        }else{
            return response()->json(['saletemp' => null]);
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
        $settings = Setting::where('shop_id', $shop->id)->first();
        $user = Auth::user();
        $sale = AnSale::where('shop_id', $shop->id)->count(); 
        $customers = Customer::where('shop_id', $shop->id)->pluck('name', 'id');
        $sameitems = ServiceItemTemp::where('service_id', $request['service_id'])->where('sale_temp_id', $request['sale_temp_id'])->count();
        
        if ($sameitems == 0) {
            $service = $shop->services()->where('id', $request['service_id'])->first();
            if (!is_null($service)) {
                $saleItemTemp = new ServiceItemTemp;
                $saleItemTemp->sale_temp_id = $request['sale_temp_id'];
                $saleItemTemp->service_id = $service->id;
                if (!is_null($service->price)) {
                    $saleItemTemp->price = $service->price;
                }else{
                    $saleItemTemp->price = 0;
                }
                $saleItemTemp->disc_percent = 0;
                $saleItemTemp->discount = 0;
                $saleItemTemp->total = $saleItemTemp->price;
                if($settings->is_vat_registered && $settings->set_vat_by_default){
                    $saleItemTemp->with_vat = 'yes';
                }
                $saleItemTemp->save();
                return $saleItemTemp;
            }
            
        }else{
            $warning = 'Ooops!. The service already in selected items.';
            return response()->json(['status' =>'DUPL', 'msg' => $warning]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(request $request, $id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $saleItemTemp =  ServiceItemTemp::find($id);
        $settings = Setting::where('shop_id', $shop->id)->first();
        if (!is_null($saleItemTemp)) {
            $saletemp = SaleTemp::find($saleItemTemp->sale_temp_id);
            if (!empty($request['servprice']) && $request['servprice'] != $saleItemTemp->price) {
                if($saletemp->currency != $saletemp->defcurr){
                    $saleItemTemp->price = $request['servprice']*$saletemp->ex_rate;
                }else{
                    $saleItemTemp->price = $request['servprice'];
                }
                if ($saleItemTemp->with_vat == 'yes') {
                    $vat_amount =  ($saleItemTemp->total-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                    $saleItemTemp->vat_amount = $vat_amount;
                }else{
                    $saleItemTemp->vat_amount = 0;
                }            
                $saleItemTemp->save();
            }
            
            if ($request['with_vat'] == $saleItemTemp->with_vat) {
                $saleItemTemp->no_of_repeatition = $request['no_of_repeatition'];
                $saleItemTemp->total = $saleItemTemp->no_of_repeatition*$saleItemTemp->price;
                if ($request['disc_percent'] != $saleItemTemp->disc_percent) {
                    $saleItemTemp->disc_percent = $request['disc_percent'];
                    if($saletemp->currency != $saletemp->defcurr){
                        $saleItemTemp->total_discount = ($saleItemTemp->total*($saleItemTemp->disc_percent/100))/$saletemp->ex_rate;
                    }else{
                        $saleItemTemp->total_discount = $saleItemTemp->total*($saleItemTemp->disc_percent/100);
                    }
                }else{
                    if($saletemp->currency != $saletemp->defcurr){
                        $saleItemTemp->total_discount = $request['total_discount']/$saletemp->ex_rate;
                    }else{
                        $saleItemTemp->total_discount = $request['total_discount'];
                    }
                    if ($saleItemTemp->total > 0) {
                        $saleItemTemp->disc_percent = ($saleItemTemp->total_discount/$saleItemTemp->total)*100;
                    }else{
                        $saleItemTemp->disc_percent = 0;
                    }
                }
                if ($saleItemTemp->no_of_repeatition > 0) {
                    $saleItemTemp->discount = $saleItemTemp->total_discount/$saleItemTemp->no_of_repeatition;
                }else{
                    $saleItemTemp->discount = 0;
                }
                
                if ($saleItemTemp->with_vat == 'yes') {
                    $vat_amount =  ($saleItemTemp->total-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                    $saleItemTemp->vat_amount = $vat_amount;
                }else{
                    $saleItemTemp->vat_amount = 0;
                }
                $saleItemTemp->save();
                return $saleItemTemp;          
            }else{
                $saleItemTemp->with_vat = $request['with_vat'];
                if ($saleItemTemp->with_vat == 'yes') {
                    $vat_amount =  ($saleItemTemp->total-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                    $saleItemTemp->vat_amount = $vat_amount;
                }else{
                    $saleItemTemp->vat_amount = 0;
                }
                $saleItemTemp->save();
                return $saleItemTemp;
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ServiceItemTemp  $saleItemTemp
     * @return \Illuminate\Http\Response
     */
    public function updateDiscount(Request $request, $id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $saleItemTemp =  ServiceItemTemp::where('id', $id)->where('user_id', Session::get('user_id'))->first();
        
        if (!is_null($saleItemTemp)) {
         
            $saleItemTemp->no_of_repeatition = $request['no_of_repeatition'];
            $saleItemTemp->discount = $request['discount'];
            $saleItemTemp->total_discount = $saleItemTemp->discount*$saleItemTemp->no_of_repeatition;
            $saleItemTemp->total = $saleItemTemp->no_of_repeatition*$saleItemTemp->price;   
            $saleItemTemp->save();

            return $saleItemTemp;
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ServiceItemTemp  $saleItemTemp
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ServiceItemTemp::destroy($id);
    }
}
