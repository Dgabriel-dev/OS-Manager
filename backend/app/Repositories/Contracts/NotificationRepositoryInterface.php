<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface NotificationRepositoryInterface
{
    public function create(array $data): \App\Models\Notification;

    public function findByUser(int $userId, bool $unreadOnly = false): Collection;

    public function markAsRead(int $notificationId): bool;

    public function markAllAsRead(int $userId): bool;

    public function countUnread(int $userId): int;
}
