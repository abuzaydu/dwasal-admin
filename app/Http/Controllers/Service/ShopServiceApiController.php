<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Response;
use App\Models\Shop;

class ShopServiceApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $services = $shop->services()->select('id', 'name')->take(15)->get();

        return $services;
    }

    public function autoSearch(Request $request)
    {
        if ($request->ajax()) {
            $shop = Shop::find(Session::get('shop_id'));
            if (!empty($request->search_key)) {
                $data = $shop->services()->where('name', 'LIKE', '%'.$request->search_key.'%')->select('id', 'name')->get();

                return $data;
            }else{
                $services = $shop->services()->select('id', 'name')->take(15)->get();
                return $services;
            }
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
