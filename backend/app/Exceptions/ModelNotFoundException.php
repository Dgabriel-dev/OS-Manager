<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException as BaseModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ModelNotFoundException extends BaseModelNotFoundException
{
    public function render($request): JsonResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Recurso não encontrado.',
            ], 404);
        }

        return parent::render($request);
    }
}
