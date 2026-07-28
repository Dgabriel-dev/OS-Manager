<?php

namespace App\Repositories;

use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClientRepository implements ClientRepositoryInterface
{
    public function __construct(protected Client $model)
    {
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->orderBy('created_at', 'desc')->paginate($perPage, $columns);
    }

    public function findById(int $id, array $columns = ['*']): ?Client
    {
        return $this->model->with(['equipments', 'serviceOrders'])->find($id, $columns);
    }

    public function create(array $data): Client
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Client
    {
        $client = $this->model->findOrFail($id);
        $client->update($data);
        return $client->fresh();
    }

    public function delete(int $id): bool
    {
        $client = $this->model->findOrFail($id);
        return $client->delete();
    }

    public function restore(int $id): bool
    {
        $client = $this->model->onlyTrashed()->findOrFail($id);
        return $client->restore();
    }

    public function search(string $term, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('name', 'like', "%{$term}%")
            ->orWhere('cpf_cnpj', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%")
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function count(): int
    {
        return $this->model->count();
    }

    public function countWithPendingOrders(): int
    {
        return $this->model->whereHas('serviceOrders', function ($query) {
            $query->whereIn('status', ['pending', 'in_progress', 'waiting_parts']);
        })->count();
    }
}
