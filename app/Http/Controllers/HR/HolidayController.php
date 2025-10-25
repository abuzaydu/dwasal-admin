<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Holidays';
        $title = 'Holidays';

        $holidays = Holiday::all();

        return view('hr.holidays.index' , compact(['page' , 'title' , 'holidays']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $holiday = Holiday::create([
            'name' => $request->name,
            'date' => $request->date,
            'is_recurring' => empty($request->rec) ? false : true,
        ]);

        return redirect()->back()->with('success' , 'Holiday add successful');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $holiday = Holiday::find(decrypt($id));

        $holiday->name = $request['e_name'];
        $holiday->is_recurring = empty($request->e_rec) ? false : true;
        $holiday->date = $request->e_date;
        $holiday->save();

        return redirect()->back()->with('success' , 'Holiday updated successful');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $holiday = Holiday::find(decrypt($id));
        $holiday->delete();
        return redirect()->back()->with('success' , 'Holiday deleted successful');
    }
}
