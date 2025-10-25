<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use Response;
use App\Models\PayrollTemp;
use Log;

class PayrollTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(PayrollTemp::where('payroll_temps.company_id', Session::get('company_id'))->where('user_id', Auth::user()->id)->join('employees', 'employees.id', '=', 'payroll_temps.employee_id')->select('payroll_temps.id as id', 'employee_id', 'fname', 'lname', 'basic_pay_hourly','basic_pay_monthly', 'days_work', 'overtime_hrs', 'bonuses', 'penalty', 'absences', 'late')->get());
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
        $user = Auth::user();
        $sameitems = PayrollTemp::where('company_id', Session::get('company_id'))->where('payroll_type', $request['payroll_type'])->where('user_id', $user->id)->count();
        
        if ($sameitems == 0) {
            $payrollTemp = new PayrollTemp();
            $payrollTemp->company_id = Session::get('company_id');
            $payrollTemp->user_id = $user->id;
            $payrollTemp->save();
            return $payrollTemp;
            
        }else{
            $warning = 'Ooops!. The payroll Type already in selected items.';
            return redirect()->back()->with('warning', $warning);
        }
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
        // Log::info($request);
        $payrollTemp =  PayrollTemp::find($id);
        // if (!is_null($payrollTemp)) {
            $payrollTemp->days_work = $request['days_work'];
            // $payrollTemp->overtime_hrs = $request['overtime_hrs'];
            // $payrollTemp->late = $request['late'];
            // $payrollTemp->absences = $request['absences'];
            $payrollTemp->bonuses = $request['bonuses'];
            $payrollTemp->penalty = $request['penalty'];
            $payrollTemp->save();
            return $payrollTemp;
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PayrollTemp::destroy($id);
    }
}
