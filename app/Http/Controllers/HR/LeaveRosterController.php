<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use \Carbon\Carbon;
use Session;
use App\Models\Company;
use App\Models\Employee;
use App\Models\LeaveRoster;
use App\Models\EmployeeLeaveBalance;
use App\Models\Position;

class LeaveRosterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   $page = 'Leave Requests';
        $title = 'Leave Requests';
        $company = Company::find(Session::get('company_id'));
        $employees = Employee::where('company_id', $company->id)->select('id', 'fname', 'lname')->get();
        $leave_rosters = LeaveRoster::where('leave_rosters.company_id', $company->id)->join('employees' , 'employees.id' , '=' , 'leave_rosters.employee_id')->join('positions' , 'positions.id' , '=' , 'employees.position_id')->get([
                'leave_rosters.id',
                'fname',
                'mname',
                'lname',
                'leave_rosters.type',
                'reason',
                'approve_comments',
                'reject_reason',
                'leave_rosters.start_date',
                'leave_rosters.end_date',
                'name',
                'status',
                'approved_by',
                'approver_id',
                'leave_rosters.created_at'
            ])->sortByDesc('created_at');
        $lbalances = EmployeeLeaveBalance::join('employees','employees.id', '=', 'employee_leave_balances.employee_id')->select('employee_leave_balances.id as id', 'fname', 'lname', 'year', 'c_days', 'used')->orderBy('fname')->get();
        return view('hr.leave-rosters.index' , compact(['leave_rosters' , 'page' , 'title' , 'employees', 'lbalances']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $page = 'Request Leave';
        $title = 'Request Leave';
        $user = Auth::user();
        $company = Company::find(Session::get('company_id'));
        $leave_rosters = LeaveRoster::where('leave_rosters.company_id', $company->id)->where('employee_id' , $user->employee_id)->leftJoin('employees' , 'employees.id' , '=' , 'leave_rosters.approver_id')->leftJoin('positions' , 'positions.id' , '=' , 'employees.position_id')->get([
                'leave_rosters.id',
                'fname',
                'name',
                'lname',
                'leave_rosters.type',
                'reason',
                'approve_comments',
                'reject_reason',
                'leave_rosters.start_date',
                'leave_rosters.end_date',
                'status',
                'approved_by',
                'approver_id',
                'leave_rosters.created_at'
            ]);


        return view('hr.leave-rosters.create' , compact(['page' , 'title' , 'leave_rosters']));
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
        $leave_roster = LeaveRoster::create([
            'company_id' => $company->id,
            'employee_id' => $request->employee,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'Awaiting for Approval',
        ]);

        $location = null;
        if ($request->hasFile('file')) {
            //  Let's do everything here
            if ($request->file('file')->isValid()) {

                $extension = $request->file->extension();
                $request->file->storeAs('/public/lattachments', $company->id.'_logo.'.$extension);
                $location = $company->id.'_logo.'.$extension;
            }
        }
        $leave_roster->attachment = $location;
        $leave_roster->save();

        return redirect()->back()->with('success' , 'Leave Request added successfully');
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
        $page = 'Request Leave';
        $title = 'Request Leave';
        $leave_roster = LeaveRoster::find(decrypt($id));


        return view('hr.leave-rosters.edit' , compact(['page' , 'title', 'leave_roster']));
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
        $user = Auth::user();
        $leave = LeaveRoster::find(decrypt($id));
        $leave->type = $request->type;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->approve_comments = $request->approve_comments;
        $leave->status = 'Approved';
        $leave->approved_by = $user->name;
        $leave->approver_id = $user->id;
        $leave->save();

        if ($leave->type == 'Casual') {
            $leavBalance = EmployeeLeaveBalance::where('employee_id', $leave->employee_id)->where('year', date('Y', strtotime($leave->start_date)))->first();
            if (!is_null($leavBalance)) {
                $start = strtotime($leave->start_date);
                $end = strtotime($leave->end_date);
                $leavedays = round(($end-$start) / (60 * 60 * 24));
                $leavBalance->used = $leavBalance->used+$leavedays;
                $leavBalance->save();
            }else{
                $leavBalance = new EmployeeLeaveBalance();
                $leavBalance->employee_id = $leave->employee_id;
                $leavBalance->year = date('Y', strtotime($leave->start_date));
                $start = strtotime($leave->start_date);
                $end = strtotime($leave->end_date);
                $leavedays = round(($end-$start) / (60 * 60 * 24));
                $leavBalance->used = $leavedays;
                $leavBalance->save();
            }
        }

         return redirect('leave-rosters')->with('success' , 'Leave Request updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leave = LeaveRoster::find(decrypt($id));
        if (!is_null($leave)) {
            if ($leave->type == 'Casual') {
                $leavBalance = EmployeeLeaveBalance::where('employee_id', $leave->employee_id)->where('year', date('Y', strtotime($leave->start_date)))->first();
                if (!is_null($leavBalance)) {
                    $start = strtotime($leave->start_date);
                    $end = strtotime($leave->end_date);
                    $leavedays = round(($end-$start) / (60 * 60 * 24));
                    $leavBalance->used = $leavBalance->used-$leavedays;
                    $leavBalance->save();
                }
            }
            $leave->delete();
        }

        return redirect()->back()->with('success' , 'Leave Request deleted successfully');
    }

    public function approve($id){
        $user = Auth::user();
        $leave = LeaveRoster::find(decrypt($id));
        $leave->status = 'Approved';
        $leave->approved_by = $user->name;
        $leave->approver_id = $user->id;
        $leave->save();

        if ($leave->type == 'Casual') {
            $leavBalance = EmployeeLeaveBalance::where('employee_id', $leave->employee_id)->where('year', date('Y', strtotime($leave->start_date)))->first();
            if (!is_null($leavBalance)) {
                $start = strtotime($leave->start_date);
                $end = strtotime($leave->end_date);
                $leavedays = round(($end-$start) / (60 * 60 * 24));
                $leavBalance->used = $leavBalance->used+$leavedays;
                $leavBalance->save();
            }else{
                $leavBalance = new EmployeeLeaveBalance();
                $leavBalance->employee_id = $leave->employee_id;
                $leavBalance->year = date('Y', strtotime($leave->start_date));
                $start = strtotime($leave->start_date);
                $end = strtotime($leave->end_date);
                $leavedays = round(($end-$start) / (60 * 60 * 24));
                $leavBalance->used = $leavedays;
                $leavBalance->save();
            }
        }

        return redirect()->back()->with('success' , 'Leave Request approved');
    }

    public function reject(Request $request){

        $user = Auth::user();
        $leave = LeaveRoster::find($request->id);
        $leave->status = 'Rejected';
        $leave->approved_by = $user->name;
        $leave->approver_id = $user->id;
        $leave->is_rejected = true;
        $leave->reject_reason = $request->reason;
        $leave->save();

        return redirect()->back()->with('success' , 'Leave Request Rejected');
    }

    public function storeRequest(Request $request){

        $user = Auth::user();
        $company = Company::find(Session::get('company_id'));
        $leave_roster = LeaveRoster::create([
            'company_id' => $company->id,
            'employee_id' => $user->employee_id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'Awaiting for Approval',
        ]);

        return redirect()->back()->with('success' , 'Leave Request send successfully');
    }

    public function updateRequest(Request $request){

       
        $leave_roster = LeaveRoster::find($request->id);
        $leave_roster->type = $request->e_type;
        $leave_roster->start_date = $request->e_start_date;
        $leave_roster->end_date = $request->e_end_date;
        $leave_roster->reason = $request->e_reason;
        $leave_roster->save();

        return redirect()->back()->with('success' , 'Leave Request updated successfully');
    }
}
