<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function all(): Collection;

    public function findByName(string $name): ?\App\Models\Role;

    public function create(array $data): \App\Models\Role;
}
