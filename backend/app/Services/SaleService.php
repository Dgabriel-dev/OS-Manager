<?php

namespace App\Services;

use App\Models\Sale;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SaleService
{
    public function __construct(
        protected SaleRepositoryInterface $repository,
    ) {
    }

    public function list(array $filters = []): LengthAwarePaginator
    {
        if (isset($filters['payment_status'])) {
            return $this->repository->findByPaymentStatus($filters['payment_status'], $filters['per_page'] ?? 15);
        }

        return $this->repository->paginate($filters['per_page'] ?? 15);
    }

    public function show(int $id): ?Sale
    {
        return $this->repository->findById($id);
    }

    public function create(array $data, ?int $userId = null): Sale
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $totalAmount = 0;
        foreach ($items as $item) {
            $quantity = $item['quantity'] ?? 1;
            $unitPrice = $item['unit_price'] ?? 0;
            $totalAmount += $quantity * $unitPrice;
        }

        $data['total_amount'] = $totalAmount;
        $data['user_id'] = $userId;

        $sale = $this->repository->create($data);

        if (!empty($items)) {
            foreach ($items as $item) {
                $quantity = $item['quantity'] ?? 1;
                $unitPrice = $item['unit_price'] ?? 0;
                $sale->items()->create([
                    'name' => $item['name'],
                    'sale_category_id' => $item['sale_category_id'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'cost_price' => $item['cost_price'] ?? 0,
                    'total_price' => $quantity * $unitPrice,
                ]);
            }
        }

        return $sale;
    }

    public function update(int $id, array $data, ?int $userId = null): Sale
    {
        $items = $data['items'] ?? null;
        unset($data['items']);

        if ($items !== null) {
            $totalAmount = 0;
            foreach ($items as $item) {
                $quantity = $item['quantity'] ?? 1;
                $unitPrice = $item['unit_price'] ?? 0;
                $totalAmount += $quantity * $unitPrice;
            }
            $data['total_amount'] = $totalAmount;
        }

        $sale = $this->repository->update($id, $data);

        if ($items !== null) {
            $sale->items()->delete();
            foreach ($items as $item) {
                $quantity = $item['quantity'] ?? 1;
                $unitPrice = $item['unit_price'] ?? 0;
                $sale->items()->create([
                    'name' => $item['name'],
                    'sale_category_id' => $item['sale_category_id'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'cost_price' => $item['cost_price'] ?? 0,
                    'total_price' => $quantity * $unitPrice,
                ]);
            }
        }

        return $sale;
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
