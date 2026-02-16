<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Shop;
use Auth;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Session;

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
    // public function store(Request $request)
    // {
    //     $user = Auth::user();
    //     $company = new Company();
    //     $company->cuid = 'SME-'.$this->unique_code(16);
    //     $company->name = $request['name'];
    //     $company->slogan = $request['slogan'];
    //     $location = null;
    //     if ($request->hasFile('logo')) {
    //         //  Let's do everything here
    //         if ($request->file('logo')->isValid()) {
    //             //
    //             $validated = $request->validate([
    //                 'logo' => 'mimes:jpeg,png|max:1014',
    //             ]);

    //             $extension = $request->logo->extension();
    //             $request->logo->storeAs('public/clogos', $company->id.'_logo.'.$extension);
    //             $location = $company->id.'_logo.'.$extension;
    //         }
    //     }
    //     $company->logo_url = $location;
    //     $company->save();

    //     $user->companies()->attach($company);

    //     return redirect('user-companies')->with('success', 'New Company Created successfully');
    // }

    public function store(Request $request)
{
    $user = Auth::user();
    $company = new Company();
    $company->cuid = 'SME-' . $this->unique_code(16);
    $company->name = $request['name'];
    $company->slogan = $request['slogan'];
    
    // 1. Save the company first to generate the ID
    $company->save();

    if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
        $request->validate([
            'logo' => 'mimes:jpeg,png|max:1014',
        ]);

        // 2. Now $company->id actually has a value (e.g., 14)
        $extension = $request->logo->extension();
        $filename = $company->id . '_logo.' . $extension;

        // 3. Store the file using the now-existing ID
        $request->logo->storeAs('clogos', $filename, 'public');

        // 4. Update the company record with the filename and save again
        $company->logo_url = $filename;
        $company->save();
    }

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
    // public function update(Request $request, string $id)
    // {
    //     $company = Company::find(decrypt($id));
    //     $company->name = $request['name'];
    //     $company->slogan = $request['slogan'];
    //     $location = null;
    //     if ($request->hasFile('logo')) {
    //         //  Let's do everything here
    //         if ($request->file('logo')->isValid()) {
    //             //
    //             $validated = $request->validate([
    //                 'image' => 'mimes:jpeg,png|max:1014',
    //             ]);

    //             $logo_path = storage_path('clogos/'.$company->logo_url,);
    //             if (File::exists($logo_path)) {
    //                 unlink($logo_path);
    //             }

    //             $extension = $request->logo->extension();
    //             $request->logo->storeAs('/clogos', $company->id.'_logo.'.$extension);
    //             $location = $company->id.'_logo.'.$extension;
    //         }
    //     }else{
    //         $location = $company->logo_url;
    //     }
    //     $company->logo_url = $location;
    //     $company->save();

    //     return redirect('user-companies')->with('success', 'New Company Created successfully');
    // }
    public function update(Request $request, string $id)
{
    $company = Company::find(decrypt($id));
    $company->name = $request['name'];
    $company->slogan = $request['slogan'];

    if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
        
        // 1. Validate (Note: Ensure the key matches your input name 'logo')
        $request->validate([
            'logo' => 'mimes:jpeg,png|max:1014',
        ]);

        // 2. Delete the old logo if it exists
        if ($company->logo_url) {
            // Use the Storage facade - it's cleaner and handles paths automatically
            if (Storage::disk('public')->exists('clogos/' . $company->logo_url)) {
                Storage::disk('public')->delete('clogos/' . $company->logo_url);
            }
        }

        // 3. Prepare new file info
        $extension = $request->logo->extension();
        $filename = $company->id . '_logo.' . $extension;

        // 4. Store using the 'public' disk (This puts it in storage/app/public/clogos)
        $request->logo->storeAs('clogos', $filename, 'public');

        // 5. Update the path in the database object
        $company->logo_url = $filename;
    }

    // If no new file is uploaded, $company->logo_url remains its old value
    $company->save();

    return redirect('user-companies')->with('success', 'Company updated successfully');
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