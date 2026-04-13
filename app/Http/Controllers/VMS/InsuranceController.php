<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\Insurance;
use App\Models\InsuranceCompany;
use App\Models\IrPeriod;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class InsuranceController extends Controller
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
    public function index(Request $request)
    {
        $page = 'Vehicle Insurance';
        $companyId = Session::get('company_id');
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
        $status = request('status');

        $insurancesAll = Insurance::with(['vehicle', 'insuranceCompany', 'irPeriod'])
            ->where('company_id', $companyId)->whereBetween('insurances.created_at', [$start, $end])
            ->where('is_active', true)
            ->orderBy('end_date')
            ->get();

        $validInsurances = $insurancesAll->filter(fn ($i) => $i->status === 'VALID')->values();
        $expiringSoonInsurances = $insurancesAll->filter(fn ($i) => $i->status === 'EXPIRING_SOON')->values();
        $expiredInsurances = $insurancesAll->filter(fn ($i) => $i->status === 'EXPIRED')->values();

        $missingVehicles = Vehicle::where('company_id', $companyId)
            ->whereDoesntHave('insurances', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->orderBy('plate_no')
            ->get();

        return view('vms.insurances.index', compact('page','insurancesAll','validInsurances','expiringSoonInsurances',
            'expiredInsurances','missingVehicles','status','is_post_query', 'start_date', 'end_date'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companyId = Session::get('company_id');
        $page = 'Add Vehicle Insurance';

        $vehicleId = request('vehicle_id');

        $vehicles = Vehicle::where('company_id', $companyId)->orderBy('plate_no')->get();
        $insuranceCompanies = InsuranceCompany::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        $irPeriods = IrPeriod::where('company_id', $companyId)->where('active', true)->orderBy('period')->get();

        return view('vms.insurances.create', compact(
            'page',
            'vehicles',
            'insuranceCompanies',
            'irPeriods',
            'vehicleId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $companyId = Session::get('company_id');

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'ir_period_id' => 'required|exists:ir_periods,id',
            'policy_number' => 'required|string|max:255',
            'charge_payable' => 'required|numeric|min:0',
            'deductible' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'recurring_date' => 'nullable|date',
            'policy_attachment' => 'required|file|mimes:pdf|max:5120',
            'add_reminder' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        $vehicle = Vehicle::where('company_id', $companyId)->findOrFail($request->vehicle_id);
        InsuranceCompany::where('company_id', $companyId)->findOrFail($request->insurance_company_id);
        IrPeriod::where('company_id', $companyId)->findOrFail($request->ir_period_id);

        $path = $request->file('policy_attachment')->store('insurances', 'local');

        Insurance::create([
            'company_id' => $companyId,
            'user_id' => Auth::id(),
            'insurance_company_id' => $request->insurance_company_id,
            'vehicle_id' => $vehicle->id,
            'ir_period_id' => $request->ir_period_id,
            'policy_number' => $request->policy_number,
            'charge_payable' => $request->charge_payable,
            'deductible' => $request->deductible,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'recurring_date' => $request->recurring_date,
            'policy_attachment' => $path,
            'add_reminder' => $request->has('add_reminder'),
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
        ]);

        return redirect()->route('insurance.index')->with('success', 'Insurance saved.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $insurance = Insurance::with(['vehicle', 'insuranceCompany', 'irPeriod'])->findOrFail(decrypt($id));
        $companyId = Session::get('company_id');
        abort_unless((int)$insurance->company_id === (int)$companyId, 403);

        $page = 'Insurance Details';
        return view('vms.insurances.show', compact('page', 'insurance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $companyId = Session::get('company_id');
        $insurance = Insurance::with(['vehicle'])->findOrFail(decrypt($id));
        abort_unless((int)$insurance->company_id === (int)$companyId, 403);

        $page = 'Edit Insurance';

        $vehicles = Vehicle::where('company_id', $companyId)->orderBy('plate_no')->get();
        $insuranceCompanies = InsuranceCompany::where('company_id', $companyId)->orderBy('name')->get();
        $irPeriods = IrPeriod::where('company_id', $companyId)->orderBy('period')->get();

        return view('vms.insurances.edit', compact(
            'page',
            'insurance',
            'vehicles',
            'insuranceCompanies',
            'irPeriods'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $insurance = Insurance::findOrFail(decrypt($id));
        $companyId = Session::get('company_id');
        abort_unless((int)$insurance->company_id === (int)$companyId, 403);

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'ir_period_id' => 'required|exists:ir_periods,id',
            'policy_number' => 'required|string|max:255',
            'charge_payable' => 'required|numeric|min:0',
            'deductible' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'recurring_date' => 'nullable|date',
            'policy_attachment' => 'nullable|file|mimes:pdf|max:5120',
            'add_reminder' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        Vehicle::where('company_id', $companyId)->findOrFail($request->vehicle_id);
        InsuranceCompany::where('company_id', $companyId)->findOrFail($request->insurance_company_id);
        IrPeriod::where('company_id', $companyId)->findOrFail($request->ir_period_id);

        $data = [
            'insurance_company_id' => $request->insurance_company_id,
            'vehicle_id' => $request->vehicle_id,
            'ir_period_id' => $request->ir_period_id,
            'policy_number' => $request->policy_number,
            'charge_payable' => $request->charge_payable,
            'deductible' => $request->deductible,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'recurring_date' => $request->recurring_date,
            'add_reminder' => $request->has('add_reminder'),
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
        ];

        if ($request->hasFile('policy_attachment')) {
            // Replace old attachment.
            if ($insurance->policy_attachment) {
                if (Storage::disk('local')->exists($insurance->policy_attachment)) {
                    Storage::disk('local')->delete($insurance->policy_attachment);
                }
            }

            $data['policy_attachment'] = $request->file('policy_attachment')->store('insurances', 'local');
        }

        $insurance->update($data);

        return redirect()->route('insurance.index')->with('success', 'Insurance updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $insurance = Insurance::findOrFail(decrypt($id));
        $companyId = Session::get('company_id');
        abort_unless((int)$insurance->company_id === (int)$companyId, 403);

        if ($insurance->policy_attachment) {
            if (Storage::disk('local')->exists($insurance->policy_attachment)) {
                Storage::disk('local')->delete($insurance->policy_attachment);
            }
        }

        $insurance->delete();

        return redirect()->route('insurance.index')->with('success', 'Insurance deleted.');
    }

    public function download(string $id)
    {
        $insurance = Insurance::findOrFail(decrypt($id));

        if ($insurance->company_id != Session::get('company_id')) {
            abort(403);
        }

        $path = $insurance->policy_attachment;
        if (!$path) {
            abort(404);
        }

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->download($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path);
        }

        abort(404);
    }
}
