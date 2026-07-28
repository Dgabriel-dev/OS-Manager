<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function findById(int $id, array $columns = ['*']): ?\App\Models\User;

    public function create(array $data): \App\Models\User;

    public function update(int $id, array $data): \App\Models\User;

    public function delete(int $id): bool;

    public function findByRole(int $roleId, int $perPage = 15): LengthAwarePaginator;

    public function assignRole(int $userId, int $roleId): \App\Models\User;
}
