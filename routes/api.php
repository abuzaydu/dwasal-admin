<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WelcomeController;

use App\Http\Controllers\AppAPI\AuthenticateController;
use App\Http\Controllers\AppAPI\TruckScanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('product', [WelcomeController::class, 'index']);
Route::get('product-details/{id}', [WelcomeController::class, 'productDetail']);
Route::get('my-dashboard/{id}', [WelcomeController::class, 'clientDashboard']);
Route::get('delivery-rate', [WelcomeController::class, 'deliveryRate']);

Route::group(['middleware' => 'cors'], function () {
    
    Route::post('login', [AuthenticateController::class, 'login']);
    // Reset password
    Route::post('forgot-pass', [UserController::class, 'forgotPass']);
    Route::post('reset-code', [UserController::class, 'verifyCode']);
    Route::post('reset-pass', [UserController::class, 'resetPass']);

    Route::group(['middleware' => 'jwt.auth'], function(){
        // SMART MAUZO API SOKONI 
        Route::post('check-token', function(){
            return response()->json(['error' => false]);
        });

        Route::post('truck-order-details', [TruckScanController::class, 'orderDeliveryDetails']);
    });
});

