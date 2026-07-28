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
