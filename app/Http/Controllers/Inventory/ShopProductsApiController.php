<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Session;
use Response;
use Auth;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\SaleItemTemp;
use App\Models\ProductUnit;

class ShopProductsApiController extends Controller
{

    function __construct()
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
        $shop = Shop::find(Session::get('shop_id'));
        $products = $shop->products()->where('is_active', true)->select('id', 'product_code', 'name', 'slug', 'in_stock')->take(1)->get();
        return $products;
    }

    public function autoSearch(Request $request)
    {
        if ($request->ajax()) {
            $shop = Shop::find(Session::get('shop_id'));
            if (!empty($request->search_key) && strlen($request->search_key) >= 2) {
                $data = $shop->products()->where('is_active', true)->where(\DB::raw('CONCAT_WS(" ", `name`, `slug`, `barcode`, `product_code`)'),'LIKE', '%'.$request->search_key.'%')->select('id', 'product_code', 'name', 'slug', 'basic_uom', 'image_url as img', 'in_stock', 'unit_cost', 'retail_price', 'wholesale_price')->get();

                return $data;
            }else{
                // $products = $shop->products()->select('product_id as id', 'product_code', 'name', 'in_stock')->take(15)->get();
                // return $products;
            }
        }
    }

    public function fetchProduct(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $product = $shop->products()->where('id', $request->product_id)->select('id', 'name', 'in_stock', 'unit_cost', 'retail_price')->first();

        return $product;
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
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
         
        $product = $shop->products()->where('barcode', $request->barcode)->select('id', 'name', 'basic_uom', 'in_stock', 'unit_cost', 'retail_price')->first();
        if (!is_null($product)) {
            $saleItemTemp = SaleItemTemp::where('product_id', $product->id)->where('sale_temp_id', $request['sale_temp_id'])->first();
            $sold_in = 'Retail Price';
            if (!empty($request['sold_in'])) {
                $sold_in = $request['sold_in'];
            }

            if (!is_null($saleItemTemp)) {
                if ($saleItemTemp->curr_stock > $saleItemTemp->quantity_sold+1) {
                    $saleItemTemp->quantity_sold = $saleItemTemp->quantity_sold+1;
                    $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                    $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                    $saleItemTemp->save();
                    return response()->json(['status' => 200, 'msg' => 'Item updated successfully']);                    
                }else{
                    return response()->json(['status' => 400, 'msg' => 'Selected Item si currently out of stock']);
                }
            }else{
                $bunit = ProductUnit::where('product_id', $product->id)->where('is_basic', true)->first();
                if (is_null($bunit)) {
                    $bunit = new ProductUnit();
                    $bunit->shop_id = $shop->id;
                    $bunit->product_id = $product->id;
                    $bunit->unit_name = $product->basic_uom;
                    $bunit->qty_equal_to_basic = 1;
                    $bunit->unit_price = $product->retail_price;
                    $bunit->is_basic = true;
                    $bunit->save();
                }
                $instock = 0;
                if (!is_null($product->in_stock)) {
                    $instock = $product->in_stock;
                }
                if ($instock <= 0 && !$settings->sale_with_low_stock) {
                    return response()->json(['status' => 400, 'msg' => 'Selected Item si currently out of stock']);
                }else{
                    $saleItemTemp = new SaleItemTemp;
                    $saleItemTemp->sale_temp_id = $request['sale_temp_id'];
                    $saleItemTemp->product_id = $product->id;
                    $saleItemTemp->product_unit_id = $bunit->id;
                    if ($instock < 1) {
                        $saleItemTemp->quantity_sold = 0;
                    }else {
                        $saleItemTemp->quantity_sold = 1;
                    }
                    $saleItemTemp->curr_stock = $instock;
                    $saleItemTemp->unit_cost = $product->unit_cost;
                    $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                    $saleItemTemp->retail_price = $product->retail_price;
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
                    return response()->json(['status' => 200, 'msg' => 'Item added successfully']);
                }
            }
        }else{
            return response()->json(['status' => 400, 'msg' => 'Item not Found']);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function useBarcode()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $setting = Setting::where('shop_id', $shop->id)->first();
        if ($setting->use_barcode) {
            $usebarcode = true;
        }else{
            $usebarcode = false;
        }

        return Response::json(['usebarcode' => $usebarcode]);
    }
}
