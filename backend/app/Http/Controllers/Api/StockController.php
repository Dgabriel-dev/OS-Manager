<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\StoreStockItemRequest;
use App\Http\Requests\Stock\UpdateStockItemRequest;
use App\Http\Resources\StockItemResource;
use App\Http\Resources\StockMovementResource;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(protected StockService $service)
    {
    }

    public function indexItems(Request $request): JsonResponse
    {
        $items = $this->service->listItems($request->only(['search', 'category_id', 'per_page']));

        return response()->json([
            'data' => StockItemResource::collection($items),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function storeItem(StoreStockItemRequest $request): JsonResponse
    {
        $item = $this->service->createItem($request->validated());

        return response()->json([
            'data' => new StockItemResource($item->load('category')),
            'message' => 'Item de estoque criado com sucesso.',
        ], 201);
    }

    public function showItem(int $id): JsonResponse
    {
        $item = $this->service->showItem($id);

        if (!$item) {
            return response()->json(['message' => 'Item de estoque não encontrado.'], 404);
        }

        return response()->json([
            'data' => new StockItemResource($item),
        ]);
    }

    public function updateItem(UpdateStockItemRequest $request, int $id): JsonResponse
    {
        $item = $this->service->updateItem($id, $request->validated());

        return response()->json([
            'data' => new StockItemResource($item->load('category')),
            'message' => 'Item de estoque atualizado com sucesso.',
        ]);
    }

    public function destroyItem(int $id): JsonResponse
    {
        $this->service->deleteItem($id);

        return response()->json([
            'message' => 'Item de estoque removido com sucesso.',
        ]);
    }

    public function adjustStock(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'string', 'in:entry,exit'],
            'notes' => ['nullable', 'string'],
            'service_order_id' => ['nullable', 'exists:service_orders,id'],
        ]);

        $item = $this->service->adjustStock(
            $id,
            $validated['quantity'],
            $validated['type'],
            $validated['notes'] ?? null,
            $request->user()->id,
            $validated['service_order_id'] ?? null,
        );

        return response()->json([
            'data' => new StockItemResource($item),
            'message' => 'Estoque ajustado com sucesso.',
        ]);
    }

    public function indexMovements(Request $request): JsonResponse
    {
        $movements = $this->service->listMovements($request->only(['item_id', 'start_date', 'end_date', 'per_page']));

        return response()->json([
            'data' => StockMovementResource::collection($movements),
            'meta' => [
                'current_page' => $movements->currentPage(),
                'last_page' => $movements->lastPage(),
                'per_page' => $movements->perPage(),
                'total' => $movements->total(),
            ],
        ]);
    }

    public function indexCategories(): JsonResponse
    {
        $categories = \App\Models\StockCategory::orderBy('name')->get();

        return response()->json([
            'data' => $categories,
        ]);
    }
}
