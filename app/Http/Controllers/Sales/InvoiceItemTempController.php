<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use Response;
use Auth;
use Log;
use App\Models\Shop;
use App\Models\ProInvoice;
use App\Models\InvoiceItemTemp;
use App\Models\Customer;
use App\Models\User;
use App\Models\Setting;
use App\Models\ProductUnit;

class InvoiceItemTempController extends Controller
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
        $itemtemps = InvoiceItemTemp::where('invoice_item_temps.shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->with('product')->get();
        $temps = array();
        foreach ($itemtemps as $temp) {
            $itemTemp = [
                'id' =>  $temp->id,
                'product_id' => $temp->product_id,
                'product_unit_id' => $temp->product_unit_id,
                'name' => $temp->product->name,
                'slug' => $temp->product->slug,
                'quantity' => $temp->quantity,
                'sold_in' => $temp->sold_in,
                'cost_per_unit' => $temp->cost_per_unit,
                'amount' => $temp->amount,
                'disc_percent' => $temp->disc_percent,
                'discount' => $temp->discount,
                'total_discount' => $temp->total_discount,
                'with_vat' => $temp->with_vat,
                'vat_amount' => round($temp->vat_amount, 2),
                'created_at' => $temp->created_at,
                'updated_at' => $temp->updated_at
            ];
            $itemTemp['units'] = ProductUnit::where('product_id', $temp->product_id)->get()->toArray();
            array_push($temps, array_merge($itemTemp, $itemTemp['units']));
        }
        
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
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $user = Auth::user();
        $sameitems = InvoiceItemTemp::where('product_id', $request['product_id'])->where('shop_id', $shop->id)->where('user_id', $user->id)->count();
        
        if ($sameitems == 0) {
            $bunit = ProductUnit::where('product_id', $request['product_id'])->where('is_basic', true)->first();
            if (is_null($bunit)) {
                $bunit = new ProductUnit();
                $bunit->shop_id = $shop->id;
                $bunit->product_id = $request['product_id'];
                if (empty($request['basic_uom'])) {
                    $bunit->unit_name = 'pc';
                }else{
                    $bunit->unit_name = $request['basic_uom'];
                }
                $bunit->qty_equal_to_basic = 1;
                $bunit->unit_price = $request['retail_price'];
                $bunit->is_basic = true;
                $bunit->save();
            }
            $invoiceItemTemp = new InvoiceItemTemp;
            $invoiceItemTemp->shop_id = $shop->id;
            $invoiceItemTemp->user_id = $user->id;
            $invoiceItemTemp->product_id = $request['product_id'];
            $invoiceItemTemp->product_unit_id = $bunit->id;
            $invoiceItemTemp->quantity = 1;
            $invoiceItemTemp->cost_per_unit = $request['cost_per_unit'] == null ? 0 : $request['cost_per_unit'] ;
            $invoiceItemTemp->amount = $invoiceItemTemp->cost_per_unit*$invoiceItemTemp->quantity;
            if($settings->is_vat_registered && $settings->set_vat_by_default){
                $invoiceItemTemp->with_vat = 'yes';
            }
            if ($invoiceItemTemp->with_vat == 'yes') {
                $vat_amount =  ($invoiceItemTemp->amount-$invoiceItemTemp->total_discount)*($settings->tax_rate/100);
                $invoiceItemTemp->vat_amount = $vat_amount;
            }else{
                $invoiceItemTemp->vat_amount = 0;
            }
            $invoiceItemTemp->save();

            return $invoiceItemTemp;
        }else{
            $warning = 'Ooops!. The product already in selected items.';

            return redirect()->back()->with('warning', $warning);
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
        $invoiceItemTemp =  InvoiceItemTemp::where('id', $id)->where('user_id', Auth::user()->id)->first();
        $settings = Setting::where('shop_id', $shop->id)->first();
        
        if (!is_null($invoiceItemTemp)) {
            $punit = ProductUnit::find($request['product_unit_id']);
            if ($invoiceItemTemp->product_unit_id != $punit->id) {
                $invoiceItemTemp->product_unit_id = $punit->id;
                $invoiceItemTemp->cost_per_unit = $punit->unit_price;
                $invoiceItemTemp->amount = $invoiceItemTemp->cost_per_unit*$invoiceItemTemp->quantity;
                if ($invoiceItemTemp->with_vat == 'yes') {
                    $vat_amount =  ($invoiceItemTemp->amount-$invoiceItemTemp->total_discount)*($settings->tax_rate/100);
                    $invoiceItemTemp->vat_amount = $vat_amount;
                }else{
                    $invoiceItemTemp->vat_amount = 0;
                }
                $invoiceItemTemp->save();
                return $invoiceItemTemp;
            }else{
                if ($request['sold_in'] == $invoiceItemTemp->sold_in) {
                    $qty_sold = $request['quantity'];
                    if ($qty_sold == $invoiceItemTemp->quantity && $qty_sold != 0) {
                        // Log::info($request);
                        if($request['discount'] != $invoiceItemTemp->discount){
                            $invoiceItemTemp->discount = $request['discount'];
                            $invoiceItemTemp->total_discount = $invoiceItemTemp->discount*$invoiceItemTemp->quantity;
                            $invoiceItemTemp->disc_percent = ($invoiceItemTemp->total_discount/$invoiceItemTemp->amount)*100; 
                            $invoiceItemTemp->with_vat = $request['with_vat'];
                            if ($invoiceItemTemp->with_vat == 'yes') {
                                $vat_amount =  ($invoiceItemTemp->amount-$invoiceItemTemp->total_discount)*($settings->tax_rate/100);
                                $invoiceItemTemp->vat_amount = $vat_amount;
                            }else{
                                $invoiceItemTemp->vat_amount = 0;
                            }
                            $invoiceItemTemp->save();
                        }elseif($request['total_discount'] != $invoiceItemTemp->total_discount){
                            $invoiceItemTemp->total_discount = $request['total_discount'];
                            $invoiceItemTemp->discount = (float)$invoiceItemTemp->total_discount/(float)$invoiceItemTemp->quantity;
                            $invoiceItemTemp->disc_percent = ((float)$invoiceItemTemp->total_discount/(float)$invoiceItemTemp->amount)*100;
                            $invoiceItemTemp->with_vat = $request['with_vat']; 
                            if ($invoiceItemTemp->with_vat == 'yes') {
                                $vat_amount =  ((float)$invoiceItemTemp->amount-(float)$invoiceItemTemp->total_discount)*($settings->tax_rate/100);
                                $invoiceItemTemp->vat_amount = $vat_amount;
                            }else{
                                $invoiceItemTemp->vat_amount = 0;
                            }
                            $invoiceItemTemp->save();
                        }else{
                            Log::info($request['disc_percent']);
                            $invoiceItemTemp->disc_percent = $request['disc_percent'];
                            $invoiceItemTemp->total_discount = $invoiceItemTemp->amount*($invoiceItemTemp->disc_percent/100);
                            $invoiceItemTemp->discount = $invoiceItemTemp->total_discount/$invoiceItemTemp->quantity;    
                            $invoiceItemTemp->with_vat = $request['with_vat']; 
                            if ($invoiceItemTemp->with_vat == 'yes') {
                                $vat_amount =  ($invoiceItemTemp->amount-$invoiceItemTemp->total_discount)*($settings->tax_rate/100);
                                $invoiceItemTemp->vat_amount = $vat_amount;
                            }else{
                                $invoiceItemTemp->vat_amount = 0;
                            }
                            $invoiceItemTemp->save();
                        }
                        return response()->json(['status' => 'DISCOUNT UPDATED', 'msg' => 'Discount was updated successfully']);
                    }else{
                        $invoiceItemTemp->quantity = $request['quantity'];
                        $invoiceItemTemp->cost_per_unit = $request['cost_per_unit'];
                        $invoiceItemTemp->amount = (float)$invoiceItemTemp->quantity* (float)$invoiceItemTemp->cost_per_unit;
                        $invoiceItemTemp->total_discount = $invoiceItemTemp->amount*($invoiceItemTemp->disc_percent/100);
                        if ($invoiceItemTemp->quantity > 0) {
                            $invoiceItemTemp->discount = $invoiceItemTemp->total_discount/$invoiceItemTemp->quantity;
                        }else{
                            $invoiceItemTemp->discount = 0;
                        }

                        $invoiceItemTemp->with_vat = $request['with_vat'];
                        if($invoiceItemTemp->with_vat == 'yes'){
                            $invoiceItemTemp->vat_amount = ($invoiceItemTemp->amount-$invoiceItemTemp->total_discount) * ($settings->tax_rate / 100);
                        }else{
                            $invoiceItemTemp->vat_amount = 0;
                        }
                        $invoiceItemTemp->save();
                        return $invoiceItemTemp;
                    }
                }else{
                    $myproduct = $shop->products()->where('id', $invoiceItemTemp->product_id)->first();
                    if ($request['sold_in'] == 'Retail Price') {
                        $invoiceItemTemp->cost_per_unit = $myproduct->pivot->retail_price;
                        $invoiceItemTemp->amount = $invoiceItemTemp->quantity*$invoiceItemTemp->cost_per_unit;
                        $invoiceItemTemp->sold_in = $request['sold_in'];
                        $invoiceItemTemp->save();
                        return $invoiceItemTemp;
                    }else{
                        if ($myproduct->pivot->wholesale_price != 0 && !is_null($myproduct->pivot->wholesale_price)) {
                            
                            $invoiceItemTemp->cost_per_unit = $myproduct->pivot->wholesale_price;
                            $invoiceItemTemp->amount = $invoiceItemTemp->quantity*$invoiceItemTemp->cost_per_unit;
                            $invoiceItemTemp->sold_in = $request['sold_in'];
                            $invoiceItemTemp->save();

                            return $invoiceItemTemp;
                        }
                    }
                }
            }  
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
        InvoiceItemTemp::destroy($id);
    }

    public function ajaxPost(Request $request)
    { 
        $page = 'Point of Sale';
        $title = 'New Ivoice';
        $title_sw = 'Ankara Mpya';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $product = $shop->products()->where('barcode', $request['barcode'])->first();
        if (!is_null($product)) {
             $invoiceItemTemp = InvoiceItemTemp::where('product_id', $product->id)->where('shop_id', $shop->id)->where('user_id', $user->id)->first();
            if (!is_null($invoiceItemTemp)) {
                $invoiceItemTemp->quantity = $invoiceItemTemp->quantity+1;
                $invoiceItemTemp->amount = $invoiceItemTemp->cost_per_unit*$invoiceItemTemp->quantity;
                if ($invoiceItemTemp->with_vat == 'yes') {
                    $vat_amount =  ($invoiceItemTemp->amount-$invoiceItemTemp->total_discount)*($settings->tax_rate/100);
                    $invoiceItemTemp->vat_amount = $vat_amount;
                }else{
                    $invoiceItemTemp->vat_amount = 0;
                }
                $invoiceItemTemp->save();

                return response()->json(['status' => 200, 'msg' => 'Item updated successfully']);                    
            }else{
                $bunit = ProductUnit::where('product_id', $product->id)->where('is_basic', true)->first();
                $invoiceItemTemp = new InvoiceItemTemp;
                $invoiceItemTemp->shop_id = $shop->id;
                $invoiceItemTemp->user_id = $user->id;
                $invoiceItemTemp->product_id = $product->id;
                $invoiceItemTemp->product_unit_id = $bunit->id;
                $invoiceItemTemp->quantity = 1;
                $invoiceItemTemp->cost_per_unit = $product->retail_price;
                $invoiceItemTemp->amount = $invoiceItemTemp->cost_per_unit*$invoiceItemTemp->quantity;
                if($settings->is_vat_registered && $settings->set_vat_by_default){
                    $invoiceItemTemp->with_vat = 'yes';
                }
                if ($invoiceItemTemp->with_vat == 'yes') {
                    $vat_amount =  ($invoiceItemTemp->amount-$invoiceItemTemp->total_discount)*($settings->tax_rate/100);
                    $invoiceItemTemp->vat_amount = $vat_amount;
                }else{
                    $invoiceItemTemp->vat_amount = 0;
                }
                $invoiceItemTemp->save();

                return response()->json(['status' => 200, 'msg' => 'Item added successfully']);
            }
        }else{
            return response()->json(['status' => 400, 'msg' => 'Item not Found']);
        }
    }
}
