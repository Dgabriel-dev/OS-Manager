<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface StockMovementRepositoryInterface
{
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function create(array $data): \App\Models\StockMovement;

    public function findByItem(int $itemId, int $perPage = 15): LengthAwarePaginator;

    public function findByDateRange(string $startDate, string $endDate, int $perPage = 15): LengthAwarePaginator;
}
