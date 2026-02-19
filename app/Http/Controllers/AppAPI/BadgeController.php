<?php

namespace App\Http\Controllers\AppAPI;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Company;
use Illuminate\Http\Request;

class BadgeController extends Controller
{

 public function index()
    {   $page = 'Badges';
        $badges = Badge::with('company')->latest()->paginate(20);
        return view('vml.badges.index', compact('badges', 'page'));
    }

    public function create()
    {   $page = 'Create Badge';
         $companies = Company::all();
        $companies = Company::all();
        $badges = Badge::all();
        return view('vml.badges.create', compact('companies', 'page', 'badges'));
    }
    public function storeBulk(Request $request)
    {
        $request->validate([
            'company_id'   => 'required|exists:companies,id',
            'badge_count'  => 'required|integer|min:1|max:100',
            'badge_prefix' => 'nullable|string|max:10',
        ]);

        $companyId  = $request->company_id;
        $count      = $request->badge_count;
        $prefix     = $request->badge_prefix ?? 'B';

        // Get the last badge number for this company to continue the sequence
        $lastBadge = Badge::where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->first();

        // Extract last number or start from 0
        $lastNumber = $lastBadge
            ? (int) filter_var($lastBadge->badge_number, FILTER_SANITIZE_NUMBER_INT)
            : 0;

        $rows = [];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'company_id'   => $companyId,
                'badge_number' => $prefix . str_pad($lastNumber + $i, 4, '0', STR_PAD_LEFT),
                'status'       => 'available',
            ];
        }

        Badge::insert($rows);

        return redirect()->route('badges.create')
            ->with('success', "{$count} badge(s) created successfully.");
    }

    public function destroy($id)
    {
        $badge = Badge::findOrFail(decrypt($id));
        $badge->delete();

        return redirect()->route('badges.index')
            ->with('success', 'Badge deleted successfully.');
    }

}
