<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\LegalDocument;
use App\Models\Ownership;
use App\Models\UnitMeasure;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
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

    public function dashboard(Request $request)
    {
        $page = 'VMS Dashboard';
        return view('vms.index', compact('page'));
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Vehicles';
        $now = Carbon::now();
        $start = $now->copy()->startOfMonth()->format('Y-m-d');
        $end = \Carbon\Carbon::now()->format('Y-m-d');
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));

        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }
        $companyId = (int) Session::get('company_id');
        Ownership::ensureDefaultsForCompany($companyId);

        $units = UnitMeasure::select('unit_name')->get();
        $vehicles = Vehicle::where('vehicles.company_id', Session::get('company_id'))->join('vehicle_types', 'vehicle_types.id', '=', 'vehicles.vehicle_type_id')->join('ownerships', 'ownerships.id', '=', 'vehicles.ownership_id')->select('vehicles.id as id', 'plate_no', 'vehicle_name',  'reg_date', 'name as type', 'type as ownership', 'status', 'capacity', 'uom')
        ->whereBetween('vehicles.created_at', [$start, $end])
        ->get();
        $vehtypes = VehicleType::where('company_id', Session::get('company_id'))->get();
        // Show only the core ownership types in the Vehicles page.
        $ownerships = Ownership::where('company_id', $companyId)
            ->where('is_system', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        $departments = Department::where('company_id', Session::get('company_id'))->get();

        return view('vms.vehicles.index', compact('page','start','end','start_date','end_date','is_post_query','vehicles', 'units', 'vehtypes', 'ownerships', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'Register New Vehicle';
        $companyId = (int) Session::get('company_id');
        Ownership::ensureDefaultsForCompany($companyId);

        $units = UnitMeasure::select('unit_name')->get();
        $vehtypes = VehicleType::where('company_id', $companyId)->get();
        $ownerships = Ownership::where('company_id', $companyId)->where('active', true)->orderBy('sort_order')->orderBy('id')->get();
        $departments = Department::where('company_id', $companyId)->get();
        return view('vms.vehicles.create', compact('page', 'units', 'vehtypes', 'ownerships', 'departments'));
    }

    /**
     * Company-owned fleet requires legal documents on registration (TR Tanzania flow).
     */
    private function ownershipRequiresLegalDocuments(int $ownershipId): bool
    {
        $ownership = Ownership::find($ownershipId);

        return $ownership && $ownership->requiresLegalDocuments();
    }

    /**
     * Store a newly created resource in storage.
     * Company-owned: save only via the document upload step (prepareDocuments).
     * Other ownership types: save directly; documents optional later.
     */
    public function store(Request $request)
    {
        // Normalize empty strings coming from HTML selects to `null`
        // so validation rules like `nullable|exists:...` work as expected.
        $request->merge([
            'department_id' => $request->input('department_id') ?: null,
            'reg_date' => $request->input('reg_date') ?: null,
        ]);

        $companyId = Session::get('company_id');

        $request->validate([
            'plate_no' => 'required|string|max:50',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'ownership_id' => 'required|exists:ownerships,id',
            'capacity' => 'required',
            'uom' => 'required',
            'reg_date' => 'nullable|date',
            'department_id' => 'nullable|exists:departments,id',
            'vehicle_name' => 'nullable|string|max:255',
            'chassis_no' => 'nullable|string|max:255',
            'vehicle_picture' => 'nullable|image|max:5120',
        ]);

        $ownershipId = (int) $request->ownership_id;
        $ownershipRow = Ownership::where('company_id', $companyId)->where('id', $ownershipId)->where('active', true)->first();
        if (!$ownershipRow) {
            return redirect()
                ->route('vehicles.create')
                ->withInput()
                ->with('error', 'Please select an active ownership type.');
        }

        if ($this->ownershipRequiresLegalDocuments($ownershipId)) {
            return redirect()
                ->route('vehicles.create')
                ->withInput()
                ->with('error', 'Company-owned vehicles require legal documents on registration. Click "Next: Upload Documents".');
        }

        $vehicle = Vehicle::where('company_id', $companyId)->where('plate_no', $request->plate_no)->first();
        if ($vehicle) {
            return redirect()->back()->withInput()->with('error', 'A vehicle with this plate number already exists.');
        }

        $vehicle = new Vehicle();
        $vehicle->company_id = $companyId;
        $vehicle->vehicle_type_id = $request->vehicle_type_id;
        $vehicle->ownership_id = $request->ownership_id;
        $vehicle->department_id = $request->department_id ?: null;
        $vehicle->plate_no = $request->plate_no;
        $vehicle->vehicle_name = $request->vehicle_name ?: null;
        $vehicle->chassis_no = $request->chassis_no ?: null;
        $vehicle->capacity = $request->capacity;
        $vehicle->uom = $request->uom;
        $vehicle->reg_date = $request->reg_date ?: null;
        if ($request->hasFile('vehicle_picture')) {
            $vehicle->vehicle_picture = $request->file('vehicle_picture')->store('vehicle_pictures', 'local');
        }
        $vehicle->save();

        // Non–company-owned: documents are optional (admin can add later).
        return redirect('vehicles')->with('success', 'Vehicle registered successfully. Documents can be added later if needed.');
    }

    /**
     * Company-owned flow: validate vehicle details, keep in session,
     * then redirect to required legal document upload step.
     */
    public function prepareDocuments(Request $request)
    {
        $request->merge([
            'department_id' => $request->input('department_id') ?: null,
            'reg_date' => $request->input('reg_date') ?: null,
        ]);

        $companyId = Session::get('company_id');

        $request->validate([
            'plate_no' => 'required|string|max:50',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'ownership_id' => 'required|exists:ownerships,id',
            'capacity' => 'required',
            'uom' => 'required',
            'reg_date' => 'nullable|date',
            'department_id' => 'nullable|exists:departments,id',
            'vehicle_name' => 'nullable|string|max:255',
            'chassis_no' => 'nullable|string|max:255',
            'vehicle_picture' => 'nullable|image|max:5120',
        ]);

        $ownershipId = (int) $request->ownership_id;
        $ownershipRow = Ownership::where('company_id', $companyId)->where('id', $ownershipId)->where('active', true)->first();
        if (!$ownershipRow) {
            return redirect()->route('vehicles.create')
                ->withInput()
                ->with('error', 'Please select an active ownership type.');
        }

        if (!$this->ownershipRequiresLegalDocuments($ownershipId)) {
            return redirect()->route('vehicles.create')
                ->withInput()
                ->with('error', 'Use "Save Vehicle" for this ownership type. The document upload step applies only to company-owned vehicles.');
        }

        $exists = Vehicle::where('company_id', $companyId)
            ->where('plate_no', $request->plate_no)
            ->exists();

        if ($exists) {
            return redirect()->route('vehicles.create')
                ->withInput()
                ->with('error', 'A vehicle with this plate number already exists.');
        }

        $vehiclePicturePath = null;
        if ($request->hasFile('vehicle_picture')) {
            $vehiclePicturePath = $request->file('vehicle_picture')->store('vehicle_pictures', 'local');
        }

        Session::put('pending_vehicle_registration', [
            'company_id' => $companyId,
            'vehicle_type_id' => (int) $request->vehicle_type_id,
            'ownership_id' => (int) $request->ownership_id,
            'department_id' => $request->department_id,
            'plate_no' => $request->plate_no,
            'vehicle_name' => $request->vehicle_name ?: null,
            'chassis_no' => $request->chassis_no ?: null,
            'capacity' => $request->capacity,
            'uom' => $request->uom,
            'reg_date' => $request->reg_date,
            'vehicle_picture' => $vehiclePicturePath,
        ]);

        return redirect()->route('vehicles.documents.create');
    }

    public function createDocumentsStep()
    {
        $pendingVehicle = Session::get('pending_vehicle_registration');
        if (!$pendingVehicle) {
            return redirect()->route('vehicles.create')
                ->with('error', 'Please fill vehicle details first.');
        }

        $page = 'Upload Required Vehicle Documents';
        return view('vms.vehicles.documents-step', compact('page', 'pendingVehicle'));
    }

    public function storeWithDocuments(Request $request)
    {
        $pendingVehicle = Session::get('pending_vehicle_registration');
        if (!$pendingVehicle) {
            return redirect()->route('vehicles.create')
                ->with('error', 'Session expired. Please fill vehicle details again.');
        }

        $pendingOwnership = Ownership::find($pendingVehicle['ownership_id'] ?? null);
        if (!$pendingOwnership || !$pendingOwnership->requiresLegalDocuments()) {
            Session::forget('pending_vehicle_registration');

            return redirect()->route('vehicles.create')
                ->with('error', 'This registration step applies only to company-owned vehicles. Please start again.');
        }

        $request->validate([
            'doc_registration' => 'required|file|mimes:pdf|max:5120',
            'doc_road_license' => 'required|file|mimes:pdf|max:5120',
            'doc_inspection' => 'required|file|mimes:pdf|max:5120',
            'doc_registration_issue' => 'required|date',
            'doc_registration_expiry' => 'required|date|after_or_equal:doc_registration_issue',
            'doc_registration_charge_paid' => 'nullable|numeric|min:0',
            'doc_registration_commission' => 'nullable|numeric|min:0',
            'doc_road_license_issue' => 'required|date',
            'doc_road_license_expiry' => 'required|date|after_or_equal:doc_road_license_issue',
            'doc_road_license_charge_paid' => 'nullable|numeric|min:0',
            'doc_road_license_commission' => 'nullable|numeric|min:0',
            'doc_inspection_issue' => 'required|date',
            'doc_inspection_expiry' => 'required|date|after_or_equal:doc_inspection_issue',
            'doc_inspection_charge_paid' => 'nullable|numeric|min:0',
            'doc_inspection_commission' => 'nullable|numeric|min:0',
        ], [
            'doc_registration.required' => 'Vehicle Registration Card (TRA) PDF is required.',
            'doc_road_license.required' => 'Road License (TRA) PDF is required.',
            'doc_inspection.required' => 'Vehicle Inspection Certificate (Police) PDF is required.',
        ]);

        $exists = Vehicle::where('company_id', $pendingVehicle['company_id'])
            ->where('plate_no', $pendingVehicle['plate_no'])
            ->exists();

        if ($exists) {
            Session::forget('pending_vehicle_registration');
            return redirect()->route('vehicles.create')
                ->with('error', 'A vehicle with this plate number already exists.');
        }

        $docConfigs = [
            ['name' => 'Vehicle Registration Card', 'file_key' => 'doc_registration', 'issue' => 'doc_registration_issue', 'expiry' => 'doc_registration_expiry', 'charge' => 'doc_registration_charge_paid', 'commission' => 'doc_registration_commission'],
            ['name' => 'Road License', 'file_key' => 'doc_road_license', 'issue' => 'doc_road_license_issue', 'expiry' => 'doc_road_license_expiry', 'charge' => 'doc_road_license_charge_paid', 'commission' => 'doc_road_license_commission'],
            ['name' => 'Vehicle Inspection Certificate', 'file_key' => 'doc_inspection', 'issue' => 'doc_inspection_issue', 'expiry' => 'doc_inspection_expiry', 'charge' => 'doc_inspection_charge_paid', 'commission' => 'doc_inspection_commission'],
        ];

        DB::transaction(function () use ($pendingVehicle, $request, $docConfigs) {
            $vehicle = new Vehicle();
            $vehicle->company_id = $pendingVehicle['company_id'];
            $vehicle->vehicle_type_id = $pendingVehicle['vehicle_type_id'];
            $vehicle->ownership_id = $pendingVehicle['ownership_id'];
            $vehicle->department_id = $pendingVehicle['department_id'];
            $vehicle->plate_no = $pendingVehicle['plate_no'];
            $vehicle->vehicle_name = $pendingVehicle['vehicle_name'];
            $vehicle->chassis_no = $pendingVehicle['chassis_no'];
            $vehicle->capacity = $pendingVehicle['capacity'];
            $vehicle->uom = $pendingVehicle['uom'];
            $vehicle->reg_date = $pendingVehicle['reg_date'];
            $vehicle->vehicle_picture = $pendingVehicle['vehicle_picture'] ?? null;
            $vehicle->save();

            foreach ($docConfigs as $config) {
                DocumentType::firstOrCreate(
                    ['company_id' => $pendingVehicle['company_id'], 'dt_name' => $config['name']],
                    ['active' => true]
                );
            }

            foreach ($docConfigs as $config) {
                $path = $request->file($config['file_key'])->store('documents', 'local');
                $documentTypeId = DocumentType::where('company_id', $pendingVehicle['company_id'])
                    ->where('dt_name', $config['name'])
                    ->value('id');

                LegalDocument::create([
                    'company_id' => $pendingVehicle['company_id'],
                    'user_id' => Auth::id(),
                    'vehicle_id' => $vehicle->id,
                    'document_type_id' => $documentTypeId,
                    'last_issue_date' => $request->{$config['issue']},
                    'expire_date' => $request->{$config['expiry']},
                    'charge_paid' => $request->{$config['charge']} ?? 0,
                    'commission' => $request->{$config['commission']} ?? 0,
                    'doc_attachment' => $path,
                ]);
            }
        });

        Session::forget('pending_vehicle_registration');

        return redirect('vehicles')->with('success', 'Vehicle registered successfully with required legal documents.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Vehicle Details';
        $codetype = 'QRCODE';
        $vehicle = Vehicle::where('vehicles.id', decrypt($id))->join('vehicle_types', 'vehicle_types.id', '=', 'vehicles.vehicle_type_id')->join('ownerships', 'ownerships.id', '=', 'vehicles.ownership_id')->select('vehicles.id as id', 'plate_no', 'vehicle_name', 'reg_date', 'name as type', 'type as ownership', 'status', 'capacity', 'uom')->first();
        return view('vms.vehicles.show', compact('page', 'vehicle', 'codetype'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edi Vehicle Details';
        $companyId = (int) Session::get('company_id');
        Ownership::ensureDefaultsForCompany($companyId);

        $units = UnitMeasure::select('unit_name')->get();
        $vehicle = Vehicle::find(decrypt($id));
        $vehtypes = VehicleType::where('company_id', $companyId)->get();
        $ownerships = Ownership::where('company_id', $companyId)
            ->where(function ($q) use ($vehicle) {
                $q->where('active', true)->orWhere('id', $vehicle->ownership_id);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        $departments = Department::where('company_id', $companyId)->get();
        return view('vms.vehicles.edit', compact('page', 'vehicle', 'units', 'vehtypes', 'ownerships', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'vehicle_picture' => 'nullable|image|max:5120',
        ]);

        $vehicle = Vehicle::find(decrypt($id));
        if (!is_null($vehicle)) {
            $vehicle->vehicle_type_id = $request['vehicle_type_id'];
            $vehicle->ownership_id = $request['ownership_id'];
            $vehicle->department_id = $request['department_id'];
            $vehicle->plate_no = $request['plate_no'];
            $vehicle->vehicle_name = $request['vehicle_name'];
            $vehicle->chassis_no = $request['chassis_no'];
            $vehicle->capacity = $request['capacity'];
            $vehicle->uom = $request['uom'];
            $vehicle->reg_date = $request['reg_date'];
            if ($request->hasFile('vehicle_picture')) {
                if ($vehicle->vehicle_picture && Storage::disk('local')->exists($vehicle->vehicle_picture)) {
                    Storage::disk('local')->delete($vehicle->vehicle_picture);
                }
                $vehicle->vehicle_picture = $request->file('vehicle_picture')->store('vehicle_pictures', 'local');
            }
            $vehicle->save();

            return redirect('vehicles')->with('success', 'Vehicle Details updated successfully');
        }else {
            return redirect('vehicles')->with('error', 'Vehicle not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehicle = Vehicle::find(decrypt($id));
        if (!is_null($vehicle)) {
            // $orderdeliveries = DeliveryNode::where('vehicle_id', $vehicle->id)->count();
            // if ($orderdeliveries > 0) {
            //     return redirect()->back()->with('info', "Vehicle with Order details can't be deleted");
            // }else{
               // $vehicle->delete();
               $vehicle->status = 'UnAvailable';
               $vehicle->save();
                return redirect('vehicles')->with('success', 'Vehicle deleted successfully');
            //}
        }
    }
}
