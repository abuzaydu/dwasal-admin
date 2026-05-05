<?php

namespace App\Notifications\Channels;

use App\Services\Firebase\FcmClient;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class FcmChannel
{
    protected $fcmClient;

    public function __construct()
    {
        $this->fcmClient = new FcmClient();
    }
    
    public function send($notifiable, Notification $notification)
    {
        // Reload from DB to avoid stale model attributes during long request lifecycles.
        $freshNotifiable = method_exists($notifiable, 'fresh') ? $notifiable->fresh() : $notifiable;
        $token = trim((string) optional($freshNotifiable)->routeNotificationFor('fcm'));
        if (empty($token)) {
            return null;
        }

        // The FcmClient sends HTTP v1 payloads and expects array data.
        // Use toArray() so title/body/data are always available.
        $payload = method_exists($notification, 'toArray')
            ? $notification->toArray($notifiable)
            : [];

        \Log::info('Sending FCM notification', [
            'user_id' => $freshNotifiable->id ?? $notifiable->id ?? null,
            'token_length' => strlen($token),
            'token_sha1' => sha1($token),
            'db' => DB::connection()->getDatabaseName(),
            'env' => app()->environment(),
            'base_path' => base_path(),
        ]);

        $response = $this->fcmClient->sendMessage($token, $payload);

        // Remove stale token if Firebase reports this registration as invalid.
        $errorText = strtolower((string) ($response['error'] ?? ''));
        $statusText = strtoupper((string) ($response['status'] ?? ''));
        $isNotRegistered = str_contains($errorText, 'notregistered')
            || str_contains($errorText, 'unregistered')
            || $statusText === 'NOT_FOUND';

        if ($isNotRegistered && isset($freshNotifiable->fcm_token)) {
            $freshNotifiable->fcm_token = null;
            $freshNotifiable->save();
            \Log::warning('Cleared invalid FCM token after NotRegistered/UNREGISTERED response.', [
                'user_id' => $freshNotifiable->id ?? $notifiable->id ?? null,
            ]);
        }

        return $response;
    }
}