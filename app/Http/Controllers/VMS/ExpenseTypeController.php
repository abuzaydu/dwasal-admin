<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ExpenseTypeController extends Controller
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
            'type' => 'required|string',
            'active' => 'boolean'
            ]);
            ExpenseType::create([
                'company_id' => $companyId,
                'type' => $request->type,
                'active' => $request->input('active', true)
            ]);
            return redirect()->back()->with('success', 'Expense type created successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
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
            'type' => 'required|string',
            'active' => 'boolean'
            ]);
            $expenseType = ExpenseType::find($id);
            $expenseType->update([
                'type' => $request->type,
                'active' => $request->input('active', true)
            ]);
            return redirect()->back()->with('success', 'Expense type updated successfully');
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
            $expenseType = ExpenseType::find($id);
            if($expenseType){
               // $expenseType->delete();
               $expenseType ->active = false;
               $expenseType ->save();
                return redirect()->back()->with('success','Expense type deleted succcessfully');
            }
            return redirect()->back()->with('error', 'Expense type not found');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
