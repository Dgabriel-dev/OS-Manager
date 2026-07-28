<?php

namespace App\Repositories\Contracts;

use App\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SaleRepositoryInterface
{
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;
    public function findById(int $id, array $columns = ['*']): ?Sale;
    public function create(array $data): Sale;
    public function update(int $id, array $data): Sale;
    public function delete(int $id): bool;
    public function findByPaymentStatus(string $status, int $perPage = 15): LengthAwarePaginator;
    public function getTotalByPaymentStatus(string $status): float;
    public function getMonthlySalesSummary(int $year): \Illuminate\Database\Eloquent\Collection;
}
