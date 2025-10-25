<?php

namespace App\Http\Controllers\Acc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\ExpenseCategory;
use App\Models\TransactionAccount;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Expense Categories';
        $title = 'Expense Categories';
        $title_sw = 'Expense Categories';

        $shop = Shop::find(Session::get('shop_id'));
        $expcategories = ExpenseCategory::where('shop_id', $shop->id)->get();

        $traccounts = TransactionAccount::where('company_id', $shop->company_id)->where('type', 'Expenses')->get();

        return view('accounting.expenses.categories.index', compact('page', 'title', 'title_sw', 'expcategories', 'traccounts'));
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
        $expcategory = ExpenseCategory::where('name', $request['name'])->where('shop_id', $shop->id)->first();
        if (is_null($expcategory)) {
            $expcategory = new ExpenseCategory();
            $expcategory->shop_id = $shop->id;
            $expcategory->transaction_account_id = $request['transaction_account_id'];
            $expcategory->name = $request['name'];
            $expcategory->description = $request['description'];
            if (!empty($request['is_included_in_prod_cost'])) {
                $expcategory->is_included_in_prod_cost = $request['is_included_in_prod_cost'];
            }
            $expcategory->save();

            return redirect('expense-categories')->with('success', 'Expenses Category was added successfully');
        }else{
            return redirect('expense-categories')->with('info', 'Expense Category with same name already exists');
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
        $page = 'Edit Expense Category';
        $title = 'Edit Expense Category';
        $title_sw = 'Hariri Aina ya Matumizi';
        $expcategory = ExpenseCategory::find(decrypt($id));
        $traccounts = TransactionAccount::where('company_id', Session::get('company_id'))->where('type', 'Expenses')->get();
        return view('accounting.expenses.categories.edit', compact('page', 'title', 'title_sw', 'expcategory', 'traccounts'));
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
        $expcategory = ExpenseCategory::find(decrypt($id));
        $expcategory->transaction_account_id = $request['transaction_account_id'];
        $expcategory->name = $request['name'];
        $expcategory->description = $request['description'];
        if (!empty($request['is_included_in_prod_cost'])) {
            $expcategory->is_included_in_prod_cost = $request['is_included_in_prod_cost'];
        }
        $expcategory->save();

        return redirect('expense-categories')->with('success', 'Expenses Category was updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $expcategory = ExpenseCategory::find(decrypt($id));
        if (!is_null($expcategory)) {
            $expcategory->delete();
        }

        return redirect('expense-categories')->with('success', 'Expenses Category was deleted successfully');
    }
}