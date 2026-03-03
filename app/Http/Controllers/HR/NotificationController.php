<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
     public function index()
    {   $page = 'notifications';
        $user = Auth::user();

        return view('hr.hr-notification.notification', [
            'allNotifications'    => $user->notifications,
            'unreadNotifications' => $user->unreadNotifications,
            'readNotifications'   => $user->notifications->whereNotNull('read_at'),
            'page' => $page
        ]);
    }

    public function markRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
    public function readAndRedirect($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $visitorId = $notification->data['visitor_id'];

        return redirect()->route('visitors.show', encrypt($visitorId));
    }

}
