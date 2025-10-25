<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\Setting;
use App\Models\RmPurchase;
use App\Models\RmSupplierTransaction;
use App\Models\RmItem;
use App\Models\RmUse;
use App\Models\RmUsedItem;
use App\Models\RmPurchaseItemTemp;
use App\Models\RmPurchasePayment;
use App\Models\RawMaterial;
use App\Models\PaymentVoucher;
use App\Models\Supplier;
use App\Models\RmDamage;
use App\Models\Payment;
use App\Models\ShopCurrency;
use App\Models\RmPurchaseTemp;
use App\Models\UnitMeasure;
use App\Models\Account;
use App\Models\AccountStatement;

class RmPurchaseController extends Controller
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
        $page = 'Raw Materials';
        $title = 'Raw Materials Purchases';
        $title_sw = 'Manunuzi ya Malighafi';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
        $rmpurchases = RmPurchase::where('rm_purchases.shop_id', $shop->id)->where('is_deleted' , false)->orderBy('date', 'desc')->join('suppliers', 'suppliers.id', '=', 'rm_purchases.supplier_id')->select('rm_purchases.id as id', 'name', 'grn_no', 'total_amount', 'amount_paid', 'date', 'rm_purchases.created_at as created_at')->get();
        
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Raw Materials')->get();
        return view('production.raw-materials.purchases.index', compact('page', 'title', 'title_sw', 'rmpurchases', 'suppliers', 'shop' , 'settings' , 'currencies' , 'defcurr' , 'dfcurr', 'bdetails'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'Raw Materials';
        $title = 'New Raw Material Purchase';
        $title_sw = 'Manunuzi Mapya ya Malighafi';
        $user = Auth::user();
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        $accounts = Account::where('shop_id', $shop->id)->get();

        if (is_null($dfcurr)) {
            return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
        }

        $rmtemp = RmPurchaseTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->first();
            
        if (is_null($rmtemp)) {
            $rmtemp = new RmPurchaseTemp();
            $rmtemp->shop_id = $shop->id;
            $rmtemp->user_id = $user->id;
            $rmtemp->currency = $dfcurr->code;
            $rmtemp->defcurr = $dfcurr->code;
            $rmtemp->save();
        }

        $units = UnitMeasure::select('unit_name')->get();
        return view('production.raw-materials.purchases.create', compact('page', 'title', 'title_sw', 'units', 'shop' , 'currencies' , 'dfcurr' , 'settings' , 'rmtemp'  , 'accounts'));
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
        $now = Carbon::now();
        $rmtemp = RmPurchaseTemp::find($request['rm_purchase_temp_id']);
        if (!empty($request['date'])) {
            $now = $request['date'];
        }

        $supplier_id = null;
        if (!is_null($rmtemp->supplier_id)) {
            $supplier_id = $rmtemp->supplier_id;
        }

        $ritems = RmPurchaseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();

        if ($ritems->count() > 0) {
            $temps = array();
            foreach ($ritems as $key => $value) {
                if ($value->qty == 0) {
                    array_push($temps, $value->qty);
                }
            }

            if (!empty($temps)) {
                return redirect()->back()->with('warning', 'Please update the quantity and Unit cost of each item to continue');
            }else{

                $max_no = RmPurchase::where('shop_id', $shop->id)->orderBy('grn_no', 'desc')->first();
                $grnno = 0;

                if (!is_null($max_no)) {
                    $grnno = $max_no->grn_no+1;
                }else{
                    $grnno = 1;
                }

                $total_amount = 0;
                $amount_paid = 0;
                
                $purchase = RmPurchase::create([
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'supplier_id' => $supplier_id,
                    'grn_no' => $grnno, 
                    'currency' => $rmtemp->currency,
                    'defcurr' => $rmtemp->defcurr,
                    'ex_rate' => $rmtemp->ex_rate,
                    'order_no' => $request['order_no'],
                    'delivery_note_no' => $request['delivery_note_no'],
                    'invoice_no' => $request['invoice_no'],
                    'total_amount' => $total_amount,
                    'amount_paid' => $amount_paid,
                    'comments' => $request['comments'],
                    'date' => $now,
                    'purchase_type' => $rmtemp->purchase_type,
                    'status' => $rmtemp->purchase_type == 'credit' ? 'Pending' : 'Paid'
                ]);

                foreach ($ritems as $key => $item) {
                    $raw_material = RawMaterial::find($item->raw_material_id);
                    $stock  = new RmItem;
                    $stock->rm_purchase_id = $purchase->id;
                    $stock->raw_material_id = $raw_material->id;
                    $stock->shop_id = $shop->id;
                    $stock->qty = $item->qty;
                    $stock->unit_cost = $item->unit_cost;
                    $stock->total = $item->total;
                    $stock->date = $now;
                    $stock->save();

                    $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();

                    if ($shop_raw_material->pivot->in_store == 0 || $shop_raw_material->pivot->in_store <= 1 ) {
                        $shop_raw_material->pivot->unit_cost = $item->unit_cost;
                        $shop_raw_material->pivot->save();
                    }

                    $purchased = RmItem::where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->where('shop_id', $shop->id)->sum('qty');           
                    $used = RmUse::where('raw_material_id', $raw_material->id)->where('rm_uses.shop_id', $shop->id)->where('is_deleted' , false)->join('rm_use_items' , 'rm_use_items.rm_use_id' , '=' , 'rm_uses.id')->sum('quantity');
                    $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');

                    $instore = $purchased-($used + $damaged); 
                    $shop_raw_material->pivot->in_store = $instore;
                    $shop_raw_material->pivot->save();
                    
                    $total_amount += $item->total;
                }
                
                if ($rmtemp->purchase_type == 'cash') {
                    $amount_paid = $total_amount;
                }else{
                    $amount_paid = 0;
                }

                $purchase->total_amount = $total_amount;
                $purchase->amount_paid = $amount_paid;
                $purchase->save();

                $acctrans = new RmSupplierTransaction();
                $acctrans->shop_id = $shop->id;
                $acctrans->user_id = $user->id;
                $acctrans->supplier_id = $supplier_id;
                $acctrans->rm_purchase_id = $purchase->id;
                $acctrans->invoice_no = $request['invoice_no'];
                $acctrans->amount = $total_amount;
                $acctrans->date = $now;
                $acctrans->save();

                if ($amount_paid > 0) {
                    $account = Account::find($request['account_id']);
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
                    $pv->account = $account->type;
                    $pv->voucher_for = 'Raw Materials Purchase';
                    $pv->save();

                    $payacctrans = new RmSupplierTransaction();
                    $payacctrans->shop_id = $shop->id;
                    $payacctrans->user_id = $user->id;
                    $payacctrans->supplier_id = $supplier_id;
                    $payacctrans->pv_no = $pvno;
                    $payacctrans->payment = $amount_paid;
                    $payacctrans->trans_invoice_amount = $amount_paid;
                    $payacctrans->payment_mode = $account->type;
                    $payacctrans->date = $now;
                    $payacctrans->save(); 

                    $payment = new RmPurchasePayment();
                    $payment->shop_id = $shop->id;
                    $payment->rm_purchase_id = $purchase->id;
                    $payment->rm_trans_id = $payacctrans->id;
                    $payment->pay_mode = $account->type;
                    $payment->pay_date = $now;
                    $payment->amount = $amount_paid;
                    $payment->pv_no = $pvno;
                    $payment->save();
                    
                    $astmt = new AccountStatement();
                    $astmt->shop_id = $shop->id;
                    $astmt->user_id = $user->id;
                    $astmt->rm_supplier_transaction_id = $payacctrans->id;
                    $astmt->account_id = $account->id;
                    $astmt->date = $now;
                    $astmt->debit = 0;
                    $astmt->credit = $amount_paid;
                    $astmt->description = 'Raw Material Purchase Payments';
                    $astmt->save();
                }

                $puritems = RmPurchaseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                foreach ($puritems as $key => $value) {
                    $value->delete();
                }

                $rmtemp->delete();

                return redirect()->back()->with('success', 'Raw Materials RmItems were added successfully');
            }
        }else{
            return redirect()->back()->with('warning', 'Please Select at least one Material Item to continue!.');
        }


    }

    public function purchaseGRN($id)
    {
        $page = 'Raw Materials';
        $title = 'Goods Received Note (GRN)';
        $title_sw = 'Bidhaa zilizopokelewa (GRN)';

        $shop = Shop::find(Session::get('shop_id'));
        $purchase = RmPurchase::where('id', decrypt($id))->where('is_deleted' , false)->first();
        $supplier = null;
        if (!is_null($purchase->supplier_id)) {
            $supplier = Supplier::find($purchase->supplier_id);
        }

        $ritems = RmItem::where('rm_purchase_id', $purchase->id)->where('rm_items.shop_id', $shop->id)->where('is_deleted' , false)->join('raw_materials', 'raw_materials.id', '=', 'rm_items.raw_material_id')->select('rm_items.id as id', 'rm_items.qty as qty', 'rm_items.unit_cost as unit_cost', 'rm_items.date as date', 'rm_items.created_at as created_at', 'raw_materials.name as name', 'raw_materials.basic_uom as basic_uom')->orderBy('date', 'desc')->get();
        
        return view('production.raw-materials.purchases.show', compact('page', 'title', 'title_sw', 'purchase', 'ritems', 'supplier', 'shop'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $page = 'Raw Materials';
        $title = 'Raw Material Purchase Details';
        $title_sw = 'Maelezo ya Manunuzi';

        $shop = Shop::find(Session::get('shop_id'));
        $purchase = RmPurchase::where('id', decrypt($id))->first();
        $supplier = null;
        if (!is_null($purchase->supplier_id)) {
            $supplier = Supplier::find($purchase->supplier_id);
        }

        $ritems = RmItem::where('rm_purchase_id', $purchase->id)->where('rm_items.shop_id', $shop->id)->where('is_deleted' , false)->join('raw_materials', 'raw_materials.id', '=', 'rm_items.raw_material_id')->select('rm_items.id as id', 'rm_items.qty as qty', 'rm_items.unit_cost as unit_cost', 'rm_items.date as date', 'rm_items.created_at as created_at', 'raw_materials.name as name', 'raw_materials.basic_uom as basic_uom')->orderBy('date', 'desc')->get();
        
        $payments = RmPurchasePayment::where('rm_purchase_id', $purchase->id)->get();

        return view('production.raw-materials.purchases.items', compact('page', 'title', 'title_sw', 'purchase', 'ritems', 'supplier', 'payments'));

        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Raw Materials';
        $title = 'Edit Purchase';
        $title_sw = 'Hariri Manunuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $purchase = RmPurchase::find(decrypt($id));
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Raw Materials')->get();
        return view('production.raw-materials.purchases.edit', compact('page', 'title', 'title_sw', 'purchase', 'suppliers'));
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
        $purchase = RmPurchase::find(decrypt($id));
        $purchase->supplier_id = $request['supplier_id'];
        $purchase->order_no = $request['order_no'];
        $purchase->delivery_note_no = $request['delivery_note_no'];
        $purchase->invoice_no = $request['invoice_no'];
        $purchase->comments = $request['comments'];
        $purchase->date = $request['date'];
        $purchase->save();

        $rmitems = RmItem::where('rm_purchase_id', $purchase->id)->get();
        foreach ($rmitems as $key => $value) {
            $value->date = $purchase->date;
            $value->save();
        }
        return redirect('rm-purchases')->with('success', 'Purchase was updated successfully');
    }

    public function updateTemp(Request $request , $id){
        $rmtemp = RmPurchasetemp::find($id);

        $local_ex_rate = 1;
        $foreign_ex_rate = 1;
        $ex_rate = 1;
        if ($request['currency'] != $rmtemp->defcurr) {
            if ($request['ex_rate_mode'] == 'Foreign') {
                $local_ex_rate = $request['local_ex_rate'];
                $ex_rate = 1/$local_ex_rate;
            }else{
                $foreign_ex_rate = $request['foreign_ex_rate'];
                $ex_rate = $foreign_ex_rate;
            }
        }

        $rmtemp->supplier_id = $request['supplier_id'];
        $rmtemp->date_set = $request['date_set'];
        $rmtemp->date = $request['date'];
        $rmtemp->purchase_type = $request['purchase_type'];
        $rmtemp->pay_type = $request['pay_type'];
        $rmtemp->currency = $request['currency'];
        $rmtemp->ex_rate_mode = $request['ex_rate_mode'];
        $rmtemp->local_ex_rate = $local_ex_rate;
        $rmtemp->foreign_ex_rate = $foreign_ex_rate;
        $rmtemp->ex_rate = $ex_rate;
        $rmtemp->due_date = $request['due_date'];
        $rmtemp->comments = $request['comments'];
        $rmtemp->grn_no = $request['grn_no'];
        $rmtemp->order_no  = $request['order_no'];
        $rmtemp->invoice_no  = $request['invoice_no']; 
        $rmtemp->delivery_note_no  = $request['delivery_note_no'];
        $rmtemp->save();

        return $rmtemp;
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
        $purchase = RmPurchase::where('id', decrypt($id))->where('shop_id', $shop->id)->first();

        $ritems = RmItem::where('rm_purchase_id', $purchase->id)->where('shop_id', $shop->id)->get();

        foreach ($ritems as $key => $value) {
            $value->is_deleted = true;
            $value->save();
            $raw_material = RawMaterial::find($value->raw_material_id);
            $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();

            $stock_in = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');
            $used = RmUse::where('raw_material_id', $raw_material->id)->where('rm_uses.shop_id', $shop->id)->where('is_deleted' , false)->join('rm_use_items' , 'rm_use_items.rm_use_id' , '=' , 'rm_uses.id')->sum('quantity');

            $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');


            $instock = ($stock_in - ($used + $damaged)); 
            if (!is_null($shop_raw_material)) {
                  $shop_raw_material->pivot->in_store = $instock;
                  $shop_raw_material->pivot->save();
            }  
            

            $message = 'Stock was successfully deleted';
        }

        $payments = RmPurchasePayment::where('rm_purchase_id', $purchase->id)->get();

        foreach ($payments as $key => $payment) {
            $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
            if (!is_null($pv)) {
                $acctrans = RmSupplierTransaction::where('pv_no', $pv->pv_no)->where('shop_id', $shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->delete();
                }
                $pv->delete();
            }
            
            $payment->delete();
        }

        $acctrans = RmSupplierTransaction::where('rm_purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
        if (!is_null($acctrans)) {
            $acctrans->delete();
        }
        
        $purchase->delete();

        return redirect()->back()->with('success', 'Purchase was deleted successfully');
    }

    public function deleteMultiple(Request $request)
    {
        
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        foreach ($request->input('ids') as $id) {
            $purchase = RmPurchase::find($id);
            if (!is_null($purchase)) {
                $ritems = RmItem::where('rm_purchase_id', $purchase->id)->get();

                foreach ($ritems as $key => $value) {
                    $value->is_deleted = true;
                    $value->save();
                    $raw_material = RawMaterial::find($value->raw_material_id);
                    $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();

                    $stock_in = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');
                    $used = RmUse::where('raw_material_id', $raw_material->id)->where('rm_uses.shop_id', $shop->id)->where('is_deleted' , false)->join('rm_use_items' , 'rm_use_items.rm_use_id' , '=' , 'rm_uses.id')->sum('quantity');

                    $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');


                    $instock = ($stock_in - ($used + $damaged)); 
                    if (!is_null($shop_raw_material)) {
                          $shop_raw_material->pivot->in_store = $instock;
                          $shop_raw_material->pivot->save();
                    }  
                    

                    $message = 'Stock was successfully deleted';
                }

                $payments = RmPurchasePayment::where('rm_purchase_id', $purchase->id)->get();

                foreach ($payments as $key => $payment) {
                    $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                    if (!is_null($pv)) {
                        $acctrans = RmSupplierTransaction::where('pv_no', $pv->pv_no)->where('shop_id', $shop->id)->first();
                        if (!is_null($acctrans)) {
                            $acctrans->delete();
                        }
                        $pv->delete();
                    }
                    
                    $payment->delete();
                }

                $acctrans = RmSupplierTransaction::where('rm_purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->delete();
                }
                
                $purchase->delete();
            }
        }
        return redirect()->back()->with('success', 'Purchase was deleted successfully');
    }

    public function cancel(){
        $shop = Shop::find(Session::get('shop_id'));
        $rmtemp =RmPurchaseTemp::where('shop_id' , $shop->id)->where('user_id' , Auth::id())->first();
        if (!is_null($rmtemp)) {
            $rms = RmPurchaseItemTemp::where('rm_purchase_temp_id', $rmtemp->id)->with('rawMaterial')->get();
            foreach ($rms as $key => $value) {
                $value->delete();
            }

            $rmtemp->delete();
        }

        return redirect()->back()->with('success', 'Purchase cancelled successfully');
    }
}
