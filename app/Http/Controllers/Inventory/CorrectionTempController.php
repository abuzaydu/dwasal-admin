<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\CorrectionTemp;

class CorrectionTempController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $temps = CorrectionTemp::where('correction_temps.shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->join('products', 'products.id', '=', 'correction_temps.product_id')->select('correction_temps.id as id', 'name', 'correction_qty')->get();
        return response()->json($temps);
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
        $temp = CorrectionTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('product_id', $request->product_id)->first();
        if (is_null($temp)) {
            $correction = new CorrectionTemp();
            $correction->shop_id = $shop->id;
            $correction->user_id = $user->id;
            $correction->product_id = $request->product_id;
            $correction->correction_qty = 1;
            $correction->save();
            return $correction;
        }else{
            return response()->json(['status' => 'DUPL', 'msg' => 'Item already selected']);
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
        $correction = CorrectionTemp::find($id);
        if (!is_null($correction)) {
            $correction->correction_qty = $request->correction_qty;
            $correction->save();
            return $correction;
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
        $correction = CorrectionTemp::find($id);
        if (!is_null($correction)) {
            $correction->delete();
        }

        return response()->json(['msg' => 'Item removed successfully']);
    }
}
