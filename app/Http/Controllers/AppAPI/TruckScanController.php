<?php

namespace App\Http\Controllers\AppAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\AnSale;

class TruckScanController extends Controller
{
    public function orderDeliveryDetails(Request $request)
    {
        $delivery = DeliveryNote::where('delivery_notes.status', 'Loading')->join('vehicles', 'vehicles.id', 'delivery_notes.vehicle_id')->where('plate_no', $request['plate_no'])->join('users', 'users.id', '=', 'delivery_notes.user_id')->join('delivery_addresses', 'delivery_addresses.id', '=', 'delivery_notes.delivery_address_id')->select('delivery_notes.id as id', 'order_detail_id', 'first_name as firstName', 'last_name as lastName', 'plate_no as plateNo', 'chassis_no as chassisNo', 'type', 'plus_code as plusCode', 'address1 as address', 'locality', 'delivery_notes.created_at as loading_time')->first();
        if (!is_null($delivery)) {
            $order = AnSale::where('an_sales.id', $delivery->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('uuid', 'name', 'contact_person', 'phone')->first();

            $ditems = DeliveryNoteItem::where('delivery_note_id', $delivery->id)->join('products', 'products.id', '=', 'order_delivery_items.product_id')->select('name', 'quantity', 'uom')->get();

            return response()->json(['status' => 'success', 'delivery' => $delivery, 'order' => $order, 'items' => $ditems]);
        }else{
            return response()->json(['status' => 'fail', 'message' => 'No Order Delivery assigned to this Truck. Please try again']);
        }
    }
}
