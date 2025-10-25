<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Log;
use Session;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Models\Customer;
use App\Models\AnSale;
use App\Models\SmsResponseLog;
use App\Models\SmsAccount;
use App\Models\Supplier;
use App\Models\Invoice;
use App\Models\SmsSetting;

class SmsTemplateController extends Controller
{
    
    private $shop;
    private $start;
    private $end;
    private $range;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $page = 'SMS Notification & Templates';
        $title = 'SMS Notification & Templates';
        $title_sw = 'Arifa za SMS';
        $shop = Shop::find(Session::get('shop_id'));
        $sms_templates = SmsTemplate::where('shop_id', $shop->id)->get();
        $smsacc = SmsAccount::where('shop_id', $shop->id)->first();

        $senderids = null;
        if (!is_null($smsacc)) {
            $senderids = $smsacc->senderIds()->get();
        } 

        $smssetting = SmsSetting::where('shop_id', $shop->id)->first();
        if (is_null($smssetting)) {
            $smssetting = new SmsSetting();
            $smssetting->shop_id = $shop->id;
            $smssetting->save();
        }

        $tempuses = array(
            "sale" => 'Recording Sales',
            "passed_due" => 'Reminding Invoices passed due date',
            "due_date_rem" => 'Reminding Invoice payments due date',
            "cust_pay" => 'Recording Customer payment',
            "supp_pay" => 'Recording Supplier payment',
            "exp_date" => 'Reminding Expiration Dates',
            "reorder" => 'Reminding Stock reaching Re-order point'
        );

