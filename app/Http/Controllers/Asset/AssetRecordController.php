<?php

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Company;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\DepreciationMethod;
use App\Models\AssetRecord;
use App\Models\Depreciation;

class AssetRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Asset Records';
        $title = 'Asset Records';
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $currency = ShopCurrency::where('shop_id', $shop->id)->first()->code;
        $assets = AssetRecord::where('company_id', $company->id)->get();
        $depmethods = DepreciationMethod::where('company_id', $company->id)->get();

        return view('accounting.assets.index', compact('page', 'title', 'currency', 'assets', 'depmethods'));
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
        $company = Company::find(Session::get('company_id'));
        $asset = new AssetRecord();
        $asset->company_id = $company->id;
        $asset->dep_method = $request['dep_method'];
        $asset->asset_name = $request['asset_name'];
        $asset->asset_class = $request['asset_class'];
        $asset->description = $request['description'];
        $asset->physical_location = $request['physical_location'];
        $asset->asset_number = $request['asset_number'];
        $asset->serial_no = $request['serial_no'];
        $asset->acquisition_date = $request['acquisition_date'];
        $asset->acquisition_cost = $request['acquisition_cost'];
        $asset->useful_life = $request['useful_life'];
        $asset->salvage_value = $request['salvage_value'];
        $asset->first_year = $request['first_year'];
        $asset->save();

        return redirect('asset-records')->with('success', 'Asset added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Asset Depreciations';
        $title = 'Asset Depreciations';
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $currency = ShopCurrency::where('shop_id', $shop->id)->first()->code;
        $asset = AssetRecord::find(decrypt($id));
        $depreciations = Depreciation::where('asset_record_id', $asset->id)->orderBy('year', 'asc')->get();

        return view('accounting.assets.show', compact('page', 'title', 'currency', 'asset', 'depreciations'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Asset Record';
        $title = 'Edit Asset Record';
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $currency = ShopCurrency::where('shop_id', $shop->id)->first()->code;
        $asset = AssetRecord::find(decrypt($id));
        $depmethods = DepreciationMethod::where('company_id', $company->id)->get();

        return view('accounting.assets.edit', compact('page', 'title', 'currency', 'asset', 'depmethods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $asset = AssetRecord::find(decrypt($id));
        $asset->dep_method = $request['dep_method'];
        $asset->asset_name = $request['asset_name'];
        $asset->asset_class = $request['asset_class'];
        $asset->description = $request['description'];
        $asset->physical_location = $request['physical_location'];
        $asset->asset_number = $request['asset_number'];
        $asset->serial_no = $request['serial_no'];
        $asset->acquisition_date = $request['acquisition_date'];
        $asset->acquisition_cost = $request['acquisition_cost'];
        $asset->useful_life = $request['useful_life'];
        $asset->salvage_value = $request['salvage_value'];
        $asset->first_year = $request['first_year'];
        $asset->save();

        return redirect('asset-records')->with('success', 'Asset updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $asset = AssetRecord::find(decrypt($id));
        if (!is_null($asset)) {
            $depreciations = Depreciation::where('asset_record_id', $asset->id)->count();
            if ($depreciations > 0) {
                return redirect()->back()->with('info', 'Asset cannot be deleted because has Depreciation records');
            }else{
                $asset->delete();
                return redirect()->back()->with('success', 'Asset deleted successfully');              
            }
        }
    }
}
