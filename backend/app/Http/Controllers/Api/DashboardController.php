<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $service)
    {
    }

    public function index(): JsonResponse
    {
        $stats = $this->service->getStats();
        $monthlyOrders = $this->service->getMonthlyOrdersChart();
        $revenueChart = $this->service->getRevenueChart();
        $topEquipments = $this->service->getTopEquipmentsChart();
        $recentOrders = $this->service->getRecentOrders();

        return response()->json([
            'data' => [
                'stats' => $stats,
                'monthly_orders' => $monthlyOrders,
                'revenue_chart' => $revenueChart,
                'top_equipments' => $topEquipments,
                'recent_orders' => $recentOrders,
                'low_stock_items' => $stats['low_stock_items'] ?? collect(),
            ],
        ]);
    }
}
