<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\TransferOrder;
use App\Models\TransferOrderItem;
use App\Models\AnSaleItem;
use App\Models\Stock;
use App\Jobs\StockUpdaterJob;

class TransferOrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $transorder = TransferOrder::find($request['transfer_order_id']);
        if (!is_null($transorder)) {
            $now = Carbon::now();
            $ordertime = $transorder->order_date.' '.$now->format('H:i:s');
            $shop = Shop::find($transorder->shop_id);
            $destinshop = Shop::find($transorder->destination_id);

            $product = $shop->products()->where('id', $request['product_id'])->first();
            if (!is_null($product)) {
                $destinproduct = $destinshop->products()->where('is_active', true)->where('product_id', $request['product_id'])->first();
                if (is_null($destinproduct)) {
                    $newproduct = Product::find($request['product_id']);

                    $newdestinproduct = $destinshop->products()->attach($newproduct, ['in_stock' => 0, 'location' => null, 'product_code' => $product->product_code, 'barcode' => $product->barcode, 'unit_cost' => $product->unit_cost, 'retail_price' => $product->retail_price, 'time_created' => \Carbon\Carbon::now()]);


                    $prod_unit = new ProductUnit();
                    $prod_unit->shop_id = $destinshop->id;
                    $prod_unit->product_id = $product->id;
                    $prod_unit->unit_name = $product->basic_uom;
                    $prod_unit->is_basic = true;
                    $prod_unit->qty_equal_to_basic = 1;
                    $prod_unit->unit_price = $product->retail_price;
                    $prod_unit->save();
                    
                    $destinproduct = $destinshop->products()->where('product_id', $request['product_id'])->first();
                }

                $orderItem = new TransferOrderItem;
                $orderItem->shop_id = $shop->id;
                $orderItem->transfer_order_id = $transorder->id;
                $orderItem->product_id = $product->id;
                if ($transorder->is_request) {
                    $orderItem->req_qty = $request['quantity'];
                }else{
                    $orderItem->quantity = $request['quantity'];
                }
                $orderItem->source_stock = $product->in_stock;
                if (!is_null($destinproduct->pivot->in_stock)) {
                    $orderItem->destin_stock = $destinproduct->pivot->in_stock;
                }else{
                    $orderItem->destin_stock = 0;
                }
                $orderItem->source_unit_cost = $product->unit_cost;
                $orderItem->destin_unit_cost = $product->unit_cost;
                $orderItem->source_unit_price = $product->retail_price;
                $orderItem->save();
                $orderItem->created_at = $ordertime;
                $orderItem->save();

                if (!$transorder->is_request) {
                    dispatch(new StockUpdaterJob($shop, $orderItem->product_id));

                    $dstock = Stock::create([
                        'product_id' => $orderItem->product_id,
                        'shop_id' => $destinshop->id,
                        'quantity_in' => $orderItem->quantity,
                        'unit_cost' => $orderItem->destin_unit_cost,
                        'source' => 'Transfered (From: '.$shop->name.')',
                        'time_created' => $ordertime,
                        'order_id' => $transorder->id
                    ]);

                    dispatch(new StockUpdaterJob($destinshop, $orderItem->product_id));

                    // $this->updateSaleItems($product, $destinshop);
                }
                return redirect()->back()->with('success', 'Order Item added successfully');
            }else{
                return redirect()->back()->with('error', 'Product not Found in Either source or destination shop/Store');
            }
        }else{
            return redirect()->back()->with('error', 'Transfer Order was not Found');
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
    public function update(Request $request)
    {
        $orderItem = TransferOrderItem::find($request['id']);

        if (!is_null($orderItem)) {
            //Update Item
            $orderItem->req_qty = $request['quantity'];
            $orderItem->save();
            return response()->json(['success' => 1, 'msg' => 'Updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item not Found']);
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
        $orderItem = TransferOrderItem::find(decrypt($id));

        if (!is_null($orderItem)) {
            $orderItem->delete();
            return redirect()->back()->with('success', 'Item removed successfully');
        }else{
            return redirect()->back()->with('info', 'Item not Found');
        }
    }
    
    public function updateSaleItems($product, $shop)
    {
        // Check if there are sales done with low stock
        $psitems = AnSaleItem::where('product_id', $product->id)->where('shop_id', $shop->id)->whereNull('stock_id')->where('is_deleted', false)->get();
        foreach ($psitems as $key => $value) {
            $astocks = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->get();
            $qtysold = $value->quantity_sold;
            foreach ($astocks as $key => $stock) {
                $remqty = ($stock->quantity_in-$stock->quantity_out);
                if ($qtysold > 0) {
                    if ($qtysold <= $remqty) {
                        $saleitemData = new AnSaleItem;
                        $saleitemData->shop_id = $shop->id;
                        $saleitemData->an_sale_id = $value->an_sale_id;
                        $saleitemData->product_id = $value->product_id;
                        $saleitemData->stock_id = $stock->id;
                        $saleitemData->product_unit_id = $value->product_unit_id;
                        $saleitemData->quantity_sold = $qtysold;
                        $saleitemData->unit_cost = $stock->unit_cost;
                        $saleitemData->buying_price = $saleitemData->quantity_sold*$saleitemData->unit_cost;
                        $saleitemData->retail_price = $value->retail_price;
                        $saleitemData->price = $saleitemData->retail_price*$saleitemData->quantity_sold;
                        $saleitemData->disc_percent = $value->disc_percent;
                        $saleitemData->discount = $value->discount;
                        $saleitemData->total_discount = $saleitemData->discount*$saleitemData->quantity_sold;
                        $saleitemData->time_created = $value->time_created;
                        if ($value->vat_amount > 0) {
                            $saleitemData->tax_amount = $value->vat_amount;
                            $saleitemData->input_tax = $saleitemData->buying_price*(($settings->tax_rate/100)/(1+($settings->tax_rate/100)));
                        }
                        $saleitemData->sold_in = $value->sold_in;
                        $saleitemData->save();

                        $stock->quantity_out = $stock->quantity_out+$qtysold;
                        if ($stock->quantity_in == $stock->quantity_out) {
                            $stock->is_utilized = true;
                        }
                        $stock->save();
                    }else{
                        $saleitemData = new AnSaleItem;
                        $saleitemData->shop_id = $shop->id;
                        $saleitemData->an_sale_id = $value->an_sale_id;
                        $saleitemData->product_id = $value->product_id;
                        $saleitemData->stock_id = $stock->id;
                        $saleitemData->product_unit_id = $value->product_unit_id;
                        $saleitemData->quantity_sold = $remqty;
                        $saleitemData->unit_cost = $stock->unit_cost;
                        $saleitemData->buying_price = $saleitemData->quantity_sold*$saleitemData->unit_cost;
                        $saleitemData->retail_price = $value->retail_price;
                        $saleitemData->price = $saleitemData->retail_price*$saleitemData->quantity_sold;
                        $saleitemData->disc_percent = $value->disc_percent;
                        $saleitemData->discount = $value->discount;
                        $saleitemData->total_discount = $saleitemData->discount*$saleitemData->quantity_sold;
                        $saleitemData->time_created = $value->time_created;
                        if ($value->vat_amount > 0) {
                            $saleitemData->tax_amount = $value->vat_amount;
                            $saleitemData->input_tax = $saleitemData->buying_price*(($settings->tax_rate/100)/(1+($settings->tax_rate/100)));
                        }
                        $saleitemData->sold_in = $value->sold_in;
                        $saleitemData->save();

                        $stock->quantity_out = $stock->quantity_out+$remqty;
                        if ($stock->quantity_in == $stock->quantity_out) {
                            $stock->is_utilized = true;
                        }
                        $stock->save();
                    }
                }
                $qtysold -= $remqty;
            }
        }
    }
}
