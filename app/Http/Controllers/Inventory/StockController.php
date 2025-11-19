<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Stock;
use App\Models\AnSaleItem;
use App\Models\Purchase;
use App\Models\SaleReturnItem;
use App\Models\TransferOrderItem;
use App\Models\ProdDamage;
use App\Models\SupplierTransaction;
use App\Models\PurchaseCostItem;
use App\Jobs\StockUpdaterJob;
use App\Models\PurchaseOrderItem;
use App\Models\ProductionRun;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $stockdate = Carbon::now();
        if (!empty($request['stock_date'])) {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $stockdate = $request['stock_date'] . ' ' . $time;
        }

        $shop = Shop::find(Session::get('shop_id'));
        $stock = Stock::where('production_run_id', $request['production_run_id'])->where('product_id', $request['product_id'])->first();
        if (is_null($stock)) {
            $prodrun = ProductionRun::find($request['production_run_id']);
            $stock = new Stock();
            $stock->shop_id = $shop->id;
            $stock->product_id = $request['product_id'];
            $stock->production_run_id = $request['production_run_id'];
            $stock->storage_location_id = $request['storage_location_id'];
            $stock->stock_date = $stockdate;
            $stock->quantity_in = $request['quantity_in'];
            $stock->source = 'Production Run No. '.$prodrun->pr_no;
            $stock->save();

            dispatch(new StockUpdaterJob($shop, $stock->product_id));
        
            return redirect()->route('sand-productions.show', encrypt($request['production_run_id']))->with('success', 'End Product added successfully');
        }else{
            return redirect()->back()->with('info', 'Same End Product for this Production already exists');
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
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $stock = Stock::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        $products = $shop->products()->get();
        $suppliers = $shop->suppliers()->get();
        if (is_null($stock)) {
            return redirect('forbiden');
        }else{
            $product = Product::find($stock->product_id);
            $page = 'Edit stock';
            $title = 'Edit stock';
            $title_sw = 'Hariri Stock';

            return view('products.stocks.edit', compact('page', 'title', 'title_sw', 'stock', 'product', 'products', 'suppliers', 'shop', 'settings'));
        }
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
        $stock = Stock::find(decrypt($id));
        $stock->product_id = $request['product_id'];
        $stock->quantity_in = $request['quantity_in'];
        $stock->unit_cost = $request['unit_cost'];
        $stock->expire_date = $request['exp_date'];
        $stock->save();

        $product = $shop->products()->where('id', $stock->product_id)->first();

        if ($product->unit_cost == 0 || $product->unit_cost > $product->retail_price) {
            $product->unit_cost = $stock->unit_cost;
            $product->save();    
        }

        dispatch(new StockUpdaterJob($shop, $stock->product_id));
        
        $purchase = Purchase::find($stock->purchase_id);

        if (!is_null($purchase)) {
            $poitem = PurchaseOrderItem::where('purchase_order_id', $purchase->purchase_order_id)->where('product_id', $stock->product_id)->where('shop_id', $shop->id)->first();
            if (!is_null($poitem)) {
                $poitem->received_qty = $stock->quantity_in;
                $poitem->save();
            }

            $pitems = Stock::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->get();
            $total_amount = 0;
            foreach ($pitems as $key => $item) {
                $total_amount += ($item->quantity_in*$item->unit_cost);
            }

            $purchase->total_amount = $total_amount;
            $purchase->save();

            $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $purchase->shop_id)->first();
            if (!is_null($acctrans) ) {
                $acctrans->amount = $purchase->total_amount;
                $acctrans->save();
            }

            $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
            $total_cost = 0;
            foreach ($costitems as $key => $item) {
                $total_cost += $item->amount;
            }

            $purchase->total_cost = $total_cost;
            $purchase->save();

            $total_unit_cost = 0;
            $pitems = Stock::where('purchase_id', $purchase->id)->get();
            foreach ($pitems as $key => $value) {
                $total_unit_cost += $value->unit_cost;
            }
            foreach ($pitems as $key => $pstock) {
                $unit_ac = 0;
                if ($total_unit_cost > 0 && $pstock->quantity_in > 0) {
                    $unit_ac = round((($stock->unit_cost/$total_unit_cost)*$total_cost)/$pstock->quantity_in, 2);
                }
                $pstock->unit_cost = $pstock->unit_cost+$unit_ac;
                $pstock->save();
            }
        }else{
            $stock->unit_cost = $stock->unit_cost;
            $stock->save();
        }

        $message = 'Stock was successfully updated';
        if (!is_null($stock->purchase_id)) {
            return redirect('purchase-items/'.encrypt($purchase->id))->with('success', $message);
        }else{
            return redirect()->route('products.show', encrypt($stock->product_id))->with('success', $message);
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        if ($user->can('delete-stock')) {
            $stock = Stock::find(decrypt($id));
            if (!is_null($stock)) {

                if ($stock->quantity_out == 0 ) {
                    $stock->delete();

                    dispatch(new StockUpdaterJob($shop, $stock->product_id));
                    $message = 'Stock was successfully deleted';

                    $purchase = Purchase::find($stock->purchase_id);
                    if (!is_null($purchase)) {
                        $pitems = Stock::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->get();
                        $total_amount = 0;
                        foreach ($pitems as $key => $item) {
                            $total_amount += ($item->quantity_in*$item->unit_cost);
                        }

                        $purchase->total_amount = $total_amount;
                        $purchase->save();

                        $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->first();
                        if (!is_null($acctrans)) {
                            $acctrans->amount = $purchase->total_amount;
                            $acctrans->save();
                        }

                        $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
                        $total_cost = 0;
                        foreach ($costitems as $key => $item) {
                            $total_cost += $item->amount;
                        }

                        $purchase->total_cost = $total_cost;
                        $purchase->save();

                        $total_unit_cost = 0;
                        $pitems = Stock::where('purchase_id', $purchase->id)->get();
                        foreach ($pitems as $key => $value) {
                            $total_unit_cost += $value->unit_cost;
                        }

                        foreach ($pitems as $key => $stock) {
                            $unit_ac = 0;
                            if ($total_unit_cost > 0) {
                                $unit_ac = round((($stock->unit_cost/$total_unit_cost)*$total_cost)/$stock->quantity_in, 2);
                            }
                            $stock->unit_cost = $stock->unit_cost+$unit_ac;
                            $stock->save();
                        }
                    
                        return redirect('purchase-items/'.encrypt($purchase->id))->with('success', $message);
                    }else{
                        return redirect()->back()->with('success', $message);
                    }
                }else{

                    $message = 'Stock cannot be deleted because has sales associated with';
                    return redirect()->route('products.show' , encrypt($stock->product_id))->with('info', $message);
                }
            }else{
                return redirect()->back()->with('info', 'Stock  entry not Found');
            }
        }else{
            return view('errors.401');
        }
    }
}
