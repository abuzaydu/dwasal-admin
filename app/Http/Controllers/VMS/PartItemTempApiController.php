<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Response;
use Session;
use Auth;
use Log;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\Shop;
use App\Models\User;
use App\Models\part;
use App\Models\ShopCurrency;
use App\Models\Vendor;
use App\Models\PartPurchaseTemp;
use App\Models\PartPurchaseItemTemp;

class PartItemTempApiController extends Controller
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

    public function updatePartPurchaseTemp(Request $request)
    {
        $partPurchaseTemp = PartPurchaseTemp::find($request['id']);
        if (!is_null($partPurchaseTemp)) {
                
            $local_ex_rate = 1;
            $foreign_ex_rate = 1;
            $ex_rate = 1;
            if ($request['currency'] != $partPurchaseTemp->defcurr) {
                if ($request['ex_rate_mode'] == 'Foreign') {
                    $local_ex_rate = $request['local_ex_rate'];
                    $ex_rate = 1/$local_ex_rate;
                }else{
                    $foreign_ex_rate = $request['foreign_ex_rate'];
                    if ($foreign_ex_rate != 0) {
                        $ex_rate = $foreign_ex_rate;
                    }
                }
            }

            $partPurchaseTemp->vendor_id = $request['vendor_id'];
            $partPurchaseTemp->pp_date = $request['pp_date'];
            $partPurchaseTemp->purchase_type = $request['purchase_type'];
            $partPurchaseTemp->pay_type = $request['pay_type'];
            $partPurchaseTemp->currency = $request['currency'];
            $partPurchaseTemp->ex_rate_mode = $request['ex_rate_mode'];
            $partPurchaseTemp->local_ex_rate = $local_ex_rate;
            $partPurchaseTemp->foreign_ex_rate = $foreign_ex_rate;
            $partPurchaseTemp->ex_rate = $ex_rate;
            $partPurchaseTemp->comments = $request['comments'];
            $partPurchaseTemp->save();

            return $partPurchaseTemp;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {   
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $vendors = Vendor::where('company_id', $company->id)->where('vendor_for', 'Parts')->select('id','vendor_name as name')->get();
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $partPurchaseTemp = PartPurchaseTemp::find($id);
        if (!is_null($partPurchaseTemp)) {
                
            $itemtemps = PartPurchaseItemTemp::where('part_purchase_temp_id', $partPurchaseTemp->id)->join('parts', 'parts.id', '=', 'part_purchase_item_temps.part_id')->join('part_categories', 'part_categories.id', '=', 'parts.part_category_id')->select('part_purchase_item_temps.id as id', 'part_purchase_temp_id', 'part_id', 'name as category', 'part_no', 'part_name', 'pp_qty', 'part_purchase_item_temps.unit_price as unit_price', 'total_price')->get();
            $temps = array();
            foreach ($itemtemps as $key => $temp) {
                array_push($temps, [
                    'id' => $temp->id,
                    'part_purchase_temp_id' => $temp->part_purchase_temp_id,
                    'part_id' => $temp->part_id,
                    'category' => $temp->category,
                    'part_no' => $temp->part_no,
                    'part_name' => $temp->part_name,
                    'pp_qty' => $temp->pp_qty,
                    'unit_price' => round($temp->unit_price*$partPurchaseTemp->ex_rate, 2),
                    'total_price' => round($temp->total_price*$partPurchaseTemp->ex_rate, 2)
                ]);
            }
            // Log::info($temps);
            return Response::json(['purchasetemp' => $partPurchaseTemp, 'vendors' => $vendors, 'currencies' => $currencies, 'items' =>$temps]);
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
        // $company = Shop::find(Session::get('company_id'));
        $partPurchaseTemp = PartPurchaseTemp::find($request['part_purchase_temp_id']);
        $sameitems = PartPurchaseItemTemp::where('part_id', $request['part_id'])->where('part_purchase_temp_id', $partPurchaseTemp->id)->count();
        
        if ($sameitems == 0) {
            $part = Part::find($request['part_id']);
            if (!is_null($part)) {
                $partItemTemp = new PartPurchaseItemTemp;
                $partItemTemp->part_purchase_temp_id = $partPurchaseTemp->id;
                $partItemTemp->part_id = $part->id;
                $partItemTemp->part_category_id = $part->part_category_id;
                $partItemTemp->pp_qty  = 0;
                $partItemTemp->unit_price = $part->unit_price;
                $partItemTemp->total_price = 0;
                $partItemTemp->save();

                return $partItemTemp;
            }
        }else{
            $warning = 'Ooops!. The part already in selected items.';
            return response()->json(['status' =>'DUPL', 'msg' => $warning]);
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
        // Log::info($request);
        $partItemTemp =  PartPurchaseItemTemp::find($id);
        if (!is_null($partItemTemp)) {
            $partPurchaseTemp = PartPurchaseTemp::find($partItemTemp->part_purchase_temp_id);
            if ($partItemTemp->pp_qty != $request['pp_qty']) {
                $part = Part::find($partItemTemp->part_id);
                $partItemTemp->pp_qty  = $request['pp_qty'];
                $partItemTemp->total_price = (float)$partItemTemp->pp_qty*(float)$partItemTemp->unit_price;
                $partItemTemp->save();

                return $partItemTemp;
            }else{
                if($partItemTemp->unit_price != round((float)$request['unit_price']/(float)$partPurchaseTemp->ex_rate,2)) {
                    if ($partPurchaseTemp->currency != $partPurchaseTemp->defcurr) {
                        $partItemTemp->unit_price = (float)$request['unit_price']/(float)$partPurchaseTemp->ex_rate;     
                    }else{
                        $partItemTemp->unit_price = $request['unit_price'];
                    }
                    $partItemTemp->total_price = (float)$partItemTemp->pp_qty*(float)$partItemTemp->unit_price;
                    $partItemTemp->save();

                    return $partItemTemp;
                }else{
                    if ($partPurchaseTemp->currency != $partPurchaseTemp) {
                        $partItemTemp->total_price = $request['total_price']/$partPurchaseTemp->ex_rate;
                    }else{
                        $partItemTemp->total_price = $request['total_price'];
                    }
                    if ($partItemTemp->pp_qty > 0) {
                        $partItemTemp->unit_price = $partItemTemp->total_price/$partItemTemp->pp_qty;
                    }
                    $partItemTemp->save();
                    return $partItemTemp;   
                }
            }
        }
    }

    function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PartPurchaseItemTemp::destroy($id);
    }

    public function cancelPurchase($id)
    {
        $partPurchaseTemp = PartPurchaseTemp::find(decrypt($id));
        if (!is_null($partPurchaseTemp)) {
            $items = PartPurchaseItemTemp::where('part_purchase_temp_id', $partPurchaseTemp->id)->get();
            foreach ($items as $key => $item) {
                $item->delete();
            }
            $partPurchaseTemp->delete();
        }

        return redirect()->route('part-purchases.create')->with('success', 'Purchase cancelled successfully');
    }
}
