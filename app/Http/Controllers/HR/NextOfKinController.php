<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\NextOfKin;

class NextOfKinController extends Controller
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
        $kin = new NextOfKin();
        $kin->employee_id = $request['id'];
        $kin->f_name = $request['f_name'];
        $kin->m_name = $request['m_name'];
        $kin->l_name = $request['l_name'];
        $kin->relationship = $request['relationship'];
        $kin->occupation = $request['occupation'];
        $kin->address = $request['address'];
        $kin->residence = $request['residence'];
        $kin->f_phone = $request['f_phone'];
        $kin->s_phone = $request['s_phone'];
        $kin->save();

        return redirect()->back()->with('success' , 'Next of kin added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   $page = 'Next Of Kin';
        $title = 'Next Of Kin Details';
        $nok = NextOfKin::find(decrypt($id));

        return view('hr.employees.next-of-kin.show' , compact(['page' , 'title' , 'nok']));
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
        $kin = NextOfKin::find($request['id']);

        $kin->employee_id = decrypt($id);
        $kin->f_name = $request['e_f_name'];
        $kin->m_name = $request['e_m_name'];
        $kin->l_name = $request['e_l_name'];
        $kin->relationship = $request['e_relationship'];
        $kin->occupation = $request['e_occupation'];
        $kin->address = $request['e_address'];
        $kin->residence = $request['e_residence'];
        $kin->f_phone = $request['e_f_phone'];
        $kin->s_phone = $request['e_s_phone'];
        $kin->save();

         return redirect()->back()->with('success' , 'Next of kin Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $kin = NextOfKin::find(decrypt($id));
        $kin->delete();   
        return redirect()->back()->with('success' , 'Next of kin Deleted successfully');
    }
}
