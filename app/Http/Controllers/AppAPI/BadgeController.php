<?php

namespace App\Http\Controllers\AppAPI;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Company;
use Illuminate\Http\Request;

class BadgeController extends Controller
{

 public function index()
    {  
         $page = 'Badges';
         $companies = Company::all();
        $companies = Company::all();
        $badges = Badge::all();
        return view('vml.badges.index', compact('badges', 'page','companies'));
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

    // Get last badge
    $lastBadge = Badge::where('company_id', $companyId)
        ->orderBy('id', 'desc')
        ->first();

    $lastNumber = $lastBadge
        ? (int) filter_var($lastBadge->badge_number, FILTER_SANITIZE_NUMBER_INT)
        : 0;

    $createdIds = [];

    for ($i = 1; $i <= $count; $i++) {

        $badge = Badge::create([
            'company_id'   => $companyId,
            'badge_number' => $prefix . str_pad($lastNumber + $i, 4, '0', STR_PAD_LEFT),
            'status'       => 'available',
        ]);

        $createdIds[] = $badge->id;
    }

    // 🔥 Redirect to auto print page
    return redirect()->route('badges.auto.print', [
        'ids' => implode(',', $createdIds)
    ]);
}

public function autoPrint(Request $request)
{
    $ids = explode(',', $request->ids);

    $badges = Badge::with('company')
        ->whereIn('id', $ids)
        ->get();

    return view('vml.badges.print', compact('badges'));
}

    public function destroy($id)
    {
        $badge = Badge::findOrFail(decrypt($id));
        $badge->delete();

        return redirect()->route('badges.index')
            ->with('success', 'Badge deleted successfully.');
    }

    public function printSelectedBadges(Request $request)
{
    $request->validate([
        'badge_ids' => 'required|array'
    ]);

    $badges = Badge::whereIn('id', $request->badge_ids)->get();

    return view('badges.print', compact('badges'));
}

}
