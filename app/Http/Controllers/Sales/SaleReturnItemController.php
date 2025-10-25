<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use \DB;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\User;
use App\Models\Settings;
use App\Models\Invoice;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\Stock;
use App\Models\ProdDamage;
use App\Models\TransferOrderItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\CreditNote;
use App\Models\SalePayment;
use App\Jobs\StockUpdaterJob;

class SaleReturnItemController extends Controller
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
        $salereturn = SaleReturn::find($request['sale_return_id']);
        $sale = AnSale::find($salereturn->an_sale_id);
        $saleitem = AnSaleItem::where('an_sale_id', $sale->id)->where('product_id', $request['product_id'])->first();
        $sritem = SaleReturnItem::where('sale_return_id', $salereturn->id)->where('product_id', $saleitem->product_id)->first();
        if (is_null($sritem)) {
            $sritem = SaleReturnItem::create([
                'sale_return_id' => $salereturn->id,
                'product_id' => $saleitem->product_id,
                'shop_id' => $shop->id,
                'quantity' => 0,
                'unit_cost' => $saleitem->unit_cost,
                'retail_price' => $saleitem->retail_price,
                'discount' => $saleitem->discount,
            ]);
        }
        

        return redirect()->back();
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
    public function update(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $sritem = SaleReturnItem::where('id', $request['id'])->first();
        if (!is_null($sritem)) {
            $salereturn = SaleReturn::find($sritem->sale_return_id);
            $stock = Stock::find($sritem->stock_id);
            if (!is_null($stock)) {
                // Restore returned
                $stock->quantity_out = $stock->quantity_out+$sritem->quantity;
                $stock->save();
                //Remove returned
                $stock->quantity_out = $stock->quantity_out-$request['quantity'];
                if ($stock->quantity_in == $stock->quantity_out) {
                    $stock->is_utilized = true;
                }else{
                    $stock->is_utilized = false;
                }
                $stock->save();
            }
    
            $item = AnSaleItem::where('an_sale_id', $salereturn->an_sale_id)->where('product_id', $sritem->product_id)->first();
            $sritem->quantity = $request['quantity'];
            $sritem->buying_price = $sritem->quantity*$sritem->unit_cost;
            $sritem->price = $sritem->quantity*$sritem->retail_price;
            $sritem->total_discount = $sritem->quantity*$sritem->discount;
            if ($item->with_vat == 'yes') {
                $sritem->tax_amount = ($sritem->price-$sritem->total_discount)*($settings->tax_rate/100);
            }else{
                $sritem->tax_amount = 0;
            }
            $sritem->save();
            dispatch(new StockUpdaterJob($shop, $sritem->product_id));

            return response()->json(['success' => 1, 'msg' => 'Updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item not Found']);
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
        $sritem = SaleReturnItem::find(decrypt($id));
        if (!is_null($sritem)) {
            $salereturn = SaleReturn::find($sritem->sale_return_id);
            $sritem->delete();
            if (!is_null($salereturn)) {
                return redirect()->route('sales-returns.edit', encrypt($salereturn->id));
            }
        }else{
            return redirect('sales-returns');
        }
    }
}
