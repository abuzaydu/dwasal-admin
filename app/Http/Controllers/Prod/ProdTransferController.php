<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use Auth;

use Carbon\Carbon;
use App\Models\PmUseItem;
use App\Models\PmUse;
use App\Models\ProductionCost;
use App\Models\Shop;
use App\Models\User;
use App\Models\TransferOrder;
use App\Models\Stock;
use App\Models\SaleReturnItem;
use App\Models\ProdDamage;
use App\Models\AnSaleItem;
use App\Models\TransferOrderItem;
use App\Models\ProductionCostItem;
use App\Models\Payment;
use App\Jobs\StockUpdaterJob;

class ProdTransferController extends Controller
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
        $page = 'Production';
        $title = 'Transfer Products To Shops';
        $title_sw = 'Hamisha Bidhaa Kwenda Dukani ';
        $shop = Shop::find(Session::get('shop_id'));
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->first();
        if (!is_null($payment)) {
        
        $user =Auth::user();

        $production =  ProductionCost::find(decrypt($id));
        $prod_cost_items = ProductionCostItem::where('production_cost_id' , $production->id)->join('products' , 'products.id' , '=' , 'production_cost_items.product_id')->get();
    

       if($production->is_transfered){
            return redirect()->back()->with('warning' , "production batchs product already transferred");
       }else{
      
            return view('production.transfer.create' , compact(['prod_cost_items'  , 'production'  , 'shop' ,  'page' , 'title' , 'title_sw']));
       }
          } else {
            $info = 'Dear customer your account is not activated please make payment and activate now.';
            // Alert::info("Payment Expired", $info);
            return redirect('verify-payment')->with('error', $info);
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
        $now = Carbon::now();
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $destinshop = $user->shops()->where('shop_id', $request['destin_id'])->first();
        $orderdate = $now;

        if (!empty($request['order_date'])) {
            $orderdate = $request['order_date'];
        }

        $production =  ProductionCost::find($request['production_id']);
        $prod_cost_items = ProductionCostItem::where('production_cost_id' , $production->id)->get();

        foreach ($prod_cost_items as $key => $item) {
            $destshop_product = $destinshop->products()->where('product_id' , $item->product_id)->first();
            if (!is_null($destshop_product)) {
                $dstock = Stock::create([
                    'product_id' => $item->product_id,
                    'shop_id' => $destinshop->id,
                    'quantity_in' => $item->quantity,
                    'unit_cost' => $item->cost_per_unit,
                    'source' => 'Production Batch '.$production->prod_batch,
                    'time_created' => $orderdate,
                ]);

                $item->stock_id = $dstock->id;
                $item->save();

                $destshop_product->pivot->buying_per_unit = $item->cost_per_unit;
                $destshop_product->pivot->save();

                dispatch(new StockUpdaterJob($destinshop, $item->product_id));
            }
        }

        $production->is_transferred = true;
        $production->save();
        $success = 'Transfer Order was created successfully';
        return redirect('prod-costs')->with('success', $success);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
