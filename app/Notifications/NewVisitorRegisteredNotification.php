<?php

namespace App\Notifications;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewVisitorRegisteredNotification extends Notification
{
    use Queueable;

    public function __construct(private Visitor $visitor)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Visitor Registration',
            'message' => 'New visitor '.$this->visitor->name.' is waiting for permission approval.',
            'type' => 'visitor',
            'action' => 'visitor_registered',
            'visitor_id' => $this->visitor->id,
            'visitor_name' => $this->visitor->name,
            'visitor_mobile' => $this->visitor->mobile,
            'shop_id' => $this->visitor->shop_id,
        ];
    }
}

