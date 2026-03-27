<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class InsuranceCompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $companyId = Session::get('company_id');
        $page = 'Insurance Companies';

        $companies = InsuranceCompany::where('company_id', $companyId)
            ->orderByDesc('active')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('vms.insurance-companies.index', compact('page', 'companies'));
    }

    public function store(Request $request)
    {
        $companyId = Session::get('company_id');

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'active' => 'sometimes|boolean',
        ]);

        InsuranceCompany::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'description' => $request->description ?: 'N/A',
            'active' => $request->has('active'),
        ]);

        return redirect()->route('insurance-companies.index')->with('success', 'Insurance company saved.');
    }

    public function edit(string $id)
    {
        $companyId = Session::get('company_id');
        $company = InsuranceCompany::findOrFail(decrypt($id));
        abort_unless((int) $company->company_id === (int) $companyId, 403);

        $page = 'Edit Insurance Company';
        return view('vms.insurance-companies.edit', compact('page', 'company'));
    }

    public function update(Request $request, string $id)
    {
        $companyId = Session::get('company_id');
        $company = InsuranceCompany::findOrFail(decrypt($id));
        abort_unless((int) $company->company_id === (int) $companyId, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'active' => 'sometimes|boolean',
        ]);

        $company->update([
            'name' => $request->name,
            'description' => $request->description ?: 'N/A',
            'active' => $request->has('active'),
        ]);

        return redirect()->route('insurance-companies.index')->with('success', 'Insurance company updated.');
    }

    public function destroy(string $id)
    {
        $companyId = Session::get('company_id');
        $company = InsuranceCompany::findOrFail(decrypt($id));
        abort_unless((int) $company->company_id === (int) $companyId, 403);

        $company->delete();
        return redirect()->route('insurance-companies.index')->with('success', 'Insurance company deleted.');
    }
}

