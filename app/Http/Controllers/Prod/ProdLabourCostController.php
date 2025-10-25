<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\ProductionStage;
use App\Models\ProdLabourCost;
use App\Models\PlcItemTemp;
use App\Models\PlcItem;
use App\Models\PlcItemPayment;
use App\Models\PlcPayment;

class ProdLabourCostController extends Controller
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
        $page = 'Labour Costs';
        $title = 'Labour Costs';
        $title_sw = 'Labour Costs';
        $shop = Shop::find(Session::get('shop_id'));
        $labourcosts = ProdLabourCost::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->get();
        return view('production.labour-costs.index', compact('page', 'title', 'title_sw', 'labourcosts', 'duration', 'start_date', 'end_date', 'is_post_query'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $page = 'Production Labour Costs';
        $title = 'Daily Production Labour Costs';
        $title_sw = '';

        return view('production.labour-costs.create', compact(['page', 'title', 'title_sw', 'shop', 'settings']));
    }

    public function cancel()
    {   
        $shop = Shop::find(Session::get('shop_id'));
        $itemtemps = PlcItemTemp::where('shop_id', $shop->id)->where('user_id', Auth::user()->id)->get();
        foreach ($itemtemps as $key => $value) {
            $value->delete();
        }

        return redirect('prod-labour-costs')->with('success', 'Labour Cost record Cancelled successfully');
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

        $maxno = ProdLabourCost::where('shop_id', $shop->id)->latest()->first();
        $plc_no = null;
        if (!is_null($maxno)) {
            $plc_no = $maxno->plc_no + 1;
        } else {
            $plc_no = 1;
        }

        $itemtemps = PlcItemTemp::where('plc_item_temps.shop_id', $shop->id)->where('user_id', Auth::user()->id)->get();
        if ($itemtemps->count() > 0) {
            $plc = new ProdLabourCost();
            $plc->shop_id = $shop->id;
            $plc->user_id = $user->id;
            $plc->plc_no = $plc_no;
            $plc->date = $date;
            $plc->amount = 0;
            $plc->remarks = $request['remarks'];
            $plc->save();
            $amount = 0;
            foreach ($itemtemps as $key => $temp) {
                $item = new PlcItem;
                $item->shop_id = $temp->shop_id;
                $item->user_id = $temp->user_id;
                $item->prod_labour_cost_id = $plc->id;
                $item->production_stage_id = $temp->production_stage_id;
                $item->quantity = $temp->quantity;
                $item->unit_cost = $temp->unit_cost;
                $item->total = $item->quantity*$item->unit_cost;
                $item->save();

                $amount += $item->total;
            }

            $plc->amount = $amount;
            $plc->save();

            $this->checkPayments($plc);

            foreach ($itemtemps as $key => $value) {
                $value->delete();
            }
        }

        return redirect('prod-labour-costs')->with('success', 'Labour Cost record Created successfully');
    }

    public function checkPayments($plc)
    {
        $plcpays = PlcPayment::where('shop_id', $plc->shop_id)->whereRaw('amount-utilized_amt > 0')->get();

        $amount_paid = 0;
        if ($plcpays->count() > 0) {
            $payamount = $plc->amount;
            foreach ($plcpays as $key => $mpay) {
                $rem_amount = $mpay->amount-$mpay->utilized_amt;
                $paid = 0;
                if ($payamount <= $rem_amount) {
                    $plcpay = new PlcItemPayment();
                    $plcpay->prod_labour_cost_id = $plc->id;
                    $plcpay->plc_payment_id = $mpay->id;
                    $plcpay->pay_date = $mpay->pay_date;
                    $plcpay->paid_amt = $payamount;
                    $plcpay->save();

                    $mpay->utilized_amt = $mpay->utilized_amt+$payamount;
                    $mpay->is_utilized = false;
                    $mpay->save();

                    $paid = $payamount;
                }else{
                    $plcpay = new PlcItemPayment();
                    $plcpay->prod_labour_cost_id = $plc->id;
                    $plcpay->plc_payment_id = $mpay->id;
                    $plcpay->pay_date = $mpay->pay_date;
                    $plcpay->paid_amt = $rem_amount;
                    $plcpay->save();

                    $mpay->utilized_amt = $mpay->amount;
                    $mpay->is_utilized = true;
                    $mpay->save();

                    $paid = $rem_amount;
                }

                $payamount -= $paid;

                $amount_paid += $paid;
            }
        }

        $plc->amount_paid = $plc->amount_paid+$amount_paid;
        $plc->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Production Labour Costs';
        $title = 'Production Labour Costs';
        $shop = Shop::find(Session::get('shop_id'));
        $plc = ProdLabourCost::where('prod_labour_costs.id', decrypt($id))->join('users', 'users.id', '=','prod_labour_costs.user_id')->select('prod_labour_costs.id as id', 'plc_no', 'date', 'amount', 'amount_paid', 'remarks', 'first_name', 'last_name')->first();
        if (!is_null($plc)) {
            $items = PlcItem::where('prod_labour_cost_id', $plc->id)->join('production_stages', 'production_stages.id','=', 'plc_items.production_stage_id')->select('plc_items.id as id', 'stage', 'quantity', 'unit_cost', 'total')->get();

            $payments = PlcItemPayment::where('prod_labour_cost_id', $plc->id)->join('plc_payments', 'plc_payments.id','=', 'plc_item_payments.plc_payment_id')->select('paid_amt', 'plc_payments.pay_date as pay_date', 'pv_no',  'pay_mode')->get();
            return view('production.labour-costs.show', compact('page', 'title', 'shop', 'plc', 'items', 'payments'));
        }else{
            return redirect()->back()->with('info', 'Labour Cost record not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $page = 'Production Labour Costs';
        $title = 'Edit Daily Production Labour Costs';
        $title_sw = '';

        $plc = ProdLabourCost::find(decrypt($id));
        if (!is_null($plc)) {
            $items = PlcItem::where('prod_labour_cost_id', $plc->id)->join('production_stages', 'production_stages.id','=', 'plc_items.production_stage_id')->select('plc_items.id as id', 'stage', 'quantity', 'unit_cost', 'total')->get();
            $stages = ProductionStage::where('shop_id', $shop->id)->select('id', 'stage')->get();
            return view('production.labour-costs.edit', compact(['page', 'title', 'title_sw', 'shop', 'settings', 'plc', 'items', 'stages']));
        }else{
            return redirect()->back()->with('info', 'Labour Cost record not found');
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
        $plc = ProdLabourCost::find(decrypt($id));
        if (!is_null($plc)) {
                
            $plc->date = $date;
            $plc->remarks = $request['remarks'];
            $plc->save();

            return redirect('prod-labour-costs')->with('success', 'Labour Cost record Created successfully');
        }else{
            return redirect('prod-labour-costs')->with('error', 'Labour Cost record not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $plc = ProdLabourCost::find(decrypt($id));
        if (!is_null($plc)) {
            $plcitems = PlcItem::where('prod_labour_cost_id', $plc->id)->get();
            foreach ($plcitems as $key => $value) {
                $value->delete();
            }

            $plcpays = PlcItemPayment::where('prod_labour_cost_id', $plc->id)->get();
            foreach ($plcpays as $key => $value) {
                $value->delete();
            }
            $plc->delete();
            return redirect('prod-labour-costs')->with('success', 'Labour Cost deleted successfully');
        }else{
            return redirect()->back()->with('error', 'Item not found');
        }
    }
}
