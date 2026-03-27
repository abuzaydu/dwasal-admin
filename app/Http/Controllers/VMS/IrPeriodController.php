<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\IrPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class IrPeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $companyId = Session::get('company_id');
        $page = 'Insurance Periods';

        $periods = IrPeriod::where('company_id', $companyId)
            ->orderByDesc('active')
            ->orderBy('period')
            ->paginate(20)
            ->withQueryString();

        return view('vms.ir-periods.index', compact('page', 'periods'));
    }

    public function store(Request $request)
    {
        $companyId = Session::get('company_id');

        $request->validate([
            'period' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'active' => 'sometimes|boolean',
        ]);

        IrPeriod::create([
            'company_id' => $companyId,
            'period' => $request->period,
            'description' => $request->description ?: 'N/A',
            'active' => $request->has('active'),
        ]);

        return redirect()->route('ir-periods.index')->with('success', 'IR period saved.');
    }

    public function edit(string $id)
    {
        $companyId = Session::get('company_id');
        $period = IrPeriod::findOrFail(decrypt($id));
        abort_unless((int) $period->company_id === (int) $companyId, 403);

        $page = 'Edit IR Period';
        return view('vms.ir-periods.edit', compact('page', 'period'));
    }

    public function update(Request $request, string $id)
    {
        $companyId = Session::get('company_id');
        $period = IrPeriod::findOrFail(decrypt($id));
        abort_unless((int) $period->company_id === (int) $companyId, 403);

        $request->validate([
            'period' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'active' => 'sometimes|boolean',
        ]);

        $period->update([
            'period' => $request->period,
            'description' => $request->description ?: 'N/A',
            'active' => $request->has('active'),
        ]);

        return redirect()->route('ir-periods.index')->with('success', 'IR period updated.');
    }

    public function destroy(string $id)
    {
        $companyId = Session::get('company_id');
        $period = IrPeriod::findOrFail(decrypt($id));
        abort_unless((int) $period->company_id === (int) $companyId, 403);

        $period->delete();
        return redirect()->route('ir-periods.index')->with('success', 'IR period deleted.');
    }
}

