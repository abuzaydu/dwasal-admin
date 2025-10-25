<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Response;
use Auth;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Models\SaleOrder;
use App\Models\Customer;
use App\Models\SaleOrderItem;
use App\Models\ProductUnit;
use App\Models\Product;
use Log;

class SaleOrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $saleorder = SaleOrder::find($id);
        $customers = Customer::where('shop_id', $shop->id)->get();
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $orderitems = SaleOrderItem::where('sale_order_id', $id)->with('product')->orderBy('id', 'desc')->get();
        // Log::info($itemtemps);
        $temps = array();
        foreach ($orderitems as $temp) {
            $itemTemp = [
                'id' =>  $temp->id,
                'sale_order_id' =>  $temp->sale_order_id,
                'product_id' => $temp->product_id,
                'product_unit_id' => $temp->product_unit_id,
                'name' => $temp->product->name,
                'curr_stock' => $temp->curr_stock,
                'quantity' => $temp->quantity,
                'quantity_packed' => $temp->quantity_packed,
                'sold_in' => $temp->sold_in,
                'used_stock' => $temp->used_stock,
                'retail_price' => $temp->retail_price,
                'discount' => $temp->discount,
                'price' => $temp->price,
                'disc_percent' => $temp->disc_percent,
                'total_discount' => $temp->total_discount,
                'with_vat' => $temp->with_vat,
                'vat_amount' => $temp->vat_amount,
                'created_at' => $temp->created_at,
                'updated_at' => $temp->updated_at
            ];
            $itemTemp['units'] = ProductUnit::where('product_id', $temp->product_id)->get()->toArray();
            array_push($temps, array_merge($itemTemp, $itemTemp['units']));
        }
        return Response::json(['saleorder' => $saleorder, 'items' => $temps, 'customers' => $customers, 'currencies' => $currencies]);
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $sameitems = SaleOrderItem::where('product_id', $request['product_id'])->where('sale_order_id', $request['sale_order_id'])->count();
        
        if ($sameitems == 0) {
            $product = $shop->products()->where('id', $request['product_id'])->first();
            if (!is_null($product)) {
                $bunit = ProductUnit::where('product_id', $product->id)->where('is_basic', true)->first();
                $settings = Setting::where('shop_id', $shop->id)->first();
                if (!$settings->allow_sp_less_bp && $product->retail_price <= $product->unit_cost) {
                    $warning = 'Ooops!. The product has wrong prices. Please update the prices for accurate calculations';
                    return response()->json(['status' => 'WP', 'msg' => $warning]);
                }else {
                    if (is_null($product->in_stock) || $product->in_stock < $request['quantity']) {
                        return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Product '.$product->name.' is currently less than.'.($request['quantity'])]);
                    } else {
                        $orderitem = new SaleOrderItem();
                        $orderitem->sale_order_id = $request['sale_order_id'];
                        $orderitem->product_id = $request['product_id'];
                        $orderitem->product_unit_id = $bunit->id;
                        $orderitem->quantity = 0;
                        $orderitem->quantity_packed = 0;
                        $orderitem->curr_stock = $product->in_stock;
                        $orderitem->retail_price = $request['retail_price'];
                        $orderitem->price = $orderitem->retail_price*$orderitem->quantity;
                        $orderitem->discount = 0;
                        $orderitem->disc_percent = 0;
                        $orderitem->total_discount = 0;
                        $orderitem->sold_in = 'Retail Price';
                        if($settings->is_vat_registered && $settings->set_vat_by_default){
                            $orderitem->with_vat = 'yes';
                        }
                        $orderitem->save();
                        return $orderitem;
                    }
                }
            }
        }else{
            $warning = 'Ooops!. The product already in selected items.';
            return response()->json(['status' =>'DUPL', 'msg' => $warning]);
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
        $shop = Shop::find(Session::get('shop_id'));
        $orderitem =  SaleOrderItem::find($id);
        $settings = Setting::where('shop_id', $shop->id)->first();
        if (!is_null($orderitem) ) {
            $punit = ProductUnit::find($request['product_unit_id']);
            if ($orderitem->product_unit_id != $punit->id) {
                $orderitem->product_unit_id = $punit->id;
                $orderitem->retail_price = $punit->unit_price;
                $orderitem->price = $orderitem->retail_price*$orderitem->quantity;
                if ($orderitem->with_vat == 'yes') {
                    $vat_amount =  ($orderitem->price-$orderitem->total_discount)*($settings->tax_rate/100);
                    $orderitem->vat_amount = $vat_amount;
                }else{
                    $orderitem->vat_amount = 0;
                }
                $orderitem->save();
                return response()->json(['status' => 'UNIT UPDATED', 'msg' => 'VAT Updated Successful']);
            }else{
                if ($request['quantity_packed'] != $orderitem->quantity_packed) {
                    $orderitem->quantity_packed = $request['quantity_packed'];
                    $orderitem->save();
                    return $orderitem;
                }

                if ($request['sold_in'] == $orderitem->sold_in) {
                    if ($request['with_vat'] == $orderitem->with_vat) {
                        if (!is_null($orderitem)) {
                            $qty_sold = $request['quantity'];
                            if ($qty_sold == $orderitem->quantity && $qty_sold != 0) {
                                if($request['discount'] != $orderitem->discount) {
                                    $orderitem->discount = $request['discount'];
                                    $orderitem->total_discount = $orderitem->discount*$orderitem->quantity;     
                                    $orderitem->with_vat = $request['with_vat'];
                                    if ($orderitem->with_vat == 'yes') {
                                        $vat_amount =  ($orderitem->price-$orderitem->total_discount)*($settings->tax_rate/100);
                                        $orderitem->vat_amount = $vat_amount;
                                    }else{
                                        $orderitem->vat_amount = 0;
                                    }
                                                   
                                    $orderitem->save();
                                    return response()->json(['status' => 'DISCOUNT UPDATED', 'msg' => 'Discount was updated successfully']);
                                }elseif ($orderitem->total_discount != $request['total_discount']) {
                                    $orderitem->total_discount = $request['total_discount'];
                                    $orderitem->discount = $orderitem->total_discount/$orderitem->quantity;    
                                    $orderitem->with_vat = $request['with_vat']; 
                                    if ($orderitem->with_vat == 'yes') {
                                        $vat_amount =  ($orderitem->price-$orderitem->total_discount)*($settings->tax_rate/100);
                                        $orderitem->vat_amount = $vat_amount;
                                    }else{
                                        $orderitem->vat_amount = 0;
                                    }
                                    $orderitem->save();
                                    return response()->json(['status' => 'DISCOUNT UPDATED', 'msg' => 'Discount was updated successfully']);
                                }else{
                                    $orderitem->disc_percent = $request['disc_percent'];
                                    $orderitem->total_discount = $orderitem->price*($orderitem->disc_percent/100);
                                    $orderitem->discount = $orderitem->total_discount/$orderitem->quantity;    
                                    $orderitem->with_vat = $request['with_vat']; 
                                    if ($orderitem->with_vat == 'yes') {
                                        $vat_amount =  ($orderitem->price-$orderitem->total_discount)*($settings->tax_rate/100);
                                        $orderitem->vat_amount = $vat_amount;
                                    }else{
                                        $orderitem->vat_amount = 0;
                                    }
                                    $orderitem->save();
                                    return response()->json(['status' => 'DISCOUNT UPDATED', 'msg' => 'Discount was updated successfully']);
                                }       
                            }else{
                                $product = Product::find($orderitem->product_id);
                                $shopproduct = $shop->products()->where('id', $product->id)->first();
                                if ($qty_sold > $orderitem->curr_stock) {
                                    return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Product '.$product->name.' is currently less than.'.$qty_sold]);
                                }else{
                                    if ($product->basic_uom == 'pcs' || $product->basic_uom == 'prs' || $product->basic_uom == 'box' || $product->basic_uom == 'btl' || $product->basic_uom == 'pks' || $product->basic_uom == 'gls') {
                                        if (!$this->is_decimal($request['quantity'])) {
                                            $orderitem->quantity = $request['quantity'];
                                            $orderitem->total_discount = $orderitem->quantity*$orderitem->discount;
                                            $orderitem->price = $orderitem->retail_price*$orderitem->quantity;
                                            $orderitem->with_vat = $request['with_vat'];
                                            if ($orderitem->with_vat == 'yes') {
                                                $vat_amount =  ($orderitem->price-$orderitem->total_discount)*($settings->tax_rate/100);
                                                $orderitem->vat_amount = $vat_amount;
                                            }else{
                                                $orderitem->vat_amount = 0;
                                            }
                                                    
                                            $orderitem->save();
                                            return $orderitem;
                                        } else{
                                            return response()->json(['status' => 'WRONG', 'msg' => 'This product '.$product->name.' can not accept decimal quantity. Please change its basic unit if you want to set decimal for stock quantity values']);
                                        }            
                                    }else{
                                        $orderitem->quantity = $request['quantity'];
                                        $orderitem->total_discount = $orderitem->quantity*$orderitem->discount;
                                        $orderitem->price = $orderitem->retail_price*$orderitem->quantity;
                                        $orderitem->with_vat = $request['with_vat'];
                                        if ($orderitem->with_vat == 'yes') {
                                            $vat_amount =  ($orderitem->price-$orderitem->total_discount)*($settings->tax_rate/100);
                                            $orderitem->vat_amount = $vat_amount;
                                        }else{
                                            $orderitem->vat_amount = 0;
                                        }
                                        $orderitem->save();
                                        return $orderitem;
                                    }
                                }
                            }   
                        }
                    }else{
                        $orderitem->with_vat = $request['with_vat'];
                        if ($orderitem->with_vat == 'yes') {
                            $vat_amount =  ($orderitem->price-$orderitem->total_discount)*($settings->tax_rate/100);
                            $orderitem->vat_amount = $vat_amount;
                        }else{
                            $orderitem->vat_amount = 0;
                        }
                        $orderitem->save();

                        return response()->json(['status' => 'VAT UPDATED', 'msg' => 'VAT Updated Successful']);
                    }

                }else{
                    $myproduct = $shop->products()->where('id', $orderitem->product_id)->first();
                    if ($request['sold_in'] == 'Retail Price') {
                        $orderitem->retail_price = $myproduct->pivot->retail_price;
                        $orderitem->price = $orderitem->retail_price*$orderitem->quantity;
                        $orderitem->sold_in = $request['sold_in'];
                        $orderitem->save();
                        return $orderitem;
                    }else{
                        if ($myproduct->pivot->wholesale_price != 0 && !is_null($myproduct->pivot->wholesale_price)) {
                            $orderitem->retail_price = $myproduct->pivot->wholesale_price;
                            $orderitem->price = $orderitem->retail_price*$orderitem->quantity;
                            $orderitem->sold_in = $request['sold_in'];
                            $orderitem->save();

                            return $orderitem;
                        }
                    }
                }
            }
        }
    }

    public function updateDiscount($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $orderitem =  SaleOrderItem::find($id);
        
        if (!is_null($orderitem)) {
         
            $orderitem->quantity = $request['quantity'];
            $orderitem->discount = $request['discount'];
            $orderitem->price = $request['price'];        
            $orderitem->save();

            return $orderitem;
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
