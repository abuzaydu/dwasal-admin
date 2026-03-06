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
        $badges = Badge::orderBy('created_at', 'desc')->get();
        return view('vml.badges.index', compact('badges', 'page','companies'));
    }
    public function show($id)
    {
        $page = 'Badge Details';
        $badges = Badge::with('company')->findOrFail(decrypt($id));
        //dd($badges);
        return view('vml.badges.show', compact('badges', 'page'));
    }
   
    public function storeBulk(Request $request)
    {
        $request->validate([
            'badge_count'  => 'required|integer|min:1|max:100',
            'badge_prefix' => 'nullable|string|max:10',
        ]);

        $companyId  = $request->session()->get('company_id');
        //dd($companyId);
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

        return redirect()->route('badges.index')->with('success', "$count badges created successfully.");
    }

    public function autoPrint(Request $request)
    {
        $ids = explode(',', $request->ids);

        $badges = Badge::with('company')
            ->whereIn('id', $ids)
            ->get();

        return view('vml.badges.print', compact('badges'));
    }

    public function autoPrintFOrOneBadge(Request $request)
    {
        $encryptedIds = $request->input('ids', []);

        $badges = Badge::with('company')
            ->whereIn('id', collect($encryptedIds)->map(fn($id) => decrypt($id)))
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

    public function printSelected(Request $request)
    {
        
        $encryptedIds = $request->input('ids', []);

        $decryptedIds = collect($encryptedIds)->map(function ($id) {
            try {
                return decrypt($id);
            } catch (\Exception $e) {
                return null;
            }
        })->filter()->values();

        $badges = Badge::with('company')
            ->whereIn('id', $decryptedIds)
            ->get();

        return view('vml.badges.print', compact('badges'));
    }

}
