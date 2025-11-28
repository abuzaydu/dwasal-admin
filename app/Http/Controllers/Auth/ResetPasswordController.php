<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CustomPassReset;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function verifyCode(Request $request)
    {
        $user = User::find($request['id']);
        $reset_code = CustomPassReset::where('code', $request['code'])->first();
        if (is_null($reset_code)) {
            return redirect('password/reset')->with('error', 'Code you entered in invalid. Please check and Re-Try by providing Your Email or Models Number');
        }else{
            $reset_code->is_expired = true;
            $reset_code->save();
            $message = 'Verified. Change password now.';
            $token = $request->route()->parameter('token');
            return view('auth.passwords.reset', compact('user', 'token'))->with('message', $message, 'user', $user);
        }
    }

    public function resetPass(Request $request)
    {
        $user = User::find($request['user_id']);
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            $errors = (new ValidationException($validator))->errors();
            $error = array();
            foreach ($errors as $key => $value) {
                array_push($error, $value);
            }
            return view('auth.passwords.reset', compact('user'))->with('message', $error);
        }else{
          $user->password = bcrypt($request['password']);
          $user->save();
          return redirect('login')->with('message', 'Hi '.$user->name.', Your Password has been reseted successfuly. Login now');
        }
      
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'password' => 'required|string|min:6|confirmed',
        ]);
    }
}
