<?php

namespace App\Http\Controllers\AppAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Log;

class AuthenticateController extends Controller
{
    
    public function login(Request $request)
    {
        // Log::info($request);
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        if (is_numeric($request->get('email'))) {
            $credentials = ['phone' => $request->get('email'), 'password' => $request->get('password')];
        }

        try {
            // Log::info($credentials);
            $token = \JWTAuth::attempt($credentials);
            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized! .Wrong Credentials Please re-enter to try again',
                ], 401);

            }

            $user = Auth::user();
            $shop = $user->shops()->where('is_default', true)->first();
            
            if (!is_null($shop)) {
                $company = $user->companies()->wherePivot('is_default', true)->first();
                return response()->json([
                    'status' => 'success',
                    'user' => $user,
                    'shop' => $shop,
                    'company' => $company,
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer', 
                        'expires_in' => auth('api')->factory()->getTTL() * 60,
                    ]
                ]);
            }else{
                Log::info('No default shop');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized! You are not Granted access to any Shop Please contract your admin',
                ], 401);
            }
        } catch (JWTException $e) {
            Log::info($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Wrong Credentials',
            ], 403);
        }
    }


    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function storeFCMToken(Request $request)
    {
        // Always prefer the authenticated API user to avoid cross-user token overwrite.
        $user = auth('api')->user();
        if (is_null($user) && $request->filled('user_id')) {
            $user = User::find($request['user_id']);
        }

        if (!is_null($user)) {
            $token = trim((string) $request->input('fcm_token', ''));
            if ($token === '') {
                return response()->json(['statusCode' => 400, 'message' => 'Invalid FCM token']);
            }

            $user->fcm_token = $token;
            $user->save();

            return response()->json(['statusCode' => 200, 'message' => 'FCM Token stored Successfully']);
        }else {
            return response()->json(['statusCode' => 400, 'message' => 'User not found']);
        }
    }
}
