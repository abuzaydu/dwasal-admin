<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Route;
use Session;
use Auth;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\SaleTemp;
use App\Models\SaleItemTemp;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\ProductUnit;

class SaleOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Sales Orders';
        $title = 'Sales Orders';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['sale_date'])) {
            $start_date = $request['sale_date'];
            $end_date = $request['sale_date'];
            $start = $request['sale_date'] . ' 00:00:00';
            $end = $request['sale_date'] . ' 23:59:59';
            $is_post_query = true;
        } else if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }
            
        $saleorders = null;
        if (Auth::user()->can('approve-sales-order') || Auth::user()->can('package-sales-order')) {
            $saleorders = SaleOrder::where('sale_orders.shop_id', $shop->id)->whereBetween('order_date', [$start, $end])->join('users', 'users.id', '=', 'sale_orders.user_id')->join('customers', 'customers.id', '=', 'sale_orders.customer_id')->select('sale_orders.id as id', 'order_date', 'order_no', 'users.first_name as first_name', 'users.last_name as last_name', 'name', 'order_amount', 'status', 'is_approved', 'sale_orders.created_at as created_at', 'sale_orders.updated_at as updated_at')->orderBy('order_date', 'desc')->get();
        }else{
            $saleorders = SaleOrder::where('sale_orders.shop_id', $shop->id)->whereBetween('order_date', [$start, $end])->where('user_id', Auth::user()->id)->join('customers', 'customers.id', '=', 'sale_orders.customer_id')->select('sale_orders.id as id', 'order_date', 'order_no', 'name', 'order_amount', 'status', 'is_approved', 'sale_orders.created_at as created_at', 'sale_orders.updated_at as updated_at')->orderBy('order_date' , 'desc')->get();
        }

        return view('sales.sale-orders.index', compact('page', 'title', 'shop', 'settings', 'saleorders', 'start_date', 'end_date', 'is_post_query'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Sales Order';
        $title = 'New Sales Order';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        if (!is_null($settings)) {
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            if (is_null($dfcurr)) {
                return redirect('settings')->with('info', 'Please set Default currency to continue');
            }
            $user = Auth::user();
            $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
            if (!is_null($payment)) {
                $lastorder = SaleOrder::where('shop_id', $shop->id)->latest()->first();
                $orderno = 0;
                if (!is_null($lastorder)) {
                    $orderno = $lastorder->order_no+1;
                }else{
                    $orderno = 1;
                }
                $customers = Customer::where('shop_id', $shop->id)->orderBy('id', 'desc')->get();
                $saleorder = SaleOrder::where('shop_id', $shop->id)->where('user_id', $user->id)->whereNull('customer_id')->first();
                if (is_null($saleorder)) {
                    $saleorder = new SaleOrder();
                    $saleorder->shop_id = $shop->id;
                    $saleorder->user_id = $user->id;
                    $saleorder->order_no = $orderno;
                    $saleorder->order_date = Carbon::now();
                    $saleorder->save();
                }

                $pendingorders = SaleOrder::where('sale_orders.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('customer_id')->where('status', 'Pending')->join('customers', 'customers.id', '=', 'sale_orders.customer_id')->select('sale_orders.id as id', 'name', 'sale_orders.created_at as created_at')->get();
                $custids = array(
                    ['id' => 1, 'name' => 'TIN'],
                    ['id' => 2, 'name' => 'Driving License'],
                    ['id' => 3, 'name' => 'Voters Number'],
                    ['id' => 4, 'name' => 'Passport'],
                    ['id' => 5, 'name' => 'NID'],
                    ['id' => 6, 'name' => 'NIL'],
                    ['id' => 7, 'name' => 'Meter No']
                );

                $mindays = 0;
                $date = Carbon::parse($payment->expire_date);
                $now = Carbon::now();
                $status = $date->diffInDays($now);
                $paydate = Carbon::parse($payment->created_at);

                $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
                if (!is_null($lastpay)) {
                    $lastexp = Carbon::parse($lastpay->expire_date);
                    $oldpaydate = Carbon::parse($lastpay->created_at);
                    $slipdays = $paydate->diffInDays($lastexp);
                    // return $slipdays;
                    if ($slipdays < 15) {
                        $mindays = $now->diffInDays($oldpaydate);
                    } else {
                        $mindays = $now->diffInDays($paydate);
                    }
                } else {
                    $mindays = $now->diffInDays($paydate);
                }

                if ($mindays < 10) {
                    $mindays = 15;
                }

                return view('sales.sale-orders.create', compact('page', 'title', 'shop', 'settings', 'saleorder', 'dfcurr', 'pendingorders', 'custids', 'payment', 'status', 'mindays'));
            }else{
                $info = 'Dear customer your account is not activated please make payment and activate now.';
                return redirect('verify-payment')->with('info', $info);
            }
        }else{
            return redirect('settings')->with('info', 'Please Update Default currency');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $saleorder = SaleOrder::find($request['id']);
        if (!is_null($saleorder)) {
            $orderitems = SaleOrderItem::where('sale_order_id', $saleorder->id)->get();
            $orderamount = 0;
            $orderdiscount = 0;
            foreach ($orderitems as $key => $item) {
                $orderamount += $item->price;
                $orderdiscount += $item->total_discount;
            }
            $saleorder->status = 'Awaiting for Approval';
            $saleorder->order_amount = $orderamount;
            $saleorder->order_discount = $orderdiscount;
            $saleorder->save();

            return redirect()->route('sale-orders.show', encrypt($saleorder->id))->with('success', 'Sales Order submitted successfully');
        }else{
            return redirect('sale-orders')->with('error', 'Order not Found');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Sales Order';
        $title = 'Sales Order';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $saleorder = SaleOrder::where('sale_orders.id', decrypt($id))->join('customers', 'customers.id', '=', 'sale_orders.customer_id')->join('users', 'users.id', '=', 'sale_orders.user_id')->select('sale_orders.id as id', 'first_name', 'last_name', 'custid', 'name', 'customers.phone as phone', 'customers.email as email', 'tin', 'vrn', 'order_no', 'order_date', 'sale_type', 'status', 'is_approved', 'is_packaged', 'order_amount', 'order_discount', 'comments')->first();
        if (!is_null($saleorder)) {
            $items = SaleOrderItem::where('sale_order_id', $saleorder->id)->join('products', 'products.id', '=', 'sale_order_items.product_id')->join('product_units', 'product_units.id', '=', 'sale_order_items.product_unit_id')->select('sale_order_items.id as id', 'name', 'unit_name', 'quantity', 'retail_price', 'price', 'disc_percent', 'discount', 'total_discount', 'vat_amount')->get();

            return view('sales.sale-orders.show', compact('page', 'title', 'shop', 'settings', 'saleorder', 'items'));
        }else{
            return redirect()->back()->with('error', 'Record was not Found');
        }
    }

    public function approveSO($id)
    {
        $saleorder = SaleOrder::find(decrypt($id));
        $saleorder->status = 'Approved';
        $saleorder->is_approved = true;
        $saleorder->approved_by = Auth::user()->first_name.' '.Auth::user()->last_name;
        $saleorder->approved_time = Carbon::now();
        $saleorder->save();

        return redirect()->back()->with('success', 'Sales Order Approved successfully');
    }

    public function rejectSO(Request $request)
    {
        $saleorder = SaleOrder::find($request['sale_order_id']);
        $saleorder->status = 'Rejected';
        $saleorder->comments = $request['comments'];
        $saleorder->save();

        return redirect()->back()->with('success', 'Sale Order reject submitted successfully');
    }

    public function confirmPackaged(Request $request)
    {
        $saleorder = SaleOrder::find($request['id']);
        $orderitems = SaleOrderItem::where('sale_order_id', $saleorder->id)->get();
        $unpackeditems = 0;
        foreach ($orderitems as $key => $item) {
            if ($item->quantity < $item->quantity_packed) {
                $unpackeditems++;
            }
        }
        if ($unpackeditems > 0) {
            $saleorder->is_full_packaged = false;
        }else{
            $saleorder->is_full_packaged = true;
        }
        $saleorder->status = 'Order Packaged';
        $saleorder->is_packaged = true;
        $saleorder->packaged_by = Auth::user()->first_name.' '.Auth::user()->last_name;
        $saleorder->packaging_time = Carbon::now();
        $saleorder->save();
        return redirect()->route('sale-orders.show', encrypt($saleorder->id))->with('success', 'Sales Order Approved successfully');
    }

    public function createSale(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $user = Auth::user();
        $saleorder = SaleOrder::find($request['id']);
        if (!is_null($saleorder)) {
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $orderinvcount = AnSale::where('sale_order_id', $saleorder->id)->count();
            if ($orderinvcount > 0) {
                $remorderitems = array();
                $orderItems = SaleOrderItem::where('sale_order_id', $saleorder->id)->get();
                foreach($orderItems as $key => $orderitem) {
                    $punit = ProductUnit::find($orderitem->product_unit_id);
                    $quantity = $orderitem->quantity * $punit->qty_equal_to_basic;
                    $orderinvitems = AnSaleItem::where('product_id', $orderitem->product_id)->join('an_sales', 'an_sales.id', '=', 'an_sale_items.an_sale_id')->where('sale_order_id', $saleorder->id)->get();
                    $itemqtyinvoieced = 0;
                    foreach ($orderinvitems as $key => $value) {
                        $itemqtyinvoieced += $value->quantity_sold;
                    }
                    
                    // Log::info($orderinvitems);
                    if ($quantity != $itemqtyinvoieced) {
                        $remquantity = ($quantity-$itemqtyinvoieced) / $punit->qty_equal_to_basic;
                        array_push($remorderitems, ['product_id' => $orderitem->product_id, 'product_unit_id' => $orderitem->product_unit_id, 'qty' => $remquantity, 'retail_price' => $orderitem->retail_price, 'with_vat' => $orderitem->with_vat]);
                    }
                }

                // Log::info($remorderitems);
                if (count($remorderitems) > 0) {
                    $saletemp = SaleTemp::where('sale_order_id', $saleorder->id)->first();
                    if (is_null($saletemp)) {       
                        $saletemp = new SaleTemp();
                        $saletemp->shop_id = $shop->id;
                        $saletemp->user_id = $user->id;
                        $saletemp->customer_id = $saleorder->customer_id;
                        $saletemp->is_from_so = true;
                        $saletemp->sale_order_id = $saleorder->id;
                        $saletemp->sale_date = Carbon::now();
                        $saletemp->sale_type = $saleorder->sale_type;
                        $saletemp->due_date = $saleorder->due_date;
                        $saletemp->currency = $dfcurr->code;
                        $saletemp->defcurr = $dfcurr->code;
                        $saletemp->save();

                        foreach ($remorderitems as $key => $item) {
                            $product = $shop->products()->where('id', $item['product_id'])->first();
                            $saleItemTemp = new SaleItemTemp();
                            $saleItemTemp->sale_temp_id = $saletemp->id;
                            $saleItemTemp->product_id = $item['product_id'];
                            $saleItemTemp->product_unit_id = $item['product_unit_id'];
                            $saleItemTemp->quantity_sold = $item['qty'];
                            $saleItemTemp->curr_stock = $product->in_stock;
                            $saleItemTemp->unit_cost = $product->unit_cost;
                            $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                            $saleItemTemp->retail_price = $item['retail_price'];
                            $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                            $saleItemTemp->discount = $item['discount'];
                            $saleItemTemp->disc_percent = $item['disc_percent'];
                            $saleItemTemp->total_discount = $item['total_discount'];
                            $saleItemTemp->with_vat = $item['with_vat'];
                            if ($saleItemTemp->with_vat == 'yes') {
                                $saleItemTemp->vat_amount = ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                            }
                            $saleItemTemp->used_stock = 'Old';
                            $saleItemTemp->sold_in = 'Retail Price';
                            $saleItemTemp->save();
                        }
                    }

                    $myrequest = request()->merge(['id' => $saletemp->id]);
                    
                    return SaleController::createSaleFromSO($myrequest);                    
                }else{
                    $saleorder->status = 'Full Invoiced';
                    $saleorder->save();

                    return redirect('sale-orders')->with('info', 'Sales Order already Invoiced');
                }
            }else{
                $saletemp = SaleTemp::where('sale_order_id', $saleorder->id)->first();
                if (is_null($saletemp)) {       
                    $saletemp = new SaleTemp();
                    $saletemp->shop_id = $shop->id;
                    $saletemp->user_id = $user->id;
                    $saletemp->customer_id = $saleorder->customer_id;
                    $saletemp->is_from_so = true;
                    $saletemp->sale_order_id = $saleorder->id;
                    $saletemp->sale_date = Carbon::now();
                    $saletemp->sale_type = $saleorder->sale_type;
                    $saletemp->due_date = $saleorder->due_date;
                    $saletemp->currency = $dfcurr->code;
                    $saletemp->defcurr = $dfcurr->code;
                    $saletemp->save();

                    $orderitems = SaleOrderItem::where('sale_order_id', $saleorder->id)->get();
                    foreach ($orderitems as $key => $item) {
                        $product = $shop->products()->where('id', $item->product_id)->first();
                        $saleItemTemp = new SaleItemTemp();
                        $saleItemTemp->sale_temp_id = $saletemp->id;
                        $saleItemTemp->product_id = $item->product_id;
                        $saleItemTemp->product_unit_id = $item->product_unit_id;
                        $saleItemTemp->quantity_sold = $item->quantity;
                        $saleItemTemp->curr_stock = $product->in_stock;
                        $saleItemTemp->unit_cost = $product->unit_cost;
                        $saleItemTemp->buying_price = $saleItemTemp->unit_cost*$saleItemTemp->quantity_sold;
                        $saleItemTemp->retail_price = $item->retail_price;
                        $saleItemTemp->price = $saleItemTemp->retail_price*$saleItemTemp->quantity_sold;
                        $saleItemTemp->discount = $item->discount;
                        $saleItemTemp->disc_percent = $item->disc_percent;
                        $saleItemTemp->total_discount = $item->total_discount;
                        $saleItemTemp->with_vat = $item->with_vat;
                        if ($saleItemTemp->with_vat == 'yes') {
                            $saleItemTemp->vat_amount = ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                        }
                        $saleItemTemp->used_stock = 'Old';
                        $saleItemTemp->sold_in = 'Retail Price';
                        $saleItemTemp->save();
                    }
                }

                $myrequest = request()->merge(['id' => $saletemp->id]);
                
                return SaleController::createSaleFromSO($myrequest);
            }
        }else{
            return redirect()->back()->with('error', 'Record was not Found');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $saleorder = SaleOrder::find($id);
        $saleorder->customer_id = $request['customer_id'];
        $saleorder->sale_type = $request['sale_type'];
        $saleorder->due_date = $request['due_date'];
        $saleorder->comments = $request['comments'];
        $saleorder->save();

        return $saleorder;
    }

    public function editSO(Request $request)
    {
        $page = 'New Sales Order';
        $title = 'New Sales Order';

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (is_null($dfcurr)) {
            return redirect('settings')->with('error', 'Please Update Default currency');
        }else{
            $user = Auth::user();
            $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
            if (!is_null($payment)) {
                $customers = Customer::where('shop_id', $shop->id)->orderBy('id', 'desc')->get();
                $saleorder = SaleOrder::find($request['id']);
               
                $pendingorders = SaleOrder::where('sale_orders.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('customer_id')->where('status', 'Pending')->join('customers', 'customers.id', '=', 'sale_orders.customer_id')->select('sale_orders.id as id', 'name', 'sale_orders.created_at as created_at')->get();
                $custids = array(
                    ['id' => 1, 'name' => 'TIN'],
                    ['id' => 2, 'name' => 'Driving License'],
                    ['id' => 3, 'name' => 'Voters Number'],
                    ['id' => 4, 'name' => 'Passport'],
                    ['id' => 5, 'name' => 'NID'],
                    ['id' => 6, 'name' => 'NIL'],
                    ['id' => 7, 'name' => 'Meter No']
                );

                $mindays = 0;
                $date = Carbon::parse($payment->expire_date);
                $now = Carbon::now();
                $status = $date->diffInDays($now);
                $paydate = Carbon::parse($payment->created_at);

                $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
                if (!is_null($lastpay)) {
                    $lastexp = Carbon::parse($lastpay->expire_date);
                    $oldpaydate = Carbon::parse($lastpay->created_at);
                    $slipdays = $paydate->diffInDays($lastexp);
                    // return $slipdays;
                    if ($slipdays < 15) {
                        $mindays = $now->diffInDays($oldpaydate);
                    } else {
                        $mindays = $now->diffInDays($paydate);
                    }
                } else {
                    $mindays = $now->diffInDays($paydate);
                }

                if ($mindays < 10) {
                    $mindays = 15;
                }
                return view('sales.sale-orders.edit', compact('page', 'title', 'shop', 'settings', 'saleorder', 'dfcurr', 'pendingorders', 'custids', 'payment', 'status', 'mindays'));
            }else{
                $info = 'Dear customer your account is not activated please make payment and activate now.';
                return redirect('verify-payment')->with('info', $info);
            }
        }
    }

    public function editPacked(Request $request)
    {
        $page = 'New Sales Order';
        $title = 'New Sales Order';
        $saleorder = SaleOrder::find($request['id']);

        return view('sales.sale-orders.packing', compact('page', 'title', 'saleorder'));
    }

    public function updateSO(Request $request)
    {
        $saleorder = SaleOrder::find($request['id']);
        $saleorder->customer_id = $request['customer_id'];
        $saleorder->sale_type = $request['sale_type'];
        $saleorder->due_date = $request['due_date'];
        $saleorder->comments = $request['comments'];
        $saleorder->save();

        return $saleorder;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $saleorder = SaleOrder::find(decrypt($id));
        if (!is_null($saleorder)) {
            if ($saleorder->status != 'Pending') {
                $saleorder->status = 'Cancelled';
            }else{
                $orderitems = SaleOrderItem::where('sale_order_id', $saleorder->id)->get();
                foreach ($orderitems as $key => $item) {
                    $item->delete();
                }
                $saleorder->delete();
            }
        }
        $success = 'Sale Order was successfully canceled.';
        return redirect()->route('sale-orders.create')->with('success', $success);
    }
}
