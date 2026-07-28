<?php

namespace App\Services;

use App\Models\ServiceOrder;
use App\Repositories\Contracts\OrderHistoryRepositoryInterface;
use App\Repositories\Contracts\ServiceOrderRepositoryInterface;
use App\Repositories\Contracts\StockItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ServiceOrderService
{
    public function __construct(
        protected ServiceOrderRepositoryInterface $repository,
        protected OrderHistoryRepositoryInterface $historyRepository,
        protected StockItemRepositoryInterface $stockRepository,
    ) {
    }

    public function list(array $filters = []): LengthAwarePaginator
    {
        if (isset($filters['status'])) {
            return $this->repository->findByStatus($filters['status'], $filters['per_page'] ?? 15);
        }

        if (isset($filters['technician_id'])) {
            return $this->repository->findByTechnician($filters['technician_id'], $filters['per_page'] ?? 15);
        }

        if (isset($filters['client_id'])) {
            return $this->repository->findByClient($filters['client_id'], $filters['per_page'] ?? 15);
        }

        return $this->repository->paginate($filters['per_page'] ?? 15);
    }

    public function show(int $id): ?ServiceOrder
    {
        return $this->repository->findById($id);
    }

    public function create(array $data, ?int $userId = null): ServiceOrder
    {
        $data['order_number'] = $this->repository->generateOrderNumber();
        $data['status'] = $data['status'] ?? 'pending';

        $order = $this->repository->create($data);

        $this->historyRepository->create([
            'service_order_id' => $order->id,
            'user_id' => $userId,
            'action' => 'created',
            'old_values' => null,
            'new_values' => $order->toArray(),
        ]);

        return $order;
    }

    public function update(int $id, array $data, ?int $userId = null): ServiceOrder
    {
        $oldOrder = $this->repository->findById($id);
        $order = $this->repository->update($id, $data);

        $this->historyRepository->create([
            'service_order_id' => $order->id,
            'user_id' => $userId,
            'action' => 'updated',
            'old_values' => $oldOrder->toArray(),
            'new_values' => $order->toArray(),
        ]);

        return $order;
    }

    public function updateStatus(int $id, string $status, ?string $notes = null, ?int $userId = null, ?array $usedParts = null): ServiceOrder
    {
        $oldOrder = $this->repository->findById($id);

        $updateData = ['status' => $status];

        if ($status === 'delivered') {
            $updateData['delivered_at'] = now();
        }

        if ($notes) {
            $updateData['notes'] = $notes;
        }

        $order = $this->repository->update($id, $updateData);

        if ($usedParts && count($usedParts) > 0) {
            foreach ($usedParts as $part) {
                $this->stockRepository->adjustStock(
                    $part['stock_item_id'],
                    $part['quantity'],
                    'exit'
                );
            }
        }

        $this->historyRepository->create([
            'service_order_id' => $order->id,
            'user_id' => $userId,
            'action' => 'status_changed',
            'old_values' => ['status' => $oldOrder->status],
            'new_values' => ['status' => $status, 'notes' => $notes],
        ]);

        return $order;
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function restore(int $id): bool
    {
        return $this->repository->restore($id);
    }

    public function countByStatus(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->countByStatus();
    }

    public function getRecentOrders(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getRecentOrders($limit);
    }

    public function getMonthlyStats(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getMonthlyStats();
    }
}
