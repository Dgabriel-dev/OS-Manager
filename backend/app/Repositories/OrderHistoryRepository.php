<?php

namespace App\Repositories;

use App\Models\OrderHistory;
use App\Repositories\Contracts\OrderHistoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OrderHistoryRepository implements OrderHistoryRepositoryInterface
{
    public function __construct(protected OrderHistory $model)
    {
    }

    public function create(array $data): OrderHistory
    {
        return $this->model->create($data);
    }

    public function findByOrder(int $orderId): Collection
    {
        return $this->model->with('user')
            ->where('service_order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
