<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\PayrollDeduction;
use App\Models\Account;

class PayrollDeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
    {
        $this->middleware(['auth']);
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Payroll Deductions';

        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }
        
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $accounts = Account::where('shop_id', Session::get('shop_id'))->get();
        $deductions = PayrollDeduction::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->get();
        // return $deductions;
        return view('payroll-deductions.index', compact('page', 'settings', 'accounts', 'start_date', 'end_date', 'is_post_query', 'deductions'));   
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Payroll Deduction Details';
        $deduction = PayrollDeduction::find(decrypt($id));
        if (!is_null($deduction)) {
            $accounts = Account::where('shop_id', Session::get('shop_id'))->get();
            return view('payroll-deductions.show', compact('page', 'deduction', 'accounts'));
        }else{
            return redirect('payroll-deductions')->with('error', 'Deduction not Found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
