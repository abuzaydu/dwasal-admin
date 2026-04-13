<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\Ownership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OwnershipController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    /**
     * Custom ownership types are no longer created here — core types are seeded per company.
     */
    public function store(Request $request)
    {
        return redirect('vehicles')->with('error', 'Ownership types are predefined. Use the Ownership tab to activate or deactivate them.');
    }

    public function show(string $id)
    {
        return redirect()->route('ownerships.edit', $id);
    }

    public function edit(string $id)
    {
        $page = 'Edit Ownership';
        $ownership = Ownership::findOrFail(decrypt($id));

        if ((int) $ownership->company_id !== (int) Session::get('company_id')) {
            abort(403);
        }

        return view('vms.ownerships.edit', compact('page', 'ownership'));
    }

    public function update(Request $request, string $id)
    {
        $ownership = Ownership::findOrFail(decrypt($id));
        if ((int) $ownership->company_id !== (int) Session::get('company_id')) {
            abort(403);
        }

        if ($ownership->is_system) {
            $request->validate([
                'description' => 'nullable|string|max:500',
                'active' => 'required|in:0,1',
            ]);
            $ownership->description = $request->input('description');
            $ownership->active = $request->input('active') === '1' || $request->input('active') === 1;
            $ownership->save();
        } else {
            $request->validate([
                'type' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'active' => 'required|in:0,1',
            ]);
            $ownership->type = $request->input('type');
            $ownership->description = $request->input('description');
            $ownership->active = $request->input('active') === '1' || $request->input('active') === 1;
            $ownership->save();
        }

        return redirect('vehicles')->with('success', 'Ownership type updated successfully');
    }

    public function toggleActive(string $id)
    {
        $ownership = Ownership::findOrFail(decrypt($id));
        if ((int) $ownership->company_id !== (int) Session::get('company_id')) {
            abort(403);
        }

        $ownership->active = !$ownership->active;
        $ownership->save();

        return redirect('vehicles')->with('success', 'Ownership status updated.');
    }

    public function destroy(string $id)
    {
        $ownership = Ownership::find(decrypt($id));
        if ($ownership === null) {
            return redirect('vehicles')->with('error', 'Ownership type not found');
        }

        if ((int) $ownership->company_id !== (int) Session::get('company_id')) {
            abort(403);
        }

        if ($ownership->is_system) {
            return redirect('vehicles')->with('error', 'Core ownership types cannot be deleted. You can deactivate them instead.');
        }

        $ownership->delete();

        return redirect('vehicles')->with('success', 'Ownership Type deleted successfully');
    }
}
