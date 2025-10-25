<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use \Carbon\Carbon;
use App\Models\OrderDetail;
use App\Models\PaymentDetail;
use App\Models\OrderItem;
use App\Models\DeliveryAddress;
use App\Models\Address;
use App\Models\OrderStatus;
use App\Models\OrderDelivery;
use App\Models\OrderDeliveryItem;
use App\Models\Vehicle;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Orders';

        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
        $is_post_query = false;
        
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = '';
        $orders = OrderDetail::whereBetween('order_details.created_at', [$start, $end])->join('users', 'users.id', '=', 'order_details.user_id')->orderBy('order_details.created_at', 'desc')->select('order_details.id as id', 'uuid', 'total', 'status', 'first_name', 'last_name', 'phone', 'email', 'order_details.created_at as created_at', 'order_details.updated_at as updated_at')->get();

        return view('shop.orders.index', compact('page', 'orders', 'is_post_query', 'start_date', 'end_date', 'duration'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Order Details';

        $statuses = ['Awaiting Payment', 'Awaiting Fulfillment', 'Awaiting Shipment', 'Awaiting Pickup', 'Partially Shipped', 'Shipped', 'Completed', 'Cancelled', 'Declined', 'Manual Verification Required', 'Partially Refunded'];
        $order = OrderDetail::find(decrypt($id));
        if (!is_null($order)) {
            $orderitems = OrderItem::where('order_detail_id', $order->id)->join('products', 'products.id', '=', 'order_items.product_id')->get();
            $address = DeliveryAddress::find($order->delivery_address_id);
            $billaddress = Address::find($order->address_id);
            $payment = PaymentDetail::where('order_detail_id', $order->id)->first();
            $orderdeliveries = OrderDeliveryItem::join('products', 'products.id', '=', 'order_delivery_items.product_id')->join('order_deliveries', 'order_deliveries.id', '=', 'order_delivery_items.order_delivery_id')->where('order_detail_id', $order->id)->join('vehicles', 'vehicles.id', '=', 'order_deliveries.vehicle_id')->select('order_delivery_items.id as id', 'name', 'quantity', 'order_delivery_items.uom as uom', 'plate_no', 'order_delivery_items.created_at as created_at')->get();

            $vehicles = Vehicle::where('shop_id', Session::get('shop_id'))->get();
            return view('shop.orders.show', compact('page', 'order', 'payment', 'orderitems', 'address', 'billaddress', 'statuses', 'vehicles', 'orderdeliveries'));
        }else{
            return redirect()->back()->with('order not found');
        }
    }


    public function updateOrderStatus(Request $request)
    {
        $order = OrderDetail::find($request['order_id']);
        $ostatus = OrderStatus::where('order_detail_id', $order->id)->where('status', $request['status'])->first();
        if (is_null($ostatus)) {
            $ostatus = new OrderStatus();
            $ostatus->order_detail_id = $order->id;
            $ostatus->status = $request['status'];
            $ostatus->updated_by = Auth::user()->first_name.' '.Auth::user()->last_name;
            $ostatus->save();

            $order->status = $ostatus->status;
            $order->save();
            return redirect()->route('orders.show', encrypt($order->id))->with('success', 'Order status updated successfully');
        }else{
            return redirect()->back()->with('info', 'Order already updated to status '.$ostatus->status.' by '.$ostatus->updated_by.' on '.date('d/m/Y H:i:s a', strtotime($ostatus->created_at)));
        }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
