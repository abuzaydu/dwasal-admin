<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Company;
use App\Models\AttendanceSetting;

class AttendanceSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Attendance Setting';
        $title = 'Attendance Setting';
        $company = Company::find(Session::get('company_id'));
        $setting = AttendanceSetting::where('company_id', $company->id)->first();
        if (is_null($setting)) {
            $setting = AttendanceSetting::create([
                'company_id' => $company->id,
                'start_of_day' => date("H:i",mktime(8, 0)),
                'end_of_day' => date("H:i", mktime(16, 0)),
                'max_overtime' =>  6,
            ]);
        }


        return view('hr.attendance.settings' , compact(['setting' , 'page' ,'title']));
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
        $company = Company::find(Session::get('company_id'));
        AttendanceSetting::create([
            'company_id' => $company->id,
            'start_of_day' => $request->start_of_day,
            'end_of_day' => $request->end_of_day,
            'max_overtime' =>  $request->max_overtime,
            'works_on_weekend' => $request->works_on_weekend,
            'w_start_of_day' => $request->w_start_of_day,
            'w_end_of_day' => $request->w_end_of_day,
        ]);


        return redirect()->back()->with('success' , 'Settings updated successfully');
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
        
        $setting = AttendanceSetting::first();
        $setting->start_of_day = $request->start_of_day;
        $setting->end_of_day = $request->end_of_day;
        $setting->max_overtime = $request->max_overtime;
        $setting->works_on_weekend = empty($request->works_on_weekend) ? 0 : 1 ;
        $setting->w_start_of_day = $request->w_start_of_day;
        $setting->w_end_of_day = $request->w_end_of_day;

        $setting->save();


        return redirect()->back()->with('success' , 'Settings updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
