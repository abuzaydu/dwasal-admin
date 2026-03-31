<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Maintenance;
use App\Models\MaintenanceItem;
use App\Models\MaintenancePhoto;
use App\Models\MaintenanceType;
use App\Models\Part;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class MaintenanceController extends Controller
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
        $page = 'Vehicle Maintanance';
        $companyId = Session::get('company_id');

        $maintenances = Maintenance::with(['vehicle', 'maintenanceType', 'employee'])
            ->where('company_id', $companyId)
            ->where('is_deleted', false)
            ->latest()
            ->get();

        $maintenanceTypes = MaintenanceType::where('company_id', $companyId)
            ->orderByDesc('id')
            ->get();

        return view('vms.maintenance.index', compact('page', 'maintenances', 'maintenanceTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Maintenance';
        $companyId = Session::get('company_id');

        $vehicles = Vehicle::where('company_id', $companyId)->select('id', 'plate_no', 'vehicle_name')->latest()->get();
        $employees = Employee::where('company_id', $companyId)->select('id', 'fname', 'lname')->latest()->get();
        $maintenanceTypes = MaintenanceType::where('company_id', $companyId)->where('active', true)->select('id', 'type')->latest()->get();

        return view('vms.maintenance.create', compact('page', 'vehicles', 'employees', 'maintenanceTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       try {
          $companyId = Session::get('company_id');

            $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'employee_id' => 'required|exists:employees,id',
                'maintenance_type_id' => 'required|exists:maintenance_types,id',
                'date' => 'required|date',
                'maintenance_code' => 'nullable|string|max:255',
                'req_type' => 'nullable|string|max:255',
                'priority' => 'nullable|string|max:100',
                'service_title' => 'nullable|string|max:255',
                'charge_bear_by' => 'nullable|string|max:255',
                'charge' => 'nullable|numeric|min:0',
                'remarks' => 'nullable|string|max:2000',
                'items' => 'required|array|min:1',
                'items.*.part_id' => 'required|exists:parts,id',
                'items.*.qty' => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'required|numeric|min:0',

            ]);

            $items = $request->input('items', []);
            $status = $request->input('status', 'Pending');
            $maintenanceCode = str('M-Code-'.' '.rand(100,5000));
            DB::transaction(function () use ($request, $companyId, $items, $status, $maintenanceCode) {
                $maintenance = new Maintenance();
                $maintenance->company_id = $companyId;
                $maintenance->user_id = Auth::id();
                $maintenance->employee_id = $request->employee_id;
                $maintenance->vehicle_id = $request->vehicle_id;
                $maintenance->maintenance_type_id = $request->maintenance_type_id;
                $maintenance->date = $request->date;
                $maintenance->maintenance_code = $maintenanceCode;
                $maintenance->req_type = $request->req_type;
                $maintenance->priority = $request->priority;
                $maintenance->service_title = $request->service_title;
                $maintenance->charge_bear_by = $request->charge_bear_by;
                $maintenance->charge = $request->charge ?? 0;
                $maintenance->remarks = $request->remarks;
                $maintenance->status = $status;
                $maintenance->is_deleted = false;
                $maintenance->save();

                foreach ($items as $item) {
                    $part = Part::where('company_id', $companyId)->findOrFail($item['part_id']);

                    $maintenanceItem = new MaintenanceItem();
                    $maintenanceItem->maintenance_id = $maintenance->id;
                    $maintenanceItem->part_category_id = $part->part_category_id;
                    $maintenanceItem->part_id = $part->id;
                    $maintenanceItem->date = $request->date . ' 00:00:00';
                    $maintenanceItem->qty = $item['qty'];
                    $maintenanceItem->unit_price = $item['unit_price'];
                    $maintenanceItem->total_price = round(((float) $item['qty']) * ((float) $item['unit_price']), 2);
                    $maintenanceItem->is_deleted = false;
                    $maintenanceItem->save();
                }

            });

            return redirect('maintenance')->with('success', 'Maintenance record created successfully.');
       } catch (\Throwable $e) {
            return redirect()->back()->with('error',$e->getMessage());
       }
    }

    public function approve($id){
        try {
            $maintenance = Maintenance::find($id);
            //($maintenance);
            if(!$maintenance){
                return redirect()->back()->with('error', 'No such maintenance id');
            }
            if($maintenance->status !== 'Pending'){
                return redirect()->back()->with('error', 'Only pending and in progress maintenace can be approved');

            }
            $maintenance->update([
                'status' =>'Approved',
                'rejection_reason' => null,
            ]);
            return redirect()->back()->with('success', 'Maintenance approved successfully!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request,$id){
       // dd($id);
        try {
            $maintenance = Maintenance::find($id);
            if(!$maintenance){
                return redirect()->back()->with('error', 'No such maintenance id');
            }
            if($maintenance->status !== 'Pending'){
                return redirect()->back()->with('error', 'Only Pending status can be rejected');

            }
            $maintenance->update([
                'status' =>'Rejected',
                'rejection_reason' => $request->rejection_reason ?? null,
            ]);
            return redirect()->back()->with('success', 'Maintenance rejected successfully!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function start($id){
        try {
            $maintenance = Maintenance::find($id);
            if(!$maintenance){
                return redirect()->back()->with('error', 'No such maintenance id');
            }
            if($maintenance->status !== 'Approved'){
                return redirect()->back()->with('error', 'Only Approved status can be started');

            }
            $maintenance->update([
                'status' =>'In Progress'
            ]);
            return redirect()->back()->with('success', 'Maintenance resumed successfully!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function complete($id){
        try {
            $maintenance = Maintenance::find($id);
            if(!$maintenance){
                return redirect()->back()->with('error', 'No such maintenance id');
            }
            if($maintenance->status !== 'In Progress'){
                return redirect()->back()->with('error', 'Only In Progress status can be completed');

            }
            $maintenance->update([
                'status' =>'Completed',
                'charge' => $maintenance->items()->where('is_deleted', false)->sum('total_price'),
            ]);
            return redirect()->back()->with('success', 'Maintenance completed successfully!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $companyId = Session::get('company_id');
        $maintenance = Maintenance::with(['vehicle', 'employee', 'maintenanceType', 'items.part', 'photos'])
            ->where('company_id', $companyId)
            ->where('is_deleted', false)
            ->findOrFail(decrypt($id));

        $page = 'Maintenance Details';
        return view('vms.maintenance.show', compact('page', 'maintenance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $companyId = Session::get('company_id');
        $maintenance = Maintenance::with(['vehicle', 'employee', 'maintenanceType', 'items.part', 'photos'])
            ->where('company_id', $companyId)
            ->where('is_deleted', false)
            ->findOrFail(decrypt($id));

        $page = 'Edit Maintenance';
        $vehicles = Vehicle::where('company_id', $companyId)->select('id', 'plate_no', 'vehicle_name')->latest()->get();
        $employees = Employee::where('company_id', $companyId)->select('id', 'fname', 'lname')->latest()->get();
        $maintenanceTypes = MaintenanceType::where('company_id', $companyId)->where('active', true)->select('id', 'type')->latest()->get();

        return view('vms.maintenance.edit', compact('page', 'maintenance', 'vehicles', 'employees', 'maintenanceTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $companyId = Session::get('company_id');
            $maintenance = Maintenance::where('company_id', $companyId)
                ->where('is_deleted', false)
                ->findOrFail(decrypt($id));

            $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'employee_id' => 'required|exists:employees,id',
                'maintenance_type_id' => 'required|exists:maintenance_types,id',
                'date' => 'required|date',
                'maintenance_code' => 'nullable|string|max:255',
                'req_type' => 'nullable|string|max:255',
                'priority' => 'nullable|string|max:100',
                'service_title' => 'nullable|string|max:255',
                'charge_bear_by' => 'nullable|string|max:255',
                'charge' => 'nullable|numeric|min:0',
                'remarks' => 'nullable|string|max:2000',
                'items' => 'required|array|min:1',
                'items.*.part_id' => 'required|exists:parts,id',
                'items.*.qty' => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'required|numeric|min:0',
                
            ]);

            $items = $request->input('items', []);

            DB::transaction(function () use ($request, $maintenance, $companyId, $items) {
                $maintenance->vehicle_id = $request->vehicle_id;
                $maintenance->employee_id = $request->employee_id;
                $maintenance->maintenance_type_id = $request->maintenance_type_id;
                $maintenance->date = $request->date;
                $maintenance->maintenance_code = $request->maintenance_code;
                $maintenance->req_type = $request->req_type;
                $maintenance->priority = $request->priority;
                $maintenance->service_title = $request->service_title;
                $maintenance->charge_bear_by = $request->charge_bear_by;
                $maintenance->charge = $request->charge ?? 0;
                $maintenance->remarks = $request->remarks;
                $maintenance->status = 'Pending';
                $maintenance->save();

                $maintenance->items()->where('is_deleted', false)->update(['is_deleted' => true]);

                foreach ($items as $item) {
                    $part = Part::where('company_id', $companyId)->findOrFail($item['part_id']);

                    $maintenanceItem = new MaintenanceItem();
                    $maintenanceItem->maintenance_id = $maintenance->id;
                    $maintenanceItem->part_category_id = $part->part_category_id;
                    $maintenanceItem->part_id = $part->id;
                    $maintenanceItem->date = $request->date . ' 00:00:00';
                    $maintenanceItem->qty = $item['qty'];
                    $maintenanceItem->unit_price = $item['unit_price'];
                    $maintenanceItem->total_price = round(((float) $item['qty']) * ((float) $item['unit_price']), 2);
                    $maintenanceItem->is_deleted = false;
                    $maintenanceItem->save();
                }

            });

            return redirect('maintenance')->with('success', 'Maintenance updated successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $maintenance = Maintenance::where('is_deleted', false)->findOrFail(decrypt($id));

            DB::transaction(function () use ($maintenance) {                

                $maintenance->items()->where('is_deleted', false)->update(['is_deleted' => true]);
                $maintenance->is_deleted = true;
                $maintenance->save();
            });

            return redirect('maintenance')->with('success', 'Maintenance deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
