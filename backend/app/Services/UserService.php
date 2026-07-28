<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(protected UserRepositoryInterface $repository)
    {
    }

    public function list(array $filters = []): LengthAwarePaginator
    {
        if (isset($filters['role_id'])) {
            return $this->repository->findByRole($filters['role_id'], $filters['per_page'] ?? 15);
        }

        return $this->repository->paginate($filters['per_page'] ?? 15);
    }

    public function show(int $id): ?\App\Models\User
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): \App\Models\User
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): \App\Models\User
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function assignRole(int $userId, int $roleId): \App\Models\User
    {
        return $this->repository->assignRole($userId, $roleId);
    }
}
