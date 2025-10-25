<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeMedicalInfo;
use Storage;

class EmployeeMedicalInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $emp =Employee::find($request->id);
        $link = "";
        if($request->hasFile('attachment')){
            $file = request()->file('attachment');

            $certificateName = time().$emp->fname.'-'.$emp->id_no.'.'.$file->getClientOriginalExtension();
            $destination =  storage_path('app/public/medical_forms');
            $file->move($destination, $certificateName);
            $link = 'medical_forms/'.$certificateName;
        }
        
        $med_info = EmployeeMedicalInfo::create([
            'employee_id' => $emp->id,
            'conditions_name' => $request['conditions_name'],
            'status' => $request['status'],
            'attachment' => $link,
        ]);

        return redirect()->back()->with('success' , 'Medical Information Added Succesfuly');
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
        $emp = Employee::find(decrypt($id));
        $med = EmployeeMedicalInfo::find($request->id);
        $med->conditions_name = $request['e_conditions_name'];
        $med->status = $request['e_status'];
        $med->save();

        if($request->hasFile('e_attachment')){

            $file = request()->file('e_attachment');
            $certificateName = time().$emp->fname.'-'.$emp->id_no.'.'.$file->getClientOriginalExtension();
            $destination =  storage_path('app/public/medical_forms');
            $file->move($destination, $certificateName);
            $link = 'medical_forms/'.$certificateName;

            $med->attachment = $link;
            $med->save();

            if(Storage::exists($med->attachment)){
                Storage::delete($med->attachment);
            }

        }

        return redirect()->back()->with('success' , 'Medical Information Updated Succesfuly');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $med = EmployeeMedicalInfo::find(decrypt($id));
        if(Storage::exists($med->attachment)){
            Storage::delete($med->attachment);
        }

        $med->delete();

         return redirect()->back()->with('success' , 'Medical Information Delete Succesfuly');
    }
}
