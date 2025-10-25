<?php

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Depreciation;
use App\Models\AssetRecord;

class DepreciationController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Asset Depreciations';
        $title = 'Asset Depreciations';
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $currency = ShopCurrency::where('shop_id', $shop->id)->first()->code;
        $year = Carbon::now()->format('Y');
        if (!empty($request['year'])) {
            $year = $request['year'];
        }
        $years = range(Carbon::now()->year, 2017);
        $assets = AssetRecord::where('company_id', $company->id)->select('id', 'asset_name', 'asset_number')->get();
        $depreciations = Depreciation::join('asset_records', 'asset_records.id', '=', 'depreciations.asset_record_id')->where('company_id', $company->id)->get();
        return view('accounting.assets.depreciations.index', compact('page', 'title', 'company', 'currency', 'depreciations', 'year', 'years', 'assets'));
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
        $asset = AssetRecord::find($request['asset_record_id']);
        $latestdep = Depreciation::where('year', $request['year']-1)->first();
        $value_begin_yr = 0;
        $dep_amount = 0;
        $value_end_yr = 0;
        if (!is_null($latestdep)) {
            $value_begin_yr = $latestdep->value_end_yr;
        }

        if ($asset->dep_method == 'SL') {
            $dep_amount = (($asset->acquisition_cost-$asset->salvage_value)/$asset->useful_life)*($asset->first_year/100);
        }elseif ($asset->dep_method == "150% DDB") {
            if ($value_begin_yr > 0) {
                $dep_amount = (($asset->acquisition_cost-$asset->salvage_value-$value_begin_yr)/$asset->useful_life)*1.5*($asset->first_year/100);
            }else{
                $dep_amount = (($asset->acquisition_cost-$asset->salvage_value)/$asset->useful_life)*($asset->first_year/100);
            }
        }else{
            if ($value_begin_yr > 0) {
                $dep_amount = (($asset->acquisition_cost-$asset->salvage_value-$value_begin_yr)/$asset->useful_life)*2*($asset->first_year/100);
            }else{
                $dep_amount = (($asset->acquisition_cost-$asset->salvage_value)/$asset->useful_life)*($asset->first_year/100);
            }
        }
        
        if ($value_begin_yr > 0) {
            $value_begin_yr = $latestdep->value_end_yr;
            $asset_value = $value_begin_yr-$dep_amount;
        }else{
            $value_begin_yr = $asset->acquisition_cost;
            $asset_value = $asset->acquisition_cost-$dep_amount;
        }

        $depreciation = new Depreciation();
        $depreciation->asset_record_id = $asset->id;
        $depreciation->year = $request['year'];
        $depreciation->value_begin_yr = $value_begin_yr;
        $depreciation->dep_amount = $dep_amount;
        $depreciation->value_end_yr = $asset_value;
        $depreciation->save();

        return redirect('depreciations')->with('success', 'Depreciation added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Depreciation';
        $title = 'Edit Depreciation';
        $depreciation = Depreciation::find(decrypt($id));

        $years = range(Carbon::now()->year, 2017);
        $company = Company::find(Session::get('company_id'));
        $assets = AssetRecord::where('company_id', $company->id)->select('id', 'asset_name', 'asset_number')->get();

        return view('accounting.assets.depreciations.edit', compact('page', 'title', 'company', 'depreciation', 'years', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $depreciation = Depreciation::find(decrypt($id));
        $asset = AssetRecord::find($request['asset_record_id']);
        $latestdep = Depreciation::where('year', $request['year']-1)->first();
        $value_begin_yr = 0;
        $dep_amount = 0;
        $value_end_yr = 0;
        if (!is_null($latestdep)) {
            $value_begin_yr = $latestdep->value_end_yr;
        }

        if ($asset->dep_method == 'SL') {
            $dep_amount = (($asset->acquisition_cost-$asset->salvage_value)/$asset->useful_life)*($asset->first_year/100);
        }elseif ($asset->dep_method == "150% DDB") {
            if ($value_begin_yr > 0) {
                $dep_amount = (($asset->acquisition_cost-$asset->salvage_value-$value_begin_yr)/$asset->useful_life)*1.5*($asset->first_year/100);
            }else{
                $dep_amount = (($asset->acquisition_cost-$asset->salvage_value)/$asset->useful_life)*($asset->first_year/100);
            }
        }else{
            if ($value_begin_yr > 0) {
                $dep_amount = (($asset->acquisition_cost-$asset->salvage_value-$value_begin_yr)/$asset->useful_life)*2*($asset->first_year/100);
            }else{
                $dep_amount = (($asset->acquisition_cost-$asset->salvage_value)/$asset->useful_life)*($asset->first_year/100);
            }
        }
        
        if ($value_begin_yr > 0) {
            $value_begin_yr = $latestdep->value_end_yr;
            $asset_value = $value_begin_yr-$dep_amount;
        }else{
            $value_begin_yr = $asset->acquisition_cost;
            $asset_value = $asset->acquisition_cost-$dep_amount;
        }

        $depreciation->asset_record_id = $asset->id;
        $depreciation->year = $request['year'];
        $depreciation->value_begin_yr = $value_begin_yr;
        $depreciation->dep_amount = $dep_amount;
        $depreciation->value_end_yr = $asset_value;
        $depreciation->save();

        return redirect('depreciations')->with('success', 'Depreciation updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
