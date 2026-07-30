<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\StoreEquipmentRequest;
use App\Http\Requests\Equipment\UpdateEquipmentRequest;
use App\Http\Resources\EquipmentResource;
use App\Services\EquipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function __construct(protected EquipmentService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $equipment = $this->service->list($request->only(['per_page']));

        return response()->json([
            'data' => EquipmentResource::collection($equipment),
            'meta' => [
                'current_page' => $equipment->currentPage(),
                'last_page' => $equipment->lastPage(),
                'per_page' => $equipment->perPage(),
                'total' => $equipment->total(),
            ],
        ]);
    }

    public function store(StoreEquipmentRequest $request): JsonResponse
    {
        $equipment = $this->service->create($request->validated());

        return response()->json([
            'data' => new EquipmentResource($equipment->load('client')),
            'message' => 'Equipamento criado com sucesso.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $equipment = $this->service->show($id);

        if (!$equipment) {
            return response()->json(['message' => 'Equipamento não encontrado.'], 404);
        }

        return response()->json([
            'data' => new EquipmentResource($equipment),
        ]);
    }

    public function update(UpdateEquipmentRequest $request, int $id): JsonResponse
    {
        $equipment = $this->service->update($id, $request->validated());

        return response()->json([
            'data' => new EquipmentResource($equipment->load('client')),
            'message' => 'Equipamento atualizado com sucesso.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Equipamento removido com sucesso.',
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $this->service->restore($id);

        return response()->json([
            'message' => 'Equipamento restaurado com sucesso.',
        ]);
    }

    public function byClient(int $clientId, Request $request): JsonResponse
    {
        $equipment = $this->service->listByClient($clientId, $request->get('per_page', 15));

        return response()->json([
            'data' => EquipmentResource::collection($equipment),
            'meta' => [
                'current_page' => $equipment->currentPage(),
                'last_page' => $equipment->lastPage(),
                'per_page' => $equipment->perPage(),
                'total' => $equipment->total(),
            ],
        ]);
    }

    public function uploadFile(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $equipment = \App\Models\Equipment::findOrFail($id);
        $file = $request->file('file');
        $path = $file->store('equipment-files', 'public');

        $equipmentFile = $equipment->equipmentFiles()->create([
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'original_name' => $file->getClientOriginalName(),
        ]);

        return response()->json([
            'data' => $equipmentFile,
            'message' => 'Arquivo enviado com sucesso.',
        ], 201);
    }

    public function deleteFile(int $id, int $fileId): JsonResponse
    {
        $equipment = \App\Models\Equipment::findOrFail($id);
        $file = $equipment->equipmentFiles()->findOrFail($fileId);

        $fullPath = storage_path('app/public/' . $file->file_path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $file->delete();

        return response()->json([
            'message' => 'Arquivo removido com sucesso.',
        ]);
    }
}
