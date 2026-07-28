#!/bin/bash
# =============================================================================
# FIX BACKEND - Execute com: sudo ./fix-backend.sh
# =============================================================================
set -e
BASEDIR="$(cd "$(dirname "$0")" && pwd)"
cd "$BASEDIR"

echo "=== Corrigindo backend ==="

# --- 1. Fix ServiceOrderController history() method ---
echo "[1/5] Corrigindo ServiceOrderController::history()..."
cat > /tmp/fix_history_method.php << 'PHPEOF'
<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceOrder\StoreServiceOrderRequest;
use App\Http\Requests\ServiceOrder\UpdateServiceOrderRequest;
use App\Http\Requests\ServiceOrder\UpdateStatusRequest;
use App\Http\Resources\ServiceOrderDetailResource;
use App\Http\Resources\ServiceOrderResource;
use App\Models\ServiceOrder;
use App\Services\ServiceOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceOrderController extends Controller
{
    public function __construct(protected ServiceOrderService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $orders = $this->service->list($request->only(['status', 'technician_id', 'client_id', 'per_page']));

        return response()->json([
            'data' => ServiceOrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function store(StoreServiceOrderRequest $request): JsonResponse
    {
        $this->authorize('create', ServiceOrder::class);

        $order = $this->service->create($request->validated(), $request->user()->id);

        return response()->json([
            'data' => new ServiceOrderDetailResource($order->load(['client', 'equipment', 'technician', 'histories'])),
            'message' => 'Ordem de serviço criada com sucesso.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->service->show($id);

        if (!$order) {
            return response()->json(['message' => 'Ordem de serviço não encontrada.'], 404);
        }

        return response()->json([
            'data' => new ServiceOrderDetailResource($order),
        ]);
    }

    public function update(UpdateServiceOrderRequest $request, int $id): JsonResponse
    {
        $order = ServiceOrder::findOrFail($id);
        $this->authorize('update', $order);

        $order = $this->service->update($id, $request->validated(), $request->user()->id);

        return response()->json([
            'data' => new ServiceOrderDetailResource($order->load(['client', 'equipment', 'technician', 'histories'])),
            'message' => 'Ordem de serviço atualizada com sucesso.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $order = ServiceOrder::findOrFail($id);
        $this->authorize('delete', $order);

        $this->service->delete($id);

        return response()->json([
            'message' => 'Ordem de serviço removida com sucesso.',
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $this->service->restore($id);

        return response()->json([
            'message' => 'Ordem de serviço restaurada com sucesso.',
        ]);
    }

    public function updateStatus(UpdateStatusRequest $request, int $id): JsonResponse
    {
        $order = ServiceOrder::findOrFail($id);
        $this->authorize('updateStatus', $order);

        $order = $this->service->updateStatus(
            $id,
            $request->validated('status'),
            $request->validated('notes'),
            $request->user()->id,
            $request->validated('used_parts'),
        );

        return response()->json([
            'data' => new ServiceOrderDetailResource($order->load(['client', 'equipment', 'technician', 'histories'])),
            'message' => 'Status atualizado com sucesso.',
        ]);
    }

    public function history(int $id): JsonResponse
    {
        $order = \App\Models\ServiceOrder::find($id);

        if (!$order) {
            return response()->json(['message' => 'Ordem de serviço não encontrada.'], 404);
        }

        return response()->json([
            'data' => \App\Http\Resources\OrderHistoryResource::collection(
                $order->histories()->with('user')->latest()->get()
            ),
        ]);
    }
}
PHPEOF
cp /tmp/fix_history_method.php "backend/app/Http/Controllers/Api/ServiceOrderController.php"
chown www-data:www-data "backend/app/Http/Controllers/Api/ServiceOrderController.php"
echo "   OK"

# --- 2. Fix StockController indexCategories() method ---
echo "[2/5] Corrigindo StockController::indexCategories()..."
cat > /tmp/fix_stock_controller.php << 'PHPEOF'
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
PHPEOF
cp /tmp/fix_stock_controller.php "backend/app/Http/Controllers/Api/StockController.php"
chown www-data:www-data "backend/app/Http/Controllers/Api/StockController.php"
echo "   OK"

# --- 3. Fix FinancialController indexCategories() method ---
echo "[3/5] Corrigindo FinancialController::indexCategories()..."
cat > /tmp/fix_financial_controller.php << 'PHPEOF'
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\StoreTransactionRequest;
use App\Http\Requests\Financial\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\FinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function __construct(protected FinancialService $service)
    {
    }

    public function indexTransactions(Request $request): JsonResponse
    {
        $transactions = $this->service->listTransactions($request->only(['type', 'start_date', 'end_date', 'per_page']));

        return response()->json([
            'data' => TransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    public function storeTransaction(StoreTransactionRequest $request): JsonResponse
    {
        $this->authorize('create', Transaction::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $transaction = $this->service->createTransaction($data);

        return response()->json([
            'data' => new TransactionResource($transaction->load(['category', 'serviceOrder'])),
            'message' => 'Transação criada com sucesso.',
        ], 201);
    }

    public function showTransaction(int $id): JsonResponse
    {
        $transaction = $this->service->showTransaction($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transação não encontrada.'], 404);
        }

        return response()->json([
            'data' => new TransactionResource($transaction->load(['category', 'serviceOrder', 'user'])),
        ]);
    }

    public function updateTransaction(UpdateTransactionRequest $request, int $id): JsonResponse
    {
        $transaction = $this->service->updateTransaction($id, $request->validated());

        return response()->json([
            'data' => new TransactionResource($transaction->load(['category', 'serviceOrder'])),
            'message' => 'Transação atualizada com sucesso.',
        ]);
    }

    public function destroyTransaction(int $id): JsonResponse
    {
        $this->service->deleteTransaction($id);

        return response()->json([
            'message' => 'Transação removida com sucesso.',
        ]);
    }

    public function dashboard(): JsonResponse
    {
        $dashboard = $this->service->getDashboard();

        return response()->json([
            'data' => $dashboard,
        ]);
    }

    public function indexCategories(): JsonResponse
    {
        $categories = \App\Models\FinancialCategory::orderBy('name')->get();

        return response()->json([
            'data' => $categories,
        ]);
    }
}
PHPEOF
cp /tmp/fix_financial_controller.php "backend/app/Http/Controllers/Api/FinancialController.php"
chown www-data:www-data "backend/app/Http/Controllers/Api/FinancialController.php"
echo "   OK"

# --- 4. Restore Handler.php ---
echo "[4/5] Restaurando Handler.php..."
if [ -f "backend/app/Exceptions/Handler.php.bak" ]; then
    cp "backend/app/Exceptions/Handler.php.bak" "backend/app/Exceptions/Handler.php"
    chown www-data:www-data "backend/app/Exceptions/Handler.php"
    echo "   OK"
else
    echo "   Handler.php.bak não encontrado, pulando"
fi

# --- 5. Fix APP_DEBUG ---
echo "[5/5] Configurando APP_DEBUG=false..."
if grep -q 'APP_DEBUG=true' backend/.env; then
    sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' backend/.env
    echo "   OK"
else
    echo "   Já configurado"
fi

# --- Clear caches ---
echo ""
echo "Limpando caches..."
docker compose exec php php artisan route:clear 2>/dev/null || true
docker compose exec php php artisan config:clear 2>/dev/null || true
docker compose exec php php artisan cache:clear 2>/dev/null || true

echo ""
echo "=== Todos os fixes aplicados com sucesso! ==="
echo ""
echo "Correcoes:"
echo "  1. ServiceOrderController::history() - PHP syntax corrigido"
echo "  2. StockController::indexCategories() - PHP syntax corrigido"
echo "  3. FinancialController::indexCategories() - PHP syntax corrigido"
echo "  4. Handler.php restaurado"
echo "  5. APP_DEBUG definido como false"
