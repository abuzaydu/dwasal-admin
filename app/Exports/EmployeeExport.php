<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class EmployeeExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithCustomValueBinder
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Employee::select('emp_id', 'fname', 'mname', 'lname', 'nin', 'tin', 'gender', 'marital_status', 'address', 'mobile', 'email', 'type', 'basic_pay_monthly', 'account_number', 'account_name', 'bank_name', 'ssf', 'trans_allowance', 'house_allowance', 'com_allowance')->get();
    }

    public function headings() : array
    {
        return [
            'emp_id',
            'fname',
            'mname',
            'lname',
            'nin',
            'tin',
            'gender',
            'marital_status',
            'address',
            'mobile',
            'email',
            'type',
            'basic_pay_monthly',
            'account_number',
            'account_name',
            'bank_name',
            'ssf',
            'trans_allowance',
            'house_allowance',
            'com_allowance'
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() == 'nin') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
