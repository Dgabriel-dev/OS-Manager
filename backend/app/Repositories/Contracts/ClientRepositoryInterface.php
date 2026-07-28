<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ClientRepositoryInterface
{
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function findById(int $id, array $columns = ['*']): ?\App\Models\Client;

    public function create(array $data): \App\Models\Client;

    public function update(int $id, array $data): \App\Models\Client;

    public function delete(int $id): bool;

    public function restore(int $id): bool;

    public function search(string $term, int $perPage = 15): LengthAwarePaginator;

    public function count(): int;

    public function countWithPendingOrders(): int;
}
