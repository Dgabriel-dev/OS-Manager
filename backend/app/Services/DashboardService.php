<?php

namespace App\Services;

use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\EquipmentRepositoryInterface;
use App\Repositories\Contracts\ServiceOrderRepositoryInterface;
use App\Repositories\Contracts\StockItemRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;

class DashboardService
{
    public function __construct(
        protected ClientRepositoryInterface $clientRepository,
        protected EquipmentRepositoryInterface $equipmentRepository,
        protected ServiceOrderRepositoryInterface $serviceOrderRepository,
        protected StockItemRepositoryInterface $stockRepository,
        protected TransactionRepositoryInterface $transactionRepository,
    ) {
    }

    public function getStats(): array
    {
        $totalClients = $this->clientRepository->count();
        $totalEquipment = $this->equipmentRepository->paginate(1)->total();
        $ordersByStatus = $this->serviceOrderRepository->countByStatus()->pluck('count', 'status');
        $lowStockItems = $this->stockRepository->getLowStock();

        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();
        $monthlyRevenue = $this->transactionRepository->getTotalByType('income', $startOfMonth, $endOfMonth);

        return [
            'total_clients' => $totalClients,
            'total_equipment' => $totalEquipment,
            'orders_by_status' => $ordersByStatus,
            'monthly_revenue' => $monthlyRevenue,
            'low_stock_count' => $lowStockItems->count(),
            'low_stock_items' => $lowStockItems,
        ];
    }

    public function getMonthlyOrdersChart(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->serviceOrderRepository->getMonthlyStats();
    }

    public function getRevenueChart(int $months = 12): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth()->toDateString();
            $endOfMonth = $date->copy()->endOfMonth()->toDateString();

            $data[] = [
                'month' => $date->format('Y-m'),
                'income' => $this->transactionRepository->getTotalByType('income', $startOfMonth, $endOfMonth),
                'expense' => $this->transactionRepository->getTotalByType('expense', $startOfMonth, $endOfMonth),
            ];
        }

        return $data;
    }

    public function getTopEquipmentsChart(): array
    {
        $topBrands = \App\Models\Equipment::selectRaw('brand, count(*) as count')
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return $topBrands->toArray();
    }

    public function getRecentOrders(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->serviceOrderRepository->getRecentOrders($limit);
    }
}
