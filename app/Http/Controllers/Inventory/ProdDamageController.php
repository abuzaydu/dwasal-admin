<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Session;
use App\Models\Shop;
use App\Models\Product;
use App\Models\ProdDamage;
use App\Models\Stock;
use App\Jobs\StockUpdaterJob;
use App\Models\Setting;

class ProdDamageController extends Controller
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
        $shop = Shop::find(Session::get('shop_id'));
        $product = Product::find($request['product_id']);
        $now = Carbon::now();
        if (!empty($request['dam_date'])) {
            $time = date('H:i:s', strtotime($now));
            $now = $request['dam_date'].' '.$time;
        }
        $product = $shop->products()->where('id', $product->id)->first();
        if (!empty($request['quantity'])) {
            $pdam = new ProdDamage();
            $pdam->product_id = $product->id;
            $pdam->shop_id = $shop->id;
            $pdam->quantity = $request['quantity'];
            $pdam->selling_price = $product->retail_price;
            $pdam->buying_price = $product->unit_cost;
            $pdam->reason = $request['reason'];
            $pdam->time_created = $now;
            $pdam->save();

            $astocks = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->get();
            $damqty = $request['quantity'];
            foreach ($astocks as $key => $stock) {
                $remqty = ($stock->quantity_in-$stock->quantity_out);
                if ($damqty > 0) {
                    if ($remqty >= $damqty) {
                        $stock->quantity_out = $stock->quantity_out+$damqty;
                        if ($stock->quantity_in == $stock->quantity_out) {
                            $stock->is_utilized = true;
                        }
                        $stock->save();
                        $pdam->stock_id = $stock->id;
                        $pdam->save();
                        
                        $damqty -= $remqty;
                    }
                }
            }
        }else{
            if (!empty($request['deph_measure'])) {
                $quantity = $product->in_stock-$request['deph_measure'];
                if ($quantity > 1000) {
                    return redirect()->back()->with('info', 'You can not record new Depth before recording New Sales Done');
                }elseif($quantity < -1000) {
                    return redirect()->back()->with('info', 'You can not record new Depth before recording new Stock Purchased');
                }else{ 
                    $pdam = new ProdDamage();
                    $pdam->product_id = $product->id;
                    $pdam->shop_id = $shop->id;
                    $pdam->deph_measure = $request['deph_measure'];
                    $pdam->in_stock = $product->in_stock;
                    $pdam->quantity = $quantity;
                    $pdam->selling_price = $product->retail_price;
                    $pdam->buying_price = $product->unit_cost;
                    $pdam->reason = $request['reason'];
                    $pdam->time_created = $now;
                    $pdam->save();
                }
            }
        }

        dispatch(new StockUpdaterJob($shop, $product->id));

        $message = 'Damaged Items was successfully recorded';

        return redirect()->back()->with('success', $message);
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
        $pdam = ProdDamage::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (is_null($pdam)) {
            return redirect('forbiden');
        }else{
            $product = Product::find($pdam->product_id);
            $page = 'Edit damaged';
            $title = 'Edit damaged';
            $title_sw = 'Hariri Iliyoharibika';

            return view('products.damaged.edit', compact('page', 'title', 'title_sw', 'pdam', 'product', 'settings'));
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
        $settings = Setting::where('shop_id', $shop->id)->first();
        $now = Carbon::now();
        if (!empty($request['dam_date'])) {
            $time = date('H:i:s', strtotime($now));
            $now = $request['dam_date'].' '.$time;
        }   

        $pdam = ProdDamage::find(decrypt($id));
        if ($settings->is_filling_station) {
            $pdam->deph_measure = $request['deph_measure'];
            $pdam->quantity = $pdam->in_stock-$pdam->deph_measure;
        }else{
            $pdam->quantity = $request['quantity'];
        }
        $pdam->reason = $request['reason'];
        $pdam->save();
        dispatch(new StockUpdaterJob($shop, $pdam->product_id));   

        $message = 'Product Damage was successfully updated';

        return redirect()->route('products.show' , encrypt($product->id))->with('success', $message);
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
        $pdam = ProdDamage::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (is_null($pdam)) {
            return redirect('forbiden');
        }else{
            $product = Product::find($pdam->product_id);
            $stock = Stock::find($pdam->stock_id);
            if (!is_null($stock)) {
                // Restore returned
                $stock->quantity_out = $stock->quantity_out-$pdam->quantity;
                $stock->save();
                if ($stock->quantity_in == $stock->quantity_out) {
                    $stock->is_utilized = true;
                }else{
                    $stock->is_utilized = false;
                }
                $stock->save();
            }
            $pdam->delete();

            dispatch(new StockUpdaterJob($shop, $pdam->product_id));
            $message = 'Stock was successfully deleted';

            return redirect()->route('products.show' , encrypt($product->id))->with('success', $message);
        }
    }
}


