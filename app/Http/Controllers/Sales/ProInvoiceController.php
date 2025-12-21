<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Response;
use Log;
use Session;
use \Carbon\Carbon;
use Auth;
use App\Models\Company;
use App\Models\Shop;
use App\Models\User;
use App\Models\Setting;
use App\Models\Account;
use App\Models\ShopCurrency;
use App\Models\Payment;
use App\Models\ProInvoice;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTemp;
use App\Models\InvoiceServitem;
use App\Models\InvoiceServiceItemTemp;
use App\Models\SaleTemp;
use App\Models\SaleItemTemp;
use App\Models\ServiceItemTemp;
use App\Models\AnSale;
use App\Models\Invoice;
use App\Models\ServiceSaleItem;
use App\Models\CustomerTransaction;
use App\Models\AnSaleItem;
use App\Models\Stock;
use App\Models\ProdDamage;
use App\Models\TransferOrderItem;
use App\Models\Service;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\SaleReturnItem;
use App\Models\LatestStockSoldLog;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\InvoiceNote;
use App\Models\InvoiceApproval;
use App\Notifications\ProformaInvoiceApprovalNotification;

class ProInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
    {
        $this->middleware(['auth']);
    }


    public function index(Request $request)
    {
        $page = 'Proforma Invoices';
        $title = 'Proforma Invoices';
        $title_sw = 'Ankara za Proforma';

        $now = Carbon::now();
        $start = $now->startOfDay();
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


        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        if (!is_null($shop)) {
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            if (is_null($dfcurr)) {
                if ($user->can('edit-settings')) {
                    return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
                }else{
                    return redirect('user-profile')->with('info', 'Currency is not unpdated, Please Contact Account Admin');
                }
            }

            $invoices = ProInvoice::where('pro_invoices.shop_id', $shop->id)->whereBetween('pro_invoices.time_created', [$start, $end])->join('customers', 'customers.id', '=', 'pro_invoices.customer_id')->join('users', 'users.id', '=', 'pro_invoices.user_id')->select('first_name', 'last_name', 'customers.name as name', 'pro_invoices.id as id', 'pro_invoices.invoice_no as invoice_no', 'pro_invoices.time_created as time_created', 'net_amount', 'pro_invoices.status as status', 'pro_invoices.due_date as due_date', 'pro_invoices.created_at as created_at', 'pro_invoices.updated_at as updated_at')->orderBy('pro_invoices.created_at', 'desc')->get();

            $customer = Customer::where('shop_id', $shop->id)->first();

            $duration = '';
            return view('sales.invoices.pro-invoices.index', compact('page', 'title', 'title_sw', 'invoices', 'customer', 'duration', 'is_post_query', 'start_date', 'end_date'));
        }else{
            return redirect('user-profile')->with('info', 'Shop not found');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'New Proforma Invoice';
        $title = 'New Proforma Invoice';
        $title_sw = 'Ankara mpya ya Proforma';

        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::where('shop_id', $shop->id)->count();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $notes = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Proforma')->where('note_type', 'Notes')->first();
        if (is_null($settings)) {
            $settings = Setting::create([
                'shop_id' => $shop->id,
                'tax_rate' => 18,
                'inv_no_type' => 'Automatic'                
            ]);
        }

        $status = null;
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->first();
        if (!is_null($payment)) {
            $date = Carbon::parse($payment->expire_date);
            $now = Carbon::now();
            $status = $date->diffInDays($now);
        }

        $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NIN'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );
        
        $invoice_date = Carbon::now()->format('Y-m-d');
        if ($shop->business_type_id == 3) {
            return view('sales.invoices.pro-invoices.service-pos', compact('page', 'title', 'title_sw', 'invoice_date', 'invoice', 'settings', 'payment', 'status' , 'custids', 'notes'));    
        } elseif ($shop->business_type_id == 4) {
            return view('sales.invoices.pro-invoices.both-pos', compact('page', 'title', 'title_sw', 'invoice_date', 'invoice', 'settings', 'payment', 'status', 'custids', 'notes'));
        }else{
            return view('sales.invoices.pro-invoices.pos', compact('page', 'title', 'title_sw', 'invoice_date', 'invoice', 'settings', 'payment', 'status' , 'custids', 'notes'));
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
        $settings = Setting::where('shop_id', $shop->id)->first();
        $user = Auth::user();

        if (!empty($request['customer_id'])) {
            $now = Carbon::now();
            if (!empty($request['invoice_date'])) {
                $timenow = Carbon::now();
                $time = date('H:i:s', strtotime($timenow));
                $now = $request['invoice_date'] . ' ' . $time;
            }

            $duedate = Carbon::now()->addDays(30);
            if (!empty($request['due_date'])) {
                $duedate = $request['due_date'];
            }
            $max_no = ProInvoice::where('shop_id', $shop->id)->orderByRaw('CONVERT(invoice_no, SIGNED) desc')->first();
            $invoice_no = 0;
            if (!is_null($max_no)) {
                $invoice_no = $max_no->invoice_no+1;
            }else{
                $invoice_no = 1;
            }
            $servitems = InvoiceServiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            $proditems = InvoiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                
            if ($servitems->count() > 0 || $proditems->count() > 0) {                
                $terms = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Proforma')->where('note_type', 'Terms & Conditions')->first();
                $terms_and_conditions = null;
                if (!is_null($terms)) {
                    $terms_and_conditions = $terms->content;
                }
                $invoice = new ProInvoice();
                $invoice->customer_id = $request['customer_id'];
                $invoice->shop_id = $shop->id;
                $invoice->user_id = $user->id;
                $invoice->summary = $request['summary'];
                $invoice->due_date = $duedate;
                $invoice->discount = $request['discount'];
                $invoice->shipping_cost = $request['shipping_cost'];
                $invoice->adjustment = $request['adjustment'];
                $invoice->notes = $request['notes'];
                $invoice->terms_and_conditions = $terms_and_conditions;
                $invoice->time_created = $now;
                $invoice->invoice_no = $invoice_no;
                $invoice->bank_detail_id = $request['bank_detail_id'];
                $invoice->ref_no = $request['ref_no'];
                if (!$settings->enable_sale_approval) {
                    $invoice->status = 'Pending';
                }
                $invoice->save();

                $net_amount = 0;
                if (!is_null($servitems)) {
                    foreach ($servitems as $key => $value) {
                        $shop_service = $shop->services()->where('id', $value->service_id)->first();
                        $servitemData = new InvoiceServitem;
                        $servitemData->pro_invoice_id = $invoice->id;
                        $servitemData->service_id = $value->service_id;
                        $servitemData->repeatition = $value->repeatition;
                        $servitemData->cost_per_unit = $value->cost_per_unit;
                        $servitemData->amount = $value->amount;
                        $servitemData->disc_percent = $value->disc_percent;
                        $servitemData->discount = $value->discount;
                        $servitemData->total_discount = $servitemData->discount * $servitemData->no_of_repeatition;
                        $servitemData->tax_amount = $value->vat_amount;
                        $servitemData->time_created = $now;
                        $servitemData->save();
                    }
                }

                if (!is_null($proditems)) {
                    $temps = array();
                    $valid = array();
                    foreach ($proditems as $key => $value) {
                        $product = $shop->products()->where('id', $value->product_id)->first();
                        if (is_null($product)) {
                            array_push($valid, $key+1);
                        }
                        if ($value->quantity == 0) {
                            array_push($temps, $value->quantity);
                        }
                    }

                    if (!empty($temps)) {
                        return redirect()->back()->with('warning', 'Please update the quantity of each item to continue');
                    }else if(!empty($valid)){
                        return redirect()->back()->with('warning', 'You have selected Product/Products which are not registered for this shop. Please review your products and try again.');
                    }else{
                        foreach ($proditems as $key => $value) {
                            $invoiceitemData = new InvoiceItem;
                            $invoiceitemData->pro_invoice_id = $invoice->id;
                            $invoiceitemData->product_id = $value->product_id;
                            $invoiceitemData->product_unit_id = $value->product_unit_id;
                            $invoiceitemData->quantity = $value->quantity;
                            $invoiceitemData->cost_per_unit = $value->cost_per_unit;
                            $invoiceitemData->with_vat = $value->with_vat;
                            $invoiceitemData->amount = $value->amount;
                            $invoiceitemData->disc_percent = $value->disc_percent;
                            $invoiceitemData->discount = $value->discount;
                            $invoiceitemData->total_discount = $invoiceitemData->discount * $invoiceitemData->quantity;
                            $invoiceitemData->tax_amount = $value->vat_amount;
                            $invoiceitemData->time_created = $now;
                            $invoiceitemData->save();

                            $net_amount += ($invoiceitemData->amount-$invoiceitemData->total_discount)+$invoiceitemData->tax_amount;
                        }
                    }
                }

                $invoice->net_amount = $net_amount;
                $invoice->save();

                if ($settings->enable_sale_approval) {
                    $invapproval = new InvoiceApproval();
                    $invapproval->shop_id = $shop->id;
                    $invapproval->user_id = $user->id;
                    $invapproval->pro_invoice_id = $invoice->id;
                    $invapproval->save();

                    $permissionName = 'approve-discount';
                    $approvers = User::whereHas('permissions', function ($query) use ($permissionName) {
                        $query->where('name', $permissionName);
                    })->orWhereHas('roles.permissions', function ($query) use ($permissionName) {
                        $query->where('name', $permissionName);
                    })->get();
                    // Log::info($approvers);
                    Notification::sendNow($approvers, new ProformaInvoiceApprovalNotification($invapproval));
                }

                $temp_serv_items = InvoiceServiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                foreach ($temp_serv_items as $key => $item) {
                    $item->delete();
                }
                $temp_items = InvoiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                foreach ($temp_items as $key => $item) {
                    $item->delete();
                }
                return redirect('pro-invoices')->with('success', 'Your Data was submitted successfully');
            }else{
                return redirect()->back()->with('warning', 'You should Select at least one Item to create Proforma.');
            }
        }else{
            return redirect()->back()->with('error', 'Customer required. Please Select Customer to create Proforma.');
        }
    }

    public function cancel()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();

        if ($shop->business_type_id == 3) {
            $temp_items = InvoiceServiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
        }elseif ($shop->business_type_id == 4) {
            $temp_serv_items = InvoiceServiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            foreach ($temp_serv_items as $key => $item) {
                $item->delete();
            }$temp_items = InvoiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
        }else{
            $temp_items = InvoiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
        }

        $success = 'Invoice creation was successfully canceled.';
        return redirect('pro-invoices')->with('success', '$success');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Proforma Invoice';
        $title = 'Proforma Invoice';
        $title_sw = 'Ankara ya Proforma';
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $baccounts = Account::where('shop_id', $shop->id)->where('type', 'Bank')->get();
        $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        $user = Auth::user();
        $invoice = ProInvoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($invoice)) {
            $customer = Customer::find($invoice->customer_id);

            $items = null;
            $servitems = null;
            $grandtotal = 0;
            $tax = 0;

            $servitems = InvoiceServitem::where('pro_invoice_id', $invoice->id)->join('services', 'services.id', '=', 'invoice_servitems.service_id')->select('services.id as serv_id', 'services.name as name', 'code', 'description as description', 'price as price', 'invoice_servitems.id as id', 'invoice_servitems.repeatition as repeatition', 'invoice_servitems.cost_per_unit as cost_per_unit', 'invoice_servitems.amount as amount', 'disc_percent', 'invoice_servitems.total_discount as total_discount', 'invoice_servitems.time_created as time_created' , 'invoice_servitems.tax_amount as tax_amount')->get();

            $items = InvoiceItem::where('pro_invoice_id', $invoice->id)->join('products', 'products.id', '=', 'invoice_items.product_id')->select('products.id as prod_id', 'products.name as name', 'product_code', 'description as description', 'retail_price as retail_price', 'invoice_items.id as id', 'product_unit_id', 'invoice_items.quantity as quantity', 'invoice_items.cost_per_unit as cost_per_unit', 'invoice_items.amount as amount', 'disc_percent', 'invoice_items.total_discount as total_discount', 'invoice_items.time_created as time_created','invoice_items.tax_amount as tax_amount')->get();

            $grandtotal1 = 0;
            $tax1 = 0;
            $tdiscount1 = 0;
            foreach ($servitems as $key => $item) {
                $grandtotal1 += $item->amount;
                $tax1 += $item->tax_amount;
                $tdiscount1 += $item->total_discount;  
            }

            $grandtotal2 = 0;
            $tax2 = 0;
            $tdiscount2 = 0;
            foreach ($items as $key => $item) {
                $grandtotal2 += $item->amount;
                $tax2 += $item->tax_amount;
                $tdiscount2 += $item->total_discount;
            }

            $grandtotal = $grandtotal1+$grandtotal2;
            $total_discount = $tdiscount1+$tdiscount2;
            $tax = ($tax1+$tax2);
            $subtotal = $grandtotal;

            $invoice->net_amount = ($subtotal-$total_discount)+$tax;
            $invoice->save();

            return view('sales.invoices.pro-invoices.show', compact('page', 'title', 'title_sw', 'company', 'shop' , 'settings', 'baccounts', 'defcurr', 'invoice', 'customer', 'items', 'servitems', 'grandtotal', 'total_discount', 'tax', 'subtotal'));
        }else{
            return redirect('pro-invoices')->with('info', 'Invoice Not found');
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
        $page = 'Edit Proforma Invoice';
        $title = 'Edit Proforma Invoice';
        $title_sw = 'Hariri Maelezo ya Ankara';
        $invoice = ProInvoice::find(decrypt($id));
        if (!is_null($invoice)) {
            $shop = Shop::find(Session::get('shop_id'));
            $settings = Setting::where('shop_id', $shop->id)->first();
            $terms = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Proforma')->where('note_type', 'Terms & Conditions')->first();
            $customer = Customer::find($invoice->customer_id);

            $items = InvoiceItem::where('pro_invoice_id', $invoice->id)->join('products', 'products.id', '=', 'invoice_items.product_id')->select('products.id as prod_id', 'products.name as name', 'product_code', 'description as description', 'retail_price as retail_price', 'invoice_items.id as id', 'product_unit_id', 'invoice_items.quantity as quantity', 'invoice_items.cost_per_unit as cost_per_unit', 'invoice_items.amount as amount', 'total_discount', 'tax_amount', 'with_vat', 'invoice_items.time_created as time_created')->get();
            
            $products = []; 
            // $shop->products()->get([
            //     \DB::raw('product_id as id'),
            //     \DB::raw('name'),
            //     \DB::raw('product_code')
            // ]);

            $servitems = InvoiceServitem::where('pro_invoice_id', $invoice->id)->join('services', 'services.id', '=', 'invoice_servitems.service_id')->select('services.id as serv_id', 'services.name as name', 'description as description', 'price as price', 'invoice_servitems.id as id', 'invoice_servitems.repeatition as repeatition', 'invoice_servitems.cost_per_unit as cost_per_unit', 'invoice_servitems.amount as amount', 'total_discount', 'with_vat', 'tax_amount', 'invoice_servitems.time_created as time_created')->get();

            $services = $shop->services()->get([
                \DB::raw('id'),
                \DB::raw('name'),
                \DB::raw('price')
            ]);

            return view('sales.invoices.pro-invoices.edit', compact('page', 'title', 'title_sw', 'shop', 'settings', 'invoice', 'items', 'servitems', 'customer', 'products', 'services', 'terms'));
        }else{
            return redirect('pro-invoices')->with('error', 'Record not Found');
        }
    }

    public function proinvoiceItems($id)
    {
        $page = 'Proforma Invoice Items';
        $title = 'Proforma Invoice Items';
        $title_sw = 'Proforma Invoice Items';

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $invoice = ProInvoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
      
        if (!is_null($invoice)) {
            $servitems = InvoiceServitem::where('pro_invoice_id', $invoice->id)->join('services', 'services.id', '=', 'invoice_servitems.service_id')->select('services.id as serv_id', 'services.name as name', 'description as description', 'price as price', 'invoice_servitems.id as id', 'invoice_servitems.repeatition as repeatition', 'invoice_servitems.cost_per_unit as cost_per_unit', 'invoice_servitems.amount as amount', 'total_discount', 'with_vat', 'tax_amount', 'invoice_servitems.time_created as time_created')->get();

            $items = InvoiceItem::where('pro_invoice_id', $invoice->id)->join('products', 'products.id', '=', 'invoice_items.product_id')->select('products.id as prod_id', 'products.name as name', 'product_code', 'description as description', 'retail_price as retail_price', 'invoice_items.id as id', 'product_unit_id', 'invoice_items.quantity as quantity', 'invoice_items.cost_per_unit as cost_per_unit', 'invoice_items.amount as amount', 'total_discount', 'tax_amount', 'with_vat', 'invoice_items.time_created as time_created')->get();
                    

            $subtotal1 = 0;
            $tax1 = 0;
            foreach ($servitems as $key => $item) {
                $subtotal1 += $item->amount;
                $tax1 += $item->tax_amount;    
            }

            $subtotal2 = 0;
            $tax2 = 0;
            foreach ($items as $key => $item) {
                $subtotal2 += $item->amount;
                $tax2 += $item->tax_amount;    
            }

            $subtotal = $subtotal1+$subtotal2;
            $tax = $tax1+$tax2;

            $services = $shop->services()->get([
                \DB::raw('service_id as id'),
                \DB::raw('name'),
                \DB::raw('price')
            ]);

            $products = $shop->products()->get([
                \DB::raw('product_id as id'),
                \DB::raw('barcode'),
                \DB::raw('name'),
                \DB::raw('product_code'),
                \DB::raw('in_stock'),
                \DB::raw('unit_cost'),
                \DB::raw('retail_price')
            ]);

            return view('sales.invoices.pro-invoices.invoice-items', compact('page', 'title', 'title_sw', 'shop', 'settings', 'invoice','servitems', 'items', 'products', 'services'));
        }else{
            return redirect('pro-invoices')->with('error', 'Record not Found');
        }
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
        $shop = Shop::find(Session::get('shop_id'));
        $timenow = Carbon::now();
        $time = date('H:i:s', strtotime($timenow));
        $invoicedate = $request['invoice_date'] . ' ' . $time;
        
        $invoice = ProInvoice::find(decrypt($id));
        if (!is_null($invoice)) {
            $invoice->customer_id = $request['customer_id'];
            $invoice->time_created = $invoicedate;
            $invoice->due_date = $request['due_date'];
            $invoice->bank_detail_id = $request['bank_detail_id'];
            $invoice->ref_no = $request['ref_no'];
            $invoice->discount = $request['discount'];
            $invoice->shipping_cost = $request['shipping_cost'];
            $invoice->adjustment = $request['adjustment'];
            $invoice->notes = $request['notes'];
            $invoice->terms_and_conditions = $request['terms_and_conditions'];
            $invoice->summary = $request['summary'];
            $invoice->save();
        }

        $success = 'Invoice was successfully updated';
        return redirect('pro-invoices/'.encrypt($invoice->id))->with('success', $success);
    }


    public function editInvoiceItem($id)
    {
        $page = 'Edit Invoice Item';
        $title = 'Edit Invoice Item';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $item = InvoiceItem::find(decrypt($id));
        $product = Product::find($item->product_id);

        return view('sales.invoices.pro-invoices.edit-item', compact('page', 'title', 'shop', 'settings', 'item', 'product'));
    }

     //Update Invoice Items
    public function updateInvoiceItem(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $item = InvoiceItem::find($request['id']);
        if (!is_null($item)) {
            $item->quantity = $request['quantity'];
            $item->cost_per_unit = $request['retail_price'];
            $item->amount = $item->cost_per_unit*$item->quantity;
            if($request['disc_percent'] != $item->disc_percent){
                // Log::info($request['disc_percent']);
                $item->disc_percent = $request['disc_percent'];
                $item->total_discount = $item->amount*($item->disc_percent/100);
                $item->discount = $item->total_discount/$item->quantity;
            }else{    
                $item->total_discount = $request['total_discount'];
                $item->discount = $item->total_discount/$item->quantity;
                $item->disc_percent = ($item->total_discount/$item->amount)*100;
            }
            $item->with_vat = $request['with_vat'];
            $item->save();
            if ($item->with_vat == 'yes') {
                $vat_amount =  ($item->amount-$item->total_discount)*($settings->tax_rate/100);
                $item->tax_amount = $vat_amount;
            }else{
                $item->tax_amount = 0;
            }
            $item->save();
        }
        
        return redirect()->route('pro-invoices.edit', encrypt($request['invoice_id']))->with('success', 'Invoice Item updated successfully');
    }

    public function editInvoiceServItem($id)
    {
        $page = 'Edit Invoice Item';
        $title = 'Edit Invoice Item';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $item = InvoiceServitem::find(decrypt($id));
        $service = Service::find($item->service_id);

        return view('sales.invoices.pro-invoices.edit-servitem', compact('page', 'title', 'shop', 'settings', 'item', 'service'));
    }

    //Update Invoice Items
    public function updateInvoiceServiceItem(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $item = InvoiceServitem::find($request['id']);
        if (!is_null($item)) {
            $item->repeatition = $request['repeatition'];
            $item->cost_per_unit = $request['cost_per_unit'];
            $item->amount = $item->cost_per_unit*$item->repeatition;
            if($request['disc_percent'] != $item->disc_percent){
                // Log::info($request['disc_percent']);
                $item->disc_percent = $request['disc_percent'];
                $item->total_discount = $item->amount*($item->disc_percent/100);
                $item->discount = $item->total_discount/$item->repeatition;
            }else{    
                $item->total_discount = $request['total_discount'];
                $item->discount = $item->total_discount/$item->repeatition;
                $item->disc_percent = ($item->total_discount/$item->amount)*100;
            }
            $item->with_vat = $request['with_vat'];
            $item->save();
            if ($item->with_vat == 'yes') {
                $vat_amount =  ($item->amount-$item->total_discount)*($settings->tax_rate/100);
                $item->tax_amount = $vat_amount;
            }else{
                $item->tax_amount = 0;
            }
            $item->save();
        }
        
        return redirect()->route('pro-invoices.edit', encrypt($request['invoice_id']))->with('success', 'Invoice Item updated successfully');
    }

    // Add Invoice Item
    public function addItem(Request $request)
    {        
        // return $request;
        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::find($request['invoice_id']);
        $settings = Setting::where('shop_id', $shop->id)->first();
        $now = Carbon::now();
        if (!is_null($invoice)) {
            $product = $shop->products()->where('id', $request['product_id'])->first();
            if (!is_null($product)) {
                $invoiceitemData = InvoiceItem::where('product_id', $product->id)->where('pro_invoice_id', $invoice->id)->first();
                if (is_null($invoiceitemData)) {
                    $bunit = ProductUnit::where('product_id', $product->id)->where('is_basic', true)->first();
                    if (is_null($bunit)) {
                        $bunit = new ProductUnit();
                        $bunit->shop_id = $shop->id;
                        $bunit->product_id = $product->id;
                        $bunit->unit_name = $product->basic_uom;
                        $bunit->is_basic = true;
                        $bunit->qty_equal_to_basic = 1;
                        $bunit->unit_price = $product->retail_price;
                        $bunit->save();
                    }
                    $invoiceitemData = new InvoiceItem;
                    $invoiceitemData->pro_invoice_id = $invoice->id;
                    $invoiceitemData->product_id = $product->product_id;
                    $invoiceitemData->product_unit_id = $bunit->id;
                    $invoiceitemData->quantity = $request['quantity'];
                    $invoiceitemData->cost_per_unit = $product->retail_price;
                    $invoiceitemData->amount = $invoiceitemData->cost_per_unit*$invoiceitemData->quantity;
                    $invoiceitemData->time_created = $now;
                    $invoiceitemData->with_vat = 'yes';
                    if ($invoiceitemData->with_vat == 'yes') {
                        $vat_amount =  ($invoiceitemData->amount-$invoiceitemData->total_discount)*($settings->tax_rate/100);
                        $invoiceitemData->tax_amount = $vat_amount;
                    }else{
                        $invoiceitemData->tax_amount = 0;
                    }
                    $invoiceitemData->save();
                    
                    return redirect()->route('pro-invoices.edit', encrypt($invoice->id))->with('success', 'Item added successfully');
                }else{
                    return redirect()->route('pro-invoices.edit', encrypt($invoice->id))->with('info', 'Item already selected');
                }
            }
        }
    }

    public function addServiceItem(Request $request)
    {        

        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::find($request['invoice_id']);
        $now = Carbon::now();
        if (!is_null($invoice)) {
            $service = $shop->services()->where('id', $request['service_id'])->first();
            if (!is_null($service)) {
                $invoiceitemData = new InvoiceServitem;
                $invoiceitemData->pro_invoice_id = $invoice->id;
                $invoiceitemData->service_id = $service->service_id;
                $invoiceitemData->repeatition = $request['repeatition'];
                $invoiceitemData->cost_per_unit = $service->price;
                $invoiceitemData->amount = $service->price;
                $invoiceitemData->time_created = $now;
                $invoiceitemData->save();

                return redirect()->route('pro-invoices.edit', encrypt($invoice->id))->with('success', 'Item added successfully');
            }
        }
    }

    public function deleteItem($id)
    {
            
        $shop = Shop::find(Session::get('shop_id'));
        $item = InvoiceItem::find(decrypt($id));

        if (!is_null($item)) {
            $invoice = ProInvoice::where('id', $item->pro_invoice_id)->where('shop_id', $shop->id)->first();
            if (!is_null($invoice)) {
                $item->delete();
                
                return redirect('pro-invoices/'.encrypt($invoice->id)."/edit");
            }
        }

        return redirect()->back();
    }

     public function deleteServiceItem($id)
    {
            
        $shop = Shop::find(Session::get('shop_id'));
        $item = InvoiceServitem::find(decrypt($id));

        if (!is_null($item)) {
            $invoice = ProInvoice::where('id', $item->pro_invoice_id)->where('shop_id', $shop->id)->first();
            if (!is_null($invoice)) {
                $item->delete();

                return redirect('pro-invoices/'.encrypt($invoice->id)."/edit");
            }
        }
        return redirect()->back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($invoice)) {
            $invoiceitems = InvoiceItem::where('pro_invoice_id', $invoice->id)->get();
            foreach ($invoiceitems as $key => $item) {
                $item->delete();
            }
            $invoice_servitems = InvoiceServitem::where('pro_invoice_id', $invoice->id)->get();
            foreach ($invoice_servitems as $key => $servitem) {
                $servitem->delete();
            }
            $invoice->delete();
        }

        $success = 'Invoice was successfully deleted';
        return redirect('pro-invoices')->with('success', $success);
    }

    public function updateCustomer(Request $request)
    {
        $customer = Customer::find($request['customer_id']);
        $customer->name = $request['name'];
        $customer->phone = $request['phone'];
        $customer->email = $request['email'];
        $customer->address = $request['address'];
        $customer->tin = $request['tin'];
        $customer->save();

        return redirect('pro-invoices/'.encrypt($request['invoice_id'].'/edit'));
    }

    public function changeCustomer(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::where('id', $request['invoice_id'])->where('shop_id', $shop->id)->first();
        $invoice->customer_id = $request['customer_id'];
        $invoice->save();
        
        return redirect('pro-invoices/'.encrypt($invoice->id.'/edit'));
    }

     public function cancelProforma($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($invoice)) {
            $invoice->status = 'Cancelled';
            $invoice->save();
        }

        return redirect('pro-invoices')->with('success', 'Proforma Invoice canceled successfully');
    }


    public function resumeProforma($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($invoice)) {
            $invoice->status = 'Pending';
            $invoice->save();
        }

        return redirect('pro-invoices')->with('success', 'Proforma Invoice resumed successfully');;
    }

    public function createSale(Request $request)
    {
        $user = Auth::user();
        $proinvoice = ProInvoice::find($request['id']);
        if (!is_null($proinvoice)) {
            $shop = Shop::find(Session::get('shop_id'));
            $settings = Setting::where('shop_id', $shop->id)->first();
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $saletemp = SaleTemp::where('pro_invoice_id', $proinvoice->id)->first();
            if (is_null($saletemp)) {       
                $saletemp = new SaleTemp();
                $saletemp->shop_id = $shop->id;
                $saletemp->user_id = $user->id;
                $saletemp->customer_id = $proinvoice->customer_id;
                $saletemp->pro_invoice_id = $proinvoice->id;
                $saletemp->sale_date = Carbon::now();
                $saletemp->currency = $dfcurr->code;
                $saletemp->defcurr = $dfcurr->code;
                $saletemp->save();

                $items = InvoiceItem::where('pro_invoice_id', $proinvoice->id)->get();
            
                if (!is_null($items)) {
                    foreach ($items as $key => $item) {
                        $product = $shop->products()->where('id', $item->product_id)->first();
                        if (!is_null($product)) {
                                
                            $bunit = ProductUnit::where('product_id', $product->id)->where('is_basic', true)->first();
                            $saleItemTemp = new SaleItemTemp();
                            $saleItemTemp->sale_temp_id = $saletemp->id;
                            $saleItemTemp->product_id = $item->product_id;
                            $saleItemTemp->product_unit_id = $bunit->id;
                            $saleItemTemp->quantity_sold = $item->quantity;
                            if (!is_null($product->in_stock)) {
                                $saleItemTemp->curr_stock = $product->in_stock;
                            }else{
                                $saleItemTemp->curr_stock = 0;
                            }
                            $saleItemTemp->unit_cost = $product->unit_cost;
                            $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                            $saleItemTemp->retail_price = $item->cost_per_unit;
                            $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                            $saleItemTemp->disc_percent = $item->disc_percent;
                            $saleItemTemp->discount = $saleItemTemp->retail_price*($saleItemTemp->disc_percent/100);
                            $saleItemTemp->total_discount = $saleItemTemp->discount*$saleItemTemp->quantity_sold;
                            $saleItemTemp->with_vat = $item->with_vat;
                            if ($saleItemTemp->with_vat == 'yes') {
                                $saleItemTemp->vat_amount = ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                            }
                            $saleItemTemp->used_stock = 'Old';
                            $saleItemTemp->sold_in = $item->sold_in;
                            $saleItemTemp->save();
                        }else{
                            Log::info('Product does not exists in this shop');
                        }
                    }
                }

                $servitems = InvoiceServitem::where('pro_invoice_id', $proinvoice->id)->get();
                if (!is_null($servitems)) {
                    foreach ($servitems as $key => $sitem) {
                        $service = $shop->services()->where('id', $sitem->service_id)->first(); 
                        $saleItemTemp = new ServiceItemTemp;
                        $saleItemTemp->sale_temp_id = $saletemp->id;
                        $saleItemTemp->service_id = $service->id;
                        $saleItemTemp->no_of_repeatition = $sitem->repeatition;
                        $saleItemTemp->price = $sitem->cost_per_unit;
                        $saleItemTemp->total = $saleItemTemp->no_of_repeatition*$saleItemTemp->price;
                        $saleItemTemp->disc_percent = $sitem->disc_percent;
                        $saleItemTemp->discount = $saleItemTemp->price*($saleItemTemp->disc_percent/100);
                        $saleItemTemp->total_discount = $saleItemTemp->discount*$saleItemTemp->quantity_sold;
                        $saleItemTemp->with_vat = $sitem->with_vat;
                        if ($saleItemTemp->with_vat == 'yes') {
                            $vat_amount =  ($saleItemTemp->total-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                            $saleItemTemp->vat_amount = $vat_amount;
                        }
                            
                        $saleItemTemp->save();
                    }
                }

                $myrequest = request()->merge(['id' => $saletemp->id]);
                return SaleController::createSaleFromProforma($myrequest);
            }else{

                $temps = SaleItemTemp::where('sale_temp_id', $saletemp->id)->get();
                foreach ($temps as $key => $value) {
                    $value->delete();
                }
                $stemps = ServiceItemTemp::where('sale_temp_id', $saletemp->id)->get();
                foreach ($stemps as $key => $value) {
                    $value->delete();
                }


                $items = InvoiceItem::where('pro_invoice_id', $proinvoice->id)->get();
            
                if (!is_null($items)) {
                    foreach ($items as $key => $item) {
                        $product = $shop->products()->where('id', $item->product_id)->first();
                        $bunit = ProductUnit::where('product_id', $product->id)->where('is_basic', true)->first();
                        $saleItemTemp = new SaleItemTemp();
                        $saleItemTemp->sale_temp_id = $saletemp->id;
                        $saleItemTemp->product_id = $item->product_id;
                        $saleItemTemp->product_unit_id = $bunit->id;
                        $saleItemTemp->quantity_sold = $item->quantity;
                        $saleItemTemp->curr_stock = $product->in_stock;
                        $saleItemTemp->unit_cost = $product->unit_cost;
                        $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                        $saleItemTemp->retail_price = $item->cost_per_unit;
                        $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                        $saleItemTemp->disc_percent = $item->disc_percent;
                        $saleItemTemp->discount = $saleItemTemp->retail_price*($saleItemTemp->disc_percent/100);
                        $saleItemTemp->total_discount = $saleItemTemp->discount*$saleItemTemp->quantity_sold;
                        $saleItemTemp->with_vat = $item->with_vat;
                        if ($saleItemTemp->with_vat == 'yes') {
                            $saleItemTemp->vat_amount = ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                        }
                        $saleItemTemp->used_stock = 'Old';
                        $saleItemTemp->sold_in = $item->sold_in;
                        $saleItemTemp->save();
                    }
                }

                $servitems = InvoiceServitem::where('pro_invoice_id', $proinvoice->id)->get();
                if (!is_null($servitems)) {
                    foreach ($servitems as $key => $sitem) {
                        $service = $shop->services()->where('id', $sitem->service_id)->first(); 
                        $saleItemTemp = new ServiceItemTemp;
                        $saleItemTemp->sale_temp_id = $saletemp->id;
                        $saleItemTemp->service_id = $service->id;
                        $saleItemTemp->no_of_repeatition = $sitem->repeatition;
                        $saleItemTemp->price = $sitem->cost_per_unit;
                        $saleItemTemp->total = $saleItemTemp->no_of_repeatition*$saleItemTemp->price;
                        $saleItemTemp->disc_percent = $sitem->disc_percent;
                        $saleItemTemp->discount = $saleItemTemp->price*($saleItemTemp->disc_percent/100);
                        $saleItemTemp->total_discount = $saleItemTemp->discount*$saleItemTemp->quantity_sold;
                        $saleItemTemp->with_vat = $sitem->with_vat;
                        if ($saleItemTemp->with_vat == 'yes') {
                            $vat_amount =  ($saleItemTemp->total-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                            $saleItemTemp->vat_amount = $vat_amount;
                        }
                            
                        $saleItemTemp->save();
                    }
                }

                $myrequest = request()->merge(['id' => $saletemp->id]);
                return SaleController::createSaleFromProforma($myrequest);
            }
        }else{
            return redirect()->back()->with('error', 'Record was not Found'); 
        }
    }

    public function cpOrders(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $orders = ProInvoice::where('customer_id', $request['cust_id'])->where('shop_id', $shop->id)->where('status', 'Pending')->get();
        return Response::json($orders);
    }

    public function pendingOrders($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $page = 'Invoices';
        $title = 'Proforma Invoices';
        $title_sw = 'Ankara za Proforma';

        $invoices = ProInvoice::where('customer_id', $id)->where('pro_invoices.shop_id', $shop->id)->where('status', 'Pending')->join('customers', 'customers.id', '=', 'pro_invoices.customer_id')->select('customers.name as name', 'pro_invoices.id as id', 'pro_invoices.invoice_no as invoice_no', 'pro_invoices.status as status', 'pro_invoices.due_date as due_date', 'pro_invoices.created_at as created_at', 'pro_invoices.updated_at as updated_at')->orderBy('pro_invoices.created_at', 'desc')->get();

        $customer = Customer::where('shop_id', $shop->id)->first();

        $duration = '';
        return view('sales.invoices.pro-invoices.index', compact('page', 'title', 'title_sw', 'invoices', 'customer', 'duration'));

    }
}
