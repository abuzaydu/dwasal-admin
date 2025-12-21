<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Validator;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Vendor;
use App\Models\VendorTransaction;
use App\Models\SmsAccount;
use App\Models\PartPurchase;
use App\Models\Setting;
use App\Models\Expense;
use App\Models\Account;
use App\Imports\SupplierImport;

class VendorController extends Controller
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
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = Company::find(Session::get('company_id')); 
        $vendors = Vendor::where('company_id', $company->id)->where('vendor_for', 'Parts')->get();
        $page = 'vendors';
        $title = 'My vendors';
        $title_sw = 'Wauzaji Wangu';
        return view('vms.vendors.index', compact('page', 'title', 'title_sw', 'vendors'));
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
        $company = Company::find(Session::get('company_id'));
        $vendor = Vendor::where('vendor_name', $request['name'])->where('vendor_for', $request['vendor_for'])->where('company_id' , $company->id)->first();
        if (!is_null($vendor)) {
            return redirect()->back()->with('info', 'This vendor has been added earlier');   
        }else{
            $supp_id = Vendor::where('company_id' , $company->id)->get()->max('supp_id');
            $vendor = new vendor();
            $vendor->company_id = $company->id;
            $vendor->vendor_name = $request['name'];
            $vendor->phone = $request['phone'];
            $vendor->email = $request['email'];
            $vendor->address = $request['address'];
            $vendor->vendor_for = $request['vendor_for'];
            $vendor->save();
            
            return redirect()->back()->with('message', 'Your vendor was added successfully.');
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
        $page = 'Reports';
        $title = 'vendor Account Statement';
        $title_sw = 'Taarifa ya Akaunti ya vendor';
        $vendor = Vendor::find(decrypt($id));
        if (!is_null($vendor)) {
            $company = Company::find($vendor->company_id);
            $shop = Shop::find(Session::get('shop_id'));
            $accounts = Account::where('shop_id', $shop->id)->get();

            $now = Carbon::now();
            $ftrans = VendorTransaction::where('company_id', $vendor->company_id)->where('vendor_id', $vendor->id)->orderBy('id', 'asc')->first();
            $sdate = date('Y-m-d', strtotime($vendor->created_at)).' 00:00:00';
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

            $transactions = VendorTransaction::where('vendor_id', $vendor->id)->where('is_deleted', false)->whereBetween('created_at', [$start, $end])->orderBy('date', 'asc')->get();
            $invtrans = VendorTransaction::where('vendor_id', $vendor->id)->where('is_deleted', false)->where('amount', '>', 0)->whereBetween('created_at', [$start, $end])->orderBy('date', 'asc')->get();
            $payments = VendorTransaction::where('payment', '!=', null)->where('vendor_id', $vendor->id)->where('is_deleted', false)->whereBetween('created_at', [$start, $end])->orderBy('date', 'desc')->get();
            $purchases = PartPurchase::where('company_id', $company->id)->where('vendor_id', $vendor->id)->whereRaw('(total_amount-amount_paid) > 0')->orderBy('pp_date', 'desc')->get();
            $obal = VendorTransaction::where('vendor_id', $vendor->id)->where('is_ob', true)->first();

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
            return view('vms.vendors.show', compact('page', 'title', 'title_sw', 'shop', 'accounts', 'transactions', 'payments', 'purchases', 'vendor', 'is_post_query', 'duration', 'duration_sw', 'start_date', 'end_date', 'start', 'end', 'reporttime', 'settings', 'obal', 'invtrans', 'senderids', 'defcurr', 'currencies')); 
        }else{
            return redirect()->back()->with('error', 'vendor not Found');
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
        $page = 'Edit vendor';
        $title = 'Edit vendor Info';
        $title_sw = 'Hariri tarifa za Muuzaji';
        $vendor = Vendor::find(decrypt($id));

        return view('vms.vendors.edit', compact('page', 'title', 'title_sw', 'vendor'));
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
        $company = Company::find(Session::get('company_id'));
        $vendor = Vendor::find(decrypt($id));
        $vendor->name = $request['name'];
        $vendor->phone = $request['phone'];
        $vendor->email = $request['email'];
        $vendor->address = $request['address'];
        $vendor->save();

        return redirect('vendors')->with('message', 'Your vendor was updated successfully.');
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
        $vendor = Vendor::find(decrypt($id));
        if (!is_null($vendor)) {
            $expenses = Expense::where('vendor_id', $vendor->id)->count();
            $purchases = Purchase::where('vendor_id', $vendor->id)->count();
            if ($expenses > 0) {
                return redirect()->back()->with('info', 'vendor has related recods in Expenses so cannot be deleted');
            }elseif ($purchases > 0) {
                return redirect()->back()->with('info', 'vendor has related recods in Purchases so cannot be deleted');
            }else{
                $vendor->delete();
                return redirect()->back()->with('success', 'vendor deleted successfully');
            }
        }
    }

    public function downloadSample()
    {
        return response()->download(public_path('sample-vendors.xlsx'));
    }


    public function import(Request $request) 
    {
         $rules = array(
            'file' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        // process the form
        if ($validator->fails()) 
        {
            return \Redirect::to('vendors')->withErrors($validator);
        }else{
            Excel::import(new vendorImport, request()->file('file'));
            return redirect('vendors')->with('success', 'vendors were imported successfully!');
        }
    }
}
