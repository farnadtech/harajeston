<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function getRecent()
    {
        try {
            $notifications = Notification::where('user_id', auth()->id())
                ->latest()
                ->take(5)
                ->get();

            $unreadCount = Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->count();

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching notifications: ' . $e->getMessage());
            
            return response()->json([
                'notifications' => [],
                'unread_count' => 0,
            ]);
        }
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        // If notification has a link, redirect to it
        if ($notification->link) {
            return redirect($notification->link);
        }

        // Otherwise go back to notifications
        return redirect()->route('admin.notifications.index');
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
