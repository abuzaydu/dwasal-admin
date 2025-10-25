<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\TransactionAccount;
use Log;

class COASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $companies = Company::select('id', 'name')->get();
        foreach ($companies as $key => $company) {
            Log::info($company->name);
            $accounts = array(
                ['account_number' => 1000, 'type' => 'Assets', 'account_name' => 'Cash'],
                ['account_number' => 1010, 'type' => 'Assets', 'account_name' => 'Accounts Receivable'],
                ['account_number' => 1020, 'type' => 'Assets', 'account_name' => 'Inventory'],
                ['account_number' => 1030, 'type' => 'Assets', 'account_name' => 'Prepaid Expenses'],
                ['account_number' => 1040, 'type' => 'Assets', 'account_name' => 'Fixed Assets'],
                ['account_number' => 1041, 'type' => 'Assets', 'account_name' => 'Office Equipment'],
                ['account_number' => 1042, 'type' => 'Assets', 'account_name' => 'Machinery'],
                ['account_number' => 1043, 'type' => 'Assets', 'account_name' => 'Vehicles'],
                ['account_number' => 1050, 'type' => 'Assets', 'account_name' => 'Salary Advance'],
                ['account_number' => 1060, 'type' => 'Assets', 'account_name' => 'Employee Recovery'],
                ['account_number' => 2000, 'type' => 'Liabilities', 'account_name' => 'Accounts Payable'],
                ['account_number' => 2010, 'type' => 'Liabilities', 'account_name' => 'Credit Card Payable'],
                ['account_number' => 2020, 'type' => 'Liabilities', 'account_name' => 'Taxes Payable'],
                ['account_number' => 2030, 'type' => 'Liabilities', 'account_name' => 'Long-Term Debt'],
                ['account_number' => 2040, 'type' => 'Liabilities', 'account_name' => 'Loans Payable'],
                ['account_number' => 2050, 'type' => 'Liabilities', 'account_name' => 'Social Security Contributions'],
                ['account_number' => 2060, 'type' => 'Liabilities', 'account_name' => 'Loans Board Payable'],
                ['account_number' => 3000, 'type' => 'Equity', 'account_name' => "Owner's Equity"],
                ['account_number' => 3010, 'type' => 'Equity', 'account_name' => 'Retained Earnings'],
                ['account_number' => 3020, 'type' => 'Equity', 'account_name' => 'Owner’s Drawings'],
                ['account_number' => 3020, 'type' => 'Equity', 'account_name' => 'Income Summary'],
                ['account_number' => 4000, 'type' => 'Revenue', 'account_name' => 'Sales Revenue'],
                ['account_number' => 4010, 'type' => 'Revenue', 'account_name' => 'Service Revenue'],
                ['account_number' => 4020, 'type' => 'Revenue', 'account_name' => 'Interest Income'],
                ['account_number' => 5000, 'type' => 'Expenses', 'account_name' => 'Cost of Goods Sold'],
                ['account_number' => 5010, 'type' => 'Expenses', 'account_name' => 'Rent Expense'],
                ['account_number' => 5020, 'type' => 'Expenses', 'account_name' => 'Utilities Expense'],
                ['account_number' => 5030, 'type' => 'Expenses', 'account_name' => 'Salaries & Wages'],
                ['account_number' => 5040, 'type' => 'Expenses', 'account_name' => 'Office Supplies'],
                ['account_number' => 5050, 'type' => 'Expenses', 'account_name' => 'Travel Expense'],
                ['account_number' => 5060, 'type' => 'Expenses', 'account_name' => 'Meals & Entertainment'],
                ['account_number' => 5070, 'type' => 'Expenses', 'account_name' => 'Depreciation Expense'],
                ['account_number' => 5080, 'type' => 'Expenses', 'account_name' => 'Insurance Expense'],
                ['account_number' => 5090, 'type' => 'Expenses', 'account_name' => 'Bank Charges'],
                ['account_number' => 5100, 'type' => 'Expenses', 'account_name' => 'Interest Expense'],
                ['account_number' => 5110, 'type' => 'Expenses', 'account_name' => 'Bad Debt Expense'],
                ['account_number' => 5120, 'type' => 'Expenses', 'account_name' => 'Reconciliation Discrepancies'],
                ['account_number' => 5130, 'type' => 'Expenses', 'account_name' => 'Advertising & Marketing'],
                ['account_number' => 5140, 'type' => 'Expenses', 'account_name' => 'Production Costs'],
            );

            foreach ($accounts as $key => $value) {
                $transacc = TransactionAccount::where('company_id', $company->id)->where('account_number', $value['account_number'])->first();
                if (is_null($transacc)) {
                    $transacc = new TransactionAccount();
                    $transacc->company_id = $company->id;
                    $transacc->account_number = $value['account_number'];
                    $transacc->account_name = $value['account_name'];
                    $transacc->type = $value['type'];
                    $transacc->save();
                }

                if ($value['account_number'] == 1000) {
                    $subaccounts = array(
                        ['parent_id' => $transacc->id, 'account_number' => 1001, 'type' => 'Assets', 'account_name' => 'Cash'],
                        ['parent_id' => $transacc->id, 'account_number' => 1002, 'type' => 'Assets', 'account_name' => 'Bank'],
                        ['parent_id' => $transacc->id, 'account_number' => 1003, 'type' => 'Assets', 'account_name' => 'Mobile Money'],
                        ['parent_id' => $transacc->id, 'account_number' => 1004, 'type' => 'Assets', 'account_name' => 'Petty Cash']
                    );
                    foreach ($subaccounts as $key => $sub) {
                        $subacc = TransactionAccount::where('company_id', $company->id)->where('account_number', $sub['account_number'])->first();
                        if (is_null($subacc)) {
                            $transacc = new TransactionAccount();
                            $transacc->company_id = $company->id;
                            $transacc->parent_id = $sub['parent_id'];
                            $transacc->account_number = $sub['account_number'];
                            $transacc->account_name = $sub['account_name'];
                            $transacc->type = $sub['type'];
                            $transacc->save();
                        }
                    }
                }
            }
        }
    }
}
