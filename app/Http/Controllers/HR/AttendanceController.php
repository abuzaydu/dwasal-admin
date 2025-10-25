<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use App\Models\Company;
use App\Models\EmployeeAttendance;
use App\Models\AttendanceSetting;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Holiday;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Attendance';
        $title = 'Employees Attendance';

        //check if user opted for date range
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }elseif(!empty($request['month'])){
            $date = Carbon::createFromFormat('d F Y', '05 '.$request['month']);
            $start = $date->firstOfMonth()->format('Y-m-d');
            $start_date = $start;
            $end = $date->endOfMonth()->format('Y-m-d');
            $end_date = $end;
            $is_post_query = true;
        }

        $m = Carbon::today()->startOfMonth();
        $n = Carbon::now();
        $y = Carbon::today()->startOfMonth()->format('Y');
        $curmonth = $m->monthName.' '.$y;
        if (!empty($request['month'])) {
            $curmonth = $request['month'];
        }

        // $days = $m->diffInDays($n);
        $company = Company::find(Session::get('company_id'));
        $setting = AttendanceSetting::where('company_id', $company->id)->first();
        if (!is_null($setting)) {
               
            $employees = Employee::where('company_id', $company->id)->get();
            $today = Carbon::now()->format('Y-m-d');
            $created_at = date('Y-m-d H:i:s', strtotime($today.' '.$setting->start_of_day));
            $holiday = Holiday::where('date', $today)->first();
            foreach ($employees as $key => $employee) {
                $attexit = EmployeeAttendance::where('company_id', $company->id)->where('employee_id', $employee->id)->where('created_at', $created_at)->first();
                if (is_null($attexit)) {
                    $newdayatt = new EmployeeAttendance();
                    $newdayatt->company_id = $company->id;
                    $newdayatt->employee_id = $employee->id;
                    if (!is_null($holiday)) {
                        $newdayatt->status = 'Holiday';
                    }elseif (Carbon::parse($today)->isWeekend()) {
                        $newdayatt->status = 'Weekend';
                    }
                    $newdayatt->save();
                    $newdayatt->created_at = $created_at;
                    $newdayatt->save();
                }
            }

            $attendance = [];
            foreach($employees as $emp){
                $at = [];
                $position = 'Not Assinged';
                $pst = Position::find($emp->position_id);
                if (!is_null($pst)) {
                    $position = $pst->name;
                }
                for ($i=0; $i < 31 ; $i++) {

                    $date = Carbon::parse($start)->addDays($i)->format('Y-m-d');
                    $empatt = EmployeeAttendance::where('company_id', $company->id)->where('employee_id' , $emp->id)->whereDate('created_at' , $date)->first();

                    if(!is_null($empatt)){
                        $com = collect([
                            'id' => $empatt->id,
                            'fname' => $emp->fname,
                            'mname'=> $emp->mname,
                            'lname' => $emp->lname,
                            'start_of_day' => $empatt->start_of_day,
                            'end_of_day' => $empatt->end_of_day,
                            'is_fullday' => $empatt->is_fullday,
                            'is_present' => $empatt->is_present,
                            'status' => $empatt->status,
                            'is_holiday' => $empatt->status == 'Holiday' ? true : false,
                            'is_late' => $empatt->is_late,
                            'positions' => $position,
                            'created_at' => $empatt->created_at,
                            'employee_id' => $emp->id,
                            'day' => $i+1,
                            'date' => $date,
                            'is_null' => false
                        ]);
                    }else{
                        $com = collect([
                            'fname' => $emp->fname,
                            'mname'=> $emp->mname,
                            'lname' => $emp->lname,
                            'positions' => $position,
                            'employee_id' => $emp->id,
                            'day' => $i+1,
                            'date' => $date,
                            'is_null' => true
                        ]);
                    }

                    array_push($at , $com);
                }

                array_push($attendance , $at);
            }

            // return $attendance;

            $data = array();
            for ($i = 12; $i >= 0; $i--) {
                $month = Carbon::today()->startOfMonth()->subMonth($i);
                $year = Carbon::today()->startOfMonth()->subMonth($i)->format('Y');
                array_push($data, array(
                    'month' => $month->monthName,
                    'year' => $year
                ));
            }

            return view('hr.attendance.index' , compact(['page' , 'title' , 'attendance' , 'employees' , 'data' ,'curmonth']));
        }else{
            return redirect('attendance-setting')->with('error', 'Please update Attendance Settings');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $page = 'Employee Attendance';
        $title = 'Employees Attendance';
        $date = Carbon::now();

        $attendance = EmployeeAttendance::where('employee_id' , decrypt($id))->whereDate('start_of_day' , $date->format('Y-m-d'))->get();

        return view('hr.attendance.show' , compact(['page' , 'title' , 'attendance' , 'data']));
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
        $company = Company::find(Session::get('company_id'));
        $setting = AttendanceSetting::where('company_id', $company->id)->first();
        $attendance = EmployeeAttendance::find($request->id);
        $date = Carbon::parse($attendance->created_at)->format('Y-m-d');
        $start_of_day =  Carbon::createFromFormat('Y-m-d H:i:s' , $date.' '.$setting->start_of_day);
        $end_of_day = Carbon::createFromFormat('Y-m-d H:i:s' , $date.' '.$setting->end_of_day);
        $start = null;
        $end = null;

        if(!empty($request->e_start_of_day)){
            $start = Carbon::createFromFormat('Y-m-d H:i:s' , $date.' '.$request->e_start_of_day.':00');
        }
        if(!empty($request->e_end_of_day)){
            $end = Carbon::createFromFormat('Y-m-d H:i:s' , $date.' '.$request->e_end_of_day.':00');
        }

        $attendance->start_of_day = $start;
        $attendance->end_of_day = $end;
        $attendance->save();

        if(!is_null($start)){

            if($attendance->status == 'Absent'){
               $attendance->status == 'Present'; 
            }

            $attendance->is_present = true;
            if($start->lessThanOrEqualTo($start_of_day)){
                $attendance->is_late = false;
            }else{
                 $attendance->is_late = true;
            }
            $attendance->save();
        }else{
            $attendance->is_present = false;
            $attendance->start_of_day = null;
            $attendance->end_of_day = null;
            $attendance->save();
        }

        if(!is_null($end)){
            if($end->greaterThanOrEqualTo($end_of_day)){
                $attendance->is_fullday = true;
            }else{
                $attendance->is_fullday = false;
            }
            $attendance->save(); 
        }
        return redirect()->back()->with('success' , 'Attendance is updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attendance = EmployeeAttendance::find($request->id);
        $attendance->delete();
        return redirect()->back()->with('success' , 'Attendance is deleted successfully');
    }

    public function punchIn(Request $request){
        $company = Company::find(Session::get('company_id'));
        $setting = AttendanceSetting::where('company_id', $company->id)->first();
        $start = Carbon::parse($request->start_of_day)->format('Y-m-d');
        if (!empty($request->employee)) {
            foreach($request->employee as $value){
                $att = EmployeeAttendance::whereDate('created_at' , $start )->where('employee_id' , $value)->first();

                if (is_null($att)) {
                    $att = new EmployeeAttendance();
                    $att->company_id = $company->id;
                    $att->employee_id = $value; 
                    $att->status = 'Present';
                    $att->created_at = $request->start_of_day;
                    $att->save();
                }

                $att->start_of_day = $request->start_of_day;
                if($att->status == 'Absent'){
                    $att->status == 'Present'; 
                }
                $att->is_present = true;
                $att->save();

                $arrive = Carbon::parse($request->start_of_day);
                $start_of_day = Carbon::createFromFormat('Y-m-d H:i:s' , Carbon::parse($request->start_of_day)->format('Y-m-d').' '.$setting->start_of_day);
                if($arrive->lessThanOrEqualTo($start_of_day)){
                    $att->is_late = false;
                    $att->save();
                }else{
                    $att->is_late = true;
                    $att->save();
                }

                if(!is_null($att->end_of_day)){

                    $leave = Carbon::parse($att->end_of_day);
                    $end_of_day = Carbon::createFromFormat('Y-m-d H:i:s' , Carbon::parse($att->end_of_day)->format('Y-m-d').' '.$setting->end_of_day);

                     if($leave->greaterThanOrEqualTo($end_of_day)){
                        $att->is_fullday = true;
                        $att->save();
                    }else{
                        $att->is_fullday = false;
                        $att->save();
                    }

                }
            }
            return redirect('hr-attendance')->with('success', 'success');
        }else{
            return redirect()->back()->with('info', 'No employee selected');
        }
    }

    public function punchOut(Request $request){
        $company = Company::find(Session::get('company_id'));
        $setting = AttendanceSetting::where('company_id', $company->id)->first();
        $date = Carbon::parse($request->end_of_day)->format('Y-m-d');
        if (!empty($request->employee)) {
            
            foreach($request->employee as $value){
                $att = EmployeeAttendance::whereDate('created_at' , $date )->where('employee_id' , $value)->first();
                if (is_null($att)) {

                    $att->end_of_day = $request->end_of_day;

                    $att->save();

                    $leave = Carbon::parse($request->end_of_day);
                    $end_of_day = Carbon::createFromFormat('Y-m-d H:i:s' , Carbon::parse($request->end_of_day)->format('Y-m-d').' '.$setting->end_of_day);

                    if($leave->greaterThanOrEqualTo($end_of_day)){
                        $att->is_fullday = true;
                        $att->save();
                    }else{
                        $att->is_fullday = false;
                        $att->save();
                    }
                }
            }
            return redirect('hr-attendance')->with('success', 'success');
        }else{
            return redirect()->back()->with('info', 'No employee selected');
        }
    }
}
