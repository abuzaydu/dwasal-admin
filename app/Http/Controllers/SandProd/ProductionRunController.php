<?php

namespace App\Http\Controllers\SandProd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\WashingPlant;
use App\Models\StorageLocation;
use App\Models\ProductionRun;
use App\Models\Stock;
use App\Models\QualityTest;

class ProductionRunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Production Runs';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
        $is_post_query = false;
        
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }
        $duration = '';
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $plants = WashingPlant::where('shop_id', $shop->id)->select('id', 'plant_name')->get();
            $slocations = StorageLocation::where('shop_id', $shop->id)->where('storage_for', 'Raw Material')->select('id', 'location_name')->get();
            $prodruns = ProductionRun::where('production_runs.shop_id', $shop->id)->whereBetween('start_time', [$start, $end])->join('storage_locations', 'storage_locations.id', '=', 'production_runs.storage_location_id')->join('users', 'users.id', '=', 'production_runs.user_id')->join('washing_plants', 'washing_plants.id', '=', 'production_runs.washing_plant_id')->select('production_runs.id as id', 'plant_name', 'location_name', 'first_name', 'last_name', 'pr_no', 'start_time', 'end_time', 'input_quantity', 'output_quantity', 'waste_water_quantity')->get();

            return view('production.sand.prod-runs.index', compact('page', 'is_post_query', 'start_date', 'end_date', 'duration', 'plants', 'slocations', 'prodruns'));
        }else{
            return redirect('users-and-roles')->with('info', 'User has no default Branch/Shop assined. Please assin one');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Production Run';

        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $plants = WashingPlant::where('shop_id', $shop->id)->select('id', 'plant_name')->get();
            $slocations = StorageLocation::where('shop_id', $shop->id)->where('storage_for', 'Raw Material')->select('id', 'location_name')->get();

            return view('production.sand.prod-runs.create', compact('page', 'plants', 'slocations'));
        }else{
            return redirect('users-and-roles')->with('info', 'User has no default Branch/Shop assined. Please assin one');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $now = Carbon::now();
        $prefix = $now->format('ym');
        $lastprod = ProductionRun::where('shop_id', $shop->id)->where(\DB::raw('CONCAT_WS(" ", `pr_no`)'),'LIKE', '%'.$prefix.'%')->orderBy('pr_no', 'desc')->first();
        $prNo = '';
        if (!is_null($lastprod)) {
            $lastno = str_replace($prefix.'/', '', $lastprod->pr_no);
            $lastno = (int)$lastno;
            $prNo = $prefix.'/'.sprintf('%03d', $lastno+1);
        }else{
            $prNo = $prefix.'/'.sprintf('%03d', 1);
        }
        $prodrun = new ProductionRun();
        $prodrun->shop_id = $shop->id;
        $prodrun->user_id = Auth::user()->id;
        $prodrun->washing_plant_id = $request['washing_plant_id'];
        $prodrun->storage_location_id = $request['storage_location_id'];
        $prodrun->pr_no = $prNo;
        $prodrun->start_time = $request['start_time'];
        $prodrun->end_time = $request['end_time'];
        $prodrun->input_quantity = $request['input_quantity'];
        $prodrun->output_quantity = $request['output_quantity'];
        $prodrun->waste_water_quantity = $request['waste_water_quantity'];
        $prodrun->remarks = $request['remarks'];
        $prodrun->save();

        return redirect('sand-productions')->with('success', 'Production Run created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Production Run Details';
        $prodrun = ProductionRun::where('production_runs.id', decrypt($id))->join('storage_locations', 'storage_locations.id', '=', 'production_runs.storage_location_id')->join('users', 'users.id', '=', 'production_runs.user_id')->join('washing_plants', 'washing_plants.id', '=', 'production_runs.washing_plant_id')->select('production_runs.id as id', 'plant_name', 'location_name', 'first_name', 'last_name', 'pr_no', 'start_time', 'end_time', 'input_quantity', 'output_quantity', 'waste_water_quantity', 'status', 'remarks')->first();

        $qualitytests = QualityTest::where('production_run_id', $prodrun->id)->join('users', 'users.id', '=', 'quality_tests.user_id')->select('quality_tests.id as id', 'test_date', 'test_type', 'result', 'passed',  'first_name', 'last_name')->get();
        $stocks = Stock::where('production_run_id', $prodrun->id)->join('products', 'products.id', '=', 'stocks.product_id')->select('stocks.id as id', 'stock_date', 'name', 'quantity_in', 'source')->get();

        return view('production.sand.prod-runs.show', compact('page', 'prodrun', 'qualitytests', 'stocks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Production Run';
        $prodrun = ProductionRun::find(decrypt($id));
        if (!is_null($prodrun)) {
            $plants = WashingPlant::where('shop_id', $prodrun->shop_id)->select('id', 'plant_name')->get();
            $slocations = StorageLocation::where('shop_id', $prodrun->shop_id)->select('id', 'location_name')->get();

            return view('production.sand.prod-runs.edit', compact('page', 'prodrun', 'plants', 'slocations'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $prodrun = ProductionRun::find(decrypt($id));
        $prodrun->washing_plant_id = $request['washing_plant_id'];
        $prodrun->storage_location_id = $request['storage_location_id'];
        $prodrun->start_time = $request['start_time'];
        $prodrun->end_time = $request['end_time'];
        $prodrun->input_quantity = $request['input_quantity'];
        $prodrun->output_quantity = $request['output_quantity'];
        $prodrun->waste_water_quantity = $request['waste_water_quantity'];
        $prodrun->remarks = $request['remarks'];
        $prodrun->save();

        return redirect('sand-productions')->with('success', 'Production Run updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prodrun = ProductionRun::find(decrypt($id));
        if (!is_null($prodrun)) {
            
            $prodrun->delete();
        }
    }
}
