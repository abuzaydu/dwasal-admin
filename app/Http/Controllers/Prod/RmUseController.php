<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\RmUse;
use App\Models\User;
use App\Models\RawMaterial;
use App\Models\RmItem;
use App\Models\RmUseItem;
use App\Models\RmUseItemTemp;
use App\Models\RmDamage;
use App\Models\Payment;
use App\Models\Module;
use App\Models\ProductionCost;
use App\Models\ProductionCostItem;
use App\Models\TransferOrder;
use App\Models\Stock;
use App\Models\SaleReturnItem;
use App\Models\ProdDamage;
use App\Models\AnSaleItem;
use App\Models\TransferOrderItem;


class RmUseController extends Controller
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
    public function index()
    {
        $page = 'Raw Materials';
        $title = 'Raw Materials Utilized';
        $title_sw = 'Matumizi ya Malighafi';
        $shop = Shop::find(Session::get('shop_id'));
        $rmuses = RmUse::where('rm_uses.shop_id', $shop->id)->where('is_deleted' , false)->join('users', 'users.id', '=', 'rm_uses.user_id')->select('rm_uses.id as id', 'rm_uses.total_cost as total_cost', 'rm_uses.date as date', 'users.first_name as first_name', 'users.last_name as last_name', 'rm_uses.created_at as created_at' , 'rm_uses.prod_batch as prod_batch')->latest()->get();


        return view('production.raw-materials.rm-uses.index', compact('page', 'title', 'title_sw', 'rmuses', 'shop' ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'Raw Materials';
        $title = 'Utilizing Raw Materials';
        $title_sw = 'Tumia Malighafi';
        $shop = Shop::find(Session::get('shop_id'));
        $products = $shop->products()->get();
        $settings = Setting::where('shop_id' , $shop->id)->first();

        // $module = Module::where('name', 'Production')->first();
        // $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', true)->first();
        // if (!is_null($payment)) {
            
            $last_prod_batch = RmUse::where('shop_id' , $shop->id)->max('prod_batch');
            $prod_batch = $last_prod_batch + 1;

            return view('production.raw-materials.rm-uses.create', compact('page', 'title', 'title_sw', 'shop' , 'prod_batch' , 'products' , 'settings'));
        //   } else {
        //     $info = 'Dear customer you have not subscribed to this module please make payment and activate now.';
        //     // Alert::info("Payment Expired", $info);
        //     return redirect('verify-module-payment/'.encrypt($module->id))->with('error', $info);
        // } 
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
        $now = null;
        if (empty($request['rmused_date'])) {
            $now = Carbon::now();
        }else{
            $now = $request['rmused_date'];
        }

        $uitems = RmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();

        if (!is_null($uitems)) {
            $temps = array();
            foreach ($uitems as $key => $value) {
                if ($value->quantity == 0) {
                    array_push($temps, $value->quantity);
                }
            }

            if (!empty($temps)) {
                return redirect()->back()->with('warning', 'Please update the quantity and of each item to continue');
            }else{

                $total_cost = 0;
                $rmuse = RmUse::create([
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'total_cost' => $total_cost,
                    'comments' => $request['comments'],
                    'date' => $now,
                    'prod_batch' => $request['prod_batch'],
                ]);

                foreach ($uitems as $key => $item){ 
                    $raw_material = RawMaterial::find($item->raw_material_id);
                    $useditem  = new RmUseItem;
                    $useditem->rm_use_id = $rmuse->id;
                    $useditem->raw_material_id = $raw_material->id;
                    $useditem->shop_id = $shop->id;
                    $useditem->quantity = $item->quantity;
                    $useditem->unit_cost = $item->unit_cost;
                    $useditem->total = $item->total;
                    $useditem->date = $now;
                    $useditem->save();

                    $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();

                    $purchased = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');
                    $used = RmUseItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
                    $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
                                    
                    $instore = $purchased-($damaged +$used); 
                                 
                    $shop_raw_material->pivot->in_store = $instore;
                    $lastpur = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->latest()->first();
                    if ($shop_raw_material->pivot->in_store <= $lastpur->qty) {
                        $shop_raw_material->pivot->unit_cost = $lastpur->unit_cost;
                    }
                    $shop_raw_material->pivot->save();

                    $total_cost += $item->total;
                }

                $rmuse->total_cost = $total_cost;
                $rmuse->save();

                $puritems = RmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                foreach ($puritems as $key => $value) {
                    $value->delete();
                }

                return redirect()->back()->with('success', 'Use of Raw Materials were added successfully');
            }
        }else{

            return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
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
        $page = 'Raw Materials';
        $title = 'Details of Used Raw Materials';
        $title_sw = 'Maelezo ya Matumizi ya Malighafi';

        $shop = Shop::find(Session::get('shop_id'));
        $rmuse = RmUse::find(decrypt($id));
        $uitems = RmUseItem::where('rm_use_id', $rmuse->id)->join('raw_materials', 'raw_materials.id', '=', 'rm_use_items.raw_material_id')->select('rm_use_items.id as id', 'rm_use_items.quantity as quantity', 'rm_use_items.unit_cost as unit_cost', 'rm_use_items.total as total', 'rm_use_items.date as date', 'raw_materials.name as name', 'raw_materials.basic_uom as basic_uom')->get();
        $employee = User::find($rmuse->user_id);
        return view('production.raw-materials.rm-uses.show', compact('page', 'title', 'title_sw', 'rmuse', 'uitems', 'shop', 'employee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Raw Materials';
        $title = 'Edit Raw Materials Used';
        $title_sw = 'Hariri Matumizi ya Malighafi';

        $shop = Shop::find(Session::get('shop_id'));
        $rmuses = RmUse::find(decrypt($id));


        return view('production.raw-materials.rm-uses.edit', compact('page', 'title', 'title_sw', 'rmuses', 'shop'));
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
        $rmuse = RmUse::find(decrypt($id));

        $rmuse->date = $request['date'];
        $rmuse->prod_batch = $request['prod_batch'];
        $rmuse->save();

        $rmitems = RmUseItem::where('shop_id' , $shop->id)->where('rm_use_id' , $rmuse->id)->get();

        foreach($rmitems as $item){
            $item->date = $request['date'];
            $item->save();
        }

        return redirect('rm-uses');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rmuse = RmUse::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($rmuse)) {
            $rmuseitems = RmUseItem::where('rm_use_id', $rmuse->id)->get();
            foreach ($rmuseitems as $key => $item) {
                $raw_material = rawMaterial::find($item->raw_material_id);
                $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();
                $shop_raw_material->pivot->in_store = $shop_raw_material->pivot->in_store + $item->quantity;
                $shop_raw_material->pivot->save();

                $item->delete();
                    
            }

            $rmuse->delete();

            return redirect()->back()->with('success', 'Use of Packing Materials was deleted successfully');
        }
        return redirect()->back();
    }

    public function storeProduct(Request $request)
    {
        
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $now = null;
        if (empty($request['rmused_date'])) {
            $now = Carbon::now();
        }else{
            $now = $request['rmused_date'];
        }

        $prod_record = ProductionCost::where('shop_id' , $shop->id)->where('prod_batch' , $request['prod_batch'])->first();

        if(!is_null($prod_record)){

           return redirect()->back()->with('warning', 'This Batch No is already used!.');
        }

        $uitems = RmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();

        if (!is_null($uitems)) {
            $temps = array();
            foreach ($uitems as $key => $value) {
                if ($value->quantity == 0) {
                    array_push($temps, $value->quantity);
                }
            }

            if (!empty($temps)) {
                return redirect()->back()->with('warning', 'Please update the quantity and of each item to continue');
            }else{

                $total_cost = 0;
                $rmuse = RmUse::create([
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'total_cost' => $total_cost,
                    'comments' => $request['comments'],
                    'date' => $now,
                    'prod_batch' => $request['prod_batch'],
                ]);

                foreach ($uitems as $key => $item){ 
                    $raw_material = RawMaterial::find($item->raw_material_id);
                    $useditem  = new RmUseItem;
                    $useditem->rm_use_id = $rmuse->id;
                    $useditem->raw_material_id = $raw_material->id;
                    $useditem->shop_id = $shop->id;
                    $useditem->quantity = $item->quantity;
                    $useditem->unit_cost = $item->unit_cost;
                    $useditem->total = $item->total;
                    $useditem->date = $now;
                    $useditem->save();

                    $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();

                    $purchased = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');
                    $used = RmUseItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
                    $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
                                    
                    $instore = $purchased-($damaged +$used); 
                                 
                    $shop_raw_material->pivot->in_store = $instore;
                    $lastpur = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->latest()->first();
                    if ($shop_raw_material->pivot->in_store <= $lastpur->qty) {
                        $shop_raw_material->pivot->unit_cost = $lastpur->unit_cost;
                    }
                    $shop_raw_material->pivot->save();

                    $total_cost += $item->total;
                }

                $rmuse->total_cost = $total_cost;
                $rmuse->save();

                $puritems = RmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                foreach ($puritems as $key => $value) {
                    $value->delete();
                }

    //...... production cost record
          
                $prod_cost = ProductionCost::create([
                    'total_prod_qty' =>  $request['product_v'],
                    'total_cost' => $total_cost, 
                    'prod_batch' => $request['prod_batch'],
                    'date' => $now,
                    'user_id'=> $user->id,
                    'shop_id'=> $shop->id,
                    'is_transferred' => true,
                ]);

                $product = $shop->products()->where('product_id' , $request->product)->first();
                $cost_per_unit = $total_cost/$request->product_v;

                $profit_margin = $product->retail_price - $cost_per_unit ;

                $dstock = Stock::create([
                    'product_id' => $request->product,
                    'shop_id' => $shop->id,
                    'quantity_in' => $request->product_v,
                    'unit_cost' => $cost_per_unit,
                    'profit_margin' => $request->profit_margin,
                    'selling_price' => !empty($request->profit_margin)? ($request->profit_margin + $cost_per_unit) : null ,
                    'source' => 'Production Batch '.$request->prod_batch,
                    'time_created' => $now,
                ]);


                $prod_cost_item = new ProductionCostItem();
                $prod_cost_item->product_id = $request->product;
                $prod_cost_item->unit_packed = 1;
                $prod_cost_item->quantity = $request->product_v;
                $prod_cost_item->cost_per_unit = floatval($cost_per_unit) ;
                $prod_cost_item->profit_margin = $profit_margin;
                $prod_cost_item->selling_price = $product->retail_price;
                $prod_cost_item->production_cost_id = $prod_cost->id;
                $prod_cost_item->stock_id = $dstock->id;
                $prod_cost_item->save();

                

                $deststock_in = Stock::where('product_id', $request->product)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                $destsold = AnSaleItem::where('product_id', $request->product)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                $destdamaged = ProdDamage::where('product_id', $request->product)->where('shop_id', $shop->id)->sum('quantity');
                $desttranfered =  TransferOrderItem::where('product_id', $request->product)->where('shop_id', $shop->id)->sum('quantity');
                
                $destreturned = SaleReturnItem::where('product_id', $request->product)->where('shop_id', $shop->id)->sum('quantity');
                                
                $destinstock = ($deststock_in+$destreturned)-($destsold+$destdamaged+$desttranfered);

                if (!is_null($product)) {
                    $product->in_stock = $destinstock;
                    $product->save();
                }

                return redirect()->back()->with('success', 'Use of Raw Materials were added successfully');
            }
        }else{

            return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
        }
    }
}
