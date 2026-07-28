<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(protected ClientService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $clients = $this->service->list($request->only(['search', 'per_page']));

        return response()->json([
            'data' => ClientResource::collection($clients),
            'meta' => [
                'current_page' => $clients->currentPage(),
                'last_page' => $clients->lastPage(),
                'per_page' => $clients->perPage(),
                'total' => $clients->total(),
            ],
        ]);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $this->authorize('create', Client::class);

        $client = $this->service->create($request->validated());

        return response()->json([
            'data' => new ClientResource($client),
            'message' => 'Cliente criado com sucesso.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $client = $this->service->show($id);

        if (!$client) {
            return response()->json(['message' => 'Cliente não encontrado.'], 404);
        }

        return response()->json([
            'data' => new ClientResource($client->loadCount('serviceOrders')),
        ]);
    }

    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        $client = Client::findOrFail($id);
        $this->authorize('update', $client);

        $client = $this->service->update($id, $request->validated());

        return response()->json([
            'data' => new ClientResource($client),
            'message' => 'Cliente atualizado com sucesso.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $client = Client::findOrFail($id);
        $this->authorize('delete', $client);

        $this->service->delete($id);

        return response()->json([
            'message' => 'Cliente removido com sucesso.',
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $this->service->restore($id);

        return response()->json([
            'message' => 'Cliente restaurado com sucesso.',
        ]);
    }
}
