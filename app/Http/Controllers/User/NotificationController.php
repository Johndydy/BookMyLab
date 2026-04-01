<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->user()->user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('user.notifications.index', compact('notifications'));
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->user()->user_id)
            ->update(['is_read' => 1]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
