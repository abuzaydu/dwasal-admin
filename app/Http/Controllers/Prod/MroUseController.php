<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;

use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Mro;
use App\Models\MroUsedItemTemp;
use App\Models\User;
use App\Models\MroUse;
use App\Models\MroItem;
use App\Models\Payment;

class MroUseController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'MRO Items';
        $title = 'New Use of MROs';
        $title_sw = 'Matumizi mapya ya MROs';
        $shop = Shop::find(Session::get('shop_id'));
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->first();
        
        // if (!is_null($payment)) {

        $last_prod_batch = MroUse::where('shop_id' , $shop->id)->where('is_deleted' , false)->max('prod_batch');
        $prod_batch = $last_prod_batch + 1;

        return view('production.mro.mro-uses.create', compact('page', 'title', 'title_sw', 'shop' , 'prod_batch'));
      // } else {
      //       $info = 'Dear customer your account is not activated please make payment and activate now.';
      //       // Alert::info("Payment Expired", $info);
      //       return redirect('verify-payment')->with('error', $info);
      //   }
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
        if (empty($request['mroused_date'])) {
            $now = Carbon::now();
        }else{
            $now = $request['mroused_date'];
        }


        $uitems = MroUsedItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();

        if (!is_null($uitems)) {
            $tempsq = array();
            $tempsc = array();

            foreach ($uitems as $key => $value) {
                if ($value->quantity == 0) {
                    array_push($tempsq, $value->quantity);
                    array_push($tempsc, $value->unit_cost);
                }
            }

            if (!empty($tempsc) && !empty($tempsq)) {
                return redirect()->back()->with('warning', 'Please update the quantity & unit_cost of each item to continue');
            }else{

                $total_cost = 0;
                $mrouse = MroUse::create([
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'total_cost' => $total_cost,
                    'comments' => $request['comments'],
                    'date' => $now,
                    'prod_batch' => $request['prod_batch'],
                ]);

                foreach ($uitems as $key => $item) {
                    $mro = mro::find($item->mro_id);
            
                    $mro = MroItem::create([
                        'mro_use_id' => $mrouse->id,
                        'mro_id' => $mro->id,
                        'shop_id' => $shop->id,
                        'qty' => $item->quantity,
                        'unit_cost' => $item->unit_cost,
                        'total' => $item->total,
                        'date' => $now,

                    ]);

                    $total_cost += $item->total;
                }

                $mrouse->total_cost = $total_cost;
                $mrouse->save();

                $puritems = MroUsedItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                foreach ($puritems as $key => $value) {
                    $value->delete();
                }

                return redirect()->back()->with('success', 'Use of Mro were added successfully');
            }
        }else{
            return redirect()->back()->with('warning', 'Please Select at least one Mro item to continue!.');
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
        $page = 'MRO Items';
        $title = 'Details for MRO Used';
        $title_sw = 'Maelezo ya Matumizi ya MROs';

        $shop = Shop::find(Session::get('shop_id'));
        $mrouse = MroUse::find(decrypt($id));
        $mro_used_items= MroItem::where('mro_use_id', $mrouse->id)->where('mro_items.is_deleted' , false)->join('mros', 'mros.id', '=', 'mro_items.mro_id')->select('mro_items.id as id', 'mro_items.qty as quantity', 'mro_items.unit_cost as unit_cost', 'mro_items.total as total', 'mro_items.date as date', 'mros.name as name')->get();
        $employee = User::find($mrouse->user_id);
        return view('production.mro.mro-uses.show', compact('page', 'title', 'title_sw', 'mrouse', 'mro_used_items', 'shop', 'employee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'MRO Uses';
         $title = 'Edit MRO Use';
         $title_sw = 'Hariri Matumizi ya MROs';
         $mro_use = MroUse::find(decrypt($id));

          return view('production.mro.mro-uses.edit', compact('page', 'title', 'title_sw', 'mro_use'));
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
         $mro_use = MroUse::find(decrypt($id));
          $mro_use->prod_batch = $request['prod_batch'];
          $mro_use->date = $request['date'];
          $mro_use->save();

          $mro_items = MroItem::where('mro_use_id' , $mro_use->id)->get();

          foreach($mro_items as $mro_item){
             $mro_item->date =  $request['date'];
             $mro_item->save();
          }

          return redirect('mro-items')->with('success' , "MRO Use Updated Successful");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mrouse = MroUse::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($mrouse)) {
            $mrouseitems = MroItem::where('mro_use_id', $mrouse->id)->get();
            foreach ($mrouseitems as $key => $item) {
                $item->is_deleted = true; 
                $item->save();           
            }

            $mrouse->is_deleted = true;
            $mrouse->save();

            return redirect()->back()->with('success', 'MRO uses  was deleted successfully');
        }
        
        return redirect()->back();
    }
}
