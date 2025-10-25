<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use App\Models\Mro;
use App\Models\MroItem;
use App\Models\MroUse;
use App\Models\Shop;

class MROItemController extends Controller
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
    public function index(Request $request)
    {
        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
  
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['stock_date'])) {
            $start_date = $request['stock_date'];
            $end_date = $request['stock_date'];
            $start = $request['stock_date'].' 00:00:00';
            $end = $request['stock_date'].' 23:59:59';
            $is_post_query = true;
        }else if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $expired = Session::get('expired');
        $page = 'MRO Items';
        $title = 'MRO USED';
        $title_sw = 'MRO ZILIZOTUMIKA';

        // if ($expired == 0) {
            $shop = Shop::find(Session::get('shop_id'));

            $mro_used_items = $shop->mroItems()->where('mro_items.is_deleted' , false)->whereBetween('mro_items.date', [$start, $end])->join('mros' , 'mros.id' , '=' , 'mro_items.mro_id')->latest('mro_items.created_at')->get([
                \DB::raw('mro_items.id as id'),
                \DB::raw('qty as qty'),
                \DB::raw('unit_cost as unit_cost'),
                \DB::raw('total as total'),
                \DB::raw('name as name'),
                \DB::raw('date as date'),
                \DB::raw('mro_items.created_at as created_at'),
            ]);

            $mrouses = $shop->mroUse()->where('is_deleted' , false)->whereBetween('mro_uses.date', [$start, $end])->latest()->get();

            return view('production.mro.mro-uses.index', compact('page', 'title', 'title_sw', 'mrouses', 'mro_used_items' , 'shop' , 'start' , 'end' , 'is_post_query' , 'start_date' , 'end_date'));

        // } else {
        //     $info = 'Dear customer your account is not activated please make payment and activate now.';
        //     return redirect('verify-payment')->with('error', $info);
        // } 
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
        //
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
        $page = 'MRO Items';
        $title = 'Edit MRO Used Item';
        $title_sw = 'Hariri MRO hii';
        $mro_item = MroItem::where('mro_items.id' , decrypt($id))->join('mros' , 'mros.id' , '=' , 'mro_items.mro_id')->get([
               \DB::raw('mro_items.id as id'),
                \DB::raw('qty as qty'),
                \DB::raw('unit_cost as unit_cost'),
                \DB::raw('total as total'),
                \DB::raw('name as name'),
                \DB::raw('date as date'),
        ])->first();

      
        return view('production.mro.mro-uses.edit-item' , compact(['page' , 'title' , 'title_sw' , 'mro_item']));
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
         $mro_item = MroItem::find(decrypt($id));

         $mro_item->qty = $request['qty'];
         $mro_item->unit_cost = $request['unit_cost'];
         $mro_item->total = ($request['qty'] * $request['unit_cost']);
         $mro_item->save();

         $total_cost = MroItem::where('shop_id' , $shop->id)->where('is_deleted' , false)->where('mro_use_id' , $mro_item->mro_use_id)->sum('total');

         $mro_use = MroUse::find($mro_item->mro_use_id);
         $mro_use->total_cost = $total_cost;
         $mro_use->save();

         $message = "MRO Updated Successful";
         return redirect('mro-items')->with('success' , $message);
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
        $mro_used_item = MroItem::find(decrypt($id));
        $mro_used_item->is_deleted = true;
        $mro_used_item->save();
        

         $total_cost = MroItem::where('shop_id' , $shop->id)->where('is_deleted' , false)->where('mro_use_id' , $mro_used_item->mro_use_id)->sum('total');

         $mro_use = MroUse::find($mro_used_item->mro_use_id);
         $mro_use->total_cost = $total_cost;
         $mro_use->save();

        


        return redirect()->back()->with('success' , 'Mro Used Item Was deleted successfully');

    }
}
