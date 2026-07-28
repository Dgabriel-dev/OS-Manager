<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(protected RoleRepositoryInterface $repository)
    {
    }

    public function index(): JsonResponse
    {
        $roles = $this->repository->all();

        return response()->json([
            'data' => RoleResource::collection($roles),
        ]);
    }
}
