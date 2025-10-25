<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use DB;
use Carbon\Carbon;
use App\Models\Company;
use App\Models\Shop;
use App\Models\User;
use App\Models\ShopCurrency;
use App\Models\Category;
use App\Models\AnSale;
use App\Models\SaleReturn;
use App\Models\TransferOrderItem;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\CashIn;
use App\Models\CashOut;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\AccountTransaction;
use App\Models\OCAmount;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\SupplierTransaction;
use App\Models\ExpensePayment;
use App\Models\BusinessValue;
use App\Models\SalePayment;
use App\Models\PurchasePayment;
use App\Models\RmPurchasePayment;
use App\Models\PmPurchasePayment;
use App\Models\PlcPayment;
use App\Models\MohCostPayment;
use App\Models\BankDetail;
use App\Models\accountstatement;
use App\Models\Account;
use App\Models\PettyCash;

class FinancialReportsController extends Controller
{
    public function BusinessValue(Request $request)
    {
        $page = 'Reports';
        $title = 'Business value Reports';
        $title_sw = 'Ripoti ya Thamani ya Biashara';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
    
        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        $balances =  $this->accountsBalance($shop);

        $total_balance = $balances['cashBal']+$balances['mobiBal']+$balances['bankBal'];
        // Business value
        // Assets
        $cash_in_hand = $total_balance;
        $account_receivable = 0;
        $inventory = 0;
        $total_ob = 0;
        $total_invoices = 0;
        $supp_debtor = 0;
        $other_loan = 0;

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

        $stocks = Stock::where('stocks.shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->join('products', 'products.id', '=', 'stocks.product_id')->join('product_shop', 'product_id', '=', 'products.id')->select('name', 'basic_uom', 'quantity_in', 'quantity_out', 'stocks.unit_cost', 'retail_price', 'wholesale_price')->get();
        foreach ($stocks as $key => $stock) {
            $inventory += ($stock->quantity_in-$stock->quantity_out)*$stock->unit_cost;
        }

        $supptrans = SupplierTransaction::where('shop_id', $shop->id)->where('is_deleted', false)->groupBy('supplier_id')->get([
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

        $unpaidexp = 0;
        $pexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('status', 'Pending')->get();

        foreach ($pexpenses as $key => $expense) {
            $unpaidexp += ($expense->amount-$expense->amount_paid);
        }

        $custtrans = CustomerTransaction::where('shop_id', $shop->id)->where('is_deleted', false)->groupBy('customer_id')->get([
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
        // return $owners_equity;

        $discounts_made = AnSale::where('shop_id', $shop->id)->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->sum('sale_discount');
        $paid_expenses = ExpensePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->sum('amount');

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.financial.business-value',  compact('page', 'title', 'title_sw', 'is_post_query', 'shop', 'settings', 'crtime', 'reporttime','cash_in_hand', 'inventory', 'account_receivable', 'supp_debtor', 'other_loan', 'total_assets', 'supplier_payable', 'cust_creditor', 'unpaidexp', 'other_credits', 'account_payable', 'paid_expenses', 'discounts_made', 'start_date', 'end_date','duration', 'duration_sw' ));
    }

    public function CashFlowStatement(Request $request)
    {
        $page = 'Reports';
        $title = 'Cash Flow Statement';
        $title_sw = 'Ripoti ya Mzunguuko wa Cashi';
        $company = Company::find(Session::get('company_id'));
        $shops = Shop::where('company_id', $company->id)->get();

        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        $endingdate = Carbon::parse($start)->subDays(1);

        $shop = Shop::find(Session::get('shop_id'));
        $cash_balance = 0;
        $tcashins = CashIn::where('shop_id', $shop->id)->where('in_date' , '<', $start)->sum('amount');
        $tpayments = SalePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');

        $tcashouts = CashOut::where('shop_id', $shop->id)->where('out_date', '<', $start)->sum('amount');
        $texpense = ExpensePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');

        $tppayments = PurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');

        $trm_payments = RmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');
        $tpm_payments = PmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');
        $tdlc_payments = PlcPayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');
        $tmoh_cost_payments = MohCostPayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('is_deleted', false)->sum('amount');
        $cash_balance = ($tcashins+$tpayments)-($tcashouts+$texpense+$tppayments+$trm_payments+$trm_payments+$tdlc_payments+$tmoh_cost_payments);


        $invoice_payments = SalePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('is_deleted', false)->sum('amount');
        $other_payments = 0;

        $purchase_payments = PurchasePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('is_deleted', false)->sum('amount');

        $rm_purchase_payments = RmPurchasePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('is_deleted', false)->sum('amount');
        $pm_purchase_payments = PmPurchasePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('is_deleted', false)->sum('amount');
        $dlc_payments = PlcPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('is_deleted', false)->sum('amount');
        $moh_cost_payments = MohCostPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('is_deleted', false)->sum('amount');

        $expcategories = ExpensePayment::where('expense_payments.shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('expense_payments.is_deleted', false)->join('expenses', 'expenses.id', '=', 'expense_payments.expense_id')->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->groupBy('name')->get([
            \DB::raw('name as category'),
            \DB::raw('SUM(expense_payments.amount) as amount')
        ]);

        $income_tax = 0;

        // return $expcategories;
        $inv_cashins = CashIn::where('shop_id', $shop->id)->where('category', 'Investing Activities')->whereBetween('in_date', [$start, $end])->get();
        $fin_cashins = CashIn::where('shop_id', $shop->id)->where('category', 'Financing Activities')->whereBetween('in_date', [$start, $end])->get();

        $inv_cashouts = CashOut::where('shop_id', $shop->id)->where('category', 'Investing Activities')->whereBetween('out_date', [$start, $end])->get();
        $fin_cashouts = CashOut::where('shop_id', $shop->id)->where('category', 'Financing Activities')->whereBetween('out_date', [$start, $end])->get();;

        //Cash flow Stment starts here
        $salescashins = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('sale_payments.pay_date', [$start, $end])->where('pay_mode', 'Cash')->sum('amount');

        $salesbankins = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('sale_payments.pay_date', [$start, $end])->where('pay_mode', 'Bank')->sum('amount');
      
        $salesmobiins = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('sale_payments.pay_date', [$start, $end])->where('pay_mode', 'Mobile Money')->sum('amount');

        $cashin = CashIn::where('cash_ins.shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->join('accounts', 'accounts.id','=', 'cash_ins.account_id')->where('type', 'Cash')->sum('amount');

        $bankin = CashIn::where('cash_ins.shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->join('accounts', 'accounts.id','=', 'cash_ins.account_id')->where('type', 'Bank')->sum('amount');

        $mobiin = CashIn::where('cash_ins.shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->join('accounts', 'accounts.id','=', 'cash_ins.account_id')->where('type', 'Mobile Money')->sum('amount');

        $cashins = array(
            ['pay_mode' => 'Cash', 'amount' => ($salescashins+$cashin)],
            ['pay_mode' => 'Bank', 'amount' => ($salesbankins+$bankin)],
            ['pay_mode' => 'Mobile Money', 'amount' => ($salesmobiins+$mobiin)]
        );

        //Cashouts 
        $cashout = CashOut::where('cash_outs.shop_id', $shop->id)->whereBetween('out_date', [$start, $end])->join('accounts', 'accounts.id', '=', 'cash_outs.account_id')->where('type', 'Cash')->sum('amount');

        $bankout = CashOut::where('cash_outs.shop_id', $shop->id)->whereBetween('out_date', [$start, $end])->join('accounts', 'accounts.id', '=', 'cash_outs.account_id')->where('type', 'Bank')->sum('amount');

        $mobiout = CashOut::where('cash_outs.shop_id', $shop->id)->whereBetween('out_date', [$start, $end])->join('accounts', 'accounts.id', '=', 'cash_outs.account_id')->where('type', 'Mobile Money')->sum('amount');

        $cashppay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->whereBetween('purchase_payments.pay_date', [$start, $end])->where('pay_mode', 'Cash')->sum('amount');

        $bankppay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->whereBetween('purchase_payments.pay_date', [$start, $end])->where('pay_mode', 'Bank')->sum('amount');

        $mobippay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->whereBetween('purchase_payments.pay_date', [$start, $end])->where('pay_mode', 'Mobile Money')->sum('amount');

        $cashexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->whereBetween('expense_payments.pay_date', [$start, $end])->where('expense_payments.pay_mode', 'Cash')->sum('expense_payments.amount');

        $bankexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->whereBetween('expense_payments.pay_date', [$start, $end])->where('expense_payments.pay_mode', 'Bank')->sum('expense_payments.amount');

        $mobiexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->whereBetween('expense_payments.pay_date', [$start, $end])->where('expense_payments.pay_mode', 'Mobile Money')->sum('expense_payments.amount');

        $cashouts = array(
            ['pay_mode' => 'Cash', 'amount' => ($cashout+$cashppay+$cashexp)],
            ['pay_mode' => 'Bank', 'amount' => ($bankout+$bankppay+$bankexp)],
            ['pay_mode' => 'Mobile Money', 'amount' => ($mobiout+$mobippay+$mobiexp)]
        );
      
        $sales_cashin = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('sale_payments.pay_date', [$start, $end])->sum('amount');
        $other_cashin = CashIn::where('shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->sum('amount');
      
        $total_cashin = $sales_cashin+$other_cashin;

        $tcashout = (($cashout+$cashppay+$cashexp)+($mobiout+$mobippay+$mobiexp)+($bankout+$bankppay+$bankexp));

        $total_cashout = $tcashout;

        $balances =  $this->accountsBalance($shop);

        $total_balance = $balances['cashBal']+$balances['mobiBal']+$balances['bankBal'];

        $cashin_outs = array(
            ['pay_mode' => 'Cash', 'amount'=> $balances['cashBal']],
            ['pay_mode' => 'Mobile Money', 'amount' => $balances['mobiBal']],
            ['pay_mode' => 'Bank', 'amount' => $balances['bankBal']]
        );
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.financial.cash-flow-statement',  compact('page', 'title', 'title_sw', 'is_post_query', 'company', 'shops', 'shop', 'reporttime', 'start_date', 'end_date','duration', 'duration_sw', 'cashins','total_cashin','cashouts','total_cashout', 'cashin_outs','total_balance', 'endingdate', 'cash_balance', 'invoice_payments', 'other_payments', 'purchase_payments', 'rm_purchase_payments', 'pm_purchase_payments', 'dlc_payments', 'moh_cost_payments', 'expcategories', 'income_tax', 'inv_cashins', 'inv_cashouts', 'fin_cashins', 'fin_cashouts'));
    }

    // pay_mode Balances
    public function accountsBalance($shop)
    {
        
        // accounts balances;
        $cashAccin = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->where('pay_mode', 'Cash')->sum('amount');

        $mobileAccin = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->where('pay_mode', 'Mobile Money')->sum('amount');
      
        $bankAccin = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->where('pay_mode', 'Bank')->sum('amount');

        $totalcashin = CashIn::where('cash_ins.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_ins.account_id')->where('type', 'Cash')->sum('amount');

        $totalbankin = CashIn::where('cash_ins.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_ins.account_id')->where('type', 'Bank')->sum('amount');

        $totalmobiin = CashIn::where('cash_ins.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_ins.account_id')->where('type', 'Mobile Money')->sum('amount');

        $totalcashout = CashOut::where('cash_outs.shop_id', $shop->id)->join('accounts', 'accounts.id','=', 'cash_outs.account_id')->where('type', 'Cash')->sum('amount');

        $totalbankout = CashOut::where('cash_outs.shop_id', $shop->id)->join('accounts', 'accounts.id','=', 'cash_outs.account_id')->where('type', 'Bank')->sum('amount');

        $totalmobiout = CashOut::where('cash_outs.shop_id', $shop->id)->join('accounts', 'accounts.id','=', 'cash_outs.account_id')->where('type', 'Mobile Money')->sum('amount');

        $totalcashppay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_mode', 'Cash')->sum('amount');

        $totalbankppay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_mode', 'Bank')->sum('amount');

        $totalmobippay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_mode', 'Mobile Money')->sum('amount');

        $totalcashexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('expense_payments.pay_mode', 'Cash')->sum('expense_payments.amount');

        $totalbankexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('expense_payments.pay_mode', 'Bank')->sum('expense_payments.amount');

        $totalmobiexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('expense_payments.pay_mode', 'Mobile Money')->sum('expense_payments.amount');

        $cash_to_bank = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Cash')->where('to', 'Bank')->sum('amount');
        $cash_to_mobile = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Cash')->where('to', 'Mobile Money')->sum('amount');

        $mobile_to_bank = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Mobile Money')->where('to', 'Bank')->sum('amount');
        $mobile_to_cash = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Mobile Money')->where('to', 'Cash')->sum('amount');

        $bank_to_cash = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Bank')->where('to', 'Cash')->sum('amount');
        $bank_to_mobile = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Bank')->where('to', 'Mobile Money')->sum('amount');

        $plc_cash_pay = PlcPayment::where('shop_id', $shop->id)->where('pay_mode', 'Cash')->sum('amount');
        $plc_mob1_pay = PlcPayment::where('shop_id', $shop->id)->where('pay_mode', 'Mobile Money')->sum('amount');
        $plc_bank_pay = PlcPayment::where('shop_id', $shop->id)->where('pay_mode', 'Bank')->sum('amount');

        $moh_cash_pay = MohCostPayment::where('shop_id', $shop->id)->where('pay_mode', 'Cash')->sum('amount');
        $moh_mobi_pay = MohCostPayment::where('shop_id', $shop->id)->where('pay_mode', 'Mobile Money')->sum('amount');
        $moh_bank_pay = MohCostPayment::where('shop_id', $shop->id)->where('pay_mode', 'Bank')->sum('amount');

        $cashBal = ($totalcashin+$cashAccin+$bank_to_cash+$mobile_to_cash)- ($totalcashout+$totalcashppay+$totalcashexp+$cash_to_bank+$cash_to_mobile+$plc_cash_pay+$moh_cash_pay);

        $mobiBal = ($totalmobiin+$mobileAccin+$cash_to_mobile+$bank_to_mobile)-($totalmobiout+$totalmobippay+$totalmobiexp+$mobile_to_cash+$mobile_to_bank+$plc_mob1_pay+$moh_mobi_pay);
      
        $bankBal = ($totalbankin+$bankAccin+$cash_to_bank+$mobile_to_bank)-($totalbankout+$totalbankppay+$totalbankexp+$bank_to_cash+$bank_to_mobile+$plc_bank_pay+$moh_bank_pay);

        return array('cashBal' => $cashBal, 'mobiBal' => $mobiBal, 'bankBal' => $bankBal);
    }

    public function DailyCashFlowStatement(Request $request)
    { 
        $page = 'Reports';
        $title = 'Daily Cash Flow Statement';
        $title_sw = 'Ripoti ya Mzunguuko wa Cashi kilasiku';
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }
        
        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        $cashpay_bf = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->where('pay_date', '<', $start)->where('pay_mode', 'Cash')->sum('amount');

        $mobipay_bf = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->where('pay_date', '<', $start)->where('pay_mode', 'Mobile Money')->sum('amount');
      
        $bankpay_bf = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->where('pay_date', '<', $start)->where('pay_mode', 'Bank')->sum('amount');

        $totalcashin_bf = CashIn::where('cash_ins.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_ins.account_id')->where('in_date', '<', $start)->where('type', 'Cash')->sum('amount');

        $totalbankin_bf = CashIn::where('cash_ins.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_ins.account_id')->where('in_date', '<', $start)->where('type', 'Bank')->sum('amount');

        $totalmobiin_bf = CashIn::where('cash_ins.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_ins.account_id')->where('in_date', '<', $start)->where('type', 'Mobile Money')->sum('amount');

        $totalcashout_bf = CashOut::where('cash_outs.shop_id', $shop->id)->join('accounts', 'accounts.id','=', 'cash_outs.account_id')->where('out_date', '<', $start)->where('type', 'Cash')->sum('amount');

        $totalbankout_bf = CashOut::where('cash_outs.shop_id', $shop->id)->join('accounts', 'accounts.id','=', 'cash_outs.account_id')->where('out_date', '<', $start)->where('type', 'Bank')->sum('amount');

        $totalmobiout_bf = CashOut::where('cash_outs.shop_id', $shop->id)->join('accounts', 'accounts.id','=', 'cash_outs.account_id')->where('out_date', '<', $start)->where('type', 'Mobile Money')->sum('amount');

        $totalcashppay_bf = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_date', '<', $start)->where('pay_mode', 'Cash')->sum('amount');

        $totalbankppay_bf = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_date', '<', $start)->where('pay_mode', 'Bank')->sum('amount');

        $totalmobippay_bf = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_date', '<', $start)->where('pay_mode', 'Mobile Money')->sum('amount');

        $totalcashexp_bf = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('pay_date', '<', $start)->where('pay_mode', 'Cash')->sum('expense_payments.amount');
        $totalpettycashexp_bf = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('pay_date', '<', $start)->where('pay_mode', 'Petty Cash')->sum('expense_payments.amount');

        $totalbankexp_bf = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('pay_date', '<', $start)->where('pay_mode', 'Bank')->sum('expense_payments.amount');

        $totalmobiexp_bf = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('pay_date', '<', $start)->where('pay_mode', 'Mobile Money')->sum('expense_payments.amount');

        $cash_to_bank_bf = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Cash')->where('to', 'Bank')->where('date', '<', $start)->sum('amount');
        $cash_to_mobile_bf = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Cash')->where('to', 'Mobile Money')->where('date', '<', $start)->sum('amount');

        $mobile_to_bank_bf = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Mobile Money')->where('to', 'Bank')->where('date', '<', $start)->sum('amount');
        $mobile_to_cash_bf = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Mobile Money')->where('to', 'Cash')->where('date', '<', $start)->sum('amount');

        $bank_to_cash_bf = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Bank')->where('to', 'Cash')->where('date', '<', $start)->sum('amount');
        $bank_to_mobile_bf = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Bank')->where('to', 'Mobile Money')->where('date', '<', $start)->sum('amount');

        $rm_cash_pay_bf = RmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Cash')->sum('amount');
        $rm_mobi_pay_bf = RmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Mobile Money')->sum('amount');
        $rm_bank_pay_bf = RmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Bank')->sum('amount');

        $pm_cash_pay_bf = PmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Cash')->sum('amount');
        $pm_mobi_pay_bf = PmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Mobile Money')->sum('amount');
        $pm_bank_pay_bf = PmPurchasePayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Bank')->sum('amount');

        $plc_cash_pay_bf = PlcPayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Cash')->sum('amount');
        $plc_petty_cash_pay_bf = PlcPayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Petty Cash')->sum('amount');
        $plc_mob1_pay_bf = PlcPayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Mobile Money')->sum('amount');
        $plc_bank_pay_bf = PlcPayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Bank')->sum('amount');

        $moh_cash_pay_bf = MohCostPayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Cash')->sum('amount');
        $moh_petty_cash_pay_bf = MohCostPayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Petty Cash')->sum('amount');
        $moh_mobi_pay_bf = MohCostPayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Mobile Money')->sum('amount');
        $moh_bank_pay_bf = MohCostPayment::where('shop_id', $shop->id)->where('pay_date', '<', $start)->where('pay_mode', 'Bank')->sum('amount');

        //Petty cash
        // $pettycash_bf = PettyCash::where('shop_id', $shop->id)->where('request_date', '<', $start)->where('pay_mode', 'Cash')->where('status', 'Received')->sum('amount');
        $pettymobi_bf = PettyCash::where('shop_id', $shop->id)->where('request_date', '<', $start)->where('pay_mode', 'Mobile Money')->where('status', 'Received')->sum('amount');
        $pettybank_bf = PettyCash::where('shop_id', $shop->id)->where('request_date', '<', $start)->where('pay_mode', 'Bank')->where('status', 'Received')->sum('amount');

        $cashBal_bf = ($totalcashin_bf+$cashpay_bf+$bank_to_cash_bf+$mobile_to_cash_bf+$pettybank_bf+$pettymobi_bf)- ($totalcashout_bf+$totalcashppay_bf+$totalcashexp_bf+$cash_to_bank_bf+$cash_to_mobile_bf+$rm_cash_pay_bf+$pm_cash_pay_bf+$plc_cash_pay_bf+$moh_cash_pay_bf+$totalpettycashexp_bf+$plc_petty_cash_pay_bf+$moh_petty_cash_pay_bf);

        $mobiBal_bf = ($totalmobiin_bf+$mobipay_bf+$cash_to_mobile_bf+$bank_to_mobile_bf)-($totalmobiout_bf+$totalmobippay_bf+$totalmobiexp_bf+$mobile_to_cash_bf+$mobile_to_bank_bf+$rm_mobi_pay_bf+$pm_mobi_pay_bf+$plc_mob1_pay_bf+$moh_mobi_pay_bf+$pettymobi_bf);
      
        $bankBal_bf = ($totalbankin_bf+$bankpay_bf+$cash_to_bank_bf+$mobile_to_bank_bf)-($totalbankout_bf+$totalbankppay_bf+$totalbankexp_bf+$bank_to_cash_bf+$bank_to_mobile_bf+$rm_bank_pay_bf+$pm_bank_pay_bf+$plc_bank_pay_bf+$moh_bank_pay_bf+$pettybank_bf);


        $cash_pay = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start,$end])->where('pay_mode', 'Cash')->sum('amount');
        $mob_pay = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start,$end])->where('pay_mode', 'Mobile Money')->sum('amount'); 
        $bank_pay = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start,$end])->where('pay_mode', 'Bank')->sum('amount');

        $total_pay = $cash_pay+$mob_pay+$bank_pay;

        $cash_in = CashIn::where('cash_ins.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_ins.account_id')->where('type', 'Cash')->whereBetween('in_date', [$start, $end])->sum('amount');
        $mobi_in = CashIn::where('cash_ins.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_ins.account_id')->where('type', 'Mobile Money')->whereBetween('in_date', [$start, $end])->sum('amount');
        $bank_in = CashIn::where('cash_ins.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_ins.account_id')->where('type', 'Bank')->whereBetween('in_date', [$start, $end])->sum('amount');
        $total_cash_in = $cash_in+$mobi_in+$bank_in;
    
        $pettymobi = PettyCash::where('shop_id', $shop->id)->whereBetween('request_date', [$start, $end])->where('pay_mode', 'Mobile Money')->where('status', 'Received')->sum('amount');
        $pettybank = PettyCash::where('shop_id', $shop->id)->whereBetween('request_date', [$start, $end])->where('pay_mode', 'Bank')->where('status', 'Received')->sum('amount');
        $totalpetty = $pettymobi+$pettybank;

        $cash_out = CashOut::where('cash_outs.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_outs.account_id')->where('type', 'Cash')->whereBetween('out_date', [$start, $end])->sum('amount');
        $mobi_out = CashOut::where('cash_outs.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_outs.account_id')->where('type', 'Mobile Money')->whereBetween('out_date', [$start, $end])->sum('amount');
        $bank_out = CashOut::where('cash_outs.shop_id', $shop->id)->join('accounts', 'accounts.id', '=', 'cash_outs.account_id')->where('type', 'Bank')->whereBetween('out_date', [$start, $end])->sum('amount');
        $total_cash_out = $cash_out+$mobi_out+$bank_out;

        
        $cash_to_bank = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Cash')->where('to', 'Bank')->whereBetween('date', [$start, $end])->sum('amount');
        $cash_to_mobile = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Cash')->where('to', 'Mobile Money')->whereBetween('date', [$start, $end])->sum('amount');

        $mobile_to_bank = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Mobile Money')->where('to', 'Bank')->whereBetween('date', [$start, $end])->sum('amount');
        $mobile_to_cash = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Mobile Money')->where('to', 'Cash')->whereBetween('date', [$start, $end])->sum('amount');

        $bank_to_cash = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Bank')->where('to', 'Cash')->whereBetween('date', [$start, $end])->sum('amount');
        $bank_to_mobile = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Bank')->where('to', 'Mobile Money')->whereBetween('date', [$start, $end])->sum('amount');

        $cpaid_exp = ExpensePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Cash')->sum('amount');
        $pettycpaid_exp = ExpensePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Petty Cash')->sum('amount');
        $mpaid_exp = ExpensePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Mobile Money')->sum('amount');
        $bpaid_exp = ExpensePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Bank')->sum('amount');
        $paid_expenses = $cpaid_exp+$pettycpaid_exp+$mpaid_exp+$bpaid_exp;

        $cpur_pay = PurchasePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Cash')->sum('amount');
        $mpur_pay = PurchasePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Mobile Money')->sum('amount');
        $bpur_pay = PurchasePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Bank')->sum('amount');
        $purchase_payments = $cpur_pay+$mpur_pay+$bpur_pay;

        $rm_cash_pay = RmPurchasePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Cash')->sum('amount');
        $rm_mobi_pay = RmPurchasePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Mobile Money')->sum('amount');
        $rm_bank_pay = RmPurchasePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Bank')->sum('amount');
        $rm_purchase_payments = $rm_cash_pay+$rm_mobi_pay+$rm_bank_pay;

        $pm_cash_pay = PmPurchasePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Cash')->sum('amount');
        $pm_mobi_pay = PmPurchasePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Mobile Money')->sum('amount');
        $pm_bank_pay = PmPurchasePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Bank')->sum('amount');
        $pm_purchase_payments = $pm_cash_pay+$pm_mobi_pay+$pm_bank_pay;

        $plc_cash_pay = PlcPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Cash')->sum('amount');
        $plc_petty_cash_pay = PlcPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Petty Cash')->sum('amount');
        $plc_mobi_pay = PlcPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Mobile Money')->sum('amount');
        $plc_bank_pay = PlcPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Bank')->sum('amount');
        $plc_payments = $plc_cash_pay+$plc_petty_cash_pay+$plc_mobi_pay+$plc_bank_pay;

        $moh_cash_pay = MohCostPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Cash')->sum('amount');
        $moh_petty_cash_pay = MohCostPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Petty Cash')->sum('amount');
        $moh_mobi_pay = MohCostPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Mobile Money')->sum('amount');
        $moh_bank_pay = MohCostPayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start, $end])->where('pay_mode', 'Bank')->sum('amount');
        $moh_cost_payments = $moh_cash_pay+$moh_petty_cash_pay+$moh_mobi_pay+$moh_bank_pay;

