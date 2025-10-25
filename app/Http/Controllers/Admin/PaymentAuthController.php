<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use DateTimeImmutable;
use App\Models\Shop;
use App\Models\PaymentAuth;
use App\Models\Account;
use Log;

class PaymentAuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Payment Accounts';
        $title = 'Payment Accounts';
        $payauths = PaymentAuth::join('shops', 'shops.id', '=', 'payment_auths.shop_id')->select('payment_auths.id as id', 'name', 'merchant_msisdn', 'username', 'passhint')->get();
        $shops = Shop::where('is_warehouse', false)->select('id', 'name', 'mobile')->get();
        return view('admin.api-credentials.index', compact('page', 'title', 'shops', 'payauths'));
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
        $payauth = PaymentAuth::where('shop_id', $request['shop_id'])->first();
        if (is_null($payauth)) {
            $payauth = new PaymentAuth();
            $payauth->shop_id = $request['shop_id'];
            $payauth->merchant_msisdn = $request['merchant_msisdn'];
            $payauth->username = $request['username'];
            $payauth->password = bcrypt($request['password']);
            $payauth->passhint = $request['password'];
            $payauth->save();

            $acc = new Account();
            $acc->shop_id = $payauth->shop_id;
            $acc->payment_auth_id = $payauth->id;
            $acc->type = $request['type'];
            $acc->bank_name = $request['bank_name'];
            $acc->account_number = $request['account_number'];
            $acc->account_name = $request['account_name'];
            $acc->save();

            return redirect('admin/payment-auths')->with('success', 'Auth Account created successfully');
        }else{
            return redirect()->back()->with('info', 'Payment Auth Account already created');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Payment API Auth Details';
        $title = 'Payment API Auth Details';
        $payauth = PaymentAuth::find(decrypt($id));
        if (!is_null($payauth)) {
            $shop = Shop::find($payauth->shop_id);
            $acc = Account::where('payment_auth_id', $payauth->id)->first();

            //AMI Secret Key;
            // $secret_Key  = 'KbPeShVmYq3t6w9z$C&F)J@NcQfTjWnZr4u7x!A%D*G-KaPdSgVkXp2s5v8y/B?E';
            $secret_Key = 'NHz<}pz7khFJIvn9d|ng@ZzOjpT2Iu+A2fNNIhn0ym(U%}?4Z02)5V\{I*V|@mzE';

            $date   = new DateTimeImmutable();
            $iat = $date->getTimestamp();
            $expire_at     = $date->modify('+9460800 minutes')->getTimestamp();      // Add 60 seconds
            $iss = "airtel_africa";
            $sub = "airtel_africa";
            $username = $payauth->username;
            $payload = array(
                'txnId' => $payauth->id,
            );
            $request_data = [
                'iat'  => $iat,         // Issued at: time when the token was generated
                'iss'  => $iss,
                'sub' => $sub,                   // Issuer
                'nbf'  => $iat,         // Not before
                'exp'  => $expire_at,                         // Expire
                'userName' => $username,
                'Payload' => $payload
            ];
            
            if (is_null($payauth->access_token)) {
                Log::info($iat);
                Log::info($expire_at);
                Log::info($expire_at-$iat);
                
                $token = JWT::encode(
                    $request_data,
                    $secret_Key,
                    'HS512'
                );
                $payauth->access_token = $token;
                $payauth->save();
            }

            return view('admin.api-credentials.show', compact('page', 'title', 'payauth', 'shop', 'acc'));
        }else{
            return redirect()->back()->with('error', 'Item not found');
        }
    }

    public function createNewToken(string $id)
    {
        $payauth = PaymentAuth::find(decrypt($id));
        if (!is_null($payauth)) {
            $shop = Shop::find($payauth->shop_id);
            $acc = Account::where('payment_auth_id', $payauth->id)->first();

            //AMI Secret Key;
            // $secret_Key  = 'KbPeShVmYq3t6w9z$C&F)J@NcQfTjWnZr4u7x!A%D*G-KaPdSgVkXp2s5v8y/B?E';
            $secret_Key = 'NHz<}pz7khFJIvn9d|ng@ZzOjpT2Iu+A2fNNIhn0ym(U%}?4Z02)5V\{I*V|@mzE';

            $date   = new DateTimeImmutable();
            $iat = $date->getTimestamp();
            $expire_at     = $date->modify('+9460800 minutes')->getTimestamp();      // Add 60 seconds
            $iss = "airtel_africa";
            $sub = "airtel_africa";
            $username = $payauth->username;
            // Retrieved from filtered POST data
            $payload = array(
                'txnId' => $payauth->id,
            );
            $request_data = [
                'iat'  => $iat,         // Issued at: time when the token was generated
                'iss'  => $iss,
                'sub' => $sub,                   // Issuer
                'nbf'  => $iat,         // Not before
                'exp'  => $expire_at,                         // Expire
                'userName' => $username,
                'Payload' => $payload
            ];
            
            // if (is_null($payauth->access_token)) {
                Log::info($iat);
                Log::info($expire_at);
                Log::info($expire_at-$iat);
                
                $token = JWT::encode(
                    $request_data,
                    $secret_Key,
                    'HS512'
                );
                $payauth->access_token = $token;
                $payauth->save();
            // }

            return redirect()->route('payment-auths.show', encrypt($payauth->id))->with('success', 'New Token Created successfully');
        }else{
            return redirect()->back()->with('error', 'Item not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Payment API Auth';
        $title = 'Edit Payment API Auth';
        $payauth = PaymentAuth::find(decrypt($id));
        if (!is_null($payauth)) {
            $acc = Account::where('payment_auth_id', $payauth->id)->first();
            $shops = Shop::where('is_warehouse', false)->select('id', 'name', 'mobile')->get();
            return view('admin.api-credentials.edit', compact('page', 'title', 'shops', 'payauth', 'acc'));
        }else{
            return redirect()->back()->with('error', 'Item not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payauth = PaymentAuth::find(decrypt($id));
        if (!is_null($payauth)) {
            $payauth->shop_id = $request['shop_id'];
            $payauth->merchant_msisdn = $request['merchant_msisdn'];
            $payauth->username = $request['username'];
            $payauth->password = bcrypt($request['password']);
            $payauth->passhint = $request['password'];
            $payauth->save();

            $acc = Account::where('payment_auth_id', $payauth->id)->first();
            if (!is_null($acc)) {
                $acc->type = $request['type'];
                $acc->bank_name = $request['bank_name'];
                $acc->account_number = $request['account_number'];
                $acc->account_name = $request['account_name'];
                $acc->save();
            }else{
                $acc = new Account();
                $acc->shop_id = $payauth->shop_id;
                $acc->payment_auth_id = $payauth->id;
                $acc->type = $request['type'];
                $acc->bank_name = $request['bank_name'];
                $acc->account_number = $request['account_number'];
                $acc->account_name = $request['account_name'];
                $acc->save();
            }

            return redirect('admin/payment-auths')->with('success', 'Auth Account updated successfully');
        }else{
            return redirect()->back()->with('error', 'Item not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payauth = PaymentAuth::find(decrypt($id));
        if (!is_null($payauth)) {
            $acc = Account::where('payment_auth_id', $payauth->id)->first();
            if (!is_null($acc)) {
                // $acc->payment_auth_id = null;
                // $acc->save();
                $acc->delete();
            }
            $payauth->delete();

            return redirect('admin/payment-auths')->with('success', 'Auth Account deleted successfully');
        }else{
            return redirect()->back()->with('error', 'Item not found');
        }
    }

    public function generateKey()
    {
        return $this->generateRandomString(64);
    }

    protected function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+{}[]|\<>?/-=';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
