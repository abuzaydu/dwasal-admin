<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ExpenseType;
use App\Models\VmsExpenseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ExpenseAjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function searchExpenseType(Request $request)
    {
        $company = Company::find(Session::get('company_id'));
        return ExpenseType::where('company_id', $company->id)
            ->where('type', 'like', '%' . $request->search_key . '%')
            ->select('id', 'type')
            ->get();
    }

    public function fetchExpenseType(Request $request)
    {
        return ExpenseType::where('id', $request->expense_type_id)
            ->select('id', 'type')
            ->first();
    }

    public function fetchExpenseTypes()
    {
        $company = Company::find(Session::get('company_id'));
        return ExpenseType::where('company_id', $company->id)
            ->select('id', 'type')
            ->get();
    }

    public function fetchExpenseItems(Request $request)
    {
        return VmsExpenseItem::where('vms_expense_id', $request->expense_id)
            ->join('expense_types', 'expense_types.id', '=', 'vms_expense_items.expense_type_id')
            ->select(
                'vms_expense_items.id as id',
                'expense_type_id',
                'expense_types.type as expense_type',
                'quantity',
                'unit_price',
                'total_price'
            )
            ->get();
    }

    public function addExpenseItem(Request $request)
    {
        $expType = ExpenseType::find($request->expense_type_id);

        $item = new VmsExpenseItem();
        $item->vms_expense_id  = $request->vms_expense_id;
        $item->expense_type_id = $request->expense_type_id;
        $item->quantity        = 1;
        $item->unit_price      = 0;
        $item->total_price     = 0;
        $item->save();

        return response()->json([
            'id'              => $item->id,
            'expense_type_id' => $item->expense_type_id,
            'expense_type'    => $expType ? $expType->type : '',
            'quantity'        => $item->quantity,
            'unit_price'      => $item->unit_price,
            'total_price'     => $item->total_price,
        ]);
    }

    public function updateExpenseItem(Request $request)
    {
        $item = VmsExpenseItem::find($request->id);
        if ($item) {
            $item->quantity    = $request->quantity;
            $item->unit_price  = $request->unit_price;
            $item->total_price = $request->total_price;
            $item->save();
        }
        return response()->json(['status' => 'ok']);
    }

    public function removeExpenseItem(Request $request)
    {
        VmsExpenseItem::destroy($request->id);
        return response()->json(['status' => 'ok']);
    }
}