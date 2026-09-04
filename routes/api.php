<?php

use App\Helpers\QrCodeEncryption;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AppAPI\AttendanceController;
use App\Http\Controllers\AppAPI\AuthenticateController;
use App\Http\Controllers\AppAPI\PushNotificationController;
use App\Http\Controllers\AppAPI\QrCodeController;
use App\Http\Controllers\AppAPI\TruckScanController;
use App\Http\Controllers\AppAPI\VisitorsController;
use App\Http\Controllers\VML\VisitorController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('product', [WelcomeController::class, 'products']);
Route::get('product-details/{id}', [WelcomeController::class, 'productDetail']);
Route::get('my-dashboard/{id}', [WelcomeController::class, 'clientDashboard']);
Route::get('delivery-rate', [WelcomeController::class, 'deliveryRate']);

Route::group(['middleware' => 'cors'], function () {

    Route::post('login', [AuthenticateController::class, 'login']);
    Route::get('visitor-photo-file/{filename}', [VisitorsController::class, 'photo'])
        ->where('filename', '.*');
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


        Route::get('visitors', [VisitorsController::class, 'index']);
        Route::get('visitor/{id}', [VisitorsController::class, 'show']);
        Route::post('hosters', [VisitorsController::class, 'create']);
        Route::post('create-visitor', [VisitorsController::class, 'store']);
        Route::post('visitor-photo', [VisitorsController::class, 'visitorPhoto']);
        Route::post('update-visitor', [VisitorsController::class, 'update']);
        Route::post('visitor-check-in', [VisitorsController::class, 'visitorCheckIn']);
        Route::post('visitor-check-out', [VisitorsController::class, 'visitorCheckOut']);
        Route::post('available-badges', [VisitorsController::class, 'getAvailableBadges']);
        Route::post('checkin-with-badge', [VisitorsController::class, 'Vcheckinwithbadge']);

        Route::post('send-notification', [PushNotificationController::class, 'sendToUsers']);
        Route::post('send-broadcast-notification', [PushNotificationController::class, 'sendBroadcast']);

    });
        //check in visitor
       Route::post('checkin-with-badge', [VisitorsController::class, 'Vcheckinwithbadge']);


    // QR Code API
        Route::post('/qr/decrypt',[QrCodeController::class, 'decrypt']);

        // Attendance API — JWT (logged-in Entrance Admin)
        Route::middleware(['auth:api', 'throttle:10,1'])->group(function () {
            Route::post('attendance-punchin', [AttendanceController::class, 'punchIn'])->name('api.attendance-punchin');
            Route::post('attendance-punchin-by-face', [AttendanceController::class, 'punchInByFace'])->name('api.attendance-punchin-by-face');
            Route::post('attendance-punchin-by-fingerprint', [AttendanceController::class, 'punchInByFingerprint'])->name('api.attendance-punchin-by-fingerprint');
            Route::post('attendance-punchout', [AttendanceController::class, 'punchOut'])->name('api.attendance-punchout');
            Route::post('attendance-register-face', [AttendanceController::class, 'registerFaceTemplate'])->name('api.attendance-register-face');
            Route::post('attendance-verify-qr', [AttendanceController::class, 'verifyEmployeeQr'])->name('api.attendance-verify-qr');
        });

        });

