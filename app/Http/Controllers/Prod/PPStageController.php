<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\PpStage;
use App\Models\ProductionStage;

class PPStageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $pstage = new PpStage();
        $pstage->shop_id = $shop->id;
        $pstage->production_stage_id = $request['production_stage_id'];
        $pstage->is_wip_stage = $request['is_wip_stage'];
        $pstage->product_id = $request['product_id'];
        $pstage->save();

        return redirect('production-stages')->with('success', 'Production Stage created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
        $page = 'Edit Production Stage';
        $title = 'Edit Production Stage';
        $shop = Shop::find(Session::get('shop_id'));
        $stage = PpStage::find(decrypt($id));
        $stages = ProductionStage::where('shop_id', $shop->id)->get();
        $products = $shop->products()->select('id', 'name')->get();

        return view('production.labour-costs.stages.pp-stages.edit', compact('page', 'title', 'stage', 'stages', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pstage = PpStage::find(decrypt($id));
        $pstage->production_stage_id = $request['production_stage_id'];
        $pstage->is_wip_stage = $request['is_wip_stage'];
        $pstage->product_id = $request['product_id'];
        $pstage->save();

        return redirect('production-stages')->with('success', 'Production Stage updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        PpStage::destroy(decrypt($id));

        return redirect('production-stages')->with('success', 'Production Stage removed successfully');
    }
}
