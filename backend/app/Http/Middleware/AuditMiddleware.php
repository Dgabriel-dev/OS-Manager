<?php

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    public function __construct(protected AuditService $auditService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            try {
                $this->auditService->log(
                    $request->user(),
                    $this->getAction($request),
                    $request->path(),
                    null,
                    $this->getModelData($request, $response),
                    $request
                );
            } catch (\Throwable $e) {
                Log::warning('Audit logging failed: ' . $e->getMessage());
            }
        }

        return $response;
    }

    private function getAction(Request $request): string
    {
        return match ($request->method()) {
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'accessed',
        };
    }

    private function getModelData(Request $request, Response $response): ?array
    {
        if ($response->getStatusCode() >= 400) {
            return null;
        }

        $content = $response->getContent();
        if (!$content) {
            return null;
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) ? ($decoded['data'] ?? $decoded) : null;
    }
}
