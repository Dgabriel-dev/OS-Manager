<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laudo Técnico #{{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; margin: 40px; }
        .header { text-align: center; border-bottom: 2px solid #f44336; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #f44336; font-size: 22px; }
        .section { margin-bottom: 20px; }
        .section h3 { background: #f44336; color: #fff; padding: 8px 12px; margin: 0 0 10px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background: #f5f5f5; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .signature-area { margin-top: 40px; display: flex; justify-content: space-between; }
        .signature-area .sign-block { width: 40%; text-align: center; }
        .signature-area .sign-block .line { border-top: 1px solid #333; margin-top: 60px; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OS ASSIST</h1>
        <p>Laudo Técnico nº {{ $order->order_number }}</p>
    </div>

    <div class="section">
        <h3>Identificação</h3>
        <table>
            <tr><th style="width:25%">OS Nº</th><td>{{ $order->order_number }}</td><th style="width:25%">Data</th><td>{{ $order->created_at->format('d/m/Y') }}</td></tr>
            <tr><th>Técnico</th><td>{{ $order->technician->name ?? '-' }}</td><th>Status</th><td>{{ $order->status }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Cliente</h3>
        <table>
            <tr><th style="width:25%">Nome</th><td>{{ $order->client->name }}</td><th style="width:25%">Telefone</th><td>{{ $order->client->phone ?? '-' }}</td></tr>
            <tr><th>CPF/CNPJ</th><td>{{ $order->client->document ?? '-' }}</td><th>Endereço</th><td>{{ $order->client->address ?? '-' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Equipamento</h3>
        <table>
            <tr><th style="width:25%">Categoria</th><td>{{ $order->equipment->category }}</td><th style="width:25%">Marca</th><td>{{ $order->equipment->brand }}</td></tr>
            <tr><th>Modelo</th><td>{{ $order->equipment->model }}</td><th>Nº Série</th><td>{{ $order->equipment->serial_number ?? '-' }}</td></tr>
            <tr><th>Cor</th><td>{{ $order->equipment->color ?? '-' }}</td><th>Estado Físico</th><td>{{ $order->equipment->physical_state ?? '-' }}</td></tr>
            <tr><th>Acessórios</th><td colspan="3">{{ $order->equipment->accessories_delivered ?? 'Nenhum' }}</td></tr>
            <tr><th>Senha</th><td colspan="3">{{ $order->equipment->password_encrypted ? '****' : 'Não informada' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Diagnóstico</h3>
        <table>
            <tr><th style="width:25%">Defeito Relatado</th><td>{{ $order->equipment->reported_defect ?? '-' }}</td></tr>
            <tr><th>Diagnóstico Técnico</th><td>{{ $order->equipment->technical_diagnosis ?? '-' }}</td></tr>
        </table>
    </div>

    @if($order->items->count())
    <div class="section">
        <h3>Peças e Serviços Utilizados</h3>
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
            </tbody>
        </table>
    </div>
    @endif

    @if($order->histories->count())
    <div class="section">
        <h3>Histórico de Atividades</h3>
        <table>
            <thead>
                <tr><th>Data</th><th>Usuário</th><th>De</th><th>Para</th><th>Observação</th></tr>
            </thead>
            <tbody>
                @foreach($order->histories as $history)
                <tr>
                    <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $history->user->name ?? '-' }}</td>
                    <td>{{ $history->old_status ?? '-' }}</td>
                    <td>{{ $history->new_status ?? '-' }}</td>
                    <td>{{ $history->observation ?? '-' }}</td>
                </tr>
                @endforeach
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

    <div class="section">
        <h3>Valor</h3>
        <table>
            <tr><th style="width:25%">Valor Estimado</th><td>R$ {{ number_format($order->estimated_value ?? 0, 2, ',', '.') }}</td></tr>
            <tr><th>Valor Final</th><td>R$ {{ number_format($order->final_value ?? 0, 2, ',', '.') }}</td></tr>
        </table>
    </div>

    <div class="signature-area">
        <div class="sign-block">
            <div class="line">Técnico Responsável</div>
        </div>
        <div class="sign-block">
            <div class="line">Cliente</div>
        </div>
    </div>

    <div class="footer">
        OS Assist - Sistema de Ordem de Serviço | Documento gerado em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
