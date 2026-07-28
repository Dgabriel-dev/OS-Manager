<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Services\PdfService;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    public function __construct(protected PdfService $service)
    {
    }

    public function serviceOrder(int $id): Response
    {
        $order = ServiceOrder::with(['client', 'equipment', 'technician', 'items.stockItem'])->findOrFail($id);
        $pdf = $this->service->generateServiceOrder($order);

        return $pdf->download("ordem-servico-{$order->order_number}.pdf");
    }

    public function budget(int $id): Response
    {
        $order = ServiceOrder::with(['client', 'equipment', 'items.stockItem'])->findOrFail($id);
        $pdf = $this->service->generateBudget($order);

        return $pdf->download("orcamento-{$order->order_number}.pdf");
    }

    public function receipt(int $id): Response
    {
        $transaction = Transaction::with(['serviceOrder', 'category', 'user'])->findOrFail($id);
        $pdf = $this->service->generateReceipt($transaction);

        return $pdf->download("comprovante-{$transaction->id}.pdf");
    }

    public function warranty(int $id): Response
    {
        $order = ServiceOrder::with(['client', 'equipment'])->findOrFail($id);
        $pdf = $this->service->generateWarranty($order);

        return $pdf->download("garantia-{$order->order_number}.pdf");
    }

    public function technicalReport(int $id): Response
    {
        $order = ServiceOrder::with(['client', 'equipment', 'technician', 'histories.user', 'items.stockItem'])->findOrFail($id);
        $pdf = $this->service->generateTechnicalReport($order);

        return $pdf->download("laudo-tecnico-{$order->order_number}.pdf");
    }
}
