<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(protected User $model)
    {
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->with('role')->orderBy('name')->paginate($perPage, $columns);
    }

    public function findById(int $id, array $columns = ['*']): ?User
    {
        return $this->model->with('role')->find($id, $columns);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->model->findOrFail($id);
        $user->update($data);
        return $user->fresh();
    }

    public function delete(int $id): bool
    {
        $user = $this->model->findOrFail($id);
        return $user->delete();
    }

    public function findByRole(int $roleId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('role_id', $roleId)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function assignRole(int $userId, int $roleId): User
    {
        $user = $this->model->findOrFail($userId);
        $user->update(['role_id' => $roleId]);
        return $user->fresh('role');
    }
}
