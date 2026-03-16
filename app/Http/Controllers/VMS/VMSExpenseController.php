<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ExpenseType;
use App\Models\TripType;
use App\Models\Vehicle;
use App\Models\Vendor;
use App\Models\VmsExpense;
use App\Models\VmsExpenseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class VMSExpenseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Vehicle Expenses';
        $expenseTypes = ExpenseType::where('company_id', session::get('company_id'))->latest()->get();
        $tripTypes = TripType::where('company_id',session::get('company_id'))->latest()->get();

        $expenses = VmsExpense::where('company_id', Session::get('company_id'))
                        ->with(['employee', 'vehicle', 'tripType'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        $employees = Employee::where('company_id', Session::get('company_id'))->get();
        $vendors = Vendor::where('company_id', Session::get('company_id'))->get();
        $vehicles = Vehicle::where('company_id', Session::get('company_id'))->get();
        return view('vms.expenses.index',compact('page','expenseTypes','tripTypes','expenses','employees', 'vendors','vehicles'));
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
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'vendor_id' => 'required|exists:vendors,id',
                'vehicle_id' => 'required|exists:vehicles,id',
                'trip_type_id' => 'required|exists:trip_types,id',
                'exp_group' => 'required|string',
                'date'  => 'required|date',
                'remarks' => 'nullable|string',
            ]);
            $tripNo = 'Trip-'. Str::upper(Str::random(5));

            $expense = new VmsExpense();
            $expense->company_id = Session::get('company_id');
            $expense->user_id = Auth::id();
            $expense->employee_id = $request->employee_id;
            $expense->vendor_id = $request->vendor_id;
            $expense->vehicle_id = $request->vehicle_id;
            $expense->trip_type_id = $request->trip_type_id;
            $expense->exp_group  = $request->exp_group;
            $expense->trip_no = $tripNo;
            $expense->odometer_mileage  = 0;
            $expense->vehicle_rent = 0;
            $expense->date = $request->date;
            $expense->remarks = $request->remarks;
            $expense->status = 'Open';
            $expense->save();

            return redirect()->route('vms-expenses.show', $expense->id)
                    ->with('success', 'Trip created successfully. You can now add expense items.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error'. $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Trip Details';
        $expense = VmsExpense::where('company_id', Session::get('company_id'))
                        ->with(['employee', 'vehicle', 'tripType', 'vendor', 'items.expenseType'])
                        ->findOrFail($id);

        $expenseTypes = ExpenseType::where('company_id', Session::get('company_id'))->where('active', true)->get();

        return view('vms.expenses.show', compact('page', 'expense', 'expenseTypes'));
    }

    public function getItems(string $id)
    {
        $items = VmsExpenseItem::where('vms_expense_id', $id)->get();
        return response()->json($items);
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
        try {
            $validated=$request->validate([
                'employee_id' => 'required|exists:employees,id',
                'vendor_id' => 'required|exists:vendors,id',
                'vehicle_id'  => 'required|exists:vehicles,id',
                'trip_type_id' => 'required|exists:trip_types,id',
                'exp_group' => 'required|string',
                'trip_no' => 'required|string',
                'odometer_mileage' => 'required|numeric|min:0',
                'vehicle_rent' => 'required|numeric|min:0',
                'date' => 'required|date',
                'remarks' => 'nullable|string',
            ]);

            $expenses = VmsExpense::findOrFail($id);
            $expenses->update($validated);

            return redirect()->back()->with('success', 'Expense updated successfully'); 
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function closeTrip(Request $request, string $id)
    {
        try {
            $expense = VmsExpense::findOrFail($id);

            if (!in_array($expense->status, ['Open', 'In Progress', 'Rejected'])) {
                return redirect()->back()->with('warning', 'This trip cannot be submitted at this stage.');
            }

            $itemsCount = VmsExpenseItem::where('vms_expense_id', $id)->count();
            if ($itemsCount === 0) {
                return redirect()->back()->with('warning', 'Please add at least one expense item before submitting.');
            }

            $expense->odometer_mileage = $request->odometer_mileage;
            $expense->vehicle_rent = $request->vehicle_rent;
            $expense->remarks = $request->remarks;
            $expense->status = 'Pending';
            $expense->save();

            return redirect()->back()->with('success', 'Expense resubmitted for approval successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approveExpense(string $id)
    {
        try {
            $expense = VmsExpense::findOrFail($id);

            if ($expense->status !== 'Pending') {
                return redirect()->back()->with('warning', 'Only pending expenses can be approved.');
            }

            $itemsCount = VmsExpenseItem::where('vms_expense_id', $expense->id)->count();
            if ($itemsCount === 0) {
                return redirect()->back()->with('warning', 'Cannot approve this expense. Please add at least one expense item first.');
            }

            $expense->status = 'Approved';
            $expense->save();

            return redirect()->back()->with('success', 'Expense approved successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function rejectExpense(Request $request, string $id)
    {
        try {
            $request->validate(['rejection_reason' => 'required|string']);

            $expense = VmsExpense::findOrFail($id);

            if ($expense->status !== 'Pending') {
                return redirect()->back()->with('warning', 'Only pending expenses can be rejected.');
            }

            $expense->status = 'Rejected';
            $expense->remarks = $request->rejection_reason;
            $expense->save();

            return redirect()->back()->with('success', 'Expense rejected.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeItem(Request $request, string $id)
    {
        try {
            $request->validate([
                'expense_type_id' => 'required|exists:expense_types,id',
                'quantity' => 'required|numeric|min:0',
                'unit_price' => 'required|numeric|min:1',
            ]);

            $expense = VmsExpense::findOrFail($id);

            if ($expense->status === 'Approved') {
                return redirect()->back()->with('warning', 'Cannot add items to an approved expense.');
            }

            $item = new VmsExpenseItem();
            $item->vms_expense_id = $expense->id;
            $item->expense_type_id = $request->expense_type_id;
            $item->quantity = $request->quantity;
            $item->unit_price = $request->unit_price;
            $item->total_price = $request->quantity * $request->unit_price;
            $item->save();

            if ($expense->status === 'Open') {
                $expense->status = 'In Progress';
                $expense->save();
            }

            return redirect()->back()->with('success', 'Expense item added successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }           
    }

    public function destroyItem(string $id)
    {
        try {
            $item = VmsExpenseItem::findOrFail($id);
            $expense = VmsExpense::findOrFail($item->vms_expense_id);

            if ($expense->status === 'Approved') {
                return redirect()->back()->with('warning', 'Cannot delete items from an approved expense.');
            }

            $item->delete();

            return redirect()->back()->with('success', 'Item deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       try {
         $expense = VmsExpense::findOrFail($id);

         if ($expense->status !== 'Open' && $expense->status !== 'In Progress') {
             return redirect()->back()->with('warning', 'Cannot delete an expense that is Pending, Approved or Rejected.');
         }

         VmsExpenseItem::where('vms_expense_id', $expense->id)->delete();
         $expense->delete();

         return redirect()->route('vms-expenses.index')->with('success', 'Expense deleted successfully.');
       } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
       }
    }
  
}
