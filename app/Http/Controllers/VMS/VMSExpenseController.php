<?php

namespace App\Http\Controllers\VMS;

use \Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employee;
use App\Models\ExpenseType;
use App\Models\TripType;
use App\Models\Vehicle;
use App\Models\Vendor;
use App\Models\VmsExpense;
use App\Models\VmsExpenseAttachment;
use App\Models\VmsExpenseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class VmsExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'VMS Expenses';
        $company = Company::find(Session::get('company_id'));
        $now = Carbon::now();
        $start = $now->copy()->startOfMonth();
        $end = Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date   = $request['end_date'];
            $start      = $request['start_date'] . ' 00:00:00';
            $end        = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }
        $expenses = VmsExpense::whereNotNull('requisition_trip_log_id')->with(['employee','tripLog.vehicleRequisition.driver','vehicle','vendor','tripLog.vehicleRequisition.driver' // optional relation
        ])->latest()->get();
        $expenses1 = VmsExpense::whereNull('requisition_trip_log_id')->get();

        // $expenses = VmsExpense::where('vms_expenses.company_id', $company->id)
        //     ->whereBetween('date', [$start, $end])
        //     ->join('vehicles',  'vehicles.id',  '=', 'vms_expenses.vehicle_id')
        //     ->join('employees', 'employees.id', '=', 'vms_expenses.employee_id')
        //     ->join('users',     'users.id',     '=', 'vms_expenses.user_id')
        //     ->join('trip_types','trip_types.id','=', 'vms_expenses.trip_type_id')
        //     ->select('vms_expenses.id as id','trip_no','exp_group','date','plate_no','vehicle_name','vms_expenses.status as status',
        //         'fname','lname','trip_type','vms_expenses.updated_at as updated_at' 
        //     )
        // ->orderBy('vms_expenses.created_at', 'desc')->get();
        $expenseTypes = ExpenseType::latest()->get();
        $tripTypes = TripType::latest()->get();

        return view('vms.expenses.index', compact('expenses1',
            'page', 'is_post_query', 'start_date', 'end_date', 'expenses','expenseTypes','tripTypes'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page    = 'New VMS Expense';
        $user    = Auth::user();
        $company = Company::find(Session::get('company_id'));

        $expense = VmsExpense::where('company_id', $company->id)->where('user_id', $user->id)
                ->where('status', 'Pending')->first();

        if (is_null($expense)) {
            $tripNo  = $this->getAutoTripNo();
            $expense = new VmsExpense();
            $expense->company_id   = $company->id;
            $expense->user_id      = $user->id;
            $expense->date         = Carbon::now()->toDateString();
            $expense->trip_no      = $tripNo;
            $expense->exp_group    = '';
            $expense->odometer_mileage = 0;
            $expense->vehicle_rent = 0;
            $expense->employee_id  = null;
            $expense->vendor_id    = null;
            $expense->vehicle_id   = null;
            $expense->trip_type_id = null;
            $expense->save();
        }

        $expenseItems = VmsExpenseItem::where('vms_expense_id', $expense->id)
            ->join('expense_types', 'expense_types.id', '=', 'vms_expense_items.expense_type_id')
            ->select('vms_expense_items.id as id','expense_type_id','expense_types.type as expense_type','quantity','unit_price','total_price' )->get();

        $pendingExpenses = VmsExpense::where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->where('status', 'Pending')
            ->where('id', '!=', $expense->id)
            ->get();

        $vehicles     = Vehicle::where('company_id', $company->id)->select('id', 'plate_no', 'vehicle_name')->get();
        $employees    = Employee::where('company_id', $company->id)->select('id', 'fname', 'lname')->get();
        $vendors      = Vendor::where('company_id', $company->id)->select('id', 'vendor_name')->get();
        $tripTypes    = TripType::where('company_id', $company->id)->select('id', 'trip_type')->get();
        $expenseTypes = ExpenseType::where('company_id', $company->id)->select('id', 'type')->get();

        return view('vms.expenses.create', compact(
            'page', 'expense', 'expenseItems', 'pendingExpenses',
            'vehicles', 'employees', 'vendors', 'tripTypes', 'expenseTypes'
        ));
    }

    /**
     * Auto-generate a trip number.
     */
    public function getAutoTripNo()
    {
        $company = Company::find(Session::get('company_id'));
        $prefix  = '';
        if (preg_match_all('/\b(\w)/', strtoupper($company->name), $m)) {
            $prefix = implode('', $m[1]);
        }

        $last = VmsExpense::where('company_id', $company->id)->orderBy('id', 'desc')->first();
        if (!is_null($last)) {
            $lastNum = (int) str_replace($prefix . '/EXP-', '', $last->trip_no);
            return $prefix . '/EXP-' . sprintf('%03d', $lastNum + 1);
        }
        return $prefix . '/TRIP-' . sprintf('%03d', 1);
    }

    /**
     * Store a newly created resource in storage.
     */
    
public function store(Request $request)
{//dd($request->all());
    $request->validate([
        'exp_group'        => 'required|string|max:255',
    ]);

    $expense = VmsExpense::find($request['vms_expense_id']);

    if (!is_null($expense)) {
        $expense->vendor_id        = $request['vendor_id'];
        $expense->exp_group        = $request['exp_group'];
        $expense->date             = $request['date'];
        $expense->remarks          = $request['remarks'];
        $expense->status           = 'Awaiting For Approval';
        $expense->save();

        if ($request->hasFile('doc_attachment')) {
            foreach ($request->file('doc_attachment') as $file) {
                $path     = $file->store('vms-expense-attachments', 'public');
                $mimeType = $file->getClientMimeType();

                VmsExpenseAttachment::create([
                    'vms_expense_id' => $expense->id,
                    'file_path'      => $path,
                    'file_type'      => $mimeType,
                ]);
            }
        }

        return redirect('vms-expenses')->with('success', 'Expense created successfully');
    }

    return redirect()->back()->with('error', 'Expense record not found');
}

    /**
     * Display the specified resource.
     */
   public function show(string $id)
    {
        $page    = 'Expense Details';
        $company = Company::find(Session::get('company_id'));

        $expense = VmsExpense::where('vms_expenses.id', decrypt($id))
            ->where('vms_expenses.company_id', $company->id)
            ->leftJoin('vehicles',   'vehicles.id',   '=', 'vms_expenses.vehicle_id')
            ->leftJoin('employees',  'employees.id',  '=', 'vms_expenses.employee_id')
            ->leftJoin('vendors',    'vendors.id',    '=', 'vms_expenses.vendor_id')
            ->leftJoin('trip_types', 'trip_types.id', '=', 'vms_expenses.trip_type_id')
            ->leftJoin('users',      'users.id',      '=', 'vms_expenses.user_id')
            ->select('vms_expenses.id as id','vms_expenses.trip_no','vms_expenses.exp_group','vms_expenses.date','vms_expenses.odometer_mileage',
                'vms_expenses.vehicle_rent','vms_expenses.remarks','vms_expenses.status','vms_expenses.created_at','vms_expenses.updated_at',
                'vms_expenses.employee_id','vms_expenses.vendor_id','vms_expenses.vehicle_id','vms_expenses.trip_type_id',
                'vehicles.plate_no','vehicles.vehicle_name','employees.fname','employees.lname','vendors.vendor_name',
                'trip_types.trip_type','users.first_name','users.last_name'
            )
            ->first();

        if (is_null($expense)) {
            return redirect()->back()->with('error', 'Expense not found.');
        }

        $expenseItems = VmsExpenseItem::where('vms_expense_id', $expense->id)
            ->join('expense_types', 'expense_types.id', '=', 'vms_expense_items.expense_type_id')
            ->select('vms_expense_items.id as id','expense_type_id','expense_types.type as expense_type',
                'quantity','unit_price','total_price'
            )
            ->get();
        $attachments = VmsExpenseAttachment::where('vms_expense_id', $expense->id)->get();


        return view('vms.expenses.show', compact('page', 'expense', 'expenseItems','attachments'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page    = 'Edit VMS Expense';
        $company = Company::find(Session::get('company_id'));
        $expense = VmsExpense::find(decrypt($id));

        if (!is_null($expense)) {
            $expenseItems = VmsExpenseItem::where('vms_expense_id', $expense->id)
                ->join('expense_types', 'expense_types.id', '=', 'vms_expense_items.expense_type_id')
                ->select('vms_expense_items.id as id','expense_type_id','expense_types.type as expense_type','quantity','unit_price','total_price')->get();

            $vehicles     = Vehicle::where('company_id', $company->id)->select('id', 'plate_no', 'vehicle_name')->get();
            $employees    = Employee::where('company_id', $company->id)->select('id', 'fname', 'lname')->get();
            $vendors      = Vendor::where('company_id', $company->id)->select('id', 'vendor_name')->get();
            $tripTypes    = TripType::where('company_id', $company->id)->select('id', 'trip_type')->get();
            $expenseTypes = ExpenseType::where('company_id', $company->id)->select('id', 'type')->get();

            return view('vms.expenses.edit', compact('page', 'expense', 'expenseItems','vehicles', 'employees', 'vendors', 'tripTypes', 'expenseTypes' ));
        }

        return redirect()->back()->with('error', 'Expense not found');
    }

    /**
     * Update the specified resource in storage.
     */
    
    public function update(Request $request, string $id)
{
    $request->validate([
        'exp_group'        => 'required|string|max:255',
        'odometer_mileage' => 'required|numeric|min:1',
        'vehicle_rent'     => 'required|numeric|min:1',
    
    ]);

    $expense = VmsExpense::find(decrypt($id));

    if (!is_null($expense)) {
        $expense->vehicle_id       = $request['vehicle_id'];
        $expense->employee_id      = $request['employee_id'];
        $expense->vendor_id        = $request['vendor_id'];
        $expense->trip_type_id     = $request['trip_type_id'];
        $expense->exp_group        = $request['exp_group'];
        $expense->date             = $request['date'];
        $expense->odometer_mileage = $request['odometer_mileage'];
        $expense->vehicle_rent     = $request['vehicle_rent'];
        $expense->remarks          = $request['remarks'];
        $expense->save();

        if ($request->hasFile('doc_attachment')) {
            foreach ($request->file('doc_attachment') as $file) {
                $path     = $file->store('vms-expense-attachments', 'public');
                $mimeType = $file->getClientMimeType();

                VmsExpenseAttachment::create([
                    'vms_expense_id' => $expense->id,
                    'file_path'      => $path,
                    'file_type'      => $mimeType,
                ]);
            }
        }

        return redirect()
            ->route('vms-expenses.show', encrypt($expense->id))
            ->with('success', 'Expense updated successfully');
    }

    return redirect()->back()->with('error', 'Expense not found');
}

    /**
     * Approve an expense.
     */
    public function approveExpense(string $id)
    {
        $expense = VmsExpense::find(decrypt($id));
        if (!is_null($expense)) {
            $expense->status      = 'Approved';
            $expense->save();

            return redirect()
                ->route('vms-expenses.show', encrypt($expense->id))
                ->with('success', 'Expense approved successfully');
        }
        return redirect()->back()->with('error', 'Expense not found');
    }

    /**
     * Reject an expense.
     */
    public function rejectExpense(Request $request)
    {
        $expense = VmsExpense::find($request['id']);
        if (!is_null($expense)) {
            $expense->status        = 'Rejected';
            $expense->remarks = $request['remarks'];
            $expense->save();

            return redirect()
                ->route('vms-expenses.show', encrypt($expense->id))
                ->with('success', 'Expense rejected successfully');
        }
        return redirect()->back()->with('error', 'Expense not found');
    }


    /**
     * Close an expense.
     */
    public function closeExpense(string $id)
    {
        $expense = VmsExpense::find(decrypt($id));
        if (!is_null($expense)) {
            $expense->status    = 'Closed';
            $expense->closed_by = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $expense->closed_at = Carbon::now();
            $expense->save();

            return redirect()->route('vms-expenses.show', encrypt($expense->id))
                ->with('success', 'Expense closed successfully');
        }

        return redirect()->back()->with('error', 'Expense not found');
    }

    /**
     * Cancel / delete the expense (and its items).
     */
    public function destroy(string $id)
    {
        $expense = VmsExpense::find(decrypt($id));
        if (!is_null($expense)) {
            VmsExpenseItem::where('vms_expense_id', $expense->id)->delete();
            $expense->delete();

            return redirect('vms-expenses')->with('success', 'Expense deleted successfully');
        }

        return redirect()->back()->with('error', 'Expense not found');
    }

    public function destroyAttachment(string $id)
    {
        $attachment = VmsExpenseAttachment::find($id);
        if ($attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $attachment->delete();
            return redirect()->back()->with('success', 'Attachment removed successfully');
        }
        return redirect()->back()->with('error', 'Attachment not found');
    }
}