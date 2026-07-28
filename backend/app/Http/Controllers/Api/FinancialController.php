<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\StoreTransactionRequest;
use App\Http\Requests\Financial\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\FinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function __construct(protected FinancialService $service)
    {
    }

    public function indexTransactions(Request $request): JsonResponse
    {
        $transactions = $this->service->listTransactions($request->only(['type', 'start_date', 'end_date', 'per_page']));

        return response()->json([
            'data' => TransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    public function storeTransaction(StoreTransactionRequest $request): JsonResponse
    {
        $this->authorize('create', Transaction::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $transaction = $this->service->createTransaction($data);

        return response()->json([
            'data' => new TransactionResource($transaction->load(['category', 'serviceOrder'])),
            'message' => 'Transação criada com sucesso.',
        ], 201);
    }

    public function showTransaction(int $id): JsonResponse
    {
        $transaction = $this->service->showTransaction($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transação não encontrada.'], 404);
        }

        return response()->json([
            'data' => new TransactionResource($transaction->load(['category', 'serviceOrder', 'user'])),
        ]);
    }

    public function updateTransaction(UpdateTransactionRequest $request, int $id): JsonResponse
    {
        $transaction = $this->service->updateTransaction($id, $request->validated());

        return response()->json([
            'data' => new TransactionResource($transaction->load(['category', 'serviceOrder'])),
            'message' => 'Transação atualizada com sucesso.',
        ]);
    }

    public function destroyTransaction(int $id): JsonResponse
    {
        $this->service->deleteTransaction($id);

        return response()->json([
            'message' => 'Transação removida com sucesso.',
        ]);
    }

    public function dashboard(): JsonResponse
    {
        $dashboard = $this->service->getDashboard();

        return response()->json([
            'data' => $dashboard,
        ]);
    }

    public function revenueByMonth(): JsonResponse
    {
        $data = $this->service->getRevenueByMonth();

        return response()->json([
            'data' => $data,
        ]);
    }

    public function indexCategories(): JsonResponse
    {
        $categories = \App\Models\FinancialCategory::orderBy('name')->get();

        return response()->json([
            'data' => $categories,
        ]);
    }
}
