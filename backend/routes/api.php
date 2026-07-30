<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\FinancialController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PdfController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ServiceOrderController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\AuditMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:10,1')->post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/password', [AuthController::class, 'changePassword']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::middleware(AuditMiddleware::class)->group(function () {
        Route::apiResource('clients', ClientController::class);
        Route::apiResource('equipment', EquipmentController::class);
        Route::apiResource('service-orders', ServiceOrderController::class);
        Route::apiResource('users', UserController::class);
    });

    Route::post('clients/{id}/restore', [ClientController::class, 'restore']);

    Route::post('equipment/{id}/restore', [EquipmentController::class, 'restore']);
    Route::get('clients/{clientId}/equipment', [EquipmentController::class, 'byClient']);
    Route::post('equipment/{id}/files', [EquipmentController::class, 'uploadFile']);
    Route::delete('equipment/{id}/files/{fileId}', [EquipmentController::class, 'deleteFile']);

    Route::post('service-orders/{id}/restore', [ServiceOrderController::class, 'restore']);
    Route::put('service-orders/{id}/status', [ServiceOrderController::class, 'updateStatus']);
    Route::get('service-orders/{id}/history', [ServiceOrderController::class, 'history']);
    Route::post('service-orders/{id}/items', [ServiceOrderController::class, 'addItem']);
    Route::delete('service-orders/{id}/items/{itemId}', [ServiceOrderController::class, 'removeItem']);

    Route::get('/stock/items', [StockController::class, 'indexItems']);
    Route::post('/stock/items', [StockController::class, 'storeItem']);
    Route::get('/stock/items/{id}', [StockController::class, 'showItem']);
    Route::put('/stock/items/{id}', [StockController::class, 'updateItem']);
    Route::delete('/stock/items/{id}', [StockController::class, 'destroyItem']);
    Route::put('/stock/items/{id}/adjust', [StockController::class, 'adjustStock']);
    Route::get('/stock/movements', [StockController::class, 'indexMovements']);
    Route::get('/stock/categories', [StockController::class, 'indexCategories']);

    Route::get('/financial/transactions', [FinancialController::class, 'indexTransactions']);
    Route::post('/financial/transactions', [FinancialController::class, 'storeTransaction']);
    Route::get('/financial/transactions/{id}', [FinancialController::class, 'showTransaction']);
    Route::put('/financial/transactions/{id}', [FinancialController::class, 'updateTransaction']);
    Route::delete('/financial/transactions/{id}', [FinancialController::class, 'destroyTransaction']);
    Route::get('/financial/dashboard', [FinancialController::class, 'dashboard']);
    Route::get('/financial/revenue-by-month', [FinancialController::class, 'revenueByMonth']);
    Route::get('/financial/categories', [FinancialController::class, 'indexCategories']);

    Route::get('/sales', [SaleController::class, 'index']);
    Route::post('/sales', [SaleController::class, 'store']);
    Route::get('/sales/categories', [SaleController::class, 'indexCategories']);
    Route::post('/sales/categories', [SaleController::class, 'storeCategory']);
    Route::get('/sales/{id}', [SaleController::class, 'show']);
    Route::put('/sales/{id}', [SaleController::class, 'update']);
    Route::delete('/sales/{id}', [SaleController::class, 'destroy']);
    Route::put('/sales/{id}/status', [SaleController::class, 'updateStatus']);

    Route::get('/roles', [RoleController::class, 'index']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);

    Route::get('/pdf/service-order/{id}', [PdfController::class, 'serviceOrder']);
    Route::get('/pdf/budget/{id}', [PdfController::class, 'budget']);
    Route::get('/pdf/receipt/{id}', [PdfController::class, 'receipt']);
    Route::get('/pdf/warranty/{id}', [PdfController::class, 'warranty']);
    Route::get('/pdf/technical-report/{id}', [PdfController::class, 'technicalReport']);
});
