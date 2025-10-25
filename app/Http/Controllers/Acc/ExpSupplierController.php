<?php

namespace App\Http\Controllers\Acc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Supplier;
use App\Models\ExpSupplierTransaction;
use App\Models\SmsAccount;
use App\Models\Purchase;
use App\Models\Setting;
use App\Models\Expense;
use App\Models\BankDetail;
use App\Models\Account;

class ExpSupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Expense')->get();

        $page = 'Suppliers';
        $title = 'My Suppliers';
        $title_sw = 'Wauzaji Wangu';
        return view('accounting.expenses.suppliers.index', compact('page', 'title', 'title_sw', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $supplier = Supplier::where('name', $request['name'])->where('supplier_for', $request['supplier_for'])->where('shop_id' , $shop->id)->first();
        if (!is_null($supplier)) {
            return redirect()->back()->with('info', 'This Supplier has been added earlier');   
        }else{
            $supp_id = Supplier::where('shop_id' , $shop->id)->get()->max('supp_id');
            $supplier = Supplier::create([
                'name' => $request['name'],
                'shop_id' => $shop->id,
                'supp_id' =>  !is_null($supp_id) & ($supp_id > 1) ? $supp_id+1 : 1 , 
                'contact_no' => $request['contact_no'],
                'email' => $request['email'],
                'address' => $request['address'],
                'country_code' => $request['phone_country'],
                'supplier_for' => $request['supplier_for'],
                'time_created' => Carbon::now()
            ]);
            return redirect()->back()->with('message', 'Your Supplier was added successfully.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id , Request $request)
    {
        $page = 'Supplier Account Statement';
        $title = 'Supplier Account Statement';
        $title_sw = 'Taarifa ya Akaunti ya Supplier';
        $shop = Shop::find(Session::get('shop_id'));
        $supplier = Supplier::find(decrypt($id));
        $accounts = Account::where('shop_id', $shop->id)->get();

        $now = Carbon::now();
        $ftrans = ExpSupplierTransaction::where('shop_id', $shop->id)->where('supplier_id', $supplier->id)->orderBy('id', 'asc')->first();
        $sdate = date('Y-m-d', strtotime($supplier->created_at)).' 00:00:00';
        if (!is_null($ftrans)) {
            $sdate = $ftrans->date.' 00:00:00';
        }

        $start = $sdate;
        $end = \Carbon\Carbon::now();
        $start_date = $start;            
        $end_date = $end->format('Y-m-d');
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        $transactions = ExpSupplierTransaction::where('supplier_id', $supplier->id)->where('is_deleted', false)->whereBetween('created_at', [$start, $end])->orderBy('date', 'asc')->get();
        $invtrans = ExpSupplierTransaction::where('supplier_id', $supplier->id)->where('is_deleted', false)->where('amount', '>', 0)->whereBetween('created_at', [$start, $end])->orderBy('date', 'asc')->get();
        $payments = ExpSupplierTransaction::where('payment', '!=', null)->where('supplier_id', $supplier->id)->where('is_deleted', false)->whereBetween('created_at', [$start, $end])->orderBy('date', 'desc')->get();
        $purchases = Expense::where('shop_id', $shop->id)->where('supplier_id', $supplier->id)->where('is_deleted', false)->where('status', 'Pending')->orderBy('time_created', 'desc')->get();
        $obal = ExpSupplierTransaction::where('supplier_id', $supplier->id)->where('is_ob', true)->first();

        $settings = Setting::where('shop_id', $shop->id)->first();
        $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
        $senderids = null;
        if (!is_null($smsacc)) {
            $senderids = $smsacc->senderIds()->get();
        }

        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('accounting.expenses.suppliers.show', compact('page', 'title', 'title_sw', 'shop', 'accounts', 'transactions', 'payments', 'purchases', 'supplier', 'is_post_query', 'duration', 'duration_sw', 'start_date', 'end_date', 'start', 'end', 'reporttime', 'settings', 'obal', 'invtrans', 'senderids', 'defcurr', 'currencies')); 
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Supplier';
        $title = 'Edit Supplier Info';
        $title_sw = 'Hariri tarifa za Muuzaji';
        $supplier = Supplier::find(decrypt($id));

        return view('products.suppliers.edit', compact('page', 'title', 'title_sw', 'supplier'));
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
        $supplier = Supplier::find(decrypt($id));
        $supplier->name = $request['name'];
        $supplier->contact_no = $request['contact_no'];
        $supplier->email = $request['email'];
        $supplier->address = $request['address'];
        $supplier->save();

        return redirect('suppliers')->with('message', 'Your Supplier was updated successfully.');
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
        $supplier = Supplier::find(decrypt($id));
        if (!is_null($supplier)) {
            $expenses = Expense::where('supplier_id', $supplier->id)->count();
            $purchases = Purchase::where('supplier_id', $supplier->id)->count();
            if ($expenses > 0) {
                return redirect()->back()->with('info', 'Supplier has related recods in Expenses so cannot be deleted');
            }elseif ($purchases > 0) {
                return redirect()->back()->with('info', 'Supplier has related recods in Purchases so cannot be deleted');
            }else{
                $supplier->delete();
                return redirect()->back()->with('success', 'supplier deleted successfully');
            }
        }
    }
}
