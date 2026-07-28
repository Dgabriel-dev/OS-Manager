<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    public function __construct(protected NotificationRepositoryInterface $repository)
    {
    }

    public function sendToUser(int $userId, string $title, string $message, ?array $data = null): \App\Models\Notification
    {
        return $this->repository->create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public function sendToRole(string $roleName, string $title, string $message, ?array $data = null): void
    {
        $users = User::whereHas('role', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })->where('is_active', true)->get();

        foreach ($users as $user) {
            $this->sendToUser($user->id, $title, $message, $data);
        }
    }

    public function getForUser(int $userId, bool $unreadOnly = false): Collection
    {
        return $this->repository->findByUser($userId, $unreadOnly);
    }

    public function markAsRead(int $notificationId): bool
    {
        return $this->repository->markAsRead($notificationId);
    }

    public function markAllAsRead(int $userId): bool
    {
        return $this->repository->markAllAsRead($userId);
    }

    public function countUnread(int $userId): int
    {
        return $this->repository->countUnread($userId);
    }
}
