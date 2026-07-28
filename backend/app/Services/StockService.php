<?php

namespace App\Services;

use App\Repositories\Contracts\StockItemRepositoryInterface;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class StockService
{
    public function __construct(
        protected StockItemRepositoryInterface $itemRepository,
        protected StockMovementRepositoryInterface $movementRepository,
    ) {
    }

    public function listItems(array $filters = []): LengthAwarePaginator
    {
        if (isset($filters['search']) && $filters['search'] !== '') {
            return $this->itemRepository->search($filters['search'], $filters['per_page'] ?? 15);
        }

        if (isset($filters['category_id'])) {
            return $this->itemRepository->findByCategory($filters['category_id'], $filters['per_page'] ?? 15);
        }

        return $this->itemRepository->paginate($filters['per_page'] ?? 15);
    }

    public function showItem(int $id): ?\App\Models\StockItem
    {
        return $this->itemRepository->findById($id);
    }

    public function createItem(array $data): \App\Models\StockItem
    {
        return $this->itemRepository->create($data);
    }

    public function updateItem(int $id, array $data): \App\Models\StockItem
    {
        return $this->itemRepository->update($id, $data);
    }

    public function deleteItem(int $id): bool
    {
        return $this->itemRepository->delete($id);
    }

    public function adjustStock(int $itemId, int $quantity, string $type, ?string $notes = null, ?int $userId = null, ?int $serviceOrderId = null): \App\Models\StockItem
    {
        $item = $this->itemRepository->findById($itemId);
        $previousQuantity = $item->quantity;

        $updatedItem = $this->itemRepository->adjustStock($itemId, $quantity, $type);

        $this->movementRepository->create([
            'stock_item_id' => $itemId,
            'type' => $type,
            'quantity' => $quantity,
            'previous_quantity' => $previousQuantity,
            'new_quantity' => $updatedItem->quantity,
            'user_id' => $userId,
            'service_order_id' => $serviceOrderId,
            'notes' => $notes,
        ]);

        return $updatedItem;
    }

    public function listMovements(array $filters = []): LengthAwarePaginator
    {
        if (isset($filters['item_id'])) {
            return $this->movementRepository->findByItem($filters['item_id'], $filters['per_page'] ?? 15);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            return $this->movementRepository->findByDateRange(
                $filters['start_date'],
                $filters['end_date'],
                $filters['per_page'] ?? 15
            );
        }

        return $this->movementRepository->paginate($filters['per_page'] ?? 15);
    }

    public function getLowStockItems(): Collection
    {
        return $this->itemRepository->getLowStock();
    }
}
