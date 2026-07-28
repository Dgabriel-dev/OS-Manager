<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EquipmentRepositoryInterface
{
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function findById(int $id, array $columns = ['*']): ?\App\Models\Equipment;

    public function create(array $data): \App\Models\Equipment;

    public function update(int $id, array $data): \App\Models\Equipment;

    public function delete(int $id): bool;

    public function restore(int $id): bool;

    public function findByClient(int $clientId, int $perPage = 15): LengthAwarePaginator;
}
