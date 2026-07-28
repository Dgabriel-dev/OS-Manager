<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $notifications = $this->service->getForUser($request->user()->id);

        return response()->json([
            'data' => NotificationResource::collection($notifications),
        ]);
    }

    public function markAsRead(int $id): JsonResponse
    {
        $this->service->markAsRead($id);

        return response()->json([
            'message' => 'Notificação marcada como lida.',
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $this->service->markAllAsRead($request->user()->id);

        return response()->json([
            'message' => 'Todas as notificações foram marcadas como lidas.',
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = $this->service->countUnread($request->user()->id);

        return response()->json([
            'count' => $count,
        ]);
    }
}
