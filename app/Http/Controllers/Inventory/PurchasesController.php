<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Session;
use Auth;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Purchase;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\Stock;
use App\Models\AnSaleItem;
use App\Models\PurchaseTemp;
use App\Models\PurchaseItemTemp;
use App\Models\Product;
use App\Models\ProdDamage;
use App\Jobs\StockUpdaterJob;
use App\Models\PaymentVoucher;
use App\Models\PurchasePayment;
use App\Models\SupplierTransaction;
use App\Models\SupplierAccount;
use App\Models\Supplier;
use App\Models\UnitMeasure;
use App\Models\PurchaseCostItemTemp;
use App\Models\PurchaseCostItem;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrder;
use App\Models\PriceChange;
use App\Models\Account;
use App\Models\AccountStatement;
use Log;

class PurchasesController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Purchases';
        $title = 'Purchases';
        $title_sw = 'Uzalishaji';
        $shop = Shop::find(Session::get('shop_id'));
        $defcurr = '';
        $dfc = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (!is_null($dfc)) {
            $defcurr = $dfc->code;
            $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Stock')->select('id','name')->get();
            $settings = Setting::where('shop_id', $shop->id)->first();
            $accounts = Account::where('shop_id', $shop->id)->get();
            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            
            $now = Carbon::now(); 
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $start_date = date('Y-m-d', strtotime($start));
            $end_date = date('Y-m-d', strtotime($end));
              
            //check if user opted for date range
            $is_post_query = false;
            if (!empty($request['start_date'])) {
                $start_date = $request['start_date'];
                $end_date = $request['end_date'];
                $start = $request['start_date'].' 00:00:00';
                $end = $request['end_date'].' 23:59:59';
                $is_post_query = true;
            }

            $payments = PurchasePayment::where('shop_id', $shop->id)->where('pay_mode', '')->get();
            // Log::info($payments->count().' Purchase Payments');
            foreach ($payments as $key => $value) {
                $value->pay_mode = $value->account;
                $value->save();
            }

            $currsupp = '';
            $purchases = null;
            if (!empty($request['supplier_id'])) {
                $purchases = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->where('supplier_id', $request['supplier_id'])->whereBetween('purchases.time_created', [$start, $end])->where('is_production', false)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->join('users', 'users.id', '=', 'purchases.user_id')->select('purchases.id as id', 'grn_no', 'invoice_no', 'purchases.time_created as time_created', 'name', 'total_amount', 'amount_paid', 'total_cost', 'purchases.created_at as created_at', 'first_name as user')->orderBy('purchases.time_created', 'desc')->get();
            }else{
                $purchases = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('purchases.time_created', [$start, $end])->where('is_production', false)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->join('users', 'users.id', '=', 'purchases.user_id')->select('purchases.id as id', 'grn_no', 'invoice_no', 'purchases.time_created as time_created', 'name', 'total_amount', 'amount_paid', 'total_cost', 'purchases.created_at as created_at', 'first_name as user')->orderBy('purchases.time_created', 'desc')->get();
            }

            return view('products.purchases.index', compact('page', 'title', 'title_sw', 'shop', 'settings', 'accounts', 'currencies', 'defcurr', 'purchases', 'suppliers', 'currsupp', 'is_post_query', 'start_date', 'end_date'));
        
        }else{
            return redirect('settings')->with('warning', 'Please set your Default Currency to continue');
        }
    }

    public function index1(Request $request)
    {
        $page = 'Purchases';
        $title = 'Productions';
        $title_sw = 'Uzalishaji';
        $shop = Shop::find(Session::get('shop_id'));
        $defcurr = '';
        $dfc = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (!is_null($dfc)) {
            $defcurr = $dfc->code;
            $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Stock')->select('id','name')->get();
            $settings = Setting::where('shop_id', $shop->id)->first();
            $accounts = Account::where('shop_id', $shop->id)->get();
            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            
            $now = Carbon::now(); 
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $start_date = date('Y-m-d', strtotime($start));
            $end_date = date('Y-m-d', strtotime($end));
              
            //check if user opted for date range
            $is_post_query = false;
            if (!empty($request['start_date'])) {
                $start_date = $request['start_date'];
                $end_date = $request['end_date'];
                $start = $request['start_date'].' 00:00:00';
                $end = $request['end_date'].' 23:59:59';
                $is_post_query = true;
            }

            $purchases = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('purchases.time_created', [$start, $end])->where('is_production', true)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->join('users', 'users.id', '=', 'purchases.user_id')->select('purchases.id as id', 'grn_no', 'invoice_no', 'purchases.time_created as time_created', 'name', 'total_amount', 'amount_paid', 'total_cost', 'purchases.created_at as created_at', 'first_name as user')->orderBy('purchases.time_created', 'desc')->get();

            return view('products.purchases.index1', compact('page', 'title', 'title_sw', 'shop', 'settings', 'accounts', 'currencies', 'defcurr', 'purchases', 'is_post_query', 'start_date', 'end_date'));
        
        }else{
            return redirect('settings')->with('warning', 'Please set your Default Currency to continue');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'New Purchase';
        $title = 'New Purchase';
        $title_sw = 'Uzalishaji Mapya';
        $units = UnitMeasure::select('unit_name')->get();

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $accounts = Account::where('shop_id', $shop->id)->get();
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

        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (is_null($dfcurr)) {
            return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
        }

        // $purchasetemp = PurchaseTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->whereNull('supplier_id')->first();
        $purchasetemp = PurchaseTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('supplier_id')->first();
        if (is_null($purchasetemp)) {
            $supplier_id = null;
            $supplier = Supplier::where('shop_id', $shop->id)->first();
            if (!is_null($supplier)) {
                $supplier_id = $supplier->id;
            }
            $purchasetemp = new PurchaseTemp();
            $purchasetemp->shop_id = $shop->id;
            $purchasetemp->user_id = $user->id;
            $purchasetemp->purchase_date = Carbon::now();
            $purchasetemp->supplier_id = $supplier_id;
            $purchasetemp->currency = $dfcurr->code;
            $purchasetemp->defcurr = $dfcurr->code;
            $purchasetemp->save();
        }

        $pendingtemps = PurchaseTemp::where('purchase_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('supplier_id')->join('suppliers', 'suppliers.id', '=', 'purchase_temps.supplier_id')->select('purchase_temps.id as id', 'name', 'purchase_temps.created_at as created_at')->get();
        return view('products.purchases.create', compact('page', 'title', 'title_sw', 'units', 'settings', 'shop', 'accounts', 'mindays', 'dfcurr', 'purchasetemp', 'pendingtemps'));
    }

     public function createProduction()
    {
        $page = 'New Production';
        $title = 'New Production';
        $title_sw = 'Uzalishaji Mapya';
        $units = UnitMeasure::select('unit_name')->get();

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $accounts = Account::where('shop_id', $shop->id)->get();
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

        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (is_null($dfcurr)) {
            return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
        }

        // $purchasetemp = PurchaseTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->whereNull('supplier_id')->first();
        $purchasetemp = PurchaseTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('supplier_id')->first();
        if (is_null($purchasetemp)) {
            $supplier_id = null;
            $supplier = Supplier::where('shop_id', $shop->id)->first();
            if (!is_null($supplier)) {
                $supplier_id = $supplier->id;
            }
            $purchasetemp = new PurchaseTemp();
            $purchasetemp->shop_id = $shop->id;
            $purchasetemp->user_id = $user->id;
            $purchasetemp->purchase_date = Carbon::now();
            $purchasetemp->supplier_id = $supplier_id;
            $purchasetemp->currency = $dfcurr->code;
            $purchasetemp->defcurr = $dfcurr->code;
            $purchasetemp->save();
        }

        $pendingtemps = PurchaseTemp::where('purchase_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('supplier_id')->join('suppliers', 'suppliers.id', '=', 'purchase_temps.supplier_id')->select('purchase_temps.id as id', 'name', 'purchase_temps.created_at as created_at')->get();
        return view('products.purchases.create-production', compact('page', 'title', 'title_sw', 'units', 'settings', 'shop', 'accounts', 'mindays', 'dfcurr', 'purchasetemp', 'pendingtemps'));
    }


    public static function pendingPurchase(Request $request)
    {
        $page = 'Products';
        $title = 'New Purchase';
        $title_sw = 'Manunuzi Mapya';
        
        $units = UnitMeasure::select('unit_name')->get();

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $accounts = Account::where('shop_id', $shop->id)->get();
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

        $products = $shop->products()->get();
        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (is_null($dfcurr)) {
            return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
        }

        $purchasetemp = PurchaseTemp::find($request['id']);

        $pendingtemps = PurchaseTemp::where('purchase_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('supplier_id')->join('suppliers', 'suppliers.id', '=', 'purchase_temps.supplier_id')->select('purchase_temps.id as id', 'name', 'purchase_temps.created_at as created_at')->get();
        return view('products.purchases.create', compact('page', 'title', 'title_sw', 'units', 'settings', 'products', 'shop', 'accounts', 'mindays', 'dfcurr', 'purchasetemp', 'pendingtemps'));
    }


    public static function pendingProduction(Request $request)
    {
        $page = 'Products';
        $title = 'New Purchase';
        $title_sw = 'Manunuzi Mapya';
        
        $units = UnitMeasure::select('unit_name')->get();

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $accounts = Account::where('shop_id', $shop->id)->get();
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

        $products = $shop->products()->get();
        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (is_null($dfcurr)) {
            return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
        }

        $purchasetemp = PurchaseTemp::find($request['id']);

        $pendingtemps = PurchaseTemp::where('purchase_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('supplier_id')->join('suppliers', 'suppliers.id', '=', 'purchase_temps.supplier_id')->select('purchase_temps.id as id', 'name', 'purchase_temps.created_at as created_at')->get();
        return view('products.purchases.create-production', compact('page', 'title', 'title_sw', 'units', 'settings', 'products', 'shop', 'accounts', 'mindays', 'dfcurr', 'purchasetemp', 'pendingtemps'));
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
        $settings = Setting::where('shop_id', $shop->id)->first();
        $purchasetemp = PurchaseTemp::find($request['purchase_temp_id']);
        if (!is_null($purchasetemp)) {
            $now = Carbon::now();
            if (!empty($request['purchase_date'])) {
                $crtime = Carbon::now();
                $time = date('H:i:s', strtotime($crtime));
                $now = $request['purchase_date'].' '.$time;
            }

            $total_unit_cost = 0;
            $pitems = PurchaseItemTemp::where('purchase_temp_id', $purchasetemp->id)->get();
            if (!is_null($pitems)) {
                $temps = array();
                foreach ($pitems as $key => $value) {
                    $total_unit_cost += $value->unit_cost;
                    if ($value->quantity_in == 0) {
                        array_push($temps, $value->quantity_in);
                    }
                }

                if (!empty($temps)) {
                    return redirect()->back()->with('warning', 'Please update the quantity and Unit cost of each item to continue');
                }else{
                    $total_amount = 0;
                    $amount_paid = 0; 

                    $max_no = Purchase::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
                    $grnno = 0;
                    if (!is_null($max_no)) {
                        $grnno = $max_no->grn_no+1;
                    }else{
                        $grnno = 1;
                    }

                    $purchase = new Purchase();
                    $purchase->shop_id = $shop->id;
                    $purchase->user_id = $user->id;
                    $purchase->purchase_order_id = $purchasetemp->purchase_order_id;
                    $purchase->order_no = $purchasetemp->order_no;
                    $purchase->supplier_id = $purchasetemp->supplier_id;
                    $purchase->grn_no = $grnno;
                    $purchase->order_no = $purchasetemp->order_no;
                    $purchase->delivery_note_no = $request['delivy_note_no'];
                    $purchase->invoice_no = $request['invoice_no'];
                    $purchase->total_amount = $total_amount;
                    $purchase->amount_paid = $amount_paid;
                    $purchase->comments = $purchasetemp->comments;
                    $purchase->time_created = $now;
                    $purchase->purchase_type = $purchasetemp->purchase_type;
                    $purchase->currency = $purchasetemp->currency;
                    $purchase->defcurr = $purchasetemp->defcurr;
                    $purchase->ex_rate = $purchasetemp->ex_rate;
                    $purchase->is_production = $request['is_production'];
                    $purchase->save();

                    $acctrans = new SupplierTransaction();
                    $acctrans->shop_id = $shop->id;
                    $acctrans->user_id = $user->id;
                    $acctrans->supplier_id = $purchase->supplier_id;
                    $acctrans->purchase_id = $purchase->id;
                    $acctrans->invoice_no = $request['invoice_no'];
                    $acctrans->amount = $total_amount;
                    $acctrans->currency = $purchasetemp->currency;
                    $acctrans->defcurr = $purchasetemp->defcurr;
                    $acctrans->ex_rate = $purchasetemp->ex_rate;
                    $acctrans->date = $now;
                    $acctrans->save();

                    $cost_percent = 0;
                    $total_cost = 0;
                    $costtemps = PurchaseCostItemTemp::where('purchase_temp_id', $purchasetemp->id)->get();
                    if (!is_null($costtemps)) {
                        foreach ($costtemps as $key => $ctemp) {
                            $cost_percent += $ctemp->percent;
                            $total_cost += $ctemp->amount;

                            $costitem = new PurchaseCostItem();
                            $costitem->purchase_id = $purchase->id;
                            $costitem->item_desc = $ctemp->item_desc;
                            $costitem->percent = $ctemp->percent;
                            $costitem->amount = $ctemp->amount;
                            $costitem->save();
                        }
                    }

                    $purchase->total_cost = $total_cost;
                    $purchase->save();

                    $eritems = 0;
                    $pritems = 0;
                    foreach ($pitems as $key => $item) {
                        $product = Product::find($item->product_id);
                        $stock  = new Stock();
                        $unit_ac = 0;
                        if ($total_unit_cost > 0) {
                            $unit_ac = round((($item->unit_cost/$total_unit_cost)*$total_cost)/$item->quantity_in, 2);
                        }
                        $stock->product_id = $product->id;
                        $stock->purchase_id = $purchase->id;
                        $stock->shop_id = $shop->id;
                        $stock->quantity_in = $item->quantity_in;
                        $stock->supp_unit_cost = $item->unit_cost;
                        $stock->unit_cost = $stock->unit_cost+$unit_ac;
                        if ($shop->business_type_id == 1) {
                            $stock->source = 'Production Batch No. '.$purchase->grn_no;
                        }else{
                            $stock->source = 'Purchased';
                        }
                        
                        $stock->stock_date = $now;
                        $stock->expire_date = $item->expire_date;
                        $stock->save();
                        $product = $shop->products()->where('id', $product->id)->first();
                        if (!is_null($product)) {
                            $product->unit_cost = $stock->unit_cost;
                            $product->retail_price = $item->retail_price;
                            $product->save();
                            dispatch(new StockUpdaterJob($shop, $product->id));
                        }

                        $poitem = PurchaseOrderItem::where('purchase_order_id', $purchase->purchase_order_id)->where('product_id', $stock->product_id)->where('shop_id', $shop->id)->first();
                        if (!is_null($poitem)) {
                            $poitem->received_qty = $stock->quantity_in;
                            $poitem->save();
                        }

                        if ($settings->change_price_for_all_store) {
                            $shops = $user->shops()->get();
                            foreach ($shops as $key => $ushop) {
                                $shopproduct = $ushop->products()->where('product_id', $product->id)->first();
                                if (!is_null($shopproduct)) {
                                    if ($shopproduct->pivot->retail_price != $item->retail_price) {
                                        $shopproduct->pivot->retail_price = $item->retail_price;
                                        $shopproduct->pivot->save();

                                        $pchange = new PriceChange();
                                        $pchange->product_id = $shopproduct->id;
                                        $pchange->shop_id = $shop->id;
                                        $pchange->user_id = $user->id;
                                        $pchange->retail_price = $shopproduct->pivot->retail_price;
                                        $pchange->save();
                                    }
                                }
                            }
                        }

                        $total_amount += $item->total;
                        // Update Sale Items with Actual Stock used
                        $this->updateSaleItems($product, $shop);
                    }

                    $porder = PurchaseOrder::find($purchase->purchase_order_id);
                    if (!is_null($porder)) {       
                        if ($pritems > 0 || $eritems > 0) {
                            if ($pritems == 0 && $eritems > 0) {
                                $porder->status = 'Excess Delivered';
                                $porder->save();
                            }else{
                                $porder->status = 'Partially Delivered';
                                $porder->save();
                            }
                        }else{
                            $porder->status = 'Full Delivered';
                            $porder->save();
                        }
                    }
                    if ($request['purchase_type'] == 'cash') {
                        $amount_paid = $total_amount;
                    }else{
                        $amount_paid = $purchasetemp->amount_paid;
                    }

                    $purchase->total_amount = $total_amount;
                    $purchase->amount_paid = $amount_paid;
                    $purchase->save();
                    
                    $account = null;
                    $pay_mode = 'Cash';
                    if (!empty($request['account_id'])) {
                        $account = Account::find($request['account_id']);
                        $pay_mode = $account->type;
                    }
                    $pvno = null;
                    if ($amount_paid > 0) {
                        $pvno = 0;
                        $max_pv_no = PaymentVoucher::where('shop_id', $shop->id)->orderByRaw('CONVERT(pv_no, SIGNED) desc')->first();
                        if (!is_null($max_pv_no)) {
                            $pvno = $max_pv_no->pv_no+1;
                        }else{
                            $pvno = 1;
                        }

                        $pv = new PaymentVoucher();
                        $pv->shop_id = $shop->id;
                        $pv->user_id = $user->id;
                        $pv->pv_no =$pvno;
                        $pv->amount = $amount_paid;
                        $pv->account = $pay_mode;
                        $pv->voucher_for = 'Purchase';
                        $pv->save();
                        
                        $payacctrans = new SupplierTransaction();
                        $payacctrans->shop_id = $shop->id;
                        $payacctrans->user_id = $user->id;
                        $payacctrans->supplier_id = $purchase->supplier_id;
                        $payacctrans->pv_no = $pvno;
                        $payacctrans->payment = $amount_paid;
                        $payacctrans->currency = $purchasetemp->currency;
                        $payacctrans->defcurr = $purchasetemp->defcurr;
                        $payacctrans->ex_rate = $purchasetemp->ex_rate;
                        $payacctrans->payment_mode = $pay_mode;
                        $payacctrans->date = $now;
                        $payacctrans->save();

                        $payment = new PurchasePayment();
                        $payment->shop_id = $shop->id;
                        $payment->purchase_id = $purchase->id;
                        $payment->trans_id = $payacctrans->id;
                        $payment->pay_mode = $pay_mode;
                        $payment->pay_date = $now;
                        $payment->amount = $amount_paid;
                        $payment->currency = $purchasetemp->currency;
                        $payment->defcurr = $purchasetemp->defcurr;
                        $payment->ex_rate = $purchasetemp->ex_rate;
                        $payment->pv_no = $pvno;
                        $payment->save();

                        if (!is_null($account)) {
                            $astmt = new AccountStatement();
                            $astmt->shop_id = $shop->id;
                            $astmt->user_id = $user->id;
                            $astmt->supplier_transaction_id = $payacctrans->id;
                            $astmt->account_id = $account->id;
                            $astmt->date = $now;
                            $astmt->debit = 0;
                            $astmt->credit = $amount_paid;
                            $astmt->description = 'Purchase Payments';
                            $astmt->save();
                        }
                    }else{
                        $utransactions = SupplierTransaction::where('shop_id', $shop->id)->where('supplier_id', $purchase->supplier_id)->whereNotNull('pv_no')->where('is_utilized', false)->where('is_deleted', false)->get();

                        if (!is_null($utransactions)) {
                            foreach ($utransactions as $key => $trans) {
                                $rem_amount = $trans->payment-($trans->trans_invoice_amount+$trans->trans_ob_amount+$trans->trans_credit_amount);
                                if ($rem_amount > 0) {
                                    $paidamount = 0;
                                    if ($rem_amount > $purchase->total_amount) {
                                        $paidamount = $purchase->total_amount;
                                        $trans->trans_invoice_amount = $trans->trans_invoice_amount+$paidamount;
                                        $trans->save();
                                    }else{
                                        $paidamount = $rem_amount;
                                        $trans->trans_invoice_amount = $trans->trans_invoice_amount+$paidamount;
                                        $trans->is_utilized = true;
                                        $trans->save();
                                    }
                                    $payment = PurchasePayment::create([
                                        'purchase_id' => $purchase->id,
                                        'shop_id' => $shop->id,
                                        'trans_id' => $trans->id,
                                        'pv_no' => $trans->pv_no,
                                        'pay_mode' => $trans->payment_mode,
                                        'bank_name' => $trans->bank_name,
                                        'bank_branch' => $trans->bank_branch,
                                        'pay_date' => $trans->date,
                                        'cheque_no' => $trans->cheque_no,
                                        'amount' => $paidamount,
                                        'currency' => $trans->currency,
                                        'defcurr' => $trans->defcurr,
                                        'ex_rate' => $trans->ex_rate,
                                    ]);

                                    $purchase->amount_paid = $paidamount;
                                    if (($purchase->total_amount-$purchase->amount_paid) == 0) {
                                        $purchase->status = 'Paid';
                                    }
                                    $purchase->save();
                                }
                            }
                        }
                    }
                    
                    $puritems = PurchaseItemTemp::where('purchase_temp_id', $purchasetemp->id)->get();
                    foreach ($puritems as $key => $value) {
                        $value->delete();
                    }
                    $costtemps = purchaseCostItemTemp::where('purchase_temp_id', $purchasetemp->id)->get();
                    foreach ($costtemps as $key => $value) {
                        $value->delete();
                    }
                    $purchasetemp->delete();
                    
                    if ($shop->business_type_id == 1 && $purchase->is_production) {
                        return redirect('productions')->with('success', 'Stock were added successfully');
                    }else{
                        return redirect('purchases')->with('success', 'Stocks were added successfully');
                    }
                }
            }else{
                return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
            }
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
        
        $page = 'Purchase Details';
        $title = 'Purchase Details';
        $title_sw = 'Purchase Details';
        try {
            $shop = Shop::find(Session::get('shop_id'));
            $purchase = Purchase::where('purchases.id', decrypt($id))->join('users', 'users.id', '=', 'purchases.user_id')->select('purchases.id as id', 'supplier_id', 'grn_no', 'order_no', 'delivery_note_no', 'invoice_no', 'purchases.time_created as time_created', 'first_name', 'last_name', 'total_amount', 'amount_paid', 'total_cost', 'purchases.created_at as created_at', 'is_production')->first();
            $supplier = Supplier::find($purchase->supplier_id);

            $pitems = Stock::where('purchase_id', $purchase->id)->join('products', 'products.id', '=', 'stocks.product_id')->select('stocks.id as id', 'stocks.quantity_in as quantity_in', 'stocks.unit_cost as unit_cost', 'stocks.stock_date as stock_date', 'stocks.created_at as created_at', 'products.slug as name', 'products.basic_uom as basic_uom')->orderBy('time_created', 'desc')->get();

            return view('products.purchases.show', compact('page', 'title', 'title_sw', 'shop', 'purchase', 'pitems', 'supplier'));

        }catch (DecryptException $e) {
            $msg = 'FAILED. The Payload is invalid.';
            return redirect()->back()->with('error', $msg);
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
        $page = 'Edit Purchase';
        $title = 'Update Purchase';
        $title_sw = 'Hariri Manunuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $purchase = Purchase::find(decrypt($id));
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Stock')->select('id','name')->get();
        if (!is_null($purchase)) {
            return view('products.purchases.edit', compact('page', 'title', 'title_sw', 'purchase', 'suppliers', 'shop'));
        }else{
            return redirect('purchases');
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
        $user = Auth::user();
        $crtime = Carbon::now();
        $time = date('H:i:s', strtotime($crtime));
        $time_created = $request['purchase_date'].' '.$time;
        
        $purchase = Purchase::find(decrypt($id));
        $purchase->time_created = $time_created;
        $purchase->supplier_id = $request['supplier_id'];
        $purchase->comments = $request['comments'];
        $purchase->order_no = $request['order_no'];
        $purchase->delivery_note_no = $request['delivery_note_no'];
        $purchase->invoice_no = $request['invoice_no'];
        $purchase->save();

        $items = Stock::where('purchase_id', $purchase->id)->get();
        foreach ($items as $key => $stock) {
            $stock->stock_date = $purchase->time_created;
            $stock->save();
        }

        $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $purchase->shop_id)->first();
        if (!is_null($acctrans) ) {
            $acctrans->supplier_id = $purchase->supplier_id;
            $acctrans->invoice_no = $purchase->invoice_no;
            $acctrans->save();
        }elseif (!empty($request['supplier_id'] && $purchase->purchase_type == 'credit')) {
            if ($shop->subscription_type_id <= 2) {
                $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->first();
                if (is_null($acctrans)) {
                    $acctrans = new SupplierTransaction();
                    $acctrans->shop_id = $shop->id;
                    $acctrans->user_id = $user->id;
                    $acctrans->supplier_id = $purchase->supplier_id;
                    $acctrans->purchase_id = $purchase->id;
                    $acctrans->invoice_no = $purchase->invoice_no;
                    $acctrans->amount = $purchase->total_amount;
                    $acctrans->date = date('Y-m-d', strtotime($purchase->time_created));
                    $acctrans->save();
                }else{
                    $acctrans->invoice_no = $purchase->invoice_no;
                    $acctrans->date = date('Y-m-d', strtotime($purchase->time_created));
                    $acctrans->save();
                }
            }
        }

        return redirect('purchases')->with('success', 'Purchase was updated successfully');
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
        $user = Auth::user();
        $purchase = Purchase::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($purchase)) {
            $pitems = Stock::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->get();

            foreach ($pitems as $key => $value) {
                $value->is_deleted = true;
                $value->del_by = $user->first_name.'('.Carbon::now().')';
                $value->save();

                dispatch(new StockUpdaterJob($shop, $value->product_id));
            }

            $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();
            foreach ($payments as $key => $payment) {
                $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                if (!is_null($pv)) {
                    $acctrans = SupplierTransaction::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->is_deleted = true;
                        $acctrans->save();
                    }
                    $pv->delete();
                }
                $payment->is_deleted = true;
                $payment->save();
            }

            $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
            if ($acctrans) {
                $acctrans->is_deleted = true;
                $acctrans->save();
            }
            
            $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
            $total_cost = 0;
            foreach ($costitems as $key => $item) {
                $item->is_deleted = true;
                $item->save();
            }

            $purchase->is_deleted = true;
            $purchase->del_by = $user->first_name.' ('.Carbon::now().')';
            $purchase->save();
            
            return redirect()->back()->with('success', 'Purchase was deleted successfully');
        }
    }

    public function deleteMultiple(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        $user = Auth::user();
        foreach ($request->input('ids') as $key => $id) {
            $purchase = Purchase::where('id', $id)->where('shop_id', $shop->id)->first();
            $pitems = Stock::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->get();

            foreach ($pitems as $key => $value) {
                $value->is_deleted = true;
                $value->del_by = $user->first_name.'('.Carbon::now().')';
                $value->save();

                dispatch(new StockUpdaterJob($shop, $value->product_id));
            }

            $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();

            foreach ($payments as $key => $payment) {
                $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                if (!is_null($pv)) {
                    $acctrans = SupplierTransaction::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->is_deleted = true;
                        $acctrans->save();
                        // $acctrans->delete();
                    }
                   
                    $pv->delete();
                }

                $payment->is_deleted = true;
                $payment->save();
                // $payment->delete();
            }

            $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
            if ($acctrans) {
                $acctrans->is_deleted = true;
                $acctrans->save();
                // $acctrans->delete();
            }
            
            $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
            $total_cost = 0;
            foreach ($costitems as $key => $item) {
                $item->is_deleted = true;
                $item->save();
            }
            
            $purchase->is_deleted = true;
            $purchase->del_by = $user->first_name.' ('.Carbon::now().')';
            $purchase->save();
            // $purchase->delete();
        }

        return redirect()->back()->with('success', 'Purchases were deleted successfully');
    }

    public function purchaseItems($id)
    {
        $page = 'Purchase details';
        $title = 'Purchase details';
        $title_sw = 'Maelezo ya Manunuzi';

        $shop = Shop::find(Session::get('shop_id'));
        $purchase = Purchase::find(decrypt($id));
        $supplier = Supplier::find($purchase->supplier_id);
        $products = $shop->products()->get();

        $pitems = Stock::where('purchase_id', $purchase->id)->join('products', 'products.id', '=', 'stocks.product_id')->select('stocks.id as id', 'stocks.quantity_in as quantity_in', 'stocks.unit_cost as unit_cost', 'stocks.unit_cost as unit_cost', 'stocks.stock_date as stock_date', 'stocks.created_at as created_at', 'products.slug as name', 'products.basic_uom as basic_uom')->orderBy('time_created', 'desc')->get();
       
        $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();

        $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
        return view('products.purchases.items', compact('page', 'title', 'title_sw', 'shop', 'purchase', 'pitems', 'supplier', 'payments', 'costitems', 'products'));
    }

    public function addItem(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $purchase = Purchase::find($request['purchase_id']);
        if (!is_null($purchase)) {
            $product = Product::find($request['product_id']);
            if (!is_null($product)) {
                $stock  = new Stock();
                $stock->product_id = $product->id;
                $stock->purchase_id = $purchase->id;
                $stock->shop_id = $shop->id;
                $stock->quantity_in = $request['quantity_in'];
                $stock->unit_cost = $request['unit_cost'];
                if ($shop->business_type_id == 1) {
                    $stock->source = 'Production Batch No. '.$purchase->grn_no;
                }else{
                    $stock->source = 'Purchased';
                }
                
                $stock->stock_date = $purchase->time_created;
                $stock->save();

                $product = $shop->products()->where('id', $product->id)->first();

                if ($product->in_stock == 0) {
                    $product->unit_cost = $request['unit_cost'];
                    $product->save();
                }
                dispatch(new StockUpdaterJob($shop, $product->id));

                // Update Sale Items with Actual Stock used
                $this->updateSaleItems($product, $shop);

                $pitems = Stock::where('purchase_id', $purchase->id)->get();
                $total_amount = 0;
                foreach ($pitems as $key => $item) {
                    $total_amount += ($item->quantity_in*$item->unit_cost);
                }

                $purchase->total_amount = $total_amount;
                $purchase->save();


                $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $purchase->shop_id)->first();
                if (!is_null($acctrans) ) {
                    $acctrans->amount = $purchase->total_amount;
                    $acctrans->save();
                }

                $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
                $total_cost = 0;
                foreach ($costitems as $key => $item) {
                    $total_cost += $item->amount;
                }

                $purchase->total_cost = $total_cost;
                $purchase->save();

                $total_unit_cost = 0;
                $pitems = Stock::where('purchase_id', $purchase->id)->get();
                foreach ($pitems as $key => $value) {
                    $total_unit_cost += $value->unit_cost;
                }

                foreach ($pitems as $key => $stock) {
                    $unit_ac = 0;
                    if ($total_unit_cost = 0) {
                        $unit_ac = round((($stock->unit_cost/$total_unit_cost)*$total_cost)/$stock->quantity_in, 2);
                    }
                    $stock->unit_cost = $stock->unit_cost+$unit_ac;
                    $stock->save();
                }
            }       
        }

        return redirect()->back()->with('success', 'Item Was Added successfully');
    }


    public function updateSaleItems($product, $shop)
    {
        // Check if there are sales done with low stock
        $psitems = AnSaleItem::where('product_id', $product->id)->where('shop_id', $shop->id)->whereNull('stock_id')->where('is_deleted', false)->get();
        Log::info($shop->name.' updating actual stock used for product : '.$product->slug);
        // foreach ($psitems as $key => $value) {

        //     $astocks = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->get();
        //     $qtysold = $value->quantity_sold;
        //     foreach ($astocks as $key => $stock) {
        //         $remqty = ($stock->quantity_in-$stock->quantity_out);
        //         if ($qtysold > 0) {
        //             if ($qtysold <= $remqty) {
        //                 $value->stock_id = $stock->id;
        //                 $value->quantity_sold = $qtysold;
        //                 $value->unit_cost = $stock->unit_cost;
        //                 $value->buying_price = $value->quantity_sold*$value->unit_cost;
        //                 $value->retail_price = $value->retail_price;
        //                 $value->price = $value->retail_price*$value->quantity_sold;
        //                 $value->disc_percent = $value->disc_percent;
        //                 $value->discount = $value->discount;
        //                 $value->total_discount = $value->discount*$value->quantity_sold;
        //                 $value->time_created = $value->time_created;
        //                 if ($value->vat_amount > 0) {
        //                     $value->tax_amount = $value->vat_amount;
        //                     $value->input_tax = $value->buying_price*(($settings->tax_rate/100)/(1+($settings->tax_rate/100)));
        //                 }
        //                 $value->save();

        //                 $stock->quantity_out = $stock->quantity_out+$qtysold;
        //                 if ($stock->quantity_in == $stock->quantity_out) {
        //                     $stock->is_utilized = true;
        //                 }
        //                 $stock->save();
        //             }else{
        //                 $saleitemData = new AnSaleItem;
        //                 $saleitemData->shop_id = $shop->id;
        //                 $saleitemData->an_sale_id = $value->an_sale_id;
        //                 $saleitemData->product_id = $value->product_id;
        //                 $saleitemData->stock_id = $stock->id;
        //                 $saleitemData->product_unit_id = $value->product_unit_id;
        //                 $saleitemData->quantity_sold = $remqty;
        //                 $saleitemData->unit_cost = $stock->unit_cost;
        //                 $saleitemData->buying_price = $saleitemData->quantity_sold*$saleitemData->unit_cost;
        //                 $saleitemData->retail_price = $value->retail_price;
        //                 $saleitemData->price = $saleitemData->retail_price*$saleitemData->quantity_sold;
        //                 $saleitemData->disc_percent = $value->disc_percent;
        //                 $saleitemData->discount = $value->discount;
        //                 $saleitemData->total_discount = $saleitemData->discount*$saleitemData->quantity_sold;
        //                 $saleitemData->time_created = $value->time_created;
        //                 if ($value->vat_amount > 0) {
        //                     $saleitemData->tax_amount = $value->vat_amount;
        //                     $saleitemData->input_tax = $saleitemData->buying_price*(($settings->tax_rate/100)/(1+($settings->tax_rate/100)));
        //                 }
        //                 $saleitemData->sold_in = $value->sold_in;
        //                 $saleitemData->save();

        //                 $stock->quantity_out = $stock->quantity_out+$remqty;
        //                 if ($stock->quantity_in == $stock->quantity_out) {
        //                     $stock->is_utilized = true;
        //                 }
        //                 // $stock->save();
        //             }
        //         }
        //         $qtysold -= $remqty;
        //     }
        // }
    }
    
    public function setOpeningBalance(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $opdate = null;
        if (!empty($request['open_date'])) {
            $opdate = $request['open_date'];
        }else{
            $opdate = Carbon::now();
        }

        $acctrans = SupplierTransaction::where('supplier_id', $request['supplier_id'])->where('is_ob', true)->first();
        if (!is_null($acctrans)) {
            $acctrans->amount = $request['amount'];
            $acctrans->ob_paid = $request['ob_paid'];
            $acctrans->date = $opdate;
            $acctrans->save();
        }else{
            $acctrans = new SupplierTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->supplier_id = $request['supplier_id'];
            $acctrans->is_ob = true;
            $acctrans->amount = $request['amount'];
            $acctrans->currency = $request['currency'];
            $acctrans->date = $opdate;
            $acctrans->save();
        }

        return redirect()->route('suppliers.show', encrypt($request['supplier_id']))->with('success', 'Opening balance was created successfully');
    }
}