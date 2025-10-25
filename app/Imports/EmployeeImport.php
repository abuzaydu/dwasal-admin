<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Session;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Position;

class EmployeeImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {   
        $company = Company::find(Session::get('company_id'));
        $position_id = null;
        if (!is_null($row[12])) {
                
            $position = Position::where('company_id', $company->id)->where('name', $row[12])->first();
            if (!is_null($position)) {

                $position_id = $position->id;
            }else{

                $position = new Position();
                $position->company_id = $company->id;
                $position->name = $row[12];
                $position->basic_pay_monthly = $row[13];
                $position->trans_allowance = $row[14];
                $position->house_allowance = $row[15];
                $position->com_allowance = $row[16];

                $position->save();
                $position_id = $position->id;
            }
        }
        
        return new Employee([
            'company_id' => $company->id,
            'emp_id' => $row[0],
            'fname' => $row[1],
            'mname' => $row[2],
            'lname' => $row[3],
            'nin' => $row[4],
            'tin' => $row[5],
            'gender' => $row[6],
            'marital_status' => $row[7],
            'address' => $row[8],
            'mobile' => $row[9],
            'email' => $row[10],
            'type' => $row[11],
            'position_id' => $position_id,
            'basic_pay_monthly' => $row[13],
            'trans_allowance' => $row[14],
            'house_allowance' => $row[15],
            'com_allowance' => $row[16],
            'account_number' => $row[17],
            'account_name' => $row[18],
            'bank_name' => $row[19],
        ]);
    }
    
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
}
