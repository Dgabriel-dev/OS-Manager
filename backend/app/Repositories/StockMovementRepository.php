<?php

namespace App\Repositories;

use App\Models\StockMovement;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockMovementRepository implements StockMovementRepositoryInterface
{
    public function __construct(protected StockMovement $model)
    {
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->with(['stockItem', 'user', 'serviceOrder'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, $columns);
    }

    public function create(array $data): StockMovement
    {
        return $this->model->create($data);
    }

    public function findByItem(int $itemId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['user', 'serviceOrder'])
            ->where('stock_item_id', $itemId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByDateRange(string $startDate, string $endDate, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['stockItem', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
