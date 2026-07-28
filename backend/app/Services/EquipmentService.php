<?php

namespace App\Services;

use App\Repositories\Contracts\EquipmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EquipmentService
{
    public function __construct(
        protected EquipmentRepositoryInterface $repository,
    ) {
    }

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters['per_page'] ?? 15);
    }

    public function show(int $id): ?\App\Models\Equipment
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): \App\Models\Equipment
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): \App\Models\Equipment
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function restore(int $id): bool
    {
        return $this->repository->restore($id);
    }

    public function listByClient(int $clientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->findByClient($clientId, $perPage);
    }
}
