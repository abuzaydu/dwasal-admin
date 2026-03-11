<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\LicenseType;
use Illuminate\Http\Request;

class LicenseTypeController extends Controller
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
        $companyId = session('company_id');

        if (!$companyId) {
            return redirect()->back()->with('error', 'Company session not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        LicenseType::create([
            'company_id' => $companyId,
            'name'       => $request->name,
        ]);

        return redirect()->back()->with('success', 'License type added successfully');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $licenseType = LicenseType::find($id);

        if (!$licenseType) {
            return redirect()->back()->with('error', 'License type not found!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $licenseType->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'License type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $licenseType = LicenseType::find($id);

        if (!$licenseType) {
            return redirect()->back()->with('error', 'License type not found!');
        }

        $licenseType->delete();

        return redirect()->back()->with('success', 'License type deleted successfully');
    }
}
