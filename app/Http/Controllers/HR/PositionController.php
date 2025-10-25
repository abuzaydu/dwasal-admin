<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Company;
use App\Models\Position;

class PositionController extends Controller
{
    
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    function __construct()
    {
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Positions';
        $title = 'Positions';
        $company = Company::find(Session::get('company_id'));
        $positions = Position::where('company_id', $company->id)->get();

        return view('hr.positions.index', compact('page', 'title', 'positions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'New Position';
        $title = 'New Position';
        return view('hr.positions.create', compact('page', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $company = Company::find(Session::get('company_id'));
        $position = new Position();
        $position->company_id = $company->id;
        $position->name = $request['name'];
        $position->basic_pay_hourly = $request['basic_pay_hourly'];
        $position->basic_pay_monthly = $request['basic_pay_monthly'];
        $position->trans_allowance = $request['trans_allowance'];
        $position->house_allowance = $request['house_allowance'];
        $position->com_allowance = $request['com_allowance'];

        $position->save();

        return redirect()->back()->with('success', 'Position was added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return 'OI';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Position';
        $title = 'Edit Position';
        $position = Position::find(decrypt($id));

        return view('hr.positions.edit', compact('page', 'title', 'position'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $position = Position::find(decrypt($id));
        $position->name = $request['name'];
        $position->basic_pay_hourly = $request['basic_pay_hourly'];
        $position->basic_pay_monthly = $request['basic_pay_monthly'];
        $position->trans_allowance = $request['trans_allowance'];
        $position->house_allowance = $request['house_allowance'];
        $position->com_allowance = $request['com_allowance'];
        $position->save();

        return redirect('positions')->with('success', 'Position was updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $position = Position::find(decrypt($id));


        if (!is_null($position)) {
            try {
                $position->delete();
            } catch (\Illuminate\Database\QueryException $e) {
                
                return redirect()->back()->with('error', 'Position have can not be deleted because it have employees ');
            }
            
        }

        return redirect()->back()->with('success', 'Position was deleted successfully');
    }
}
