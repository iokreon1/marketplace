<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Interfaces\NotificationRepositoryInterface;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 15);
            $type = $request->input('type');

            $notifications = $this->notificationRepository->getAllPaginated($limit, $type);

            return ResponseHelper::jsonResponse(true, 'Notifications fetched successfully', $notifications, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Get count of unread notifications.
     */
    public function unreadCount()
    {
        try {
            $count = $this->notificationRepository->getUnreadCount();

            return ResponseHelper::jsonResponse(true, 'Unread notification count fetched', ['unread_count' => $count], 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        try {
            $notification = $this->notificationRepository->markAsRead($id);

            return ResponseHelper::jsonResponse(true, 'Notification marked as read', $notification, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Mark all notifications for user as read.
     */
    public function markAllAsRead()
    {
        try {
            $this->notificationRepository->markAllAsRead();

            return ResponseHelper::jsonResponse(true, 'All notifications marked as read', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->notificationRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Notification deleted successfully', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
