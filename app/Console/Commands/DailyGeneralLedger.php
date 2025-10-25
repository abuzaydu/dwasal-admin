<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\TransactionAccount;
use App\Models\CustomerTransaction;
use App\Models\SupplierTransaction;
use App\Models\RmSupplierTransaction;
use App\Models\PmSupplierTransaction;
use App\Models\ProdLabourCost;
use App\Models\PlcPayment;
use App\Models\MohCost;
use App\Models\MohCostPayment;
use App\Models\Expense;
use App\Models\ExpensePayment;
use App\Models\CashIn;
use App\Models\CashOut;
use App\Models\GeneralLedger;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\SalePayment;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\RmPurchase;
use App\Models\RmPurchasePayment;
use App\Models\PmPurchase;
use App\Models\PmPurchasePayment;
use App\Models\PayrollDeduction;
use App\Models\PayrollDeductionPayment;

class DailyGeneralLedger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-general-ledger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $shops = Shop::select('id', 'company_id', 'name')->get();
        foreach ($shops as $key => $shop) {
            Log::info($shop->name);
            $custransactions = CustomerTransaction::where('customer_transactions.shop_id', $shop->id)->where('is_deleted', false)->where('is_added_to_ledger', false)->join('customers', 'customers.id', '=', 'customer_transactions.customer_id')->select('customer_transactions.id as id', 'an_sale_id', 'name', 'date', 'invoice_no', 'amount', 'receipt_no', 'payment', 'payment_mode', 'is_ob')->get();
            foreach ($custransactions as $key => $ctrans) {
                $acctrans = CustomerTransaction::find($ctrans->id);
                if (!is_null($ctrans->amount)) {
                    $sale = AnSale::find($ctrans->an_sale_id);
                    if (!is_null($sale)) {
                        Log::info('Sale Type : '.$sale->sale_type);
                        if ($sale->sale_type == 'cash') {
                            Log::info('Cash Sales to '.$ctrans->name.' : '.$ctrans->amount);
                            $rev_acc = TransactionAccount::where('account_number', 4000)->where('company_id', $shop->company_id)->first();
                            $rev_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $rev_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                            if (is_null($rev_ledger)) {   
                                $rev_ledger = new GeneralLedger();
                                $rev_ledger->shop_id = $shop->id;
                                $rev_ledger->transaction_account_id = $rev_acc->id;
                                $rev_ledger->customer_transaction_id = $ctrans->id;
                                $rev_ledger->date = $ctrans->date;
                                $rev_ledger->transaction_description = 'Sold Goods for cash';
                                $rev_ledger->credit_amount = $ctrans->amount;
                                $rev_ledger->reference = 'INV-'.$ctrans->invoice_no;
                                $rev_ledger->save();
                            }else{
                                $rev_ledger->credit_amount = $ctrans->amount;
                                $rev_ledger->save();
                            }

                            Log::info('Record Inventory and COGS');
                            $cogs_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('buying_price');
                            $cogs_acc = TransactionAccount::where('account_number', 5000)->where('company_id', $shop->company_id)->first();
                            $cogs_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cogs_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                            if (is_null($cogs_ledger)) {   
                                $cogs_ledger = new GeneralLedger();
                                $cogs_ledger->shop_id = $shop->id;
                                $cogs_ledger->transaction_account_id = $cogs_acc->id;
                                $cogs_ledger->customer_transaction_id = $ctrans->id;
                                $cogs_ledger->date = $ctrans->date;
                                $cogs_ledger->transaction_description = 'Sold Inventory for cash';
                                $cogs_ledger->debit_amount = $ctrans->amount;
                                $cogs_ledger->reference = 'INV-'.$ctrans->invoice_no;
                                $cogs_ledger->save();
                            }else{
                                $cogs_ledger->debit_amount = $ctrans->amount;
                                $cogs_ledger->save();
                            }

                            $inv_acc = TransactionAccount::where('account_number', 1020)->where('company_id', $shop->company_id)->first();
                            $inv_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $inv_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                            if (is_null($inv_ledger)) {   
                                $inv_ledger = new GeneralLedger();
                                $inv_ledger->shop_id = $shop->id;
                                $inv_ledger->transaction_account_id = $inv_acc->id;
                                $inv_ledger->customer_transaction_id = $ctrans->id;
                                $inv_ledger->date = $ctrans->date;
                                $inv_ledger->transaction_description = 'Sold Inventory for cash';
                                $inv_ledger->credit_amount = $ctrans->amount;
                                $inv_ledger->reference = 'INV-'.$ctrans->invoice_no;
                                $inv_ledger->save();
                            }else{
                                $inv_ledger->credit_amount = $ctrans->amount;
                                $inv_ledger->save();
                            }

                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }else{
                            Log::info('Credit Sales to '.$ctrans->name.' : '.$ctrans->amount);
                            $rev_acc = TransactionAccount::where('account_number', 4000)->where('company_id', $shop->company_id)->first();
                            $rev_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $rev_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                            if (is_null($rev_ledger)) {   
                                $rev_ledger = new GeneralLedger();
                                $rev_ledger->shop_id = $shop->id;
                                $rev_ledger->transaction_account_id = $rev_acc->id;
                                $rev_ledger->customer_transaction_id = $ctrans->id;
                                $rev_ledger->date = $ctrans->date;
                                $rev_ledger->transaction_description = 'Sold Goods/Services on credit';
                                $rev_ledger->credit_amount = $ctrans->amount;
                                $rev_ledger->reference = 'INV-'.$ctrans->invoice_no;
                                $rev_ledger->save();
                            }else{
                                $rev_ledger->credit_amount = $ctrans->amount;
                                $rev_ledger->save();
                            }

                            $ar_acc = TransactionAccount::where('account_number', 1010)->where('company_id', $shop->company_id)->first();
                            $ar_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ar_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                            if (is_null($ar_ledger)) {   
                                $ar_ledger = new GeneralLedger();
                                $ar_ledger->shop_id = $shop->id;
                                $ar_ledger->transaction_account_id = $ar_acc->id;
                                $ar_ledger->customer_transaction_id = $ctrans->id;
                                $ar_ledger->date = $ctrans->date;
                                $ar_ledger->transaction_description = 'Sold Goods/Services on credit';
                                $ar_ledger->debit_amount = $ctrans->amount;
                                $ar_ledger->reference = 'INV-'.$ctrans->invoice_no;
                                $ar_ledger->save();
                            }else{
                                $ar_ledger->debit_amount = $ctrans->amount;
                                $ar_ledger->save();
                            }

                            Log::info('Record Inventory and COGS');
                            $cogs_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('buying_price');
                            $cogs_acc = TransactionAccount::where('account_number', 5000)->where('company_id', $shop->company_id)->first();
                            $cogs_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cogs_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                            if (is_null($cogs_ledger)) {   
                                $cogs_ledger = new GeneralLedger();
                                $cogs_ledger->shop_id = $shop->id;
                                $cogs_ledger->transaction_account_id = $cogs_acc->id;
                                $cogs_ledger->customer_transaction_id = $ctrans->id;
                                $cogs_ledger->date = $ctrans->date;
                                $cogs_ledger->transaction_description = 'Sold Inventory for cash';
                                $cogs_ledger->debit_amount = $ctrans->amount;
                                $cogs_ledger->reference = 'INV-'.$ctrans->invoice_no;
                                $cogs_ledger->save();
                            }else{
                                $cogs_ledger->debit_amount = $ctrans->amount;
                                $cogs_ledger->save();
                            }

                            $inv_acc = TransactionAccount::where('account_number', 1020)->where('company_id', $shop->company_id)->first();
                            $inv_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $inv_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                            if (is_null($inv_ledger)) {   
                                $inv_ledger = new GeneralLedger();
                                $inv_ledger->shop_id = $shop->id;
                                $inv_ledger->transaction_account_id = $inv_acc->id;
                                $inv_ledger->customer_transaction_id = $ctrans->id;
                                $inv_ledger->date = $ctrans->date;
                                $inv_ledger->transaction_description = 'Sold Inventory for cash';
                                $inv_ledger->credit_amount = $ctrans->amount;
                                $inv_ledger->reference = 'INV-'.$ctrans->invoice_no;
                                $inv_ledger->save();
                            }else{
                                $inv_ledger->credit_amount = $ctrans->amount;
                                $inv_ledger->save();
                            }

                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }
                    }else{
                        $ar_acc = TransactionAccount::where('account_number', 1010)->where('company_id', $shop->company_id)->first();
                        $ar_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ar_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                        if (is_null($ar_ledger)) {   
                            $ar_ledger = new GeneralLedger();
                            $ar_ledger->shop_id = $shop->id;
                            $ar_ledger->transaction_account_id = $ar_acc->id;
                            $ar_ledger->customer_transaction_id = $ctrans->id;
                            $ar_ledger->date = $ctrans->date;
                            $ar_ledger->transaction_description = 'Opening Balance for '.$ctrans->name;
                            $ar_ledger->debit_amount = $ctrans->amount;
                            $ar_ledger->reference = 'OB';
                            $ar_ledger->save();
                        }else{
                            $ar_ledger->debit_amount = $ctrans->amount;
                            $ar_ledger->save();
                        }

                        $rev_acc = TransactionAccount::where('account_number', 4000)->where('company_id', $shop->company_id)->first();
                        $rev_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $rev_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                        if (is_null($rev_ledger)) {   
                            $rev_ledger = new GeneralLedger();
                            $rev_ledger->shop_id = $shop->id;
                            $rev_ledger->transaction_account_id = $rev_acc->id;
                            $rev_ledger->customer_transaction_id = $ctrans->id;
                            $rev_ledger->date = $ctrans->date;
                            $rev_ledger->transaction_description = 'Opening Balance for '.$ctrans->name;
                            $rev_ledger->credit_amount = $ctrans->amount;
                            $rev_ledger->reference = 'OB';
                            $rev_ledger->save();
                        }else{
                            $rev_ledger->credit_amount = $ctrans->amount;
                            $rev_ledger->save();
                        }

                        $acctrans->is_added_to_ledger = true;
                        $acctrans->save();
                    }
                }

                if (!is_null($ctrans->payment)) {
                    $spay = SalePayment::where('trans_id', $ctrans->id)->first();
                    $sale = AnSale::find($spay->an_sale_id);
                    if (!is_null($sale)) {
                        Log::info('Sale Type : '.$sale->sale_type);
                        if ($sale->sale_type == 'cash') {
                            Log::info('Payment from '.$ctrans->name.' : '.$ctrans->payment);
                            $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                            Log::info($cash_acc);
                            $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                            if (is_null($cash_ledger)) {   
                                $cash_ledger = new GeneralLedger();
                                $cash_ledger->shop_id = $shop->id;
                                $cash_ledger->transaction_account_id = $cash_acc->id;
                                $cash_ledger->customer_transaction_id = $ctrans->id;
                                $cash_ledger->date = $ctrans->date;
                                $cash_ledger->transaction_description = 'Received Payment for cash sales';
                                $cash_ledger->debit_amount = $ctrans->payment;
                                $cash_ledger->reference = 'REC-'.$ctrans->receipt_no;
                                $cash_ledger->save();
                            }else{
                                $cash_ledger->debit_amount = $ctrans->payment;
                                $cash_ledger->save();
                            }

                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }else{
                            $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                            $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                            if (is_null($cash_ledger)) {   
                                $cash_ledger = new GeneralLedger();
                                $cash_ledger->shop_id = $shop->id;
                                $cash_ledger->transaction_account_id = $cash_acc->id;
                                $cash_ledger->customer_transaction_id = $ctrans->id;
                                $cash_ledger->date = $ctrans->date;
                                $cash_ledger->transaction_description = 'Received payment from a customer ('.$ctrans->name.')';
                                $cash_ledger->debit_amount = $ctrans->payment;
                                $cash_ledger->reference = 'REC-'.$ctrans->receipt_no;
                                $cash_ledger->save();
                            }else{
                                $cash_ledger->debit_amount = $ctrans->payment;
                                $cash_ledger->save();
                            }

                            $ar_acc = TransactionAccount::where('account_number', 1010)->where('company_id', $shop->company_id)->first();
                            $ar_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ar_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                            if (is_null($ar_ledger)) {   
                                $ar_ledger = new GeneralLedger();
                                $ar_ledger->shop_id = $shop->id;
                                $ar_ledger->transaction_account_id = $ar_acc->id;
                                $ar_ledger->customer_transaction_id = $ctrans->id;
                                $ar_ledger->date = $ctrans->date;
                                $ar_ledger->transaction_description = 'Received payment from a customer ('.$ctrans->name.')';
                                $ar_ledger->credit_amount = $ctrans->payment;
                                $ar_ledger->reference = 'REC-'.$ctrans->receipt_no;
                                $ar_ledger->save();
                            }else{
                                $ar_ledger->credit_amount = $ctrans->payment;
                                $ar_ledger->save();
                            }

                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }
                    }else{
                        $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                        $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                        if (is_null($cash_ledger)) {   
                            $cash_ledger = new GeneralLedger();
                            $cash_ledger->shop_id = $shop->id;
                            $cash_ledger->transaction_account_id = $cash_acc->id;
                            $cash_ledger->customer_transaction_id = $ctrans->id;
                            $cash_ledger->date = $ctrans->date;
                            $cash_ledger->transaction_description = 'Received payment from a customer ('.$ctrans->name.')';
                            $cash_ledger->debit_amount = $ctrans->payment;
                            $cash_ledger->reference = 'REC-'.$ctrans->receipt_no;
                            $cash_ledger->save();
                        }else{
                            $cash_ledger->debit_amount = $ctrans->payment;
                            $cash_ledger->save();
                        }

                        $ar_acc = TransactionAccount::where('account_number', 1010)->where('company_id', $shop->company_id)->first();
                        $ar_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ar_acc->id)->where('customer_transaction_id', $ctrans->id)->first();
                        if (is_null($ar_ledger)) {   
                            $ar_ledger = new GeneralLedger();
                            $ar_ledger->shop_id = $shop->id;
                            $ar_ledger->transaction_account_id = $ar_acc->id;
                            $ar_ledger->customer_transaction_id = $ctrans->id;
                            $ar_ledger->date = $ctrans->date;
                            $ar_ledger->transaction_description = 'Received payment from a customer ('.$ctrans->name.')';
                            $ar_ledger->credit_amount = $ctrans->payment;
                            $ar_ledger->reference = 'REC-'.$ctrans->receipt_no;
                            $ar_ledger->save();
                        }else{
                            $ar_ledger->credit_amount = $ctrans->payment;
                            $ar_ledger->save();
                        }

                        $acctrans->is_added_to_ledger = true;
                        $acctrans->save();
                    }
                }
            }

            $suptransactions = SupplierTransaction::where('supplier_transactions.shop_id', $shop->id)->where('is_deleted', false)->where('is_added_to_ledger', false)->join('suppliers', 'suppliers.id', '=', 'supplier_transactions.supplier_id')->select('supplier_transactions.id as id', 'purchase_id', 'name', 'date', 'amount', 'pv_no', 'payment', 'payment_mode', 'is_ob')->get();
            foreach ($suptransactions as $key => $strans) {
                $acctrans = SupplierTransaction::find($strans->id);
                if (!is_null($strans->amount)) {
                    $purchase = Purchase::find($strans->purchase_id);
                    if (!is_null($purchase)) {
                        Log::info('Purchase Type : '.$purchase->purchase_type);
                        if ($purchase->purchase_type == 'cash') {
                            Log::info('Cash Purchase from '.$strans->name.' : '.$strans->amount);
                            $inv_acc = TransactionAccount::where('account_number', 1020)->where('company_id', $shop->company_id)->first();
                            $inv_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $inv_acc->id)->where('supplier_transaction_id', $strans->id)->first();
                            if (is_null($inv_ledger)) {   
                                $inv_ledger = new GeneralLedger();
                                $inv_ledger->shop_id = $shop->id;
                                $inv_ledger->transaction_account_id = $inv_acc->id;
                                $inv_ledger->supplier_transaction_id = $strans->id;
                                $inv_ledger->date = $strans->date;
                                $inv_ledger->transaction_description = 'Purchase Inventory for cash';
                                $inv_ledger->debit_amount = $strans->amount;
                                $inv_ledger->reference = 'GRN-'.$purchase->grn_no;
                                $inv_ledger->save();
                            }else{
                                $inv_ledger->debit_amount = $strans->amount;
                                $inv_ledger->save();
                            }

                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }else{
                            Log::info('Credit Purchase from '.$strans->name.' : '.$strans->amount);
                            
                            $inv_acc = TransactionAccount::where('account_number', 1020)->where('company_id', $shop->company_id)->first();
                            $inv_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $inv_acc->id)->where('supplier_transaction_id', $strans->id)->first();
                            if (is_null($inv_ledger)) {   
                                $inv_ledger = new GeneralLedger();
                                $inv_ledger->shop_id = $shop->id;
                                $inv_ledger->transaction_account_id = $inv_acc->id;
                                $inv_ledger->supplier_transaction_id = $strans->id;
                                $inv_ledger->date = $strans->date;
                                $inv_ledger->transaction_description = 'Purchase Inventory on credit';
                                $inv_ledger->debit_amount = $strans->amount;
                                $inv_ledger->reference = 'GRN-'.$purchase->grn_no;
                                $inv_ledger->save();
                            }else{
                                $inv_ledger->debit_amount = $strans->amount;
                                $inv_ledger->save();
                            }

                            $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                            $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ap_acc->id)->where('supplier_transaction_id', $strans->id)->first();
                            if (is_null($ap_ledger)) {   
                                $ap_ledger = new GeneralLedger();
                                $ap_ledger->shop_id = $shop->id;
                                $ap_ledger->transaction_account_id = $ap_acc->id;
                                $ap_ledger->supplier_transaction_id = $strans->id;
                                $ap_ledger->date = $strans->date;
                                $ap_ledger->transaction_description = 'Purchase of Inventory on credit';
                                $ap_ledger->credit_amount = $strans->amount;
                                $ap_ledger->reference = 'GRN-'.$purchase->grn_no;
                                $ap_ledger->save();
                            }else{
                                $ap_ledger->credit_amount = $strans->amount;
                                $ap_ledger->save();
                            }

                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }
                    }else{
                        $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                        $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ap_acc->id)->where('supplier_transaction_id', $strans->id)->first();
                        if (is_null($ap_ledger)) {   
                            $ap_ledger = new GeneralLedger();
                            $ap_ledger->shop_id = $shop->id;
                            $ap_ledger->transaction_account_id = $ap_acc->id;
                            $ap_ledger->supplier_transaction_id = $strans->id;
                            $ap_ledger->date = $strans->date;
                            $ap_ledger->transaction_description = 'Opening Balance for '.$strans->name;
                            $ap_ledger->credit_amount = $strans->amount;
                            $ap_ledger->reference = 'OB-'.$strans->name;
                            $ap_ledger->save();
                        }else{
                            $ap_ledger->credit_amount = $strans->amount;
                            $ap_ledger->save();
                        }

                        $acctrans->is_added_to_ledger = true;
                        $acctrans->save();
                    }
                }

                if (!is_null($strans->payment)) {
                    $ppay = PurchasePayment::where('trans_id', $strans->id)->first();
                    if (!is_null($ppay)) {
                        $purchase = Purchase::find($ppay->purchase_id);
                        if (!is_null($purchase)) {
                            Log::info('Purchase Type : '.$purchase->purchase_type);
                            if ($purchase->purchase_type == 'cash') {
                                Log::info('Payment to '.$strans->name.' : '.$strans->payment);
                                $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                                $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('supplier_transaction_id', $strans->id)->first();
                                if (is_null($cash_ledger)) {   
                                    $cash_ledger = new GeneralLedger();
                                    $cash_ledger->shop_id = $shop->id;
                                    $cash_ledger->transaction_account_id = $cash_acc->id;
                                    $cash_ledger->supplier_transaction_id = $strans->id;
                                    $cash_ledger->date = $strans->date;
                                    $cash_ledger->transaction_description = 'Payment for cash purchases';
                                    $cash_ledger->credit_amount = $strans->payment;
                                    $cash_ledger->reference = 'PV-'.$strans->pv_no;
                                    $cash_ledger->save();
                                }else{
                                    $cash_ledger->debit_amount = $strans->payment;
                                    $cash_ledger->save();
                                }

                                $acctrans->is_added_to_ledger = true;
                                $acctrans->save();
                            }else{
                                $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                                $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('supplier_transaction_id', $strans->id)->first();
                                if (is_null($cash_ledger)) {   
                                    $cash_ledger = new GeneralLedger();
                                    $cash_ledger->shop_id = $shop->id;
                                    $cash_ledger->transaction_account_id = $cash_acc->id;
                                    $cash_ledger->supplier_transaction_id = $strans->id;
                                    $cash_ledger->date = $strans->date;
                                    $cash_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                                    $cash_ledger->credit_amount = $strans->payment;
                                    $cash_ledger->reference = 'PV-'.$strans->pv_no;
                                    $cash_ledger->save();
                                }else{
                                    $cash_ledger->credit_amount = $strans->payment;
                                    $cash_ledger->save();
                                }

                                $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                                $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ar_acc->id)->where('supplier_transaction_id', $strans->id)->first();
                                if (is_null($ap_ledger)) {   
                                    $ap_ledger = new GeneralLedger();
                                    $ap_ledger->shop_id = $shop->id;
                                    $ap_ledger->transaction_account_id = $ap_acc->id;
                                    $ap_ledger->customer_transaction_id = $strans->id;
                                    $ap_ledger->date = $strans->date;
                                    $ap_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                                    $ap_ledger->credit_amount = $strans->payment;
                                    $ap_ledger->reference = 'PV-'.$strans->pv_no;
                                    $ap_ledger->save();
                                }else{
                                    $ap_ledger->credit_amount = $strans->payment;
                                    $ap_ledger->save();
                                }

                                $acctrans->is_added_to_ledger = true;
                                $acctrans->save();
                            }
                        }else{
                            $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                            $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('supplier_transaction_id', $strans->id)->first();
                            if (is_null($cash_ledger)) {   
                                $cash_ledger = new GeneralLedger();
                                $cash_ledger->shop_id = $shop->id;
                                $cash_ledger->transaction_account_id = $cash_acc->id;
                                $cash_ledger->supplier_transaction_id = $strans->id;
                                $cash_ledger->date = $strans->date;
                                $cash_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                                $cash_ledger->credit_amount = $strans->payment;
                                $cash_ledger->reference = 'PV-'.$strans->pv_no;
                                $cash_ledger->save();
                            }else{
                                $cash_ledger->credit_amount = $strans->payment;
                                $cash_ledger->save();
                            }

                            $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                            $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ap_acc->id)->where('supplier_transaction_id', $strans->id)->first();
                            if (is_null($ap_ledger)) {   
                                $ap_ledger = new GeneralLedger();
                                $ap_ledger->shop_id = $shop->id;
                                $ap_ledger->transaction_account_id = $ap_acc->id;
                                $ap_ledger->customer_transaction_id = $strans->id;
                                $ap_ledger->date = $strans->date;
                                $ap_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                                $ap_ledger->credit_amount = $strans->payment;
                                $ap_ledger->reference = 'PV-'.$strans->pv_no;
                                $ap_ledger->save();
                            }else{
                                $ap_ledger->credit_amount = $strans->payment;
                                $ap_ledger->save();
                            }

                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }
                    }else{
                        $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                        $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('supplier_transaction_id', $strans->id)->first();
                        if (is_null($cash_ledger)) {   
                            $cash_ledger = new GeneralLedger();
                            $cash_ledger->shop_id = $shop->id;
                            $cash_ledger->transaction_account_id = $cash_acc->id;
                            $cash_ledger->supplier_transaction_id = $strans->id;
                            $cash_ledger->date = $strans->date;
                            $cash_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                            $cash_ledger->credit_amount = $strans->payment;
                            $cash_ledger->reference = 'PV-'.$strans->pv_no;
                            $cash_ledger->save();
                        }else{
                            $cash_ledger->credit_amount = $strans->payment;
                            $cash_ledger->save();
                        }

                        $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                        $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ap_acc->id)->where('supplier_transaction_id', $strans->id)->first();
                        if (is_null($ap_ledger)) {   
                            $ap_ledger = new GeneralLedger();
                            $ap_ledger->shop_id = $shop->id;
                            $ap_ledger->transaction_account_id = $ap_acc->id;
                            $ap_ledger->customer_transaction_id = $strans->id;
                            $ap_ledger->date = $strans->date;
                            $ap_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                            $ap_ledger->credit_amount = $strans->payment;
                            $ap_ledger->reference = 'PV-'.$strans->pv_no;
                            $ap_ledger->save();
                        }else{
                            $ap_ledger->credit_amount = $strans->payment;
                            $ap_ledger->save();
                        }

                        $acctrans->is_added_to_ledger = true;
                        $acctrans->save();
                    }
                }
            }

            $rmsuptransactions = RmSupplierTransaction::where('rm_supplier_transactions.shop_id', $shop->id)->where('is_deleted', false)->where('is_added_to_ledger', false)->join('suppliers', 'suppliers.id', '=', 'rm_supplier_transactions.supplier_id')->select('rm_supplier_transactions.id as id', 'rm_purchase_id', 'name', 'date', 'amount', 'pv_no', 'payment', 'payment_mode', 'is_ob')->get();
            foreach ($rmsuptransactions as $key => $strans) {
                $acctrans = RmSupplierTransaction::find($strans->id);
                if (!is_null($strans->amount)) {
                    $purchase = RmPurchase::find($strans->purchase_id);
                    if (!is_null($purchase)) {
                        Log::info('Purchase Type : '.$purchase_type);
                        if ($purchase->purchase_type == 'cash') {
                            Log::info('Cash Purchase from '.$strans->name.' : '.$strans->amount);
                            $inv_acc = TransactionAccount::where('account_number', 1020)->where('company_id', $shop->company_id)->first();
                            $inv_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $inv_acc->id)->where('rm_supplier_transaction_id', $strans->id)->first();
                            if (is_null($inv_ledger)) {   
                                $inv_ledger = new GeneralLedger();
                                $inv_ledger->shop_id = $shop->id;
                                $inv_ledger->transaction_account_id = $inv_acc->id;
                                $inv_ledger->rm_supplier_transaction_id = $strans->id;
                                $inv_ledger->date = $strans->date;
                                $inv_ledger->transaction_description = 'Purchase Inventory for cash';
                                $inv_ledger->debit_amount = $strans->amount;
                                $inv_ledger->reference = 'GRN-'.$purchase->grn_no;
                                $inv_ledger->save();
                            }else{
                                $inv_ledger->debit_amount = $strans->amount;
                                $inv_ledger->save();
                            }

                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }else{
                            Log::info('Credit Purchase from '.$strans->name.' : '.$strans->amount);
                            
                            $inv_acc = TransactionAccount::where('account_number', 1020)->where('company_id', $shop->company_id)->first();
                            $inv_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $inv_acc->id)->where('rm_supplier_transaction_id', $strans->id)->first();
                            if (is_null($inv_ledger)) {   
                                $inv_ledger = new GeneralLedger();
                                $inv_ledger->shop_id = $shop->id;
                                $inv_ledger->transaction_account_id = $inv_acc->id;
                                $inv_ledger->rm_supplier_transaction_id = $strans->id;
                                $inv_ledger->date = $strans->date;
                                $inv_ledger->transaction_description = 'Purchase Inventory on credit';
                                $inv_ledger->debit_amount = $strans->amount;
                                $inv_ledger->reference = 'GRN-'.$purchase->grn_no;
                                $inv_ledger->save();
                            }else{
                                $inv_ledger->debit_amount = $strans->amount;
                                $inv_ledger->save();
                            }

                            $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                            $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ap_acc->id)->where('rm_supplier_transaction_id', $strans->id)->first();
                            if (is_null($ap_ledger)) {   
                                $ap_ledger = new GeneralLedger();
                                $ap_ledger->shop_id = $shop->id;
                                $ap_ledger->transaction_account_id = $ap_acc->id;
                                $ap_ledger->rm_supplier_transaction_id = $strans->id;
                                $ap_ledger->date = $strans->date;
                                $ap_ledger->transaction_description = 'Purchase of Inventory on credit';
                                $ap_ledger->credit_amount = $strans->amount;
                                $ap_ledger->reference = 'GRN-'.$purchase->grn_no;
                                $ap_ledger->save();
                            }else{
                                $ap_ledger->credit_amount = $strans->amount;
                                $ap_ledger->save();
                            }

                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }
                    }else{
                        $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                        $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ap_acc->id)->where('rm_supplier_transaction_id', $strans->id)->first();
                        if (is_null($ap_ledger)) {   
                            $ap_ledger = new GeneralLedger();
                            $ap_ledger->shop_id = $shop->id;
                            $ap_ledger->transaction_account_id = $ap_acc->id;
                            $ap_ledger->rm_supplier_transaction_id = $strans->id;
                            $ap_ledger->date = $strans->date;
                            $ap_ledger->transaction_description = 'Opening Balance for '.$strans->name;
                            $ap_ledger->credit_amount = $strans->amount;
                            $ap_ledger->reference = 'OB-'.$strans->name;
                            $ap_ledger->save();
                        }else{
                            $ap_ledger->credit_amount = $strans->amount;
                            $ap_ledger->save();
                        }

                        $acctrans->is_added_to_ledger = true;
                        $acctrans->save();
                    }
                }

                if (!is_null($strans->payment)) {
                    $ppay = RmPurchasePayment::where('rm_trans_id', $strans->id)->first();
                    if (!is_null($ppay)) {
                        $purchase = RmPurchase::find($ppay->purchase_id);
                        if (!is_null($purchase)) {
                            Log::info('Purchase Type : '.$purchase->purchase_type);
                            if ($purchase->purchase_type == 'cash') {
                                Log::info('Payment to '.$strans->name.' : '.$strans->payment);
                                $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                                $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('rm_supplier_transaction_id', $strans->id)->first();
                                if (is_null($cash_ledger)) {   
                                    $cash_ledger = new GeneralLedger();
                                    $cash_ledger->shop_id = $shop->id;
                                    $cash_ledger->transaction_account_id = $cash_acc->id;
                                    $cash_ledger->rm_supplier_transaction_id = $strans->id;
                                    $cash_ledger->date = $strans->date;
                                    $cash_ledger->transaction_description = 'Payment for cash purchase';
                                    $cash_ledger->credit_amount = $strans->payment;
                                    $cash_ledger->reference = 'PV-'.$strans->pv_no;
                                    $cash_ledger->save();
                                }else{
                                    $cash_ledger->debit_amount = $strans->payment;
                                    $cash_ledger->save();
                                }

                                $acctrans->is_added_to_ledger = true;
                                $acctrans->save();
                            }else{
                                $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                                $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('rm_supplier_transaction_id', $strans->id)->first();
                                if (is_null($cash_ledger)) {   
                                    $cash_ledger = new GeneralLedger();
                                    $cash_ledger->shop_id = $shop->id;
                                    $cash_ledger->transaction_account_id = $cash_acc->id;
                                    $cash_ledger->rm_supplier_transaction_id = $strans->id;
                                    $cash_ledger->date = $strans->date;
                                    $cash_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                                    $cash_ledger->credit_amount = $strans->payment;
                                    $cash_ledger->reference = 'PV-'.$strans->pv_no;
                                    $cash_ledger->save();
                                }else{
                                    $cash_ledger->credit_amount = $strans->payment;
                                    $cash_ledger->save();
                                }

                                $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                                $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ar_acc->id)->where('rm_supplier_transaction_id', $strans->id)->first();
                                if (is_null($ap_ledger)) {   
                                    $ap_ledger = new GeneralLedger();
                                    $ap_ledger->shop_id = $shop->id;
                                    $ap_ledger->transaction_account_id = $ap_acc->id;
                                    $ap_ledger->customer_transaction_id = $strans->id;
                                    $ap_ledger->date = $strans->date;
                                    $ap_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                                    $ap_ledger->credit_amount = $strans->payment;
                                    $ap_ledger->reference = 'PV-'.$strans->pv_no;
                                    $ap_ledger->save();
                                }else{
                                    $ap_ledger->credit_amount = $strans->payment;
                                    $ap_ledger->save();
                                }

                                $acctrans->is_added_to_ledger = true;
                                $acctrans->save();
                            }
                        }else{
                            $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                            $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('rm_supplier_transaction_id', $strans->id)->first();
                            if (is_null($cash_ledger)) {   
                                $cash_ledger = new GeneralLedger();
                                $cash_ledger->shop_id = $shop->id;
                                $cash_ledger->transaction_account_id = $cash_acc->id;
                                $cash_ledger->rm_supplier_transaction_id = $strans->id;
                                $cash_ledger->date = $strans->date;
                                $cash_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                                $cash_ledger->credit_amount = $strans->payment;
                                $cash_ledger->reference = 'PV-'.$strans->pv_no;
                                $cash_ledger->save();
                            }else{
                                $cash_ledger->credit_amount = $strans->payment;
                                $cash_ledger->save();
                            }

                            $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                            $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ap_acc->id)->where('rm_supplier_transaction_id', $strans->id)->first();
                            if (is_null($ap_ledger)) {   
                                $ap_ledger = new GeneralLedger();
                                $ap_ledger->shop_id = $shop->id;
                                $ap_ledger->transaction_account_id = $ap_acc->id;
                                $ap_ledger->customer_transaction_id = $strans->id;
                                $ap_ledger->date = $strans->date;
                                $ap_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                                $ap_ledger->credit_amount = $strans->payment;
                                $ap_ledger->reference = 'PV-'.$strans->pv_no;
                                $ap_ledger->save();
                            }else{
                                $ap_ledger->credit_amount = $strans->payment;
                                $ap_ledger->save();
                            }


                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }
                    }else{
                        $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                        $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('rm_supplier_transaction_id', $strans->id)->first();
                        if (is_null($cash_ledger)) {   
                            $cash_ledger = new GeneralLedger();
                            $cash_ledger->shop_id = $shop->id;
                            $cash_ledger->transaction_account_id = $cash_acc->id;
                            $cash_ledger->rm_supplier_transaction_id = $strans->id;
                            $cash_ledger->date = $strans->date;
                            $cash_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                            $cash_ledger->credit_amount = $strans->payment;
                            $cash_ledger->reference = 'PV-'.$strans->pv_no;
                            $cash_ledger->save();
                        }else{
                            $cash_ledger->credit_amount = $strans->payment;
                            $cash_ledger->save();
                        }

                        $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                        $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ap_acc->id)->where('rm_supplier_transaction_id', $strans->id)->first();
                        if (is_null($ap_ledger)) {   
                            $ap_ledger = new GeneralLedger();
                            $ap_ledger->shop_id = $shop->id;
                            $ap_ledger->transaction_account_id = $ap_acc->id;
                            $ap_ledger->customer_transaction_id = $strans->id;
                            $ap_ledger->date = $strans->date;
                            $ap_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                            $ap_ledger->credit_amount = $strans->payment;
                            $ap_ledger->reference = 'PV-'.$strans->pv_no;
                            $ap_ledger->save();
                        }else{
                            $ap_ledger->credit_amount = $strans->payment;
                            $ap_ledger->save();
                        }

                        $acctrans->is_added_to_ledger = true;
                        $acctrans->save();
                    }
                }
            }


            $pmsuptransactions = PmSupplierTransaction::where('pm_supplier_transactions.shop_id', $shop->id)->where('is_deleted', false)->where('is_added_to_ledger', false)->join('suppliers', 'suppliers.id', '=', 'pm_supplier_transactions.supplier_id')->select('pm_supplier_transactions.id as id', 'pm_purchase_id', 'name', 'date', 'amount', 'pv_no', 'payment', 'payment_mode', 'is_ob')->get();
            foreach ($pmsuptransactions as $key => $strans) {
                $acctrans = PmSupplierTransaction::find($strans->id);
                if (!is_null($strans->amount)) {
                    $purchase = PmPurchase::find($strans->purchase_id);
                    if (!is_null($purchase)) {
                        Log::info('Purchase Type : '.$purchase_type);
                        if ($purchase->purchase_type == 'cash') {
                            Log::info('Cash Purchase from '.$strans->name.' : '.$strans->amount);
                            $inv_acc = TransactionAccount::where('account_number', 1020)->where('company_id', $shop->company_id)->first();
                            $inv_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $inv_acc->id)->where('pm_supplier_transaction_id', $strans->id)->first();
                            if (is_null($inv_ledger)) {   
                                $inv_ledger = new GeneralLedger();
                                $inv_ledger->shop_id = $shop->id;
                                $inv_ledger->transaction_account_id = $inv_acc->id;
                                $inv_ledger->pm_supplier_transaction_id = $strans->id;
                                $inv_ledger->date = $strans->date;
                                $inv_ledger->transaction_description = 'Purchase Packaging Materials for cash';
                                $inv_ledger->debit_amount = $strans->amount;
                                $inv_ledger->reference = 'GRN-'.$purchase->grn_no;
                                $inv_ledger->save();
                            }else{
                                $inv_ledger->debit_amount = $strans->amount;
                                $inv_ledger->save();
                            }

                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }else{
                            Log::info('Credit Purchase from '.$strans->name.' : '.$strans->amount);
                            
                            $inv_acc = TransactionAccount::where('account_number', 1020)->where('company_id', $shop->company_id)->first();
                            $inv_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $inv_acc->id)->where('pm_supplier_transaction_id', $strans->id)->first();
                            if (is_null($inv_ledger)) {   
                                $inv_ledger = new GeneralLedger();
                                $inv_ledger->shop_id = $shop->id;
                                $inv_ledger->transaction_account_id = $inv_acc->id;
                                $inv_ledger->pm_supplier_transaction_id = $strans->id;
                                $inv_ledger->date = $strans->date;
                                $inv_ledger->transaction_description = 'Purchase Packaging Materials on credit';
                                $inv_ledger->debit_amount = $strans->amount;
                                $inv_ledger->reference = 'GRN-'.$purchase->grn_no;
                                $inv_ledger->save();
                            }else{
                                $inv_ledger->debit_amount = $strans->amount;
                                $inv_ledger->save();
                            }

                            $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                            $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ap_acc->id)->where('pm_supplier_transaction_id', $strans->id)->first();
                            if (is_null($ap_ledger)) {   
                                $ap_ledger = new GeneralLedger();
                                $ap_ledger->shop_id = $shop->id;
                                $ap_ledger->transaction_account_id = $ap_acc->id;
                                $ap_ledger->pm_supplier_transaction_id = $strans->id;
                                $ap_ledger->date = $strans->date;
                                $ap_ledger->transaction_description = 'Purchase of Packaging Materials on credit';
                                $ap_ledger->credit_amount = $strans->amount;
                                $ap_ledger->reference = 'GRN-'.$purchase->grn_no;
                                $ap_ledger->save();
                            }else{
                                $ap_ledger->credit_amount = $strans->amount;
                                $ap_ledger->save();
                            }


                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }
                    }else{
                        $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                        $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ap_acc->id)->where('pm_supplier_transaction_id', $strans->id)->first();
                        if (is_null($ap_ledger)) {   
                            $ap_ledger = new GeneralLedger();
                            $ap_ledger->shop_id = $shop->id;
                            $ap_ledger->transaction_account_id = $ap_acc->id;
                            $ap_ledger->pm_supplier_transaction_id = $strans->id;
                            $ap_ledger->date = $strans->date;
                            $ap_ledger->transaction_description = 'Opening Balance for '.$strans->name;
                            $ap_ledger->credit_amount = $strans->amount;
                            $ap_ledger->reference = 'OB-'.$strans->name;
                            $ap_ledger->save();
                        }else{
                            $ap_ledger->credit_amount = $strans->amount;
                            $ap_ledger->save();
                        }

                        $acctrans->is_added_to_ledger = true;
                        $acctrans->save();
                    }
                }

                if (!is_null($strans->payment)) {
                    $ppay = PmPurchasePayment::where('pm_trans_id', $strans->id)->first();
                    if (!is_null($ppay)) {
                        $purchase = PmPurchase::find($ppay->purchase_id);
                        if (!is_null($purchase)) {
                            Log::info('Purchase Type : '.$purchase->purchase_type);
                            if ($purchase->purchase_type == 'cash') {
                                Log::info('Payment to '.$strans->name.' : '.$strans->payment);
                                $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                                $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('pm_supplier_transaction_id', $strans->id)->first();
                                if (is_null($cash_ledger)) {   
                                    $cash_ledger = new GeneralLedger();
                                    $cash_ledger->shop_id = $shop->id;
                                    $cash_ledger->transaction_account_id = $cash_acc->id;
                                    $cash_ledger->pm_supplier_transaction_id = $strans->id;
                                    $cash_ledger->date = $strans->date;
                                    $cash_ledger->transaction_description = 'Payment for cash purchase';
                                    $cash_ledger->credit_amount = $strans->payment;
                                    $cash_ledger->reference = 'PV-'.$strans->pv_no;
                                    $cash_ledger->save();
                                }else{
                                    $cash_ledger->debit_amount = $strans->payment;
                                    $cash_ledger->save();
                                }

                                
                                $acctrans->is_added_to_ledger = true;
                                $acctrans->save();
                            }else{
                                $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                                $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('pm_supplier_transaction_id', $strans->id)->first();
                                if (is_null($cash_ledger)) {   
                                    $cash_ledger = new GeneralLedger();
                                    $cash_ledger->shop_id = $shop->id;
                                    $cash_ledger->transaction_account_id = $cash_acc->id;
                                    $cash_ledger->pm_supplier_transaction_id = $strans->id;
                                    $cash_ledger->date = $strans->date;
                                    $cash_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                                    $cash_ledger->credit_amount = $strans->payment;
                                    $cash_ledger->reference = 'PV-'.$strans->pv_no;
                                    $cash_ledger->save();
                                }else{
                                    $cash_ledger->credit_amount = $strans->payment;
                                    $cash_ledger->save();
                                }

                                $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                                $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ar_acc->id)->where('pm_supplier_transaction_id', $strans->id)->first();
                                if (is_null($ap_ledger)) {   
                                    $ap_ledger = new GeneralLedger();
                                    $ap_ledger->shop_id = $shop->id;
                                    $ap_ledger->transaction_account_id = $ap_acc->id;
                                    $ap_ledger->customer_transaction_id = $strans->id;
                                    $ap_ledger->date = $strans->date;
                                    $ap_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                                    $ap_ledger->credit_amount = $strans->payment;
                                    $ap_ledger->reference = 'PV-'.$strans->pv_no;
                                    $ap_ledger->save();
                                }else{
                                    $ap_ledger->credit_amount = $strans->payment;
                                    $ap_ledger->save();
                                }


                                $acctrans->is_added_to_ledger = true;
                                $acctrans->save();
                            }
                        }else{
                            $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                            $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('pm_supplier_transaction_id', $strans->id)->first();
                            if (is_null($cash_ledger)) {   
                                $cash_ledger = new GeneralLedger();
                                $cash_ledger->shop_id = $shop->id;
                                $cash_ledger->transaction_account_id = $cash_acc->id;
                                $cash_ledger->pm_supplier_transaction_id = $strans->id;
                                $cash_ledger->date = $strans->date;
                                $cash_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                                $cash_ledger->credit_amount = $strans->payment;
                                $cash_ledger->reference = 'PV-'.$strans->pv_no;
                                $cash_ledger->save();
                            }else{
                                $cash_ledger->credit_amount = $strans->payment;
                                $cash_ledger->save();
                            }

                            $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                            $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ap_acc->id)->where('pm_supplier_transaction_id', $strans->id)->first();
                            if (is_null($ap_ledger)) {   
                                $ap_ledger = new GeneralLedger();
                                $ap_ledger->shop_id = $shop->id;
                                $ap_ledger->transaction_account_id = $ap_acc->id;
                                $ap_ledger->customer_transaction_id = $strans->id;
                                $ap_ledger->date = $strans->date;
                                $ap_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                                $ap_ledger->credit_amount = $strans->payment;
                                $ap_ledger->reference = 'PV-'.$strans->pv_no;
                                $ap_ledger->save();
                            }else{
                                $ap_ledger->credit_amount = $strans->payment;
                                $ap_ledger->save();
                            }

                            $acctrans->is_added_to_ledger = true;
                            $acctrans->save();
                        }
                    }else{
                        $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                        $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('pm_supplier_transaction_id', $strans->id)->first();
                        if (is_null($cash_ledger)) {   
                            $cash_ledger = new GeneralLedger();
                            $cash_ledger->shop_id = $shop->id;
                            $cash_ledger->transaction_account_id = $cash_acc->id;
                            $cash_ledger->pm_supplier_transaction_id = $strans->id;
                            $cash_ledger->date = $strans->date;
                            $cash_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                            $cash_ledger->credit_amount = $strans->payment;
                            $cash_ledger->reference = 'PV-'.$strans->pv_no;
                            $cash_ledger->save();
                        }else{
                            $cash_ledger->credit_amount = $strans->payment;
                            $cash_ledger->save();
                        }

                        $ap_acc = TransactionAccount::where('account_number', 2000)->where('company_id', $shop->company_id)->first();
                        $ap_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $ap_acc->id)->where('pm_supplier_transaction_id', $strans->id)->first();
                        if (is_null($ap_ledger)) {   
                            $ap_ledger = new GeneralLedger();
                            $ap_ledger->shop_id = $shop->id;
                            $ap_ledger->transaction_account_id = $ap_acc->id;
                            $ap_ledger->customer_transaction_id = $strans->id;
                            $ap_ledger->date = $strans->date;
                            $ap_ledger->transaction_description = 'Bill payment to supplier ('.$strans->name.')';
                            $ap_ledger->credit_amount = $strans->payment;
                            $ap_ledger->reference = 'PV-'.$strans->pv_no;
                            $ap_ledger->save();
                        }else{
                            $ap_ledger->credit_amount = $strans->payment;
                            $ap_ledger->save();
                        }

                        $acctrans->is_added_to_ledger = true;
                        $acctrans->save();
                    }
                }
            }

            $expenses = Expense::where('expenses.shop_id', $shop->id)->where('is_deleted', false)->where('is_added_to_ledger', false)->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->select('expenses.id as id', 'expense_categories.transaction_account_id as transaction_account_id', 'time_created', 'amount')->get();
            foreach ($expenses as $key => $expense) {
                $transacc = TransactionAccount::find($expense->transaction_account_id);
                if (!is_null($transacc)) {
                    $exp_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $transacc->id)->where('expense_id', $expense->id)->first();
                    if (is_null($exp_ledger)) {
                        $pv_no = '';
                        $exppay = ExpensePayment::where('expense_id', $expense->id)->first();
                        if (!is_null($exppay)) {
                             $pv_no = 'PV-'.$exppay->pv_no;
                         } 
                        $exp_ledger = new GeneralLedger();
                        $exp_ledger->shop_id = $shop->id;
                        $exp_ledger->transaction_account_id = $transacc->id;
                        $exp_ledger->expense_id = $expense->id;
                        $exp_ledger->date = $expense->time_created;
                        $exp_ledger->transaction_description = 'Paid '.$expense->expense_type;
                        $exp_ledger->debit_amount = $expense->amount;
                        $exp_ledger->reference = $pv_no;
                        $exp_ledger->save();
                    }else{
                        $exp_ledger->debit_amount = $expense->amount;
                        $exp_ledger->save();
                    }

                    $exprecord = Expense::find($expense->id);
                    $exprecord->is_added_to_ledger = true;
                    $exprecord->save();
                }
            }


            $expensepayments = ExpensePayment::where('expense_payments.shop_id', $shop->id)->where('expense_payments.is_deleted', false)->where('expense_payments.is_added_to_ledger', false)->join('expenses', 'expenses.id', '=', 'expense_payments.expense_id')->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->select('expense_payments.id as id', 'transaction_account_id', 'pay_date', 'expense_payments.amount as amount', 'name')->get();

            foreach ($expensepayments as $key => $exppay) {
                $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('expense_payment_id', $exppay->id)->first();
                if (is_null($cash_ledger)) {   
                    $cash_ledger = new GeneralLedger();
                    $cash_ledger->shop_id = $shop->id;
                    $cash_ledger->transaction_account_id = $cash_acc->id;
                    $cash_ledger->expense_payment_id = $exppay->id;
                    $cash_ledger->date = $exppay->pay_date;
                    $cash_ledger->transaction_description = $exppay->name.' Bill payment';
                    $cash_ledger->credit_amount = $exppay->amount;
                    $cash_ledger->reference = 'PV-'.$exppay->pv_no;
                    $cash_ledger->save();
                }else{
                    $cash_ledger->credit_amount = $exppay->amount;
                    $cash_ledger->save();
                }

                $payment = ExpensePayment::find($exppay->id);
                $payment->is_added_to_ledger = true;
                $payment->save();
            }

            $prodlabourcosts = ProdLabourCost::where('shop_id', $shop->id)->where('is_deleted', false)->where('is_added_to_ledger', false)->get();
            foreach ($prodlabourcosts as $key => $plc) {
                $pc_acc = TransactionAccount::where('account_number', 5140)->where('company_id', $shop->company_id)->first();
                $pc_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $pc_acc->id)->where('prod_labour_cost_id', $plc->id)->first();
                if (is_null($pc_ledger)) {   
                    $pc_ledger = new GeneralLedger();
                    $pc_ledger->shop_id = $shop->id;
                    $pc_ledger->transaction_account_id = $pc_acc->id;
                    $pc_ledger->prod_labour_cost_id = $plc->id;
                    $pc_ledger->date = $plc->date;
                    $pc_ledger->transaction_description = 'Direct Labour costs payment';
                    $pc_ledger->debit_amount = $plc->amount;
                    $pc_ledger->reference = 'PLC No.-'.$plc->plc_no;
                    $pc_ledger->save();
                }else{
                    $pc_ledger->debit_amount = $plc->amount;
                    $pc_ledger->save();
                }

                $plc->is_added_to_ledger = true;
                $plc->save();
            }

            $dlc_payments = PlcPayment::where('shop_id', $shop->id)->where('is_deleted', false)->where('is_added_to_ledger', false)->get();

            foreach ($dlc_payments as $key => $dlcpay) {
                $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('plc_payment_id', $dlcpay->id)->first();
                if (is_null($cash_ledger)) {   
                    $cash_ledger = new GeneralLedger();
                    $cash_ledger->shop_id = $shop->id;
                    $cash_ledger->transaction_account_id = $cash_acc->id;
                    $cash_ledger->plc_payment_id = $dlcpay->id;
                    $cash_ledger->date = $dlcpay->pay_date;
                    $cash_ledger->transaction_description = 'Direct Labour Cost payment';
                    $cash_ledger->credit_amount = $dlcpay->amount;
                    $cash_ledger->reference = 'PV-'.$dlcpay->pv_no;
                    $cash_ledger->save();
                }else{
                    $cash_ledger->credit_amount = $dlcpay->amount;
                    $cash_ledger->save();
                }

                $dlcpay->is_added_to_ledger = true;
                $dlcpay->save();
            }

            $mohcosts = MohCost::where('shop_id', $shop->id)->where('is_deleted', false)->where('is_added_to_ledger', false)->get();
            foreach ($mohcosts as $key => $moh) {
                $pc_acc = TransactionAccount::where('account_number', 5140)->where('company_id', $shop->company_id)->first();
                $pc_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $pc_acc->id)->where('moh_cost_id', $moh->id)->first();
                if (is_null($pc_ledger)) {   
                    $pc_ledger = new GeneralLedger();
                    $pc_ledger->shop_id = $shop->id;
                    $pc_ledger->transaction_account_id = $pc_acc->id;
                    $pc_ledger->moh_cost_id = $moh->id;
                    $pc_ledger->date = $moh->date;
                    $pc_ledger->transaction_description = 'MOH costs payment';
                    $pc_ledger->debit_amount = $moh->amount;
                    $pc_ledger->reference = 'MOH No.-'.$moh->moh_no;
                    $pc_ledger->save();
                }else{
                    $pc_ledger->debit_amount = $moh->amount;
                    $pc_ledger->save();
                }

                $moh->is_added_to_ledger = true;
                $moh->save();
            }

            $moh_payments = MohCostPayment::where('shop_id', $shop->id)->where('is_deleted', false)->where('is_added_to_ledger', false)->get();

            foreach ($moh_payments as $key => $mohpay) {
                $cash_acc = TransactionAccount::where('account_number', 1000)->where('company_id', $shop->company_id)->first();
                $cash_ledger = GeneralLedger::where('shop_id', $shop->id)->where('transaction_account_id', $cash_acc->id)->where('plc_payment_id', $mohpay->id)->first();
                if (is_null($cash_ledger)) {   
                    $cash_ledger = new GeneralLedger();
                    $cash_ledger->shop_id = $shop->id;
                    $cash_ledger->transaction_account_id = $cash_acc->id;
                    $cash_ledger->moh_cost_payment_id = $mohpay->id;
                    $cash_ledger->date = $mohpay->pay_date;
                    $cash_ledger->transaction_description = 'MOH Cost payment';
                    $cash_ledger->credit_amount = $mohpay->amount;
                    $cash_ledger->reference = 'PV-'.$mohpay->pv_no;
                    $cash_ledger->save();
                }else{
                    $cash_ledger->credit_amount = $mohpay->amount;
                    $cash_ledger->save();
                }

                $mohpay->is_added_to_ledger = true;
                $mohpay->save();
            }

            $cashins = CashIn::where('shop_id', $shop->id)->where('is_added_to_ledger', false)->where('is_loan', false)->get();
            foreach ($cashins as $key => $cashin) {
                $cash_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number', 1000)->first();
                $gledger = GeneralLedger::where('shop_id', $shop->id)->where('cash_in_id', $cashin->id)->first();
                if (is_null($gledger)) {
                    $gledger = new GeneralLedger();
                    $gledger->shop_id = $shop->id;
                    $gledger->transaction_account_id = $cash_acc->id;
                    $gledger->cash_in_id = $cashin->id;
                    $gledger->date = $cashin->in_date;
                    $gledger->transaction_description = $cashin->source;
                    $gledger->debit_amount = $cashin->amount;
                    $gledger->save();
                }else{
                    $gledger->debit_amount = $cashin->amount;
                    $gledger->save();
                }

                $cashin->is_added_to_ledger = true;
                $cashin->save();
            }

            $cashins = CashIn::where('shop_id', $shop->id)->where('is_added_to_ledger', false)->where('is_loan', true)->get();
            foreach ($cashins as $key => $cashin) {
                $cash_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number', 1000)->first();
                $gledger = GeneralLedger::where('shop_id', $shop->id)->where('cash_in_id', $cashin->id)->first();
                if (is_null($gledger)) {
                    $gledger = new GeneralLedger();
                    $gledger->shop_id = $shop->id;
                    $gledger->transaction_account_id = $cash_acc->id;
                    $gledger->cash_in_id = $cashin->id;
                    $gledger->date = $cashin->in_date;
                    $gledger->transaction_description = $cashin->source;
                    $gledger->debit_amount = $cashin->amount;
                    $gledger->save();
                }else{
                    $gledger->debit_amount = $cashin->amount;
                    $gledger->save();
                }

                $lp_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number', 2040)->first();
                $lp_ledger = GeneralLedger::where('shop_id', $shop->id)->where('cash_in_id', $cashin->id)->first();
                if (is_null($lp_ledger)) {
                    $lp_ledger = new GeneralLedger();
                    $lp_ledger->shop_id = $shop->id;
                    $lp_ledger->transaction_account_id = $lp_acc->id;
                    $lp_ledger->cash_in_id = $cashin->id;
                    $lp_ledger->date = $cashin->in_date;
                    $lp_ledger->transaction_description = $cashin->source;
                    $lp_ledger->credit_amount = $cashin->amount;
                    $lp_ledger->save();
                }else{
                    $lp_ledger->credit_amount = $cashin->amount;
                    $lp_ledger->save();
                }

                $cashin->is_added_to_ledger = true;
                $cashin->save();
            }

            $cashouts = CashOut::where('shop_id', $shop->id)->where('is_added_to_ledger', false)->where('is_loan_pay', false)->get();
            foreach ($cashouts as $key => $cashout) {
                $cash_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number', 1000)->first();
                $gledger = GeneralLedger::where('shop_id', $shop->id)->where('cash_out_id', $cashout->id)->first();
                if (is_null($gledger)) {
                    $gledger = new GeneralLedger();
                    $gledger->shop_id = $shop->id;
                    $gledger->transaction_account_id = $cash_acc->id;
                    $gledger->cash_out_id = $cashout->id;
                    $gledger->date = $cashout->out_date;
                    $gledger->transaction_description = $cashout->reason;
                    $gledger->credit_amount = $cashout->amount;
                    $gledger->save();
                }else{
                    $gledger->credit_amount = $cashout->amount;
                    $gledger->save();
                }

                $cashout->is_added_to_ledger = true;
                $cashout->save();
            }

            $cashouts = CashOut::where('shop_id', $shop->id)->where('is_added_to_ledger', false)->where('is_loan_pay', true)->get();
            foreach ($cashouts as $key => $cashout) {
                $cash_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number', 1000)->first();
                $gledger = GeneralLedger::where('shop_id', $shop->id)->where('cash_out_id', $cashout->id)->first();
                if (is_null($gledger)) {
                    $gledger = new GeneralLedger();
                    $gledger->shop_id = $shop->id;
                    $gledger->transaction_account_id = $cash_acc->id;
                    $gledger->cash_out_id = $cashout->id;
                    $gledger->date = $cashout->out_date;
                    $gledger->transaction_description = $cashout->reason;
                    $gledger->credit_amount = $cashout->amount;
                    $gledger->save();
                }else{
                    $gledger->credit_amount = $cashout->amount;
                    $gledger->save();
                }

                $lp_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number', 2040)->first();
                $lp_ledger = GeneralLedger::where('shop_id', $shop->id)->where('cash_in_id', $cashout->id)->first();
                if (is_null($lp_ledger)) {
                    $lp_ledger = new GeneralLedger();
                    $lp_ledger->shop_id = $shop->id;
                    $lp_ledger->transaction_account_id = $lp_acc->id;
                    $lp_ledger->cash_out_id = $cashout->id;
                    $lp_ledger->date = $cashout->out_date;
                    $lp_ledger->transaction_description = $cashout->reason;
                    $lp_ledger->debit_amount = $cashout->amount;
                    $lp_ledger->save();
                }else{
                    $lp_ledger->debit_amount = $cashout->amount;
                    $lp_ledger->save();
                }


                $cashout->is_added_to_ledger = true;
                $cashout->save();
            }

            $deductions = PayrollDeduction::where('shop_id', $shop->id)->where('is_added_to_ledger', false)->get();
            foreach ($deductions as $key => $deduction) {
                if ($deduction->name == 'PAYE') {
                    $tax_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number',2020)->first();
                    $gledger = GeneralLedger::where('shop_id', $shop->id)->where('payroll_deduction_id', $deduction->id)->first();
                    if (is_null($gledger)) {
                        $gledger = new GeneralLedger();
                        $gledger->shop_id = $shop->id;
                        $gledger->transaction_account_id = $tax_acc->id;
                        $gledger->payroll_deduction_id = $deduction->id;
                        $gledger->date = $deduction->date;
                        $gledger->transaction_description = $deduction->name.' for '.date('M Y', strtotime($deduction->date));
                        $gledger->debit_amount = $deduction->amount;
                        $gledger->save();
                    }else{
                        $gledger->debit_amount = $deduction->amount;
                        $gledger->save();
                    }
                }elseif ($deduction->name == 'HESLB') {
                    $heslb_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number',2060)->first();
                    $gledger = GeneralLedger::where('shop_id', $shop->id)->where('payroll_deduction_id', $deduction->id)->first();
                    if (is_null($gledger)) {
                        $gledger = new GeneralLedger();
                        $gledger->shop_id = $shop->id;
                        $gledger->transaction_account_id = $heslb_acc->id;
                        $gledger->payroll_deduction_id = $deduction->id;
                        $gledger->date = $deduction->date;
                        $gledger->transaction_description = $deduction->name.' for '.date('M Y', strtotime($deduction->date));
                        $gledger->debit_amount = $deduction->amount;
                        $gledger->save();
                    }else{
                        $gledger->debit_amount = $deduction->amount;
                        $gledger->save();
                    }
                }elseif ($deduction->name == 'Employee Recovery') {
                    $empr_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number',1060)->first();
                    $gledger = GeneralLedger::where('shop_id', $shop->id)->where('payroll_deduction_id', $deduction->id)->first();
                    if (is_null($gledger)) {
                        $gledger = new GeneralLedger();
                        $gledger->shop_id = $shop->id;
                        $gledger->transaction_account_id = $empr_acc->id;
                        $gledger->payroll_deduction_id = $deduction->id;
                        $gledger->date = $deduction->date;
                        $gledger->transaction_description = $deduction->name.' for '.date('M Y', strtotime($deduction->date));
                        $gledger->debit_amount = $deduction->amount;
                        $gledger->save();
                    }else{
                        $gledger->debit_amount = $deduction->amount;
                        $gledger->save();
                    }
                }else {
                    $ssc_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number',2050)->first();
                    $gledger = GeneralLedger::where('shop_id', $shop->id)->where('payroll_deduction_id', $deduction->id)->first();
                    if (is_null($gledger)) {
                        $gledger = new GeneralLedger();
                        $gledger->shop_id = $shop->id;
                        $gledger->transaction_account_id = $ssc_acc->id;
                        $gledger->payroll_deduction_id = $deduction->id;
                        $gledger->date = $deduction->date;
                        $gledger->transaction_description = $deduction->name.' for '.date('M Y', strtotime($deduction->date));
                        $gledger->debit_amount = $deduction->amount;
                        $gledger->save();
                    }else{
                        $gledger->debit_amount = $deduction->amount;
                        $gledger->save();
                    }
                }

                $deduction->is_added_to_ledger = true;
                $deduction->save();
            }

            $deductionpayments = PayrollDeductionPayment::where('shop_id', $shop->id)->where('is_added_to_ledger', false)->get();
            foreach ($deductionpayments as $key => $payment) {
                $deduction = PayrollDeduction::find($payment->payroll_deduction_id);
                if (!is_null($deduction)) {
                    if ($deduction->name == 'PAYE') {
                        $cash_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number',1000)->first();
                        $gledger = GeneralLedger::where('shop_id', $shop->id)->where('payroll_deduction_payment_id', $payment->id)->first();
                        if (is_null($gledger)) {
                            $gledger = new GeneralLedger();
                            $gledger->shop_id = $shop->id;
                            $gledger->transaction_account_id = $cash_acc->id;
                            $gledger->payroll_deduction_payment_id = $payment->id;
                            $gledger->date = $payment->pay_date;
                            $gledger->transaction_description = $deduction->name.' Payment for '.date('M Y', strtotime($deduction->date));
                            $gledger->credit_amount = $payment->amount_paid;
                            $gledger->save();
                        }else{
                            $gledger->credit_amount = $payment->amount_paid;
                            $gledger->save();
                        }
                    }elseif ($deduction->name == 'HESLB') {
                        $cash_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number',1000)->first();
                        $gledger = GeneralLedger::where('shop_id', $shop->id)->where('payroll_deduction_payment_id', $payment->id)->first();
                        if (is_null($gledger)) {
                            $gledger = new GeneralLedger();
                            $gledger->shop_id = $shop->id;
                            $gledger->transaction_account_id = $cash_acc->id;
                            $gledger->payroll_deduction_payment_id = $payment->id;
                            $gledger->date = $payment->pay_date;
                            $gledger->transaction_description = $deduction->name.' Payment for '.date('M Y', strtotime($deduction->date));
                            $gledger->credit_amount = $payment->amount_paid;
                            $gledger->save();
                        }else{
                            $gledger->credit_amount = $payment->amount_paid;
                            $gledger->save();
                        }
                    }else {
                        $cash_acc = TransactionAccount::where('company_id', $shop->company_id)->where('account_number',1000)->first();
                        $gledger = GeneralLedger::where('shop_id', $shop->id)->where('payroll_deduction_payment_id', $payment->id)->first();
                        if (is_null($gledger)) {
                            $gledger = new GeneralLedger();
                            $gledger->shop_id = $shop->id;
                            $gledger->transaction_account_id = $cash_acc->id;
                            $gledger->payroll_deduction_payment_id = $payment->id;
                            $gledger->date = $payment->pay_date;
                            $gledger->transaction_description = $deduction->name.' Payment for '.date('M Y', strtotime($deduction->date));
                            $gledger->credit_amount = $payment->amount_paid;
                            $gledger->save();
                        }else{
                            $gledger->credit_amount = $payment->amount_paid;
                            $gledger->save();
                        }
                    }

                    $payment->is_added_to_ledger = true;
                    $payment->save();
                }else{
                    Log::info($payment);
                    Log::info('Has no deduction linked to');
                }
            }
        }
    }
}
