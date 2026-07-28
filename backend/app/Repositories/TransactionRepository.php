<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(protected Transaction $model)
    {
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->with(['category', 'serviceOrder', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, $columns);
    }

    public function findById(int $id, array $columns = ['*']): ?Transaction
    {
        return $this->model->with(['category', 'serviceOrder', 'user'])->find($id, $columns);
    }

    public function create(array $data): Transaction
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Transaction
    {
        $transaction = $this->model->findOrFail($id);
        $transaction->update($data);
        return $transaction->fresh();
    }

    public function delete(int $id): bool
    {
        $transaction = $this->model->findOrFail($id);
        return $transaction->delete();
    }

    public function findByType(string $type, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['category', 'user'])
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByDateRange(string $startDate, string $endDate, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['category', 'user'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->paginate($perPage);
    }

    public function getTotalByType(string $type, ?string $startDate = null, ?string $endDate = null): float
    {
        $query = $this->model->where('type', $type)->where('status', 'completed');

        if ($startDate && $endDate) {
            $query->whereBetween('payment_date', [$startDate, $endDate]);
        }

        return (float) $query->sum('amount');
    }

    public function getMonthlySummary(int $year): Collection
    {
        return $this->model->selectRaw("
            type,
            to_char(payment_date, 'YYYY-MM') as month,
            SUM(amount) as total
        ")
            ->whereYear('payment_date', $year)
            ->where('status', 'completed')
            ->groupBy('type', 'month')
            ->orderBy('month')
            ->get();
    }
}
