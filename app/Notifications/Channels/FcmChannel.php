<?php

namespace App\Notifications\Channels;

use App\Services\Firebase\FcmClient;
use Illuminate\Notifications\Notification;

class FcmChannel
{
    protected $fcmClient;

    public function __construct()
    {
        $this->fcmClient = new FcmClient();
    }
    
    public function send($notifiable, Notification $notification)
    {
        $fcmNotification = $notification->toFcm($notifiable);
        $response = $this->fcmClient->sendMessage(
            $notifiable->routeNotificationFor('fcm'),
            $fcmNotification
        );
        //\Log::info('FCM Response:', ['response' => $response]); 
        return $response;
    }
}