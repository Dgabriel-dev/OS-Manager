<?php

namespace App\Services;

use App\Repositories\Contracts\TransactionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FinancialService
{
    public function __construct(protected TransactionRepositoryInterface $repository)
    {
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

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense,
            'monthly_income' => $monthlyIncome,
            'monthly_expense' => $monthlyExpense,
            'monthly_balance' => $monthlyIncome - $monthlyExpense,
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
}
