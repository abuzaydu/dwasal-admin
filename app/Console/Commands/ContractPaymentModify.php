<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Contract;
use App\Models\AnSale;
use App\Models\Customer;
use App\Models\SalePayment;
use App\Models\DailyDeposit;
use App\Models\ContractService;
use App\Models\ServiceSaleItem;

class ContractPaymentModify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:contract-payment-modify';

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
        $shop = Shop::find(5);
        if (!is_null($shop)) {
            
            Log::info('Modifying data for '.$shop->name);
            $acontracts = Contract::where('shop_id', $shop->id)->get();
            foreach ($acontracts as $key => $value) {
                // $device = Device::find($value->device_id);
                // $device->is_assigned = true;
                // $device->save();

                // $value->actual_end_date = $value->end_date;
                // $value->save();

                // $deposits = DailyDeposit::where('contract_id', $value->id)->count();
                // // Log::info('Contract Days waorked '.$value->days_worked.' Deposits '.$deposits);
                // $value->days_worked = $deposits;
                // $value->save();
                // $datediff = strtotime($value->end_date)-strtotime($value->start_date);
                // $workingdays = round($datediff / (60 * 60 * 24));
                // if ($value->status == 'Working' || $value->status == 'Graduated') {
                //     if ($value->days_worked < $workingdays) {
                //         $value->status = 'Working';
                //         $value->save();
                //     }else{
                //         $value->status = 'Graduated';
                //         $value->save();
                //     }

                // }
                // $this->removeDeposits($value);
                // $this->updatePayments($value);
                $this->modifyContractEndDate($value);
            }
        }else{
            Log::info('shop not Found');
        }
    }


    public function modifyContractEndDate($contract)
    {
        $starting = Carbon::parse($contract->start_date);
        $enddate = $starting->addDays(402)->format('Y-m-d');
        Log::info('Start : '.$starting.' End : '.$enddate);
        $contract->end_date = $enddate;
        $contract->actual_end_date = $enddate;
        $contract->save();

        $sale = AnSale::find($contract->an_sale_id);
        if (!is_null($sale)) {
                
            $diff = strtotime($contract->end_date) - strtotime($contract->start_date);
            $days = round($diff / (60 * 60 * 24));

            $ContractServices = ContractService::where('contract_id', $contract->id)->where('is_add_on', false)->get();
            $contract_amt = 0;
            foreach ($ContractServices as $key => $bservice) {
                $bservice->qty = $days;
                $bservice->total = $bservice->qty*$bservice->unit_price;
                $bservice->save();

                $saleitemData = ServiceSaleItem::where('an_sale_id', $sale->id)->where('service_id', $bservice->service_id)->first();
                if (!is_null($saleitemData)) {
                    $saleitemData->no_of_repeatition = $bservice->qty;
                    $saleitemData->total = $saleitemData->no_of_repeatition*$saleitemData->price;
                    $saleitemData->save();
                }
                
                $contract_amt += $bservice->total;
            }

            $servsale_amount = 0;
            $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
            foreach ($servitems as $key => $item) {
                $servsale_amount += $item->total;
            }
            Log::info('Contract Amt : '.$contract_amt);
            Log::info('Sale Amount : '.$servsale_amount);

            $contract->amount = $contract_amt;
            $contract->save();

            $this->updateContractStatusOnly($contract);

            $sale->sale_amount = $servsale_amount;
            $sale->save();

            $this->updateSaleStatus($sale);

        }
    }
    
    public function updateContractStatusOnly($contract)
    {
        $days_worked = DailyDeposit::where('contract_id', $contract->id)->count();
        if ($days_worked > 0) {
            $cservice = ContractService::where('contract_id', $contract->id)->where('is_add_on', 0)->first();
            $contract->days_worked = $days_worked;
            $contract->amount_paid = $days_worked*$cservice->unit_price;
            if ($contract->amount <= $contract->amount_paid) {
                $lastdeposit = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'desc')->first();
                $contract->status = 'Graduated';
                $contract->actual_end_date = $lastdeposit->date;
                $contract->terminated_at = Carbon::now();
            }else{
                $contract->status = 'Working';
            }
            $contract->save();
        }
    }

    public function updateSaleStatus($sale)
    {
        $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
        $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
        $netsales_amount = $tnetsales-$tnetreturn;
        if ($netsales_amount == $sale->sale_amount_paid) {
            $sale->status = 'Paid';
            $sale->is_paid = true;
            $sale->time_paid = \Carbon\Carbon::now();
            $sale->save();
        }elseif ($netsales_amount > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
            $sale->status = 'Partially Paid';
            $sale->time_paid = null;
            $sale->is_paid = false;
            $sale->save();
        }elseif ($netsales_amount < $sale->sale_amount_paid) {
            $sale->status = 'Excess Paid';
            $sale->is_paid = true;
            $sale->time_paid = \Carbon\Carbon::now();
            $sale->save();
        }else{
            $sale->status = 'Unpaid';
            $sale->time_paid = null;
            $sale->is_paid = false;
            $sale->save();
        }
    }

    public function updatePayments($contract)
    {
        $sale = AnSale::find($contract->an_sale_id);
        if (!is_null($sale)) {
            // $netsales = (($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount);
            // $netreturn = (($sale->return_amount-$sale->return_discount)+$sale->return_tax);
            // $netpayable = $netsales-$netreturn;
            $customer = Customer::find($contract->customer_id);
            // if ($sale->sale_amount_paid > $netsales) {
                Log::info('Riders Name '.$customer->name);
                // $spays = SalePayment::where('an_sale_id', $sale->id)->get();
                // foreach ($spays as $key => $payment) {
                //     // $deposits = DailyDeposit::where('sale_payment_id', $payment->id)->sum('amount');
                    // if ($payment->amount != $deposits) {
                    //     // Log::info('Excess Paid '.$sale->sale_amount_paid-$netsales);
                    //     Log::info('Pay date :'.$payment->pay_date.' Receipt '.$payment->receipt_no.' Amount : '.$payment->amount.' Deposit AMT : '.$deposits);
                    // }
                // }
            // }

            $this->updateContractStatus($sale);
        }else{
            Log::info('Invoice not Found');
        }
    }

    public function removeDeposits($contract)
    {
        $customer = Customer::find($contract->customer_id);
        Log::info('Riders Name '.$customer->name);
        $deposits = DailyDeposit::where('contract_id', $contract->id)->get();
        Log::info($deposits->count());
        foreach ($deposits as $key => $value) {
            $value->delete();
        }
    }

    public function updateContractStatus($sale)
    {
        $contract = Contract::where('an_sale_id', $sale->id)->first();
        if (!is_null($contract)) {
            $incservices = ContractService::where('contract_id', $contract->id)->where('is_add_on', 1)->sum('total');
            $spays = SalePayment::where('an_sale_id', $sale->id)->get();
            if ($spays->count() > 0) {
                foreach ($spays as $key => $payment) {       
                    if ($key > 0) {
                        $cservice = ContractService::where('contract_id', $contract->id)->where('is_add_on', 0)->first();
                        if (!is_null($cservice)) {
                            $paiddays = $payment->amount/$cservice->unit_price;
                            $lddeposit = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'desc')->first();
                            if (!is_null($lddeposit)) {
                                $currdate = $lddeposit->date;
                                for ($i=0; $i < $paiddays; $i++) {
                                    $date = strtotime("+1 day", strtotime($currdate));
                                    $currdate = date("Y-m-d", $date);
                                    $deposit = new DailyDeposit();
                                    $deposit->contract_id = $contract->id;
                                    $deposit->sale_payment_id = $payment->id;
                                    $deposit->date = $currdate;
                                    $deposit->amount = $cservice->unit_price;
                                    $deposit->save();
                                }
                            }else{
                                $currdate = $contract->start_date;
                                for ($i=0; $i < $paiddays; $i++) {
                                    $date = strtotime("+".$i." day", strtotime($currdate));
                                    if ($i > 0) {
                                        $date = strtotime("+1 day", strtotime($currdate));
                                    }
                                    $currdate = date("Y-m-d", $date);
                                    $deposit = new DailyDeposit();
                                    $deposit->contract_id = $contract->id;
                                    $deposit->sale_payment_id = $payment->id;
                                    $deposit->date = $currdate;
                                    $deposit->amount = $cservice->unit_price;
                                    $deposit->save();
                                }
                            }

                            $days_worked = DailyDeposit::where('contract_id', $contract->id)->count();
                            $contract->days_worked = $days_worked;
                            $contract->amount_paid = $days_worked*$cservice->unit_price;
                            if ($contract->amount <= $contract->amount_paid) {
                                $lastdeposit = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'desc')->first();
                                $contract->status = 'Graduated';
                                $contract->actual_end_date = $lastdeposit->date;
                                $contract->terminated_at = Carbon::now();
                            }else{
                                $contract->status = 'Working';
                            }
                            $contract->save();
                        }
                    }else{
                        $cservice = ContractService::where('contract_id', $contract->id)->where('is_add_on', 0)->first();
                        if (!is_null($cservice)) {
                            $amount = $payment->amount-$incservices;
                            if ($amount >= 0) {
                                Log::info('Jumla:  '.$payment->amount.' Kiingilio : '.$incservices.' Deposit:  '.$amount);
                                $paiddays = $amount/$cservice->unit_price;
                                $lddeposit = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'desc')->first();

                                if (!is_null($lddeposit)) {
                                    $currdate = $lddeposit->date;
                                    for ($i=0; $i < $paiddays; $i++) {
                                        $date = strtotime("+1 day", strtotime($currdate));
                                        $currdate = date("Y-m-d", $date);
                                        $deposit = new DailyDeposit();
                                        $deposit->contract_id = $contract->id;
                                        $deposit->sale_payment_id = $payment->id;
                                        $deposit->date = $currdate;
                                        $deposit->amount = $cservice->unit_price;
                                        $deposit->save();
                                    }
                                }else{
                                    $currdate = $contract->start_date;
                                    for ($i=0; $i < $paiddays; $i++) {
                                        $date = strtotime("+".$i." day", strtotime($currdate));
                                        if ($i > 0) {
                                            $date = strtotime("+1 day", strtotime($currdate));
                                        }
                                        $currdate = date("Y-m-d", $date);
                                        $deposit = new DailyDeposit();
                                        $deposit->contract_id = $contract->id;
                                        $deposit->sale_payment_id = $payment->id;
                                        $deposit->date = $currdate;
                                        $deposit->amount = $cservice->unit_price;
                                        $deposit->save();
                                    }
                                }

                                $days_worked = DailyDeposit::where('contract_id', $contract->id)->count();
                                $contract->days_worked = $days_worked;
                                $contract->amount_paid = $days_worked*$cservice->unit_price;
                                if ($contract->amount <= $contract->amount_paid) {
                                    $lastdeposit = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'desc')->first();
                                    $contract->status = 'Graduated';
                                    $contract->actual_end_date = $lastdeposit->date;
                                    $contract->terminated_at = Carbon::now();
                                }else{
                                    $contract->status = 'Working';
                                }
                                $contract->save();
                            }
                        }
                    }
                }
            }else{
                Log::info('No Payment done');
                // $contract->status = 'Working';
                // $contract->save();
            }
        }else{
            Log::info('Contract not Found');
        }
    }
}
