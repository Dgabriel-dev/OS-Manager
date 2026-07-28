<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\ServiceOrder;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FinancialService
{
    public function __construct(
        protected TransactionRepositoryInterface $repository,
        protected ServiceOrder $serviceOrderModel,
        protected Sale $saleModel,
    ) {
    }

    public function listTransactions(array $filters = []): LengthAwarePaginator
    {
        if (isset($filters['type'])) {
            return $this->repository->findByType($filters['type'], $filters['per_page'] ?? 15);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            return $this->repository->findByDateRange(
                $filters['start_date'],
                $filters['end_date'],
                $filters['per_page'] ?? 15
            );
        }

        return $this->repository->paginate($filters['per_page'] ?? 15);
    }

    public function showTransaction(int $id): ?\App\Models\Transaction
    {
        return $this->repository->findById($id);
    }

    public function createTransaction(array $data): \App\Models\Transaction
    {
        return $this->repository->create($data);
    }

    public function updateTransaction(int $id, array $data): \App\Models\Transaction
    {
        return $this->repository->update($id, $data);
    }

    public function deleteTransaction(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getDashboard(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $totalIncome = $this->repository->getTotalByType('income');
        $totalExpense = $this->repository->getTotalByType('expense');
        $monthlyIncome = $this->repository->getTotalByType('income', $startOfMonth, $endOfMonth);
        $monthlyExpense = $this->repository->getTotalByType('expense', $startOfMonth, $endOfMonth);

        $ordersByStatus = $this->serviceOrderModel->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $totalOrders = $this->serviceOrderModel->count();

        $pendingRevenue = (float) $this->serviceOrderModel
            ->whereIn('status', ['open', 'in_progress', 'waiting_parts'])
            ->sum('estimated_value');

        $completedRevenue = (float) $this->serviceOrderModel
            ->whereIn('status', ['completed', 'delivered'])
            ->sum('final_value');

        $monthlyCompletedRevenue = (float) $this->serviceOrderModel
            ->whereIn('status', ['completed', 'delivered'])
            ->whereBetween('delivered_at', [$startOfMonth, $endOfMonth . ' 23:59:59'])
            ->sum('final_value');

        $monthlyOrdersCreated = $this->serviceOrderModel
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth . ' 23:59:59'])
            ->count();

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense,
            'monthly_income' => $monthlyIncome,
            'monthly_expense' => $monthlyExpense,
            'monthly_balance' => $monthlyIncome - $monthlyExpense,
            'total_orders' => $totalOrders,
            'orders_by_status' => $ordersByStatus,
            'pending_revenue' => $pendingRevenue,
            'completed_revenue' => $completedRevenue,
            'monthly_completed_revenue' => $monthlyCompletedRevenue,
            'monthly_orders_created' => $monthlyOrdersCreated,
        ];
    }

    public function getDailySummary(?string $date = null): array
    {
        $date = $date ?? Carbon::now()->toDateString();

        return [
            'date' => $date,
            'income' => $this->repository->getTotalByType('income', $date, $date),
            'expense' => $this->repository->getTotalByType('expense', $date, $date),
        ];
    }

    public function getMonthlySummary(int $year): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getMonthlySummary($year);
    }

    public function getYearlySummary(int $year): array
    {
        $startDate = Carbon::create($year, 1, 1)->toDateString();
        $endDate = Carbon::create($year, 12, 31)->toDateString();

        return [
            'year' => $year,
            'income' => $this->repository->getTotalByType('income', $startDate, $endDate),
            'expense' => $this->repository->getTotalByType('expense', $startDate, $endDate),
        ];
    }

    public function getRevenueByMonth(): array
    {
        $now = Carbon::now();
        $months = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth()->toDateString();
            $endOfMonth = $date->copy()->endOfMonth()->toDateString();
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('M/Y');

            $transactionIncome = $this->repository->getTotalByType('income', $startOfMonth, $endOfMonth);
            $transactionExpense = $this->repository->getTotalByType('expense', $startOfMonth, $endOfMonth);

            $saleRevenue = (float) $this->saleModel
                ->where('payment_status', 'paid')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth . ' 23:59:59'])
                ->sum('total_amount');

            $saleCost = (float) $this->saleModel
                ->where('payment_status', 'paid')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth . ' 23:59:59'])
                ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->sum('sale_items.cost_price');

            $months[] = [
                'month' => $monthKey,
                'label' => $monthLabel,
                'income' => $transactionIncome + $saleRevenue,
                'transaction_income' => $transactionIncome,
                'sale_revenue' => $saleRevenue,
                'sale_cost' => $saleCost,
                'sale_profit' => $saleRevenue - $saleCost,
                'expense' => $transactionExpense,
                'balance' => ($transactionIncome + $saleRevenue) - $transactionExpense,
            ];
        }

        $cumulativeRevenue = 0;
        $currentMonthRevenue = 0;
        $currentMonthKey = $now->format('Y-m');

        foreach ($months as &$month) {
            $cumulativeRevenue += $month['income'];
            $month['cumulative'] = $cumulativeRevenue;

            if ($month['month'] === $currentMonthKey) {
                $currentMonthRevenue = $month['income'];
            }
        }
        unset($month);

        return [
            'months' => $months,
            'cumulative_revenue' => $cumulativeRevenue,
            'current_month_revenue' => $currentMonthRevenue,
        ];
    }
}
