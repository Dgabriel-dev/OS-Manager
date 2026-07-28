<?php

namespace App\Repositories;

use App\Models\Equipment;
use App\Repositories\Contracts\EquipmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EquipmentRepository implements EquipmentRepositoryInterface
{
    public function __construct(protected Equipment $model)
    {
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->with('client')->orderBy('created_at', 'desc')->paginate($perPage, $columns);
    }

    public function findById(int $id, array $columns = ['*']): ?Equipment
    {
        return $this->model->with(['client', 'serviceOrders'])->find($id, $columns);
    }

    public function create(array $data): Equipment
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Equipment
    {
        $equipment = $this->model->findOrFail($id);
        $equipment->update($data);
        return $equipment->fresh();
    }

    public function delete(int $id): bool
    {
        $equipment = $this->model->findOrFail($id);
        return $equipment->delete();
    }

    public function restore(int $id): bool
    {
        $equipment = $this->model->onlyTrashed()->findOrFail($id);
        return $equipment->restore();
    }

    public function findByClient(int $clientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
