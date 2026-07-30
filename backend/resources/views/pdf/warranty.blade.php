<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Garantia #{{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; margin: 40px; }
        .header { text-align: center; border-bottom: 2px solid #9c27b0; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #9c27b0; font-size: 22px; }
        .section { margin-bottom: 20px; }
        .section h3 { background: #9c27b0; color: #fff; padding: 8px 12px; margin: 0 0 10px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background: #f5f5f5; font-weight: bold; width: 30%; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .warranty-box { border: 2px solid #9c27b0; padding: 15px; margin: 20px 0; text-align: center; }
        .warranty-box h2 { margin: 0; color: #9c27b0; }
        .warranty-box p { margin: 5px 0 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OS ASSIST</h1>
        <p>Certificado de Garantia</p>
    </div>

    <div class="warranty-box">
        <h2>GARANTIA DE {{ $order->warranty_days ?? 30 }} DIAS</h2>
        <p>Válida até {{ $order->warranty_until ? $order->warranty_until->format('d/m/Y') : '-' }}</p>
    </div>

    <div class="section">
        <h3>Dados da OS</h3>
        <table>
            <tr><th>Número</th><td>{{ $order->order_number }}</td></tr>
            <tr><th>Data de Entrada</th><td>{{ $order->entry_date ? $order->entry_date->format('d/m/Y') : '-' }}</td></tr>
            <tr><th>Valor Total</th><td>R$ {{ number_format($order->final_value ?? $order->estimated_value ?? 0, 2, ',', '.') }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Cliente</h3>
        <table>
            <tr><th>Nome</th><td>{{ $order->client->name }}</td></tr>
            <tr><th>Telefone</th><td>{{ $order->client->phone ?? '-' }}</td></tr>
            <tr><th>CPF/CNPJ</th><td>{{ $order->client->document ?? '-' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Equipamento</h3>
        <table>
            <tr><th>Categoria</th><td>{{ $order->equipment->category }}</td></tr>
            <tr><th>Marca / Modelo</th><td>{{ $order->equipment->brand }} {{ $order->equipment->model }}</td></tr>
            <tr><th>Nº Série</th><td>{{ $order->equipment->serial_number ?? '-' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Termos de Garantia</h3>
        <p>1. A garantia cobre exclusivamente o serviço realizado, conforme descrito na Ordem de Serviço.</p>
        <p>2. Não estão cobertos danos causados por mau uso, quedas, líquidos, ou intervenções de terceiros.</p>
        <p>3. Para fazer jus à garantia, o cliente deverá apresentar este certificado e o comprovante de pagamento.</p>
        <p>4. A garantia é pessoal e intransferível.</p>
    </div>

    <div class="footer">
        OS Assist - Sistema de Ordem de Serviço | Documento gerado em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
