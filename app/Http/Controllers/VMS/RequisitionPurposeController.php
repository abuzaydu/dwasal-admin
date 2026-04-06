<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\RequisitionPurpose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RequisitionPurposeController extends Controller
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

        try {
            $companyId = Session::get('company_id');
            $request->validate([
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                'active' => 'boolean'
            ]);


            RequisitionPurpose::create([
                'company_id' =>$companyId,
                'purpose' => $request->purpose,
                'description' => $request->description,
                'active' => $request->input('active', true)
            ]);
            return redirect()->back()->with('success','Purpose created successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getmessage());
        }
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
        try {
            $request->validate([
                'purpose' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'active' => 'required|boolean',
            ]);

            $purpose = RequisitionPurpose::findOrFail($id);

            $purpose->update([
                'purpose' => $request->purpose,
                'description' => $request->description,
                'active' => $request->active,
            ]);

            return redirect()->back()->with('success', 'Purpose updated successfully.');
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
            $purpose = RequisitionPurpose::findOrFail(decrypt($id));

            $purpose->delete();

            return redirect()->back()->with('success', 'Purpose deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
