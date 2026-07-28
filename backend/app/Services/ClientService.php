<?php

namespace App\Services;

use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClientService
{
    public function __construct(protected ClientRepositoryInterface $repository)
    {
    }

    public function list(array $filters = []): LengthAwarePaginator
    {
        if (isset($filters['search']) && $filters['search'] !== '') {
            return $this->repository->search($filters['search'], $filters['per_page'] ?? 15);
        }

        return $this->repository->paginate($filters['per_page'] ?? 15);
    }

    public function show(int $id): ?\App\Models\Client
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): \App\Models\Client
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): \App\Models\Client
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

    public function count(): int
    {
        return $this->repository->count();
    }

    public function countWithPendingOrders(): int
    {
        return $this->repository->countWithPendingOrders();
    }
}
