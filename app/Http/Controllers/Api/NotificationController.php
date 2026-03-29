<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends BaseApiController
{
    /**
     * Get user notifications.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return $this->paginatedSuccess($notifications, 'Notifications retrieved successfully');
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Request $request, $notificationId): JsonResponse
    {
        $notification = $request->user()->notifications()->find($notificationId);

        if (!$notification) {
            return $this->notFound('Notification not found');
        }

        $notification->markAsRead();

        return $this->success(null, 'Notification marked as read');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->success(null, 'All notifications marked as read');
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();

        return $this->success(['unread_count' => $count], 'Unread count retrieved');
    }
}
