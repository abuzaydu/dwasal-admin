<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\ProductionStage;
use App\Models\PpStage;

class ProductionStageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Product Production Stages';
        $title = 'Product Production Stages';
        $shop = Shop::find(Session::get('shop_id'));
        $stages = ProductionStage::where('shop_id', $shop->id)->get();
        $pstages = PpStage::where('pp_stages.shop_id', $shop->id)->join('products', 'products.id', '=', 'pp_stages.product_id')->join('production_stages', 'production_stages.id', '=', 'pp_stages.production_stage_id')->select('pp_stages.id as id', 'stage', 'name', 'is_wip_stage')->get();
        $products = $shop->products()->select('id', 'name')->get();

        return view('production.labour-costs.stages.index', compact('page', 'title', 'stages', 'pstages', 'products'));
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
        $shop = Shop::find(Session::get('shop_id'));
        $stage = new ProductionStage();
        $stage->shop_id = $shop->id;
        $stage->stage = $request['stage'];
        $stage->save();

        return redirect('production-stages')->with('success', 'Production Stage created successfully');
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
        $page = 'Edit Production Stage';
        $title = 'Edit Production Stage';
        $stage = ProductionStage::find(decrypt($id));
        return view('production.labour-costs.stages.edit', compact('page', 'title', 'stage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $stage = ProductionStage::find(decrypt($id));
        if (!is_null($stage)) {
            $stage->stage = $request['stage'];
            $stage->save(); 
        }

        return redirect('production-stages')->with('success', 'Stage updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stage = ProductionStage::find(decrypt($id));
        $pstages = PpStage::where('production_stage_id', $stage->id)->count();
        if ($pstages > 0) {
            return redirect('production-stages')->with('info', 'Stage cannot be removed because has products associated with');
        }else{
            $stage->delete();
            return redirect('production-stages')->with('success', 'Stage removed successfully');
        }
    }
}
