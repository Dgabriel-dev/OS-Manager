<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as BaseNotFoundHttpException;

class NotFoundHttpException extends BaseNotFoundHttpException
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
