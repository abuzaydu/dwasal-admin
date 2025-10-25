<?php

namespace App\Http\Controllers\SandProd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\QualityTest;

class QualityTestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Quality Tests';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
        $is_post_query = false;
        
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }
        $duration = '';
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $qualitytests = QualityTest::where('quality_tests.shop_id', $shop->id)->whereBetween('test_date', [$start, $end])->join('production_runs', 'production_runs.id', '=', 'quality_tests.production_run_id')->join('users', 'users.id', '=', 'quality_tests.user_id')->select('quality_tests.id as id', 'pr_no', 'test_date', 'test_type', 'result', 'passed',  'first_name', 'last_name')->get();

            return view('production.sand.quality-tests.index', compact('page', 'is_post_query', 'start_date', 'end_date', 'duration', 'qualitytests'));
        }
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
