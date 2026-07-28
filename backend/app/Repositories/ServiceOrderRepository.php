<?php

namespace App\Repositories;

use App\Models\ServiceOrder;
use App\Repositories\Contracts\ServiceOrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ServiceOrderRepository implements ServiceOrderRepositoryInterface
{
    public function __construct(protected ServiceOrder $model)
    {
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->with(['client', 'equipment', 'technician'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, $columns);
    }

    public function findById(int $id, array $columns = ['*']): ?ServiceOrder
    {
        return $this->model->with(['client', 'equipment', 'technician', 'histories', 'items', 'transactions'])
            ->find($id, $columns);
    }

    public function create(array $data): ServiceOrder
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ServiceOrder
    {
        $order = $this->model->findOrFail($id);
        $order->update($data);
        return $order->fresh();
    }

    public function delete(int $id): bool
    {
        $order = $this->model->findOrFail($id);
        return $order->delete();
    }

    public function restore(int $id): bool
    {
        $order = $this->model->onlyTrashed()->findOrFail($id);
        return $order->restore();
    }

    public function generateOrderNumber(): string
    {
        $year = Carbon::now()->year;
        $lastOrder = $this->model->whereYear('created_at', $year)->max('order_number');

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('OS-%s-%06d', $year, $newNumber);
    }

    public function findByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['client', 'technician'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByTechnician(int $technicianId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['client', 'equipment'])
            ->where('technician_id', $technicianId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByClient(int $clientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['equipment', 'technician'])
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function countByStatus(): Collection
    {
        return $this->model->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();
    }

    public function getRecentOrders(int $limit = 10): Collection
    {
        return $this->model->with(['client', 'technician'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMonthlyStats(): Collection
    {
        $grammar = $this->model->getConnection()->getDriverName();
        $dateFormat = match ($grammar) {
            'sqlite' => "strftime('%%Y-%%m', created_at)",
            default => "DATE_FORMAT(created_at, '%%Y-%%m')",
        };

        return $this->model->selectRaw("{$dateFormat} as month, count(*) as count")
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
