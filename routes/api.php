<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WelcomeController;

use App\Http\Controllers\AppAPI\AuthenticateController;
use App\Http\Controllers\AppAPI\TruckScanController;
use App\Http\Controllers\AppAPI\VisitorsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('product', [WelcomeController::class, 'products']);
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

        Route::post('store-token', [AuthenticateController::class, 'storeFCMToken']);

        Route::post('truck-order-details', [TruckScanController::class, 'orderDeliveryDetails']);
        Route::post('confirm-delivery-check', [TruckScanController::class, 'guradCheckConfirm']);
        Route::post('my-delivery-notes', [TruckScanController::class, 'dailyDeliveryCheckList']);
        Route::post('delivery-note-info', [TruckScanController::class, 'deliveryNoteInfo']);


        Route::post('visitors', [VisitorsController::class, 'index']);
        Route::post('hosters', [VisitorsController::class, 'create']);
        Route::post('create-visitor', [VisitorsController::class, 'store']);
        Route::post('visitor-photo', [VisitorsController::class, 'visitorPhoto']);
        Route::post('update-visitor', [VisitorsController::class, 'update']);
        Route::post('visitor-check-in', [VisitorsController::class, 'visitorCheckIn']);
        Route::post('visitor-check-out', [VisitorsController::class, 'visitorCheckOut']);
    });

    Route::post('/qr/decrypt', function (Request $request) {
    $request->validate([
        'qr_data' => 'required|string',
        'app_key' => 'required|string'
    ]);

    $empID = QrCodeEncryption::decrypt(
        $request->qr_data, 
        $request->app_key
    );

    if (!$empID) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid or unauthorized QR code'
        ], 401);
    }

    return response()->json([
        'success' => true,
        'emp_id' => $empID
    ]);
});
});

