<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Departments';
        $title = 'Departments';
        $company = Company::find(Session::get('company_id'));
        $employees = Employee::where('company_id', $company->id)->get();

        $depts = Department::where('departments.company_id', $company->id)->leftJoin('employees' , 'employees.id' , '=' , 'departments.leader_id')->get([
            'departments.id',
            'name',
            'description',
            'fname',
            'lname',
            'leader_id'
        ]);
        return view('hr.departments.index' , compact(['page' , 'title' , 'depts' , 'employees']));
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
        $dept = Department::where('company_id', $company->id)->where('name', $request->name)->first();
        if (is_null($dept)) {
            $dept = new Department();
            $dept->company_id = $company->id;
            $dept->name = $request->name;
            $dept->description = $request->description;
            $dept->leader_id = $request->head;
            $dept->save();
        }

        if(!empty($request->head)){
            $dept->employees()->attach($request->head);
        }

        return redirect()->back()->with('success' , 'Department created successful');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $page = 'Department details';
        $title = 'Department details';
        $dept = Department::find(decrypt($id));
        $head = Employee::find($dept->leader_id);
        $position = Position::find($head->position_id);
        $members = $dept->employees()->get();
        $employees = Employee::all();

        return view('hr.departments.show' , compact([ 'page','title','dept' ,'head' ,'members' ,'employees' , 'position']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return 'OI';
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
        $dept = Department::find($request->id);

        $dept->name = $request->e_name;
        $dept->description = $request->e_description;
        $dept->leader_id = $request->e_head;
        $dept->save();

        if(!empty($request->e_head)){

           $exits = $dept->employees()->where('employee_id', $request->e_head)->first();
           if (is_null($exits)) {
                $dept->employees()->attach($request->e_head);
            }
        }

        return redirect()->back()->with('success' , 'Department updated successful');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Department::find(decrypt($id))->delete();

        return redirect()->back()->with('success' , 'Department deleted successful');
    }

    public function addMember(Request $request){

        $emp = Employee::find($request->employee);
        $dept = Department::find($request->id);

        $emps = $dept->employees()->wherePivot('employee_id' , $emp->id)->first();
        if(is_null($emps)){
            $dept->employees()->attach($emp->id);
        }else{

           return redirect()->back()->with('warning' , 'Member Exists in this Department');
        }
        

        return redirect()->back()->with('success' , 'Member Added Succesfuly');
    }

    public function removeMember($emp_id , $dept_id ){

        $emp = Employee::find(decrypt($emp_id));
        $dept = Department::find(decrypt($dept_id));
        $dept->employees()->detach($emp->id);

        return redirect()->back()->with('success' , 'Member Removed Succesfuly');
    }
}
