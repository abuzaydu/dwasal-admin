<?php

namespace App\Http\Controllers\Acc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\AccountTransaction;
use App\Models\BankDetail;
use App\Models\Account;
use App\Models\AccountStatement;

class AccountTransController extends Controller
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

        $date = Carbon::now();
        if (!empty($request['date']) && $request['date'] != '') {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $date = $request['date'].' '.$time;
        }

        $fromacc = Account::find($request['from']);
        $toacc = Account::find($request['to']);
        $acctrans = new AccountTransaction();
        $acctrans->shop_id = $shop->id;
        $acctrans->user_id = $user->id;
        $acctrans->from_acc_id = $fromacc->id;
        $acctrans->to_acc_id = $toacc->id;
        $acctrans->from = $fromacc->type;
        $acctrans->to = $toacc->type;
        $acctrans->amount = $request['amount'];
        $acctrans->reason = $request['reason'];
        $acctrans->date = $date;
        $acctrans->save();

        $astmt = new AccountStatement();
        $astmt->shop_id = $shop->id;
        $astmt->user_id = $user->id;
        $astmt->account_transaction_id = $acctrans->id;
        $astmt->account_id = $fromacc->id;
        $astmt->date = $date;
        $astmt->debit = 0;
        $astmt->credit = $acctrans->amount;
        $astmt->description = $acctrans->reason;
        $astmt->save();

        $astmt = new AccountStatement();
        $astmt->shop_id = $shop->id;
        $astmt->user_id = $user->id;
        $astmt->account_transaction_id = $acctrans->id;
        $astmt->account_id = $toacc->id;
        $astmt->date = $date;
        $astmt->debit = $acctrans->amount;
        $astmt->credit = 0;
        $astmt->description = $acctrans->reason;
        $astmt->save();

        return redirect('cash-flows')->with('success', 'Transaction recorded successfully');
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
        $title = 'Edit Account Transaction';
        $title_sw = 'Hariri Muamala wa akaunti';
        $shop = Shop::find(Session::get('shop_id'));
        $acctrans = AccountTransaction::find(decrypt($id));
        $accounts = Account::where('shop_id', $shop->id)->get();
        
        $is_post_query = false;
        $start_date = null;
        $end_date = null;

        return view('accounting.cash-flows.edit-trans', compact('page', 'title', 'title_sw', 'acctrans', 'accounts', 'is_post_query', 'start_date', 'end_date',));
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
        $date = Carbon::now();
        if (!empty($request['date'])) {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $date = $request['date'].' '.$time;
        }

        $fromacc = Account::find($request['from']);
        $toacc = Account::find($request['to']);
        $acctrans = AccountTransaction::find(decrypt($id));
        $acctrans->from_acc_id = $fromacc->id;
        $acctrans->to_acc_id = $toacc->id;
        $acctrans->from = $fromacc->type;
        $acctrans->to = $toacc->type;
        $acctrans->amount = $request['amount'];
        $acctrans->reason = $request['reason'];
        $acctrans->date = $date;
        $acctrans->save();

        $trans_stmts = AccountStatement::where('account_transaction_id', $acctrans->id)->get();
        foreach ($trans_stmts as $key => $value) {
            $value->delete();
        }

        $astmt = new AccountStatement();
        $astmt->shop_id = $acctrans->shop_id;
        $astmt->user_id = $acctrans->user_id;
        $astmt->account_transaction_id = $acctrans->id;
        $astmt->account_id = $fromacc->id;
        $astmt->date = $date;
        $astmt->debit = 0;
        $astmt->credit = $acctrans->amount;
        $astmt->description = $acctrans->reason;
        $astmt->save();

        $astmt = new AccountStatement();
        $astmt->shop_id = $acctrans->shop_id;
        $astmt->user_id = $acctrans->user_id;
        $astmt->account_transaction_id = $acctrans->id;
        $astmt->account_id = $toacc->id;
        $astmt->date = $date;
        $astmt->debit = $acctrans->amount;
        $astmt->credit = 0;
        $astmt->description = $acctrans->reason;
        $astmt->save();

        return redirect('cash-flows')->with('success', 'Transaction was updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $acctrans = AccountTransaction::find(decrypt($id));
        if (!is_null($acctrans)) {
            $astmt = AccountStatement::where('account_transaction_id', $acctrans->id)->first();
            if (!is_null($astmt)) {
                $astmt->delete();
            }
            $acctrans->delete();
        }

        return redirect('cash-flows')->with('success', 'Transaction was deleted successfully');
    }
}
