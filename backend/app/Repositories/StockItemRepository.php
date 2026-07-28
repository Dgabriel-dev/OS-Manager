<?php

namespace App\Repositories;

use App\Models\StockItem;
use App\Repositories\Contracts\StockItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class StockItemRepository implements StockItemRepositoryInterface
{
    public function __construct(protected StockItem $model)
    {
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->with('category')->orderBy('name')->paginate($perPage, $columns);
    }

    public function findById(int $id, array $columns = ['*']): ?StockItem
    {
        return $this->model->with(['category', 'movements'])->find($id, $columns);
    }

    public function create(array $data): StockItem
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): StockItem
    {
        $item = $this->model->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        $item = $this->model->findOrFail($id);
        return $item->delete();
    }

    public function restore(int $id): bool
    {
        $item = $this->model->onlyTrashed()->findOrFail($id);
        return $item->restore();
    }

    public function findByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('stock_category_id', $categoryId)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getLowStock(): Collection
    {
        return $this->model->whereRaw('quantity <= minimum_quantity')
            ->orderBy('quantity')
            ->get();
    }

    public function adjustStock(int $id, int $quantity, string $type): StockItem
    {
        $item = $this->model->findOrFail($id);

        if ($type === 'entry') {
            $item->quantity += $quantity;
        } else {
            $item->quantity -= $quantity;
        }

        $item->save();
        return $item->fresh();
    }

    public function search(string $term, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('name', 'like', "%{$term}%")
            ->orWhere('internal_code', 'like', "%{$term}%")
            ->orWhere('barcode', 'like', "%{$term}%")
            ->orderBy('name')
            ->paginate($perPage);
    }
}
