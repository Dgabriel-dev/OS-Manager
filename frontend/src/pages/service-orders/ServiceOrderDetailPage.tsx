import { useParams, useNavigate } from 'react-router-dom';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft, Pencil, Clock, User, Wrench, Calendar, FileText, Download, FileCheck, Shield, ClipboardList } from 'lucide-react';
import { serviceOrdersApi } from '@/api/serviceOrders';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { LoadingPage } from '@/components/ui/loading-spinner';
import { useToast } from '@/components/ui/toast';
import { formatDate, formatDateTime, formatCurrency } from '@/utils/formatters';

const statusLabels: Record<string, string> = {
  open: 'Aberta',
  in_progress: 'Em Andamento',
  waiting_parts: 'Aguardando Peças',
  completed: 'Concluída',
  delivered: 'Entregue',
  cancelled: 'Cancelada',
};

const statusColors: Record<string, 'default' | 'success' | 'warning' | 'info' | 'destructive'> = {
  open: 'info',
  in_progress: 'warning',
  waiting_parts: 'warning',
  completed: 'success',
  delivered: 'success',
  cancelled: 'destructive',
};

export function ServiceOrderDetailPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const queryClient = useQueryClient();

  const { data: order, isLoading } = useQuery({
    queryKey: ['service-order', id],
    queryFn: () => serviceOrdersApi.getById(Number(id)),
  });

  const { data: history } = useQuery({
    queryKey: ['service-order-history', id],
    queryFn: () => serviceOrdersApi.getHistory(Number(id)),
  });

  const statusMutation = useMutation({
    mutationFn: (status: string) => serviceOrdersApi.updateStatus(Number(id), status),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['service-order', id] });
      toast('success', 'Status atualizado');
    },
    onError: () => {
      toast('error', 'Erro ao atualizar status');
    },
  });

  const downloadPdf = async (type: string) => {
    try {
      const apiHost = window.location.hostname;
      const baseUrl = import.meta.env.VITE_API_URL || `http://${apiHost}:8080/api`;
      const response = await fetch(`${baseUrl}/pdf/${type}/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      });
      if (!response.ok) throw new Error('Erro ao gerar PDF');
      const blob = await response.blob();
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `${type}-${order?.order_number || id}.pdf`;
      document.body.appendChild(a);
      a.click();
      window.URL.revokeObjectURL(url);
      a.remove();
    } catch {
      toast('error', 'Erro ao gerar PDF');
    }
  };

  if (isLoading) return <LoadingPage />;
  if (!order) return <div className="text-center p-8 text-muted-foreground">OS não encontrada</div>;

  const nextStatuses: Record<string, string[]> = {
    open: ['in_progress', 'cancelled'],
    in_progress: ['waiting_parts', 'completed'],
    waiting_parts: ['in_progress', 'cancelled'],
    completed: ['delivered'],
    delivered: [],
    cancelled: [],
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Button variant="ghost" size="icon" onClick={() => navigate('/service-orders')}>
            <ArrowLeft className="h-5 w-5" />
          </Button>
          <div>
            <h1 className="text-2xl font-bold">OS {order.order_number}</h1>
            <p className="text-muted-foreground">
              Criada em {formatDateTime(order.created_at)}
            </p>
          </div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="outline" size="sm" onClick={() => downloadPdf('service-order')}>
            <FileText className="mr-2 h-4 w-4" />
            OS
          </Button>
          <Button variant="outline" size="sm" onClick={() => downloadPdf('budget')}>
            <Download className="mr-2 h-4 w-4" />
            Orçamento
          </Button>
          <Button variant="outline" size="sm" onClick={() => downloadPdf('warranty')}>
            <Shield className="mr-2 h-4 w-4" />
            Garantia
          </Button>
          <Button variant="outline" size="sm" onClick={() => downloadPdf('technical-report')}>
            <ClipboardList className="mr-2 h-4 w-4" />
            Laudo
          </Button>
          <Button onClick={() => navigate(`/service-orders/${id}/edit`)}>
            <Pencil className="mr-2 h-4 w-4" />
            Editar
          </Button>
        </div>
      </div>

      <div className="grid gap-6 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Dados da OS</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div className="flex justify-between">
              <span className="text-muted-foreground">Status</span>
              <Badge variant={statusColors[order.status]}>{statusLabels[order.status]}</Badge>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Prioridade</span>
              <span className="font-medium capitalize">{order.priority}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Valor Estimado</span>
              <span className="font-medium">{order.estimated_value ? formatCurrency(order.estimated_value) : '-'}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Valor Final</span>
              <span className="font-medium">{order.final_value ? formatCurrency(order.final_value) : '-'}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Garantia</span>
              <span className="font-medium">{order.warranty_days} dias</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Data Entrada</span>
              <span className="font-medium">{formatDate(order.entry_date)}</span>
            </div>
            {order.estimated_delivery_date && (
              <div className="flex justify-between">
                <span className="text-muted-foreground">Previsão Entrega</span>
                <span className="font-medium">{formatDate(order.estimated_delivery_date)}</span>
              </div>
            )}
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Cliente e Equipamento</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div className="flex items-center gap-2">
              <User className="h-4 w-4 text-muted-foreground" />
              <span className="font-medium">{order.client?.name}</span>
            </div>
            <div className="flex items-center gap-2">
              <Wrench className="h-4 w-4 text-muted-foreground" />
              <span>{order.equipment?.category} - {order.equipment?.brand} {order.equipment?.model}</span>
            </div>
            {order.technician && (
              <div className="flex items-center gap-2">
                <User className="h-4 w-4 text-muted-foreground" />
                <span>Técnico: {order.technician.name}</span>
              </div>
            )}
          </CardContent>
        </Card>
      </div>

      {order.items && order.items.length > 0 && (
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Itens / Serviços</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="rounded-lg border">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b bg-muted/50">
                    <th className="p-2 text-left">Descrição</th>
                    <th className="p-2 text-center">Qtd</th>
                    <th className="p-2 text-right">Preço Unit.</th>
                    <th className="p-2 text-right">Total</th>
                  </tr>
                </thead>
                <tbody>
                  {order.items.map((item) => (
                    <tr key={item.id} className="border-b">
                      <td className="p-2">{item.stock_item?.name || item.description || '-'}</td>
                      <td className="p-2 text-center">{item.quantity}</td>
                      <td className="p-2 text-right">{formatCurrency(item.unit_price)}</td>
                      <td className="p-2 text-right">{formatCurrency(item.total_price)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>
      )}

      {order.notes && (
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Observações</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-sm whitespace-pre-wrap">{order.notes}</p>
          </CardContent>
        </Card>
      )}

      {nextStatuses[order.status] && nextStatuses[order.status].length > 0 && (
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Alterar Status</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex gap-2">
              {nextStatuses[order.status].map((status) => (
                <Button
                  key={status}
                  variant="outline"
                  onClick={() => statusMutation.mutate(status)}
                  disabled={statusMutation.isPending}
                >
                  {statusLabels[status]}
                </Button>
              ))}
            </div>
          </CardContent>
        </Card>
      )}

      {history && history.length > 0 && (
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Histórico</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {history.map((item) => (
                <div key={item.id} className="flex gap-4">
                  <Clock className="h-4 w-4 text-muted-foreground mt-1 shrink-0" />
                  <div>
                    <p className="text-sm font-medium">{item.action}</p>
                    <p className="text-xs text-muted-foreground">
                      {item.user?.name} - {formatDateTime(item.created_at)}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      )}
    </div>
  );
}
