<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Session;
use Auth;
use Response;
use App\Models\Shop;
use App\Models\ProInvoice;
use App\Models\InvoiceServiceItemTemp;
use App\Models\Customer;
use App\Models\User;
use App\Models\Setting;

class ServiceInvoiceItemTempController extends Controller
{
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
        $temps = InvoiceServiceItemTemp::where('invoice_service_item_temps.shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->join('services', 'services.id', '=', 'invoice_service_item_temps.service_id')->select('invoice_service_item_temps.id as id', 'name', 'repeatition', 'cost_per_unit', 'discount', 'total_discount', 'amount', 'with_vat', 'vat_amount')->get();
         return Response::json(['temps' => $temps]);
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
        $page = 'Point of Sale';
        $title = 'Point of Sale';

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $invoice = ProInvoice::where('shop_id', $shop->id)->count(); 
        $customers = Customer::where('shop_id', $shop->id)->pluck('name', 'id');
        $sameitems = InvoiceServiceItemTemp::where('service_id', $request['service_id'])->where('user_id', $user->id)->where('shop_id', $shop->id)->count();
        
        if ($sameitems == 0) {
            $service = $shop->services()->where('id', $request['service_id'])->first();
            if (!is_null($service)) {
                $invoiceItemTemp = new InvoiceServiceItemTemp;
                $invoiceItemTemp->shop_id = $shop->id;
                $invoiceItemTemp->user_id = $user->id;
                $invoiceItemTemp->service_id = $service->id;
                $invoiceItemTemp->repeatition = 1;
                if (!is_null($service->price)) {
                    $invoiceItemTemp->cost_per_unit = $service->price;
                }else{
                    $invoiceItemTemp->cost_per_unit = 0;
                }
                $invoiceItemTemp->amount = $invoiceItemTemp->cost_per_unit;
                $invoiceItemTemp->save();
                return $invoiceItemTemp;
            }
        }else{
            $warning = 'Ooops!. The service already in selected items.';
            return redirect('pos')->with('warning', $warning);
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
    public function update(Request $request, $id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $invoiceItemTemp =  InvoiceServiceItemTemp::find($id);
        $settings = Setting::where('shop_id', $shop->id)->first();
        
        if (!is_null($invoiceItemTemp)) {
            $invoiceItemTemp->repeatition = $request['repeatition'];
            $invoiceItemTemp->cost_per_unit = $request['cost_per_unit'];
            $invoiceItemTemp->amount = $invoiceItemTemp->repeatition*$request['cost_per_unit'];
            $invoiceItemTemp->total_discount = $request['total_discount'];
            if ($invoiceItemTemp->total > 0) {
                $invoiceItemTemp->disc_percent = ($invoiceItemTemp->total_discount/$invoiceItemTemp->total)*100;
            }else{
                $invoiceItemTemp->disc_percent = 0;
            }
            
            $invoiceItemTemp->discount = $invoiceItemTemp->total_discount/$invoiceItemTemp->repeatition;
            $invoiceItemTemp->with_vat = $request['with_vat'];
             if($invoiceItemTemp->with_vat == 'yes'){
                $invoiceItemTemp->vat_amount = ($invoiceItemTemp->amount-$invoiceItemTemp->total_discount) * ($settings->tax_rate / 100);
            }else{
                $invoiceItemTemp->vat_amount = 0;
            }        
            $invoiceItemTemp->save();

            return $invoiceItemTemp;          
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        InvoiceServiceItemTemp::destroy($id);
    }
}
