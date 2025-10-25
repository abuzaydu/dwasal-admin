<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\AcademicInfo;
use Storage;

class AcademicInfoController extends Controller
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
        if($request->hasFile('certificate_link')){
            $file = request()->file('certificate_link');

            $certificateName = time().$emp->fname.'-'.$emp->id_no.'.'.$file->getClientOriginalExtension();
            $destination =  storage_path('app/public/academic_certificates');
            $file->move($destination, $certificateName);
            $link = 'academic_certificates/'.$certificateName;
        }

        $acd = AcademicInfo::create([
            'employee_id' => $emp->id,
            'level' => $request['level'],
            'institution' => $request['institution'],
            'title' => $request['title'],
            'certificate_link' => $link,
        ]);

        return redirect()->back()->with('success' , 'Academic Infos Added Succesfuly');
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
    {   $emp = Employee::find(decrypt($id));
        $acd = AcademicInfo::find($request->id);
        $acd->level = $request['e_level'];
        $acd->institution = $request['e_institution'];
        $acd->title = $request['e_title'];
        $acd->save();

        if($request->hasFile('certificate_link')){

            $file = request()->file('certificate_link');
            $certificateName = time().$emp->fname.'-'.$emp->id_no.'.'.$file->getClientOriginalExtension();
            $destination =  storage_path('app/public/academic_certificates');
            $file->move($destination, $certificateName);
            $link = 'academic_certificates/'.$certificateName;

            $acd->certificate_link = $link;
            $acd->save();

            if(Storage::exists($acd->certificate_link)){
                Storage::delete($acd->certificate_link);
            }

        }

        return redirect()->back()->with('success' , 'Academic Infos Updated Succesfuly');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $acd = AcademicInfo::find(decrypt($id));

        if(Storage::exists($acd->certificate_link)){
            Storage::delete($acd->certificate_link);
        }

        $acd->delete();

        return redirect()->back()->with('success' , 'Academic Infos Deleted Succesfuly');
    }
}
