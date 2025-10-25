<?php

namespace App\Http\Controllers\Acc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use \Carbon\Carbon;
use Auth;
use App\Models\Shop;
use App\Models\CashOut;
use App\Models\AnSale;
use App\Models\SalePayment;
use App\Models\AccountTransaction;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\CashIn;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Account;
use App\Models\AccountStatement;

class CashOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Cash Flows';
        $title = 'Cash Flows';
        $title_sw = 'Mtiririko wa Fedha';
        $shop = Shop::find(Session::get('shop_id'));

        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
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
                
        $cashouts = CashOut::where('shop_id', $shop->id)->whereBetween('out_date', [$start, $end])->get();
        $cashins = CashIn::where('shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->get();
        
        $salescashins = AnSale::where('an_sales.shop_id', $shop->id)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('sale_payments.is_deleted', false)->whereBetween('pay_date', [$start, $end])->groupBy('sale_payments.pay_mode')->groupBy('sale_payments.pay_date')->orderBy('pay_date', 'desc')->get([
            \DB::raw('pay_mode as pay_mode'),
            \DB::raw('SUM(amount) as amount'),
            \DB::raw('DATE(pay_date) as pay_date')
        ]);

        $expcashouts = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->whereBetween('pay_date', [$start, $end])->groupBy('pay_date')->get([
            \DB::raw('SUM(expense_payments.amount) as amount'),
            \DB::raw('DATE(pay_date) as pay_date')
        ]);

        $purchcashouts = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->whereBetween('pay_date', [$start, $end])->groupBy('purchase_payments.pay_date')->orderBy('pay_date', 'desc')->get([
            \DB::raw('SUM(amount) as amount'),
            \DB::raw('DATE(pay_date) as pay_date')
        ]);


        $couts = CashOut::where('shop_id', $shop->id)->select('reason')->groupBy('reason')->orderBy('reason', 'asc')->get();

        $acctransactions = AccountTransaction::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->get();
        $customers = Customer::where('shop_id', $shop->id)->get();

        $accounts = Account::where('shop_id', $shop->id)->select('id', 'account_name', 'account_number')->get();
        return view('accounting.cash-flows.index', compact('page', 'title', 'title_sw', 'shop', 'accounts', 'cashouts', 'cashins', 'salescashins', 'couts', 'acctransactions', 'is_post_query', 'start_date', 'end_date', 'purchcashouts', 'expcashouts', 'customers'));
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
        $user = Auth::user();
        $shop = Shop::find(Session::get('shop_id'));
        if ($request['is_borrowed'] == 'Yes' && empty($request['customer_id'])) {
            return redirect()->back()->with('errors', 'You must specify a customer borrowed this amount.');
        }else{
            $is_borrowed = false;
            if ($request['is_borrowed'] == 'Yes') {
                $is_borrowed = true;
            }
            $outdate = Carbon::now();
            if (!empty($request['out_date'])) {
                $timenow = Carbon::now();
                $time = date('H:i:s', strtotime($timenow));
                $outdate = $request['out_date'].' '.$time;
            }
            $customer_id = null;
            if (!empty($request['customer_id'])) {
                $customer_id = $request['customer_id'];
            }

            // return $customer_id;
            $cashout = new CashOut();
            $cashout->shop_id = $shop->id;
            $cashout->account_id = $request['account_id'];
            $cashout->category = $request['category'];
            $cashout->amount = $request['amount'];
            $cashout->reason = $request['reason'];
            $cashout->out_date = $outdate;
            $cashout->is_borrowed = $is_borrowed;
            $cashout->status = 'Pending';
            $cashout->customer_id = $customer_id;
            $cashout->save();

            $astmt = new AccountStatement();
            $astmt->shop_id = $shop->id;
            $astmt->user_id = $user->id;
            $astmt->account_id = $cashout->account_id;
            $astmt->cash_out_id = $cashout->id;
            $astmt->date = $cashout->out_date;
            $astmt->debit = 0;
            $astmt->credit = $cashout->amount;
            $astmt->description = $cashout->reason;
            $astmt->save();

            if ($cashout) {
                if (!is_null($cashout->customer_id)) {
                    $acctrans = new CustomerTransaction();
                    $acctrans->shop_id = $shop->id;
                    $acctrans->user_id = Auth::user()->id;
                    $acctrans->customer_id = $cashout->customer_id;
                    $acctrans->cash_out_id = $cashout->id;
                    $acctrans->amount = $cashout->amount;
                    $acctrans->date = $cashout->out_date;
                    $acctrans->save();
                }
            }
            return redirect('cash-flows')->with('success', 'Your Data recorded successfuly');
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
        $page = 'Cash Flows';
        $title = 'Cash Out';
        $title_sw = 'Pesa iliyotoka';
        $cashout = CashOut::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        $customer = null;
        if (!is_null($cashout->customer_id)) {
            $customer = Customer::find($cashout->customer_id)->name;
        }
        $is_post_query =false;
        $start_date = null;
        $end_date = null;

        return view('accounting.cash-flows.show', compact('page', 'title', 'title_sw', 'customer', 'cashout', 'is_post_query', 'start_date', 'end_date'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Cash Flows';
        $title = 'Edit Cash Out';
        $title_sw = 'Hariri Pesa iliyotoka';
        $cashout = CashOut::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        $couts = CashOut::where('shop_id', $shop->id)->select('reason')->groupBy('reason')->orderBy('reason')->get();
        $accounts = Account::where('shop_id', $shop->id)->get();
        $is_post_query =false;
        $start_date = null;
        $end_date = null;
        return view('accounting.cash-flows.edit', compact('page', 'title', 'title_sw', 'couts', 'cashout', 'is_post_query', 'start_date', 'end_date', 'accounts'));
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
        $cashout = CashOut::find(decrypt($id));
        $outdate = Carbon::now();
        if (!empty($request['out_date'])) {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $outdate = $request['out_date'].' '.$time;
        }
        $cashout->account_id = $request['account_id'];
        $cashout->category = $request['category'];
        $cashout->amount = $request['amount'];
        $cashout->reason = $request['reason'];
        $cashout->out_date = $outdate;
        $cashout->save();

        $astmt = AccountStatement::where('cash_out_id', $cashout->id)->first();
        if (!is_null($astmt)) {
            $astmt->account_id = $cashout->account_id;
            $astmt->cash_out_id = $cashout->id;
            $astmt->date = $cashout->out_date;
            $astmt->debit = 0;
            $astmt->credit = $cashout->amount;
            $astmt->description = $cashout->reason;
            $astmt->save();
        }
        if (!is_null($cashout->customer_id)) {
            $acctrans = CustomerTransaction::where('invoice_no', 'CO_'.$cashout->id)->where('customer_id', $cashout->customer_id)->where('shop_id', $shop->id)->first();
            if (!is_null($acctrans)) {
                $acctrans->amount = $cashout->amount;
                $acctrans->date = $cashout->out_date;
                $acctrans->save();
            }
        }

        return redirect('cash-flows')->with('success', 'Cash Out updated successfuly');
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
        $cashout = CashOut::find(decrypt($id));
        if (!is_null($cashout)) {
            $cashout->delete();

            if (!is_null($cashout->customer_id)) {
                $acctrans = CustomerTransaction::where('invoice_no', 'CO_'.$cashout->id)->where('customer_id', $cashout->customer_id)->where('shop_id',$shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->delete();
                }
            }
        }

        return redirect('cash-flows')->with('success', 'Cash Out was deleted successfuly');
    }
}
