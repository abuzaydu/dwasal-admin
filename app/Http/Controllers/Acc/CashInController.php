<?php

namespace App\Http\Controllers\Acc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\CashIn;
use App\Models\Account;
use App\Models\AccountStatement;

class CashInController extends Controller
{
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $indate = Carbon::now();
        if (!empty($request['in_date']) && $request['in_date'] != '') {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $indate = $request['in_date'].' '.$time;
        }
        $cashin = new CashIn();
        $cashin->shop_id = $shop->id;
        $cashin->account_id = $request['account_id'];
        $cashin->category = $request['category'];
        $cashin->amount = $request['amount'];
        $cashin->source = $request['source'];
        $cashin->in_date = $indate;
        $cashin->save();

        $astmt = new AccountStatement();
        $astmt->shop_id = $shop->id;
        $astmt->user_id = $user->id;
        $astmt->cash_in_id = $cashin->id;
        $astmt->account_id = $cashin->account_id;
        $astmt->date = $indate;
        $astmt->debit = $cashin->amount;
        $astmt->credit = 0;
        $astmt->description = $cashin->source;
        $astmt->save();

        return redirect('cash-flows')->with('success', 'Your Data recorded successfuly');
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
        $page = 'Cash Flows';
        $title = 'Edit Cash In';
        $title_sw = 'Hariri Pesa iliyotoka';
        $cashin = CashIn::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        $cins = CashIn::where('shop_id', $shop->id)->select('source')->groupBy('source')->orderBy('source')->get();
        $accounts = Account::where('shop_id', $shop->id)->get();
        $start_date = null;            
        $end_date = null;
              
        //check if user opted for date range
        $is_post_query = false;

        return view('accounting.cash-flows.edit-cash-in', compact('page', 'title', 'title_sw', 'cins', 'cashin', 'accounts', 'is_post_query', 'start_date', 'end_date'));
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
        $cashin = CashIn::find(decrypt($id));
        $indate = $cashin->in_date;
        if (!empty($request['in_date']) && $request['in_date'] != '') {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $indate = $request['in_date'].' '.$time;
        }

        $cashin->account_id = $request['account_id'];
        $cashin->category = $request['category'];
        $cashin->amount = $request['amount'];
        $cashin->source = $request['source'];
        $cashin->in_date = $indate;
        $cashin->save();

        $astmt = AccountStatement::where('cash_in_id', $cashin->id)->first();
        if (!is_null($astmt)) {
            $astmt->account_id = $cashin->account_id;
            $astmt->date = $indate;
            $astmt->debit = $cashin->amount;
            $astmt->credit = 0;
            $astmt->description = $cashin->source;
            $astmt->save();
        }

        return redirect('cash-flows')->with('success', 'Cash In updated successfuly');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cashin = CashIn::find(decrypt($id));
        if (!is_null($cashin)) {
            $astmt = AccountStatement::where('cash_in_id', $cashin->id)->first();
            if (!is_null($astmt)) {
                $astmt->delete();
            }
            $cashin->delete();
        }

        return redirect('cash-flows')->with('success', 'Cash In was deleted successfuly');
    }
}
