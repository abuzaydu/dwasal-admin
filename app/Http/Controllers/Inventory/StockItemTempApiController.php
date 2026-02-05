<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Response;
use Session;
use Auth;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\Product;
use App\Models\ShopCurrency;
use App\Models\Supplier;
use App\Models\PurchaseTemp;
use App\Models\PurchaseItemTemp;
use App\Models\PurchaseCostItemTemp;

class StockItemTempApiController extends Controller
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

    public function updatePurchaseTemp(Request $request)
    {
        $purchasetemp = PurchaseTemp::find($request['id']);
        if (!is_null($purchasetemp)) {
                
            $local_ex_rate = 1;
            $foreign_ex_rate = 1;
            $ex_rate = 1;
            if ($request['currency'] != $purchasetemp->defcurr) {
                if ($request['ex_rate_mode'] == 'Foreign') {
                    $local_ex_rate = $request['local_ex_rate'];
                    $ex_rate = 1/$local_ex_rate;
                }else{
                    $foreign_ex_rate = $request['foreign_ex_rate'];
                    if ($foreign_ex_rate != 0) {
                        $ex_rate = $foreign_ex_rate;
                    }
                }
            }

            $purchasetemp->supplier_id = $request['supplier_id'];
            $purchasetemp->date_set = $request['date_set'];
            $purchasetemp->purchase_date = $request['purchase_date'];
            $purchasetemp->purchase_type = $request['purchase_type'];
            $purchasetemp->pay_type = $request['pay_type'];
            $purchasetemp->currency = $request['currency'];
            $purchasetemp->ex_rate_mode = $request['ex_rate_mode'];
            $purchasetemp->local_ex_rate = $local_ex_rate;
            $purchasetemp->foreign_ex_rate = $foreign_ex_rate;
            $purchasetemp->ex_rate = $ex_rate;
            $purchasetemp->comments = $request['comments'];
            $purchasetemp->save();

            return $purchasetemp;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {   
        $shop = Shop::find(Session::get('shop_id'));
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Stock')->select('id','name')->get();
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $purchasetemp = PurchaseTemp::find($id);
        if (!is_null($purchasetemp)) {
                
            $itemtemps = PurchaseItemTemp::where('purchase_temp_id', $purchasetemp->id)->join('products', 'products.id', '=', 'purchase_item_temps.product_id')->select('purchase_item_temps.id as id', 'purchase_temp_id', 'product_id', 'slug', 'quantity_in', 'purchase_item_temps.unit_cost as unit_cost', 'total', 'purchase_item_temps.retail_price as retail_price', 'expire_date')->get();
            $temps = array();
            foreach ($itemtemps as $key => $temp) {
                array_push($temps, [
                    'id' => $temp->id,
                    'purchase_temp_id' => $temp->purchase_temp_id,
                    'product_id' => $temp->product_id,
                    'name' => $temp->slug,
                    'quantity_in' => $temp->quantity_in,
                    'unit_cost' => round($temp->unit_cost*$purchasetemp->ex_rate, 2),
                    'total' => round($temp->total*$purchasetemp->ex_rate, 2),
                    'retail_price' => round($temp->retail_price*$purchasetemp->ex_rate, 2),
                    'expire_date' => $temp->expire_date,
                    // 'created_at' => $temp->created_at,
                    // 'updated_at' => $temp->updated_at
                ]);
            }
            $costtemps = purchaseCostItemTemp::where('purchase_temp_id', $purchasetemp->id)->get();
            return Response::json(['purchasetemp' => $purchasetemp, 'suppliers' => $suppliers, 'currencies' => $currencies, 'items' =>$temps, 'costtemps' => $costtemps]);
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
        $purchasetemp = PurchaseTemp::find($request['purchase_temp_id']);
        $sameitems = PurchaseItemTemp::where('product_id', $request['product_id'])->where('purchase_temp_id', $purchasetemp->id)->count();
        
        if ($sameitems == 0) {
            $product = $shop->products()->where('id', $request['product_id'])->first();
            if (!is_null($product)) {
                $stockItemTemp = new PurchaseItemTemp;
                $stockItemTemp->purchase_temp_id = $purchasetemp->id;
                $stockItemTemp->product_id = $request['product_id'];
                $stockItemTemp->quantity_in  = 0;
                $stockItemTemp->unit_cost = $product->unit_cost;
                if (!is_null($product->retail_price)) {
                    $stockItemTemp->retail_price = $product->retail_price;
                }else{
                    $stockItemTemp->retail_price = 0;
                }
                $stockItemTemp->total = 0;
                // $stockItemTemp->retail_price = $product->price_with_vat;
                $stockItemTemp->save();

                return $stockItemTemp;
            }
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
        $stockItemTemp =  PurchaseItemTemp::find($id);
        if (!is_null($stockItemTemp)) {
            $purchasetemp = PurchaseTemp::find($stockItemTemp->purchase_temp_id);
            if ($stockItemTemp->quantity_in != $request['quantity_in']) {
                $product = Product::find($stockItemTemp->product_id);
                if ($product->basic_uom == 'pcs' || $product->basic_uom == 'prs' || $product->basic_uom == 'box' || $product->basic_uom == 'btl' || $product->basic_uom == 'pks' || $product->basic_uom == 'gls') {
                    if (!$this->is_decimal($request['quantity_in'])) {
                        $stockItemTemp->quantity_in  = $request['quantity_in'];
                        $stockItemTemp->total = (float)$stockItemTemp->quantity_in*(float)$stockItemTemp->unit_cost;
                        $stockItemTemp->save();

                        return $stockItemTemp;
                    }else{
                        return response()->json(['status' => 'WRONG', 'msg' => 'This product '.$product->name.' can not accept decimal quantity. Please change its basic unit if you want to set decimal for stock quantity values']);
                    }
                }else{
                    $stockItemTemp->quantity_in  = $request['quantity_in'];
                    $stockItemTemp->total = (float)$stockItemTemp->quantity_in*(float)$stockItemTemp->unit_cost;
                    $stockItemTemp->save();

                    return $stockItemTemp;
                }
            }else{
                if($stockItemTemp->unit_cost != round((float)$request['unit_cost']/(float)$purchasetemp->ex_rate,2)) {
                    if ($purchasetemp->currency != $purchasetemp->defcurr) {
                        $stockItemTemp->unit_cost = (float)$request['unit_cost']/(float)$purchasetemp->ex_rate;     
                    }else{
                        $stockItemTemp->unit_cost = $request['unit_cost'];
                    }
                    $stockItemTemp->total = (float)$stockItemTemp->quantity_in*(float)$stockItemTemp->unit_cost;
                    $stockItemTemp->save();

                    return $stockItemTemp;
                }else{
                    if ($stockItemTemp->total != $request['total']) {
                        if ($purchasetemp->currency != $purchasetemp) {
                            $stockItemTemp->total = $request['total']/$purchasetemp->ex_rate;
                        }else{
                            $stockItemTemp->total = $request['total'];
                        }
                        if ($stockItemTemp->quantity_in > 0) {
                            $stockItemTemp->unit_cost = $stockItemTemp->total/$stockItemTemp->quantity_in;
                        }
                        $stockItemTemp->save();

                        return $stockItemTemp;   
                    }else{
                        if($stockItemTemp->retail_price != round((float)$request['retail_price']/(float)$purchasetemp->ex_rate,2)){
                            if ($purchasetemp->currency != $purchasetemp->defcurr) {
                                $stockItemTemp->retail_price = $request['retail_price']/$purchasetemp->ex_rate;
                            }else{
                                $stockItemTemp->retail_price = $request['retail_price'];
                            }
                            $stockItemTemp->save();
                            return $stockItemTemp;
                        }else{
                            if ($stockItemTemp->expire_date != $request['expire_date']) {
                                try {
                                    $expdate = Carbon::parse($request['expire_date']);
                                    $now = Carbon::now();
                                    $numd = $expdate->gt($now);
                                    if ($numd) {
                                        $stockItemTemp->expire_date = $request['expire_date'];
                                        $stockItemTemp->save();

                                        return $stockItemTemp;
                                    }else{
                                        return response()->json(['status' => 'FAIL']);
                                    }
                                } catch (\Exception $e) {

                                }
                            }
                        }    
                    }
                }
            }
        }
    }

    function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PurchaseItemTemp::destroy($id);
    }

    public function cancelPurchase($id)
    {
        $purchasetemp = PurchaseTemp::find(decrypt($id));
        if (!is_null($purchasetemp)) {
            $items = PurchaseItemTemp::where('purchase_temp_id', $purchasetemp->id)->get();
            foreach ($items as $key => $item) {
                $item->delete();
            }
            $purchasetemp->delete();
        }

        return redirect()->route('purchases.create')->with('success', 'Purchase cancelled successfully');
    }


    public function cancelProduction($id)
    {
        $purchasetemp = PurchaseTemp::find(decrypt($id));
        if (!is_null($purchasetemp)) {
            $items = PurchaseItemTemp::where('purchase_temp_id', $purchasetemp->id)->get();
            foreach ($items as $key => $item) {
                $item->delete();
            }
            $purchasetemp->delete();
        }

        return redirect('productions/create-production')->with('success', 'Purchase cancelled successfully');
    }

    public function ajaxPost(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $product = $shop->products()->where('barcode', $request['barcode'])->first();
        if (!is_null($product)) {
            $purchasetemp = PurchaseTemp::find($request['purchase_temp_id']);
            $stockItemTemp = PurchaseItemTemp::where('product_id', $product->id)->where('purchase_temp_id', $purchasetemp->id)->first();
        
            if (!is_null($stockItemTemp)) {
                $stockItemTemp->quantity_in  = $stockItemTemp->quantity_in+1;
                $stockItemTemp->total = (float)$stockItemTemp->quantity_in*(float)$stockItemTemp->unit_cost;
                $stockItemTemp->save();
                return response()->json(['status' => 200, 'msg' => 'Item added successfully']);
            }else{
                $stockItemTemp = new PurchaseItemTemp;
                $stockItemTemp->purchase_temp_id = $purchasetemp->id;
                $stockItemTemp->product_id = $product->id;
                $stockItemTemp->quantity_in  = 1;
                $stockItemTemp->unit_cost = $product->unit_cost;
                $stockItemTemp->total = (float)$stockItemTemp->quantity_in*(float)$stockItemTemp->unit_cost;
                if (!is_null($product->retail_price)) {
                    $stockItemTemp->retail_price = $product->retail_price;
                }else{
                    $stockItemTemp->retail_price = 0;
                }
                $stockItemTemp->save();
                return response()->json(['status' => 200, 'msg' => 'Item added successfully']);
            }
        }else{
            $warning = "Sorry, Scanned barcode value does not match any of your products . Please Try Again";
            return response()->json(['status' => 400, 'msg' => $warning]);
        }
    }
}

