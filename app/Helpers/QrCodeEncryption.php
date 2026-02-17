<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Log;

class QrCodeEncryption
{
    /**
     * Encrypt employee ID for QR code
     * 
     * @param string $empID
     * @return string
     */
    public static function encrypt($empID)
    {
        try {
            // Add a timestamp to make each QR unique and time-bound (optional)
            $data = [
                'id' => $empID,
                'timestamp' => now()->timestamp,
                'app_key' => config('app.qr_app_key') // Secret key for your mobile app
            ];
            
            return base64_encode(Crypt::encryptString(json_encode($data)));
        } catch (\Exception $e) {
            \Log::error('QR Encryption failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Decrypt employee ID from QR code
     * 
     * @param string $encryptedData
     * @param string $appKey
     * @return string|null
     */
    public static function decrypt($encryptedData, $appKey = null)
    {
        try {
            $decrypted = json_decode(Crypt::decryptString(base64_decode($encryptedData)), true);
            
            // Verify app key matches
            if ($appKey && $decrypted['app_key'] !== $appKey) {
                return null; // Invalid app key
            }
            
            // Optional: Check if QR is still valid (e.g., within 24 hours)
            // if (now()->timestamp - $decrypted['timestamp'] > 86400) {
            //     return null; // Expired QR
            // }
            
            return $decrypted['id'];
        } catch (\Exception $e) {
            Log::error('QR Decryption failed: ' . $e->getMessage());
            return null;
        }
    }
}