<?php

namespace App\Http\Controllers\VFD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\EfdmsItem;

class EfdmsItemController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Items';
        $title = 'Items';
        $title_sw = 'Bidhaa';
        $shop = Shop::find(Session::get('shop_id'));
        $items = EfdmsItem::where('shop_id', $shop->id)->orderBy('desc', 'asc')->get();
        return view('vfd.items.index', compact('page', 'title', 'title_sw', 'items'));
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
        $shop = Shop::find(Session::get('shop_id'));
        $item = new EfdmsItem();
        $item->shop_id = $shop->id;
        $item->desc = $request['desc'];
        $item->price = $request['price'];
        $item->save();

        return redirect('vfd-items')->with('success', 'Item was added successfully');
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
        $page = 'Edit Item';
        $title = 'Edit Item';
        $title_sw = 'Hariri Bidhaa';
        $item = EfdmsItem::find(decrypt($id));

        return view('vfd.items.edit', compact('page', 'title', 'title_sw', 'item'));
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
        $item = EfdmsItem::find(decrypt($id));
        $item->desc = $request['desc'];
        $item->price = $request['price'];
        $item->save();

        return redirect('vfd-items')->with('success', 'Item updated successful');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = EfdmsItem::find(decrypt($id));
        if (!is_null($item)) {
            $item->delete();
        }

        return redirect()->back()->with('success', 'Item was deleted successful');
    }

    public function deleteMultiple(Request $request)
    {
        foreach ($request->input('id') as $key => $id) {
            $item = EfdmsItem::find($id);
            if (!is_null($item)) {
                $item->delete();
            }
        } 
        return redirect()->back()->with('success', 'Item was deleted successful');
    }
}
