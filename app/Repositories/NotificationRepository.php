<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Interfaces\NotificationRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function getAllPaginated(int $limit, ?string $type)
    {
        $query = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->paginate($limit);
    }

    public function getUnreadCount()
    {
        return Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    public function getById(string $id)
    {
        return Notification::where('user_id', Auth::id())->findOrFail($id);
    }

    public function markAsRead(string $id)
    {
        $notification = $this->getById($id);
        $notification->update(['is_read' => true]);
        return $notification;
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function delete(string $id)
    {
        $notification = $this->getById($id);
        $notification->delete();
        return $notification;
    }
}
