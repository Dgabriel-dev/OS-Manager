<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprovante #{{ $transaction->id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; margin: 40px; }
        .header { text-align: center; border-bottom: 2px solid #4caf50; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #4caf50; font-size: 22px; }
        .section { margin-bottom: 20px; }
        .section h3 { background: #4caf50; color: #fff; padding: 8px 12px; margin: 0 0 10px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background: #f5f5f5; font-weight: bold; width: 30%; }
        .total-row { background: #4caf50; color: #fff; font-weight: bold; text-align: right; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .receipt-number { font-size: 14px; font-weight: bold; color: #4caf50; text-align: right; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OS ASSIST</h1>
        <p>Comprovante de Pagamento</p>
    </div>

    <div class="receipt-number">Comprovante nº {{ $transaction->id }}</div>

    <div class="section">
        <h3>Dados da Transação</h3>
        <table>
            <tr><th>Data</th><td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td></tr>
            <tr><th>Descrição</th><td>{{ $transaction->description }}</td></tr>
            <tr><th>Valor</th><td>R$ {{ number_format($transaction->amount, 2, ',', '.') }}</td></tr>
            <tr><th>Tipo</th><td>{{ $transaction->type === 'income' ? 'Receita' : 'Despesa' }}</td></tr>
            <tr><th>Categoria</th><td>{{ $transaction->category->name ?? '-' }}</td></tr>
            <tr><th>Método de Pagamento</th><td>{{ $transaction->payment_method ?? '-' }}</td></tr>
            <tr><th>Responsável</th><td>{{ $transaction->user->name ?? '-' }}</td></tr>
        </table>
    </div>

    @if($transaction->serviceOrder)
    <div class="section">
        <h3>Ordem de Serviço</h3>
        <table>
            <tr><th>Número</th><td>{{ $transaction->serviceOrder->order_number }}</td></tr>
        </table>
    </div>
    @endif

    @if($transaction->notes)
    <div class="section">
        <h3>Observações</h3>
        <p>{{ $transaction->notes }}</p>
    </div>
    @endif

    <div class="footer">
        OS Assist - Sistema de Ordem de Serviço | Documento gerado em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
