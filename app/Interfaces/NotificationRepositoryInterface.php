<?php

namespace App\Interfaces;

interface NotificationRepositoryInterface
{
    public function getAllPaginated(
        int $limit,
        ?string $type
    );

    public function getUnreadCount();

    public function getById(
        string $id
    );

    public function markAsRead(
        string $id
    );

    public function markAllAsRead();

    public function delete(
        string $id
    );
}