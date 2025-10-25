<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Models\OrderDetail;
use App\Models\Vehicle;
use App\Models\OrderDelivery;
use App\Models\OrderDeliveryItem;
use App\Models\OrderItem;

class OrderDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Order Delivery';
        $orders = OrderDetail::where('is_full_delivered', false)->join('users', 'users.id', '=', 'order_details.user_id')->select('order_details.id as id', 'uuid', 'first_name', 'last_name')->get();
        $vehicles = Vehicle::where('shop_id', Session::get('shop_id'))->get();

        return view('shop.orders.deliveries.create', compact('page', 'orders', 'vehicles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $order = OrderDetail::find($request['order_detail_id']);
        if (!is_null($order)) {
            $delivery = new OrderDelivery();
            $delivery->shop_id = Session::get('shop_id');
            $delivery->user_id = Auth::user()->id;
            $delivery->order_detail_id = $order->id;
            $delivery->delivery_address_id = $order->delivery_address_id;
            $delivery->vehicle_id = $request['vehicle_id'];
            $delivery->save();

            return redirect()->route('order-deliveries.edit', encrypt($delivery->id));
        }else{

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
        $page = 'New Order Delivery';
        $delivery = OrderDelivery::find(decrypt($id));
        $orders = OrderDetail::where('is_full_delivered', false)->join('users', 'users.id', '=', 'order_details.user_id')->select('order_details.id as id', 'uuid', 'first_name', 'last_name')->get();
        $vehicles = Vehicle::where('shop_id', Session::get('shop_id'))->get();
        $items = OrderItem::where('order_detail_id', $delivery->order_detail_id)->join('products', 'products.id', '=', 'order_items.product_id')->select('product_id', 'name')->get();
        $ditems = OrderDeliveryItem::where('order_delivery_id', $delivery->id)->join('products', 'products.id', '=', 'order_delivery_items.product_id')->select('order_delivery_items.id as id', 'name', 'quantity', 'uom')->get();

        return view('shop.orders.deliveries.edit', compact('page', 'delivery', 'orders', 'vehicles', 'items', 'ditems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = OrderDetail::find($request['order_detail_id']);
        if (!is_null($order)) {
            $delivery = OrderDelivery::find(decrypt($id));
            $delivery->order_detail_id = $order->id;
            $delivery->delivery_address_id = $order->delivery_address_id;
            $delivery->vehicle_id = $request['vehicle_id'];
            $delivery->remarks = $request['remarks'];
            $delivery->save();

            return redirect()->route('orders.show', encrypt($order->id))->with('success', 'Order Delivery updated successfully');
        }else{

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delivery = OrderDelivery::find(decrypt($id));
        if (!is_null($delivery)) {
            $ditems = OrderDeliveryItem::where('order_delivery_id', $delivery->id)->get();
            foreach ($ditems as $key => $value) {
                $value->delete();
            }

            $order = OrderDetail::find($delivery->order_detail_id);
            $order->is_full_delivered = false;
            $order->save();

            $delivery->delete();

            return redirect('order-deliveries')->with('success', 'Order Delivery cancelled successfully');
        }else{
            return redirect('order-deliveries')->with('error', 'Order Delivery not Found');
        }
    }

    public function addDeliveryItem(Request $request)
    {
        $delivery = OrderDelivery::find($request['order_delivery_id']);
        if (!is_null($delivery)) {
            $orderitem = OrderItem::where('order_detail_id', $delivery->order_detail_id)->where('product_id', $request['product_id'])->first();
            if (!is_null($orderitem)) {
                $ditem = OrderDeliveryItem::where('order_delivery_id', $delivery->id)->where('product_id', $request['product_id'])->first();
                if (is_null($ditem)) {
                    $ditem = new OrderDeliveryItem();
                    $ditem->order_delivery_id = $delivery->id;
                    $ditem->product_id = $orderitem->product_id;
                    $ditem->quantity = 1;
                    $ditem->uom = $orderitem->uom;
                    $ditem->save();    

                    return response()->json(['success' => 1, 'msg' => 'Item Added successfully']);
                }else{
                    return response()->json(['success' => 0, 'msg' => 'Item already selected']);
                }
            }else{
                return response()->json(['success' => 0, 'msg' => 'Order Item not Found']);
            }
        }else{
            return response()->json(['success' => 0, 'msg' => 'Order not Found']);
        }
    }

    public function updateDeliveryItem(Request $request)
    {
        $ditem = OrderDeliveryItem::find($request['id']);
        if (!is_null($ditem)) {
            $delivery = OrderDelivery::find($ditem->order_delivery_id);
            $orderitem = OrderItem::where('order_detail_id', $delivery->order_detail_id)->first();
            $deliveredqty = OrderDeliveryItem::where('product_id', $ditem->product_id)->join('order_deliveries', 'order_deliveries.id', '=', 'order_delivery_items.order_delivery_id')->where('order_detail_id', $delivery->order_detail_id)->where('order_deliveries.id', '!=', $delivery->id)->sum('quantity');
            $remqty = $orderitem->quantity-$deliveredqty;
            if ($remqty >= $request['quantity']) {
                $ditem->quantity = $request['quantity'];
                $ditem->save();

                return response()->json(['success' => 1, 'msg' => 'Item updated successfully']);
            }else{
                return response()->json(['success' => 0, 'msg' => 'The remaing quatity for deliver of this Order Item is less than the quantity you provide']);
            }
        }
    }
}
