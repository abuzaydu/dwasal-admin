<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CustomPassReset;
use App\Jobs\SendSMS;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function forgotPass(Request $request)
    {
        $user = User::where('phone', $request['phone'])->orWhere('email', $request['phone'])->first();
        if (is_null($user)) {
            return redirect()->back()->with('error', 'Mobile Number or Email does not exit in our records.');
        }else{

            $code_exists = CustomPassReset::where('phone', $user->phone)->where('is_expired', false)->first();
            if (is_null($code_exists)) {
                $code = $this->generatePIN(6);
                try {
                    $reset_code = new CustomPassReset();
                    $reset_code->phone = $user->phone;
                    $reset_code->code = $code;
                    $reset_code->is_expired = false;
                    $reset_code->save();

                    if ($reset_code) {
                        if (!is_null($this->formattedNumber($reset_code->phone))) {
                            $phone = $this->formattedNumber($reset_code->phone);
                            $message = 'Your Password reset Code is : '.$reset_code->code;
                            dispatch(new SendSMS('NJPFSS', 'njp@2025', 'PETOINFO', $phone, $message));
                            $success = 'Successfully checked. Enter the code you received.';
                            return view('auth.passwords.verify-code', compact('user', 'success'));
                        }else{
                            return redirect()->back()->with('error', 'It seems your number is not valid');
                        }
                    }else{

                    }
                } catch (Exception $e) {}
            }else{
                $success = 'Successfully checked. Enter the code you received.';
              return view('auth.passwords.verify-code', compact('user', 'success'));
            }
        }
    }

    public function generatePIN($digits = 4)
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
}
