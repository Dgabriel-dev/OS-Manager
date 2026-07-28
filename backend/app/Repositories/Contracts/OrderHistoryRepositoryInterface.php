<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface OrderHistoryRepositoryInterface
{
    public function create(array $data): \App\Models\OrderHistory;

    public function findByOrder(int $orderId): Collection;
}
