<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\Mro;
use App\Models\MohCost;
use App\Models\MohItemTemp;
use App\Models\MohItem;
use App\Models\MohItemPayment;
use App\Models\MohCostPayment;

class MohCostController extends Controller
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
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
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

        $duration = $start_date.' to '.$end_date;
        $page = 'MOH Costs';
        $title = 'Manufacturing Overhead(MOH) Costs';
        $title_sw = 'Manufacturing Overhead(MOH) Costs';
        $shop = Shop::find(Session::get('shop_id'));
        $mohcosts = MohCost::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->get();
        return view('production.moh-costs.index', compact('page', 'title', 'title_sw', 'mohcosts', 'duration', 'start_date', 'end_date', 'is_post_query'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $page = 'New Manufacturing Overhead(MOH) Costs';
        $title = 'New Manufacturing Overhead(MOH) Costs';
        $title_sw = '';

        return view('production.moh-costs.create', compact(['page', 'title', 'title_sw', 'shop', 'settings']));
    }

    public function cancel()
    {   
        $shop = Shop::find(Session::get('shop_id'));
        $itemtemps = MohItemTemp::where('shop_id', $shop->id)->where('user_id', Auth::user()->id)->get();
        foreach ($itemtemps as $key => $value) {
            $value->delete();
        }

        return redirect('moh-costs')->with('success', 'MOH Cost record Cancelled successfully');
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();

        $date = Carbon::now();
        if (!empty($request['date'])) {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $date = $request['date'] . ' ' . $time;
        }

        $maxno = MohCost::where('shop_id', $shop->id)->latest()->first();
        $moh_no = null;
        if (!is_null($maxno)) {
            $moh_no = $maxno->moh_no + 1;
        } else {
            $moh_no = 1;
        }

        $itemtemps = MohItemTemp::where('shop_id', $shop->id)->where('user_id', Auth::user()->id)->get();
        if ($itemtemps->count() > 0) {
            $moh = new MohCost();
            $moh->shop_id = $shop->id;
            $moh->user_id = $user->id;
            $moh->moh_no = $moh_no;
            $moh->date = $date;
            $moh->amount = 0;
            $moh->remarks = $request['remarks'];
            // $moh->production_cost_id = $request->input('production_cost_id');
            $moh->save();
            $amount = 0;
            foreach ($itemtemps as $key => $temp) {
                $item = new MohItem;
                $item->shop_id = $temp->shop_id;
                $item->user_id = $temp->user_id;
                $item->moh_cost_id = $moh->id;
                $item->mro_id = $temp->mro_id;
                $item->quantity = $temp->quantity;
                $item->unit_cost = $temp->unit_cost;
                $item->total = $item->quantity*$item->unit_cost;
                $item->save();

                $amount += $item->total;
            }

            $moh->amount = $amount;
            $moh->save();

            $this->checkPayments($moh);

            foreach ($itemtemps as $key => $value) {
                $value->delete();
            }
        }

        return redirect('moh-costs')->with('success', 'moh Cost record Created successfully');
    }

    public function checkPayments($moh)
    {
        $mohpays = MohCostPayment::where('shop_id', $moh->shop_id)->whereRaw('amount-utilized_amt > 0')->get();

        $amount_paid = 0;
        if ($mohpays->count() > 0) {
            $payamount = $moh->amount;
            foreach ($mohpays as $key => $mpay) {
                $rem_amount = $mpay->amount-$mpay->utilized_amt;
                $paid = 0;
                if ($payamount <= $rem_amount) {
                    $mohpay = new MohItemPayment();
                    $mohpay->moh_cost_id = $moh->id;
                    $mohpay->moh_cost_payment_id = $mpay->id;
                    $mohpay->pay_date = $mpay->pay_date;
                    $mohpay->paid_amt = $payamount;
                    $mohpay->save();

                    $mpay->utilized_amt = $mpay->utilized_amt+$payamount;
                    $mpay->is_utilized = false;
                    $mpay->save();

                    $paid = $payamount;
                }else{
                    $mohpay = new MohItemPayment();
                    $mohpay->moh_cost_id = $moh->id;
                    $mohpay->moh_cost_payment_id = $mpay->id;
                    $mohpay->pay_date = $mpay->pay_date;
                    $mohpay->paid_amt = $rem_amount;
                    $mohpay->save();

                    $mpay->utilized_amt = $mpay->amount;
                    $mpay->is_utilized = true;
                    $mpay->save();

                    $paid = $rem_amount;
                }

                $payamount -= $paid;

                $amount_paid += $paid;
            }
        }

        $moh->amount_paid = $moh->amount_paid+$amount_paid;
        $moh->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'MOH Costs';
        $title = 'Manufacturing Overhead(MOH) Costs';
        $shop = Shop::find(Session::get('shop_id'));
        $moh = MohCost::where('moh_costs.id', decrypt($id))->join('users', 'users.id', '=','moh_costs.user_id')->select('moh_costs.id as id', 'moh_no', 'date', 'amount', 'amount_paid', 'remarks', 'first_name', 'last_name')->first();
        if (!is_null($moh)) {
            $items = MohItem::where('moh_cost_id', $moh->id)->join('mros', 'mros.id','=', 'moh_items.mro_id')->select('moh_items.id as id', 'name', 'quantity', 'unit_cost', 'total')->get();

            $payments = MohItemPayment::where('moh_cost_id', $moh->id)->join('moh_cost_payments', 'moh_cost_payments.id','=', 'moh_item_payments.moh_cost_payment_id')->select('paid_amt', 'moh_cost_payments.pay_date as pay_date', 'pv_no',  'pay_mode')->get();
            return view('production.moh-costs.show', compact('page', 'title', 'shop', 'moh', 'items', 'payments'));
        }else{
            return redirect()->back()->with('info', 'moh Cost record not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $page = 'Production moh Costs';
        $title = 'Edit Daily Production moh Costs';
        $title_sw = '';

        $moh = MohCost::find(decrypt($id));
        if (!is_null($moh)) {
            $items = MohItem::where('moh_cost_id', $moh->id)->join('mros', 'mros.id','=', 'moh_items.mro_id')->select('moh_items.id as id', 'name', 'quantity', 'unit_cost', 'total')->get();
            $mros = Mro::where('shop_id', $shop->id)->select('id', 'name')->get();
            return view('production.moh-costs.edit', compact(['page', 'title', 'title_sw', 'shop', 'settings', 'moh', 'items', 'mros']));
        }else{
            return redirect()->back()->with('info', 'moh Cost record not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $date = Carbon::now();
        if (!empty($request['date'])) {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $date = $request['date'] . ' ' . $time;
        }
        $moh = MohCost::find(decrypt($id));
        if (!is_null($moh)) {
                
            $moh->date = $date;
            $moh->remarks = $request['remarks'];
            $moh->save();

            return redirect('moh-costs')->with('success', 'moh Cost record Created successfully');
        }else{
            return redirect('moh-costs')->with('error', 'moh Cost record not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        MohCost::destroy(decrypt($id));
        return redirect('moh-costs')->with('success', 'moh Cost deleted successfully');
    }
}
