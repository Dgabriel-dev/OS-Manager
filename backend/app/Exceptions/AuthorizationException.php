<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException as BaseAuthorizationException;
use Illuminate\Http\JsonResponse;

class AuthorizationException extends BaseAuthorizationException
{
    public function render($request): JsonResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Você não tem permissão para realizar esta ação.',
            ], 403);
        }

        return parent::render($request);
    }
}
