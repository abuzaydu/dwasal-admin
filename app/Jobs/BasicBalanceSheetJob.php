<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use DB;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\CashIn;
use App\Models\CashOut;
use App\Models\AccountTransaction;
use App\Models\SalePayment;
use App\Models\Expense;
use App\Models\ExpensePayment;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\RmPurchasePayment;
use App\Models\PmPurchasePayment;
use App\Models\PlcPayment;
use App\Models\MohCostPayment;
use App\Models\BankDetail;
use App\Models\Accountstatement;
use App\Models\Account;
use App\Models\PettyCash;
use App\Models\Customer;
use App\Models\AnSale;
use App\Models\CustomerTransaction;
use App\Models\Stock;
use App\Models\SupplierTransaction;
use App\Models\BasicBalanceSheet;

class BasicBalanceSheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shop;
    /**
     * Create a new job instance.
     */
    public function __construct($shop)
    {
        $this->shop = $shop;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shop = $this->shop;
        Log::info('Basic balance sheet for : '.$shop->name);

        $lastday = Carbon::now()->subYear(1)->endOfYear();
        $end = $lastday->endOfDay();
        Log::info($end);
        if ((int)date('Y', strtotime($end)) > 2024) {
            $bs_date = date('Y-m-d', strtotime($end));

            $mbs = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->count();
            if ($mbs <= 0) {
                Log::info('balance sheet Date : '.$bs_date);
                //CURRENT ASSETS
                // Closing Cash balances;
                $cashpay = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->where('pay_date', '<=', $end)->where('pay_mode', 'Cash')->sum('amount');

                $mobipay = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->where('pay_date', '<=', $end)->where('pay_mode', 'Mobile Money')->sum('amount');
              
                $bankpay = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->where('pay_date', '<=', $end)->where('pay_mode', 'Bank')->sum('amount');

                $totalcashin = CashIn::where('cash_ins.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_ins.account_id')->where('in_date', '<=', $end)->where('type', 'Cash')->sum('amount');

                $totalbankin = CashIn::where('cash_ins.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_ins.account_id')->where('in_date', '<=', $end)->where('type', 'Bank')->sum('amount');

                $totalmobiin = CashIn::where('cash_ins.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_ins.account_id')->where('in_date', '<=', $end)->where('type', 'Mobile Money')->sum('amount');

                $totalcashout = CashOut::where('cash_outs.shop_id', $shop->id)->join('accounts', 'accounts.id','=', 'cash_outs.account_id')->where('out_date', '<=', $end)->where('type', 'Cash')->sum('amount');

                $totalbankout = CashOut::where('cash_outs.shop_id', $shop->id)->join('accounts', 'accounts.id','=', 'cash_outs.account_id')->where('out_date', '<=', $end)->where('type', 'Bank')->sum('amount');

                $totalmobiout = CashOut::where('cash_outs.shop_id', $shop->id)->join('accounts', 'accounts.id','=', 'cash_outs.account_id')->where('out_date', '<=', $end)->where('type', 'Mobile Money')->sum('amount');

                $totalcashppay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_date', '<=', $end)->where('pay_mode', 'Cash')->sum('amount');

                $totalbankppay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_date', '<=', $end)->where('pay_mode', 'Bank')->sum('amount');

                $totalmobippay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_date', '<=', $end)->where('pay_mode', 'Mobile Money')->sum('amount');

                $totalcashexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('pay_date', '<=', $end)->where('pay_mode', 'Cash')->sum('expense_payments.amount');
                $totalpettycashexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('pay_date', '<=', $end)->where('pay_mode', 'Petty Cash')->sum('expense_payments.amount');

                $totalbankexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('pay_date', '<=', $end)->where('pay_mode', 'Bank')->sum('expense_payments.amount');

                $totalmobiexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('pay_date', '<=', $end)->where('pay_mode', 'Mobile Money')->sum('expense_payments.amount');

                $cash_to_bank = AccountTransaction::where('shop_id', $shop->id)->where('date', '<=', $end)->where('from', 'Cash')->where('to', 'Bank')->sum('amount');
                $cash_to_mobile = AccountTransaction::where('shop_id', $shop->id)->where('date', '<=', $end)->where('from', 'Cash')->where('to', 'Mobile Money')->sum('amount');

                $mobile_to_bank = AccountTransaction::where('shop_id', $shop->id)->where('date', '<=', $end)->where('from', 'Mobile Money')->where('to', 'Bank')->sum('amount');
                $mobile_to_cash = AccountTransaction::where('shop_id', $shop->id)->where('date', '<=', $end)->where('from', 'Mobile Money')->where('to', 'Cash')->sum('amount');

                $bank_to_cash = AccountTransaction::where('shop_id', $shop->id)->where('date', '<=', $end)->where('from', 'Bank')->where('to', 'Cash')->sum('amount');
                $bank_to_mobile = AccountTransaction::where('shop_id', $shop->id)->where('date', '<=', $end)->where('from', 'Bank')->where('to', 'Mobile Money')->sum('amount');

                
                $rm_cash_pay = RmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Cash')->sum('amount');
                $rm_mobi_pay = RmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Mobile Money')->sum('amount');
                $rm_bank_pay = RmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Bank')->sum('amount');

                $pm_cash_pay = PmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Cash')->sum('amount');
                $pm_mobi_pay = PmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Mobile Money')->sum('amount');
                $pm_bank_pay = PmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Bank')->sum('amount');

                $plc_cash_pay = PlcPayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Cash')->sum('amount');
                $plc_petty_cash_pay = PlcPayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Petty Cash')->sum('amount');
                $plc_mob1_pay = PlcPayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Mobile Money')->sum('amount');
                $plc_bank_pay = PlcPayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Bank')->sum('amount');

                $moh_cash_pay = MohCostPayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Cash')->sum('amount');
                $moh_petty_cash_pay = MohCostPayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Petty Cash')->sum('amount');
                $moh_mobi_pay = MohCostPayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Mobile Money')->sum('amount');
                $moh_bank_pay = MohCostPayment::where('shop_id', $shop->id)->where('pay_date', '<=', $end)->where('pay_mode', 'Bank')->sum('amount');

                //Petty cash
                // $pettycash = PettyCash::where('shop_id', $shop->id)->where('request_date', '<=', $end)->where('pay_mode', 'Cash')->where('status', 'Received')->sum('amount');
                $pettymobi = PettyCash::where('shop_id', $shop->id)->where('request_date', '<=', $end)->where('pay_mode', 'Mobile Money')->where('status', 'Received')->sum('amount');
                $pettybank = PettyCash::where('shop_id', $shop->id)->where('request_date', '<=', $end)->where('pay_mode', 'Bank')->where('status', 'Received')->sum('amount');

                $cashBal = ($totalcashin+$cashpay+$bank_to_cash+$mobile_to_cash+$pettybank+$pettymobi)- ($totalcashout+$totalcashppay+$totalcashexp+$cash_to_bank+$cash_to_mobile+$rm_cash_pay+$pm_cash_pay+$plc_cash_pay+$moh_cash_pay+$totalpettycashexp+$plc_petty_cash_pay+$moh_petty_cash_pay);

                $mobiBal = ($totalmobiin+$mobipay+$cash_to_mobile+$bank_to_mobile)-($totalmobiout+$totalmobippay+$totalmobiexp+$mobile_to_cash+$mobile_to_bank+$rm_mobi_pay+$pm_mobi_pay+$plc_mob1_pay+$moh_mobi_pay+$pettymobi);
              
                $bankBal = ($totalbankin+$bankpay+$cash_to_bank+$mobile_to_bank)-($totalbankout+$totalbankppay+$totalbankexp+$bank_to_cash+$bank_to_mobile+$rm_bank_pay+$pm_bank_pay+$plc_bank_pay+$moh_bank_pay+$pettybank);

                $cash_in_hand = $cashBal+$mobiBal+$bankBal;

                Log::info('Cash in Hand : '.$cash_in_hand);
                $mbs_cash = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Cash')->first();
                if (is_null($mbs_cash)) {
                    $mbs_cash = new BasicBalanceSheet();
                    $mbs_cash->shop_id = $shop->id;
                    $mbs_cash->date = $end;
                    $mbs_cash->item_desc = 'Cash';
                    $mbs_cash->item_category = 'CURRENT ASSETS';
                    $mbs_cash->amount = $cash_in_hand;
                    $mbs_cash->save();
                }
                //End closing cash balance

                // Account receivables
                $account_receivable = 0;
                $customers = Customer::where('shop_id', $shop->id)->get();
                foreach ($customers as $key => $customer) {
                    $obtrans = CustomerTransaction::where('customer_id', $customer->id)->where('invoice_no', 'OB')->where('shop_id', $shop->id)->first();
                    $opening_balance = 0;
                    if (!is_null($obtrans)) {
                        $opening_balance = $obtrans->amount-$obtrans->ob_paid;
                    }

                    $totalsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->where('customer_id', $customer->id)->where('time_created', '<=', $end)->get([
                        DB::raw('SUM(sale_amount) as sale_amount'),
                        DB::raw('SUM(sale_discount) as sale_discount'),
                        DB::raw('SUM(tax_amount) as tax_amount'),
                        DB::raw('SUM(return_amount) as return_amount'),
                        DB::raw('SUM(return_discount) as return_discount'),
                        DB::raw('SUM(return_tax) as return_tax'),
                        DB::raw('SUM(sale_amount_paid) as amount_paid')
                    ]);
                    
                    $new_invoices = 0;
                    foreach ($totalsales as $key => $value) {
                        $new_invoices += ((($value->sale_amount-$value->sale_discount)+$value->tax_amount)-(($value->return_amount-$value->return_discount)+$value->return_tax))-$value->amount_paid;
                    }

                    $account_receivable += ($opening_balance+$new_invoices);
                }

                Log::info('Account receivable : '.$account_receivable);
                $mbs_ar = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Accounts Receivable')->first();
                if (is_null($mbs_ar)) {
                    $mbs_ar = new BasicBalanceSheet();
                    $mbs_ar->shop_id = $shop->id;
                    $mbs_ar->date = $end;
                    $mbs_ar->item_desc = 'Accounts Receivable';
                    $mbs_ar->item_category = 'CURRENT ASSETS';
                    $mbs_ar->amount = $account_receivable;
                    $mbs_ar->save();
                }
                //End Account Receivable
                
                // Inventory value
                $prodstocks = array();
                $stockvalues = array(); 
                $products = $shop->products()->where('is_active', true)->get([
                    \DB::raw('id'),
                    \DB::raw('slug as name'),
                    \DB::raw('basic_unit'),
                    \DB::raw('in_stock as in_stock'),
                    \DB::raw('unit_cost'),
                    \DB::raw('retail_price'),
                    \DB::raw('wholesale_price')
                ]);

                foreach ($products as $key => $stock) {
                    array_push($prodstocks, ['id' => $stock->id, 'name' => $stock->name, 'basic_unit' => $stock->basic_unit, 'in_stock' => $stock->in_stock, 'unit_cost' => $stock->unit_cost, 'retail_price' => $stock->retail_price, 'wholesale_price' => $stock->wholesale_price]);
                }
                
                foreach ($prodstocks as $key => $value) {
                    if ($value['in_stock'] > 0) {
                        $lstock = Stock::where('shop_id', $shop->id)->where('product_id', $value['id'])->where('is_deleted', false)->where('is_utilized', false)->latest()->first();
                        $unit_cost = $value['unit_cost'];
                        if (!is_null($lstock) && $lstock->unit_cost != $value['unit_cost']) {
                            $unit_cost = $lstock->unit_cost;
                        }
                        array_push($stockvalues, ['name' => $value['name'], 'basic_unit' => $value['basic_unit'], 'qty' => $value['in_stock'], 'unit_cost' => $unit_cost, 'retail_price' => $value['retail_price'], 'wholesale_price' => $value['wholesale_price']]);
                    }
                }

                $inventory_value = 0;
                foreach ($stockvalues as $key => $stock) {
                    $inventory_value += ($stock['qty']*$stock['unit_cost']);
                }
                Log::info('Inventory value : '.$inventory_value);
                $mbs_inventory = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Inventory')->first();
                if (is_null($mbs_inventory)) {
                    $mbs_inventory = new BasicBalanceSheet();
                    $mbs_inventory->shop_id = $shop->id;
                    $mbs_inventory->date = $end;
                    $mbs_inventory->item_desc = 'Inventory';
                    $mbs_inventory->item_category = 'CURRENT ASSETS';
                    $mbs_inventory->amount = $inventory_value;
                    $mbs_inventory->save();
                }
                //Invemtory End

                //Pre-Paid Expenses
                $prepaid_expenses = 0;
                $mbs_prepaid_exp = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Prepaid Expenses')->first();
                if (is_null($mbs_prepaid_exp)) {
                    $mbs_prepaid_exp = new BasicBalanceSheet();
                    $mbs_prepaid_exp->shop_id = $shop->id;
                    $mbs_prepaid_exp->date = $end;
                    $mbs_prepaid_exp->item_desc = 'Prepaid Expenses';
                    $mbs_prepaid_exp->item_category = 'CURRENT ASSETS';
                    $mbs_prepaid_exp->amount = $prepaid_expenses;
                    $mbs_prepaid_exp->save();
                }
                //End Pre paid Expenses

                //Short-term Investments
                $short_term_investments = 0;
                $mbs_sti = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Short-Term Investments')->first();
                if (is_null($mbs_sti)) {
                    $mbs_sti = new BasicBalanceSheet();
                    $mbs_sti->shop_id = $shop->id;
                    $mbs_sti->date = $end;
                    $mbs_sti->item_desc = 'Short-Term Investments';
                    $mbs_sti->item_category = 'CURRENT ASSETS';
                    $mbs_sti->amount = $short_term_investments;
                    $mbs_sti->save();
                }
                //Short-term Investments
                // END CURRENT ASSETS

                // FIXED (LONG-TERM) ASSETS
                $long_term_investments = 0;
                $mbs_lti = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Long-Term Investments')->first();
                if (is_null($mbs_lti)) {
                    $mbs_lti = new BasicBalanceSheet();
                    $mbs_lti->shop_id = $shop->id;
                    $mbs_lti->date = $end;
                    $mbs_lti->item_desc = 'Long-Term Investments';
                    $mbs_lti->item_category = 'FIXED (LONG TERM) ASSETS';
                    $mbs_lti->amount = $long_term_investments;
                    $mbs_lti->save();
                }

                $property_plant_equipments = 0;
                $mbs_ppe = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Property, Plant, and Equipment')->first();
                if (is_null($mbs_ppe)) {
                    $mbs_ppe = new BasicBalanceSheet();
                    $mbs_ppe->shop_id = $shop->id;
                    $mbs_ppe->date = $end;
                    $mbs_ppe->item_desc = 'Property, Plant, and Equipment';
                    $mbs_ppe->item_category = 'FIXED (LONG TERM) ASSETS';
                    $mbs_ppe->amount = $property_plant_equipments;
                    $mbs_ppe->save();
                }
                
                $less_accoumulated_depreciation = 0;
                $mbs_lad = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', '(Less Accumulated Depreciation)')->first();
                if (is_null($mbs_lad)) {
                    $mbs_lad = new BasicBalanceSheet();
                    $mbs_lad->shop_id = $shop->id;
                    $mbs_lad->date = $end;
                    $mbs_lad->item_desc = '(Less Accumulated Depreciation)';
                    $mbs_lad->item_category = 'FIXED (LONG TERM) ASSETS';
                    $mbs_lad->amount = $less_accoumulated_depreciation;
                    $mbs_lad->save();
                }

                $intangible_assets = 0;
                $mbs_ia = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Intangible Assets')->first();
                if (is_null($mbs_ia)) {
                    $mbs_ia = new BasicBalanceSheet();
                    $mbs_ia->shop_id = $shop->id;
                    $mbs_ia->date = $end;
                    $mbs_ia->item_desc = 'Intangible Assets';
                    $mbs_ia->item_category = 'FIXED (LONG TERM) ASSETS';
                    $mbs_ia->amount = $intangible_assets;
                    $mbs_ia->save();
                }
                // END FIXED (LONG-TERM) ASSETS

                // OTHER ASSETS
                $deferred_income_tax = 0;
                $mbs_ia = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Deferred Income Tax')->where('item_category', 'OTHER ASSETS')->first();
                if (is_null($mbs_ia)) {
                    $mbs_ia = new BasicBalanceSheet();
                    $mbs_ia->shop_id = $shop->id;
                    $mbs_ia->date = $end;
                    $mbs_ia->item_desc = 'Deferred Income Tax';
                    $mbs_ia->item_category = 'OTHER ASSETS';
                    $mbs_ia->amount = $deferred_income_tax;
                    $mbs_ia->save();
                }
                $other_assets = 0;
                $mbs_other_assets = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Other')->where('item_category', 'OTHER ASSETS')->first();
                if (is_null($mbs_other_assets)) {
                    $mbs_other_assets = new BasicBalanceSheet();
                    $mbs_other_assets->shop_id = $shop->id;
                    $mbs_other_assets->date = $end;
                    $mbs_other_assets->item_desc = 'Other';
                    $mbs_other_assets->item_category = 'OTHER ASSETS';
                    $mbs_other_assets->amount = $other_assets;
                    $mbs_other_assets->save();
                }

                // END OTHER ASSETS

                // END OF ALL ASSETS

                // LIABILITIES & OWNER'S EQUITY
                // CURRENT LIABILITIES
                $account_payable = 0;
                $total_sup_ob = 0;
                $total_sup_invoices = 0;
                $supplier_payable = 0;
                $cust_creditor = 0;
                $other_credits = 0;

                $suppliers = $shop->suppliers()->get();
                foreach ($suppliers as $key => $supplier) {
                    $supobtrans = SupplierTransaction::where('supplier_id', $supplier->id)->where('invoice_no', 'OB')->where('shop_id', $shop->id)->first();
                    $supopening_balance = 0;
                    if (!is_null($supobtrans)) {
                        $supopening_balance = $supobtrans->amount-$supobtrans->ob_paid;
                    }

                    $totalpurchases = Purchase::where('shop_id', $shop->id)->where('is_deleted', false)->where('supplier_id', $supplier->id)->get([
                        DB::raw('SUM(total_amount) as total_amount'),
                        DB::raw('SUM(amount_paid) as amount_paid')
                    ]);
                    $new_sup_invoices = 0;
                    foreach ($totalpurchases as $key => $value) {
                        $new_sup_invoices += $value->total_amount-$value->amount_paid;
                    }

                    $supplier_payable += ($supopening_balance+$new_sup_invoices);
                }
                $account_payable = $supplier_payable;
                $mbs_acc_payable = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Accounts Payable')->first();
                if (is_null($mbs_acc_payable)) {
                    $mbs_acc_payable = new BasicBalanceSheet();
                    $mbs_acc_payable->shop_id = $shop->id;
                    $mbs_acc_payable->date = $end;
                    $mbs_acc_payable->item_desc = 'Accounts Payable';
                    $mbs_acc_payable->item_category = 'CURRENT LIABILITIES';
                    $mbs_acc_payable->amount = $account_payable;
                    $mbs_acc_payable->save();
                }
                
                $short_term_loans = 0;
                $mbs_st_loan = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Short-Term Loans')->first();
                if (is_null($mbs_st_loan)) {
                    $mbs_st_loan = new BasicBalanceSheet();
                    $mbs_st_loan->shop_id = $shop->id;
                    $mbs_st_loan->date = $end;
                    $mbs_st_loan->item_desc = 'Short-Term Loans';
                    $mbs_st_loan->item_category = 'CURRENT LIABILITIES';
                    $mbs_st_loan->amount = $short_term_loans;
                    $mbs_st_loan->save();
                }
                
                $income_tax_payble = 0;
                $mbs_itax_payable = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Income Taxes Payable')->first();
                if (is_null($mbs_itax_payable)) {
                    $mbs_itax_payable = new BasicBalanceSheet();
                    $mbs_itax_payable->shop_id = $shop->id;
                    $mbs_itax_payable->date = $end;
                    $mbs_itax_payable->item_desc = 'Income Taxes Payable';
                    $mbs_itax_payable->item_category = 'CURRENT LIABILITIES';
                    $mbs_itax_payable->amount = $income_tax_payble;
                    $mbs_itax_payable->save();
                }

                $accrued_salaries_and_wages = 0;
                $mbs_asw = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Accrued Salaries and Wages')->first();
                if (is_null($mbs_asw)) {
                    $mbs_asw = new BasicBalanceSheet();
                    $mbs_asw->shop_id = $shop->id;
                    $mbs_asw->date = $end;
                    $mbs_asw->item_desc = 'Accrued Salaries and Wages';
                    $mbs_asw->item_category = 'CURRENT LIABILITIES';
                    $mbs_asw->amount = $accrued_salaries_and_wages;
                    $mbs_asw->save();
                }
                
                $unearned_revenue = 0;
                $mbs_uer = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Unearned Revenue')->first();
                if (is_null($mbs_uer)) {
                    $mbs_uer = new BasicBalanceSheet();
                    $mbs_uer->shop_id = $shop->id;
                    $mbs_uer->date = $end;
                    $mbs_uer->item_desc = 'Unearned Revenue';
                    $mbs_uer->item_category = 'CURRENT LIABILITIES';
                    $mbs_uer->amount = $unearned_revenue;
                    $mbs_uer->save();
                }
                
                $current_portion_of_long_term_debt = 0;
                $mbs_cp_ltd = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Current Portion of Long-Term Debt')->first();
                if (is_null($mbs_cp_ltd)) {
                    $mbs_cp_ltd = new BasicBalanceSheet();
                    $mbs_cp_ltd->shop_id = $shop->id;
                    $mbs_cp_ltd->date = $end;
                    $mbs_cp_ltd->item_desc = 'Current Portion of Long-Term Debt';
                    $mbs_cp_ltd->item_category = 'CURRENT LIABILITIES';
                    $mbs_cp_ltd->amount = $current_portion_of_long_term_debt;
                    $mbs_cp_ltd->save();
                }
                // END CURRENT LIABILITIES

                // LONG TERM LIABILITIES
                $long_term_debt = 0;
                $mbs_lt_debt = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Long-Term Debt')->first();
                if (is_null($mbs_lt_debt)) {
                    $mbs_lt_debt = new BasicBalanceSheet();
                    $mbs_lt_debt->shop_id = $shop->id;
                    $mbs_lt_debt->date = $end;
                    $mbs_lt_debt->item_desc = 'Long-Term Debt';
                    $mbs_lt_debt->item_category = 'LONG TERM LIABILITIES';
                    $mbs_lt_debt->amount = $long_term_debt;
                    $mbs_lt_debt->save();
                }

                $liability_deferred_income_tax = 0;
                $mbs_ldi_tax = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Deferred Income Tax')->where('item_category', 'LONG TERM LIABILITIES')->first();
                if (is_null($mbs_ldi_tax)) {
                    $mbs_ldi_tax = new BasicBalanceSheet();
                    $mbs_ldi_tax->shop_id = $shop->id;
                    $mbs_ldi_tax->date = $end;
                    $mbs_ldi_tax->item_desc = 'Deferred Income Tax';
                    $mbs_ldi_tax->item_category = 'LONG TERM LIABILITIES';
                    $mbs_ldi_tax->amount = $liability_deferred_income_tax;
                    $mbs_ldi_tax->save();
                }
                
                $other_liabilities = 0;
                $mbs_other_liabilities = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Other')->where('item_category', 'LONG TERM LIABILITIES')->first();
                if (is_null($mbs_other_liabilities)) {
                    $mbs_other_liabilities = new BasicBalanceSheet();
                    $mbs_other_liabilities->shop_id = $shop->id;
                    $mbs_other_liabilities->date = $end;
                    $mbs_other_liabilities->item_desc = 'Other';
                    $mbs_other_liabilities->item_category = 'LONG TERM LIABILITIES';
                    $mbs_other_liabilities->amount = $other_liabilities;
                    $mbs_other_liabilities->save();
                }
                
                // END LONG TERM LIABILITIES

                // OWNER'S EQUITY
                $owners_investment = CashIn::where('shop_id', $shop->id)->where('in_date', '<=', $bs_date)->where('category', 'Investing Activities')->sum('amount');
                $mbs_owner_inv = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', "Owner's Investment")->first();
                if (is_null($mbs_owner_inv)) {
                    $mbs_owner_inv = new BasicBalanceSheet();
                    $mbs_owner_inv->shop_id = $shop->id;
                    $mbs_owner_inv->date = $end;
                    $mbs_owner_inv->item_desc = "Owner's Investment";
                    $mbs_owner_inv->item_category = "OWNER'S EQUITY";
                    $mbs_owner_inv->amount = $owners_investment;
                    $mbs_owner_inv->save();
                }
                
                $retained_earnings = 0;
                $mbs_retained = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', "Retained Earnings")->first();
                if (is_null($mbs_retained)) {
                    $mbs_retained = new BasicBalanceSheet();
                    $mbs_retained->shop_id = $shop->id;
                    $mbs_retained->date = $end;
                    $mbs_retained->item_desc = "Retained Earnings";
                    $mbs_retained->item_category = "OWNER'S EQUITY";
                    $mbs_retained->amount = $retained_earnings;
                    $mbs_retained->save();
                }
                
                $other_equity = 0;
                $mbs_other_equity = BasicBalanceSheet::where('shop_id', $shop->id)->where('date', $bs_date)->where('item_desc', 'Other')->where('item_category', "OWNER'S EQUITY")->first();
                if (is_null($mbs_other_equity)) {
                    $mbs_other_equity = new BasicBalanceSheet();
                    $mbs_other_equity->shop_id = $shop->id;
                    $mbs_other_equity->date = $end;
                    $mbs_other_equity->item_desc = 'Other';
                    $mbs_other_equity->item_category = "OWNER'S EQUITY";
                    $mbs_other_equity->amount = $other_equity;
                    $mbs_other_equity->save();
                }
                // END OWNER'S EQUITY
            }else{
                Log::info('Balance Sheet already created');
            }
        }
    }
}
