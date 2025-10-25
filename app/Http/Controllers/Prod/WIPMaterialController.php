<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\MaterialWip;

class WIPMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'WIP Materials';
        $title = 'WIP Materials';
        $shop = Shop::find(Session::get('shop_id'));
        $wipmaterials = MaterialWip::where('shop_id', $shop->id)->get();

        return view('production.wips.materials.index', compact('page', 'title', 'wipmaterials'));
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
        $mwip = new MaterialWip();
        $mwip->shop_id = $shop->id;
        $mwip->title = $request['title'];
        $mwip->save();

        return redirect('prod-wip-materials')->with('success', 'WIP Material create successfully');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
