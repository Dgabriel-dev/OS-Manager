<?php

namespace Database\Seeders;

use App\Models\PdfTemplate;
use Illuminate\Database\Seeder;

class PdfTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Ordem de Serviço',
                'slug' => 'service-order',
                'is_default' => true,
                'content' => $this->getServiceOrderTemplate(),
            ],
            [
                'name' => 'Orçamento',
                'slug' => 'budget',
                'is_default' => true,
                'content' => $this->getBudgetTemplate(),
            ],
            [
                'name' => 'Recibo',
                'slug' => 'receipt',
                'is_default' => true,
                'content' => $this->getReceiptTemplate(),
            ],
            [
                'name' => 'Laudo Técnico',
                'slug' => 'technical-report',
                'is_default' => true,
                'content' => $this->getTechnicalReportTemplate(),
            ],
            [
                'name' => 'Garantia',
                'slug' => 'warranty',
                'is_default' => true,
                'content' => $this->getWarrantyTemplate(),
            ],
        ];

        foreach ($templates as $template) {
            PdfTemplate::create($template);
        }
    }

    private function getServiceOrderTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18pt; }
        .header p { margin: 5px 0 0; color: #666; }
        .order-number { text-align: center; font-size: 14pt; font-weight: bold; color: #2563eb; margin: 10px 0; }
        .section { margin-bottom: 15px; }
        .section h3 { background: #f3f4f6; padding: 5px 10px; margin: 0 0 10px; font-size: 11pt; }
        .field { margin-bottom: 5px; }
        .field label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
        .footer { margin-top: 30px; text-align: center; font-size: 8pt; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OS Assist</h1>
        <p>Sistema de Ordem de Serviço</p>
    </div>
    <div class="order-number">{{ order_number }}</div>
    <div class="section">
        <h3>Dados do Cliente</h3>
        <div class="field"><label>Nome:</label> {{ client_name }}</div>
        <div class="field"><label>CPF/CNPJ:</label> {{ client_cpf_cnpj }}</div>
        <div class="field"><label>Telefone:</label> {{ client_phone }}</div>
        <div class="field"><label>E-mail:</label> {{ client_email }}</div>
        <div class="field"><label>Endereço:</label> {{ client_address }}</div>
    </div>
    <div class="section">
        <h3>Dados do Equipamento</h3>
        <div class="field"><label>Categoria:</label> {{ equipment_category }}</div>
        <div class="field"><label>Marca/Modelo:</label> {{ equipment_brand_model }}</div>
        <div class="field"><label>Nº de Série:</label> {{ equipment_serial }}</div>
        <div class="field"><label>Cor:</label> {{ equipment_color }}</div>
        <div class="field"><label>Acessórios:</label> {{ equipment_accessories }}</div>
        <div class="field"><label>Estado Físico:</label> {{ equipment_physical_state }}</div>
        <div class="field"><label>Defeito Informado:</label> {{ equipment_defect }}</div>
    </div>
    <div class="section">
        <h3>Detalhes da Ordem de Serviço</h3>
        <div class="field"><label>Técnico:</label> {{ technician_name }}</div>
        <div class="field"><label>Prioridade:</label> {{ priority }}</div>
        <div class="field"><label>Status:</label> {{ status }}</div>
        <div class="field"><label>Data de Entrada:</label> {{ entry_date }}</div>
        <div class="field"><label>Previsão de Entrega:</label> {{ estimated_delivery_date }}</div>
        <div class="field"><label>Valor Estimado:</label> {{ estimated_value }}</div>
        <div class="field"><label>Valor Final:</label> {{ final_value }}</div>
    </div>
    <div class="section">
        <h3>Peças e Serviços</h3>
        <table>
            <thead>
                <tr><th>Descrição</th><th>Tipo</th><th>Qtd</th><th>Valor Unit.</th><th>Total</th></tr>
            </thead>
            <tbody>
                @items
            </tbody>
        </table>
    </div>
    <div class="section">
        <h3>Observações</h3>
        <p>{{ notes }}</p>
    </div>
    <div class="section">
        <h3>Diagnóstico Técnico</h3>
        <p>{{ technical_diagnosis }}</p>
    </div>
    <div class="footer">
        <p>OS Assist - Sistema de Ordem de Serviço</p>
        <p>Documento gerado em {{ generated_at }}</p>
    </div>
</body>
</html>';
    }

    private function getBudgetTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
        .total { font-size: 14pt; font-weight: bold; text-align: right; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OS Assist</h1>
        <p>Orçamento - {{ order_number }}</p>
    </div>
    <div class="section">
        <h3>Cliente: {{ client_name }}</h3>
        <p>Equipamento: {{ equipment_category }} - {{ equipment_brand_model }}</p>
    </div>
    <table>
        <thead><tr><th>Descrição</th><th>Tipo</th><th>Qtd</th><th>Valor Unit.</th><th>Total</th></tr></thead>
        <tbody>@items</tbody>
    </table>
    <div class="total">Valor Total: {{ estimated_value }}</div>
    <p style="margin-top: 20px; font-size: 9pt; color: #666;">Validade do orçamento: 15 dias</p>
</body>
</html>';
    }

    private function getReceiptTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18pt; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OS Assist</h1>
        <p>Recibo de Pagamento</p>
    </div>
    <p>Recebemos de <strong>{{ client_name }}</strong> o valor de <strong>{{ amount }}</strong> referente à OS <strong>{{ order_number }}</strong>.</p>
    <p>Forma de pagamento: {{ payment_method }}</p>
    <p>Data: {{ payment_date }}</p>
    <br><br>
    <p>_______________________________</p>
    <p>Assinatura</p>
</body>
</html>';
    }

    private function getTechnicalReportTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18pt; }
        .section { margin-bottom: 15px; }
        .section h3 { background: #f3f4f6; padding: 5px 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OS Assist</h1>
        <p>Laudo Técnico</p>
    </div>
    <div class="section">
        <h3>Ordem de Serviço: {{ order_number }}</h3>
        <p><strong>Cliente:</strong> {{ client_name }}</p>
        <p><strong>Equipamento:</strong> {{ equipment_category }} - {{ equipment_brand_model }}</p>
        <p><strong>Nº Série:</strong> {{ equipment_serial }}</p>
        <p><strong>Técnico:</strong> {{ technician_name }}</p>
    </div>
    <div class="section">
        <h3>Diagnóstico</h3>
        <p>{{ technical_diagnosis }}</p>
    </div>
    <div class="section">
        <h3>Procedimentos Realizados</h3>
        <p>{{ procedures }}</p>
    </div>
    <div class="section">
        <h3>Peças Utilizadas</h3>
        <table>
            <thead><tr><th>Descrição</th><th>Qtd</th></tr></thead>
            <tbody>@items</tbody>
        </table>
    </div>
    <div class="section">
        <h3>Conclusão</h3>
        <p>{{ conclusion }}</p>
    </div>
</body>
</html>';
    }

    private function getWarrantyTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18pt; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OS Assist</h1>
        <p>Certificado de Garantia</p>
    </div>
    <p>Certificamos que o equipamento abaixo foi reparado e encontra-se em perfeitas condições de funcionamento.</p>
    <p><strong>OS:</strong> {{ order_number }}</p>
    <p><strong>Cliente:</strong> {{ client_name }}</p>
    <p><strong>Equipamento:</strong> {{ equipment_category }} - {{ equipment_brand_model }}</p>
    <p><strong>Data de Entrega:</strong> {{ delivered_at }}</p>
    <p><strong>Garantia:</strong> {{ warranty_days }} dias</p>
    <p><strong>Válido até:</strong> {{ warranty_until }}</p>
    <br>
    <p><strong>Condições da Garantia:</strong></p>
    <ul>
        <li>Esta garantia cobre apenas o serviço executado</li>
        <li>Não cobre danos causados por mau uso</li>
        <li>Apresentar este documento na assistência</li>
    </ul>
</body>
</html>';
    }
}
