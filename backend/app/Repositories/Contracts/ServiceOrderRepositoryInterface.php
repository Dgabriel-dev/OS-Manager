<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ServiceOrderRepositoryInterface
{
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function findById(int $id, array $columns = ['*']): ?\App\Models\ServiceOrder;

    public function create(array $data): \App\Models\ServiceOrder;

    public function update(int $id, array $data): \App\Models\ServiceOrder;

    public function delete(int $id): bool;

    public function restore(int $id): bool;

    public function generateOrderNumber(): string;

    public function findByStatus(string $status, int $perPage = 15): LengthAwarePaginator;

    public function findByTechnician(int $technicianId, int $perPage = 15): LengthAwarePaginator;

    public function findByClient(int $clientId, int $perPage = 15): LengthAwarePaginator;

    public function countByStatus(): Collection;

    public function getRecentOrders(int $limit = 10): Collection;

    public function getMonthlyStats(): Collection;
}
