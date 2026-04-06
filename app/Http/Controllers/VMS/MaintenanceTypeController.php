<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MaintenanceTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return redirect()->route('maintenance.index', [
            'tab' => 'types',
            'openTypeModal' => 1,
        ]);
    }

    public function create()
    {
        $page = 'Add Maintenance Type';
        return view('vms.maintenance-types.create', compact('page'));
    }

    public function store(Request $request)
    {
        $companyId = Session::get('company_id');

        $request->validate([
            'type' => 'required|string|max:255',
            'active' => 'nullable|boolean',
        ]);

        // Avoid duplicates like: same company + same type text
        $exists = MaintenanceType::where('company_id', $companyId)
            ->whereRaw('LOWER(type) = LOWER(?)', [$request->type])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('info', 'This maintenance type already exists for your company.');
        }

        MaintenanceType::create([
            'company_id' => $companyId,
            'type' => $request->type,
            'active' => $request->input('active', true),
        ]);

        return redirect()->route('maintenance.index', ['tab' => 'types'])->with('success', 'Maintenance type added successfully.');
    }

    public function edit(string $id)
    {
        return redirect()->route('maintenance.index', ['tab' => 'types'])->with('error', 'Edit maintenance type from Maintenance page.');
    }

    public function update(Request $request, string $id)
    {
        $companyId = Session::get('company_id');
        $maintenanceType = MaintenanceType::where('company_id', $companyId)->findOrFail(decrypt($id));

        $request->validate([
            'type' => 'required|string|max:255',
            'active' => 'nullable|boolean',
        ]);

        $maintenanceType->update([
            'type' => $request->type,
            'active' => $request->input('active', true),
        ]);

        return redirect()->route('maintenance.index', ['tab' => 'types'])->with('success', 'Maintenance type updated successfully.');
    }

    public function destroy(string $id)
    {
        $companyId = Session::get('company_id');
        $maintenanceType = MaintenanceType::where('company_id', $companyId)->findOrFail(decrypt($id));

        // Soft behavior: mark inactive instead of deleting (matches other modules style)
        $maintenanceType->active = false;
        $maintenanceType->save();

        return redirect()->route('maintenance.index', ['tab' => 'types'])->with('success', 'Maintenance type deactivated successfully.');
    }
}

