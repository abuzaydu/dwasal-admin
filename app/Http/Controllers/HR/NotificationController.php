<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;
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

    // Mark a single notification as read
    // public function markRead(string $id)
    // {
    //     $notification = Auth::user()->notifications()->findOrFail($id);
    //     $notification->markAsRead();

    //     return back()->with('success', 'Notification marked as read.');
    // }
   // <?php

public function markRead(string $id)
{
    $notification = Auth::user()->notifications()->findOrFail($id);
    $notification->markAsRead();

    // AJAX call (from the notification click) — return JSON
    if (request()->expectsJson() || request()->ajax()) {
        return response()->json(['success' => true]);
    }

    // Normal form POST (fallback ✓ button) — redirect back
    return back()->with('success', 'Notification marked as read.');
}

    // Mark all notifications as read
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
