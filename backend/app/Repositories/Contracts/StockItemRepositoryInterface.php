<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface StockItemRepositoryInterface
{
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function findById(int $id, array $columns = ['*']): ?\App\Models\StockItem;

    public function create(array $data): \App\Models\StockItem;

    public function update(int $id, array $data): \App\Models\StockItem;

    public function delete(int $id): bool;

    public function restore(int $id): bool;

    public function findByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator;

    public function getLowStock(): Collection;

    public function adjustStock(int $id, int $quantity, string $type): \App\Models\StockItem;

    public function search(string $term, int $perPage = 15): LengthAwarePaginator;
}
