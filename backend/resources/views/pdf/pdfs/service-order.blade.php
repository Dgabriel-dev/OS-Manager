<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ordem de Serviço #{{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; margin: 40px; }
        .header { text-align: center; border-bottom: 2px solid #0066cc; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #0066cc; font-size: 22px; }
        .header p { margin: 5px 0 0; color: #666; }
        .section { margin-bottom: 20px; }
        .section h3 { background: #0066cc; color: #fff; padding: 8px 12px; margin: 0 0 10px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background: #f5f5f5; font-weight: bold; }
        .info-grid { display: flex; flex-wrap: wrap; gap: 20px; }
        .info-grid .col { flex: 1; min-width: 200px; }
        .info-grid .label { font-weight: bold; color: #555; }
        .total-row { background: #0066cc; color: #fff; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .badge-open { background: #e3f2fd; color: #1565c0; }
        .badge-in_progress { background: #fff3e0; color: #ef6c00; }
        .badge-completed { background: #e8f5e9; color: #2e7d32; }
        .badge-delivered { background: #f3e5f5; color: #7b1fa2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OS ASSIST</h1>
        <p>Ordem de Serviço nº {{ $order->order_number }}</p>
    </div>

    <div class="section">
        <h3>Dados da OS</h3>
        <table>
            <tr><th style="width:25%">Número</th><td>{{ $order->order_number }}</td><th style="width:25%">Status</th><td>{{ $order->status }}</td></tr>
            <tr><th>Prioridade</th><td>{{ $order->priority }}</td><th>Data de Entrada</th><td>{{ $order->entry_date ? $order->entry_date->format('d/m/Y') : '-' }}</td></tr>
            <tr><th>Previsão de Entrega</th><td>{{ $order->estimated_delivery_date ? $order->estimated_delivery_date->format('d/m/Y') : '-' }}</td><th>Garantia até</th><td>{{ $order->warranty_until ? $order->warranty_until->format('d/m/Y') : '-' }}</td></tr>
            <tr><th>Valor Estimado</th><td>R$ {{ number_format($order->estimated_value ?? 0, 2, ',', '.') }}</td><th>Valor Final</th><td>R$ {{ number_format($order->final_value ?? 0, 2, ',', '.') }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Cliente</h3>
        <table>
            <tr><th style="width:25%">Nome</th><td>{{ $order->client->name }}</td><th style="width:25%">Telefone</th><td>{{ $order->client->phone ?? '-' }}</td></tr>
            <tr><th>CPF/CNPJ</th><td>{{ $order->client->document ?? '-' }}</td><th>Email</th><td>{{ $order->client->email ?? '-' }}</td></tr>
            <tr><th>Endereço</th><td colspan="3">{{ $order->client->address ?? '-' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Equipamento</h3>
        <table>
            <tr><th style="width:25%">Categoria</th><td>{{ $order->equipment->category }}</td><th style="width:25%">Marca</th><td>{{ $order->equipment->brand }}</td></tr>
            <tr><th>Modelo</th><td>{{ $order->equipment->model }}</td><th> Nº Série</th><td>{{ $order->equipment->serial_number ?? '-' }}</td></tr>
            <tr><th>Cor</th><td>{{ $order->equipment->color ?? '-' }}</td><th>Estado Físico</th><td>{{ $order->equipment->physical_state ?? '-' }}</td></tr>
            <tr><th>Acessórios</th><td colspan="3">{{ $order->equipment->accessories_delivered ?? 'Nenhum' }}</td></tr>
            <tr><th>Defeito Relatado</th><td colspan="3">{{ $order->equipment->reported_defect ?? '-' }}</td></tr>
            <tr><th>Diagnóstico Técnico</th><td colspan="3">{{ $order->equipment->technical_diagnosis ?? '-' }}</td></tr>
        </table>
    </div>

    @if($order->technician)
    <div class="section">
        <h3>Técnico Responsável</h3>
        <table>
            <tr><th style="width:25%">Nome</th><td>{{ $order->technician->name }}</td><th style="width:25%">Email</th><td>{{ $order->technician->email }}</td></tr>
        </table>
    </div>
    @endif

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
                    <td colspan="4" style="text-align:right">Total</td>
                    <td>R$ {{ number_format($order->items->sum('total_price'), 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    @if($order->notes)
    <div class="section">
        <h3>Observações</h3>
        <p>{{ $order->notes }}</p>
    </div>
    @endif

    <div class="footer">
        OS Assist - Sistema de Ordem de Serviço | Documento gerado em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
