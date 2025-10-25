<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;

class PurchaseOrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $page = 'Purchase Order Items';
        $title = 'Purchase Order Items';
        $title_sw = 'Bidhaa za Oda ya Manunuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $suppliers = $shop->suppliers()->get();
        $products = $shop->products()->get();
        $porder = PurchaseOrder::find(decrypt($id));
        $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->where('shop_id', $shop->id)->join('products', 'products.id', '=', 'purchase_order_items.product_id')->select('purchase_order_items.id as id', 'purchase_order_items.qty as qty', 'purchase_order_items.unit_cost as unit_cost', 'purchase_order_items.created_at as created_at', 'products.name as name', 'products.basic_uom as basic_uom')->orderBy('created_at', 'desc')->get();
        return view('products.purchase-orders.items', compact('page', 'title', 'title_sw', 'shop', 'suppliers', 'porder', 'pitems', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $porder = PurchaseOrder::find($request['purchase_order_id']);
        $product = Product::find($request['product_id']);
        if (!is_null($product)) {
                
            $poitem  = new PurchaseOrderItem;
            $poitem->purchase_order_id = $porder->id;
            $poitem->product_id = $product->id;
            $poitem->shop_id = $shop->id;
            $poitem->qty = $request['qty'];
            $poitem->unit_cost = $request['unit_cost'];
            $poitem->save();

            $porder = PurchaseOrder::find($poitem->purchase_order_id);
            $amount = 0;
            $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->get();
            foreach ($pitems as $key => $item) {
                $amount += $item->qty*$item->unit_cost;
            }
            
            $porder->amount = $amount;
            $porder->save();
        }

        return redirect('poitems/'.encrypt($porder->id))->with('success', 'Order Item updated successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::find($request['product_id']);
        if (!is_null($product)) {
                
            $poitem  = new PurchaseOrderItem;
            $poitem->purchase_order_id = $request['purchase_order_id'];
            $poitem->product_id = $product->id;
            $poitem->shop_id = $shop->id;
            $poitem->qty = $request['qty'];
            $poitem->unit_cost = $request['unit_cost'];
            $poitem->save();
        }

        return redirect()->back()->with('success', 'Purchase Item added successfully');
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
        $page = 'Products';
        $title = 'Edit Purchase Order Item';
        $title_sw = 'Hariri Bidhaa ya kuagiza';
        $shop = Shop::find(Session::get('shop_id'));
        $products = $shop->products()->get();
        $item = PurchaseOrderItem::find(decrypt($id));
        $product = Product::find($item->product_id);

        return view('products.purchase-orders.edit-item', compact('page', 'shop', 'title', 'title_sw', 'products', 'item', 'product'));
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
        $poitem = PurchaseOrderItem::find(decrypt($id));
        $poitem->product_id = $request['product_id'];
        $poitem->qty = $request['qty'];
        $poitem->unit_cost = $request['unit_cost'];
        $poitem->save();

        $porder = PurchaseOrder::find($poitem->purchase_order_id);
        $amount = 0;
        $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->get();
        foreach ($pitems as $key => $item) {
            $amount += $item->qty*$item->unit_cost;
        }
        $porder->amount = $amount;
        $porder->save();

        return redirect('poitems/'.encrypt($porder->id))->with('success', 'Order Item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = PurchaseOrderItem::find(decrypt($id));
        if (!is_null($item)) {
            $item->delete();
            
            $porder = PurchaseOrder::find($item->purchase_order_id);
            $amount = 0;
            $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->get();
            foreach ($pitems as $key => $item) {
                $amount += $item->qty*$item->unit_cost;
            }
            $porder->amount = $amount;
            $porder->save();
        }
        return redirect()->back()->with('success', 'Order Item removed successfully');
    }
}
