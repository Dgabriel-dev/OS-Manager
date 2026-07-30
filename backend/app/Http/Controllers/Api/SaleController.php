<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Requests\Sale\UpdateSaleRequest;
use App\Http\Resources\SaleCategoryResource;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(protected SaleService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $sales = $this->service->list($request->only(['payment_status', 'per_page']));

        return response()->json([
            'data' => SaleResource::collection($sales),
            'meta' => [
                'current_page' => $sales->currentPage(),
                'last_page' => $sales->lastPage(),
                'per_page' => $sales->perPage(),
                'total' => $sales->total(),
            ],
        ]);
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        $sale = $this->service->create($request->validated(), $request->user()->id);

        return response()->json([
            'data' => new SaleResource($sale->load(['client', 'items.category', 'user'])),
            'message' => 'Venda criada com sucesso.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $sale = $this->service->show($id);

        if (!$sale) {
            return response()->json(['message' => 'Venda não encontrada.'], 404);
        }

        return response()->json([
            'data' => new SaleResource($sale),
        ]);
    }

    public function update(UpdateSaleRequest $request, int $id): JsonResponse
    {
        $sale = $this->service->update($id, $request->validated(), $request->user()->id);

        return response()->json([
            'data' => new SaleResource($sale->load(['client', 'items.category', 'user'])),
            'message' => 'Venda atualizada com sucesso.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Venda removida com sucesso.',
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'payment_status' => ['required', 'in:pending,paid,cancelled'],
        ]);

        $sale = $this->service->updateStatus($id, $request->payment_status, $request->user()->id);

        return response()->json([
            'data' => new SaleResource($sale->load(['client', 'items.category', 'user'])),
            'message' => 'Status atualizado com sucesso.',
        ]);
    }

    public function indexCategories(): JsonResponse
    {
        $categories = \App\Models\SaleCategory::orderBy('name')->get();

        return response()->json([
            'data' => SaleCategoryResource::collection($categories),
        ]);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:sale_categories,name'],
        ]);

        $category = \App\Models\SaleCategory::create($request->only('name'));

        return response()->json([
            'data' => new SaleCategoryResource($category),
            'message' => 'Categoria criada com sucesso.',
        ], 201);
    }
}
