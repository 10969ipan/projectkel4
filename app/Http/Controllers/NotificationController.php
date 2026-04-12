<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the current user.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['notifications' => [], 'unreadCount' => 0]);
        }

        $unread = $user->unreadNotifications;
        $notifications = $unread->map(function ($notification) {
            return [
                'id' => $notification->id,
                'data' => $notification->data,
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unread->count()
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        return response()->json(['success' => true]);
    }
}
