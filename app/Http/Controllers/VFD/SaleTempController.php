<?php

namespace App\Http\Controllers\VFD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Response;
use Auth;
use App\Models\Shop;
use App\Models\EfdmsItem;
use App\Models\EfdmsItemTemp;
use App\Models\Taxcode;

class SaleTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(EfdmsItemTemp::where('shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->get());
    }

    public function getItems()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $items = EfdmsItem::where('shop_id', $shop->id)->get();
        return Response::json($items);
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
        $user = Auth::user();
        $sameitems = EfdmsItemTemp::where('item_code', $request['item_code'])->where('user_id', $user->id)->where('shop_id', $shop->id)->count();

        if ($sameitems == 0) {
            $item = EfdmsItem::find($request['item_code']);
            $itemtemp = new EfdmsItemTemp();
            $itemtemp->shop_id = $shop->id;
            $itemtemp->user_id = $user->id;
            $itemtemp->item_code = $item->id;
            $itemtemp->desc = $item->desc;
            $itemtemp->price = $item->price;
            $itemtemp->save();
        }else{
            $warning = 'Ooops!. The product already in selected items.';
            return response()->json(['status' =>'DUPL', 'msg' => $warning]);
        }
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
        $shop = Shop::find(Session::get('shop_id'));
        $itemtemp =  EfdmsItemTemp::where('id', $id)->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();
        if (!is_null($itemtemp)) {
            $itemtemp->qty = $request['qty'];
            $itemtemp->price = $request['price'];
            $itemtemp->amt = $itemtemp->qty*$itemtemp->price;
            $itemtemp->with_vat = $request['with_vat'];
            if ($itemtemp->with_vat == 'yes') {
                $itemtemp->taxcode = 1;
                $taxcode = Taxcode::find($itemtemp->taxcode);
                $itemtemp->vat = $itemtemp->amt*($taxcode->value/100);
            }
            $itemtemp->save();
            return $itemtemp;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $itemtemp = EfdmsItemTemp::destroy($id);
    }
}
