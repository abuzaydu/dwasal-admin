<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\SmsAccount;
use App\Models\SenderId;
use App\Models\SmsTemplate;
use App\Models\SmsSetting;
use App\Models\AnSale;

class CheckReorderPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-reorder-point';

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
        $this->checkReorder();
    }

    public function checkReorder()
    {
        $shops = Shop::whereRaw('business_type_id != 3')->join('payments', 'payments.shop_id', '=', 'shops.id')->where('is_expired', false)->select('shops.id as id', 'shops.name as name')->get();

        foreach ($shops as $key => $shop) {

            $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
            if (!is_null($smsacc)) {
                //Reorder point logic
                $products = $shop->products()->whereRaw('in_stock <= reorder_point')->count();
                if ($products > 0) {
                    $shop->notify(new ReorderPoint($shop));
                    $senderid = SenderId::where('sms_account_id', $smsacc->id)->where('auto_sms', true)->first();
                    if (!is_null($senderid)) {
                        $autotemp = SmsTemplate::where('shop_id', $shop->id)->where('is_auto_sms', true)->where('temp_for', 'reorder')->first();
                        if (!is_null($autotemp)) {
                            $message = $autotemp->message;
                            $users = $shop->users()->get();
                            foreach ($users as $key => $user) {
                                if (!is_null($this->formattedNumber($user->phone))) {
                                    $phone = $this->formattedNumber($user->phone);
    
                                    dispatch(new SendSMS($smsacc->username, $smsacc->password, $senderid->name, $phone, $message));
                                }
                            }
                        }
                    }
                }


                //Invoice notification logic for reminding due dates

                $smssetting = SmsSetting::where('shop_id', $shop->id)->first();
                if (!is_null($smssetting)) {  
                    $due = Carbon::now()->addDays($smssetting->days_before_due)->format('Y-m-d');         
                    $duedates = AnSale::where('shop_id', $shop->id)->where('due_date', $due)->where('is_paid', false)->where('is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->whereNotNull('phone')->select('name', 'phone', 'sale_amount', 'sale_discount', 'tax_amount', 'return_amount', 'return_discount', 'return_tax', 'sale_amount_paid', 'time_created', 'due_date', 'invoice_no')->get();
                    if (!is_null($duedates)) {
                        foreach ($duedates as $key => $sale) {
                            $senderid = SenderId::where('sms_account_id', $smsacc->id)->where('auto_sms', true)->first();
                            if (!is_null($senderid)) {
                                $autotemp = SmsTemplate::where('shop_id', $shop->id)->where('is_auto_sms', true)->where('temp_for', 'due_date_rem')->first();
                                if (!is_null($autotemp)) {
                                    $tempmessage = $autotemp->message;
                                    
                                    $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                                    $tnetreturn = ($debt->return_amount-$debt->return_discount)+$debt->return_tax;
                                    $netsales_amount = $tnetsales-$tnetreturn;
                                    $amount_due += $netsales_amount-$sale->sale_amount_paid;
                                    $invoice_no = sprintf('%04d', $sale->invoice_no);
                                    $due_date = date('d, M Y', strtotime($sale->due_date));

                                    $sms = str_replace('{customer_name}', $sale->name, $tempmessage);
                                    $sms1 = str_replace('{sale_date}', date('d, M Y', strtotime($sale->time_created)), $sms);
                                    $sms2 = str_replace('{due_date}', $due_date, $sms1);
                                    $sms3 = str_replace('{invoice_no}', $invoice_no, $sms2);
                                    $msg = str_replace('{amount_due}', number_format($amount_due, 2, '.', ','), $sms3);
                                    if (!is_null($this->formattedNumber($sale->phone))) {
                                        $phone = $this->formattedNumber($sale->phone);
        
                                        dispatch(new SendSMS($smsacc->username, $smsacc->password, $senderid->name, $phone, $message));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function formattedNumber($number)
    {
        if ($this->validate_mobile($number)) {
            $num = preg_replace('/^(?:\+?255|0)?/','255', $number);
            return $num;
        } else{
            return null;
        }
    }

    public function validate_mobile($mobile)
    {   
        $mobile = str_replace(' ', '', $mobile);
        $mobile = preg_replace('/^(?:\+?255|0)?/','0', $mobile);
        return preg_match('/^[0-9]{10}+$/', $mobile);
    }
}
