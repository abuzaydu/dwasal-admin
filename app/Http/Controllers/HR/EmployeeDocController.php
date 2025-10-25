<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeDoc;
use App\Models\User;
use Storage;

class EmployeeDocController extends Controller
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
        if($request->hasFile('link')){
            $file = request()->file('link');

            $certificateName = time().$emp->fname.'-'.$emp->id_no.'.'.$file->getClientOriginalExtension();
            $destination =  storage_path('app/public/employee_docs');
            $file->move($destination, $certificateName);
            $link = 'employee_docs/'.$certificateName;
        }
        
        $docs_info = EmployeeDoc::create([
            'employee_id' => $emp->id,
            'name' => $request['name'],
            'type' => $request['type'],
            'link' => $link,
        ]);

        if($request->type == "Passport"){

            $user = User::where('employee_id' , $emp->id)->first();
            if(!is_null($user)){
                $user->user_photo = $link;
                $user->save();
            }
        }

        return redirect()->back()->with('success' , 'Document Information Added Succesfuly');
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
        $doc = EmployeeDoc::find($request->id);
        $doc->name = $request['e_name'];
        $doc->type = $request['e_type'];
        $doc->save();

        if($request->hasFile('e_link')){

            $file = request()->file('e_link');
            $certificateName = time().$emp->fname.'-'.$emp->id_no.'.'.$file->getClientOriginalExtension();
            $destination =  storage_path('app/public/employee_docs');
            $file->move($destination, $certificateName);
            $link = 'employee_docs/'.$certificateName;

            $doc->link = $link;
            $doc->save();

            if(Storage::exists($link->link)){
                Storage::delete($link->link);
            }

            if($request['e_type'] == "Passport"){
                $user = User::where('employee_id' , $emp->id)->first();
                if(!is_null($user)){
                    $user->user_photo = $link;
                    $user->save();
                }
               
            }

        }

        return redirect()->back()->with('success' , 'Document Information Updated Succesfuly');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $doc = EmployeeDoc::find(decrypt($id));
        if(Storage::exists($doc->link)){
            Storage::delete($doc->link);
        }

        $doc->delete();

         return redirect()->back()->with('success' , 'Document Information Delete Succesfuly');
    }
}
