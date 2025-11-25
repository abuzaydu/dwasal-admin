<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use DB;
use Log;
use App\Models\Company;
use App\Models\Shop;
use App\Models\User;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\ProductUnit;
use App\Models\Setting;
use App\Models\ProInvoice;
use App\Models\InvoiceItem;
use App\Models\ServiceSaleItem;
use App\Models\InvoiceServitem;
use App\Models\Vehicle;
use App\Models\DeliveryAddress;

class DeliveryNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Delivery Notes';
        $title = 'Delivery Notes';
        $title_sw = 'Vidokezo vya Uwasilishaji';
        $shop = Shop::find(Session::get('shop_id'));
        $dnotes = DeliveryNote::where('delivery_notes.shop_id', $shop->id)->orderBy('delivery_notes.created_at', 'desc')->join('an_sales', 'an_sales.id', '=', 'delivery_notes.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('delivery_notes.id as id', 'note_no', 'delivery_notes.comments as comments', 'delivery_notes.created_at as created_at', 'delivery_notes.updated_at as updated_at', 'name')->get();

        return view('sales.delivery-notes.index', compact('page', 'title', 'title_sw', 'dnotes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $page = 'Delivery Notes';
        $title = 'New Delivery Note';
        $title_sw = 'Kidokezo Kipya cha Uwasilishaji';
        $shop = Shop::find(Session::get('shop_id'));
        $sale = AnSale::where('an_sales.id', decrypt($id))->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'name')->first();
        if (!is_null($sale)) {
            $dnotes = DeliveryNoteItem::join('delivery_notes', 'delivery_notes.id', '=', 'delivery_note_items.delivery_note_id')->where('an_sale_id', $sale->id)->groupBy('product_id')->get([
                \DB::raw('product_id'),
                \DB::raw('uom'),
                \DB::raw('SUM(delivery_qty) as delivery_qty')
            ]);
            
            if ($dnotes->count() > 0) {
                $user = Auth::user();
                $maxno = DeliveryNote::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
                $noteno = null;
                if (!is_null($maxno)) {
                    $noteno = $maxno->note_no+1;
                }else{
                    $noteno = 1;
                }

                $dnote = new DeliveryNote();
                $dnote->an_sale_id = $sale->id;
                $dnote->shop_id = $shop->id;
                $dnote->user_id = $user->id;
                $dnote->note_no = $noteno;
                $dnote->save();

                foreach ($dnotes as $key => $value) {
                    $invqty = AnSaleItem::where('an_sale_id', $sale->id)->where('product_id', $value->product_id)->sum('quantity_sold');
                    if ($value->qty < $invqty) {
                        $delqty = $invqty-$value->delivery_qty;
                        Log::info($value->delivery_qty);
                        $dnoteitem = new DeliveryNoteItem();
                        $dnoteitem->delivery_note_id = $dnote->id;
                        $dnoteitem->product_id = $value->product_id;
                        $dnoteitem->delivery_qty = $delqty;
                        $dnoteitem->uom = $value->uom;
                        $dnoteitem->save();
                    }
                }

                return redirect()->route('delivery-notes.edit', encrypt($dnote->id))->with('info', 'Delivery Note initialised successfully. Please update and confirm the Delivery quantities');
            }else{
                $user = Auth::user();
                $maxno = DeliveryNote::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
                $noteno = null;
                if (!is_null($maxno)) {
                    $noteno = $maxno->note_no+1;
                }else{
                    $noteno = 1;
                }

                $dnote = new DeliveryNote();
                $dnote->an_sale_id = $sale->id;
                $dnote->shop_id = $shop->id;
                $dnote->user_id = $user->id;
                $dnote->note_no = $noteno;
                $dnote->save();

                $invitems = AnSaleItem::where('an_sale_id', $sale->id)->groupBy('product_id')->get([
                    \DB::raw('product_id'),
                    \DB::raw('product_unit_id'),
                    \DB::raw('SUM(quantity_sold) as quantity')
                ]);

                foreach ($invitems as $key => $value) {
                    $punit = ProductUnit::find($value->product_unit_id);
                    $dnoteitem = new DeliveryNoteItem();
                    $dnoteitem->delivery_note_id = $dnote->id;
                    $dnoteitem->product_id = $value->product_id;
                    $dnoteitem->delivery_qty = $value->quantity;
                    $dnoteitem->uom = $punit->unit_name;
                    $dnoteitem->save();
                }

                return redirect()->route('delivery-notes.edit', encrypt($dnote->id))->with('info', 'Delivery Note initialised successfully. Please update and confirm the Delivery quantities');
            }
        }
    }

    public function createFromPFI($id)
    {
        $page = 'Delivery Notes';
        $title = 'New Delivery Note';
        $title_sw = 'Kidokezo Kipya cha Uwasilishaji';
        $shop = Shop::find(Session::get('shop_id'));
        $sale = ProInvoice::where('pro_invoices.id', decrypt($id))->join('customers', 'customers.id', '=', 'pro_invoices.customer_id')->select('pro_invoices.id as id', 'name')->first();
        if (!is_null($sale)) {
            $dnote = DeliveryNote::where('pro_invoice_id', $sale->id)->first();
            if (!is_null($dnote)) {
                return redirect()->route('delivery-notes.show', encrypt($dnote->id))->with('success', 'Delivery Note already created successfully');
            }else{
                return view('sales.delivery-notes.create-from-pfi', compact('page', 'title', 'title_sw', 'shop', 'sale'));
            }
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $dnote = DeliveryNote::find($request['delivery_note_id']);
        if (!is_null($dnote)) {
            $dnote->comments = $request['comments'];
            $dnote->issued_by = $request['issued_by'];
            $dnote->received_by = $request['received_by'];
            $dnote->save();
        }

        return redirect()->route('delivery-notes.show', encrypt($dnote->id))->with('success', 'Delivery Note created successfully');
    }


    public function postDNote(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $maxno = DeliveryNote::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
        $noteno = null;
        if (!is_null($maxno)) {
            $noteno = $maxno->note_no+1;
        }else{
            $noteno = 1;
        }
        $dnote = DeliveryNote::where('pro_invoice_id', $request['pro_invoice_id'])->first();
        if (is_null($dnote)) {
            $dnote = new DeliveryNote();
            $dnote->pro_invoice_id = $request['pro_invoice_id'];
            $dnote->shop_id = $shop->id;
            $dnote->user_id = $user->id;
            $dnote->note_no = $noteno;
            $dnote->comments = $request['comments'];
            $dnote->issued_by = $request['issued_by'];
            $dnote->received_by = $request['received_by'];
            $dnote->save();
        }

        return redirect()->route('delivery-notes.show', encrypt($dnote->id))->with('success', 'Delivery Note created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Delivery Notes';
        $title = 'Delivery Note';
        $title_sw = 'Kidokezo cha Uwasilishaji';

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $dnote = DeliveryNote::find(decrypt($id));
        $user = User::find($dnote->user_id);
        if (!is_null($dnote)) {
            if (empty($dnote->issued_by)) {
                $dnote->issued_by = $user->first_name.' '.$user->last_name;
                $dnote->save();
            }
            
            $sale = null;
            $items = [];
            $proinvoice = null;
            if (!is_null($dnote->an_sale_id)) {
                $sale = AnSale::where('an_sales.id', $dnote->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.cust_no as cust_no', 'customers.postal_address as po_address', 'customers.physical_address as ph_address', 'customers.street as street', 'customers.email as email', 'customers.phone as phone', 'customers.tin as tin', 'customers.vrn as vrn', 'an_sales.id as id', 'an_sales.invoice_no as invoice_no', 'lpo_no', 'an_sales.time_created as time_created', 'pro_invoice_id')->first();
                
                $items = DeliveryNoteItem::where('delivery_note_id', $dnote->id)->join('products', 'products.id', '=', 'delivery_note_items.product_id')->get([
                    DB::raw('slug'),
                    DB::raw('product_code'),
                    DB::raw('delivery_qty'),
                    DB::raw('uom')
                ]);

                $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->join('services', 'services.id', '=', 'service_sale_items.service_id')->groupBy('name')->orderBy('service_sale_items.time_created', 'desc')->get([
                    DB::raw('code'),
                    DB::raw('services.name as name'),
                    DB::raw('service_sale_items.service_id as service_id'),
                    DB::raw('SUM(service_sale_items.no_of_repeatition) as qty')
                ]);
                
                $proinvoice = ProInvoice::where('id', $sale->pro_invoice_id)->select('ref_no', 'invoice_no')->first();

                $delivaddress = DeliveryAddress::find($dnote->delivery_address_id);
                $vehicle = Vehicle::find($dnote->vehicle_id);
                return view('sales.delivery-notes.show', compact('page', 'title', 'title_sw', 'company', 'shop', 'user', 'settings', 'dnote', 'sale', 'items', 'servitems', 'proinvoice', 'delivaddress', 'vehicle'));
            }else{
                $sale = ProInvoice::where('pro_invoices.id', $dnote->pro_invoice_id)->join('customers', 'customers.id', '=', 'pro_invoices.customer_id')->select('customers.name as name', 'customers.cust_no as cust_no', 'customers.postal_address as po_address', 'customers.physical_address as ph_address', 'customers.street as street', 'customers.email as email', 'customers.phone as phone', 'customers.tin as tin', 'customers.vrn as vrn', 'pro_invoices.id as id')->first();
                
                $items = InvoiceItem::where('pro_invoice_id', $sale->id)->join('products', 'products.id', '=', 'invoice_items.product_id')->join('product_shop', 'product_id', '=', 'products.id')->groupBy('name')->orderBy('invoice_items.time_created', 'desc')->get([
                    DB::raw('products.name as name'),
                    DB::raw('invoice_items.product_id as product_id'),
                    DB::raw('product_code as product_code'),
                    DB::raw('SUM(invoice_items.quantity) as quantity_sold'),
                    DB::raw('invoice_items.product_unit_id as product_unit_id')
                ]);


                $servitems = InvoiceServitem::where('pro_invoice_id', $sale->id)->join('services', 'services.id', '=', 'invoice_servitems.service_id')->select('services.id as serv_id', 'services.name as name', 'code', 'invoice_servitems.repeatition as qty')->get();

                $proinvoice = ProInvoice::where('id', $sale->id)->select('ref_no', 'invoice_no')->first();

                return view('sales.delivery-notes.show', compact('page', 'title', 'title_sw', 'company', 'shop', 'user', 'settings', 'dnote', 'sale', 'items', 'servitems', 'proinvoice'));
            }
        }else{
            return redirect()->back()->with('error', 'Delivery Note record not Found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Delivery Notes';
        $title = 'Edit Delivery Note';
        $title_sw = 'Hariri Kidokezo cha Uwasilishaji';
        $dnote = DeliveryNote::find(decrypt($id));
        $sale = AnSale::where('an_sales.id', $dnote->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'customer_id', 'name')->first();
        $dnoteitems = DeliveryNoteItem::where('delivery_note_id', $dnote->id)->join('products', 'products.id', '=', 'delivery_note_items.product_id')->select('delivery_note_items.id as id', 'product_code', 'slug', 'delivery_qty', 'uom')->get();
        $user = User::find($dnote->user_id);

        $delivaddresses = DeliveryAddress::where('customer_id', $sale->customer_id)->get();
        $vehicles = Vehicle::where('company_id', Session::get('company_id'))->get();
        return view('sales.delivery-notes.edit', compact('page', 'title', 'title_sw', 'dnote', 'sale', 'user', 'dnoteitems', 'vehicles', 'delivaddresses'));
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
        $dnote = DeliveryNote::find(decrypt($id));
        $dnote->vehicle_id = $request['vehicle_id'];
        $dnote->delivery_address_id = $request['delivery_address_id'];
        $dnote->received_by = $request['received_by'];
        $dnote->comments = $request['comments'];
        $dnote->save();

        return redirect('delivery-notes')->with('success', 'Delivery Note updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dnote = DeliveryNote::find(decrypt($id));
        if (!is_null($dnote)) {
            $dnoteitems = DeliveryNoteItem::where('delivery_note_id', $dnote->id)->get();
            foreach ($dnoteitems as $key => $value) {
                $value->delete();
            }
            $dnote->delete();
        }
        return redirect('delivery-notes')->with('success', 'Delivery Note deleted successfully');
    }

    public function updateDNoteItem(Request $request)
    {
         $dnoteitem = DeliveryNoteItem::find($request['id']);
         if (!is_null($dnoteitem)) {
            $dnote = DeliveryNote::find($dnoteitem->delivery_note_id);
            $invqty = AnSaleItem::where('an_sale_id', $dnote->an_sale_id)->where('product_id', $dnoteitem->product_id)->sum('quantity_sold');
            $deliveredqty = DeliveryNoteItem::join('delivery_notes', 'delivery_notes.id', '=', 'delivery_note_items.delivery_note_id')->where('an_sale_id', $dnote->an_sale_id)->where('delivery_note_items.id', '!=', $dnoteitem->id)->sum('delivery_qty');
            $remqty = $invqty-$deliveredqty;

            if ($remqty >= $request['delivery_qty']) {
                $dnoteitem->delivery_qty = $request['delivery_qty'];
                $dnoteitem->save();

                return response()->json(['success' => 1, 'msg' => 'Item updated successfully']);
            }else{
                return response()->json(['success' => 0, 'msg' => 'The remaing quatity for deliver of this Order Item is less than the quantity you provide']);
            }
         }
    }

    public function removeDNoteItem($id)
    {
        $dnoteitem = DeliveryNoteItem::find(decrypt($id));
        if (!is_null($dnoteitem)) {
            $dnoteitems = DeliveryNoteItem::where('delivery_note_id', $dnoteitem->delivery_note_id)->count();
            if ($dnoteitems > 1) {
                $dnoteitem->delete();
                return redirect()->back()->with('success', 'Delivery Note Item deleted successfully');
            }else{
                return redirect()->back()->with('error', 'Delivery Note has only one Item. Please Cancel the whole Delivery Note instead of removing the item.');
            }
        }else{
            return redirect()->back()->with('error', 'Item not Found');
        }
    }
}
