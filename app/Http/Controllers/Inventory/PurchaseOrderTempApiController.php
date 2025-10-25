<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use \Response;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Supplier;
use App\Models\POrderTemp;
use App\Models\PurchaseOrderTemp;
use App\Imports\PurchaseOrderImport;
use App\Models\User;
use App\Models\Product;

class PurchaseOrderTempApiController extends Controller
{

     public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $ordertemp = POrderTemp::find($id);
        if (!is_null($ordertemp)) {
            $temps = PurchaseOrderTemp::where('p_order_temp_id', $ordertemp->id)->join('products','products.id', '=', 'purchase_order_temps.product_id')->select('purchase_order_temps.id as id', 'product_code', 'name', 'slug', 'qty', 'purchase_order_temps.unit_cost as unit_cost')->get();

            $suppliers = Supplier::where('shop_id', $ordertemp->shop_id)->where('supplier_for', 'Stock')->get();
            return response()->json(['ordertemp' => $ordertemp, 'temps' => $temps, 'suppliers' => $suppliers]);
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $ordertemp = POrderTemp::find($request['order_temp_id']);
        if (!is_null($ordertemp)) {
            
            $sameitems = PurchaseOrderTemp::where('p_order_temp_id', $ordertemp->id)->where('product_id', $request['product_id'])->count();
            
            if ($sameitems == 0) {
                $product = $shop->products()->where('id', $request['product_id'])->first();
                if (!is_null($product)) {
                    
                    $itemTemp = new PurchaseOrderTemp;
                    $itemTemp->p_order_temp_id = $ordertemp->id;
                    $itemTemp->shop_id = $shop->id;
                    $itemTemp->user_id = $user->id;
                    $itemTemp->product_id = $request['product_id'];
                    $itemTemp->qty  = 0;
                    $itemTemp->unit_cost = $product->unit_cost;
                    $itemTemp->save();

                    return $itemTemp;
                }
            }else{
                $warning = 'Ooops!. The product already in selected items.';
                return response()->json(['status' => 'DUPL', 'msg' => $warning]);
            }
        }else{
            return response()->json(['status' => 'FAIL', 'msg' => 'Order Temp Not found']);
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
        $itemTemp =  PurchaseOrderTemp::where('id', $id)->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();
        if (!is_null($itemTemp)) {

            if ($itemTemp->unit_cost != $request['unit_cost']) { 
                $itemTemp->unit_cost = $request['unit_cost'];
                $itemTemp->save();

                return $itemTemp;
            }elseif ($itemTemp->qty != $request['qty']) {

                $product = Product::find($itemTemp->product_id);

                if ($product->basic_uom == 'pcs' || $product->basic_uom == 'prs' || $product->basic_uom == 'box' || $product->basic_uom == 'btl' || $product->basic_uom == 'pks' || $product->basic_uom == 'gls') {
                    if (is_int($request['qty'])) {
                        $itemTemp->qty  = $request['qty'];
                        $itemTemp->save();

                        return $itemTemp;
                    }else{

                        return response()->json(['status' => 'WRONG', 'msg' => 'This product '.$product->name.' can not accept decimal quantity. Please change its basic unit if you want to set decimal for stock quantity values']);
                    }
                }else{
                    $itemTemp->qty  = $request['qty'];
                    $itemTemp->save();

                    return $itemTemp;
                }
            }
        }
    }

    public function updateOrderTempInfo(Request $request)
    {
        $ordertemp = POrderTemp::find($request['id']);
        if (!is_null($ordertemp)) {
            $ordertemp->supplier_id = $request['supplier_id'];
            $ordertemp->pfi_no = $request['pfi_no'];
            $ordertemp->comments = $request['comments'];
            $ordertemp->save();

            return $ordertemp;
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
        PurchaseOrderTemp::destroy($id);
    }

    public function ajaxPost(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = User::find(Session::get('user_id'));
        
        $product = $shop->products()->where('barcode', $request['barcode'])->first();
        if (!is_null($product)) {
            $itemTemp = PurchaseOrderTemp::where('product_id', $product->product_id)->where('user_id', $user->id)->where('shop_id', $shop->id)->first();
            if (!is_null($itemTemp)) {
                
                $itemTemp->quantity_in  = $itemTemp->quantity_in+1;
                $itemTemp->save();
            }else{
                $itemTemp = new PurchaseOrderTemp;
                $itemTemp->shop_id = $shop->id;
                $itemTemp->user_id = $user->id;
                $itemTemp->product_id = $product->product_id;
                $itemTemp->quantity_in  = 1;
                $itemTemp->unit_cost = $product->unit_cost;
                $itemTemp->save();
            }
            return response()->json(['status' => 'OK']);
        }else{
            $warning = "Sorry, Scanned barcode value does not match any of your products . Please Try Again";
            return response()->json(['status' => 'Fail', 'msg' => $warning]);
        }
    }

    public function importItems(Request $request)
    {
        set_time_limit(1800);
        $rules = array(
            'file' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        // process the form
        if ($validator->fails()) {
            return redirect()->route('purchase-orders.create')->withErrors($validator);
        } else {
            Excel::import(new PurchaseOrderImport, request()->file('file'));
            return redirect()->route('purchase-orders.create')->with('success', 'Items added to Purchase item list successfully. You can open the order if hidden to Pendings');
        }
    } 
}
