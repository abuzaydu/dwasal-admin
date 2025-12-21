<?php

namespace App\Http\Controllers\AppAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use \Carbon\Carbon;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\AnSale;

class TruckScanController extends Controller
{
    public function orderDeliveryDetails(Request $request)
    { 
        $vehicle = Vehicle::where('plate_no', $request['plate_no'])->join('vehicle_types', 'vehicle_types.id', '=', 'vehicles.vehicle_type_id')->select('vehicles.id as id', 'plate_no', 'vehicle_name', 'vehicle_picture', 'name as type')->first();
        if (!is_null($vehicle)) {
            $delivery = DeliveryNote::where('vehicle_id', $vehicle->id)->where('delivery_notes.status', 'Loaded')->join('users', 'users.id', '=', 'delivery_notes.user_id')->join('delivery_addresses', 'delivery_addresses.id', '=', 'delivery_notes.delivery_address_id')->select('delivery_notes.id as id', 'note_no', 'an_sale_id', 'first_name as firstName', 'last_name as lastName', 'plus_code as plusCode', 'address1 as address', 'locality', 'delivery_notes.created_at as loading_time')->first();
            if (!is_null($delivery)) {
                $sale = AnSale::where('an_sales.id', $delivery->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('invoice_no', 'name', 'contact_person', 'phone')->first();
                $order = ['invoice_no' => sprintf('%04d', $sale->invoice_no), 'name' => $sale->name, 'contact_person' => $sale->contact_person, 'phone' => $sale->phone];

                $ditems = DeliveryNoteItem::where('delivery_note_id', $delivery->id)->join('products', 'products.id', '=', 'delivery_note_items.product_id')->select('product_code', 'slug', 'delivery_qty as quantity', 'uom')->get();

                return response()->json(['status' => 'success', 'vehicle' => $vehicle, 'delivery' => $delivery, 'order' => $order, 'items' => $ditems]);
            }else{
                return response()->json(['status' => 'fail', 'message' => 'No Order Delivery assigned to this Truck. Please try again']);
            }
        }else{
            return response()->json(['status' => 'fail', 'message' => 'Vehicle/Truck not found. Please try again']);
        }
    }

    public function guradCheckConfirm(Request $request)
    {
        $delivery = DeliveryNote::find($request['delivery_note_id']);
        if (!is_null($delivery)) {
            $guard = User::find($request['guard_id']);
            $delivery->guard_id = $guard->id;
            $delivery->checked_by = $guard->first_name.' '.$guard->last_name;
            $delivery->checked_at = Carbon::now();
            $delivery->status = 'Gate Pass Checked';
            $delivery->save();

            return response()->json(['status' => 'success', 'message' => 'Delivery note check confirmed successfully']);
        }else{
            return response()->json(['status' => 'fail', 'message' => 'Delivery note found. Please try again']);
        }
    }

    public function dailyDeliveryCheckList(Request $request)
    {
        $now = Carbon::now();
        $start = $now->subDays(25)->startOfDay();
        $end = \Carbon\Carbon::now();
        $mydeliverynotes = DeliveryNote::where('guard_id', $request->guard_id)->whereBetween('delivery_notes.checked_at', [$start, $end])->join('delivery_addresses', 'delivery_addresses.id', '=', 'delivery_notes.delivery_address_id')->join('users', 'users.id', '=', 'delivery_notes.user_id')->join('vehicles', 'vehicles.id', '=', 'delivery_notes.vehicle_id')->join('vehicle_types', 'vehicle_types.id', '=', 'vehicles.vehicle_type_id')->select('delivery_notes.id as id', 'note_no',  'delivery_notes.status as status',  'an_sale_id', 'first_name', 'last_name', 'plus_code as plusCode', 'address1 as address', 'locality', 'delivery_notes.created_at as loading_time', 'checked_at', 'checked_by', 'plate_no', 'vehicle_name', 'vehicle_picture', '.name as type')->get();
        $deliverynotes = [];
        foreach ($mydeliverynotes as $key => $value) {
            $sale = AnSale::where('an_sales.id', $value->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('invoice_no', 'name', 'contact_person', 'phone')->first();
            $order = ['invoice_no' => sprintf('%04d', $sale->invoice_no), 'name' => $sale->name, 'contact_person' => $sale->contact_person, 'phone' => $sale->phone];
            array_push($deliverynotes, array_merge($value->toArray(), $order));
        }

        Log::info($deliverynotes);
        return response()->json($deliverynotes);
    }

    public function deliveryNoteInfo(Request $request)
    {
        $delivery = DeliveryNote::find($request['delivery_note_id']);
        if (!is_null($delivery)) {
            $ditems = DeliveryNoteItem::where('delivery_note_id', $delivery->id)->join('products', 'products.id', '=', 'delivery_note_items.product_id')->select('product_code', 'slug', 'image_url', 'delivery_qty as quantity', 'uom')->get();
            return response()->json($ditems);
        }else{
            Log::info('Delivery Note not found. Please try again');
            return response()->json(['status' => 'fail', 'message' => 'Delivery Note not found. Please try again']);
        }
    }
}
