<?php

namespace App\Http\Controllers\Acc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use DB;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\PettyCash;
use App\Models\BankDetail;
use App\Models\ExpensePayment;
use App\Models\Account;
use App\Models\AccountStatement;
use App\Models\PlcPayment;

class PettyCashController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Petty Cash';
        $shop = Shop::find(Session::get('shop_id'));
        $pettycashs = PettyCash::where('shop_id', $shop->id)->where('status', '!=', 'Cancelled')->join('users', 'users.id', '=', 'petty_cashes.user_id')->select('petty_cashes.id as id', 'request_date', 'amount', 'first_name', 'last_name', 'status', 'approver', 'approved_at', 'received_date', 'ref_no', 'description')->orderBy('request_date', 'desc')->get();
        $cancelpettycashs = PettyCash::where('shop_id', $shop->id)->where('status', 'Cancelled')->join('users', 'users.id', '=', 'petty_cashes.user_id')->select('petty_cashes.id as id', 'request_date', 'amount', 'first_name', 'last_name', 'status', 'approver', 'approved_at', 'received_date', 'ref_no', 'description')->orderBy('request_date', 'desc')->get();
        $branch_pettycashes = [];
        if ($shop->is_hq) {
            $branch_pettycashes = PettyCash::where('hq_shop_id', $shop->id)->join('users', 'users.id', '=', 'petty_cashes.user_id')->select('petty_cashes.id as id', 'request_date', 'amount', 'first_name', 'last_name', 'status', 'approver', 'approved_at', 'received_date', 'ref_no', 'description')->orderBy('request_date', 'desc')->get();
        }

        return view('accounting.cash-flows.petty-cash.index', compact('page', 'shop', 'pettycashs', 'cancelpettycashs', 'branch_pettycashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'New Petty Cash';
        return view('accounting.cash-flows.petty-cash.create', compact('page'));
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
        $date = \Carbon\Carbon::now();

        if (!empty($request['request_date'])) {
            $date = $request['request_date'];
        }
        $hq = null;
        if ($request['is_from_hq']) {
            $hq = Shop::where('company_id', $shop->company_id)->where('is_hq', true)->first();
        }
        $pettycash = new PettyCash();
        $pettycash->shop_id = $shop->id;
        $pettycash->user_id = Auth::user()->id;
        $pettycash->request_date = $date;
        $pettycash->amount = str_replace(',', '', $request['amount']);
        $pettycash->description = $request['description'];
        if (!is_null($hq)) {
            $pettycash->is_from_hq = true;
            $pettycash->hq_shop_id = $hq->id;
        }
        $pettycash->save();

        return redirect('petty-cash')->with('success', 'Petty Cash added successful');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Petty Cash';
        $shop = Shop::find(Session::get('shop_id'));
        $petty = PettyCash::where('petty_cashes.id', decrypt($id))->join('users', 'users.id', '=', 'petty_cashes.user_id')->select('petty_cashes.id as id', 'shop_id', 'account_id', 'is_from_hq', 'request_date', 'amount', 'first_name', 'last_name', 'status', 'is_approved', 'approver', 'approved_at', 'issued_by', 'issued_date', 'received_date', 'ref_no', 'description', 'reject_reason')->first();
        $accounts = Account::where('shop_id', $shop->id)->get();
        $account = Account::find($petty->account_id);

        return view('accounting.cash-flows.petty-cash.show', compact('page', 'accounts', 'petty', 'account'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Petty Cash';
        $banks = BankDetail::all();
        $petty = PettyCash::find(decrypt($id));
        $shop = Shop::find($petty->shop_id);
        $accounts = Account::where('shop_id', $shop->id)->get();

        return view('accounting.cash-flows.petty-cash.edit', compact('page', 'banks', 'petty', 'accounts'));
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
        $user = Auth::user();
        $pettycash = PettyCash::find(decrypt($id));
        $pettycash->amount = str_replace(',', '', $request['amount']);
        $pettycash->description = $request['description'];
        $pettycash->save();

        if ($pettycash->is_from_hq && !empty($request['status'])) {
            $pettycash->pay_mode = 'Petty Cash';
            $pettycash->ref_no = $request['ref_no'];
            $pettycash->issued_by = Auth::user()->first_name.' '.Auth::user()->last_name;
            $pettycash->issued_date = Carbon::now();
            $pettycash->status = $request['status'];
            $pettycash->save();
        }

        $account = Account::find($request['account_id']);
        if (!is_null($account)) {
            $pettycash->account_id = $account->id;
            $pettycash->pay_mode = $account->type;
            $pettycash->ref_no = $request['ref_no'];
            if(!empty($request['status'])){
                $pettycash->issued_by = Auth::user()->first_name.' '.Auth::user()->last_name;
                $pettycash->issued_date = Carbon::now();
                $pettycash->status = $request['status'];
            }
            $pettycash->save();
            
            $astmt = new AccountStatement();
            $astmt->shop_id = $shop->id;
            $astmt->user_id = $user->id;
            $astmt->petty_cash_id = $pettycash->id;
            $astmt->account_id = $account->id;
            $astmt->date = Carbon::now();
            $astmt->debit = 0;
            $astmt->credit = $pettycash->amount;
            $astmt->description = $pettycash->description.' (Petty Cash)';
            $astmt->save();

        }

        return redirect('petty-cash')->with('success', 'Petty Cash updated successfully');
    }

    public function confirmReceived($id)
    {
        $pettycash = PettyCash::find(decrypt($id));
        if (!is_null($pettycash)) {
            if ($pettycash->user_id == Auth::user()->id) {
                $pettycash->received_date = Carbon::now();
                $pettycash->status = 'Received';
                $pettycash->save();

                return redirect('petty-cash')->with('success', 'Petty Cash Received successfully');
            }else{
                return redirect()->back()->with('info', 'You can only Receive the Petty Cash that you requested');
            }
        }else{
            return redirect()->back()->with('error', 'Petty Cash record not Found');
        }
    }

    public function approvePetty($id)
    {
        $pettycash = PettyCash::find(decrypt($id));
        $pettycash->is_approved = true;
        $pettycash->approver = Auth::user()->first_name.' '.Auth::user()->last_name;
        $pettycash->approved_at = Carbon::now();
        $pettycash->status = 'Approved';
        $pettycash->save();

        return redirect('petty-cash')->with('success', 'Petty Cash approved successful');
    }

    public function rejectPettyCash(Request $request)
    {
        $pettycash = PettyCash::find($request['id']);
        $pettycash->approver = Auth::user()->first_name;
        $pettycash->approved_at = Carbon::now();
        $pettycash->status = 'Rejected';
        $pettycash->reject_reason = $request['reject_reason'];
        $pettycash->save();

        return redirect('petty-cash')->with('success', 'Petty Cash approved successful');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pettycash = PettyCash::find(decrypt($id));
        if (!is_null($pettycash)) {
            if ($pettycash->status == 'Received') {
                $pettycash->is_deleted = true;
                $pettycash->status = 'Cancelled';
                $pettycash->save();
            }else{
                $pettycash->delete();
            }
        }

        return redirect('petty-cash')->with('success', 'pettycash deleted successfully');
    }

    public function pettyCashReport(Request $request)
    {
        $page = 'Petty Cash Report';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }
        $duration = 'From '.date('d-m-Y', strtotime($start)).' to '.date('d-m-Y', strtotime($end));
        $pettycash = PettyCash::where('shop_id', $shop->id)->whereBetween('request_date', [$start, $end])->where('status', 'Received')->get();
        $branch_pettycashes = PettyCash::where('hq_shop_id', $shop->id)->whereBetween('request_date', [$start, $end])->where('status', 'Received')->join('shops', 'shops.id', '=', 'petty_cashes.shop_id')->groupBy('name')->get([
            DB::raw('name'),
            DB::raw('SUM(amount) as amount')
        ]);

        $expenses = ExpensePayment::where('expense_payments.shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Petty Cash')->join('expenses', 'expenses.id', '=', 'expense_payments.expense_id')->groupBy('expense_type')->get([
            DB::raw('expense_type as expense_type'),
            DB::raw('SUM(expense_payments.amount) as amount')
        ]);
        
        $plc_payments = PlcPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Petty Cash')->sum('amount');
        
        return view('accounting.cash-flows.petty-cash.report', compact('page', 'shop', 'settings', 'pettycash', 'branch_pettycashes', 'expenses', 'plc_payments', 'duration', 'is_post_query', 'start_date', 'end_date'));
    }
}
