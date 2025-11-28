<?php

namespace App\Http\Controllers\AppAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\AnSale;

class TruckScanController extends Controller
{
    public function orderDeliveryDetails(Request $request)
    {
        $vehicle = Vehicle::where('plate_no', $request['plate_no'])->join('vehicle_types', 'vehicle_types.id', '=', 'vehicles.vehicle_type_id')->join('ownerships', 'ownerships.id', '=', 'vehicles.ownership_id')->select('vehicles.id as id', 'plate_no', 'reg_date', 'name as type', 'type as ownership', 'status', 'capacity', 'uom')->first();

        $delivery = DeliveryNote::where('vehicle_id', $vehicle->id)->where('delivery_notes.status', 'Loading')->join('users', 'users.id', '=', 'delivery_notes.user_id')->join('delivery_addresses', 'delivery_addresses.id', '=', 'delivery_notes.delivery_address_id')->select('delivery_notes.id as id', 'an_sale_id', 'first_name as firstName', 'last_name as lastName', 'plus_code as plusCode', 'address1 as address', 'locality', 'delivery_notes.created_at as loading_time')->first();
        if (!is_null($delivery)) {
            $order = AnSale::where('an_sales.id', $delivery->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('invoice_no', 'name', 'contact_person', 'phone')->first();

            $ditems = DeliveryNoteItem::where('delivery_note_id', $delivery->id)->join('products', 'products.id', '=', 'delivery_note_items.product_id')->select('product_code', 'slug', 'delivery_qty as quantity', 'uom')->get();

            return response()->json(['status' => 'success', 'vehicle' => $vehicle, 'delivery' => $delivery, 'order' => $order, 'items' => $ditems]);
        }else{
            return response()->json(['status' => 'fail', 'message' => 'No Order Delivery assigned to this Truck. Please try again']);
        }
    }
}
