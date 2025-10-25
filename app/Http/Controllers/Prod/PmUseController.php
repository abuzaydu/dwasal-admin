<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\PmUse;
use App\Models\User;
use App\Models\PackingMaterial;
use App\Models\PmItem;
use App\Models\PmUseItem;
use App\Models\PmUseItemTemp;
use App\Models\PmDamage;
use App\Models\Payment;
use App\Models\Module;
use App\Models\ProductMadeApiTemp;

class PmUseController extends Controller
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
        $page = 'Packing Materials';
        $title = 'Packing Materials Utilized';
        $title_sw = 'Matumizi ya Vifungashio';
        $shop = Shop::find(Session::get('shop_id'));
        $pmuses = PmUse::where('pm_uses.shop_id', $shop->id)->where('is_deleted' , false)->join('users', 'users.id', '=', 'pm_uses.user_id')->select('pm_uses.id as id', 'pm_uses.total_cost as total_cost', 'pm_uses.date as date', 'users.first_name as first_name', 'users.last_name as last_name', 'pm_uses.prod_batch as prod_batch' , 'pm_uses.created_at as created_at')->latest()->get();

        return view('production.packing-materials.pm-uses.index', compact('page', 'title', 'title_sw', 'pmuses', 'shop'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $page = 'Packing Materials';
        $title = 'Utilizing Packing Materials';
        $title_sw = 'Tumia Vifungashio';
        $shop = Shop::find(Session::get('shop_id'));
        $module = Module::where('name', 'Production')->first();
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', true)->first();
        // if (!is_null($payment)) {
       
            $last_prod_batch = PmUse::where('shop_id' , $shop->id)->max('prod_batch');
            $prod_batch = $last_prod_batch + 1;
       
            return view('production.packing-materials.pm-uses.create', compact('page', 'title', 'title_sw', 'shop' , 'prod_batch'));  

        // } else {
        //     $info = 'Dear customer you have not subscribed to this module please make payment and activate now..';
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
        if (empty($request['pmused_date'])) {
            $now = Carbon::now();
        }else{
            $now = $request['pmused_date'];
        }


        $uitems = PmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();

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
                $pmuse = PmUse::create([
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'total_cost' => $total_cost,
                    'comments' => $request['comments'],
                    'date' => $now,
                    'prod_batch' => $request['prod_batch']
                ]);

                foreach ($uitems as $key => $item) {
                    $packing_material = PackingMaterial::find($item->packing_material_id);
                    $useditem  = new PmUseItem;
                    $useditem->pm_use_id = $pmuse->id;
                    $useditem->user_id = $user->id;
                    $useditem->packing_material_id = $packing_material->id;
                    $useditem->shop_id = $shop->id;
                    $useditem->quantity = $item->quantity;
                    $useditem->unit_cost = $item->unit_cost;
                    $useditem->total = $item->total;
                    $useditem->unit_packed = $item->unit_packed;
                    $useditem->date = $now;
                    $useditem->product_packed = $item->product_packed;
                    $useditem->save();

                    $shop_packing_material = $shop->packingMaterials()->where('packing_material_id', $packing_material->id)->where('is_deleted' , false)->first();

                    $purchased = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');
                    $used = PmUseItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');
                    $damaged = PmDamage::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');
                                    
                    $instore = $purchased-($used+$damaged); 
                                 
                    $shop_packing_material->pivot->in_store = $instore;
                    $lastpur = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->latest()->first();
                    if ($shop_packing_material->pivot->in_store <= $lastpur->qty) {
                        $shop_packing_material->pivot->unit_cost = $lastpur->unit_cost;
                    }
                    $shop_packing_material->pivot->save();

                    $total_cost += $item->total;
                }

                $pmuse->total_cost = $total_cost;
                $pmuse->save();

                $puritems = PmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
               $product_made = ProductMadeApiTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->get();
                foreach ($puritems as $key => $value) {
                    $value->delete();
                }

                if(!is_null($product_made)){
                    foreach ($product_made as $key => $value) {
                        if(!is_null($value->packing_material_id)){
                            $value->delete();
                        }
                    }
                }

                return redirect()->back()->with('success', 'Use of Packing Materials were added successfully');
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
        $page = 'Packing Materials';
        $title = 'Details of Packing Materials Used';
        $title_sw = 'Maelezo ya Matumizi ya Malighafi';

        $shop = Shop::find(Session::get('shop_id'));
        $products = $shop->products()->get();
        $pmuse = PmUse::find(decrypt($id));

        $uitems = PmUseItem::where('pm_use_id', $pmuse->id)->join('packing_materials', 'packing_materials.id', '=', 'pm_use_items.packing_material_id')->leftJoin('products' , 'pm_use_items.product_packed' ,'=' , 'products.id')->select('pm_use_items.id as id', 'pm_use_items.quantity as quantity', 'pm_use_items.unit_cost as unit_cost', 'pm_use_items.total as total', 'pm_use_items.date as date', 'packing_materials.name as name', 'products.basic_uom as basic_uom' , 'products.name as product_name' , 'pm_use_items.unit_packed as unit_packed')->get();

        $employee = User::find($pmuse->user_id);
        return view('production.packing-materials.pm-uses.show', compact('page', 'title', 'title_sw', 'pmuse', 'uitems', 'shop', 'employee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Packing Materials';
        $title = 'Edit Packing Materials Used';
        $title_sw = 'Hariri Matumizi ya Vifungashio';

        $shop = Shop::find(Session::get('shop_id'));
        $pmuses = PmUse::find(decrypt($id));

        return view('production.packing-materials.pm-uses.edit', compact('page', 'title', 'title_sw', 'pmuses', 'shop'));
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
        $pmuse = PmUse::find(decrypt($id));

        $pmuse->date = $request['date'];
        $pmuse->prod_batch = $request['prod_batch'];
        $pmuse->save();

        return redirect('pm-uses');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pmuse = PmUse::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($pmuse)) {
            $pmuseitems = PmUseItem::where('pm_use_id', $pmuse->id)->get();
            foreach ($pmuseitems as $key => $item) {
                $packing_material = PackingMaterial::find($item->packing_material_id);
                $shop_packing_material = $shop->packingMaterials()->where('packing_material_id', $packing_material->id)->where('is_deleted' , false)->first();
                if (!is_null($shop_packing_material)) {
                        
                    $shop_packing_material->pivot->in_store = $shop_packing_material->pivot->in_store + $item->quantity;
                    $shop_packing_material->pivot->save();
                }
                $item->delete();
                    
            }

            $pmuse->delete();

            return redirect()->back()->with('success', 'Use of Packing Materials was deleted successfully');
        }
        return redirect()->back();
    }
}
