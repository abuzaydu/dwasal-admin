<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCharge;
use App\Models\SubscriptionType;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\User;
use App\Mail\ReceiptMail;
use Illuminate\Support\Facades\Mail;
use App\Models\SmsResponseLog;
use App\Models\Module;
use Session;
use Auth;
use DB;

class VerifyPaymentController extends Controller
{
     public function index()
    {
        $page = 'Payment verification';
        $title = 'Make Payments';
        $title_sw = 'Thibitisha malipo';

        $eservice = ServiceCharge::where('subscription_type_id', 1)->orderBy('initial_pay', 'desc')->get();
        $pservice = ServiceCharge::where('subscription_type_id', 2)->orderBy('initial_pay', 'desc')->get();
        return view('sales.payments.verify-payment', compact('page', 'title', 'title_sw', 'eservice', 'pservice'));
    }

    public function modulePayment($id)
    {
        $page = 'Payment verification';
        $title = 'Verify Module Payments';
        $title_sw = 'Thibitisha malipo ya Moduli';
        $module = Module::find(decrypt($id));

        return view('sales.payments.verify-module-payment', compact('page', 'title', 'title_sw', 'module'));
    }

    public function verify(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();

        $now = \Carbon\Carbon::now();
        $actime = \Carbon\Carbon::now();

        // return $shop->subscription_type_id;
        $payment = Payment::where('code', $request['code'])->where('is_expired', true)->where('expire_date', null)->first();

        if (!is_null($payment)) {
            if ($shop->subscription_type_id == 1) {
                
                $premserv = ServiceCharge::where('subscription_type_id', 1)->where('duration', 'Monthly')->first();

                $prevpay = Payment::where('shop_id', $shop->id)->where('amount_paid', '>', 0)->where('is_expired', 0)->first();
                $remdays = 0; $price_per_day = 0; $balance = 0;
                if (!is_null($prevpay)) {
                    $activation_time = \Carbon\Carbon::parse($prevpay->activation_time);
                    $expire_date = \Carbon\Carbon::parse($prevpay->expire_date);

                    $numdays = $expire_date->diffInDays($activation_time);
                    $price_per_day = $prevpay->amount_paid/$numdays;
                    $remdays = $expire_date->diffInDays(\Carbon\Carbon::now());
                    $balance = $remdays*$price_per_day;
                    
                    //Update Previous payment as expired
                    $prevpay->is_expired = true;
                    $prevpay->save();
                }

                if (!is_null($premserv)) {
                    $premperday = $premserv->initial_pay/30.5;
                    $blcdays = $balance/$premperday;
                    $months = $payment->amount_paid/$premserv->initial_pay;

                    if ($months == 0) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Trial Days";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(3);
                        $payment->subscr_type = $premserv->type;
                        $payment->save();
                        Session::put('expired', $payment->is_expired);
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect('home')->with('success', $message);    
                    }elseif ($months == 1) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Monthly";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(31+$blcdays);
                        $payment->subscr_type = $premserv->type;
                        $payment->save();
                            
                        Session::put('expired', $payment->is_expired);
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect('home')->with('success', $message);            
                    }elseif ($months == 3) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Quarterly";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(92+$blcdays);
                        $payment->subscr_type = $premserv->type;
                        $payment->save();
                            
                        Session::put('expired', $payment->is_expired);
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect('home')->with('success', $message);
                    }elseif ($months == 6) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Semi Annually";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(183+$blcdays);
                        $payment->subscr_type = $premserv->type;
                        $payment->save();
                        
                        // $this->sendSMS($user, $payment);
                        Session::put('expired', $payment->is_expired);
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect('home')->with('success', $message);
                    }elseif ($months == 12) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Annually";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(366+$blcdays);
                        $payment->subscr_type = $premserv->type;
                        $payment->save();
                            
