<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException as BaseValidationException;
use Illuminate\Http\JsonResponse;

class ValidationException extends BaseValidationException
{
    public function render($request): JsonResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $this->errors(),
            ], 422);
        }

        return parent::render($request);
    }
}
