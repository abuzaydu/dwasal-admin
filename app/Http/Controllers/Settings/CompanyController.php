<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use File;
use App\Models\Company;
use App\Models\Shop;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'My Companies';
        $title = 'My Companies';

        $companies = Auth::user()->companies()->get();

        return view('account.companies.index', compact('page', 'title', 'companies'));
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
        $user = Auth::user();
        $company = new Company();
        $company->cuid = 'SME-'.$this->unique_code(16);
        $company->name = $request['name'];
        $company->slogan = $request['slogan'];
        $location = null;
        if ($request->hasFile('logo')) {
            //  Let's do everything here
            if ($request->file('logo')->isValid()) {
                //
                $validated = $request->validate([
                    'logo' => 'mimes:jpeg,png|max:1014',
                ]);

                $extension = $request->logo->extension();
                $request->logo->storeAs('/clogos', $company->id.'_logo.'.$extension);
                $location = $company->id.'_logo.'.$extension;
            }
        }
        $company->logo_url = $location;
        $company->save();

        $user->companies()->attach($company);

        return redirect('user-companies')->with('success', 'New Company Created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Company Details';
        $title = 'Company Details';
        $company = Company::find(decrypt($id));
        $shops = $company->shops()->get();
        $users = $company->users()->get();

        return view('account.companies.show', compact('page', 'title', 'company', 'shops', 'users'));
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
        $company = Company::find(decrypt($id));
        $company->name = $request['name'];
        $company->slogan = $request['slogan'];
        $location = null;
        if ($request->hasFile('logo')) {
            //  Let's do everything here
            if ($request->file('logo')->isValid()) {
                //
                $validated = $request->validate([
                    'image' => 'mimes:jpeg,png|max:1014',
                ]);

                $logo_path = storage_path('/clogos/'.$company->logo_url);
                if (File::exists($logo_path)) {
                    unlink($logo_path);
                }

                $extension = $request->logo->extension();
                $request->logo->storeAs('/clogos', $company->id.'_logo.'.$extension);
                $location = $company->id.'_logo.'.$extension;
            }
        }else{
            $location = $company->logo_url;
        }

        $banner_url = $company->banner_url;
        if ($request->hasFile('banner')) {
            //  Let's do everything here
            if ($request->file('banner')->isValid()) {
                //
                $validated = $request->validate([
                    'banner' => 'mimes:jpeg, jpg, png|max:1014',
                ]);

                $banner_path = storage_path('/banners/'.$company->banner_url);
                if (File::exists($banner_path)) {
                    unlink($banner_path);
                }

                $extension = $request->banner->extension();
                $request->banner->storeAs('/banners', $company->id.'_banner.'.$extension);
                $banner_url = $company->id.'_banner.'.$extension;
            }
        }

        $stamp = $company->stamp;
        if ($request->hasFile('stamp')) {
            //  Let's do everything here
            if ($request->file('stamp')->isValid()) {
                //
                $validated = $request->validate([
                    'stamp' => 'mimes:jpeg,jpg,png|max:1014',
                ]);

                $stamp_path = storage_path('/stamps/'.$company->stamp);
                if (File::exists($stamp_path)) {
                    unlink($stamp_path);
                }

                $extension = $request->stamp->extension();
                $request->stamp->storeAs('/stamps', $company->id.'_stamp.'.$extension);
                $stamp = $company->id.'_stamp.'.$extension;
            }
        }
        $company->logo_url = $location;
        $company->use_invoice_banner = $request['use_invoice_banner'];
        $company->banner_url = $banner_url;
        $company->stamp = $stamp;
        $company->save();

        return redirect('user-companies')->with('success', 'New Company Created successfully');
   }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Company::find(decrypt($id));
        if (!is_null($company)) {
            Auth::user()->companies()->detach($company);
        }

        return redirect()->back()->with('success', 'Company removed from your list successfully');
    }

    private function unique_code($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }

    public function switchCompany(Request $request)
    {
        $company = Company::find($request['company_id']);

        if (!is_null($company)) {
            Session::forget('company_id');
            $user = Auth::user();
            $companies = $user->companies()->get();
            foreach ($companies as $key => $comp) {
                $comp->pivot->is_default = 0;
                $comp->pivot->save();
            }

            $uc = $user->companies()->where('company_id', $company->id)->first();
            $uc->pivot->is_default = 1;
            $uc->pivot->save();

            $this->switchShop($company);

            Session::put('company_id', $company->id);
            $message = 'You have switched Company to '.$company->name;
            return redirect('/'.Auth::user()->default_page)->with('success', $message);
        }else{
            return redirect()->back()->with('warning', 'Company no found');
        }
    }

    public function switchShop($company)
    {            
        Session::forget('shop_id');
        $user = Auth::user();
        $shop = Auth::user()->shops()->where('company_id', $company->id)->first();;
        if (!is_null($shop)) {
            $shops = $user->shops()->get();
            foreach ($shops as $key => $mshop) {
                $mshop->pivot->is_default = 0;
                $mshop->pivot->save();
            }

            $shop->pivot->is_default = 1;
            $shop->pivot->save();
            Session::put('shop_id', $shop->id);

        }else{
            return redirect('user-profile')->with('info', 'No shop assign to your account in this company profile');
        }
    }

}