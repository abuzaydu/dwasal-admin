<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Models\Account;
use App\Models\Vendor;
use App\Models\PartPurchase;
use App\Models\PartPurchaseItem;
use App\Models\PartPurchaseTemp;
use App\Models\PartPurchaseItemTemp;
use App\Models\Part;
use App\Models\PartCategory;
use App\Models\PartLocation;
use App\Models\VendorTransaction;
use App\Models\PartPurchasePayment;
use App\Models\AccountStatement;
use App\Models\PaymentVoucher;

class PartPurchaseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Part Purchases';
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

        $purchases = PartPurchase::where('part_purchases.company_id', Session::get('company_id'))->whereBetween('pp_date', [$start, $end])->join('vendors', 'vendors.id', 'part_purchases.vendor_id')->join('users', 'users.id', '=', 'part_purchases.user_id')->select('part_purchases.id as id', 'pp_date', 'pp_code', 'vendor_name', 'status', 'total_amount', 'amount_paid', 'part_purchases.created_at as created_at', 'first_name as user')->get();

        return view('vms.parts.purchases.index', compact('page', 'start_date', 'end_date', 'is_post_query', 'purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Part Purchase';
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $accounts = Account::where('shop_id', $shop->id)->get();

        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (is_null($dfcurr)) {
            return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
        }

        $purchasetemp = PartPurchaseTemp::where('company_id', $company->id)->where('user_id', $user->id)->whereNotNull('vendor_id')->first();
        if (is_null($purchasetemp)) {
            $vendor_id = null;
            $vendor = Vendor::where('company_id', $company->id)->where('vendor_for', 'Parts')->first();
            if (!is_null($vendor)) {
                $vendor_id = $vendor->id;
            }
            $purchasetemp = new PartPurchaseTemp();
            $purchasetemp->company_id = $company->id;
            $purchasetemp->user_id = $user->id;
            $purchasetemp->vendor_id = $vendor_id;
            $purchasetemp->pp_date = Carbon::now();
            $purchasetemp->currency = $dfcurr->code;
            $purchasetemp->defcurr = $dfcurr->code;
            $purchasetemp->save();
        }

        $pendingtemps = PartPurchaseTemp::where('part_purchase_temps.company_id', $company->id)->where('user_id', $user->id)->whereNotNull('vendor_id')->join('vendors', 'vendors.id', '=', 'part_purchase_temps.vendor_id')->select('part_purchase_temps.id as id', 'vendor_name', 'part_purchase_temps.created_at as created_at')->get();

        $partcategories = PartCategory::where('company_id', $company->id)->get();
        $partlocations = PartLocation::where('company_id', $company->id)->get();
        return view('vms.parts.purchases.create', compact('page', 'settings', 'shop', 'accounts', 'dfcurr', 'purchasetemp', 'pendingtemps', 'partcategories', 'partlocations'));
    }


    public static function pendingPurchase(Request $request)
    {
        $page = 'New Part Purchase';
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $accounts = Account::where('shop_id', $shop->id)->get();

        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (is_null($dfcurr)) {
            return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
        }

        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (is_null($dfcurr)) {
            return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
        }

        $purchasetemp = PartPurchaseTemp::find($request['id']);

        $pendingtemps = PartPurchaseTemp::where('part_purchase_temps.company_id', $company->id)->where('user_id', $user->id)->whereNotNull('vendor_id')->join('vendors', 'vendors.id', '=', 'part_purchase_temps.vendor_id')->select('part_purchase_temps.id as id', 'vendor_name', 'part_purchase_temps.created_at as created_at')->get();
        $partcategories = PartCategory::where('company_id', $company->id)->get();
        $partlocations = PartLocation::where('company_id', $company->id)->get();
        return view('vms.parts.purchases.create', compact('page', 'settings', 'shop', 'accounts', 'dfcurr', 'purchasetemp', 'pendingtemps', 'partcategories', 'partlocations'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $purchasetemp = PartPurchaseTemp::find($request['part_purchase_temp_id']);
        if (!is_null($purchasetemp)) {
            $now = Carbon::now();
            if (!empty($request['purchase_date'])) {
                $crtime = Carbon::now();
                $time = date('H:i:s', strtotime($crtime));
                $now = $request['purchase_date'].' '.$time;
            }

            $total_unit_cost = 0;
            $pitems = PartPurchaseItemTemp::where('part_purchase_temp_id', $purchasetemp->id)->get();
            if (!is_null($pitems)) {
                $temps = array();
                foreach ($pitems as $key => $value) {
                    if ($value->pp_qty == 0) {
                        array_push($temps, $value->pp_qty);
                    }
                }

                if (!empty($temps)) {
                    return redirect()->back()->with('warning', 'Please update the quantity and Unit cost of each item to continue');
                }else{
                    $total_amount = 0;
                    $amount_paid = 0; 

                    $max_no = PartPurchase::where('company_id', $company->id)->orderBy('created_at', 'desc')->first();
                    $grnno = 0;
                    if (!is_null($max_no)) {
                        $grnno = $max_no->pp_code+1;
                    }else{
                        $grnno = 1;
                    }

                    $purchase = new PartPurchase();
                    $purchase->company_id = $company->id;
                    $purchase->user_id = $user->id;
                    $purchase->vendor_id = $purchasetemp->vendor_id;
                    $purchase->pp_code = $grnno;
                    $purchase->total_amount = $total_amount;
                    $purchase->amount_paid = $amount_paid;
                    $purchase->comments = $purchasetemp->comments;
                    $purchase->pp_date = $now;
                    $purchase->purchase_type = $purchasetemp->purchase_type;
                    $purchase->currency = $purchasetemp->currency;
                    $purchase->defcurr = $purchasetemp->defcurr;
                    $purchase->ex_rate = $purchasetemp->ex_rate;
                    $purchase->save();

                    $acctrans = new VendorTransaction();
                    $acctrans->company_id = $company->id;
                    $acctrans->user_id = $user->id;
                    $acctrans->vendor_id = $purchase->vendor_id;
                    $acctrans->part_purchase_id = $purchase->id;
                    $acctrans->amount = $total_amount;
                    $acctrans->currency = $purchasetemp->currency;
                    $acctrans->defcurr = $purchasetemp->defcurr;
                    $acctrans->ex_rate = $purchasetemp->ex_rate;
                    $acctrans->date = $now;
                    $acctrans->save();

                    $eritems = 0;
                    $pritems = 0;
                    foreach ($pitems as $key => $item) {
                        $part = Part::find($item->part_id);
                        $part_purchase_item  = new PartPurchaseItem();
                        $part_purchase_item->part_purchase_id = $purchase->id;
                        $part_purchase_item->part_id = $part->id;
                        $part_purchase_item->part_category_id = $part->part_category_id;
                        $part_purchase_item->pp_qty = $item->pp_qty;
                        $part_purchase_item->unit_price = $item->unit_price;
                        $part_purchase_item->total_price = $item->total_price;
                        $part_purchase_item->date = $now;
                        $part_purchase_item->save();

                        $total_amount += $item->total_price;

                        $part->av_qty += $item->pp_qty;
                        $part->save();
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
                        
                        $payacctrans = new VendorTransaction();
                        $payacctrans->company_id = $company->id;
                        $payacctrans->user_id = $user->id;
                        $payacctrans->vendor_id = $purchase->vendor_id;
                        $payacctrans->pv_no = $pvno;
                        $payacctrans->payment = $amount_paid;
                        $payacctrans->currency = $purchasetemp->currency;
                        $payacctrans->defcurr = $purchasetemp->defcurr;
                        $payacctrans->ex_rate = $purchasetemp->ex_rate;
                        $payacctrans->payment_mode = $pay_mode;
                        $payacctrans->date = $now;
                        $payacctrans->save();

                        $payment = new PartPurchasePayment();
                        $payment->company_id = $company->id;
                        $payment->part_purchase_id = $purchase->id;
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
                            $astmt->shop_id = Session::get('shop_id');
                            $astmt->user_id = $user->id;
                            $astmt->vendor_transaction_id = $payacctrans->id;
                            $astmt->account_id = $account->id;
                            $astmt->date = $now;
                            $astmt->debit = 0;
                            $astmt->credit = $amount_paid;
                            $astmt->description = 'Purchase Payments';
                            $astmt->save();
                        }
                    }else{
                        $utransactions = VendorTransaction::where('company_id', $company->id)->where('vendor_id', $purchase->vendor_id)->whereNotNull('pv_no')->where('is_utilized', false)->where('is_deleted', false)->get();

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
                                    $payment = PartPurchasePayment::create([
                                        'part_purchase_id' => $purchase->id,
                                        'company_id' => $company->id,
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
                    
                    if ($purchase->total_amount == $purchase->amount_paid) {
                        $purchase->status = 'Paid';
                        $purchase->save();
                    }elseif ($purchase->total_amount > $purchase->amount_paid && $purchase->amount_paid > 0) {
                        $purchase->status = 'Partially Paid';
                        $purchase->save();
                    }

                    $puritems = PartPurchaseItemTemp::where('part_purchase_temp_id', $purchasetemp->id)->get();
                    foreach ($puritems as $key => $value) {
                        $value->delete();
                    }

                    $purchasetemp->delete();
                    
                    return redirect('part-purchases')->with('success', 'Part Purchase part_purchase_item were added successfully');
                }
            }else{
                return redirect()->back()->with('warning', 'Please Select at least one part to continue!.');
            }
        }else{
            
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Purchase Details';
        $title = 'Purchase Details';
        $title_sw = 'Purchase Details';
        try {
            $company = Company::find(Session::get('company_id'));
            $shop = Shop::find(Session::get('shop_id'));
            $purchase = PartPurchase::where('part_purchases.id', decrypt($id))->join('users', 'users.id', '=', 'part_purchases.user_id')->select('part_purchases.id as id', 'vendor_id', 'pp_code', 'pp_date', 'first_name', 'last_name', 'total_amount', 'amount_paid', 'part_purchases.created_at as created_at')->first();
            $vendor = Vendor::find($purchase->vendor_id);

            $pitems = PartPurchaseItem::where('part_purchase_id', $purchase->id)->join('parts', 'parts.id', '=', 'part_purchase_items.part_id')->select('part_purchase_items.id as id', 'pp_qty', 'unit_price', 'total_price', 'date', 'part_purchase_items.created_at as created_at', 'part_no', 'part_name', 'uom')->orderBy('date', 'desc')->get();

            return view('vms.parts.purchases.show', compact('page', 'title', 'title_sw', 'company', 'shop', 'purchase', 'pitems', 'vendor'));

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
            return view('parts.purchases.edit', compact('page', 'title', 'title_sw', 'purchase', 'suppliers', 'shop'));
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

        $items = PartPurchaseItem::where('part_purchase_id', $purchase->id)->get();
        foreach ($items as $key => $stock) {
            $stock->stock_date = $purchase->time_created;
            $stock->save();
        }

        $acctrans = VendorTransaction::where('part_purchase_id', $purchase->id)->where('shop_id', $purchase->shop_id)->first();
        if (!is_null($acctrans) ) {
            $acctrans->supplier_id = $purchase->supplier_id;
            $acctrans->invoice_no = $purchase->invoice_no;
            $acctrans->save();
        }elseif (!empty($request['supplier_id'] && $purchase->purchase_type == 'credit')) {
            if ($shop->subscription_type_id <= 2) {
                $acctrans = VendorTransaction::where('part_purchase_id', $purchase->id)->first();
                if (is_null($acctrans)) {
                    $acctrans = new VendorTransaction();
                    $acctrans->shop_id = $shop->id;
                    $acctrans->user_id = $user->id;
                    $acctrans->supplier_id = $purchase->supplier_id;
                    $acctrans->part_purchase_id = $purchase->id;
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
        $company = Company::find(Session::get('company_id'));
        $user = Auth::user();
        $purchase = PartPurchase::find(decrypt($id));
        if (!is_null($purchase)) {
            $pitems = PartPurchaseItem::where('part_purchase_id', $purchase->id)->get();

            foreach ($pitems as $key => $value) {
                $value->is_deleted = true;
                $value->del_by = $user->first_name.'('.Carbon::now().')';
                $value->save();

                $part = Part::find($value->part_id);
                $part->av_qty -= $value->pp_qty;
                $part->save();
            }

            $payments = PurchasePayment::where('part_purchase_id', $purchase->id)->get();

            foreach ($payments as $key => $payment) {
                $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                if (!is_null($pv)) {
                    $acctrans = VendorTransaction::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
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

            $acctrans = VendorTransaction::where('part_purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
            if ($acctrans) {
                $acctrans->is_deleted = true;
                $acctrans->save();
                // $acctrans->delete();
            }
            
            $costitems = PurchaseCostItem::where('part_purchase_id', $purchase->id)->get();
            $total_cost = 0;
            foreach ($costitems as $key => $item) {
                $item->is_deleted = true;
                $item->save();
            }
            
            $purchase->is_deleted = true;
            $purchase->del_by = $user->first_name.' ('.Carbon::now().')';
            $purchase->save();
            // $purchase->delete();
            
            return redirect()->back()->with('success', 'Purchase was deleted successfully');
        }
    }

    public function deleteMultiple(Request $request)
    {
        $user = Auth::user();
        foreach ($request->input('ids') as $key => $id) {
            $purchase = PartPurchase::find($id);
            $pitems = PartPurchaseItem::where('part_purchase_id', $purchase->id)->get();

            foreach ($pitems as $key => $value) {
                $value->is_deleted = true;
                $value->del_by = $user->first_name.'('.Carbon::now().')';
                $value->save();

                $part = Part::find($value->part_id);
                $part->av_qty -= $value->pp_qty;
                $part->save();
            }

            $payments = PurchasePayment::where('part_purchase_id', $purchase->id)->get();

            foreach ($payments as $key => $payment) {
                $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                if (!is_null($pv)) {
                    $acctrans = VendorTransaction::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
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

            $acctrans = VendorTransaction::where('part_purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
            if ($acctrans) {
                $acctrans->is_deleted = true;
                $acctrans->save();
                // $acctrans->delete();
            }
            
            $costitems = PurchaseCostItem::where('part_purchase_id', $purchase->id)->get();
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
        $parts = $shop->parts()->get();

        $pitems = PartPurchaseItem::where('part_purchase_id', $purchase->id)->join('parts', 'parts.id', '=', 'stocks.part_id')->select('stocks.id as id', 'stocks.quantity_in as quantity_in', 'stocks.unit_cost as unit_cost', 'stocks.unit_cost as unit_cost', 'stocks.stock_date as stock_date', 'stocks.created_at as created_at', 'parts.slug as name', 'parts.basic_uom as basic_uom')->orderBy('time_created', 'desc')->get();
       
        $payments = PurchasePayment::where('part_purchase_id', $purchase->id)->get();

        $costitems = PurchaseCostItem::where('part_purchase_id', $purchase->id)->get();
        return view('parts.purchases.items', compact('page', 'title', 'title_sw', 'shop', 'purchase', 'pitems', 'supplier', 'payments', 'costitems', 'parts'));
    }

    public function addItem(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $purchase = Purchase::find($request['part_purchase_id']);
        if (!is_null($purchase)) {
            $part = part::find($request['part_id']);
            if (!is_null($part)) {
                $stock  = new PartPurchaseItem();
                $stock->part_id = $part->id;
                $stock->part_purchase_id = $purchase->id;
                $stock->shop_id = $shop->id;
                $stock->quantity_in = $request['quantity_in'];
                $stock->unit_cost = $request['unit_cost'];
                if ($shop->business_type_id == 1) {
                    $stock->source = 'partion Batch No. '.$purchase->grn_no;
                }else{
                    $stock->source = 'Purchased';
                }
                
                $stock->stock_date = $purchase->time_created;
                $stock->save();

                $pitems = PartPurchaseItem::where('part_purchase_id', $purchase->id)->get();
                $total_amount = 0;
                foreach ($pitems as $key => $item) {
                    $total_amount += ($item->quantity_in*$item->unit_cost);
                }

                $purchase->total_amount = $total_amount;
                $purchase->save();


                $acctrans = VendorTransaction::where('part_purchase_id', $purchase->id)->first();
                if (!is_null($acctrans) ) {
                    $acctrans->amount = $purchase->total_amount;
                    $acctrans->save();
                }
            }       
        }

        return redirect()->back()->with('success', 'Item Was Added successfully');
    }
}
