<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use \Carbon\Carbon;
use Auth;
use Log;
use App\Models\Company;
use App\Models\Shop;
use App\Models\User;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Models\Payment;
use App\Models\POrderTemp;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderTemp;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseTemp;
use App\Models\PurchaseItemTemp;
use App\Models\UnitMeasure;
use App\Models\Account;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Purchase Orders';
        $title = 'Purchase Orders';
        $title_sw = 'Oda za Manunuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $porders = PurchaseOrder::where('purchase_orders.shop_id', $shop->id)->where('is_deleted', false)->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')->orderBy('created_at', 'desc')->select('purchase_orders.id as id', 'name', 'order_no', 'amount', 'status', 'purchase_orders.created_at as created_at')->get();
        $suppliers = $shop->suppliers()->get();

        return view('products.purchase-orders.index', compact('page', 'title', 'title_sw', 'shop', 'porders', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'New Purchase Order';
        $title = 'New Purchase Order';
        $title_sw = 'Agizo Jipya la Ununuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $units = UnitMeasure::select('unit_name')->get();
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Stock')->get();
        $mindays = 0;
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
        if (!is_null($payment)) {
            $now = Carbon::now();
            $paydate = Carbon::parse($payment->created_at);

            $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
            if (!is_null($lastpay)) {
                $lastexp = Carbon::parse($lastpay->expire_date);
                $oldpaydate = Carbon::parse($lastpay->created_at);
                $slipdays = $paydate->diffInDays($lastexp);
                // return $slipdays;
                if ($slipdays < 15) {
                    $mindays = $now->diffInDays($oldpaydate);
                }else{
                    $mindays = $now->diffInDays($paydate);
                } 
            }else{
                $mindays = $now->diffInDays($paydate);
            }
        }

        if ($mindays < 10) {
            $mindays = 15;
        }
        $ordertemp = POrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->whereNull('supplier_id')->first();
        if (is_null($ordertemp)) {
            $ordertemp = new POrderTemp();
            $ordertemp->shop_id = $shop->id;
            $ordertemp->user_id = $user->id;
            $ordertemp->save();
        }

        $pendingtemps = POrderTemp::where('p_order_temps.shop_id', $shop->id)->where('user_id', $user->id)->join('suppliers', 'suppliers.id', '=', 'p_order_temps.supplier_id')->select('p_order_temps.id as id', 'name', 'p_order_temps.created_at as created_at')->get();

        return view('products.purchase-orders.create', compact('page', 'title', 'title_sw', 'units', 'shop', 'settings', 'ordertemp', 'suppliers', 'mindays', 'pendingtemps'));
    }

    public function pendingOrders(Request $request)
    {
        $page = 'New Purchase Order';
        $title = 'New Purchase Order';
        $title_sw = 'Agizo Jipya la Ununuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $units = UnitMeasure::select('unit_name')->get();
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Stock')->get();
        $mindays = 0;
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
        if (!is_null($payment)) {
            $now = Carbon::now();
            $paydate = Carbon::parse($payment->created_at);

            $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
            if (!is_null($lastpay)) {
                $lastexp = Carbon::parse($lastpay->expire_date);
                $oldpaydate = Carbon::parse($lastpay->created_at);
                $slipdays = $paydate->diffInDays($lastexp);
                // return $slipdays;
                if ($slipdays < 15) {
                    $mindays = $now->diffInDays($oldpaydate);
                }else{
                    $mindays = $now->diffInDays($paydate);
                } 
            }else{
                $mindays = $now->diffInDays($paydate);
            }
        }

        if ($mindays < 10) {
            $mindays = 15;
        }

        $ordertemp = POrderTemp::find($request['id']);

        // return $ordertemp;

        $pendingtemps = POrderTemp::where('p_order_temps.shop_id', $shop->id)->where('user_id', $user->id)->join('suppliers', 'suppliers.id', '=', 'p_order_temps.supplier_id')->select('p_order_temps.id as id', 'name', 'p_order_temps.created_at as created_at')->get();

        return view('products.purchase-orders.create', compact('page', 'title', 'title_sw', 'units', 'shop', 'settings', 'ordertemp', 'suppliers', 'mindays', 'pendingtemps'));
    }

    public function cancelPorder($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $ordertemp = POrderTemp::find(decrypt($id));
        if (!is_null($ordertemp)) {
            $puritems = PurchaseOrderTemp::where('p_order_temp_id', $ordertemp->id)->get();
            foreach ($puritems as $key => $value) {
                $value->delete();
            }
            $ordertemp->delete();
            return redirect()->route('purchase-orders.create')->with('success', 'Order cancelled successfully');
        }else{
            return redirect()->route('purchase-orders.create')->with('info', 'Order not Found');
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
        $ordertemp = POrderTemp::find($request['order_temp_id']);
        if (!is_null($ordertemp)) {
            if (is_null($ordertemp->supplier_id)) {
                return redirect()->route('purchase-orders.create')->with('error', 'Supplier required. Please Select a supplier to continue');
            }
            // return $ordertemp;
            $max_no = PurchaseOrder::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
            $orderno = 0;
            if (!is_null($max_no)) {
                $orderno = $max_no->order_no+1;
            }else{
                $orderno = 1;
            }
        
            $supplier_id = null;
            if (!empty($request['supplier_id']) && $request['supplier_id'] != 0) {
                $supplier_id = $request['supplier_id'];
            }

            $pitems = PurchaseOrderTemp::where('p_order_temp_id', $ordertemp->id)->get();

            if (!is_null($pitems)) {
                $temps = array();
                foreach ($pitems as $key => $value) {
                    if ($value->qty == 0) {
                        array_push($temps, $value->qty);
                    }
                }

                if (!empty($temps)) {
                    return redirect()->route('purchase-orders.create')->with('warning', 'Please update the quantity and Unit cost of each item to continue. You can open the order in pendings to update');
                }else{
                    $porder = new PurchaseOrder();
                    $porder->shop_id = $shop->id;
                    $porder->user_id = $user->id;
                    $porder->supplier_id = $ordertemp->supplier_id;
                    $porder->pfi_no = $ordertemp->pfi_no;
                    $porder->order_no = $orderno;
                    $porder->amount = 0;
                    $porder->comments = $ordertemp->comments;
                    $porder->save();

                    $amount = 0;
                    foreach ($pitems as $key => $item) {
                        $product = Product::find($item->product_id);
                        $poitem  = new PurchaseOrderItem;
                        $poitem->purchase_order_id = $porder->id;
                        $poitem->product_id = $product->id;
                        $poitem->shop_id = $shop->id;
                        $poitem->qty = $item->qty;
                        $poitem->unit_cost = $item->unit_cost;
                        $poitem->save();

                        $amount += $item->qty*$item->unit_cost;
                    }

                    $porder->amount = $amount;
                    $porder->save();

                    foreach ($pitems as $key => $value) {
                        $value->delete();
                    }

                    $ordertemp->delete();

                    return redirect()->route('purchase-orders.create')->with('success', 'Purchase Order were added successfully');
                }
            }else{
                return redirect()->route('purchase-orders.create')->with('warning', 'Please Select at least one Product to continue!. You can check the order in pendings');
            }
        }else{
            return redirect()->route('purchase-orders.create')->with('error', 'Order Temp Not found');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Purchase Order';
        $title = 'Purchase Order';
        $title_sw = 'Oda ya Manunuzi';

        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $porder = PurchaseOrder::find(decrypt($id));
        $user = User::find($porder->user_id);
        $supplier = Supplier::find($porder->supplier_id);
        $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->join('products', 'products.id', '=', 'purchase_order_items.product_id')->select('purchase_order_items.id as id', 'purchase_order_items.qty as qty', 'purchase_order_items.unit_cost as unit_cost', 'purchase_order_items.created_at as created_at', 'products.name as name', 'products.basic_uom as basic_uom')->orderBy('created_at', 'desc')->get();
        
        return view('products.purchase-orders.show', compact('page', 'title', 'title_sw', 'company', 'shop', 'user', 'porder', 'pitems','supplier'));
    }

    public function approvePO($id)
    {
        $porder = PurchaseOrder::find(decrypt($id));
        $porder->status = 'Approved';
        $porder->approved_by = Auth::user()->first_name.' '.Auth::user()->last_name;
        $porder->approved_time = Carbon::now();
        $porder->save();

        return redirect()->route('purchase-orders.show', encrypt($porder->id))->with('PO approved successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $page = 'Update Purchase';
        $title = 'Update Purchase';
        $title_sw = 'Hariri Manunuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $suppliers = $shop->suppliers()->get();
        $products = $shop->products()->get();
        $porder = PurchaseOrder::find(decrypt($id));
        $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->where('shop_id', $shop->id)->join('products', 'products.id', '=', 'purchase_order_items.product_id')->select('purchase_order_items.id as id', 'purchase_order_items.qty as qty', 'purchase_order_items.unit_cost as unit_cost', 'purchase_order_items.created_at as created_at', 'products.name as name', 'products.basic_uom as basic_uom')->orderBy('created_at', 'desc')->get();

        $statuses = [
            ['value' => 'Awaiting for Approval'], 
            ['value' => 'Approved'], 
            ['value' => 'Delivered'],
            ['value' => 'Cancelled']
        ];

        return view('products.purchase-orders.edit', compact('page', 'title', 'title_sw', 'shop', 'suppliers', 'porder', 'pitems', 'products', 'statuses'));
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
        $porder = PurchaseOrder::find(decrypt($id));
        if ($request['status'] == 'Delivered') {
            $porder->status = 'Submitted';
            $porder->save();
            return redirect('create-purchase/'.encrypt($porder->id));
        }else{
            $porder->supplier_id = $request['supplier_id'];
            $porder->pfi_no = $request['pfi_no'];
            $porder->status = $request['status'];
            $porder->comments = $request['comments'];
            $porder->save();
            return redirect('purchase-orders')->with('success', 'Purchase order was updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $porder = PurchaseOrder::find(decrypt($id));
        if (!is_null($porder)) {
            $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->get();
            foreach ($pitems as $key => $item) {
                $item->delete();
            }
        }

        $porder->delete();

        return redirect()->back()->with('success', 'Purchase order was updated successfully');
    }

    public function orderItems($id)
    {
        $page = 'Purchase Order Items';
        $title = 'Purchase Order Items';
        $title_sw = 'Bidhaa za Oda ya Manunuzi';

        $shop = Shop::find(Session::get('shop_id'));
        $porder = PurchaseOrder::find(decrypt($id));
        $supplier = Supplier::find($porder->supplier_id);
        
        $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->where('shop_id', $shop->id)->join('products', 'products.id', '=', 'purchase_order_items.product_id')->select('purchase_order_items.id as id', 'purchase_order_items.qty as qty', 'purchase_order_items.unit_cost as unit_cost', 'purchase_order_items.created_at as created_at', 'products.name as name', 'products.basic_uom as basic_uom')->orderBy('created_at', 'desc')->get();

        return view('products.purchase-orders.items', compact('page', 'title', 'title_sw', 'shop', 'porder', 'pitems', 'supplier'));
    }

    public function deleteMultiple(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        $user = User::find(Session::get('user_id'));
        foreach ($request->input('id') as $key => $id) {
            
            $porder = PurchaseOrder::find($id);
            if (!is_null($porder)) {
                $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->get();
                foreach ($pitems as $key => $item) {
                    $item->delete();
                }
            }

            $porder->delete();
        }

        return redirect()->back()->with('success', 'Purchase Orders were deleted successfully');
    }


    public function createPurchase(Request $request)
    {
        $page = 'New Purchase';
        $title = 'New Purchase';
        $title_sw = 'Manunuzi Mapya';
        $units = UnitMeasure::select('unit_name')->get();

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $accounts = Account::where('shop_id', $shop->id)->get();
        $mindays = 0;
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->first();
        if (!is_null($payment)) {
            $now = Carbon::now();
            $paydate = Carbon::parse($payment->created_at);

            $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->orderBy('created_at', 'desc')->first();
            if (!is_null($lastpay)) {
                $lastexp = Carbon::parse($lastpay->expire_date);
                $oldpaydate = Carbon::parse($lastpay->created_at);
                $slipdays = $paydate->diffInDays($lastexp);
                // return $slipdays;
                if ($slipdays < 15) {
                    $mindays = $now->diffInDays($oldpaydate);
                }else{
                    $mindays = $now->diffInDays($paydate);
                } 
            }else{
                $mindays = $now->diffInDays($paydate);
            }
        }

        if ($mindays < 10) {
            $mindays = 15;
        }

        $products = $shop->products()->get();
        $suppliers = $shop->suppliers()->pluck('name', 'id');

        $porder = PurchaseOrder::find($request['id']);
        if ($porder->status != 'Delivered') {
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            if (is_null($dfcurr)) {
                return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
            }
            
            // $purchasetemp = PurchaseTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->first();
            // if (is_null($purchasetemp)) {
                $purchasetemp = new PurchaseTemp();
                $purchasetemp->shop_id = $shop->id;
                $purchasetemp->user_id = $user->id;
                $purchasetemp->purchase_order_id = $porder->id;
                $purchasetemp->order_no = $porder->order_no;
                $purchasetemp->supplier_id = $porder->supplier_id;
                $purchasetemp->currency = $dfcurr->code;
                $purchasetemp->defcurr = $dfcurr->code;
                $purchasetemp->save();

                $poitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->get();
                foreach ($poitems as $key => $item) {
                    $product = $shop->products()->where('id', $item->product_id)->first();
                    if (!is_null($product)) {
                        $stockItemTemp = new PurchaseItemTemp;
                        $stockItemTemp->purchase_temp_id = $purchasetemp->id;
                        $stockItemTemp->product_id = $item->product_id;
                        $stockItemTemp->quantity_in  = $item->qty;
                        $stockItemTemp->unit_cost = $item->unit_cost;
                        if (!is_null($product->retail_price)) {
                            $stockItemTemp->retail_price = $product->retail_price;
                        }else{
                            $stockItemTemp->retail_price = 0;
                        }
                        $stockItemTemp->total = $item->qty*$item->unit_cost;
                        $stockItemTemp->save();
                    }
                }

                $pendingtemps = PurchaseTemp::where('purchase_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('supplier_id')->join('suppliers', 'suppliers.id', '=', 'purchase_temps.supplier_id')->select('purchase_temps.id as id', 'name', 'purchase_temps.created_at as created_at')->get();
                return view('products.purchases.create', compact('page', 'title', 'title_sw', 'units', 'settings', 'shop', 'accounts', 'mindays', 'dfcurr', 'purchasetemp', 'pendingtemps'));
            // }else{
            //     Log::info('there is pending Orders');
            // }
        }else{
            return redirect()->back()->with('info', 'Purchase already created');
        }
    }
}