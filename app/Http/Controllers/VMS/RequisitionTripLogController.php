<?php

namespace App\Http\Controllers\VMS;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employee;
use App\Models\ExpenseType;
use App\Models\RequisitionTripLog;
use App\Models\TripType;
use App\Models\Vehicle;
use App\Models\VehicleRequisition;
use App\Models\Vendor;
use App\Models\VmsExpense;
use App\Models\VmsExpenseAttachment;
use App\Models\VmsExpenseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class RequisitionTripLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //for displaying all expense by a trip
    public function test1(){
        $expenses = VmsExpense::with('employee','vehicle')->get();
        $page = 'new test';
        return view('vms.trip-logs.display',compact('expenses','page'));
    }
    public function index()
    {
        $page = ' Requisition Trip Logs';
        $tripLogs = RequisitionTripLog::with('vehicleRequisition.employee', 'vehicleRequisition.vehicle')
            ->latest('created_at')
            ->get();
 
        return view('vms.trip-logs.index',compact('page','tripLogs'));
    }

    private function getAutoTripNo()
    {
        $lastTrip = \App\Models\VmsExpense::latest()->first();

        if (!$lastTrip) {
            return 'TRIP-001';
        }

        $number = (int) filter_var($lastTrip->trip_no, FILTER_SANITIZE_NUMBER_INT);
        return 'TRIP-' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    public function tripStart(Request $request, $id)
    {
        try {
            $request->validate([
                'start_odometer' => 'required|numeric|min:0',
            ]);

            $requisition = VehicleRequisition::findOrFail(decrypt($id));

            $tripNo = 'TRIP' . strtoupper(Str::random(5));
            RequisitionTripLog::create([
                'vehicle_requisition_id' => $requisition->id,
                'trip_no' => 'TRIP-' . $tripNo,
                'start_time' => now(),
                'start_odometer' => $request->start_odometer,
                'remarks' => $request->remarks,
                'status' => 'In Progress',
            ]);

            return back()->with('success', 'Trip started successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());

        }
    }

     public function endTrip(Request $request, $tripId)
    {  //dd($request->all());
       try {
           $tripLog = RequisitionTripLog::findOrFail($tripId);
            $request->validate([
                'end_odometer' => 'required|numeric|min:' . $tripLog->start_odometer,
                'remarks' => 'nullable|string',
            ]);

            $tripLog->update([
                'end_time' => now(),
                'end_odometer' => $request->end_odometer,
                'status' => 'Completed',
                'remarks' => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Trip completed successfully!');
       } catch (\Throwable $e) {
        return redirect()->back()->with('error',$e->getMessage());
       }
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
        $tripLog = RequisitionTripLog::latest()->first();

        if (is_null($expense)) {
            $tripNo  = $this->getAutoTripNo();
            $expense = new VmsExpense();
            $expense->company_id   = $company->id;
            $expense->user_id      = $user->id;
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

        $vehicles     = Vehicle::where('company_id', $company->id)->select('id', 'plate_no', 'vehicle_name')->get();
        $employees    = Employee::where('company_id', $company->id)->select('id', 'fname', 'lname')->get();
        $vendors      = Vendor::where('company_id', $company->id)->select('id', 'vendor_name')->get();
        $tripTypes    = TripType::where('company_id', $company->id)->select('id', 'trip_type')->get();
        $expenseTypes = ExpenseType::where('company_id', $company->id)->select('id', 'type')->get();

        return view('vms.trip-logs.create-expense', compact(
            'page', 'expense', 'expenseItems','vehicles', 'employees', 'vendors', 'tripTypes', 'expenseTypes','tripLog'
        ));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $expense = new VmsExpense();

        $tripLog = RequisitionTripLog::find($request->requisition_trip_log_id);
        if (!$tripLog) {
            return redirect()->back()->with('error', 'Trip log not found');
        }

        $vehicleRequisition = $tripLog->vehicleRequisition;
        if (!$vehicleRequisition) {
            return redirect()->back()->with('error', 'Vehicle requisition not found for this trip log');
        }

        $expense->requisition_trip_log_id = $tripLog->id;
        $expense->company_id              = $vehicleRequisition->company_id;
        $expense->user_id                 = auth()->id(); 
        $expense->vehicle_id              = $vehicleRequisition->vehicle_id;
        $expense->employee_id             = $vehicleRequisition->employee_id;
        $expense->vendor_id               = $request['vendor_id'];
        $expense->trip_type_id            = $request['trip_type_id'];
        $expense->remarks                 = $request['remarks'];
        $expense->status                  = 'Awaiting For Approval';
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

        return redirect()->back()->with('success', 'Expense created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $page = 'Show Trip Details';
        $trip = RequisitionTripLog::with('vehicleRequisition.employee', 'vehicleRequisition.vehicle','vehicleRequisition.driver')->find($id);

        return view('vms.trip-logs.show',compact('trip','page'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RequisitionTripLog $requisitionTripLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RequisitionTripLog $requisitionTripLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RequisitionTripLog $requisitionTripLog)
    {
        //
    }
}
