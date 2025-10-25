<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\RmUse;
use App\Models\FoodType;
use App\Models\RmUseItemTemp;
use App\Models\RmUseItem;
use App\Models\RawMaterial;
use App\Models\RmItem;
use App\Models\RmDamage;

class FoodProductionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $page = 'Production Records';
        $title = 'Production Records';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
  
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $shop = Shop::find(Session::get('shop_id'));
        $rmuses = RmUse::where('rm_uses.shop_id', $shop->id)->where('is_deleted' , false)->whereBetween('date', [$start, $end])->join('users', 'users.id', '=', 'rm_uses.user_id')->join('food_types', 'food_types.id', '=', 'rm_uses.food_type_id')->select('rm_uses.id as id', 'rm_uses.total_cost as total_cost', 'rm_uses.date as date', 'users.first_name as first_name', 'users.last_name as last_name', 'rm_uses.created_at as created_at', 'name', 'rm_uses.prod_batch as prod_batch')->latest()->get();

        return view('production.food-prods.index', compact('page', 'title', 'shop', 'rmuses', 'is_post_query', 'start_date', 'end_date'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $page = 'New Production';
        $title = 'New Production';
        $foodtypes = FoodType::where('shop_id', $shop->id)->select('id', 'name')->get();
        $date = Carbon::now()->format('Y-m-d');
        return view('production.food-prods.create', compact('page', 'title', 'shop', 'settings', 'foodtypes', 'date'));

    }

    public function setFoodType(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $ftype = FoodType::find($request['ftype_id']);
        if (!is_null($ftype)) {
            session()->forget('ftype_id');
            session()->put('ftype_id', $ftype->id);
            $lastrecord = RmUse::where('shop_id', Session::get('shop_id'))->where('food_type_id', $ftype->id)->latest()->first();
            if (!is_null($lastrecord)) {
                $uitems = RmUseItem::where('rm_use_id', $lastrecord->id)->get();
                foreach ($uitems as $key => $value) {
                    $raw_material = $shop->rawMaterials()->where('raw_material_id', $value->raw_material_id)->where('is_deleted' , false)->first();
                    if (!is_null($raw_material)) {
                        $rmuItemTemp = RmUseItemTemp::where('raw_material_id', $value->raw_material_id)->where('user_id', $user->id)->where('shop_id', $shop->id)->where('is_food_production', true)->first();
                        if (is_null($rmuItemTemp)) {       
                            $rmuItemTemp = new RmUseItemTemp;
                            $rmuItemTemp->shop_id = $shop->id;
                            $rmuItemTemp->user_id = $user->id;
                            $rmuItemTemp->raw_material_id = $value->raw_material_id;
                            $rmuItemTemp->quantity  = $value->quantity;
                            $rmuItemTemp->unit_cost = is_null($raw_material->pivot->unit_cost) ? 0 : $raw_material->pivot->unit_cost ;
                            $rmuItemTemp->total = $rmuItemTemp->unit_cost*$rmuItemTemp->quantity;
                            $rmuItemTemp->is_food_production = true;
                            $rmuItemTemp->save();
                        }
                    }        
                }  
            }

            return response()->json(['success' => 1, 'msg' => 'Food Type selected successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Food Type not found']);
        }
    }

    public function cancel()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $puritems = RmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('is_food_production', true)->get();
        foreach ($puritems as $key => $value) {
            $value->delete();
        }

        Session::forget('ftype_id');

        return redirect('food-productions')->with('success', 'Production cancelled successfully');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $now = Carbon::now();
        if (!empty($request['date'])) {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $now = $request['date'] . ' ' . $time;
        }

        $uitems = RmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('is_food_production', true)->get();
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
                $maxbatch = RmUse::where('shop_id', $shop->id)->where('is_food_production', true)->max('prod_batch');
                $total_cost = 0;
                $rmuse = new RmUse();
                $rmuse->shop_id = $shop->id;
                $rmuse->user_id = $user->id;
                $rmuse->total_cost = $total_cost;
                $rmuse->comments = $request['comments'];
                $rmuse->date = $now;
                $rmuse->prod_batch = $maxbatch+1;
                $rmuse->is_food_production = true;
                $rmuse->food_type_id = $request['food_type_id'];
                $rmuse->save();

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

                $puritems = RmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('is_food_production', true)->get();
                foreach ($puritems as $key => $value) {
                    $value->delete();
                }

                Session::forget('ftype_id');

                return redirect('food-productions')->with('success', 'Food Product record created successfully');
            }
        }else{

            return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Food Production';
        $title = 'Food Production';

        $shop = Shop::find(Session::get('shop_id'));
        $rmuse = RmUse::where('rm_uses.id', decrypt($id))->join('users', 'users.id', '=', 'rm_uses.user_id')->join('food_types', 'food_types.id', '=', 'rm_uses.food_type_id')->select('rm_uses.id as id', 'total_cost', 'date', 'first_name', 'last_name', 'rm_uses.created_at as created_at', 'name', 'prod_batch', 'comments')->first();
        $uitems = RmUseItem::where('rm_use_id', $rmuse->id)->join('raw_materials', 'raw_materials.id', '=', 'rm_use_items.raw_material_id')->select('rm_use_items.id as id', 'rm_use_items.quantity as quantity', 'rm_use_items.unit_cost as unit_cost', 'rm_use_items.total as total', 'rm_use_items.date as date', 'raw_materials.name as name', 'raw_materials.basic_uom as basic_uom')->get();

        return view('production.food-prods.show', compact('page', 'title', 'rmuse', 'uitems', 'shop'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Food Production';
        $title = 'Edit Food Production';

        $shop = Shop::find(Session::get('shop_id'));
        $rmuse = RmUse::where('rm_uses.id', decrypt($id))->join('users', 'users.id', '=', 'rm_uses.user_id')->join('food_types', 'food_types.id', '=', 'rm_uses.food_type_id')->select('rm_uses.id as id', 'food_type_id', 'total_cost', 'date', 'first_name', 'last_name', 'rm_uses.created_at as created_at', 'name', 'prod_batch', 'comments')->first();
        $uitems = RmUseItem::where('rm_use_id', $rmuse->id)->join('raw_materials', 'raw_materials.id', '=', 'rm_use_items.raw_material_id')->select('rm_use_items.id as id', 'rm_use_items.quantity as quantity', 'rm_use_items.unit_cost as unit_cost', 'rm_use_items.total as total', 'rm_use_items.date as date', 'raw_materials.name as name', 'raw_materials.basic_uom as basic_uom')->get();

        $foodtypes = FoodType::where('shop_id', $shop->id)->select('id', 'name')->get();
        $materials = $shop->rawMaterials()->where('is_deleted' , false)->get([
            \DB::raw('raw_material_id as id'),
            \DB::raw('name'),
            \DB::raw('in_store'),
            \DB::raw('unit_cost'),
            \DB::raw('description')]);
        return view('production.food-prods.edit', compact('page', 'title', 'rmuse', 'uitems', 'shop', 'foodtypes', 'materials'));
    }

    public function addItem(Request $request)
    {
        $rmuse = RmUse::find($request['rm_use_id']);
        if (!is_null($rmuse)) {
            $useditem = RmUseItem::where('rm_use_id', $rmuse->id)->where('raw_material_id', $request['raw_material_id'])->first();
            if (is_null($useditem)) {
                $shop = Shop::find(Session::get('shop_id'));
                $raw_material = $shop->rawMaterials()->where('raw_material_id', $request['raw_material_id'])->where('is_deleted' , false)->first();
                if (!is_null($raw_material)) {
                    $useditem  = new RmUseItem;
                    $useditem->rm_use_id = $rmuse->id;
                    $useditem->raw_material_id = $raw_material->id;
                    $useditem->shop_id = $shop->id;
                    $useditem->quantity = $request['quantity'];
                    $useditem->unit_cost = is_null($raw_material->pivot->unit_cost) ? 0 : $raw_material->pivot->unit_cost;;
                    $useditem->total = $useditem->quantity*$useditem->unit_cost;
                    $useditem->date = $rmuse->date;
                    $useditem->save();

                    return redirect()->route('food-productions.edit', encrypt($rmuse->id))->with('success', 'Item added successfully');
                }else{
                    return redirect()->back()->with('error', 'Material not found');
                }
            }else{
                return redirect()->back()->with('error', 'Item already selected');
            }
        }else{
            return redirect()->back()->with('error', 'Item not found');
        }
    }


    public function updateRmUseItem(Request $request)
    {
        $useditem = RmUseItem::find($request['id']);
        if (!is_null($useditem)) {
            $useditem->quantity = $request['quantity'];
            $useditem->total = $useditem->quantity*$useditem->unit_cost;
            $useditem->save();

            return response()->json(['success' => 1, 'msg' => 'Item updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item Not found']);
        }

    }

    public function removeItem($id)
    {
        RmUseItem::destroy(decrypt($id));
        return redirect()->back()->with('success', 'Item removed successfully');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rmuse = RmUse::find(decrypt($id));
        if (!is_null($rmuse)) {
            $shop = Shop::find(Session::get('shop_id'));
            $total_cost = 0;
            $uitems = RmUseItem::where('rm_use_id', $rmuse->id)->get();
            foreach ($uitems as $key => $item) {
                $raw_material = RawMaterial::find($item->raw_material_id);
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

            $now = $rmuse->date;
            if ($request['date'] != $rmuse->date) {
                $timenow = Carbon::now();
                $time = date('H:i:s', strtotime($timenow));
                $now = $request['date'] . ' ' . $time;
            }

            $rmuse->date = $now;
            $rmuse->total_cost = $total_cost;
            $rmuse->comments = $request['comments'];
            $rmuse->save();

            return redirect()->route('food-productions.show', encrypt($rmuse->id))->with('success', 'Food Production updated successfully');
        }else{
            return redirect('food-productions')->with('error', 'Item not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rmuse = RmUse::find(decrypt($id));
        if (!is_null($rmuse)) {
            $shop = Shop::find($rmuse->shop_id);
            $rmuseitems = RmUseItem::where('rm_use_id', $rmuse->id)->get();
            foreach ($rmuseitems as $key => $item) {
                $raw_material = RawMaterial::find($item->raw_material_id);
                $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();
                $shop_raw_material->pivot->in_store = $shop_raw_material->pivot->in_store + $item->quantity;
                $shop_raw_material->pivot->save();
                $item->delete();
            }

            $rmuse->delete();

            return redirect()->back()->with('success', 'Item was deleted successfully');
        }else{
            return redirect()->back()->with('success', 'Item not found');
        }
    }
}
