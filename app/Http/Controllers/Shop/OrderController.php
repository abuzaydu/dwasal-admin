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
use App\Models\Shop;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Models\Customer;
USE App\Models\InvoiceNote;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\ServiceSaleItem;
use App\Models\CustomerTransaction;
use App\Models\SalePayment;
use App\Models\Account;
use App\Models\AccountStatement;
use App\Jobs\StockUpdaterJob;
use App\Models\DeliveryNote;

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
            $payment = PaymentDetail::where('order_detail_id', $order->id)->first();
            $orderitems = OrderItem::where('order_detail_id', $order->id)->join('products', 'products.id', '=', 'order_items.product_id')->get();
            $sale = AnSale::where('order_detail_id', $order->id)->select('id', 'invoice_no')->first();

            $address = DeliveryAddress::find($order->delivery_address_id);
            $billaddress = Address::find($order->address_id);
            $orderdeliveries = OrderDeliveryItem::join('products', 'products.id', '=', 'order_delivery_items.product_id')->join('order_deliveries', 'order_deliveries.id', '=', 'order_delivery_items.order_delivery_id')->where('order_detail_id', $order->id)->join('vehicles', 'vehicles.id', '=', 'order_deliveries.vehicle_id')->select('order_delivery_items.id as id', 'name', 'quantity', 'order_delivery_items.uom as uom', 'plate_no', 'order_delivery_items.created_at as created_at')->get();

            $dnotes = DeliveryNote::where('an_sale_id', $sale->id)->orderBy('delivery_notes.created_at', 'desc')->join('an_sales', 'an_sales.id', '=', 'delivery_notes.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('delivery_notes.id as id', 'note_no', 'delivery_notes.comments as comments', 'delivery_notes.created_at as created_at', 'delivery_notes.updated_at as updated_at', 'name')->get();
            return view('shop.orders.show', compact('page', 'order', 'payment', 'orderitems', 'sale', 'address', 'billaddress', 'statuses', 'orderdeliveries', 'dnotes'));
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

    public function createInvoice($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $user = Auth::user();
        $order = OrderDetail::find(decrypt($id));
        if (!is_null($order)) {
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $account = Account::where('shop_id', $shop->id)->where('type', 'Bank')->first();
            if (!is_null($account)) {
                $now = \Carbon\Carbon::now();
                $sale = AnSale::where('order_detail_id', $order->id)->first();
                if (is_null($sale)) {
                    $clientaddr = Address::find($order->address_id);
                    $name = '';
                    $contactperson = '';
                    if (!empty($clientaddr->company_name)) {
                        $name = $clientaddr->company_name;
                        $contactperson = $clientaddr->first_name.' '.$clientaddr->last_name;
                    }else{
                        $name = $clientaddr->first_name.' '.$clientaddr->last_name;
                    }
                    $customer = Customer::where('shop_id', $shop->id)->where('name', $name)->first();
                    if (is_null($customer)) {
                        $custno = 0;
                        $max_no = Customer::where('shop_id', $shop->id)->latest()->first();
                        if (!is_null($max_no)) {
                            $custno = $max_no->cust_no+1;            
                        }else{
                            $custno = 1;
                        }

                        $customer = new Customer();
                        $customer->shop_id = $shop->id;
                        $customer->name = $name;
                        $customer->contact_person = $contactperson;
                        $customer->email = $clientaddr->email;
                        $customer->phone = $clientaddr->phone;
                        $customer->physical_address = $clientaddr->address;
                        $customer->time_created = $now;
                        $customer->cust_no = $custno;
                        $customer->save();
                    }

                    $deliveryaddr = DeliveryAddress::find($order->delivery_address_id);
                    if (!is_null($deliveryaddr)) {
                        $deliveryaddr->customer_id = $customer->id;
                        $deliveryaddr->save();
                    }

                    $maxsaleno = AnSale::where('shop_id', $shop->id)->orderByRaw('CONVERT(invoice_no, SIGNED) desc')->first();
                    $invoice_no = null;
                    if (!is_null($maxsaleno)) {
                        $invoice_no = $maxsaleno->invoice_no + 1;
                    } else {
                        $invoice_no = 1;
                    }

                    $sale = new AnSale();
                    $sale->customer_id = $customer->id;
                    $sale->shop_id = $shop->id;
                    $sale->user_id = $user->id;
                    $sale->order_detail_id = $order->id;
                    $sale->currency = $dfcurr->code;
                    $sale->defcurr = $dfcurr->code;
                    $sale->ex_rate = 1;
                    $sale->status = 'Paid';
                    $sale->time_created = $now;
                    $sale->sale_type = 'cash';
                    $sale->invoice_no = $invoice_no;
                    $sale->due_date = $now;

                    $notes = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Invoice')->where('note_type', 'Notes')->first();
                    if (!is_null($notes)) {
                        $sale->note = $notes->content;
                    }
                    $sale->account_id = $account->id;
                    $sale->save();
                }
                $orderItems = OrderItem::where('order_detail_id', $order->id)->get();
                foreach($orderItems as $key => $orderitem) {
                    $saleitem = AnSaleItem::where('an_sale_id', $sale->id)->where('product_id', $orderitem->product_id)->first();
                    if (is_null($saleitem)) {
                        $product = Product::find($orderitem->product_id);
                        $punit = ProductUnit::where('product_id', $orderitem->product_id)->where('unit_name', $orderitem->uom)->first();
                        $saleitem = new AnSaleItem();
                        $saleitem->shop_id = $shop->id;
                        $saleitem->an_sale_id = $sale->id;
                        $saleitem->product_id = $orderitem->product_id;
                        $saleitem->product_unit_id = $punit->id;
                        $saleitem->quantity_sold = $orderitem->quantity;
                        $saleitem->unit_cost = $product->unit_cost;
                        $saleitem->buying_price = $saleitem->quantity_sold*$saleitem->unit_cost;
                        $saleitem->retail_price = $orderitem->price;
                        $saleitem->price = $saleitem->retail_price*$saleitem->quantity_sold;
                        $saleitem->tax_amount = 0;
                        $saleitem->time_created = $sale->time_created;
                        $saleitem->save();

                        dispatch(new StockUpdaterJob($shop, $product->id));
                    }
                }

                if ($order->delivery_cost > 0) {
                    $servname = 'Delivery service fee';
                    $service = $shop->services()->where('name', $servname)->first();
                    if (is_null($service)) {
                        $code = $this->getAutoCode();
                        $service = new Service();
                        $service->shop_id = $shop->id;
                        $service->code = $code;
                        $service->name = $servname;
                        $service->time_created = Carbon::now();
                        $service->save();
                    }

                    $saleitemData = ServiceSaleItem::where('an_sale_id', $sale->id)->where('service_id', $service->id)->first();
                    if (is_null($saleitemData)) {
                                            
                        $saleitemData = new ServiceSaleItem;
                        $saleitemData->shop_id = $sale->shop_id;
                        $saleitemData->an_sale_id = $sale->id;
                        $saleitemData->service_id = $service->id;
                        $saleitemData->no_of_repeatition = 1;
                        $saleitemData->price = $order->delivery_cost;
                        $saleitemData->total = $saleitemData->price*$saleitemData->no_of_repeatition;
                        $saleitemData->discount = 0;
                        $saleitemData->total_discount = 0;
                        $saleitemData->time_created = $sale->time_created;
                        $saleitemData->save();
                    }
                }

                $prodsale_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('price');
                $prodsale_discount = AnSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                $prodtax_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');
                $servsale_amount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total');
                $servsale_discount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                $servtax_amount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');

                $sale->sale_amount = ($servsale_amount+$prodsale_amount);
                $sale->sale_discount = ($servsale_discount+$prodsale_discount);
                $sale->tax_amount = ($servtax_amount+$prodtax_amount);
                $sale->save();
                        
                $netsaleamount = ($sale->sale_amount - $sale->sale_discount)+$sale->tax_amount;

                $amount_paid = $netsaleamount;
                $sale->sale_amount_paid = $amount_paid;
                $paydetail = PaymentDetail::where('order_detail_id', $order->id)->first();
                $cheque_no = $paydetail->reference;

                $sale->status = 'Paid';
                $sale->is_paid = true;
                $sale->time_paid = $paydetail->created_at;
                $sale->save();

                $maxrec_no = SalePayment::where('shop_id', $shop->id)->latest()->first();
                $receipt_no = 0;
                if (!is_null($maxrec_no)) {
                    $receipt_no = $maxrec_no->receipt_no + 1;
                } else {
                    $receipt_no = 1;
                }

                $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->first();
                if (is_null($acctrans)) {
                    $acctrans = new CustomerTransaction();
                    $acctrans->shop_id = $shop->id;
                    $acctrans->user_id = $user->id;
                    $acctrans->customer_id = $sale->customer_id;
                    $acctrans->an_sale_id = $sale->id;
                    $acctrans->invoice_no = $sale->invoice_no;
                    $acctrans->amount = $netsaleamount;
                    $acctrans->currency = $sale->currency;
                    $acctrans->defcurr = $sale->defcurr;
                    $acctrans->ex_rate = $sale->ex_rate;
                    $acctrans->date = $sale->time_created;
                    $acctrans->save();
                }

                $payacctrans = CustomerTransaction::where('an_sale_id', $sale->id)->whereNotNull('receipt_no')->first();
                if (is_null($payacctrans)) {
                    $pay_type = $account->type;
                    $payacctrans = new CustomerTransaction();
                    $payacctrans->shop_id = $shop->id;
                    $payacctrans->user_id = $user->id;
                    $payacctrans->customer_id = $sale->customer_id;
                    $payacctrans->an_sale_id = $sale->id;
                    $payacctrans->invoice_no = $sale->invoice_no;
                    $payacctrans->currency = $sale->currency;
                    $payacctrans->defcurr = $sale->defcurr;
                    $payacctrans->ex_rate = $sale->ex_rate;
                    $payacctrans->date = $paydetail->created_at;
                    $payacctrans->receipt_no = $receipt_no;
                    $payacctrans->payment = $amount_paid;
                    $payacctrans->trans_invoice_amount = $amount_paid;
                    $payacctrans->payment_mode = $account->type;
                    $payacctrans->bank_name = $account->bank_name;
                    $payacctrans->bank_branch = $account->bank_branch;
                    $payacctrans->cheque_no = $cheque_no;
                    $payacctrans->save();

                    $payment = SalePayment::where('trans_id', $payacctrans->id)->first();
                    if (is_null($payment)) {
                            
                        $payment = new SalePayment();
                        $payment->an_sale_id = $sale->id;
                        $payment->shop_id = $shop->id;
                        $payment->trans_id = $payacctrans->id;
                        $payment->receipt_no = $receipt_no;
                        $payment->pay_mode = $account->type;
                        $payment->bank_name = $account->bank_name;
                        $payment->bank_branch = $account->bank_branch;
                        $payment->pay_date = $paydetail->created_at;
                        $payment->cheque_no = $cheque_no;
                        $payment->amount = $amount_paid;
                        $payment->currency = $sale->currency;
                        $payment->defcurr = $sale->defcurr;
                        $payment->ex_rate = $sale->ex_rate;
                        $payment->cashier = $user->first_name.' '.$user->last_name;
                        $payment->cc_time = Carbon::now();
                        $payment->save();
                    }

                    $astmt = new AccountStatement();
                    $astmt->shop_id = $shop->id;
                    $astmt->user_id = $user->id;
                    $astmt->customer_transaction_id = $payacctrans->id;
                    $astmt->account_id = $account->id;
                    $astmt->date = $paydetail->created_at;
                    $astmt->debit = $payacctrans->payment;
                    $astmt->credit = 0;
                    $astmt->description = 'Sales Payment (Receipt No. '.sprintf('%04d', $payacctrans->receipt_no).')';
                    $astmt->save();
                }

                return redirect()->route('orders.show', encrypt($order->id))->with('success', 'Invoice created successfully');
            }else{
                return redirect('accounts')->with('error', 'No Bank account added. Please add one to continue');
            }
        }else{
            return redirect()->back()->with('error', 'Record was not Found');
        }
    }


    public function getAutoCode()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $v = '';
        if(preg_match_all('/\b(\w)/',strtoupper($shop->name),$m)) {
            // Log::info($m);
            $v = implode('',$m[1]); // $v is now SOQTU
        }
        $service = $shop->services()->orderBy('id', 'desc')->first();
        if (!is_null($service)) {
            $last = str_replace($v.'/S-', '', $service->code);
            $lastcode = (int)$last;
            // Log::info($last);
            $id = $v.'/S-'.sprintf('%03d', $lastcode+1);
            return $id;   
        }else{
            $id = $v.'/S-'.sprintf('%03d', 1);
            return $id; 
        }
    }

}
