<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Company;
use App\Models\PayrollSetting;

class PayrollSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Payroll Settings';
        $title = 'Payroll Settings';
        $company = Company::find(Session::get('company_id'));
        $settings = array(
            ['name' => 'NSSF', 'description' => 'Social Security Fund', 'percent_rate' => 10, 'fixed_paye_value' => 0, 'min_income' => 0, 'max_income' => 0],
            ['name' => 'NHIF', 'description' => 'Healthy Insurance', 'percent_rate' => 3, 'fixed_paye_value' => 0, 'min_income' => 0, 'max_income' => 0],
            ['name' => 'WCF', 'description' => 'Workers Compensation Fund', 'percent_rate' => 0.5, 'fixed_paye_value' => 0, 'min_income' => 0, 'max_income' => 0],
            ['name' => 'PAYE I', 'description' => 'Where the total income does not exceed 270,000/=. Tax Rate : NIL', 'percent_rate' => 0, 'fixed_paye_value' => 0, 'min_income' => 0, 'max_income' => 270000],
            ['name' => 'PAYE II', 'description' => 'Where the total income exceeds 270,000/= but does not exceed Tshs. 520,000/=. Tax Rate : 8% of the amount in excess of the amount in excess of Tshs. 270,000/=', 'percent_rate' => 8, 'fixed_paye_value' => 0, 'min_income' => 270000, 'max_income' => 520000],
            ['name' => 'PAYE III', 'description' => 'Where the total income exceeds 520,000/= but does not exceed Tshs. 760,000/=. Tax Rate : Tshs. 20,000/= plus 20% of the amount in excess of Tshs. 520,000/=', 'percent_rate' => 20,  'fixed_paye_value' => 20000, 'min_income' => 520000, 'max_income' => 760000],
            ['name' => 'PAYE IV', 'description' => 'Where the total income exceeds 760,000/= but does not exceed Tshs. 1,000,000/=. Tax Rate : Tshs. 68,000/= plus 25% of the amount in excess of Tshs. 760,000/=', 'percent_rate' => 25, 'fixed_paye_value' => 68000, 'min_income' => 760000, 'max_income' => 1000000],
            ['name' => 'PAYE V', 'description' => 'Where the total income is above 1,000,000/=. Tax Rate : Tshs. 128,000/= plus 30% of the amount in excess of Tshs. 1,000,000/=', 'percent_rate' => 30, 'fixed_paye_value' => 128000, 'min_income' => 1000000, 'max_income' => 1000000000],
            ['name' => 'HESLB', 'description' => 'Loans Board', 'percent_rate' => 15, 'fixed_paye_value' => 0, 'min_income' => 0, 'max_income' => 0],
        );

        foreach ($settings as $key => $value) {
            $prsetting = PayrollSetting::where('company_id', $company->id)->where('name', $value['name'])->first();
            if (is_null($prsetting)) {
                $prsetting = new PayrollSetting();
                $prsetting->company_id = $company->id;
                $prsetting->name = $value['name'];
                $prsetting->description = $value['description'];
                $prsetting->percent_rate = $value['percent_rate'];
                $prsetting->fixed_paye_value = $value['fixed_paye_value'];
                $prsetting->min_income = $value['min_income'];
                $prsetting->max_income = $value['max_income'];
                $prsetting->save();
            }else{
                $prsetting->description = $value['description'];
                $prsetting->percent_rate = $value['percent_rate'];
                $prsetting->fixed_paye_value = $value['fixed_paye_value'];
                $prsetting->min_income = $value['min_income'];
                $prsetting->max_income = $value['max_income'];
                $prsetting->save();
            }
        }
        $psettings = PayrollSetting::where('company_id', $company->id)->get();

        return view('payrolls.settings.index', compact('page', 'title', 'psettings'));
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
        $psetting = PayrollSetting::create([
            'company_id' => Session::get('company_id'),
            'name' => $request['name'],
            'percent_rate' => $request['percent_rate'],
            'description' => $request['description'],
            'min_income' => $request['min_income'],
            'max_income' => $request['max_income'],
        ]);

        return redirect('payroll-settings')->with('Settings option added successfully');
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
        $page = 'Edit Payroll Setting';
        $title = 'Edit Payroll Setting';
        $psetting = PayrollSetting::find(decrypt($id));
        
        return view('payrolls.settings.edit', compact('page', 'title', 'psetting'));
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
        $psetting = PayrollSetting::find(decrypt($id));
        $psetting->name = $request['name'];
        $psetting->percent_rate = $request['percent_rate'];
        $psetting->fixed_paye_value = $request['fixed_paye_value'];
        $psetting->description = $request['description'];
        $psetting->min_income = $request['min_income'];
        $psetting->max_income = $request['max_income'];
        $psetting->save();

        return redirect('payroll-settings')->with('success', 'Payroll settings updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $psetting = PayrollSetting::find(decrypt($id));
        if (!is_null($psetting)) {
            $psetting->delete();
        }

        return redirect()->back()->with('success', 'Payroll settings deleted successfully');
    }
}
