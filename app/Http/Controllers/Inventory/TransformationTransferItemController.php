<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Log;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\AnSaleItem;
use App\Models\Product;
use App\Models\ProdDamage;
use App\Models\SaleReturnItem;
use App\Models\TransferOrder;
use App\Models\TransferOrderItem;
use App\Models\TransformationTransferItem;
use App\Models\TransformationTransferItemTemp;
use App\Models\TransferOrderItemTemp;
use App\Models\Stock;
use App\Jobs\StockUpdaterJob;

class TransformationTransferItemController extends Controller
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
        $page = 'Products';
        $title = 'Transformation Stock Transfer';
        $title_sw = 'Uhamishaji wa Stock Kwa kubadilisha Bidhaa';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $products = $shop->products()->get();

        $owner = $shop->users()->where('user_id', $user->id)->first();
        $destinations = $owner->shops()->where('shop_id', '!=', $shop->id)->get();
        $max_no = TransferOrder::where('shop_id', $shop->id)->orderBy('order_no', 'desc')->first();
        $order_no = 1;
        
        if (!is_null($max_no)) {
            $order_no = $max_no->order_no+1;
        }else{
            $order_no = 1;
        }

        return view('products.transfers.transformation.create' , compact('page', 'title', 'title_sw', 'shop', 'destinations', 'order_no' , 'products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $now = Carbon::now();
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $destinshop = $user->shops()->where('shop_id', $request['destin_id'])->first();

        $max_no = TransferOrder::where('shop_id', $shop->id)->orderBy('order_no', 'desc')->first();
        $order_no = 0;
        if (!is_null($max_no)) {
            $order_no = $max_no->order_no+1;
        }else{
            $order_no = 1;
        }

        $orderdate = $now;

        if (!empty($request['order_date'])) {
            $orderdate = $request['order_date'];
        }

        $product = $shop->products()->where('product_id' , $request['prod_transformed_id'])->first();
        $destinproduct = $destinshop->products()->where('product_id', $request['prod_transformed_id'])->first();

        $itemtemps = TransferOrderItemTemp::where('user_id', $user->id)->where('shop_id', $shop->id)->get();
        if (!is_null($itemtemps)) {
            $temps = array();
            foreach ($itemtemps as $key => $value) {
                if ($value->quantity == 0) {
                    array_push($temps, $value->quantity);
                }
            }

            if (!empty($temps)) {
                 Log::info('no quantity');
                return redirect()->back()->with('warning', 'Please update the quantity of each item to continue');
            }else{

                if($product->in_stock < $request['prod_transformed_quantity']){
                     Log::info('stock is lesss');
                    return redirect()->back()->with('warning', ' Stock of '.$product->name.' in your Source Shop/Store is currently less than '.$request['prod_transformed_quantity']);
                }else{
                    
                    $transorder = TransferOrder::create([
                        'order_no' => $order_no,
                        'order_date' => $orderdate,
                        'reason' => $request['reason'],
                        'user_id' => $user->id,
                        'shop_id' => $shop->id,
                        'destination_id' => $destinshop->id,
                        'source_product_id' => $request['prod_transformed_id'],
                        'source_product_quantity' => $request['prod_transformed_quantity'],
                        'is_transfomation_transfer' => true ,
                    ]);

                    $orderItem = new TransferOrderItem;
                    $orderItem->shop_id = $shop->id;
                    $orderItem->transfer_order_id = $transorder->id;
                    $orderItem->product_id = $request['prod_transformed_id'];
                    $orderItem->quantity = $request['prod_transformed_quantity'];
                    $orderItem->source_stock = $product->in_stock;
                    $orderItem->destin_stock = is_null($destinproduct) ? 0 : $destinproduct->pivot->in_stock ;
                    $orderItem->source_unit_cost = $product->unit_cost;
                    $orderItem->destin_unit_cost = is_null($destinproduct) ? 0 : $destinproduct->pivot->unit_cost;
                    $orderItem->save();

                    dispatch(new StockUpdaterJob($shop, $orderItem->product_id));

                    foreach ($itemtemps as $key => $item) {

                        $dstock = Stock::create([
                            'product_id' => $item->product_id,
                            'shop_id' => $destinshop->id,
                            'quantity_in' => $item->quantity,
                            'unit_cost' => $item->destin_unit_cost,
                            'source' => 'Transfered by Transformation (From: '.$shop->name.')',
                            'time_created' => $now,
                            'order_id' => $transorder->id
                        ]);

                        dispatch(new StockUpdaterJob($destinshop, $item->product_id));
                    }
                }
            }

            foreach ($itemtemps as $key => $value) {
                $value->delete();
            }

            $success = 'Transfer Order was created successfully';
            return redirect('transfer-orders')->with('success', $success);
        }else{
                Log::info('no products');
            return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
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
        $page = 'Products';
        $title = 'Edit Transformation Transfer Order';
        $title_sw = 'Hariri Oda ya kuhamisha Stock';

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $destinations = $user->shops()->where('shop_id', '!=', $shop->id)->get();
        $transorder = TransferOrder::find(decrypt($id));
        $destinshop = Shop::find($transorder->destination_id);
        $product = Product::find($transorder->source_product_id);
        $orderitems = TransformationTransferItem::where('transfer_order_id', $transorder->id)->with('product')->get();
        return view('products.transfers.edit', compact('page', 'title', 'title_sw', 'shop', 'destinations', 'destinshop', 'transorder', 'orderitems' , 'product'));
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
       
    }
}
