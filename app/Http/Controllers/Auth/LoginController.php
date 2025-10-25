<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Log;
use Session;
use App\Models\UserTheme;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }


    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        if (is_numeric($request->get('email'))) {
            return ['phone' => $request->get('email'), 'password' => $request->get('password')];
        }
        return $request->only($this->username(), 'password');
    }


    public function authenticated($request, $user)
    {

        if ($user->roles()->count() == 0 || !$user->is_active) {
            return view('errors.401');
        }else{
            $usertheme = UserTheme::where('user_id', $user->id)->first();
            if (!is_null($usertheme)) {
                Session::put('theme_style', $usertheme->theme_style);
                Session::put('headercolor', $usertheme->header_color);
                Session::put('sidebarcolor', $usertheme->sidebar_background);
            }
            
            $company = $user->companies()->wherePivot('is_default', true)->first();
            if (!is_null($company)) {
                Session::put('company_id', $company->id);
            }else{
                $company = $user->companies()->first();
                $company->pivot->is_default = true;
                $company->pivot->save();
                Session::put('company_id', $company->id);
            }

            $shop = $user->shops()->where('is_default', true)->first();
            if (!is_null($shop)) {
                Session::put('shop_id', $shop->id);
                // Log::info($user->default_page);
                // dispatch(new DailyClosingStockJob($shop));
                // dispatch(new MonthlyBalanceSheetJob($shop));
                // dispatch(new BasicBalanceSheetJob($shop));
                return redirect()->intended($user->default_page);
            }else{
                $message = 'You have not created Or Assigned to any Shop/Store. Please complete setup';
                return redirect('user-profile')->with('error', $message);
            }
        }
    }
}
