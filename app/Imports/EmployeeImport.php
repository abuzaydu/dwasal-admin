<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Session;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Shop;
use Log;

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


        $shop = Shop::where('company_id', $company->id)->where('is_hq', true)->first();
        $shop_id = Session::get('shop_id');
        if (!is_null($shop)) {
            $shop_id = $shop->id;
        }
        
        $employee = new Employee();
        $employee->company_id = $company->id;
        $employee->shop_id = $shop_id;
        $employee->emp_id = $this->empID();
        $employee->fname = $row[1];
        $employee->mname = $row[2];
        $employee->lname = $row[3];
        $employee->nin = $row[4];
        $employee->tin = $row[5];
        $employee->gender = $row[6];
        $employee->marital_status = $row[7];
        $employee->address = $row[8];
        $employee->mobile = $row[9];
        $employee->email = $row[10];
        $employee->type = $row[11];
        $employee->position_id = $position_id;
        $employee->basic_pay_monthly = $row[13];
        $employee->trans_allowance = $row[14];
        $employee->house_allowance = $row[15];
        $employee->com_allowance = $row[16];
        $employee->account_number = $row[17];
        $employee->account_name = $row[18];
        $employee->bank_name = $row[19];
        $employee->is_reg_ssf = false;
        $employee->is_reg_mif = false;
        $employee->is_reg_wcf = true;
        $employee->save();

        return $employee;
    }
    
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function empID(){
        $company = Company::find(Session::get('company_id'));
        $v = '';
        if(preg_match_all('/\b(\w)/',strtoupper($company->name),$m)) {
            // Log::info($m);
            $v = implode('',$m[1]); // $v is now SOQTU
        }
        $employee = Employee::where('company_id', $company->id)->select('emp_id')->orderBy('id', 'desc')->first();
        if (!is_null($employee)) {
            if (!empty($employee->emp_id)) {
                $last = str_replace($v.'-', '', $employee->emp_id);
                $lastEmpID = (int)$last;
                // Log::info($last);
                $id = $v.'-'.sprintf('%03d', $lastEmpID+1);
                return $id;
            }else{
                $id = $v.'-'.sprintf('%03d', 1);
                return $id; 
            }   
        }else{
            $id = $v.'-'.sprintf('%03d', 1);
            return $id; 
        }
    }
}
