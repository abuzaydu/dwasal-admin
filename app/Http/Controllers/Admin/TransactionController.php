<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Selcom\ApigwClient\Client;
use Session;
use Auth;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Payment;
use App\Models\ServiceCharge;
use App\Jobs\CheckExpiredShops;

class TransactionController extends Controller
{
    protected $token = 'ukjKrMRH>Le\XF0|gk$lOw3%MZ!}K!=8';

    protected $apiKey = 'SMARTMAU-AB7376TR0FB21';
    protected $apiSecret = '45E9C1-WD76F4-TF87TY-GH1976-RFDE07-4ED306';
    protected $baseUrl = "https://apigw.selcommobile.com";
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info($request);
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $user = Auth::user();
            if (!is_null($this->formattedNumber($request->phone))) {
                $msisdn = $this->formattedNumber($request->phone);

                $echarge = ServiceCharge::where('subscription_type_id', 1)->where('duration', 'Monthly')->first();
                $amount = $request->amount;

                if ($amount > 0 && ($amount % $echarge->initial_pay == 0)) {
                    
                    $paytrans = new Payment();
                    $paytrans->shop_id = $shop->id;
                    $paytrans->user_id = $user->id;
                    $paytrans->req_uid = substr(bin2hex(random_bytes(32)), 0, 8);
                    $paytrans->amount_paid = $amount;
                    $paytrans->phone_number = $msisdn;
                    $paytrans->code = $this->generateCode(6);
                    $paytrans->save();



                    // $client = new Client($this->baseUrl, $this->apiKey, $this->apiSecret);
                    // //order data
                    // $orderArray = array(
                    //     "vendor" => "TILL60045358",
                    //     "order_id" => $paytrans->req_uid,
                    //     "buyer_email" => $user->email,
                    //     "buyer_name" => $user->first_name." ".$user->last_name,
                    //     "buyer_phone" => $paytrans->phone_number,
                    //     "amount" =>  $paytrans->amount_paid,
                    //     "currency" => "TZS",
                    //     "buyer_remarks" => "None",
                    //     "merchant_remarks" => "None",
                    //     "no_of_items"=>  1
                    // );

                    // // path relatiive to base url
                    // $orderPath = "/v1/checkout/create-order-minimal";
                    // // create order
                    // $response = $client->postFunc($orderPath,$orderArray);

                    $client = new \GuzzleHttp\Client();
                    $url = "https://smartmauzo.ovaltechtz.com/api/payment-transactions";
                    $data = array(
                        'form_params' => array(
                            "order_id" => $paytrans->req_uid,
                            "buyer_email" => $user->email,
                            "buyer_name" => $user->first_name." ".$user->last_name,
                            "buyer_phone" => $paytrans->phone_number,
                            "amount" =>  $paytrans->amount_paid,
                        ),
                        'verify' => false,
                        'headers' => [
                            'Authorization' => 'Bearer '.$this->token,
                            'Accept' => 'application/json',
                        ],
                    );
                    
                    $req = $client->post($url,  $data);
                    $responseData = $req->getBody();
                    // Log::info($responseData);

                    if (!is_null($responseData)) {
                        $response = json_decode($responseData, true);
                        if ($response['resultcode'] == '000') {
                            Log::info($response['reference']);
                            $paytrans->reference = $response['reference'];
                            $paytrans->resultcode = $response['resultcode'];
                            $paytrans->payment_token = $response['data'][0]['payment_token'];
                            $paytrans->payment_gateway_url = $response['data'][0]['payment_gateway_url'];
                            $paytrans->save();
                            return $this->proccesOrderWallet($paytrans->reference, $paytrans->req_uid, $paytrans->phone_number);
                        }else{
                            return response()->json(['error' => true, 'msg' =>  $response['result'].' - '.$response['message']]);
                        }
                    }else{
                        return response()->json(['error' => true, 'msg' => 'Payment Initialize Failed']);
                    }
                }else{
                    return response()->json(['error' => true, 'msg' => 'You have entered an invalid amount']);
                }
            }else{
                return response()->json(['error' => true, 'msg' =>  'Mobile number is invalid']);
            }
        }else{
            return response()->json(['error' => true, 'msg' => 'Shop not found']);
        }
    }

    public function proccesOrderWallet($reference, $orderid, $msisdn)
    {
        $client = new \GuzzleHttp\Client();
        $url = "https://smartmauzo.ovaltechtz.com/api/process-order-wallet";
        $data = array(
            'form_params' => array(
                'reference' => $reference,
                'order_id' => $orderid,
                'msisdn' => $msisdn
            ),
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
                'Accept' => 'application/json',
            ],
        );
                    
        $req = $client->post($url,  $data);
        $response = $req->getBody();

        // Log::info($response);
        return response()->json(['error' => false, 'requid' => $orderid, 'msg' => 'Payment procces in progress. Please check your phone and enter your PIN to complete']);
    }


    public function initiateOrder(Request $request)
    {  
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $user = Auth::user();
            if (!is_null($this->formattedNumber($request->phone))) {
                $msisdn = $this->formattedNumber($request->phone);

                $amount = $request->amount;
                if ($amount > 0 && ($amount % 10000 == 0 || $amount % 40000 == 0)) {
                    
                    $paytrans = new Payment();
                    $paytrans->shop_id = $shop->id;
                    $paytrans->user_id = $user->id;
                    $paytrans->req_uid = substr(bin2hex(random_bytes(32)), 0, 8);
                    $paytrans->amount_paid = $amount;
                    $paytrans->phone_number = $msisdn;
                    $paytrans->code = $this->generateCode(6);
                    $paytrans->save();

                    // $client = new Client($this->baseUrl, $this->apiKey, $this->apiSecret);

                    // // data
                    // $orderArray = array(
                    //     "vendor" => "TILL60045358",
                    //     "order_id" => $paytrans->req_uid,
                    //     "buyer_email" => $user->email,
                    //     "buyer_name" => $user->first_name.' '.$user->last_name,
                    //     "buyer_userid" => "",
                    //     "buyer_phone" => $paytrans->phone_number,
                    //     "gateway_buyer_uuid" => "",
                    //     "amount"=>  $paytrans->amount_paid,
                    //     "currency" =>"TZS",
                    //     "payment_methods" => "ALL",
                    //     "redirect_url" => "",
                    //     "cancel_url" => "",
                    //     "webhook" => "",
                    //     "billing.firstname" => $request->firstname,
                    //     "billing.lastname" => $request->lastname,
                    //     "billing.address_1" => $request->address_1,
                    //     "billing.address_2" => "",
                    //     "billing.city" => $request->city,
                    //     "billing.state_or_region" => $request->state_or_region,
                    //     "billing.postcode_or_pobox" => $request->postcode_or_pobox,
                    //     "billing.country" => $request->country,
                    //     "billing.phone" => $request->phone,
                    //     "buyer_remarks"=>"None",
                    //     "merchant_remarks"=>"None",
                    //     "no_of_items"=>  1
                    // );

                    // // path relatiive to base url
                    // $orderPath = "/v1/checkout/create-order";

                    // // create order
                    // $response = $client->postFunc($orderPath,$orderArray);

                    $client = new \GuzzleHttp\Client();
                    $url = "https://smartmauzo.ovaltechtz.com/api/card-payment-transactions";
                    $data = array(
                        'form_params' => array(
                            "req_uid" => $paytrans->req_uid,
                            "buyer_email" => $user->email,
                            "buyer_name" => $user->first_name.' '.$user->last_name,
                            "buyer_phone" => $paytrans->phone_number,
                            "amount"=>  $paytrans->amount_paid,
                            "firstname" => $request->firstname,
                            "lastname" => $request->lastname,
                            "address_1" => $request->address_1,
                            "city" => $request->city,
                            "state_or_region" => $request->state_or_region,
                            "postcode_or_pobox" => $request->postcode_or_pobox,
                            "country" => $request->country,
                            "phone" => $request->phone,
                        ),
                        'verify' => false,
                        'headers' => [
                            'Authorization' => 'Bearer '.$this->token,
                            'Accept' => 'application/json',
                        ],
                    );
                    
                    $req = $client->post($url,  $data);
                    $responseData = $req->getBody();
                    Log::info($responseData);
                    if (!is_null($responseData)) {
                        $response = json_decode($responseData, true);
                        if ($response['resultcode'] == '000') {
                            $paytrans->reference = $response['reference'];
                            $paytrans->resultcode = $response['resultcode'];
                            $paytrans->payment_token = $response['data'][0]['payment_token'];
                            $paytrans->payment_gateway_url = $response['data'][0]['payment_gateway_url'];
                            $paytrans->save();
                            $payurl = $this->base64UrlDecode($paytrans->payment_gateway_url);
                            return response()->json(['error' => false, 'payurl' => $payurl, 'msg' => $response['message']]);
                        }else{
                            return response()->json(['error' => true, 'msg' =>  $response['result'].' - '.$response['message']]);
                        }
                    }else{
                        return response()->json(['error' => true, 'msg' => 'Payment Initialize Failed']);
                    }
                }else{
                    return response()->json(['error' => true, 'msg' => 'You have entered an invalid amount']);
                }
            }else{
                return response()->json(['error' => true, 'msg' =>  'Mobile number is invalid']);
            }
        }else{
            return response()->json(['error' => true, 'msg' => 'Shop not found']);
        }
    }

    public function checkPaymentStatus(Request $request)
    {
        $shop = Shop::find($request->id);
        if (!is_null($shop)) {
            $paytrans = Payment::where('ride_request_id', $shop->id)->first();
            if (!is_null($paytrans)) {
                // $paytrans->req_uid = $shop->req_uid;
                // $paytrans->save();
                if ($paytrans->status == 'COMPLETED') {
                    return response()->json(['success' => true, 'status' => $paytrans->status]);
                }elseif($paytrans->status == 'CANCELLED') {
                    return response()->json(['success' => true, 'status' => $paytrans->status]);
                }else{
                    $client = new Client($this->baseUrl, $this->apiKey, $this->apiSecret);
                    // data
                    $orderStatusArray = array("order_id" => $paytrans->req_uid);

                    // path relatiive to base url
                    $orderStatusPath = "/v1/checkout/order-status";

                    // get order status
                    $response = $client->getFunc($orderStatusPath,$orderStatusArray);
                    Log::info($response);
                    if (!is_null($response) && $response['resultcode'] == '000') {
                        $paytrans = Payment::where('req_uid', $response['data'][0]['order_id'])->first();
                        $paytrans->creation_date = $response['data'][0]['creation_date'];
                        $paytrans->transid = $response['data'][0]['transid'];
                        $paytrans->reference = $response['data'][0]['reference'];
                        $paytrans->status = $response['data'][0]['payment_status'];
                        $paytrans->channel = $response['data'][0]['channel'];
                        if ($paytrans->status == 'COMPLETED') {
                            $paytrans->verified_at = Carbon::now();
                            $paytrans->is_real = true;
                        }
                        $paytrans->save();

                        if ($paytrans->status == 'COMPLETED') {
                            $message = 'Hello '.$customer->first_name.',  Thank you for using our service. Welcome Again and Please rate your ride on app';
                            dispatch(new SendSMSJob($paytrans->msisdn, $message));
                        }
                        return response()->json(['success' => true, 'status' => $paytrans->status, 'resultcode' => $response['resultcode'], 'result' => $response['result'], 'msg' => $response['message']]);
                    }else{
                        return response()->json($response);
                    }
                }
            }else{
                return response()->json(['success' => false,'msg' => 'No Payment Transaction for this Ride yet']);
            }
        }else{
            return response()->json(['success' => false, 'msg' => 'Ride Request not found']);
        }
    }

    public function checkOrderStatus(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $url = "https://smartmauzo.ovaltechtz.com/api/check-pay-order-status";
        $data = array(
            'form_params' => array(
                "order_id" => $request->requid
            ),
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
                'Accept' => 'application/json',
            ],
        );
                    
        $req = $client->post($url,  $data);
        $responseData = $req->getBody();

        // Log::info($responseData);
        if (!is_null($responseData)) {
            $response = json_decode($responseData, true);
            if ($response['resultcode'] == '000') {
                // Log::info($response['data'][0]['reference']);
                $paytrans = Payment::where('req_uid', $response['data'][0]['order_id'])->first();
                $paytrans->creation_date = $response['data'][0]['creation_date'];
                $paytrans->transid = $response['data'][0]['transid'];
                $paytrans->reference = $response['data'][0]['reference'];
                $paytrans->status = $response['data'][0]['payment_status'];
                $paytrans->channel = $response['data'][0]['channel'];
                $paytrans->save();
                if ($paytrans->status == 'COMPLETED') {
                    $paytrans->is_real = true;
                    $paytrans->save();
                    $this->verify($paytrans);
                    return response()->json(['status' => $paytrans->status, 'msg' => $response['message'].' Status : '.$paytrans->status]);
                }else{
                    return response()->json(['status' => $paytrans->status, 'msg' => $response['message'].' Status : '.$paytrans->status]);
                }
            }else{
                return response()->json(['status' => $response['result'], 'msg' => $response['message']]);
            }
        }else{
            return response()->json(['msg' => 'Payment order not found']);
        }
    }

    public function cancelOrder(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $url = "https://smartmauzo.ovaltechtz.com/api/cancel-pay-order";
        $data = array(
            'form_params' => array(
                "order_id" => $request->requid
            ),
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
                'Accept' => 'application/json',
            ],
        );
                    
        $req = $client->post($url,  $data);
        $responseData = $req->getBody();

        // Log::info($responseData);
        $response = json_decode($responseData, true);
        if ($response['resultcode'] == '000') {
            $paytrans = Payment::where('req_uid', $requid)->first();
            $paytrans->status = 'CANCELLED';
            $paytrans->is_real = true;
            $paytrans->save();

            return response()->json(['resultcode' => $response['resultcode'], 'result' => $response['result'], 'msg' => $response['message']]);
        }else{
            return redirect()->back()->with(['resultcode' => $response['resultcode'], 'result' => $response['result'], 'msg' => $response['message']]);
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
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        Log::info($request);
        $token = $request->header('Authorization');
        if ($token == "Bearer ".$this->apikey) {
            $paytrans = Payment::where('reference', $request->reference)->first();
            if (!is_null($paytrans)) {
                $paytrans->is_notified = true;
                $paytrans->notified_at = Carbon::now();
                $paytrans->save();

                $result = ['reference' => $paytrans->reference, 'resultcode' => '000', 'result' => 'SUCCESS', 'message' => 'Transaction Received successful'];
                return response()->json($result);
            }else{
                $result = ['reference' => $reference, 'resultcode' => '010', 'result' => 'FAILED', 'message' => 'Reference does not match any Payments'];
                return response()->json($result);
            }
        }else{
            $result = ['reference' => $request['reference'], 'resultcode' => 413, 'result' => 'FAILED', 'message' => 'Wrong API key.'];
            return response()->json($result);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function generateCode($digits = 4)
    {
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while($i < $digits){
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
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

    
    function base64UrlDecode(string $base64Url): string
    {
        return base64_decode(strtr($base64Url, '-_', '+/'));
    }

    public function verify($payment)
    {
        $now = \Carbon\Carbon::now();
        $actime = \Carbon\Carbon::now();
        if (!is_null($payment)) {
            $shop = Shop::find($payment->shop_id);
            if ($shop->subscription_type_id == 1) {
                $echarge = ServiceCharge::where('subscription_type_id', 1)->where('duration', 'Monthly')->first();
                $months = 0;
                if ($payment->amount_paid % $echarge->initial_pay == 0) {
                    $months = $payment->amount_paid/$echarge->initial_pay;
                }

                $lastpay = Payment::where('shop_id', $shop->id)->where('amount_paid', '>', 0)->where('is_expired', 0)->first();
                $remdays = 0; $price_per_day = 0;

                if (!is_null($lastpay)) {
                    $activation_time = \Carbon\Carbon::parse($lastpay->activation_time);
                    $expire_date = \Carbon\Carbon::parse($lastpay->expire_date);

                    $numdays = $expire_date->diffInDays($activation_time);
                    $price_per_day = $lastpay->amount_paid/$numdays;
                    $remdays = $expire_date->diffInDays(\Carbon\Carbon::now());
                    
                    //Update Previous payment as expired
                    $lastpay->is_expired = true;
                    $lastpay->save();
                }

                if ($months == 1) {
                    $payment->period = "Monthly";
                    $payment->is_expired = false;
                    $payment->activation_time = $actime;
                    $payment->status = 'Activated';
                    $payment->expire_date = $now->addDays(31+$remdays);
                    $payment->save();
                }elseif ($months == 3) {
                    $payment->period = "Quarterly";
                    $payment->is_expired = false;
                    $payment->activation_time = $actime;
                    $payment->status = 'Activated';
                    $payment->expire_date = $now->addDays(92+$remdays);
                    $payment->save();
                }elseif ($months == 6) {
                    $payment->period = "Semi Annually";
                    $payment->is_expired = false;
                    $payment->activation_time = $actime;
                    $payment->status = 'Activated';
                    $payment->expire_date = $now->addDays(183+$remdays);
                    $payment->save();
                }elseif ($months == 12) {
                    $payment->period = "Annually";
                    $payment->is_expired = false;
                    $payment->activation_time = $actime;
                    $payment->status = 'Activated';
                    $payment->expire_date = $now->addDays(366+$remdays);
                    $payment->save();  
                }else{
                    $payment->period = "Uncategorized";
                    $payment->is_expired = false;
                    $payment->activation_time = $actime;
                    $payment->status = 'Activated';
                    $payment->expire_date = $now->addDays($months*30.5+$remdays);
                    $payment->save();
                }

                // Mail::to($user->email)->send(new ReceiptMail($user, $shop. $payment));

                // $this->sendVFDReceipt($payment, $months, $shop->subscription_type_id);
                Session::put('expired', $payment->is_expired);
                $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                // return redirect('home')->with('success', $message);
            }
        }else{
            Log::info('Payment order not found');
        }
    }

     public function sendVFDReceipt($payment, $qty, $stypeid)
    {
        if (!$payment->is_real) {

            $shop = Shop::find(1869);
            $user = $shop->users()->first();
            $now = Carbon::now();
            if (!is_null($shop)) {
                $reginfo = EfdmsRegInfo::where('shop_id', $shop->id)->first();
                if (!is_null($reginfo)) {

                    $customer = Shop::find($payment->shop_id);
                    $zreport = EfdmsZReport::where('shop_id', $shop->id)->where('status', 'Not Submitted')->first();
                    $znum = null;
                    if (!is_null($zreport)) {
                        $znum = $zreport->znum;
                    }else{
                        $lastzr_sub = EfdmsZReport::where('shop_id', $shop->id)->latest()->first();
                        if (!is_null($lastzr_sub)) {
                            $znum = $lastzr_sub->znum+1;
                        }else{
                            $znum = 1;
                        }

                        $znumber = date('Ymd', strtotime($now));
                        $zreport = new EfdmsZReport();
                        $zreport->shop_id = $shop->id;
                        $zreport->date = $now;
                        $zreport->tin = $reginfo->tin;
                        $zreport->vrn = $reginfo->vrn;
                        $zreport->taxoffice = $reginfo->taxoffice;
                        $zreport->regid = $reginfo->regid;
                        $zreport->znum = $znum;
                        $zreport->znumber = $znumber;
                        $zreport->efdserial = $reginfo->serial;
                        $zreport->registration_date = date('Y-m-d', strtotime($reginfo->reg_date));
                        $zreport->user = $reginfo->uin;
                        $zreport->simimsi = "WEBAPI";
                        $zreport->fwversion = '3.0';
                        $zreport->fwchecksum = 'WEBAPI';
                        $zreport->save();
                    }
                    $lastrct = EfdmsRctInfo::where('shop_id', $shop->id)->latest()->first();
                    $rctnum = 1;
                    if (!is_null($lastrct)) {
                        $rctnum = $lastrct->rctnum+1;
                    }
                    $ldc = EfdmsRctInfo::where('shop_id', $shop->id)->whereDate('created_at', Carbon::today())->count();
                    $lgc = EfdmsRctInfo::where('shop_id', $shop->id)->count();

                    $stype = SubscriptionType::find($stypeid);
                    $taxcode = Taxcode::where('value', $reginfo->taxcode)->first();
                    $rectvnum = $reginfo->receiptcode.''.($lgc+1);
                    $rctinfo = new EfdmsRctInfo();
                    $rctinfo->shop_id = $shop->id;
                    $rctinfo->user_id = $user->id;
                    $rctinfo->an_sale_id = null;
                    $rctinfo->efdms_z_report_id = $zreport->id;
                    $rctinfo->date = $now;
                    $rctinfo->tin = $reginfo->tin;
                    $rctinfo->regid = $reginfo->regid;
                    $rctinfo->efdserial = $reginfo->serial;
                    $rctinfo->custidtype = 6;
                    $rctinfo->custid = null;
                    $rctinfo->custname = $customer->display_name;
                    $rctinfo->rctnum = $rctnum;
                    $rctinfo->mobilenum = $customer->phone;
                    $rctinfo->dc = $ldc+1;
                    $rctinfo->gc = $ldc+1;
                    $rctinfo->znum = $zreport->znumber;
                    $rctinfo->rctvnum = $rectvnum;
                    $rctinfo->total_tax_excl = $payment->amount_paid;
                    $rctinfo->total_tax_incl = $payment->amount_paid;
                    $rctinfo->discount = 0;
                    $rctinfo->save();

                    $code_a_netamount = 0; $code_a_taxamount = 0;
                    $code_b_netamount = 0; $code_b_taxamount = 0;
                    $code_c_netamount = $payment->amount_paid; $code_c_taxamount = 0;

                    $rctitem = new EfdmsRctItem();
                    $rctitem->efdms_rct_info_id = $rctinfo->id;
                    $rctitem->item_code = $stype->id;
                    $rctitem->desc = $stype->title;
                    $rctitem->qty = $qty;
                    $rctitem->taxcode = $taxcode->id;
                    $rctitem->amt = $payment->amount_paid;
                    $rctitem->save();

                    $cashpayment = 0;
                    $chequepayment = 0;
                    $ccardpayment = 0;
                    $emoneypayment = 0;
                    $invoicepayment = 0;
                    if (strlen($payment->reference) == 10) {
                        $emoneypayment = $payment->amount_paid;
                    }else{
                        $ccardpayment = $payment->amount_paid;
                    }
                    // Payment Types
                    $pmttypes = array(
                        ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'CHEQUE',  'pmtamount' => $chequepayment],
                        ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'CCARD', 'pmtamount' => $ccardpayment],
                        ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'CASH', 'pmtamount' => $cashpayment],
                        ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'EMONEY', 'pmtamount' => $emoneypayment],
                        ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'INVOICE', 'pmtamount' => $invoicepayment]
                    );

                    foreach ($pmttypes as $key => $pmt) {
                        EfdmsRctPayment::create($pmt);
                    }

                    // VAT Totals
                    $vattotals = array(
                        ['efdms_rct_info_id' => $rctinfo->id, 'vatrate' => 'A',  'netamount' => $code_a_netamount, 'taxamount' => $code_a_taxamount],
                        ['efdms_rct_info_id' => $rctinfo->id, 'vatrate' => 'B', 'netamount' => $code_b_netamount, 'taxamount' => $code_b_taxamount],
                        ['efdms_rct_info_id' => $rctinfo->id, 'vatrate' => 'C', 'netamount' => $code_c_netamount, 'taxamount' => $code_c_taxamount]
                    );

                    foreach ($vattotals as $key => $vatt) {
                        EfdmsRctVatTotal::create($vatt);
                    }

                    $this->sendReceiptReq($rctinfo, $reginfo);
                }else{
                    Log::info('Sorry!. Your registration for VFD not Acknowledged yet or Something went wrong please check registration status and try again');
                }
            }
        }else{
            Log::info('This is a Dummy Payment, No Receipt Issued to TRA');
        }
    }

    public function sendReceiptReq($rctinfo, $reginfo)
    {
        $token = $reginfo->access_token;
        $routingKey = $reginfo->routing_key;
        $rctitems = EfdmsRctItem::where('efdms_rct_info_id', $rctinfo->id)->get();

        $xmldoc =  "<?xml version='1.0' encoding='UTF-8'?>";
        $efdms_open = "<EFDMS>";
        $efdms_close = "</EFDMS>";
        $efdms_signatureOpen="<EFDMSSIGNATURE>";
        $efdms_signatureClose="</EFDMSSIGNATURE>";

        $rctitemsxmlopen = '<ITEMS>';
        $rctitemsxmlclose = '</ITEMS>'; 
        $xmlitems = '';
        foreach ($rctitems as $key => $rctitem) {
            $xmlitems.= '<ITEM><ID>'.$rctitem->item_code.'</ID><DESC>'.$rctitem->desc.'</DESC><QTY>'.$rctitem->qty.'</QTY><TAXCODE>'.$rctitem->taxcode.'</TAXCODE><AMT>'.$rctitem->amt.'</AMT></ITEM>';
        }

        $rctitemsxml = $rctitemsxmlopen.$xmlitems.$rctitemsxmlclose;

        $xmlpayments = '';
        $rctpayments = EfdmsRctPayment::where('efdms_rct_info_id', $rctinfo->id)->get();
        foreach ($rctpayments as $key => $rctp) {
            $xmlpayments.= '<PMTTYPE>'.$rctp->pmttype.'</PMTTYPE><PMTAMOUNT>'.$rctp->pmtamount.'</PMTAMOUNT>';
        }

        $xmlvattotals = '';
        $vattotals = EfdmsRctVatTotal::where('efdms_rct_info_id', $rctinfo->id)->get();
        foreach ($vattotals as $key => $vatt) {
            $xmlvattotals.= '<VATRATE>'.$vatt->vattotals.'</VATRATE><NETTAMOUNT>'.$vatt->netamount.'</NETTAMOUNT><TAXAMOUNT>'.$vatt->taxamount.'</TAXAMOUNT>';
        }

        $payloadData = '<RCT><DATE>'.date('Y-m-d', strtotime($rctinfo->date)).'</DATE><TIME>'.date('H:i:s', strtotime($rctinfo->date)).'</TIME><TIN>'.$rctinfo->tin.'</TIN><REGID>'.$rctinfo->regid.'</REGID><EFDSERIAL>'.$rctinfo->efdserial.'</EFDSERIAL><CUSTIDTYPE>'.$rctinfo->custidtype.'</CUSTIDTYPE><CUSTID>'.$rctinfo->custid.'</CUSTID><CUSTNAME>'.$rctinfo->custname.'</CUSTNAME><MOBILENUM>'.$rctinfo->mobilenum.'</MOBILENUM><RCTNUM>'.$rctinfo->rctnum.'</RCTNUM><DC>'.$rctinfo->dc.'</DC><GC>'.$rctinfo->gc.'</GC><ZNUM>'.$rctinfo->znum.'</ZNUM><RCTVNUM>'.$rctinfo->rctvnum.'</RCTVNUM>'.$rctitemsxml.'<TOTALS><TOTALTAXEXCL>'.$rctinfo->total_tax_excl.'</TOTALTAXEXCL><TOTALTAXINCL>'.$rctinfo->total_tax_incl.'</TOTALTAXINCL><DISCOUNT>'.$rctinfo->discount.'</DISCOUNT></TOTALS><PAYMENTS>'.$xmlpayments.'</PAYMENTS><VATTOTALS>'.$xmlvattotals.'</VATTOTALS></RCT>';

        $cert_store = file_get_contents(Storage::path('/public/'.$reginfo->file_path));
        $clientSignature = openssl_pkcs12_read($cert_store, $cert_info, decrypt($reginfo->cert_pass));
        
        $privateKey = $cert_info['pkey'];
        $publicKey = openssl_get_privatekey($privateKey);
        $certBase = base64_encode($reginfo->certbase);
        // Log::info($certBase);

        $rctsignature = $this->sign_payload_plain($payloadData, $publicKey);

        Log::info($rctsignature);
        $xmlbody = $xmldoc.$efdms_open.$payloadData.$efdms_signatureOpen.$rctsignature.$efdms_signatureClose.$efdms_close;
        Log::info($xmlbody);
        $client = new Client();
        // $urlReceipt = 'https://smartmauzo.ovaltechtz.com/efdms-rct-ack-infos';
        $urlReceipt = 'https://virtual.tra.go.tz/efdmsRctApi/api/efdmsRctInfo';

        $createRequest = new \GuzzleHttp\Psr7\Request(
            'POST', 
            $urlReceipt, 
            [
                'Content-type' => 'Application/xml',
                'Routing-Key' => $routingKey,
                'Cert-Serial' => $certBase,
                'Client' => 'WEBAPI',
                'Authorization' => 'Bearer '.$token
            ],
            $xmlbody
        );
        $rctinfo->status = 'Submitted';
        $rctinfo->save();

        $response = $client->send($createRequest);
    
        $respxmlObject = simplexml_load_string($response->getBody());
        $respjson = json_encode($respxmlObject);
        $respphpDataArray = json_decode($respjson, true); 

        Log::info($respphpDataArray);
        if ($respphpDataArray['RCTACK']['ACKCODE'] == 0) {
            $rctinfo->ack_date = $respphpDataArray['RCTACK']['DATE'].' '.$respphpDataArray['RCTACK']['TIME'];
            $rctinfo->ackcode = $respphpDataArray['RCTACK']['ACKCODE'];
            $rctinfo->ackmsg = $respphpDataArray['RCTACK']['ACKMSG'];
            $rctinfo->is_acknowledged = true;
            $rctinfo->save();
        }else{
            $rctinfo->ack_date = $respphpDataArray['RCTACK']['DATE'].' '.$respphpDataArray['RCTACK']['TIME'];
            $rctinfo->ackcode = $respphpDataArray['RCTACK']['ACKCODE'];
            $rctinfo->ackmsg = $respphpDataArray['RCTACK']['ACKMSG'];
            $rctinfo->save();
        }
    }

    public function checkExpiredShops()
    {
        dispatch(new CheckExpiredShops());
    }
}
