<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use DB;
use App\Models\Shop;
use App\Models\User;
use App\Models\DeliveryNote;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\Setting;
use App\Models\ProInvoice;
use App\Models\InvoiceItem;
use App\Models\ServiceSaleItem;
use App\Models\InvoiceServitem;

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
            $dnote = DeliveryNote::where('an_sale_id', $sale->id)->first();
            if (!is_null($dnote)) {
                return redirect()->route('delivery-notes.show', encrypt($dnote->id))->with('success', 'Delivery Note already created successfully');
            }else{
                return view('sales.delivery-notes.create', compact('page', 'title', 'title_sw', 'shop', 'sale'));
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $maxno = DeliveryNote::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
        $noteno = null;
        if (!is_null($maxno)) {
            $noteno = $maxno->note_no+1;
        }else{
            $noteno = 1;
        }
        $dnote = DeliveryNote::where('an_sale_id', $request['an_sale_id'])->first();
        if (is_null($dnote)) {
            $dnote = new DeliveryNote();
            $dnote->an_sale_id = $request['an_sale_id'];
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

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $dnote = DeliveryNote::find(decrypt($id));
        $user = User::find($dnote->user_id);
        if (!is_null($dnote)) {
            $sale = null;
            $items = [];
            $proinvoice = null;
            if (!is_null($dnote->an_sale_id)) {
                $sale = AnSale::where('an_sales.id', $dnote->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.cust_no as cust_no', 'customers.postal_address as po_address', 'customers.physical_address as ph_address', 'customers.street as street', 'customers.email as email', 'customers.phone as phone', 'customers.tin as tin', 'customers.vrn as vrn', 'an_sales.id as id', 'an_sales.invoice_no as invoice_no', 'lpo_no', 'an_sales.time_created as time_created', 'pro_invoice_id')->first();
                
                $items = AnSaleItem::where('an_sale_id', $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->join('product_shop', 'product_id', '=', 'products.id')->groupBy('name')->orderBy('an_sale_items.time_created', 'desc')->get([
                    DB::raw('products.name as name'),
                    DB::raw('an_sale_items.product_id as product_id'),
                    DB::raw('product_code as product_code'),
                    DB::raw('SUM(an_sale_items.quantity_sold) as quantity_sold'),
                    DB::raw('an_sale_items.product_unit_id as product_unit_id')
                ]);

                $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->join('services', 'services.id', '=', 'service_sale_items.service_id')->groupBy('name')->orderBy('service_sale_items.time_created', 'desc')->get([
                    DB::raw('code'),
                    DB::raw('services.name as name'),
                    DB::raw('service_sale_items.service_id as service_id'),
                    DB::raw('SUM(service_sale_items.no_of_repeatition) as qty')
                ]);
                
                $proinvoice = ProInvoice::where('id', $sale->pro_invoice_id)->select('ref_no', 'invoice_no')->first();

                return view('sales.delivery-notes.show', compact('page', 'title', 'title_sw', 'shop', 'user', 'settings', 'dnote', 'sale', 'items', 'servitems', 'proinvoice'));
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

                return view('sales.delivery-notes.show', compact('page', 'title', 'title_sw', 'shop', 'user', 'settings', 'dnote', 'sale', 'items', 'servitems', 'proinvoice'));
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
        $sale = AnSale::where('an_sales.id', $dnote->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'name')->first();
        return view('sales.delivery-notes.edit', compact('page', 'title', 'title_sw', 'dnote', 'sale'));
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
        $dnote->comments = $request['comments'];
        $dnote->issued_by = $request['issued_by'];
        $dnote->received_by = $request['received_by'];
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
            $dnote->delete();
        }
        return redirect('delivery-notes')->with('success', 'Delivery Note deleted successfully');
    }    
}
