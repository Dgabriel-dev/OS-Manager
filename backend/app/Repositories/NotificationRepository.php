<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(protected Notification $model)
    {
    }

    public function create(array $data): Notification
    {
        return $this->model->create($data);
    }

    public function findByUser(int $userId, bool $unreadOnly = false): Collection
    {
        $query = $this->model->where('user_id', $userId);

        if ($unreadOnly) {
            $query->whereNull('read_at');
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function markAsRead(int $notificationId): bool
    {
        $notification = $this->model->findOrFail($notificationId);
        return $notification->update(['read_at' => Carbon::now()]);
    }

    public function markAllAsRead(int $userId): bool
    {
        return $this->model
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]) >= 0;
    }

    public function countUnread(int $userId): int
    {
        return $this->model
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }
}
