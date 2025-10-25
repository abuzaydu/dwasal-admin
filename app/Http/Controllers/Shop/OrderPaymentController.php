<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Selcom\ApigwClient\Client;
use Log;
use Auth;
use App\Models\PaymentDetail;
use App\Models\OrderDetail;

class OrderPaymentController extends Controller
{
    protected $token = 'ukjKrMRH>Le\XF0|gk$lOw3%MZ!}K!=8';

    protected $apiKey = 'SMARTMAU-AB7376TR0FB21';
    protected $apiSecret = '45E9C1-WD76F4-TF87TY-GH1976-RFDE07-4ED306';
    protected $baseUrl = "https://apigw.selcommobile.com";
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        Log::info($request);
        $order = OrderDetail::find($request['order_id']);
        if (!is_null($order)) {
            $user = Auth::user();
            if (!is_null($this->formattedNumber($request->phone))) {
                $msisdn = $this->formattedNumber($request->phone);

                $amount = ($order->total+$order->delivery_cost+$order->tax_amount);

                if ($amount > 0) {
                    $paytrans = PaymentDetail::where('order_detail_id', $order->id)->first();
                    if (is_null($paytrans)) {
                        $paytrans = new PaymentDetail();
                        $paytrans->order_detail_id = $order->id;
                        $paytrans->amount = $amount;
                        $paytrans->msisdn = $msisdn;
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
                                "order_id" => $order->uuid,
                                "buyer_email" => $user->email,
                                "buyer_name" => $user->first_name.' '.$user->last_name,
                                "buyer_phone" => $msisdn,
                                "amount" =>  $paytrans->amount,
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
                                Log::info($response['reference']);
                                $paytrans->reference = $response['reference'];
                                $paytrans->resultcode = $response['resultcode'];
                                $paytrans->payment_token = $response['data'][0]['payment_token'];
                                $paytrans->payment_gateway_url = $response['data'][0]['payment_gateway_url'];
                                $paytrans->save();
                                return $this->proccesOrderWallet($paytrans);
                            }else{
                                return response()->json(['error' => true, 'msg' =>  $response['result'].' - '.$response['message']]);
                            }
                        }else{
                            return response()->json(['error' => true, 'msg' => 'Payment Initialize Failed']);
                        }
                    }else{
                        return $this->proccesOrderWallet($paytrans);
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


    public function proccesOrderWallet($paytrans)
    {
        $order = OrderDetail::where('id', $paytrans->order_detail_id)->select('uuid')->first();
        $client = new \GuzzleHttp\Client();
        $url = "https://smartmauzo.ovaltechtz.com/api/process-order-wallet";
        $data = array(
            'form_params' => array(
                'reference' => $paytrans->reference,
                'order_id' => $order->uuid,
                'msisdn' => $paytrans->msisdn
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
        return response()->json(['error' => false, 'requid' => $order->uuid, 'msg' => 'Payment procces in progress. Please check your phone and enter your PIN to complete']);
    }

    public function checkOrderStatus(Request $request)
    {
        $order = OrderDetail::find($request['order_id']);
        if (!is_null($order)) {
                
            $client = new \GuzzleHttp\Client();
            $url = "https://smartmauzo.ovaltechtz.com/api/check-pay-order-status";
            $data = array(
                'form_params' => array(
                    "order_id" => $order->uuid
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
                    $paytrans = PaymentDetail::where('order_detail_id', $order->id)->first();
                    $paytrans->creation_date = $response['data'][0]['creation_date'];
                    $paytrans->transid = $response['data'][0]['transid'];
                    $paytrans->reference = $response['data'][0]['reference'];
                    $paytrans->status = $response['data'][0]['payment_status'];
                    $paytrans->channel = $response['data'][0]['channel'];
                    $paytrans->save();
                    if ($paytrans->status == 'COMPLETED') {
                        $paytrans->is_real = true;
                        $paytrans->save();

                        $order->status = 'Awaiting Fulfillment';
                        $order->save();
                        
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

    public function initiateOrder(Request $request)
    {  
        Log::info($request);
        $order = OrderDetail::find($request['order_id']);
        if (!is_null($order)) {
            $user = Auth::user();
            if (!is_null($this->formattedNumber($request->phone))) {
                $msisdn = $this->formattedNumber($request->phone);

                $amount = ($order->total+$order->delivery_cost+$order->tax_amount);

                if ($amount > 0) {
                    
                    $paytrans = PaymentDetail::where('order_detail_id', $order->id)->first();
                    if (is_null($paytrans)) {
                        $paytrans = new PaymentDetail();
                        $paytrans->order_detail_id = $order->id;
                        $paytrans->amount = $amount;
                        $paytrans->msisdn = $msisdn;
                        $paytrans->save();

                        // $client = new Client($this->baseUrl, $this->apiKey, $this->apiSecret);

                        // // data
                        // $orderArray = array(
                        //     "vendor" => "TILL60045358",
                        //     "order_id" => $order->uuid,
                        //     "buyer_email" => $user->email,
                        //     "buyer_name" => $user->first_name.' '.$user->last_name,
                        //     "buyer_userid" => "",
                        //     "buyer_phone" => $paytrans->msisdn,
                        //     "gateway_buyer_uuid" => "",
                        //     "amount"=>  $paytrans->amount,
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
                                "req_uid" => $order->uuid,
                                "buyer_email" => $user->email,
                                "buyer_name" => $user->first_name.' '.$user->last_name,
                                "buyer_phone" => $paytrans->msisdn,
                                "amount"=>  $paytrans->amount,
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
                                if (!is_null($paytrans->payment_gateway_url)) {
                                    $payurl = $this->base64UrlDecode($paytrans->payment_gateway_url);
                                    return response()->json(['error' => false, 'payurl' => $payurl, 'requid' => $order->uuid, 'msg' => $response['message']]);
                                }else{
                                    return response()->json(['error' => true, 'msg' => 'Failed to procces your payment please try again']);
                                }
                            }else{
                                return response()->json(['error' => true, 'msg' =>  $response['result'].' - '.$response['message']]);
                            }
                        }else{
                            return response()->json(['error' => true, 'msg' => 'Payment Initialize Failed']);
                        }
                    }else{

                        $payurl = $this->base64UrlDecode($paytrans->payment_gateway_url);
                        return response()->json(['error' => false, 'payurl' => $payurl, 'requid' => $order->uuid, 'msg' => 'Transaction Already created. Please complete the process']);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
}
