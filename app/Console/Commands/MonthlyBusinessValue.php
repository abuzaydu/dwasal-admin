<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\AnSale;
use App\Models\Expense;
use App\Models\ExpensePayment;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\SupplierTransaction;
use App\Models\Purchase;
use App\Models\BusinessValue;
use App\Models\CashIn;
use App\Models\CashOut;
use App\Models\AccountTransaction;
use \DB;

class MonthlyBusinessValue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monthly-business-value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save monthly Business values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $range = Carbon::now()->subDays(60);
        $shops = Shop::join('an_sales', 'an_sales.shop_id', '=', 'shops.id')->where('an_sales.created_at', '>=', $range)->select('shops.id as id', 'shops.name as business')->groupBy('id')->get();
        foreach ($shops as $key => $shop) {

            // Accounts balances;
            $cashAccin = AnSale::where('an_sales.shop_id', $shop->id)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('pay_mode', 'Cash')->sum('amount');

            $mobileAccin = AnSale::where('an_sales.shop_id', $shop->id)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('pay_mode', 'Mobile Money')->sum('amount');
      
            $bankAccin = AnSale::where('an_sales.shop_id', $shop->id)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('pay_mode', 'Bank')->sum('amount');

            $totalcashin = CashIn::where('shop_id', $shop->id)->where('account', 'Cash')->sum('amount');

            $totalbankin = CashIn::where('shop_id', $shop->id)->where('account', 'Bank')->sum('amount');

            $totalmobiin = CashIn::where('shop_id', $shop->id)->where('account', 'Mobile Money')->sum('amount');

            $totalcashout = CashOut::where('shop_id', $shop->id)->where('account', 'Cash')->sum('amount');

            $totalbankout = CashOut::where('shop_id', $shop->id)->where('account', 'Bank')->sum('amount');

            $totalmobiout = CashOut::where('shop_id', $shop->id)->where('account', 'Mobile Money')->sum('amount');

            $totalcashppay = Purchase::where('purchases.shop_id', $shop->id)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('account', 'Cash')->sum('amount');

            $totalbankppay = Purchase::where('purchases.shop_id', $shop->id)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('account', 'Bank')->sum('amount');

            $totalmobippay = Purchase::where('purchases.shop_id', $shop->id)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('account', 'Mobile Money')->sum('amount');

      
            $totalcashexp = Expense::where('expense_payments.shop_id', $shop->id)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('expense_payments.pay_mode', 'Cash')->sum('expense_payments.amount');

            $totalbankexp = Expense::where('expense_payments.shop_id', $shop->id)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('expense_payments.pay_mode', 'Bank')->sum('expense_payments.amount');

            $totalmobiexp = Expense::where('expense_payments.shop_id', $shop->id)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('expense_payments.pay_mode', 'Mobile Money')->sum('expense_payments.amount');

      
            $cash_to_bank = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Cash')->where('to', 'Bank')->sum('amount');
            $cash_to_mobile = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Cash')->where('to', 'Mobile Money')->sum('amount');

            $mobile_to_bank = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Mobile Money')->where('to', 'Bank')->sum('amount');
            $mobile_to_cash = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Mobile Money')->where('to', 'Cash')->sum('amount');

            $bank_to_cash = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Bank')->where('to', 'Cash')->sum('amount');
            $bank_to_mobile = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Bank')->where('to', 'Mobile Money')->sum('amount');
      

            $cashBal = ($totalcashin+$cashAccin+$bank_to_cash+$mobile_to_cash)- ($totalcashout+$totalcashppay+$totalcashexp+$cash_to_bank+$cash_to_mobile);

            $mobiBal = ($totalmobiin+$mobileAccin+$cash_to_mobile+$bank_to_mobile)-($totalmobiout+$totalmobippay+$totalmobiexp+$mobile_to_cash+$mobile_to_bank);
      
            $bankBal = ($totalbankin+$bankAccin+$cash_to_bank+$mobile_to_bank)-($totalbankout+$totalbankppay+$totalbankexp+$bank_to_cash+$bank_to_mobile);

            $total_balance = $cashBal+$mobiBal+$bankBal;
            // Business value
            // Assets
            $cash_in_hand = $total_balance;
            $account_receivable = 0;
            $inventory = 0;
            $total_ob = 0;
            $total_invoices = 0;
            $supp_debtor = 0;
            $other_loan = 0;
            
            $dur = Carbon::now()->subDays(30);
            $discounts_made = AnSale::where('shop_id', $shop->id)->where('time_created', '>=', $dur)->sum('sale_discount');

            $customers = Customer::where('shop_id', $shop->id)->get();
            foreach ($customers as $key => $customer) {
                $obtrans = CustomerTransaction::where('customer_id', $customer->id)->where('invoice_no', 'OB')->where('shop_id', $shop->id)->first();
                $opening_balance = 0;
                if (!is_null($obtrans)) {
                    $opening_balance = $obtrans->amount-$obtrans->ob_paid;
                }

                $totalsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->where('customer_id', $customer->id)->get([
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

            $stocks = Stock::where('stocks.shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->join('products', 'products.id', '=', 'stocks.product_id')->join('product_shop', 'product_id', '=', 'products.id')->select('name', 'basic_unit', 'quantity_in', 'quantity_out', 'stocks.unit_cost', 'retail_price', 'wholesale_price')->get();
            foreach ($stocks as $key => $stock) {
                $inventory += ($stock->quantity_in-$stock->quantity_out)*$stock->unit_cost;
            }
            
            $supptrans = SupplierTransaction::where('shop_id', $shop->id)->groupBy('supplier_id')->get([
                \DB::raw('supplier_id as supplier_id'),
                \DB::raw('SUM(amount) as amount'),
                \DB::raw('SUM(payment) as payment'),
                \DB::raw('SUM(adjustment) as adjustment')
            ]);

            foreach ($supptrans as $key => $trans) {
                $suppbal = $trans->amount-($trans->payment+$trans->adjustment);
                if ($suppbal < 0) {
                  $supp_debtor += -($suppbal);
                }
            }

            // Liabilities
            $account_payable = 0;

            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now();
            $suppliers = $shop->suppliers()->get();
            $total_sup_ob = 0;
            $total_sup_invoices = 0;
            $supplier_payable = 0;
            $cust_creditor = 0;
            $other_credits = 0;
            $paid_expenses = ExpensePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->sum('amount');
            foreach ($suppliers as $key => $supplier) {
                $supobtrans = SupplierTransaction::where('supplier_id', $supplier->id)->where('invoice_no', 'OB')->where('shop_id', $shop->id)->first();
                $supopening_balance = 0;
                if (!is_null($supobtrans)) {
                  $supopening_balance = $supobtrans->amount-$supobtrans->ob_paid;
                }

                $totalpurchases = Purchase::where('shop_id', $shop->id)->where('supplier_id', $supplier->id)->get([
                  DB::raw('SUM(total_amount) as total_amount'),
                  DB::raw('SUM(amount_paid) as amount_paid')
                ]);
                $new_sup_invoices = 0;
                foreach ($totalpurchases as $key => $value) {
                  
                  $new_sup_invoices += $value->total_amount-$value->amount_paid;
                }

                $supplier_payable += ($supopening_balance+$new_sup_invoices);
            }

            $unpaidexp = 0;
            $pexpenses = Expense::where('shop_id', $shop->id)->where('status', 'Pending')->get();

            foreach ($pexpenses as $key => $expense) {
                $unpaidexp += ($expense->cost_amount-$expense->amount_paid);
            }

            $custtrans = CustomerTransaction::where('shop_id', $shop->id)->groupBy('customer_id')->get([
                \DB::raw('customer_id as customer_id'),
                \DB::raw('SUM(amount) as amount'),
                \DB::raw('SUM(payment) as payment'),
                \DB::raw('SUM(adjustment) as adjustment')
            ]);
          
            foreach ($custtrans as $key => $trans) {
                $custbal = $trans->amount-($trans->payment+$trans->adjustment);
                if ($custbal < 0) {
                  $cust_creditor += -($custbal);
                }
            }

            $account_payable = $supplier_payable+$unpaidexp+$cust_creditor+$other_credits;


            $total_assets = $cash_in_hand+$account_receivable+$inventory+$supp_debtor+$other_loan;
            $owners_equity = $total_assets-$account_payable;
            
            //Save Businesss Value
            $bvalue = new BusinessValue();
            $bvalue->shop_id = $shop->id;
            $bvalue->date = Carbon::now();
            $bvalue->total_cash = $cash_in_hand;
            $bvalue->stock_value = $inventory;
            $bvalue->cust_debts = $account_receivable;
            $bvalue->supp_debts = $supp_debtor;
            $bvalue->other_debts = $other_loan;
            $bvalue->supp_credits = $supplier_payable;
            $bvalue->cust_credits = $cust_creditor;
            $bvalue->unpaid_expenses = $unpaidexp;
            $bvalue->other_credits = $other_credits;
            $bvalue->paid_expenses = $paid_expenses;
            $bvalue->discounts_made = $discounts_made;
            $bvalue->Save();
        }
    }
}
