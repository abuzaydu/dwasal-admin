<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Response;
use Session;
use Auth;
use Log;
use App\Models\Shop;
use App\Models\User;
use App\Models\TransferOrderItemTemp;
use App\Models\Product;

class TransformationTransferItemTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

     public function destinProducts(Request $request){
    
        $shop_id =$request['id'];
        $shop = Shop::find($shop_id);
        $destinproduct = $shop->products()->get([
            \DB::raw('product_id as id'),
            \DB::raw('product_code'),
            \DB::raw('barcode'),
            \DB::raw('name'),
            \DB::raw('description'),
            \DB::raw('in_stock'),
            \DB::raw('unit_cost'),
            \DB::raw('retail_price')]);

         return Response::json($destinproduct);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
        $sameitems = TransferOrderItemTemp::where('product_id', $request['product_id'])->where('user_id', $user->id)->where('shop_id', $shop->id)->count();

        
        if ($sameitems == 0) {
            $product = $shop->products()->where('id', $request['product_id'])->first();
            $destin = Shop::find($request['destin_id']);
            $destinproduct = $destin->products()->where('product_id', $request['product_id'])->first();
            
            $orderItemTemp = new TransferOrderItemTemp;
            $orderItemTemp->shop_id = $shop->id;
            $orderItemTemp->user_id = $user->id;
            $orderItemTemp->product_id = $request['product_id'];
            $orderItemTemp->quantity = 0;
            $orderItemTemp->source_stock =  is_null($product) ? 0 :$product->in_stock;
            if (!is_null($destinproduct->pivot->in_stock)) {
                $orderItemTemp->destin_stock = $destinproduct->pivot->in_stock;
            }else{
                $orderItemTemp->destin_stock = 0;
            }
            $orderItemTemp->source_unit_cost = is_null($product) ? 0 : $product->unit_cost;
            if (!is_null($destinproduct->pivot->unit_cost) && $destinproduct->pivot->unit_cost > 0) {
                $orderItemTemp->destin_unit_cost = $destinproduct->pivot->unit_cost;
            }else{
                $orderItemTemp->destin_unit_cost = is_null($product) ? 0 : $product->unit_cost ;
            }

            $orderItemTemp->save();
            return $orderItemTemp;
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
        $orderItemTemp =  TransferOrderItemTemp::where('id', $id)->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();

        if (!is_null($orderItemTemp)) {
            $product = Product::find($orderItemTemp->product_id);
            if (false) {

                return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Product '.$product->name.' is currently less than.'.($request['quantity'])]);
            }else{
                $orderItemTemp->quantity = $request['quantity'];
                $orderItemTemp->destin_unit_cost = $request['destin_unit_cost'];
                $orderItemTemp->save();
                return $orderItemTemp;
            }
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
        TransferOrderItemTemp::destroy($id);
    }
}
