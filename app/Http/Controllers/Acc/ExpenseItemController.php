<?php

namespace App\Http\Controllers\Acc;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ExpenseItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $item = new ExpenseItem();
        $item->shop_id = Session::get('shop_id');
        $item->expense_category_id = $request['expense_category_id'];
        $item->expense_type = $request['expense_type'];
        $item->is_cost_of_sale = $request['is_cost_of_sale'];
        $item->save();

        return redirect()->back()->with('success', 'Item added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Expense Item';
        $title = 'Edit Expense Item';
        $expitem = ExpenseItem::find(decrypt($id));
        $expcategories = ExpenseCategory::where('shop_id', $expitem->shop_id)->select('id', 'name')->get();
        return view('accounting.expenses.edit-item', compact('page', 'title', 'expitem', 'expcategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = ExpenseItem::find(decrypt($id));
        $item->expense_category_id = $request['expense_category_id'];
        $item->expense_type = $request['expense_type'];
        $item->is_cost_of_sale = $request['is_cost_of_sale'];
        $item->save();

        $expenses = Expense::where('expense_item_id', $item->id)->where('shop_id', $item->shop_id)->get();
        foreach ($expenses as $key => $expense) {
            $expense->expense_type = $item->expense_type;
            $expense->expense_category_id = $item->expense_category_id;
            $expense->is_cost_of_sale = $item->is_cost_of_sale;
            $expense->save();
        }

        return redirect('expenses')->with('success', 'Item added successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = ExpenseItem::find(decrypt($id));
        if (!is_null($item)) {
            $expenses = Expense::where('expense_item_id', $item->id)->count();
            if ($expenses > 0) {
                return redirect()->back()->with('info', 'Item with Expense records cannot be deleted');
            }else{
                $item->delete();
                return redirect()->back()->with('success', 'Item deleted successfully');
            }
        }else{
            return redirect()->back()->with('error', 'Item not Found');
        }
    }
}