        return view('sms.index', compact('page', 'title', 'title_sw', 'sms_templates', 'senderids', 'smssetting', 'tempuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->shop = Shop::find(Session::get('shop_id'));
        $message = $request['message'];
         if (!empty($request['title'])) {
            $is_auto_sms = false;
            if (!empty($request['is_auto_sms'])) {
                $is_auto_sms = $request['is_auto_sms'];
            }
            $temp = new SmsTemplate();
            $temp->shop_id = $this->shop->id;
            $temp->title = $request['title'];
            $temp->message = $message;
            $temp->is_auto_sms = $is_auto_sms;
            $temp->temp_for = $request['temp_for'];
            $temp->save();
        }
   
        $numbers = array();
        if (!empty($request['customers'])) {  
            if ($request['customers'] == 'all') {
                $customers = Customer::where('shop_id', $this->shop->id)->whereNotNull('phone')->select('phone', 'country_code')->get();
            }elseif ($request['customers'] == '15-30') {
                $this->end = Carbon::now()->subDays(15);
                $this->start = Carbon::now()->subDays(30);
                $customers = AnSale::where('an_sales.shop_id', $this->shop->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Unpaid')->orWhere(function($query){
                    $query->where('an_sales.shop_id', $this->shop->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Partially Paid');
                })->join('customers', 'customers.id', '=', 'an_sales.customer_id')->whereNotNull('phone')->select('customers.phone as phone', 'customers.country_code as country_code')->get();
            }elseif ($request['customers'] == '31-60') {
                $this->end = Carbon::now()->subDays(31);
                $this->start = Carbon::now()->subDays(60);
                $customers = AnSale::where('an_sales.shop_id', $this->shop->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Unpaid')->orWhere(function($query){
                    $query->where('an_sales.shop_id', $this->shop->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Partially Paid');
                })->join('customers', 'customers.id', '=', 'an_sales.customer_id')->whereNotNull('phone')->select('customers.phone as phone', 'customers.country_code as country_code')->get();
            }elseif ($request['customers'] == '61-90') {
                $this->end = Carbon::now()->subDays(61);
                $this->start = Carbon::now()->subDays(90);
                $customers = AnSale::where('an_sales.shop_id', $this->shop->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Unpaid')->orWhere(function($query){
                    $query->where('an_sales.shop_id', $this->shop->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Partially Paid');
                })->join('customers', 'customers.id', '=', 'an_sales.customer_id')->whereNotNull('phone')->select('customers.phone as phone', 'customers.country_code as country_code')->get();
            }elseif ($request['customers'] == '91-180') {
                $this->end = Carbon::now()->subDays(91);
                $this->start = Carbon::now()->subDays(180);
                $customers = AnSale::where('an_sales.shop_id', $this->shop->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Unpaid')->orWhere(function($query){
                    $query->where('an_sales.shop_id', $this->shop->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Partially Paid');
                })->join('customers', 'customers.id', '=', 'an_sales.customer_id')->whereNotNull('phone')->select('customers.phone as phone', 'customers.country_code as country_code')->get();
            }elseif ($request['customers'] == '180+') {
                $this->range = Carbon::now()->subDays(180);
                $customers = AnSale::where('an_sales.shop_id', $this->shop->id)->where('an_sales.time_created', '<=', $this->range)->where('an_sales.status', 'Unpaid')->orWhere(function($query){
                    $query->where('an_sales.shop_id', $this->shop->id)->where('an_sales.time_created', '<=', $this->range)->where('an_sales.status', 'Partially Paid');
                })->join('customers', 'customers.id', '=', 'an_sales.customer_id')->whereNotNull('phone')->select('customers.phone as phone', 'customers.country_code as country_code')->get();
            }else{
                $customers = AnSale::where('an_sales.id', $request['customers'])->join('customers', 'customers.id', '=', 'an_sales.customer_id')->whereNotNull('phone')->select('customers.name as name', 'customers.phone as phone', 'customers.country_code as country_code', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount',  'an_sales.sale_amount_paid as amount_paid', 'an_sales.adjustment as adjustment')->get();
            }

            if (!empty($request['customer_id'])) { 
                $customers = Customer::where('id', $request['customer_id'])->whereNotNull('phone')->select('phone', 'country_code')->get();
            }

            if (!empty($request['supplier_id'])) {
                $customers = Supplier::where('id', $request['supplier_id'])->whereNotNull('contact_no')->select('contact_no as phone', 'country_code')->get();
            }

            foreach ($customers as $key => $customer) {
                if (!is_null($customer->phone)) {
                    if (!is_null($this->formattedNumber($customer->phone))) {
                        $phone = $this->formattedNumber($customer->phone);
                        array_push($numbers, $phone);
                    }else{
                        Log::info($customer->phone.' is not valid mobile number');
                    }
                }
            }
        }elseif (!empty($request['phone'])) {
            $numbers = [$request['phone']];
        }

        if (count($numbers) > 0) {
            $smsacc = SmsAccount::where('shop_id', $this->shop->id)->first();
            if (!is_null($smsacc)) {
                        
                $token = '8b49c1406246765709bfdbaa6b8a9232';
                $sender = $request['sender'];
                $client = new \GuzzleHttp\Client();
                $url = "https://ovalbsms.co.tz/api/send-sms";
                // $url = "http://localhost/OBS/public/api/send-sms";
                $data = array(
                    'form_params' => array(
                        'username' => $smsacc->username,
                        'password' => $smsacc->password,
                        'sender' => $sender,
                        'receiver' => $numbers,
                        'message' => $message,
                    ),
                    'verify' => false,
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                        'Accept' => 'application/json',
                    ],
                );
                $req = $client->post($url,  $data);
                $response = $req->getBody();
                $result = json_decode($response, true);
                Log::info($result);

                if ($result['status'] == 'OK') {
                    return redirect()->back()->with('success', $result['msg']);
                }else{
                    return redirect()->back()->with('error', $result['msg']);
                }
            }else{
                return redirect()->back()->with('info', 'Account not connected to SMS service');
            }
        }else{
            return redirect()->back()->with('info', 'No customer with valid mobile number');
        }
    }

    public function dynamic(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $orgmessage = $request['message'];
        if (!empty($request['title'])) {
            $is_auto_sms = false;
            if (!empty($request['is_auto_sms'])) {
                $is_auto_sms = $request['is_auto_sms'];
            }
            $temp = new SmsTemplate();
            $temp->shop_id = $shop->id;
            $temp->title = $request['title'];
            $temp->message = $orgmessage;
            $temp->is_auto_sms = $is_auto_sms;
            $temp->temp_for = $request['temp_for'];
            $temp->save();
        }

        if (!empty($request['customers'])) {
                
            $numbers = array();
            if ($request['customers'] == 'all') {
                $customers = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('name', 'phone',  'an_sales.time_created as sale_date', 'invoice_no', 'due_date',
                \DB::raw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) as amount'))->get();
            }elseif ($request['customers'] == '15-30') {
                $end = Carbon::now()->subDays(15);
                $start = Carbon::now()->subDays(30);
                $customers = AnSale::where('an_sales.shop_id', $shop->id)->whereBetween('an_sales.time_created', [$start, $end])->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('name', 'phone',  'an_sales.time_created as sale_date', 'invoice_no', 'due_date',
                \DB::raw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) as amount'))->get();
            }elseif ($request['customers'] == '31-60') {
                $end = Carbon::now()->subDays(31);
                $start = Carbon::now()->subDays(60);
                $customers = AnSale::where('an_sales.shop_id', $shop->id)->whereBetween('an_sales.time_created', [$start, $end])->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('name', 'phone',  'an_sales.time_created as sale_date', 'invoice_no', 'due_date',
                \DB::raw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) as amount'))->get();
            }elseif ($request['customers'] == '61-90') {
                $end = Carbon::now()->subDays(61);
                $start = Carbon::now()->subDays(90);
                $customers = AnSale::where('an_sales.shop_id', $shop->id)->whereBetween('an_sales.time_created', [$start, $end])->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('name', 'phone',  'an_sales.time_created as sale_date', 'invoice_no', 'due_date',
                \DB::raw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) as amount'))->get();
            }elseif ($request['customers'] == '91-180') {
                $end = Carbon::now()->subDays(91);
                $start = Carbon::now()->subDays(180);
                $customers = AnSale::where('an_sales.shop_id', $shop->id)->whereBetween('an_sales.time_created', [$start, $end])->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('name', 'phone',  'an_sales.time_created as sale_date', 'invoice_no', 'due_date',
                \DB::raw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) as amount'))->get();
            }elseif ($request['customers'] == '180+') {
                $range = Carbon::now()->subDays(180);
                $customers = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.time_created', '<=', $range)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('name', 'phone',  'an_sales.time_created as sale_date', 'invoice_no', 'due_date',
                \DB::raw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) as amount'))->get();
                // return $customers;
            }

            $amount_due = 0;
            foreach ($customers as $key => $customer) {
                if (!is_null($customer->phone)) {
                    if (!is_null($this->formattedNumber($customer->phone))) {
                        $phone = $this->formattedNumber($customer->phone);
                        $invoice_no = sprintf('%06d', $customer->invoice_no);
                        $due_date = date('d, M Y', strtotime($customer->due_date));
                        
                        $amount_due = $customer->amount;
                        $sms = str_replace('{customer_name}', $customer->name, $orgmessage);
                        $sms1 = str_replace('{invoice_date}', date('d, M Y', strtotime($customer->sale_date)), $sms);
                        $sms2 = str_replace('{due_date}', $due_date, $sms1);
                        $sms3 = str_replace('{invoice_no}', $invoice_no, $sms2);
                        $msg = str_replace('{amount_due}', number_format($amount_due), $sms3);   

                        $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
                        if (!is_null($smsacc)) {
                                
                            $token = '8b49c1406246765709bfdbaa6b8a9232';
                            $sender = $request['sender'];
                            $client = new \GuzzleHttp\Client();
                            $url = "https://ovalbsms.co.tz/api/send-sms";
                            // $url = "http://localhost/OBS/public/api/send-sms";
                            $data = array(
                                'form_params' => array(
                                    'username' => $smsacc->username,
                                    'password' => $smsacc->password,
                                    'sender' => $sender,
                                    'receiver' => array($phone),
                                    'message' => $msg,
                                ),
                                'verify' => false,
                                'headers' => [
                                    'Authorization' => 'Bearer '.$token,
                                    'Accept' => 'application/json',
                                ],
                            );
                            $req = $client->post($url,  $data);
                            $response = $req->getBody();
                            $result = json_decode($response, true);
                            Log::info($result);

                            if ($result['status'] == 'OK') {
                                Log::info($result['msg']);
                            }else{
                                Log::info($result['msg']);
                            }
                        }else{
                            Log::info('Account not connected to SMS service');
                        }
                    }else{
                        Log::info($customer->phone.' is not valid mobile number');
                    }
                }
            }
            return redirect()->back()->with('success', 'Done.');
        }else{
            return redirect()->back()->with('success', 'Template created successfully');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {   
        $page = 'Edit SMS Template';
        $title = 'Edit SMS Template';
        $title_sw = 'Hariri Kiolezo cha SMS';
        $temp = SmsTemplate::find(decrypt($id));

        $tempuses = array(
            "sale" => 'Recording Sales',
            "passed_due" => 'Reminding Invoices passed due date',
            "due_date_rem" => 'Reminding Invoice payments due date',
            "cust_pay" => 'Recording Customer payment',
            "supp_pay" => 'Recording Supplier payment',
            "exp_date" => 'Reminding Expiration Dates',
            "reorder" => 'Reminding Stock reaching Re-order point'
        );
        return view('sms.edit', compact('page', 'title', 'title_sw', 'temp', 'tempuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $temp = SmsTemplate::find(decrypt($id));
        $temp->title = $request['title'];
        $temp->message = $request['message'];
        $temp->is_auto_sms = $request['is_auto_sms'];
        $temp->temp_for = $request['temp_for'];
        $temp->save();

        return redirect('sms-notifications')->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $temp = SmsTemplate::find(decrypt($id));
        if (!is_null($temp)) {
            $temp->delete();
        }
        return redirect('sms-notifications')->with('success', 'SMS template deleted successfully'); 
    }

    public function getSetting()
    {
        $page = 'SMS Notification';
        $title = 'Auto SMS Settings';
        $title_sw = 'Mipangilio ys SMS za Auto';
        $shop = Shop::find(Session::get('shop_id'));
        $setting = SmsSetting::where('shop_id', $shop->id)->first();
        if (is_null($setting)) {
            $setting = new SmsSetting();
            $setting->shop_id = $shop->id;
            $setting->save();
        }

        return view('sms.settings', compact('page', 'title', 'title_sw', 'setting'));
    }

    public function settings(Request $request)
    {
        $setting = SmsSetting::find($request['id']);
        $setting->days_before_expire = $request['days_before_expire'];
        $setting->days_before_due = $request['days_before_due'];
        $setting->repeat = $request['repeat'];
        $setting->save();

        return redirect()->back()->with('success', 'Setings updated successfully');
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

