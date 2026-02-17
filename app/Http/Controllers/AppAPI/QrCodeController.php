<?php

namespace App\Http\Controllers\AppAPI;

use App\Helpers\QrCodeEncryption;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    //
    public  function decrypt(Request $request) {
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
}
}