                        // $this->sendSMS($user, $payment);
                        Session::put('expired', $payment->is_expired);
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect('home')->with('success', $message);
                    }else {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Uncategorized";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays($months*30.5+$blcdays);
                        $payment->subscr_type = $premserv->type;
                        $payment->save();
                            
                        Session::put('expired', $payment->is_expired);
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect('home')->with('success', $message);
                    }
                }
            }
        }else{

            $msg_error = 'Samahani Code uliyoingiza haiendani na yeyote katika rekodi zetu. Tafadhali angalia Code vizuri na ujaribu tena.';
            $msg_error_en = 'Sorry the Code you entered does not match any of our records. Please check the Code properly and try again.';

            return redirect('verify-payment')->with('msg_error', $msg_error)->with('msg_error_en', $msg_error_en);
        }
    }

    public function verifyModulePayment(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = User::find(Session::get('user_id'));

        $now = \Carbon\Carbon::now();
        $actime = \Carbon\Carbon::now();

        // return $shop->subscription_type_id;
        $payment = Payment::where('code', $request['code'])->where('is_expired', true)->where('expire_date', null)->first();

        if (!is_null($payment)) {
            $module = Module::find($request['module_id']);
            if (!is_null($module)) {
                
                $months = 0;
                if ($payment->amount_paid % $module->price == 0) {
                    $months = $payment->amount_paid/$module->price;
                }
                
                $prevpay = Payment::where('shop_id', $shop->id)->where('amount_paid', '>', 0)->where('is_expired', 0)->where('module', $module->id)->first();
                $remdays = 0; $price_per_day = 0; $balance = 0;
                if (!is_null($prevpay)) {
                    $activation_time = \Carbon\Carbon::parse($prevpay->activation_time);
                    $expire_date = \Carbon\Carbon::parse($prevpay->expire_date);

                    $numdays = $expire_date->diffInDays($activation_time);
                    $price_per_day = $prevpay->amount_paid/$numdays;
                    $remdays = $expire_date->diffInDays(\Carbon\Carbon::now());
                    $balance = $remdays*$price_per_day;
                    
                    //Update Previous payment as expired
                    $prevpay->is_expired = true;
                    $prevpay->save();
                }

                if (!is_null($module)) {
                    $modperday = $module->price/30.5;
                    $blcdays = $balance/$modperday;
                    $months = $payment->amount_paid/$module->price;

                    if ($months == 0) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Trial Days";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(3);
                        $payment->subscr_type = $shop->subscription_type_id;
                        $payment->is_for_module = true;
                        $payment->module = $module->id;
                        $payment->save();
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect()->back()->with('success', $message);    
                    }elseif ($months == 1) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Monthly";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(31+$blcdays);
                        $payment->subscr_type = $shop->subscription_type_id;
                        $payment->is_for_module = true;
                        $payment->module = $module->id;
                        $payment->save();
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect()->back()->with('success', $message);         
                    }elseif ($months == 3) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Quarterly";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(92+$blcdays);
                        $payment->subscr_type = $shop->subscription_type_id;
                        $payment->is_for_module = true;
                        $payment->module = $module->id;
                        $payment->save();
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect()->back()->with('success', $message); 
                    }elseif ($months == 6) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Semi Annually";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(183+$blcdays);
                        $payment->subscr_type = $shop->subscription_type_id;
                        $payment->is_for_module = true;
                        $payment->module = $module->id;
                        $payment->save();
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect()->back()->with('success', $message); 
                    }elseif ($months == 12) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Annually";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(366+$blcdays);
                        $payment->subscr_type = $shop->subscription_type_id;
                        $payment->is_for_module = true;
                        $payment->module = $module->id;
                        $payment->save();
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect()->back()->with('success', $message); 
                    }else {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Uncategorized";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays($months*30.5+$blcdays);
                        $payment->subscr_type = $shop->subscription_type_id;
                        $payment->is_for_module = true;
                        $payment->module = $module->id;
                        $payment->save();
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect()->back()->with('success', $message); 
                    }
                }
            }
        }else{

            $msg_error = 'Samahani Code uliyoingiza haiendani na yeyote katika rekodi zetu. Tafadhali angalia Code vizuri na ujaribu tena.';
            $msg_error_en = 'Sorry the Code you entered does not match any of our records. Please check the Code properly and try again.';

            return redirect('verify-payment')->with('msg_error', $msg_error)->with('msg_error_en', $msg_error_en);
        }
    }


    public function sendSMS($user, $payment)
    {
        $n = $user->phone;
        //The default country code if the recipient's is unknown:
        $default_country_code  = '255';

        //Remove any parentheses and the numbers they contain:
        $n = preg_replace("/\([0-9]+?\)/", "", $n);

        //Strip spaces and non-numeric characters:
        $n = preg_replace("/[^0-9]/", "", $n);

        //Strip out leading zeros:
        $n = ltrim($n, '0');

        $pfx = $default_country_code;
        //Check if the number doesn't already start with the correct dialling code:
        if ( !preg_match('/^'.$pfx.'/', $n)  ) {
            $phone = $pfx.$n;
        }


        $message = '';
        if($payment->period == 'Annually'){
            $message = 'Hongera!%20Umezawadiwa%20OFA%20ya%20MIEZI%203%20bure%20baada%20ya%20kulipia%20huduma%20ya%20SmartMauzo%20kwa%20MWAKA%20MZIMA.%20Malipo%20yako%20yatamalizika%20muda%20wake%20tarehe%20'.date('d-m-Y%20H:i:s', strtotime($payment->expire_date)).'.%20Endelea%20kufurahia%20huduma%20zetu.%20SmartMauzo%20"Simu%20Yako,%20Mauzo%20Yako"';
        }else{
            $message = 'Hongera!%20Umezawadiwa%20OFA%20ya%20MWEZI%201%20bure%20baada%20ya%20kulipia%20huduma%20ya%20SmartMauzo%20kwa%20MIEZI%206.%20Malipo%20yako%20yatamalizika%20muda%20wake%20tarehe%20'.date('d-m-Y%20H:i:s', strtotime($payment->expire_date)).'.%20Endelea%20kufurahia%20huduma%20zetu.%20SmartMauzo%20"Simu%20Yako,%20Mauzo%20Yako"';
        }

        $phones = array($phone, $payment->phone_number);

        for ($i=0; $i < count($phones); $i++) { 
            
            $api_url = 'https://gw.selcommobile.com:8443/bin/send.json?USERNAME=OTTL&PASSWORD=26072018!!&DESTADDR='.$phones[$i].'&MESSAGE='.$message.'.';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $api_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",                        
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                // CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER => array(
                    // Set here requred headers
                    "content-type: application/json",
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
                
            $res = '';
            if ($err) {
                $res =  $err;
            } else {
                $res = (json_decode(json_encode($response), true));
            }
             
            SmsResponseLog::create([
                'response' => $res
            ]);    
        }

    }

    public function verifyMultiple(){
        $shop = Shop::find(Session::get('shop_id'));
        $user = User::find(Session::get('user_id'));
        $user_shop = $user->shops()->get();

        $page = 'Payment verification';
        $title = 'Verify Multiple Payments';
        $title_sw = 'Thibitisha malipo Mbalimbali';

        $modules = Module::all();
        $modules_join = DB::table('modules')->leftJoin('payments' , function($join){
            $shop = Shop::find(Session::get('shop_id'));
            $join->on('modules.id' , '='  , 'payments.module')
            ->where('payments.is_expired' , false)
            ->where('is_for_module', true)
            ->where('payments.shop_id' , $shop->id);
        })->get();

        $user_shops =  $user->shops()->leftJoin('payments' , function($join){
            $join->on('shops.id' , '='  , 'payments.shop_id')
            ->where('payments.is_expired' , false)
            ->where('is_for_module', false);
        })->groupBy('display_name')->get([
            'shops.display_name',
            'subscription_type_id',
            'payments.transaction_id',
            'is_expired'
        ]);

        

        return view('verify-multiple-payments', compact('page', 'title', 'title_sw', 'modules' , 'modules_join' , 'user_shops' , 'user_shop'));
    }

    public function verifyMultipleShop(Request $request){

   
        $shop = Shop::find($request['shop_id']);
        $this_shop = Shop::find(Session::get('shop_id'));
        $user = User::find(Session::get('user_id'));

        $now = \Carbon\Carbon::now();
        $actime = \Carbon\Carbon::now();

        // return $shop->subscription_type_id;
        $payment = Payment::where('code', $request['code'])->where('is_expired', true)->where('expire_date', null)->first();

        if (!is_null($payment)) {
            if ($shop->subscription_type_id == 1) {

                $months = 0;
                if ($payment->amount_paid % 65000 == 0) {
                    $months = $payment->amount_paid/65000;
                }elseif (($payment->amount_paid-2000) % 5000 == 0) {
                    $months = ($payment->amount_paid-2000)/5000;
                }else{
                    $months = 0;
                }
                
                $prevpay = Payment::where('shop_id', $shop->id)->where('amount_paid', '>', 0)->count();
                if ($prevpay > 0) {

                    Session::forget('expired');
                    if ($months == 0) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Trial Days";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(3);
                        $payment->save();
                        if (true) {
                            if($this_shop->id == $request['shop_id']){
                                Session::put('expired', $payment->is_expired);
                            }
                            $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                            return redirect('verify-multiple-payment')->with('success', $message);
                        }
                    }elseif ($months == 1) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Monthly";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(31);
                        $payment->save();
                        if (true) {
                            if($this_shop->id == $request['shop_id']){
                                Session::put('expired', $payment->is_expired);
                            }
                            $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                            return redirect('verify-multiple-payment')->with('success', $message);
                        }
                    }elseif ($months == 3) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Quarterly";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(92);
                        $payment->save();  
                        if (true) {
                            if($this_shop->id == $request['shop_id']){
                                Session::put('expired', $payment->is_expired);
                            }
                            $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                            return redirect('verify-multiple-payment')->with('success', $message);
                        }
                    }elseif ($months == 6) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Semi Annually";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(183);
                        $payment->save();
                        if (true) {
                            if($this_shop->id == $request['shop_id']){
                                Session::put('expired', $payment->is_expired);
                            }
                            // $this->sendSMS($user, $payment);
                            $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                            return redirect('verify-multiple-payment')->with('success', $message);
                        }
                    }elseif ($months == 12) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Annually";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(366);
                        $payment->save();  
                        if (true) {
                            if($this_shop->id == $request['shop_id']){
                                Session::put('expired', $payment->is_expired);
                            }
                            // $this->sendSMS($user, $payment);
                            $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                            return redirect('verify-multiple-payment')->with('success', $message);
                        }
                    }else{
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Uncategorized";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays($months*30.5);
                        $payment->save();
                        if (true) {
                            if($this_shop->id == $request['shop_id']){
                                Session::put('expired', $payment->is_expired);
                            }
                            $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                            return redirect('verify-multiple-payment')->with('success', $message);
                        }
                    }

                    // Mail::to($user->email)->send(new ReceiptMail($user, $shop. $payment));
                }else{
                    if ($payment->amount_paid % 65000 == 0) {
                        
                        Session::forget('expired');
                        if ($months == 0) {
                            $payment->user_id = $user->id;
                            $payment->shop_id = $shop->id;
                            $payment->period = "Trial Days";
                            $payment->is_expired = false;
                            $payment->activation_time = $actime;
                            $payment->status = 'Activated';
                            $payment->expire_date = $now->addDays(7);
                            $payment->save();
                            if (true) {
                                if($this_shop->id == $request['shop_id']){
                                    Session::put('expired', $payment->is_expired);
                                }
                                $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                                return redirect('verify-multiple-payment')->with('success', $message);
                            }
                        }elseif ($months == 1) {
                            $payment->user_id = $user->id;
                            $payment->shop_id = $shop->id;
                            $payment->period = "Monthly";
                            $payment->is_expired = false;
                            $payment->activation_time = $actime;
                            $payment->status = 'Activated';
                            $payment->expire_date = $now->addDays(31);
                            $payment->save();
                            if (true) {
                                if($this_shop->id == $request['shop_id']){
                                    Session::put('expired', $payment->is_expired);
                                }
                                $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                                return redirect('verify-multiple-payment')->with('success', $message);
                            }
                        }elseif ($months == 3) {
                            $payment->user_id = $user->id;
                            $payment->shop_id = $shop->id;
                            $payment->period = "Quarterly";
                            $payment->is_expired = false;
                            $payment->activation_time = $actime;
                            $payment->status = 'Activated';
                            $payment->expire_date = $now->addDays(92);
                            $payment->save(); 
                            if (true) {
                                if($this_shop->id == $request['shop_id']){
                                    Session::put('expired', $payment->is_expired);
                                }
                                $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                                return redirect('verify-multiple-payment')->with('success', $message);
                            } 
                        }elseif ($months == 6) {
                            $payment->user_id = $user->id;
                            $payment->shop_id = $shop->id;
                            $payment->period = "Semi Annually";
                            $payment->is_expired = false;
                            $payment->activation_time = $actime;
                            $payment->status = 'Activated';
                            $payment->expire_date = $now->addDays(183);
                            $payment->save();
                            if (true) {
                                if($this_shop->id == $request['shop_id']){
                                    Session::put('expired', $payment->is_expired);
                                }
                                // $this->sendSMS($user, $payment);
                                $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                                return redirect('verify-multiple-payment')->with('success', $message);
                            }
                        }elseif ($months == 12) {
                            $payment->user_id = $user->id;
                            $payment->shop_id = $shop->id;
                            $payment->period = "Annually";
                            $payment->is_expired = false;
                            $payment->activation_time = $actime;
                            $payment->status = 'Activated';
                            $payment->expire_date = $now->addDays(366);
                            $payment->save(); 
                            if (true) {
                                if($this_shop->id == $request['shop_id']){
                                    Session::put('expired', $payment->is_expired);
                                }
                                // $this->sendSMS($user, $payment);
                                $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                                return redirect('verify-multiple-payment')->with('success', $message);
                            } 
                        }else{
                            $payment->user_id = $user->id;
                            $payment->shop_id = $shop->id;
                            $payment->period = "Uncategorized";
                            $payment->is_expired = false;
                            $payment->activation_time = $actime;
                            $payment->status = 'Activated';
                            $payment->expire_date = $now->addDays($months*30.5);
                            $payment->save();
                            if (true) {
                                if($this_shop->id == $request['shop_id']){
                                    Session::put('expired', $payment->is_expired);
                                }
                                $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                                return redirect('verify-multiple-payment')->with('success', $message);
                            }
                        }

                        // Mail::to($user->email)->send(new ReceiptMail($user, $shop. $payment));
                    }else{
                        $add_payment = Payment::where('phone_number', $payment->phone_number)->where('amount_paid', '=', 2000)->where('status', '=', 'Received')->first();
                        if (!is_null($add_payment)) {
                            
                            Session::forget('expired');
                            if ($months == 1) {
                                $payment->user_id = $user->id;
                                $payment->shop_id = $shop->id;
                                $payment->period = "Monthly";
                                $payment->is_expired = false;
                                $payment->activation_time = $actime;
                                $payment->status = 'Activated';
                                $payment->expire_date = $now->addDays(31);
                                $payment->save();
                                $add_payment->status = 'Activated';
                                $add_payment->save();
                                if (true) {
                                    if($this_shop->id == $request['shop_id']){
                                    Session::put('expired', $payment->is_expired);
                                    }
                                    $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                                    return redirect('verify-multiple-payment')->with('success', $message);
                                }
                            }elseif ($months == 3) {
                                $payment->user_id = $user->id;
                                $payment->shop_id = $shop->id;
                                $payment->period = "Quarterly";
                                $payment->is_expired = false;
                                $payment->activation_time = $actime;
                                $payment->status = 'Activated';
                                $payment->expire_date = $now->addDays(92);
                                $payment->save();  
                                $add_payment->status = 'Activated';
                                $add_payment->save();
                                if (true) {
                                    if($this_shop->id == $request['shop_id']){
                                    Session::put('expired', $payment->is_expired);
                                    }
                                    $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                                    return redirect('verify-multiple-payment')->with('success', $message);
                                }
                            }elseif ($months == 6) {
                                $payment->user_id = $user->id;
                                $payment->shop_id = $shop->id;
                                $payment->period = "Semi Annually";
                                $payment->is_expired = false;
                                $payment->activation_time = $actime;
                                $payment->status = 'Activated';
                                $payment->expire_date = $now->addDays(183);
                                $payment->save();
                                $add_payment->status = 'Activated';
                                $add_payment->save();
                                if (true) {
                                    if($this_shop->id == $request['shop_id']){
                                         Session::put('expired', $payment->is_expired);
                                    }
                                    // $this->sendSMS($user, $payment);
                                    $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                                    return redirect('verify-multiple-payment')->with('success', $message);
                                }
                            }elseif ($months == 12) {
                                $payment->user_id = $user->id;
                                $payment->shop_id = $shop->id;
                                $payment->period = "Annually";
                                $payment->is_expired = false;
                                $payment->activation_time = $actime;
                                $payment->status = 'Activated';
                                $payment->expire_date = $now->addDays(366);
                                $payment->save(); 
                                $add_payment->status = 'Activated';
                                $add_payment->save(); 
                                if (true) {
                                    if($this_shop->id == $request['shop_id']){
                                        Session::put('expired', $payment->is_expired);
                                    }
                                    // $this->sendSMS($user, $payment);
                                    $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                                    return redirect('verify-multiple-payment')->with('success', $message);
                                }
                            }else{
                                $payment->user_id = $user->id;
                                $payment->shop_id = $shop->id;
                                $payment->period = "Uncategorized";
                                $payment->is_expired = false;
                                $payment->activation_time = $actime;
                                $payment->status = 'Activated';
                                $payment->expire_date = $now->addDays($months*30.5);
                                $payment->save();
                                $add_payment->status = 'Activated';
                                $add_payment->save();
                                if (true) {
                                    if($this_shop->id == $request['shop_id']){
                                     Session::put('expired', $payment->is_expired);
                                    }
                                    $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                                    return redirect('verify-multiple-payment')->with('success', $message);
                                }
                            }

                            // Mail::to($user->email)->send(new ReceiptMail($user, $shop. $payment));
                        }else{

                            $msg_error = 'Mteja mpendwa, Kiasi ulicholipa hakiendani na malipo ya awali ya huduma hii kwa biashara mpya. Tafadhali ongeza TZS 2000 kwa kutumia namba hiyohiyo uliyolipia kiasi cha mwanzo. Kisha ingiza tena code ulizotpokea kwenye SMS. Asante.';
                            $msg_error_en = 'Dear Customer, The amount you have paid does not match the initial payment of this service for a new business. Please add 2000 TZS by using the same number you paid for the original amount. Then re-enter the code you received on the SMS. Thank you.';
                            return redirect('verify-multiple-payment')->with('msg_error', $msg_error)->with('msg_error_en', $msg_error_en);
                        }
                    }
                }
            }else{

                $premserv = ServiceCharge::where('type', 2)->where('duration', 'Monthly')->first();

                $prevpay = Payment::where('shop_id', $shop->id)->where('amount_paid', '>', 0)->where('is_expired', 0)->first();
                $remdays = 0; $price_per_day = 0; $balance = 0;
                if (!is_null($prevpay)) {
                    $activation_time = \Carbon\Carbon::parse($prevpay->activation_time);
                    $expire_date = \Carbon\Carbon::parse($prevpay->expire_date);

                    $numdays = $expire_date->diffInDays($activation_time);
                    $price_per_day = $prevpay->amount_paid/$numdays;
                    $remdays = $expire_date->diffInDays(\Carbon\Carbon::now());
                    $balance = $remdays*$price_per_day;
                    
                    //Update Previous payment as expired
                    $prevpay->is_expired = true;
                    $prevpay->save();
                }

                if (!is_null($premserv)) {
                    $premperday = $premserv->initial_pay/30.5;
                    $blcdays = $balance/$premperday;
                    $months = $payment->amount_paid/$premserv->initial_pay;

                    if ($months == 0) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Trial Days";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(3);
                        $payment->subscr_type = $premserv->type;
                        $payment->save();
                        if($this_shop->id == $request['shop_id']){
                            Session::put('expired', $payment->is_expired);
                        }
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect('verify-multiple-payment')->with('success', $message);    
                    }elseif ($months == 1) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Monthly";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(31+$blcdays);
                        $payment->subscr_type = $premserv->type;
                        $payment->save();
                            
                        if($this_shop->id == $request['shop_id']) {
                            Session::put('expired', $payment->is_expired);
                        }
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect('verify-multiple-payment')->with('success', $message);            
                    }elseif ($months == 3) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Quarterly";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(92+$blcdays);
                        $payment->subscr_type = $premserv->type;
                        $payment->save();
                            
                        if($this_shop->id == $request['shop_id']) {
                            Session::put('expired', $payment->is_expired);
                        }
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect('verify-multiple-payment')->with('success', $message);
                    }elseif ($months == 6) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Semi Annually";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(183+$blcdays);
                        $payment->subscr_type = $premserv->type;
                        $payment->save();
                        
                        // $this->sendSMS($user, $payment);
                        if($this_shop->id == $request['shop_id']){
                            Session::put('expired', $payment->is_expired);
                        }
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect('verify-multiple-payment')->with('success', $message);
                    }elseif ($months == 12) {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Annually";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays(366+$blcdays);
                        $payment->subscr_type = $premserv->type;
                        $payment->save();
                            
                        // $this->sendSMS($user, $payment);
                        if($this_shop->id == $request['shop_id']){
                            Session::put('expired', $payment->is_expired);
                        }
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect('verify-multiple-payment')->with('success', $message);
                    }else {
                        $payment->user_id = $user->id;
                        $payment->shop_id = $shop->id;
                        $payment->period = "Uncategorized";
                        $payment->is_expired = false;
                        $payment->activation_time = $actime;
                        $payment->status = 'Activated';
                        $payment->expire_date = $now->addDays($months*30.5+$blcdays);
                        $payment->subscr_type = $premserv->type;
                        $payment->save();
                            
                        if($this_shop->id == $request['shop_id']){
                            Session::put('expired', $payment->is_expired);
                        }
                        $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                        return redirect('verify-multiple-payment')->with('success', $message);
                    }
                }
            }
        }else{

            $msg_error = 'Samahani Code uliyoingiza haiendani na yeyote katika rekodi zetu. Tafadhali angalia Code vizuri na ujaribu tena.';
            $msg_error_en = 'Sorry the Code you entered does not match any of our records. Please check the Code properly and try again.';

            return redirect('verify-multiple-payment')->with('msg_error', $msg_error)->with('msg_error_en', $msg_error_en);
        }
    }

    public function makePayment()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $subscription = SubscriptionType::find($shop->subscription_type_id);
        $charges = ServiceCharge::where('subscription_type_id', 1)->orderBy('initial_pay', 'desc')->get();
        $annual_std = ServiceCharge::where('duration', 'Annually')->where('subscription_type_id', 1)->first();
        $precharges = ServiceCharge::where('subscription_type_id', 2)->orderBy('initial_pay', 'desc')->get();
        $annual_pre = ServiceCharge::where('duration', 'Annually')->where('subscription_type_id', 2)->first();
        $shopcount = Shop::count();
        return view('payment', compact('shop', 'subscription', 'charges', 'annual_std', 'precharges', 'annual_pre', 'shopcount'));
    }
}
