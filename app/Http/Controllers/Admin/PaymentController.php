<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PaymentExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCharge;
use App\Models\Payment;
use App\Models\SmsResponseLog;
use App\Models\AgentCustomer;
use App\Models\User;
use \Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use App\Models\Shop;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Payments';
        $title = 'Payments';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');
        $searchTerm = $request->search_date;

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From ' .date('d-m-Y', strtotime($start)). ' T0 ' .date('d-m-Y', strtotime($end));

        if (!empty($request->search_key)) {
            $payments = Payment::where('phone_number', 'LIKE', '%'.$request->search_key.'%')->orderBy('created_at', 'desc')->get();
        }else{
            $payments = Payment::whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->get();
        }

        return view('admin.payments.index', compact('payments', 'page', 'title', 'is_post_query', 'start_date', 'end_date', 'duration','searchTerm'));
    }

    public function query(Request $request)
    {
        $data = Payment::select("phone_number as name")->where("phone_number", "LIKE", "%{$request->input('query')}%")->get();
        Log::info($data);

        return response()->json($data);
    }

    public function paymentsExport(Request $request, Excel $excel, $from, $to, $searchTerm, $id)
    {
        $now = Carbon::now();
        $start = null;
        $end = null;
        $searchTerm = $searchTerm;

        //check if user opted for date range
        
        if (!is_null($from)) {
            $start = date('Y-m-d H:i:s', strtotime($from . ' 00:00:00'));
            $end = date('Y-m-d H:i:s', strtotime($to . ' 23:59:59'));
        } else {
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
        }

        if($searchTerm == 'noterm'){
            $searchTerm = '';
        }
        
        // dd($start . $end);
        $payments = Payment::where('phone_number', 'LIKE', '%' . $searchTerm . '%')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        switch ($id) {
            case 'pdf':
                return $excel->download(new PaymentExport($payments), 'all_transactions.pdf');
            case 'excel':
                return $excel->download(new PaymentExport($payments), 'all_transactions.xlsx');
            case 'csv':
                return $excel->download(new PaymentExport($payments), 'all_transactions.csv');
            default:
                break;
        }
    }

    public function autocompleteSearch(Request $request)
    {
        $query = $request->get('query');
        $data = Payment::select('phone_number as name')->where('phone_number', 'LIKE', '%' . $query . '%')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($data);
    }

    public function activatedPayments(Request $request)
    {
        $page = 'Activated Payments';
        $title = 'Activated Payments';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');
        $searchTerm = $request->search_date;

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From ' .date('d-m-Y', strtotime($start)). ' T0 ' .date('d-m-Y', strtotime($end));

        $users = Payment::whereBetween('payments.created_at', [$start, $end])->where('is_real', true)->join('users', 'users.id', '=', 'payments.user_id')->join('shops', 'shops.id', '=', 'payments.shop_id')->select('shops.name as name', 'users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'payments.phone_number as phone_number', 'payments.reference as reference', 'payments.code as code', 'payments.amount_paid as amount_paid', 'payments.period as period', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired', 'payments.created_at as created_at')->orderBy('payments.created_at', 'desc')
            ->get();

        return view('admin.payments.activated', compact('users', 'page', 'title', 'is_post_query', 'start_date', 'end_date', 'duration'));
    }

    public function agentActivations(Request $request)
    {
        $page = 'Payments';
        $title = 'Activations By Agents';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');
        $searchTerm = $request->search_date;

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From ' .date('d-m-Y', strtotime($start)). ' T0 ' .date('d-m-Y', strtotime($end));

        $paybyagents = AgentCustomer::join('users', 'users.id', '=', 'agent_customers.user_id')->join('shop_user', 'shop_user.user_id', '=', 'users.id')->join('shops', 'shops.id', '=', 'shop_user.shop_id')->join('payments', 'payments.shop_id', '=', 'shops.id')->whereBetween('payments.created_at', [$start, $end])->where('is_real', true)->select('agent_customers.agent_id as agent_id', 'agent_customers.agent_code as agent_code', 'shops.name as shopname', 'shops.id as shopid', 'users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'payments.phone_number as phone_number', 'payments.reference as reference', 'payments.code as code', 'payments.amount_paid as amount_paid', 'payments.period as period', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired', 'payments.created_at as created_at')
            ->paginate(10)->withQueryString();

        return view('admin.payments.agent', compact('paybyagents', 'page', 'title', 'is_post_query', 'start_date', 'end_date', 'duration'));
    }


    public function activationsOnce(Request $request)
    {
        $page = 'Payments';
        $title = 'Payments';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');
        $searchTerm = $request->search_date;

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From ' .date('d-m-Y', strtotime($start)). ' T0 ' .date('d-m-Y', strtotime($end));

        $shops = Payment::whereBetween('payments.created_at', [$start, $end])
            ->where('is_real', true)
            ->join('shops', 'shops.id', '=', 'payments.shop_id')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->select('shops.name as shopname', 'shops.id as shopid', 'users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'payments.phone_number as phone_number', 'payments.reference as reference', 'payments.code as code', 'payments.amount_paid as amount_paid', 'payments.period as period', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired', 'payments.created_at as created_at')->groupBy('shopid')->orderBy('payments.created_at', 'desc')
            ->paginate(10)->withQueryString();


        return view('admin.payments.once', compact('shops', 'page', 'title', 'is_post_query', 'start_date', 'end_date', 'duration'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'Payments';
        $title = 'Payments';
        return view('admin.payments.new', compact('page', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $api_key = 'WtUCp2KDdPNzcnCPjHhtJAxYDZl3NVuu';
        if ($request['api_key'] == $api_key) {
            $code = $this->generatePIN(6);
            
            $echarge = ServiceCharge::where('subscription_type_id', 1)->where('duration', 'Monthly')->first();
            $msisdn = $request['phone_number'];
            $amount = $request['amount_paid'];
            if ($amount > 0 && ($amount % $echarge->initial_pay == 0)) {
                $paytrans = new Payment();
                $paytrans->req_uid = substr(bin2hex(random_bytes(32)), 0, 8);
                $paytrans->amount_paid = $amount;
                $paytrans->phone_number = $msisdn;
                $paytrans->code = $code;
                $paytrans->is_real = $request['is_real'];
                $paytrans->period = "Uncategorized";
                $paytrans->status = "Received";
                $paytrans->save();
                // $this->response = curl_exec($curl);
                $this->sendSMS($code, $msisdn);
            } else {
                $result = ['status' => 'Failed', 'code' => 412, 'message' => 'Kiasi ulicholipia hakiendani na bei yeyote ya huduma hii. Tafadhali ingiza kiasi kilichoainishwa kwenye App yetu.'];
                return json_encode($result);
            }

            $message = 'The payment was created successfully';
            return redirect('admin/payments')->with('success', $message);
        } else {
            $message = 'Wrong API key.';
            return redirect('admin/payments')->with('error', $message);
        }
    }


    public function sendSMS($code, $mobile)
    {
        $message = 'Thibitisha malipo yako ya SmartMauzo kwa kutumia Code hii : ' . $code;
        $numbers = [$mobile];
        $token = '8b49c1406246765709bfdbaa6b8a9232';
        $client = new \GuzzleHttp\Client();
        $url = "https://ovalbsms.co.tz/api/send-sms";
        $data = array(
            'form_params' => array(
                'username' => 'OTTL',
                'password' => 'ottl@2020',
                'sender' => 'SmartMauzo',
                'receiver' => $numbers,
                'message' => $message,
            ),
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        );
        $req = $client->post($url,  $data);
        $response = $req->getBody();
    }

    public function generatePIN($digits = 4)
    {
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while ($i < $digits) {
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Payments';
        $title = 'Edit Payment';
        $payment = Payment::find(decrypt($id));

        return view('admin.payments.edit', compact('page', 'title', 'payment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::find($id);
        $payment->amount_paid = $request['amount_paid'];
        $payment->created_at = $request['created_at'];
        $payment->activation_time = $request['activation_time'];
        $payment->expire_date = $request['expire_date'];
        $payment->status = $request['status'];
        $payment->is_expired = $request['is_expired'];
        $payment->save();

        return redirect('admin/payments')->with('success', 'Payment updated successful');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



    public function search()
    {
        return view('search');
    }

    public function activateShopForm()
    {
        $page = 'Activate Shop';
        $title = 'Activate Shop';
        $shops = Shop::whereNull('parent_shop_id')->join('companies', 'companies.id', '=', 'shops.company_id')->select('shops.id as id', 'shops.name as name', 'companies.name as company', 'shops.created_at as created_at')->get();
        return view('admin.payments.activate-form', compact('page', 'title', 'shops'));
    }

    public function activateShop(Request $request)
    {
        $shop = Shop::find($request['shop_id']);
        $user = $shop->users()->first();

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
                    }

                    $message = 'Congratulations!. Your payment verification was done successfully. Enjoy our Smart Mauzo service!.';
                    return redirect('admin/payments')->with('success', $message);
                }
            }
        }else{

            $msg_error = 'Sorry the Code you entered does not match any of our records Or Already used. Please check the Code properly and try again.';

            return redirect()->back()->with('error', $msg_error);
        }
    }
}
