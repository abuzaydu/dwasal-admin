<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use \Response;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\AnSale;
use App\Models\Customer;
use App\Models\AnSaleItem;
use App\Models\Setting;
use App\Models\SaleTemp;
use App\Models\SaleItemTemp;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\ShopCurrency;
use App\Models\DiscountApproval;
use App\Notifications\DiscountApprovalNotification;
use Log;

class SaleItemTempController extends Controller
{/**
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
        $sale_mode = 'Retail Price';
        if (!is_null(Session::get('sold_in'))) {
            $sale_mode = Session::get('sold_in');
        }
        $saletemp = SaleTemp::find($id);
        if (!is_null($saletemp)) {
                
            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $itemtemps = SaleItemTemp::where('sale_temp_id', $id)->join('products', 'products.id', 'sale_item_temps.product_id')->select('sale_item_temps.id as id', 'sale_temp_id', 'product_id', 'product_unit_id', 'product_code', 'name', 'slug', 'curr_stock', 'quantity_sold', 'sold_in', 'used_stock', 'sale_item_temps.unit_cost as unit_cost', 'buying_price', 'sale_item_temps.retail_price as retail_price', 'disc_percent', 'discount', 'price', 'total_discount', 'with_vat', 'vat_amount', 'sale_item_temps.created_at as created_at', 'sale_item_temps.updated_at as updated_at')->orderBy('id', 'desc')->get();
            $discapprovals = DiscountApproval::where('sale_temp_id', $saletemp->id)->where('status', 'Awaiting for Approval')->count();
            // Log::info($discapprovals);
            $salemodes = [
                'Retail Price',
                'Wholesale Price', 
            ];
            $temps = array();
            foreach ($itemtemps as $temp) {
                $itemTemp = [
                    'id' =>  $temp->id,
                    'sale_temp_id' =>  $temp->sale_temp_id,
                    'product_id' => $temp->product_id,
                    'product_unit_id' => $temp->product_unit_id,
                    'product_code' => $temp->product_code,
                    'name' => $temp->name,
                    'slug' => $temp->slug,
                    'curr_stock' => $temp->curr_stock,
                    'quantity_sold' => $temp->quantity_sold,
                    'sold_in' => $temp->sold_in,
                    'used_stock' => $temp->used_stock,
                    'unit_cost' => $temp->unit_cost,
                    'buying_price' => $temp->buying_price,
                    'retail_price' => round($temp->retail_price/$saletemp->ex_rate, 2),
                    'disc_percent' => $temp->disc_percent,
                    'discount' => round($temp->discount/$saletemp->ex_rate, 2),
                    'price' => round($temp->price/$saletemp->ex_rate, 2),
                    'total_discount' => round($temp->total_discount/$saletemp->ex_rate, 2),
                    'with_vat' => $temp->with_vat,
                    'vat_amount' => round($temp->vat_amount/$saletemp->ex_rate, 2),
                    'created_at' => $temp->created_at,
                    'updated_at' => $temp->updated_at
                ];

                $itemTemp['units'] = ProductUnit::where('product_id', $temp->product_id)->get()->toArray();
                $itemTemp['salemodes'] = $salemodes;
                array_push($temps, array_merge($itemTemp, $itemTemp['units']));
            }
            return Response::json(['saletemp' => $saletemp, 'items' => $temps, 'currencies' => $currencies, 'sale_mode' => $sale_mode, 'salemodes' => $salemodes, 'discapprovals' => $discapprovals]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sales.create');
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
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        if (!empty($request['sale_temp_id'])) {
                
            $sameitems = SaleItemTemp::where('product_id', $request['product_id'])->where('sale_temp_id', $request['sale_temp_id'])->count();
            $sold_in = 'Retail Price';
            if (!empty($request['sold_in'])) {
                $sold_in = $request['sold_in'];
            }

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
                
                $instock = 0;
                if (!empty($request['in_stock'])) {
                    $instock = $request['in_stock'];
                }
                if ($instock <= 0 && !$settings->sale_with_low_stock) {
                    return response()->json(['status' => 'LOW', 'msg' => 'Selected Item si currently out of stock']);
                }else{
                    $saleItemTemp = new SaleItemTemp;
                    $saleItemTemp->sale_temp_id = $request['sale_temp_id'];
                    $saleItemTemp->product_id = $request['product_id'];
                    $saleItemTemp->product_unit_id = $bunit->id;
                    if ($instock < 1) {
                        $saleItemTemp->quantity_sold = 0;
                    }else {
                        $saleItemTemp->quantity_sold = 1;
                    }
                    $saleItemTemp->curr_stock = $instock;
                    if (!empty($request['unit_cost'])) {
                        $saleItemTemp->unit_cost = $request['unit_cost'];
                    }else{
                        $saleItemTemp->unit_cost = 0;
                    }
                    $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                    $saleItemTemp->retail_price = $request['retail_price'];
                    $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                    $saleItemTemp->discount = 0;
                    $saleItemTemp->used_stock = 'Old';
                    $saleItemTemp->sold_in = $sold_in;
                    if($settings->is_vat_registered && $settings->set_vat_by_default){
                        $saleItemTemp->with_vat = 'yes';
                    }
                                        
                    if ($saleItemTemp->with_vat == 'yes') {
                        $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                        $saleItemTemp->vat_amount = $vat_amount;
                    }else{
                        $saleItemTemp->vat_amount = 0;
                    }
                    $saleItemTemp->save();
                    return $saleItemTemp;
                }
            }else{
                $warning = 'Ooops!. The product already in selected items.';
                return response()->json(['status' =>'DUPL', 'msg' => $warning]);
            }
        }
    }


    public function ajaxPost(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
         
        $customers = Customer::where('shop_id', $shop->id)->pluck('name', 'id');
    
        $product = $shop->products()->where('barcode', $request['barcode'])->first();
        if (!is_null($product)) {
            $bunit = ProductUnit::where('product_id', $product->id)->where('is_basic', true)->first();
            if (is_null($bunit)) {
                $bunit = new ProductUnit();
                $bunit->product_id = $product->id;
                $bunit->unit_name = $product->basic_uom;
                $bunit->qty_equal_to_basic = 1;
                $bunit->unit_price = $product->retail_price;
                $bunit->is_basic = true;
                $bunit->save();
            }
            
            $itemtemp = SaleItemTemp::where('product_id', $product->product_id)->where('sale_temp_id', $request['sale_temp_id'])->first();
            if (!is_null($itemtemp)) {
                if (($itemtemp->quantity_sold+1) <= $itemtemp->curr_stock) {
                    
                    $itemtemp->quantity_sold = $itemtemp->quantity_sold+1;
                    $itemtemp->buying_price = $itemtemp->unit_cost*$itemtemp->quantity_sold;
                    $itemtemp->price = $itemtemp->retail_price*$itemtemp->quantity_sold;
                    $itemtemp->save();
                }else{
                    return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Product '.$product->name.' is currently less than.'.($itemtemp->quantity_sold+1)]);
                }
            }else{
                if ($product->in_stock == 0) {

                    return response()->json(['status' => 'ZERO', 'msg' => 'The stock of '.$product->name.' is currently ZERO. Please Purchase new Stock.']);
                } else {
                    if (is_null($product->unit_cost)) {

                        $saleItemTemp = new SaleItemTemp;
                        $saleItemTemp->sale_temp_id = $request['sale_temp_id'];
                        $saleItemTemp->product_id = $product->product_id;
                        $saleItemTemp->product_unit_id = $bunit->id;
                        $saleItemTemp->quantity_sold = 1;
                        $saleItemTemp->curr_stock = $product->in_stock;
                        $saleItemTemp->unit_cost = 0;
                        $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                        $saleItemTemp->retail_price = $product->retail_price;
                        $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                        $saleItemTemp->discount = 0;
                        $saleItemTemp->used_stock = 'Old';
                        $saleItemTemp->sold_in = 'Retail Price';
                        $saleItemTemp->save();
                        // return $saleItemTemp;
                    }else{
                        $saleItemTemp = new SaleItemTemp;
                        $saleItemTemp->shop_id = $shop->id;
                        $saleItemTemp->user_id = $user->id;
                        $saleItemTemp->product_id = $product->product_id;
                        $saleItemTemp->product_unit_id = $bunit->id;
                        $saleItemTemp->quantity_sold = 1;
                        $saleItemTemp->curr_stock = $product->in_stock;
                        $saleItemTemp->unit_cost = $product->unit_cost;
                        $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                        $saleItemTemp->retail_price = $product->retail_price;
                        $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                        $saleItemTemp->discount = 0;
                        $saleItemTemp->used_stock = 'Old';
                        $saleItemTemp->sold_in = 'Retail Price';
                        $saleItemTemp->save();
                        // return $saleItemTemp;
                    }
                }
            }
            // return redirect('pos');
            return response()->json(['status' => 'OK']);
        }else{
            $warning = "Sorry, Scanned barcode value does not match any of your products . Please Try Again";
            return response()->json(['status' => 'Fail', 'msg' => $warning]);
            // return redirect('pos')->with('warning', $warning);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\SaleItemTemp  $saleItemTemp
     * @return \Illuminate\Http\Response
     */
    public function show(SaleItemTemp $saleItemTemp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SaleItemTemp  $saleItemTemp
     * @return \Illuminate\Http\Response
     */
    public function edit(SaleItemTemp $saleItemTemp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SaleItemTemp  $saleItemTemp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $saleItemTemp =  SaleItemTemp::find($id);
        $settings = Setting::where('shop_id', $shop->id)->first();
        if (!is_null($saleItemTemp) ) {
            $punit = ProductUnit::find($request['product_unit_id']);
            if ($saleItemTemp->product_unit_id != $punit->id) {
                $saleItemTemp->product_unit_id = $punit->id;
                $saleItemTemp->retail_price = $punit->unit_price;
                $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                if ($saleItemTemp->with_vat == 'yes') {
                    $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                    $saleItemTemp->vat_amount = $vat_amount;
                }else{
                    $saleItemTemp->vat_amount = 0;
                }
                $saleItemTemp->save();

                return response()->json(['status' => 'UNIT CHANGE', 'msg' => 'Unit changed successfully']);
            }else{
                $saletemp = SaleTemp::find($saleItemTemp->sale_temp_id);
                if (!empty($request['retail_price']) && $request['retail_price'] != $saleItemTemp->retail_price) {
                    if($saletemp->currency != $saletemp->defcurr){
                        $saleItemTemp->retail_price = $request['retail_price']*$saletemp->ex_rate;
                    }else{
                        $saleItemTemp->retail_price = $request['retail_price'];
                    }
                    $saleItemTemp->price = (float)$saleItemTemp->retail_price*(float)$saleItemTemp->quantity_sold;
                    $saleItemTemp->save();
                }

                if (!empty($request['used_stock'])) {
                    if ($request['used_stock'] == 'New') {
                                
                        $lateststock = Stock::where('product_id', $saleItemTemp->product_id)->where('shop_id', $shop->id)->latest()->first();
                        // $saleItemTemp->quantity_sold = ;
                        $saleItemTemp->unit_cost = $lateststock->unit_cost;
                        $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                        // $saleItemTemp->discount = $request('discount');
                        $saleItemTemp->total_discount = $saleItemTemp->quantity_sold*$saleItemTemp->discount;
                        $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                        $saleItemTemp->used_stock = $request['used_stock'];
                        $saleItemTemp->save();
                        return $saleItemTemp;
                    }else{
                        $shopproduct = $shop->products()->where('id', $saleItemTemp->product_id)->first();
                        // $saleItemTemp->quantity_sold = $request('quantity_sold');
                        $saleItemTemp->unit_cost = $shopproduct->unit_cost;
                        $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                        // $saleItemTemp->discount = $request('discount');
                        $saleItemTemp->total_discount = $saleItemTemp->quantity_sold*$saleItemTemp->discount;
                        $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                        $saleItemTemp->used_stock = $request['used_stock'];
                        $saleItemTemp->save();

                        return $saleItemTemp;
                    }
                }
                    
                if ($request['sold_in'] == $saleItemTemp->sold_in) {
                    if ($request['with_vat'] == $saleItemTemp->with_vat) {
                        if (!is_null($saleItemTemp)) {
                            $qty_sold = $request['quantity_sold'];
                            if ($qty_sold == $saleItemTemp->quantity_sold && $qty_sold != 0) {
                                if($request['disc_percent'] != $saleItemTemp->disc_percent){
                                    $saleItemTemp->disc_percent = $request['disc_percent'];
                                    if($saletemp->currency != $saletemp->defcurr){
                                        $saleItemTemp->total_discount = ($saleItemTemp->price*((float)$saleItemTemp->disc_percent/100));
                                    }else{
                                        $saleItemTemp->total_discount = $saleItemTemp->price*((float)$saleItemTemp->disc_percent/100);
                                    }
                                    $saleItemTemp->discount = $saleItemTemp->total_discount/$saleItemTemp->quantity_sold;    
                                    $saleItemTemp->with_vat = $request['with_vat']; 
                                    if ($saleItemTemp->with_vat == 'yes') {
                                        $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                                        $saleItemTemp->vat_amount = $vat_amount;
                                    }else{
                                        $saleItemTemp->vat_amount = 0;
                                    }
                                    $saleItemTemp->save();
                                }elseif($request['discount'] != $saleItemTemp->discount){
                                    if($saletemp->currency != $saletemp->defcurr){
                                        $saleItemTemp->discount = $request['discount']*$saletemp->ex_rate;
                                    }else{
                                        $saleItemTemp->discount = $request['discount'];
                                    }
                                    $saleItemTemp->total_discount = (float)$saleItemTemp->discount* (float)$saleItemTemp->quantity_sold;
                                    $saleItemTemp->disc_percent = ($saleItemTemp->total_discount/$saleItemTemp->price)*100; 
                                    $saleItemTemp->with_vat = $request['with_vat'];
                                    if ($saleItemTemp->with_vat == 'yes') {
                                        $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                                        $saleItemTemp->vat_amount = $vat_amount;
                                    }else{
                                        $saleItemTemp->vat_amount = 0;
                                    }
                                                   
                                    $saleItemTemp->save();
                                }else{
                                    if($saletemp->currency != $saletemp->defcurr){
                                        $saleItemTemp->total_discount = $request['total_discount']*$saletemp->ex_rate;
                                    }else{
                                        $saleItemTemp->total_discount = $request['total_discount'];
                                    }
                                    $saleItemTemp->discount = (float)$saleItemTemp->total_discount / (float)$saleItemTemp->quantity_sold;
                                    if ($saleItemTemp->price > 0) {
                                        $saleItemTemp->disc_percent = ((float)$saleItemTemp->total_discount/(float)$saleItemTemp->price)*100;
                                    }else{
                                        $saleItemTemp->disc_percent = 0;
                                    }
                                    $saleItemTemp->with_vat = $request['with_vat']; 
                                    if ($saleItemTemp->with_vat == 'yes') {
                                        $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                                        $saleItemTemp->vat_amount = $vat_amount;
                                    }else{
                                        $saleItemTemp->vat_amount = 0;
                                    }
                                    $saleItemTemp->save();
                                }

                                if ($settings->enable_sale_approval) {
                                    // Send Discount Request
                                    $discapproval = DiscountApproval::where('sale_temp_id', $saletemp->id)->where('status', '!=', 'Rejected')->first();
                                    if (is_null($discapproval)) {
                                            
                                        $discapproval = new DiscountApproval();
                                        $discapproval->shop_id = $shop->id;
                                        $discapproval->sale_temp_id = $saletemp->id;
                                        $discapproval->product_id = $saleItemTemp->product_id;
                                        $discapproval->user_id = $saletemp->user_id;
                                        $discapproval->disc_percent = $saleItemTemp->disc_percent;
                                        $discapproval->discount = $saleItemTemp->discount;
                                        $discapproval->save();

                                        $permissionName = 'approve-discount';
                                        $approvers = User::whereHas('permissions', function ($query) use ($permissionName) {
                                            $query->where('name', $permissionName);
                                        })->orWhereHas('roles.permissions', function ($query) use ($permissionName) {
                                            $query->where('name', $permissionName);
                                        })->get();
                                        // Log::info($approvers);
                                        Notification::sendNow($approvers, new DiscountApprovalNotification($discapproval));
                                    }
                                }
                                return response()->json(['status' => 'DISCOUNT UPDATED', 'msg' => 'Discount was updated successfully']);
                            }else{
                                $product = Product::find($saleItemTemp->product_id);
                                $shopproduct = $shop->products()->where('id', $product->id)->first();
                                $lateststock = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->latest()->first();

                                if (!is_null($lateststock) && $shopproduct->in_stock > $lateststock->quantity_in && $shopproduct->unit_cost != $lateststock->unit_cost) {

                                    $saleItemTemp->quantity_sold = $request['quantity_sold'];
                                    $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                                    $saleItemTemp->total_discount = $saleItemTemp->quantity_sold*$saleItemTemp->discount;
                                    $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                                    $saleItemTemp->with_vat = $request['with_vat'];
                                    if ($saleItemTemp->with_vat == 'yes') {
                                        $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                                        $saleItemTemp->vat_amount = $vat_amount;
                                    }else{
                                        $saleItemTemp->vat_amount = 0;
                                    }
                                                
                                    $saleItemTemp->save();
                                    if (!$settings->always_sell_old) {
                                        return response()->json(['status' => 'SHARED', 'msg' => 'This product ('.$product->name.') has stock with different purchase prices. Please select which stock you are currently selling..']);
                                    }
                                }else{
                                    if ($qty_sold > $saleItemTemp->curr_stock && !$settings->sale_with_low_stock) {
                                        return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Product '.$product->name.' is currently less than.'.$qty_sold]);
                                    }else{
                                        if ($product->basic_uom == 'pcs' || $product->basic_uom == 'prs' || $product->basic_uom == 'box' || $product->basic_uom == 'btl' || $product->basic_uom == 'pks' || $product->basic_uom == 'gls') {
                                            if (!$this->is_decimal($request['quantity_sold'])) {
                                                $saleItemTemp->quantity_sold = $request['quantity_sold'];
                                                $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                                                $saleItemTemp->total_discount = $saleItemTemp->quantity_sold*$saleItemTemp->discount;
                                                $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                                                    
                                                $saleItemTemp->with_vat = $request['with_vat'];
                                                if ($saleItemTemp->with_vat == 'yes') {
                                                    $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                                                    $saleItemTemp->vat_amount = $vat_amount;
                                                }else{
                                                    $saleItemTemp->vat_amount = 0;
                                                }
                                                    
                                                $saleItemTemp->save();
                                                return $saleItemTemp;
                                            } else{
                                                return response()->json(['status' => 'WRONG', 'msg' => 'This product '.$product->name.' can not accept decimal quantity. Please change its basic unit if you want to set decimal for stock quantity values']);
                                            }            
                                        }else{
                                            $saleItemTemp->quantity_sold = $request['quantity_sold'];
                                            $saleItemTemp->buying_price = (float)$saleItemTemp->unit_cost*(float)$saleItemTemp->quantity_sold;
                                            $saleItemTemp->total_discount = (float)$saleItemTemp->quantity_sold*(float)$saleItemTemp->discount;
                                            $saleItemTemp->price = (float)$saleItemTemp->retail_price*(float)$saleItemTemp->quantity_sold;
                                            $saleItemTemp->with_vat = $request['with_vat'];
                                            if ($saleItemTemp->with_vat == 'yes') {
                                                $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                                                $saleItemTemp->vat_amount = $vat_amount;
                                            }else{
                                                $saleItemTemp->vat_amount = 0;
                                            }
                                            $saleItemTemp->save();
                                            return $saleItemTemp;
                                        }   
                                    }
                                }
                            }   
                        }
                    }else{
                        $saleItemTemp->with_vat = $request['with_vat'];
                        if ($saleItemTemp->with_vat == 'yes') {
                            $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                            $saleItemTemp->vat_amount = $vat_amount;
                        }else{
                            $saleItemTemp->vat_amount = 0;
                        }
                        $saleItemTemp->save();
                        return response()->json(['status' => 'VAT UPDATED', 'msg' => '']);
                    }
                }else{
                    $myproduct = $shop->products()->where('id', $saleItemTemp->product_id)->first();
                    if ($request['sold_in'] == 'Retail Price') {
                        $saleItemTemp->retail_price = $myproduct->retail_price;
                        $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                        $saleItemTemp->sold_in = $request['sold_in'];
                        $saleItemTemp->save();
                        return $saleItemTemp;
                    }elseif($request['sold_in'] == 'Wholesale Price') {
                        if ($myproduct->wholesale_price != 0 && !is_null($myproduct->wholesale_price)) {
                            
                            $saleItemTemp->retail_price = $myproduct->wholesale_price;
                            $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                            $saleItemTemp->sold_in = $request['sold_in'];
                            $saleItemTemp->save();

                            return $saleItemTemp;
                        }
                    }elseif ($request['sold_in'] == 'FOB 5000 Price') {
                        if ($myproduct->retail_price_fob != 0 && !is_null($myproduct->retail_price_fob)) {
                            
                            $saleItemTemp->retail_price = $myproduct->retail_price_fob;
                            $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                            $saleItemTemp->sold_in = $request['sold_in'];
                            $saleItemTemp->save();

                            return $saleItemTemp;
                        }
                    }elseif ($request['sold_in'] == 'FOB 10000 Price') {
                        if ($myproduct->retail_price_fob_10 != 0 && !is_null($myproduct->retail_price_fob_10)) {
                            
                            $saleItemTemp->retail_price = $myproduct->retail_price_fob_10;
                            $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                            $saleItemTemp->sold_in = $request['sold_in'];
                            $saleItemTemp->save();

                            return $saleItemTemp;
                        }
                    }
                }
            }
        }
    }

    function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }

    
    public function updateSaleMode(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        Session::forget('sold_in');
        Session::put('sold_in', $request['sold_in']);
        $saleItemTemps =  SaleItemTemp::where('sale_temp_id', $request['sale_temp_id'])->get();
        if ($saleItemTemps->count() > 0) {
            foreach ($saleItemTemps as $key => $saleItemTemp) {
                $myproduct = $shop->products()->where('id', $saleItemTemp->product_id)->first();
                if ($request['sold_in'] == 'Retail Price') {
                    $saleItemTemp->retail_price = $myproduct->retail_price;
                    $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                    $saleItemTemp->sold_in = $request['sold_in'];
                    $saleItemTemp->save();
                }elseif($request['sold_in'] == 'Wholesale Price') {
                    if ($myproduct->wholesale_price != 0 && !is_null($myproduct->wholesale_price)) {
                        $saleItemTemp->retail_price = $myproduct->wholesale_price;
                        $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                        $saleItemTemp->sold_in = $request['sold_in'];
                        $saleItemTemp->save();
                    }
                }elseif ($request['sold_in'] == 'FOB 5000 Price') {
                    if ($myproduct->retail_price_fob != 0 && !is_null($myproduct->retail_price_fob)) {
                        $saleItemTemp->retail_price = $myproduct->retail_price_fob;
                        $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                        $saleItemTemp->sold_in = $request['sold_in'];
                        $saleItemTemp->save();
                    }
                }elseif ($request['sold_in'] == 'FOB 10000 Price') {
                    if ($myproduct->retail_price_fob_10 != 0 && !is_null($myproduct->retail_price_fob_10)) {
                        $saleItemTemp->retail_price = $myproduct->retail_price_fob_10;
                        $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                        $saleItemTemp->sold_in = $request['sold_in'];
                        $saleItemTemp->save();
                    }
                }
            }

            return response()->json(['status' => 'SALE MODE CHANGED', 'msg' => 'Sale mode was successfully updated']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SaleItemTemp  $saleItemTemp
     * @return \Illuminate\Http\Response
     */
    public function updateDiscount($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $saleItemTemp =  SaleItemTemp::find($id);
        
        if (!is_null($saleItemTemp)) {
         
            $saleItemTemp->quantity_sold = $request['quantity_sold'];
            $saleItemTemp->buying_price = $request['buying_price'];
            $saleItemTemp->discount = $request['discount'];
            $saleItemTemp->price = $request['price'];        
            $saleItemTemp->save();

            return $saleItemTemp;
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SaleItemTemp  $saleItemTemp
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SaleItemTemp::destroy($id);
    }

    public function selectedCustomer(Request $request)
    {
        // Log::info($request->cust_id);
        Session::forget('cust_id');

        $customer = Customer::find($request->cust_id);
        // Log::info($customer);
        Session::put('cust_id', $customer->id);
    }
}
