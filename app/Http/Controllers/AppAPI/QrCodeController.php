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

    $decrypted = QrCodeEncryption::decrypt($request->qr_data, $request->app_key);


    if (!$decrypted) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid or unauthorized QR code'
        ], 401);
    }

    $empID = $decrypted['emp_id'] ?? null;
    $companyID = $decrypted['company_id'] ?? null;
    $employee_id = $decrypted['id'] ?? null;


    return response()->json([
        'success' => true,
        'emp_id' => $empID,
        'company_id' => $companyID,
        'id' => $employee_id
    ]);
}
}
