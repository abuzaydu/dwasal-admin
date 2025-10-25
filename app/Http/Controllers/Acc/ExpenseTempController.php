<?php

namespace App\Http\Controllers\Acc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Log;
use Auth;
use Session;
use App\Models\Shop;
use App\Models\User;
use App\Models\ExpenseTemp;
use App\Models\ExpenseItem;

class ExpenseTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(ExpenseTemp::where('shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $items = ExpenseItem::where('shop_id', Session::get('shop_id'))->select('id', 'expense_type')->get();
        return json_encode($items);
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
        $expitem = ExpenseItem::find($request['expense_item_id']);
        if (!is_null($expitem)) {
            $sameitems = ExpenseTemp::where('expense_item_id', $expitem->id)->where('shop_id', $shop->id)->where('user_id', $user->id)->count();
            
            if ($sameitems == 0) {
                $expenseTemp = new ExpenseTemp;
                $expenseTemp->shop_id = $shop->id;
                $expenseTemp->user_id = $user->id;
                $expenseTemp->expense_item_id = $expitem->id;
                $expenseTemp->expense_type  = $expitem->expense_type;
                $expenseTemp->qty = 1;
                $expenseTemp->amount = 0;
                $expenseTemp->save();
                return $expenseTemp;
                
            }else{
                $warning = 'Ooops!. The Expense Type already in selected items.';
                return redirect()->back()->with('warning', $warning);
            }
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
        $shop = Shop::find(Session::get('shop_id'));
        $expenseTemp =  ExpenseTemp::where('id', $id)->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();
        if (!is_null($expenseTemp)) {
            if (!empty($request['qty']) && $request['qty'] > 0 && $expenseTemp->qty != $request['qty']) {
                $expenseTemp->qty = $request['qty'];
                $expenseTemp->amount = (float)$expenseTemp->unit_cost*(float)$expenseTemp->qty;
            }elseif ($expenseTemp->unit_cost != $request['unit_cost']) {
                $expenseTemp->unit_cost = $request['unit_cost'];
                $expenseTemp->amount = (float)$expenseTemp->unit_cost*(float)$expenseTemp->qty;
            }elseif ($expenseTemp->amount != $request['amount']) {
                $expenseTemp->amount = $request['amount'];
                $expenseTemp->unit_cost = (float)$expenseTemp->amount/(float)$expenseTemp->qty;
            }
            $expenseTemp->wht_rate = $request['wht_rate'];
            $expenseTemp->has_vat = $request['has_vat'];
            $expenseTemp->description = $request['description'];
            $expenseTemp->save();

            return $expenseTemp;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ExpenseTemp::destroy($id);
    }
}
