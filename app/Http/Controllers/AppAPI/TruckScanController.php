<?php

namespace App\Http\Controllers\AppAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderDelivery;
use App\Models\OrderDetail;
use App\Models\OrderDeliveryItem;

class TruckScanController extends Controller
{
    public function orderDeliveryDetails(Request $request)
    {
        $delivery = OrderDelivery::where('order_deliveries.status', 'Loading')->join('vehicles', 'vehicles.id', 'order_deliveries.vehicle_id')->where('plate_no', $request['plate_no'])->join('users', 'users.id', '=', 'order_deliveries.user_id')->join('delivery_addresses', 'delivery_addresses.id', '=', 'order_deliveries.delivery_address_id')->select('order_deliveries.id as id', 'order_detail_id', 'first_name as firstName', 'last_name as lastName', 'plate_no as plateNo', 'chassis_no as chassisNo', 'type', 'plus_code as plusCode', 'address1 as address', 'locality', 'order_deliveries.created_at as loading_time')->first();
        if (!is_null($delivery)) {
            $order = OrderDetail::where('order_details.id', $delivery->order_detail_id)->join('users', 'users.id', '=', 'order_details.user_id')->select('uuid', 'first_name as fName', 'last_name as lName')->first();

            $ditems = OrderDeliveryItem::where('order_delivery_id', $delivery->id)->join('products', 'products.id', '=', 'order_delivery_items.product_id')->select('name', 'quantity', 'uom')->get();

            return response()->json(['status' => 'success', 'delivery' => $delivery, 'order' => $order, 'items' => $ditems]);
        }else{
            return response()->json(['status' => 'fail', 'message' => 'No Order Delivery assigned to this Truck. Please try again']);
        }
    }
}
