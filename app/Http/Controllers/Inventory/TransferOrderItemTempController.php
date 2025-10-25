<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Response;
use Session;
use Auth;
use Log;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\User;
use App\Models\TransferOrderTemp;
use App\Models\TransferOrderItemTemp;
use App\Models\Product;
use App\Models\ProductUnit;

class TransferOrderItemTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $ordertemp = TransferOrderTemp::find($id);
        if (!is_null($ordertemp)) {
            $shop = Shop::find(Session::get('shop_id'));
            $user = Auth::user();
            $types = [['id' => 0, 'type' => 'Select Transfer Type'], ['id' => 1, 'type' => 'Stock Request'], ['id' => 2, 'type' => 'Stock Return']];

            $destinations = null;
            if ($ordertemp->transfer_type > 0) {
                $destinations = $user->shops()->where('shop_id', '!=', $shop->id)->where('is_warehouse', true)->select('id', 'name')->get();
            }else{
                $destinations = $user->shops()->where('shop_id', '!=', $shop->id)->where('is_warehouse', false)->select('id', 'name')->get();
            }

            $temps = TransferOrderItemTemp::where('transfer_order_temp_id', $ordertemp->id)->join('products', 'products.id', '=', 'transfer_order_item_temps.product_id')->select('transfer_order_item_temps.id as id', 'product_code', 'name', 'slug',  'quantity', 'source_stock', 'destin_stock', 'source_unit_cost', 'destin_unit_cost')->orderBy('id', 'desc')->get();

            return response()->json(['ordertemp' => $ordertemp, 'temps' => $temps, 'types' => $types, 'destinations' => $destinations]);
        }else{

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
        // Log::info($request);
        $ordertemp = TransferOrderTemp::find($request['temp_id']);
        if (!is_null($ordertemp)) {
            $shop = Shop::find(Session::get('shop_id'));
            $destin = Shop::find($ordertemp->destination_id);

            if ($ordertemp->transfer_type == 1) {
                $destin = Shop::find(Session::get('shop_id'));
                $shop = Shop::find($ordertemp->destination_id);
            }

            // Log::info('source is '.$shop->name.' Destination is '.$destin->name);
            $user = Auth::user();

            $sameitems = TransferOrderItemTemp::where('transfer_order_temp_id', $ordertemp->id)->where('product_id', $request['product_id'])->where('user_id', $user->id)->where('shop_id', $shop->id)->count();
        
            if ($sameitems == 0) {
                $now = \Carbon\Carbon::now();
                $product = $shop->products()->where('is_active', true)->where('id', $request['product_id'])->first();
                if (!is_null($product)) {    
                    $destinproduct = $destin->products()->where('is_active', true)->where('slug', $product->slug)->first();
                    if (is_null($destinproduct)) {

                        $destinproduct = new Product();
                        $destinproduct->shop_id = $destin->id;
                        $destinproduct->name = $product->name;
                        $destinproduct->basic_uom = $product->basic_uom;
                        $destinproduct->slug = $product->slug;
                        $destinproduct->location = $product->location;
                        $destinproduct->product_code = $product->product_code;
                        $destinproduct->unit_cost = $product->unit_cost;
                        $destinproduct->retail_price = $product->retail_price;
                        $destinproduct->wholesale_price = $product->wholesale_price;
                        $destinproduct->barcode = $product->barcode;
                        $destinproduct->description = $product->description;
                        $destinproduct->time_created = $now;
                        $destinproduct->brand = $product->brand;
                        $destinproduct->model = $product->model;
                        $destinproduct->type = $product->type;
                        $destinproduct->size = $product->size;
                        $destinproduct->color = $product->color;
                        $destinproduct->length = $product->length;
                        $destinproduct->width = $product->width;
                        $destinproduct->thick = $product->thick;
                        $destinproduct->height = $product->height;
                        $destinproduct->volume = $product->volume;
                        $destinproduct->weight = $product->weight;
                        $destinproduct->save();

                        $prod_unit = new ProductUnit();
                        $prod_unit->product_id = $destinproduct->id;
                        $prod_unit->unit_name = $destinproduct->basic_uom;
                        $prod_unit->is_basic = true;
                        $prod_unit->qty_equal_to_basic = 1;
                        $prod_unit->unit_price = $product->retail_price;
                        $prod_unit->save();
                    }

                    $orderItemTemp = new TransferOrderItemTemp;
                    $orderItemTemp->transfer_order_temp_id = $ordertemp->id;
                    if ($ordertemp->transfer_type == 1) {
                        $orderItemTemp->shop_id = $destin->id;
                    }else{
                        $orderItemTemp->shop_id = $shop->id;
                    }
                    $orderItemTemp->user_id = $user->id;
                    $orderItemTemp->product_id = $product->id;
                    $orderItemTemp->quantity = 1;
                    if (!is_null($product->in_stock)) {
                        $orderItemTemp->source_stock = $product->in_stock;
                    }else{
                        $orderItemTemp->source_stock = 0;
                    }
                    if (!is_null($destinproduct->in_stock)) {
                        $orderItemTemp->destin_stock = $destinproduct->in_stock;
                    }else{
                        $orderItemTemp->destin_stock = 0;
                    }
                    $orderItemTemp->source_unit_cost = $product->unit_cost;
                    if (!is_null($destinproduct->unit_cost) && $destinproduct->unit_cost > 0) {
                        $orderItemTemp->destin_unit_cost = $destinproduct->unit_cost;
                    }else{
                        $orderItemTemp->destin_unit_cost = $product->unit_cost;
                    }
                    $orderItemTemp->save();
                    // Log::info('Item createdd');
                    return $orderItemTemp;
                }else{
                    return response()->json(['status' => 'NOT','msg' => 'Product does not exists in '.$shop->name]);
                }
            }else{
                Log::info('Item already selected');
                return response()->json(['status' => 'DUPL','msg' => 'Item already selected']);
            }
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
        $settings = Setting::where('shop_id', $shop->id)->first();
        $orderItemTemp =  TransferOrderItemTemp::find($id);
        if (!is_null($orderItemTemp)) {
            $product = Product::find($orderItemTemp->product_id);
            if ($settings->sale_with_low_stock) {
                $orderItemTemp->quantity = $request['quantity'];
                $orderItemTemp->save();
                return $orderItemTemp;
            }else{
                if ($orderItemTemp->source_stock < $request['quantity']) {
                    return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Product '.$product->name.' is currently less than.'.($request['quantity'])]);
                }else{
                    $orderItemTemp->quantity = $request['quantity'];
                    $orderItemTemp->destin_unit_cost = $request['destin_unit_cost'];
                    $orderItemTemp->save();
                    return $orderItemTemp;
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
        TransferOrderItemTemp::destroy($id);

        return response::json(['status' =>  'Deleted']);
    }

    public function updateOrderTemp(Request $request)
    {
        $ordertemp = TransferOrderTemp::find($request['temp_id']);
        if (!is_null($ordertemp)) {
                
            $ordertemp->destination_id = $request['destin_id'];
            $ordertemp->transfer_type = $request['transfer_type'];
            $ordertemp->order_date = $request['order_date'];
            $ordertemp->reason = $request['reason'];
            $ordertemp->save();

            return $ordertemp;
        }
    }
}
