<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function findById(int $id, array $columns = ['*']): ?\App\Models\Transaction;

    public function create(array $data): \App\Models\Transaction;

    public function update(int $id, array $data): \App\Models\Transaction;

    public function delete(int $id): bool;

    public function findByType(string $type, int $perPage = 15): LengthAwarePaginator;

    public function findByDateRange(string $startDate, string $endDate, int $perPage = 15): LengthAwarePaginator;

    public function getTotalByType(string $type, ?string $startDate = null, ?string $endDate = null): float;

    public function getMonthlySummary(int $year): Collection;
}
