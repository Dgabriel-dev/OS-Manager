<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SaleRepository implements SaleRepositoryInterface
{
    public function __construct(protected Sale $model)
    {
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->with(['client', 'items.category', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, $columns);
    }

    public function findById(int $id, array $columns = ['*']): ?Sale
    {
        return $this->model->with(['client', 'items.category', 'user'])
            ->find($id, $columns);
    }

    public function create(array $data): Sale
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Sale
    {
        $sale = $this->model->findOrFail($id);
        $sale->update($data);
        return $sale->fresh();
    }

    public function delete(int $id): bool
    {
        $sale = $this->model->findOrFail($id);
        return $sale->delete();
    }

    public function findByPaymentStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['client', 'items.category', 'user'])
            ->where('payment_status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getTotalByPaymentStatus(string $status): float
    {
        return (float) $this->model->where('payment_status', $status)->sum('total_amount');
    }

    public function getMonthlySalesSummary(int $year): Collection
    {
        return $this->model->selectRaw("
            to_char(created_at, 'YYYY-MM') as month,
            SUM(total_amount) as total,
            COUNT(*) as count
        ")
            ->whereYear('created_at', $year)
            ->where('payment_status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
