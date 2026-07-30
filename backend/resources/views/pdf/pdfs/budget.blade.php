<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orçamento #{{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; margin: 40px; }
        .header { text-align: center; border-bottom: 2px solid #ff9800; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #ff9800; font-size: 22px; }
        .header p { margin: 5px 0 0; color: #666; }
        .section { margin-bottom: 20px; }
        .section h3 { background: #ff9800; color: #fff; padding: 8px 12px; margin: 0 0 10px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background: #f5f5f5; font-weight: bold; }
        .total-row { background: #ff9800; color: #fff; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .validity { text-align: center; font-size: 11px; color: #666; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OS ASSIST</h1>
        <p>Orçamento nº {{ $order->order_number }}</p>
    </div>

    <div class="section">
        <h3>Cliente</h3>
        <table>
            <tr><th style="width:25%">Nome</th><td>{{ $order->client->name }}</td><th style="width:25%">Telefone</th><td>{{ $order->client->phone ?? '-' }}</td></tr>
            <tr><th>CPF/CNPJ</th><td>{{ $order->client->document ?? '-' }}</td><th>Email</th><td>{{ $order->client->email ?? '-' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Equipamento</h3>
        <table>
            <tr><th style="width:25%">Categoria</th><td>{{ $order->equipment->category }}</td><th style="width:25%">Marca</th><td>{{ $order->equipment->brand }}</td></tr>
            <tr><th>Modelo</th><td>{{ $order->equipment->model }}</td><th>Nº Série</th><td>{{ $order->equipment->serial_number ?? '-' }}</td></tr>
            <tr><th>Defeito Relatado</th><td colspan="3">{{ $order->equipment->reported_defect ?? '-' }}</td></tr>
        </table>
    </div>

    @if($order->items->count())
    <div class="section">
        <h3>Itens / Serviços</h3>
        <table>
            <thead>
                <tr><th>Descrição</th><th>Tipo</th><th>Qtd</th><th>Unitário</th><th>Total</th></tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->description ?? $item->stockItem->name ?? '-' }}</td>
                    <td>{{ $item->type }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" style="text-align:right">Total Estimado</td>
                    <td>R$ {{ number_format($order->estimated_value ?? $order->items->sum('total_price'), 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <p class="validity">Este orçamento é válido por 7 dias a partir da data de emissão.</p>

    <div class="footer">
        OS Assist - Sistema de Ordem de Serviço | Documento gerado em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
