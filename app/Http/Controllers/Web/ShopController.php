<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Session;
use File;
use Auth;
use App\Models\Company;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\SubscriptionType;
use App\Models\BusinessType;
use App\Models\BusinessSubType;
use App\Models\User;
use App\Models\Payment;
use App\Models\BankDetail;
use App\Models\Supplier;
use App\Models\Account;
use App\Jobs\DailyClosingStockJob;
use App\Jobs\MonthlyBalanceSheetJob;
use App\Jobs\BasicBalanceSheetJob;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Shops';
        $title = 'My Businesses, Shops & Warehouses';
        $shops = Auth::user()->shops()->join('companies', 'companies.id', '=', 'shops.company_id')->select('shops.id as id', 'shops.name as name', 'is_warehouse', 'companies.name as company', 'shops.created_at as created_at')->get();

        return view('account.companies.shops', compact('page', 'title', 'shops'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'New Shop/Business';
        $btypes = BusinessType::all();
        $bsubtypes = BusinessSubType::all();
        $stype = SubscriptionType::find(1);
        return view('account.create', compact('page', 'btypes', 'bsubtypes', 'stype'));
    }

    public function addStore()
    {
        $page = 'New Warehouse/Store';
        return view('account.add-store', compact('page'));
    }


    public function postStore(Request $request)
    {
        $user = Auth::user();
        $parentshop = Shop::find($request['parent_shop_id']);
        if (!is_null($parentshop)) {
            $company = Company::find($parentshop->company_id);
            if (!is_null($company)) {
                    
                $shop = new Shop();
                $shop->company_id = $company->id;
                $shop->suid = 'SM-'.$this->unique_code(16);
                $shop->name = $request['shop_name'];
                $shop->business_type_id = $request['business_type_id'];
                $shop->subscription_type_id = $request['subscription_type_id'];
                $shop->business_sub_type_id = $request['business_sub_type_id'];
                $shop->is_warehouse = $request['is_warehouse'];
                if ($shop->is_warehouse) {
                    $shop->parent_shop_id = $request['parent_shop_id'];
                }
                $shop->save();

                if ($user->shops()->count() == 0) {
                    Session::put('shop_id', $shop->id);
                    $user->shops()->attach($shop, ['is_owner' => true, 'is_default' => true]);
                }else{
                    $user->shops()->attach($shop, ['is_owner' => true]);
                }
                $setting = Setting::create([
                    'shop_id' => $shop->id,
                    'tax_rate' => 18,
                    'inv_no_type' => 'Automatic'
                ]);

                $setting->discount_by_percent = false;
                $setting->show_discounts = false;
                $setting->save();

                if ($company->shops()->count() == 1) {
                    $code = $this->generatePIN(6);        
                    $payment = new Payment();
                    $payment->reference = 'SM_'.time();
                    $payment->req_uid = time();
                    $payment->code = $code;        
                    $payment->phone_number = $user->phone;
                    $payment->amount_paid = 0;
                    $payment->save();

                    if ($payment) { 
                        $expire_date = \Carbon\Carbon::now()->addDays(30);
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = 'Trial Days';
                        $payment->is_expired = false;
                        $payment->expire_date = $expire_date;
                        $payment->save();
                    }
                }
                
                $supplier = Supplier::create([
                    'name' => 'Unknown',
                    'shop_id' => $shop->id,
                    'supp_id' =>  1, 
                    'supplier_for' => 'Stock',
                    'time_created' => \Carbon\Carbon::now()
                ]);

                $supplier = Supplier::create([
                    'name' => 'Unknown',
                    'shop_id' => $shop->id,
                    'supp_id' =>  2, 
                    'supplier_for' => 'Expense',
                    'time_created' => \Carbon\Carbon::now()
                ]);

                $acc = new Account();
                $acc->shop_id = $shop->id;
                $acc->type = 'Cash';
                $acc->account_name = 'CASH DRAWER';
                $acc->save();

                $message = 'Your Shop '.$shop->name.' added to '.$company->name.'  successfully';
                
                return redirect('shops')->with('success', $message);
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
        $user = Auth::user();
        $company = Company::find($request['company_id']);
        if (!is_null($company)) {
                
            $shop = new Shop();
            $shop->company_id = $company->id;
            $shop->suid = 'SM-'.$this->unique_code(16);
            $shop->name = $request['shop_name'];
            $shop->business_type_id = $request['business_type_id'];
            $shop->subscription_type_id = $request['subscription_type_id'];
            $shop->business_sub_type_id = $request['business_sub_type_id'];
            $shop->is_warehouse = $request['is_warehouse'];
            if ($shop->is_warehouse) {
                $shop->parent_shop_id = $request['parent_shop_id'];
            }
            $shop->save();

            if ($user->shops()->count() == 0) {
                Session::put('shop_id', $shop->id);
                $user->shops()->attach($shop, ['is_owner' => true, 'is_default' => true]);
            }else{
                $user->shops()->attach($shop, ['is_owner' => true]);
            }
            $setting = Setting::create([
                'shop_id' => $shop->id,
                'tax_rate' => 18,
                'inv_no_type' => 'Automatic'
            ]);
            
            $setting->discount_by_percent = false;
            $setting->show_discounts = false;
            $setting->save();
                

            if ($company->shops()->count() == 1) {
                $shop->is_hq = true;
                $shop->save();

                $code = $this->generatePIN(6);        
                $payment = new Payment();
                $payment->reference = 'SM_'.time();
                $payment->req_uid = time();
                $payment->code = $code;        
                $payment->phone_number = $user->phone;
                $payment->amount_paid = 0;
                $payment->save();

                $expire_date = \Carbon\Carbon::now()->addDays(30);
                $payment->user_id = $user->id;
                $payment->shop_id = $shop->id;
                $payment->period = 'Trial Days';
                $payment->is_expired = false;
                $payment->expire_date = $expire_date;
                $payment->save();
            }

            $supplier = Supplier::create([
                'name' => 'Unknown',
                'shop_id' => $shop->id,
                'supp_id' =>  1, 
                'supplier_for' => 'Stock',
                'time_created' => \Carbon\Carbon::now()
            ]);

            $supplier = Supplier::create([
                'name' => 'Unknown',
                'shop_id' => $shop->id,
                'supp_id' =>  2, 
                'supplier_for' => 'Expense',
                'time_created' => \Carbon\Carbon::now()
            ]);

            $acc = new Account();
            $acc->shop_id = $shop->id;
            $acc->type = 'Cash';
            $acc->account_name = 'CASH DRAWER';
            $acc->save();

            if (!$shop->is_warehouse && $request['create_warehouse'] == 1) {
                $shopwh = new Shop();
                $shopwh->company_id = $company->id;
                $shopwh->suid = 'SM-'.$this->unique_code(16);
                $shopwh->name = $request['shop_name'].' MAIN STORE';
                $shopwh->business_type_id = 2;
                $shopwh->subscription_type_id = $request['subscription_type_id'];
                $shopwh->business_sub_type_id = $request['business_sub_type_id'];
                $shopwh->is_warehouse = true;
                $shopwh->parent_shop_id = $shop->id;
                $shopwh->is_default_warehouse = true;
                $shopwh->save();

                $user->shops()->attach($shopwh, ['is_owner' => true]);
                $setting = Setting::create([
                    'shop_id' => $shopwh->id,
                    'tax_rate' => 18,
                    'inv_no_type' => 'Automatic'
                ]);

                $setting->discount_by_percent = false;
                $setting->save();
                
                $acc = new Account();
                $acc->shop_id = $shopwh->id;
                $acc->type = 'Cash';
                $acc->account_name = 'CASH DRAWER';
                $acc->save();
            }
            
            $message = 'Your Shop '.$shop->name.' added to '.$company->name.'  successfully';
            
            return redirect('shops')->with('success', $message);
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
        $shop = Shop::find(decrypt($id));
        $page = 'Shop details';
        $title = 'Business details';
        $title_sw = 'Taarifa za biashara';
        $btype = BusinessType::find($shop->business_type_id);
        $bstype = BusinesssubType::find($shop->business_sub_type_id);
        $btypes = BusinessType::all();
        $bankdetails = $shop->bankDetails()->get();

        return view('account.show', compact('page', 'title', 'title_sw', 'shop', 'btype', 'btypes', 'bankdetails' , 'bstype'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        Session::forget('shop_name');
        $shop = Shop::find(decrypt($id));
        $shop->name = $request['shop_name'];
        $shop->tin = $request['tin'];
        $shop->vrn = $request['vrn'];
        $shop->tel = $request['tel'];
        $shop->mobile = $request['mobile'];
        $shop->whatsapp = $request['whatsapp'];
        $shop->email = $request['email'];
        $shop->postal_address = $request['postal_address'];
        $shop->physical_address = $request['physical_address'];
        $shop->street = $request['street'];
        $shop->district = $request['district'];
        $shop->city = $request['city'];
        $shop->country = $request['country'];
        $shop->short_desc = $request['short_desc'];
        $shop->website = $request['website'];
        $shop->is_warehouse = $request['is_warehouse'];

        if ($request['is_hq']) {
            $hq = Shop::where('company_id', $shop->company_id)->where('is_hq', true)->first();
            if (!is_null($hq)) {
                if ($hq->id != $shop->id) {
                    $hq->is_hq = false;
                    $hq->save();
                }
            }
        }
        $shop->is_hq = $request['is_hq'];

        $location = null;
        if ($request->hasFile('image')) {
            //  Let's do everything here
            if ($request->file('image')->isValid()) {
                //
                $validated = $request->validate([
                    'image' => 'mimes:jpeg,png|max:1014',
                ]);

                $logo_path = storage_path('/public/logos/'.$shop->logo_location);
                if (File::exists($logo_path)) {
                    unlink($logo_path);
                }

                $extension = $request->image->extension();
                $request->image->storeAs('/public/logos', $shop->id.'_logo.'.$extension);
                $location = $shop->id.'_logo.'.$extension;
            }
        }else{
            $location = $shop->logo_location;
        }
        
        $shop->logo_location = $location;
        $shop->save();

        Session::put('shop_name', $shop->name);
        $message = 'Shop information was successfully updated';

        // return $request['logo'];
        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shop = Shop::find(decrypt($id));
        $user = Auth::user();

        $usershop = $user->shops()->where('shop_id', $shop->id)->first();
        if (!is_null($usershop) && !$usershop->pivot->is_default) {
            $currshop = Shop::find(Session::get('shop_id'));

            if ($shop->id != $currshop->id) {
                $user->shops()->detach($shop);
            }
            return redirect()->back()->with('success', 'Your Business was successfully removed');
        }else{
            return redirect()->back()->with('info', 'You can not remove default business Account.');
        }
    }

    public function switchShop(Request $request)
    {

        $shop = Shop::find($request['shop_id']);
        if (!is_null($shop)) {
                
            Session::forget('shop_id');
            $user = Auth::user();
            $shops = $user->shops()->get();
            foreach ($shops as $key => $mshop) {
                $mshop->pivot->is_default = 0;
                $mshop->pivot->save();
            }

            $ushops = $user->shops()->where('shop_id', $shop->id)->first();
            $ushops->pivot->is_default = 1;
            $ushops->pivot->save();

            Session::put('shop_id', $shop->id);


            $company = Company::find($shop->company_id);
            if ($company->id != Session::get('company_id')) {
                $this->switchCompany($company);
            }

            dispatch(new DailyClosingStockJob($shop));
            dispatch(new MonthlyBalanceSheetJob($shop));
            dispatch(new BasicBalanceSheetJob($shop));

            $message = 'You have switched shop to '.$shop->name;
            if ($shop->is_warehouse) {
                return redirect('products')->with('success', $message);
            }else{
                return redirect('/'.Auth::user()->default_page)->with('success', $message);
            }
        }else{
            return redirect()->back()->with('warning', 'Shop not found');
        }
    }


    public function switchCompany($company)
    {
        if (!is_null($company)) {
            $user = Auth::user();
            $uc = $user->companies()->where('company_id', $company->id)->first();
            if (!is_null($uc)) {
                Session::forget('company_id');
                $companies = $user->companies()->get();
                foreach ($companies as $key => $comp) {
                    $comp->pivot->is_default = 0;
                    $comp->pivot->save();
                }

                
                $uc->pivot->is_default = 1;
                $uc->pivot->save();

                Session::put('company_id', $company->id);
                $message = 'You have switched Company to '.$company->name;
                return redirect('/'.Auth::user()->default_page)->with('success', $message);
            }else{
                return redirect()->back()->with('error', 'You have no access to selected company');
            }
        }else{
            return redirect()->back()->with('warning', 'Company no found');
        }
    }


    public function notifications()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $notifications = [];
        if (!is_null($shop)) {
            $notifications = $shop->unreadNotifications()->orderBy('created_at', 'asc')->limit(5)->get()->toArray();
        }

        return json_encode($notifications);
    }

    public function markAsRead()
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $shop->unreadNotifications->markAsRead();
        }

        return redirect()->back();
    }

    private function unique_code($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }

    public function generatePIN($digits = 4)
    {
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while($i < $digits){
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }
}
