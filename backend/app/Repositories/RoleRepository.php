<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    public function __construct(protected Role $model)
    {
    }

    public function all(): Collection
    {
        return $this->model->withCount('users')->orderBy('name')->get();
    }

    public function findByName(string $name): ?Role
    {
        return $this->model->where('name', $name)->first();
    }

    public function create(array $data): Role
    {
        return $this->model->create($data);
    }
}
