<?php

namespace App\Http\Controllers\SandProd;

use App\Http\Controllers\Controller;
use App\Models\ProductionRun;
use App\Models\QualityTest;
use App\Models\StorageLocation;
use App\Models\WashingPlant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SandDashController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Production Dashboard';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
        $is_post_query = false;
        $duration = '';
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }
        $duration = '';
        $productionQuery = ProductionRun::whereBetween('created_at', [$start, $end])->where('shop_id', Session::get('shop_id'));

        $totalProductionRuns = (clone $productionQuery)->count();
        $totalWashedSandTons = (clone $productionQuery)->sum('output_quantity');

        $qualityQuery = QualityTest::whereBetween('test_date', [$start, $end])->where('shop_id', Session::get('shop_id'));
        $passCount = (clone $qualityQuery)->where('passed', 1)->count();

        $failCount = (clone $qualityQuery)->where('passed', 0)->count();
        $pendingCount = (clone $qualityQuery)->whereNull('passed')->count();

        $totalTests       = $passCount + $failCount + $pendingCount;
        $qualityPassRate  = $totalTests > 0 ? ($passCount / $totalTests) * 100 : 0;

        $activeWashingPlants = WashingPlant::where('shop_id', Session::get('shop_id'))->count();
        $totalStorageLocations = StorageLocation::count();

        //  Chart data
        $trendRows = (clone $productionQuery)->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(output_quantity) as tons'))->groupBy('day')->orderBy('day')->get();
        $productionTrend = [
            'labels' => $trendRows->map(fn ($row) => Carbon::parse($row->day)->format('d M'))->toArray(),
            'values' => $trendRows->map(fn ($row) => (float) $row->tons)->toArray(),
        ];

        $qualityBreakdown = [
            'pass'    => $passCount,
            'fail'    => $failCount,
            'pending' => $pendingCount,
        ];

        // Recent runs table
        $recentProductions = (clone $productionQuery)->with('washingPlant')->orderByDesc('created_at')->limit(10)->get();

        return view('production.sand.index', compact('page', 'start_date', 'end_date', 'is_post_query', 'duration',
            'totalProductionRuns', 'totalWashedSandTons', 'qualityPassRate', 'activeWashingPlants',
            'totalStorageLocations', 'productionTrend', 'qualityBreakdown', 'recentProductions'));
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
