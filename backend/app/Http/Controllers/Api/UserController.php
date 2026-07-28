<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $users = $this->service->list($request->only(['role_id', 'per_page']));

        return response()->json([
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->service->create($request->validated());

        return response()->json([
            'data' => new UserResource($user->load('role')),
            'message' => 'Usuário criado com sucesso.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->service->show($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        return response()->json([
            'data' => new UserResource($user),
        ]);
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = $this->service->update($id, $request->validated());

        return response()->json([
            'data' => new UserResource($user->load('role')),
            'message' => 'Usuário atualizado com sucesso.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Usuário removido com sucesso.',
        ]);
    }
}
