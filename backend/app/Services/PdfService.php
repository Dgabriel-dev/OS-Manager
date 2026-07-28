<?php

namespace App\Services;

use App\Models\ServiceOrder;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generateServiceOrder(ServiceOrder $order): \Barryvdh\DomPDF\PDF
    {
        $order->load(['client', 'equipment', 'technician', 'items.stockItem']);

        $pdf = Pdf::loadView('pdf.service-order', compact('order'));
        $pdf->setPaper('a4');

        return $pdf;
    }

    public function generateBudget(ServiceOrder $order): \Barryvdh\DomPDF\PDF
    {
        $order->load(['client', 'equipment', 'items.stockItem']);

        $pdf = Pdf::loadView('pdf.budget', compact('order'));
        $pdf->setPaper('a4');

        return $pdf;
    }

    public function generateReceipt(Transaction $transaction): \Barryvdh\DomPDF\PDF
    {
        $transaction->load(['serviceOrder', 'category', 'user']);

        $pdf = Pdf::loadView('pdf.receipt', compact('transaction'));
        $pdf->setPaper('a4');

        return $pdf;
    }

    public function generateWarranty(ServiceOrder $order): \Barryvdh\DomPDF\PDF
    {
        $order->load(['client', 'equipment']);

        $pdf = Pdf::loadView('pdf.warranty', compact('order'));
        $pdf->setPaper('a4');

        return $pdf;
    }

    public function generateTechnicalReport(ServiceOrder $order): \Barryvdh\DomPDF\PDF
    {
        $order->load(['client', 'equipment', 'technician', 'histories.user', 'items.stockItem']);

        $pdf = Pdf::loadView('pdf.technical-report', compact('order'));
        $pdf->setPaper('a4');

        return $pdf;
    }
}