        $dcf_cashBal = ($cashBal_bf+$cash_pay+$cash_in+$bank_to_cash+$mobile_to_cash)-($cash_to_bank+$cash_to_mobile+$cash_out+$cpaid_exp+$cpur_pay+$rm_cash_pay+$pm_cash_pay+$plc_cash_pay+$moh_cash_pay+$pettycpaid_exp+$plc_petty_cash_pay+$moh_petty_cash_pay);

        $dcf_mobiBal = ($mobiBal_bf+$mob_pay+$mobi_in+$cash_to_mobile+$bank_to_mobile)-($mobile_to_cash+$mobile_to_bank+$mpaid_exp+$mpur_pay+$rm_mobi_pay+$pm_mobi_pay+$plc_mobi_pay+$moh_mobi_pay+$mobi_out);

        $dcf_bankBal = ($bankBal_bf+$bank_pay+$bank_in+$cash_to_bank+$mobile_to_bank)-($bank_to_cash+$bank_to_mobile+$bpaid_exp+$bpur_pay+$rm_bank_pay+$pm_bank_pay+$plc_bank_pay+$moh_bank_pay+$bank_out);

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
       return view('reports.financial.daily-cash-flow-statement',  compact('page', 'title', 'title_sw', 'is_post_query', 'settings', 'company', 'shop', 'reporttime', 'start_date', 'end_date','duration', 'duration_sw', 'cashBal_bf', 'mobiBal_bf', 'bankBal_bf', 'cash_pay', 'mob_pay','bank_pay', 'total_pay', 'cash_in', 'mobi_in', 'bank_in', 'pettymobi', 'pettybank', 'totalpetty', 'cash_out', 'mobi_out', 'bank_out', 'paid_expenses', 'cpaid_exp', 'pettycpaid_exp', 'mpaid_exp', 'bpaid_exp', 'cpur_pay', 'mpur_pay', 'bpur_pay', 'purchase_payments', 'rm_cash_pay', 'rm_mobi_pay', 'rm_bank_pay', 'rm_purchase_payments', 'pm_cash_pay', 'pm_mobi_pay', 'pm_bank_pay', 'pm_purchase_payments', 'plc_cash_pay', 'plc_petty_cash_pay', 'plc_mobi_pay', 'plc_bank_pay', 'plc_payments', 'moh_cash_pay', 'moh_petty_cash_pay', 'moh_mobi_pay', 'moh_bank_pay', 'moh_cost_payments', 'dcf_cashBal', 'dcf_mobiBal', 'dcf_bankBal', 'cash_to_bank', 'cash_to_mobile', 'mobile_to_cash', 'mobile_to_bank', 'bank_to_cash', 'bank_to_mobile'));     
    }

    public function IncomeStatement(Request $request)
    {
        $page = 'Reports';
        $title = 'Income Statement';
        $title_sw = 'Ripoti ya Kipato';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        $total_sales = 0;
        $total_co_sales = 0;
        $total_expense = 0;
        $shared_expenses = 0;

        $categories = Category::where('shop_id', $shop->id)->get();
        $category = Category::find($request['category_id']);

        if ($shop->business_type_id == 1 || $shop->business_type_id == 2) {
            if (!is_null($category)) {
                $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->get([
                    \DB::raw('SUM(price) as price'),
                    \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
                    \DB::raw('SUM(total_discount) as discount'),
                    \DB::raw('SUM(buying_price) as buying_price'),
                    \DB::raw('SUM(an_sale_items.input_tax) as input_vat'),
                    \DB::raw('SUM(an_sale_items.tax_amount) as output_vat')
                ])->first();

                $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'sale_return_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->get([
                    \DB::raw('SUM(price) as return_price'),
                    \DB::raw('SUM(total_discount) as return_discount'),
                    \DB::raw('SUM(buying_price) as return_buying_price')
                ])->first();

                $transfers = TransferOrderItem::where('shop_id', $shop->id)->whereBetween('transfer_order_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'transfer_order_items.product_id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->get();

                $tqty = 0; $tscost = 0; $tdcost = 0; $total = 0;
                foreach ($transfers as $key => $transfer) {
                    $tqty += $transfer->quantity; 
                    $tscost += $transfer->source_unit_cost; 
                    $tdcost += $transfer->destin_unit_cost; 
                    $tprofit = 0; 
                    $total += (($transfer->destin_unit_cost-$transfer->source_unit_cost)*$transfer->quantity);
                }

                $total_sales = (($sales->price+$sales->tax_amount-$sales->discount)-($returns->return_price-$returns->return_discount))+$tdcost;
                $total_co_sales = ($sales->buying_price-$returns->return_buying_price)+$tscost;
            }else{
                $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
                    \DB::raw('SUM(price) as price'),
                    \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
                    \DB::raw('SUM(an_sale_items.total_discount) as discount'),
                    \DB::raw('SUM(buying_price) as buying_price'),
                    \DB::raw('SUM(an_sale_items.input_tax) as input_vat'),
                    \DB::raw('SUM(an_sale_items.tax_amount) as output_vat')
                ])->first();

                $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->get([
                    \DB::raw('SUM(price) as return_price'),
                    \DB::raw('SUM(total_discount) as return_discount'),
                    \DB::raw('SUM(buying_price) as return_buying_price')
                ])->first();
            
                $transfers = TransferOrderItem::where('shop_id', $shop->id)->whereBetween('transfer_order_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'transfer_order_items.product_id')->get();

                $tqty = 0; $tscost = 0; $tdcost = 0; $total = 0;
                foreach ($transfers as $key => $transfer) {
                    $tqty += $transfer->quantity; 
                    $tscost += $transfer->source_unit_cost; 
                    $tdcost += $transfer->destin_unit_cost; 
                    $tprofit = 0; 
                    $total += (($transfer->destin_unit_cost-$transfer->source_unit_cost)*$transfer->quantity);
                }

                $total_sales = (($sales->price+$sales->tax_amount-$sales->discount)-($returns->return_price-$returns->return_discount))+$tdcost;
                $total_co_sales = ($sales->buying_price-$returns->return_buying_price)+$tscost;
            }
        }elseif ($shop->business_type_id == 3) {
            $servsales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
                \DB::raw('SUM(total) as price'),
                \DB::raw('SUM(total_discount) as discount'),
                \DB::raw('SUM(service_sale_items.tax_amount) as tax_amount'),
                \DB::raw('DATE(an_sales.time_created) as date')
            ])->first();
          
            $total_sales = $servsales->price+$servsales->tax_amount-$servsales->discount;
        }else{
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
                \DB::raw('SUM(price) as price'),
                \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
                \DB::raw('SUM(total_discount) as discount'),
                \DB::raw('SUM(buying_price) as buying_price'),
                \DB::raw('SUM(an_sale_items.input_tax) as input_vat'),
                \DB::raw('SUM(an_sale_items.tax_amount) as output_vat')
            ])->first();

            $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->get([
                \DB::raw('SUM(price) as return_price'),
                \DB::raw('SUM(total_discount) as return_discount'),
                \DB::raw('SUM(buying_price) as return_buying_price')
            ])->first();

            $transfers = TransferOrderItem::where('shop_id', $shop->id)->whereBetween('transfer_order_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'transfer_order_items.product_id')->get();
            $tqty = 0; $tscost = 0; $tdcost = 0; $total = 0;
            foreach ($transfers as $key => $transfer) {
                $tqty += $transfer->quantity; 
                $tscost += $transfer->source_unit_cost; 
                $tdcost += $transfer->destin_unit_cost; 
                $tprofit = 0; 
                $total += (($transfer->destin_unit_cost-$transfer->source_unit_cost)*$transfer->quantity);
            }

            $servsales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
                \DB::raw('SUM(total) as price'),
                \DB::raw('SUM(total_discount) as discount'),
                \DB::raw('SUM(service_sale_items.tax_amount) as tax_amount'),
                \DB::raw('DATE(an_sales.time_created) as date')
            ])->first();


            $total_sales = (($sales->price+$sales->tax_amount-$sales->discount)-($returns->return_price-$returns->return_discount))+($servsales->price+$servsales->tax_amount-$servsales->discount)+$tdcost;
            $total_co_sales = ($sales->buying_price-$returns->return_buying_price)+$tscost;     
        }

        $categexpenses = Expense::where('expenses.shop_id', $shop->id)->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->whereBetween('time_created', [$start, $end])->groupBy('name')->get([
            \DB::raw('expense_categories.id as id'),
            \DB::raw('name'),
            \DB::raw('SUM(amount) as amount')
        ]);

        $catexpenses = array();
        foreach ($categexpenses as $key => $temp) {
            $catexp = [
                'id' => $temp->id,
                'name' => $temp->name,
                'amount' => $temp->amount
            ];
            $catexp['expenses'] = Expense::where('expenses.shop_id', $shop->id)->where('expense_category_id', $temp->id)->whereBetween('time_created', [$start, $end])->groupBy('expense_type')->get([
                \DB::raw('expense_type'),
                \DB::raw('SUM(amount) as amount')
            ])->toArray();

            array_push($catexpenses, array_merge($catexp, $catexp['expenses']));
        }

        $uncatexpenses = Expense::where('expenses.shop_id', $shop->id)->whereNull('expense_category_id')->whereBetween('time_created', [$start, $end])->groupBy('expense_type')->get([
            \DB::raw('expense_type'),
            \DB::raw('SUM(amount) as amount')
        ]);

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.financial.income-statement',  compact('page', 'title', 'title_sw', 'is_post_query', 'settings', 'shop', 'reporttime', 'start_date', 'end_date','duration', 'duration_sw', 'total_sales', 'total_co_sales', 'catexpenses', 'uncatexpenses'));
    }

    public function companyIncomeStmt(Request $request)
    {
        
    }

    public function MonthyClosingBusinessValue(Request $request)
    {
        $page = 'Reports';
        $title = 'Monthy Closing Business Value';
        $title_sw = 'Ripoti ya Thamani ya Biashara ya Kufunga Mwezi';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        $bvalues = BusinessValue::where('shop_id', $shop->id)->whereBetween('created_at', [$start,$end])->get();
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.financial.monthly-closing-business-value',  compact('page', 'title', 'title_sw', 'is_post_query', 'settings', 'shop', 'reporttime', 'start_date', 'end_date','duration', 'duration_sw', 'bvalues' ));
    }

    public function OpenClosingAmoutStatement(Request $request)
    {
        $page = 'Reports';
        $title = 'Open-Closing Amout Statement';
        $title_sw = 'Ripoti ya Kufunga na kufungua Biashara';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }
        
        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        //Copening and Closing Amount for Agents
        $ocamounts = [];
        if ($settings->is_agent) {
            $ocashs = OCAmount::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->where('record_type', 'Opening')->groupBy('date')->where('amount_type', 'cash')->get([
                \DB::raw('date'),
                \DB::raw('SUM(amount) as ot_cash')
            ])->toArray();

            $ofloats = OCAmount::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->where('record_type', 'Opening')->groupBy('date')->where('amount_type', 'float')->get([
              \DB::raw('date'),
              \DB::raw('SUM(amount) as ot_float')
            ])->toArray();


            $ccashs = OCAmount::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->where('record_type', 'Closing')->groupBy('date')->where('amount_type', 'cash')->get([
              \DB::raw('date'),
              \DB::raw('SUM(amount) as ct_cash')
            ])->toArray();

            $cfloats = OCAmount::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->where('record_type', 'Closing')->groupBy('date')->where('amount_type', 'float')->get([
              \DB::raw('date'),
              \DB::raw('SUM(amount) as ct_float')
            ])->toArray();

            $data = array_merge($ocashs, $ofloats, $ccashs, $cfloats);

            foreach ($data as $item)  {
                if (!isset($ocamounts[$item['date']])) {
                    $ocamounts[$item['date']] = [];
                }

                if (!array_key_exists('ot_cash', $ocamounts[$item['date']])) {
                    $ocamounts[$item['date']]['ot_cash'] = 0;
                }

                if (!array_key_exists('ot_float', $ocamounts[$item['date']])) {
                    $ocamounts[$item['date']]['ot_float'] = 0;
                }

                if (!array_key_exists('ct_cash', $ocamounts[$item['date']])) {
                    $ocamounts[$item['date']]['ct_cash'] = 0;
                }

                if (!array_key_exists('ct_float', $ocamounts[$item['date']])) {
                    $ocamounts[$item['date']]['ct_float'] = 0;
                }

                foreach ($item as $key => $value) {
                    // if ($key == 'date') continue;
                    $ocamounts[$item['date']][$key] = $value;
                }
            }
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.financial.open-closing-amount-statement',  compact('page', 'title', 'title_sw', 'is_post_query', 'settings', 'shop', 'reporttime', 'start_date', 'end_date','duration', 'duration_sw', 'ocamounts' ));
    }

    public function accountstatements(Request $request)
    {
        $page = 'Reports';
        $title = 'Account Statement';
        $title_sw = 'Taarifa ya Akaunti';
        $company = Company::find(Session::get('shop_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $user = Auth::user();
        $shops = $user->shops()->select('id', 'name')->get();
        $accounts = Account::where('shop_id', $shop->id)->get();
        $bdetails = BankDetail::where('shop_id', $shop->id)->get();
        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';
        $astmts = collect([]);
        $account_name = 'All accounts';
        $bal_before_start = 0;
        $account = null;
        if (!empty($request['account_id'])) {
            $account = Account::find($request['account_id']);
            $accno = '';
            if (!is_null($account->pay_mode_number)) {
                $accno = ' ('.$account->pay_mode_number.')';
            }
            $bankname = '';
            if (!is_null($account->bank_name) && $account->bank_name != '') {
                $bankname = '-'.$account->bank_name;
            }
            $account_name = $account->account_name.''.$accno.''.$bankname;
            
            $astmts = accountstatement::where('shop_id', $shop->id)->where('account_id', $account->id)->whereBetween('date', [$start, $end])->where('is_deleted', false)->orderBy('date', 'asc')->get();
        }else{
            $astmts = accountstatement::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->where('is_deleted', false)->orderBy('date', 'asc')->get();
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.financial.acc-stmts', compact('page', 'title', 'title_sw', 'is_post_query', 'settings', 'company', 'shop', 'shops', 'account', 'accounts', 'astmts', 'account_name', 'reporttime', 'start_date', 'end_date', 'duration', 'duration_sw', 'bal_before_start'));
    }


    public function expenses(Request $request)
    {
        $page = 'Reports';
        $title = 'Operating Expenses Reports';
        $title_sw = 'Ripoti ya Gharama za uendeshaji';
      
        $shop = Shop::find(Session::get('shop_id'));
        $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();

        $exptypes = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->groupBy('expense_type')->get();
        $user = Auth::user();
        $shops = $user->shops()->get();
        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';
        $expcategories = [];
        $qty_produced = 0;
        $texpenses = [];
        $expcat = null;
        $catexpenses = array();
        $uncatexpenses = [];
        $currstore = null;
        if ($is_post_query) {
            $currstore = Shop::find($request['store']);
        }else{
            $currstore = Shop::find(Session::get('shop_id'));
        }
        if (!is_null($currstore)) {
            $shopcategories = ExpenseCategory::where('shop_id', $currstore->id)->get();
            foreach ($shopcategories as $key => $catexp) {
                array_push($expcategories, ['id' => $catexp->id, 'name' => $catexp->name]);
            }
            
            if (!empty($request['expense_category_id'])) {
                $expcat = ExpenseCategory::find($request['expense_category_id']);

                $categexpenses = Expense::where('expenses.shop_id', $currstore->id)->where('expense_category_id', $request['expense_category_id'])->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->groupBy('name')->get([
                    \DB::raw('expense_categories.id as id'),
                    \DB::raw('name'),
                    \DB::raw('SUM(amount) as amount')
                ]);

                foreach ($categexpenses as $key => $temp) {
                    $catexp = [
                        'id' => $temp->id,
                        'name' => $temp->name,
                        'amount' => $temp->amount
                    ];
                    $catexp['expenses'] = Expense::where('shop_id', $currstore->id)->where('expense_category_id', $temp->id)->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->groupBy('expense_type')->get([
                        \DB::raw('expense_type'),
                        \DB::raw('SUM(amount) as amount')
                    ])->toArray();

                    array_push($catexpenses, array_merge($catexp, $catexp['expenses']));
                }

                $totalexpenses = Expense::where('shop_id', $currstore->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$start, $end])->where('expense_category_id', $request['expense_category_id'])->groupBy('expense_type')->get([
                    \DB::raw('expense_type as expense_type'),
                    \DB::raw('SUM(amount) as amount')
                ]);

                foreach ($totalexpenses as $key => $value) {
                    array_push($texpenses, ['expense_type' => $value->expense_type, 'amount' => $value->amount]);
                }
            }else{
                $categexpenses = Expense::where('expenses.shop_id', $currstore->id)->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->groupBy('name')->get([
                    \DB::raw('expense_categories.id as id'),
                    \DB::raw('name'),
                    \DB::raw('SUM(amount) as amount')
                ]);

                foreach ($categexpenses as $key => $temp) {
                    $catexp = [
                        'id' => $temp->id,
                        'name' => $temp->name,
                        'amount' => $temp->amount
                    ];
                    $catexp['expenses'] = Expense::where('shop_id', $currstore->id)->where('expense_category_id', $temp->id)->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->groupBy('expense_type')->get([
                        \DB::raw('expense_type'),
                        \DB::raw('SUM(amount) as amount')
                    ])->toArray();

                    array_push($catexpenses, array_merge($catexp, $catexp['expenses']));
                }

                $totalexpenses = Expense::where('shop_id', $currstore->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$start, $end])->groupBy('expense_type')->get([
                    \DB::raw('expense_type as expense_type'),
                    \DB::raw('SUM(amount) as amount')
                ]);

                foreach ($totalexpenses as $key => $value) {
                    array_push($texpenses, ['expense_type' => $value->expense_type, 'amount' => $value->amount]);
                }
            }

            $uncexpenses = Expense::where('shop_id', $currstore->id)->whereNull('expense_category_id')->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->groupBy('expense_type')->get([
                \DB::raw('expense_type'),
                \DB::raw('SUM(amount) as amount')
            ]);
            foreach ($uncexpenses as $key => $value) {
                array_push($uncatexpenses, ['expense_type' => $value->expense_type, 'amount' => $value->amount]);
            }
        }else{
            foreach ($shops as $key => $mshop) {
                $shopcategories = ExpenseCategory::where('shop_id', $mshop->id)->get();
                foreach ($shopcategories as $key => $catexp) {
                    array_push($expcategories, ['id' => $catexp->id, 'name' => $catexp->name]);
                }

                if (!empty($request['expense_category_id'])) {
                    $expcat = ExpenseCategory::find($request['expense_category_id']);

                    $categexpenses = Expense::where('expenses.shop_id', $mshop->id)->where('expense_category_id', $request['expense_category_id'])->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->groupBy('name')->get([
                        \DB::raw('expense_categories.id as id'),
                        \DB::raw('name'),
                        \DB::raw('SUM(amount) as amount')
                    ]);

                    foreach ($categexpenses as $key => $temp) {
                        $catexp = [
                            'id' => $temp->id,
                            'name' => $temp->name,
                            'amount' => $temp->amount
                        ];
                        $catexp['expenses'] = Expense::where('shop_id', $mshop->id)->where('expense_category_id', $temp->id)->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->groupBy('expense_type')->get([
                            \DB::raw('expense_type'),
                            \DB::raw('SUM(amount) as amount')
                        ])->toArray();

                        array_push($catexpenses, array_merge($catexp, $catexp['expenses']));
                    }

                    $totalexpenses = Expense::where('shop_id', $mshop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$start, $end])->where('expense_category_id', $request['expense_category_id'])->groupBy('expense_type')->get([
                        \DB::raw('expense_type as expense_type'),
                        \DB::raw('SUM(amount) as amount')
                    ]);

                    foreach ($totalexpenses as $key => $value) {
                        array_push($texpenses, ['expense_type' => $value->expense_type, 'amount' => $value->amount]);
                    }
                }else{
                    $categexpenses = Expense::where('expenses.shop_id', $mshop->id)->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->groupBy('name')->get([
                        \DB::raw('expense_categories.id as id'),
                        \DB::raw('name'),
                        \DB::raw('SUM(amount) as amount')
                    ]);

                    foreach ($categexpenses as $key => $temp) {
                        $catexp = [
                            'id' => $temp->id,
                            'name' => $temp->name,
                            'amount' => $temp->amount
                        ];
                        $catexp['expenses'] = Expense::where('shop_id', $mshop->id)->where('expense_category_id', $temp->id)->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->groupBy('expense_type')->get([
                            \DB::raw('expense_type'),
                            \DB::raw('SUM(amount) as amount')
                        ])->toArray();

                        array_push($catexpenses, array_merge($catexp, $catexp['expenses']));
                    }

                    $totalexpenses = Expense::where('shop_id', $mshop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$start, $end])->groupBy('expense_type')->get([
                        \DB::raw('expense_type as expense_type'),
                        \DB::raw('SUM(amount) as amount')
                    ]);

                    foreach ($totalexpenses as $key => $value) {
                        array_push($texpenses, ['expense_type' => $value->expense_type, 'amount' => $value->amount]);
                    }
                }

                $uncexpenses = Expense::where('shop_id', $mshop->id)->whereNull('expense_category_id')->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->groupBy('expense_type')->get([
                    \DB::raw('expense_type'),
                    \DB::raw('SUM(amount) as amount')
                ]);
                foreach ($uncexpenses as $key => $value) {
                    array_push($uncatexpenses, ['expense_type' => $value->expense_type, 'amount' => $value->amount]);
                }
            }
        }

        $settings = Setting::where('shop_id', $shop->id)->first();
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.expenses', compact('page', 'title', 'title_sw', 'shop', 'shops', 'currstore', 'settings', 'exptypes', 'catexpenses', 'uncatexpenses', 'expcategories', 'expcat', 'duration', 'duration_sw', 'reporttime', 'is_post_query', 'start_date', 'end_date', 'texpenses', 'defcurr', 'qty_produced'));
    }
}
