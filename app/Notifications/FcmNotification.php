<?php

namespace App\Notifications;

use App\Services\Firebase\FcmClient;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
// use NotificationChannels\Fcm\FcmChannel;
use App\Notifications\Channels\FcmChannel;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class FcmNotification extends Notification 

{
   // use Queueable;
    
    protected $notificationData;
    
    public function __construct($notificationData)
    {
        $this->notificationData = $notificationData;
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

   
    public function toFcm($notifiable)
    {
        $token = $notifiable->routeNotificationFor('fcm');
        
        //\Log::info('Attempting to send FCM to token: ' . $token);
        
        if (!$token) {
        // \Log::error('No FCM token found for user: ' . $notifiable->id);
            return null;
        }
        
        $notification = FirebaseNotification::create(
            $this->notificationData['title'],
            $this->notificationData['body']
        );
        
        // $message = CloudMessage::withTarget('token', $token)
        //     ->withNotification($notification)
        //     ->withData($this->notificationData['data'] ?? []);
        $message = CloudMessage::new()
        ->toToken($token)
        ->withNotification($notification)
        ->withData(array_map('strval', $this->notificationData['data'] ?? []));
        
        return $message;
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->notificationData['title'],
            'body' => $this->notificationData['body'],
            'data' => $this->notificationData['data'] ?? [],
        ];
    }
}