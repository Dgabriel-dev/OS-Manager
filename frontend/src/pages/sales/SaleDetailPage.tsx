import { useNavigate, useParams } from 'react-router-dom';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft, Loader2, Pencil, CheckCircle, XCircle, Clock } from 'lucide-react';
import { salesApi } from '@/api/sales';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useToast } from '@/components/ui/toast';
import { formatDate, formatCurrency } from '@/utils/formatters';

const paymentStatusLabels: Record<string, { label: string; variant: 'default' | 'success' | 'warning' | 'destructive' }> = {
  pending: { label: 'Pendente', variant: 'warning' },
  paid: { label: 'Pago', variant: 'success' },
  cancelled: { label: 'Cancelado', variant: 'destructive' },
};

const paymentMethodLabels: Record<string, string> = {
  cash: 'Dinheiro',
  credit_card: 'Cartão de Crédito',
  debit_card: 'Cartão de Débito',
  pix: 'PIX',
  bank_transfer: 'Transferência',
  boleto: 'Boleto',
};

export function SaleDetailPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const queryClient = useQueryClient();

  const { data: sale, isLoading } = useQuery({
    queryKey: ['sale', id],
    queryFn: () => salesApi.getById(Number(id)),
  });

  const updateStatusMutation = useMutation({
    mutationFn: (data: { payment_status: string; payment_method?: string }) =>
      salesApi.update(Number(id), data),
    onSuccess: () => {
      toast('success', 'Status atualizado com sucesso');
      queryClient.invalidateQueries({ queryKey: ['sale', id] });
      queryClient.invalidateQueries({ queryKey: ['sales'] });
      queryClient.invalidateQueries({ queryKey: ['financial-summary'] });
      queryClient.invalidateQueries({ queryKey: ['financial-revenue-by-month'] });
    },
    onError: () => {
      toast('error', 'Erro ao atualizar status');
    },
  });

  const deleteMutation = useMutation({
    mutationFn: () => salesApi.delete(Number(id)),
    onSuccess: () => {
      toast('success', 'Venda removida com sucesso');
      queryClient.invalidateQueries({ queryKey: ['sales'] });
      navigate('/sales');
    },
    onError: () => {
      toast('error', 'Erro ao remover venda');
    },
  });

  if (isLoading) {
    return (
      <div className="flex justify-center p-8">
        <Loader2 className="h-8 w-8 animate-spin" />
      </div>
    );
  }

  if (!sale) {
    return <div className="text-center p-8 text-muted-foreground">Venda não encontrada</div>;
  }

  const status = paymentStatusLabels[sale.payment_status] || { label: sale.payment_status, variant: 'default' as const };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Button variant="ghost" size="icon" onClick={() => navigate('/sales')}>
            <ArrowLeft className="h-5 w-5" />
          </Button>
          <div>
            <h1 className="text-2xl font-bold">Venda #{sale.id}</h1>
            <p className="text-muted-foreground">Criada em {formatDate(sale.created_at)}</p>
          </div>
        </div>
        <div className="flex gap-2">
          <Button variant="outline" onClick={() => navigate(`/sales/${sale.id}/edit`)}>
            <Pencil className="mr-2 h-4 w-4" />
            Editar
          </Button>
          <Button variant="destructive" onClick={() => {
            if (window.confirm('Deseja excluir esta venda?')) deleteMutation.mutate();
          }}>
            Excluir
          </Button>
        </div>
      </div>

      <div className="grid gap-4 md:grid-cols-3">
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Cliente</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-lg font-medium">{sale.client?.name || 'Venda Avulsa'}</p>
            {sale.client?.phone && (
              <p className="text-sm text-muted-foreground">{sale.client.phone}</p>
            )}
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Pagamento</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div className="flex items-center gap-2">
              <span className="text-sm text-muted-foreground">Status:</span>
              <Badge variant={status.variant}>{status.label}</Badge>
            </div>
            <div className="flex items-center gap-2">
              <span className="text-sm text-muted-foreground">Método:</span>
              <span className="text-sm font-medium">{(sale.payment_method && paymentMethodLabels[sale.payment_method]) || sale.payment_method || '-'}</span>
            </div>
            <div className="flex gap-2 pt-2">
              {sale.payment_status !== 'paid' && (
                <Button
                  size="sm"
                  variant="outline"
                  className="text-green-600 border-green-600 hover:bg-green-50"
                  onClick={() => updateStatusMutation.mutate({ payment_status: 'paid', payment_method: sale.payment_method || 'pix' })}
                  disabled={updateStatusMutation.isPending}
                >
                  <CheckCircle className="mr-1 h-4 w-4" />
                  Marcar como Pago
                </Button>
              )}
              {sale.payment_status !== 'cancelled' && (
                <Button
                  size="sm"
                  variant="outline"
                  className="text-red-600 border-red-600 hover:bg-red-50"
                  onClick={() => {
                    if (window.confirm('Deseja cancelar esta venda?')) {
                      updateStatusMutation.mutate({ payment_status: 'cancelled' });
                    }
                  }}
                  disabled={updateStatusMutation.isPending}
                >
                  <XCircle className="mr-1 h-4 w-4" />
                  Cancelar
                </Button>
              )}
              {sale.payment_status !== 'pending' && (
                <Button
                  size="sm"
                  variant="outline"
                  onClick={() => updateStatusMutation.mutate({ payment_status: 'pending' })}
                  disabled={updateStatusMutation.isPending}
                >
                  <Clock className="mr-1 h-4 w-4" />
                  Pendente
                </Button>
              )}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Totais</CardTitle>
          </CardHeader>
          <CardContent className="space-y-2">
            <div className="flex justify-between">
              <span className="text-sm text-muted-foreground">Receita Total:</span>
              <span className="text-lg font-bold text-green-600">{formatCurrency(sale.total_amount)}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-sm text-muted-foreground">Custo Total:</span>
              <span className="text-lg font-bold text-red-600">{formatCurrency(sale.total_cost)}</span>
            </div>
            <div className="flex justify-between border-t pt-2">
              <span className="text-sm font-medium">Lucro:</span>
              <span className={`text-lg font-bold ${sale.profit >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                {formatCurrency(sale.profit)}
              </span>
            </div>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="text-base">Itens da Venda</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="rounded-lg border">
            <table className="w-full text-sm">
              <thead>
                <tr className="border-b">
                  <th className="h-10 px-4 text-left font-medium text-muted-foreground">Produto</th>
                  <th className="h-10 px-4 text-left font-medium text-muted-foreground">Categoria</th>
                  <th className="h-10 px-4 text-right font-medium text-muted-foreground">Qtd</th>
                  <th className="h-10 px-4 text-right font-medium text-muted-foreground">Preço Venda</th>
                  <th className="h-10 px-4 text-right font-medium text-muted-foreground">Custo</th>
                  <th className="h-10 px-4 text-right font-medium text-muted-foreground">Lucro</th>
                  <th className="h-10 px-4 text-right font-medium text-muted-foreground">Total</th>
                </tr>
              </thead>
              <tbody>
                {sale.items?.map((item) => {
                  const itemProfit = (item.unit_price - (item.cost_price || 0)) * item.quantity;
                  return (
                    <tr key={item.id} className="border-b last:border-0">
                      <td className="p-4 font-medium">{item.name}</td>
                      <td className="p-4 text-muted-foreground">{item.category?.name || '-'}</td>
                      <td className="p-4 text-right">{item.quantity}</td>
                      <td className="p-4 text-right">{formatCurrency(item.unit_price)}</td>
                      <td className="p-4 text-right text-red-600">{formatCurrency(item.cost_price || 0)}</td>
                      <td className={`p-4 text-right font-medium ${itemProfit >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                        {formatCurrency(itemProfit)}
                      </td>
                      <td className="p-4 text-right font-medium">{formatCurrency(item.total_price)}</td>
                    </tr>
                  );
                })}
              </tbody>
              <tfoot>
                <tr className="border-t">
                  <td colSpan={4} className="p-4 text-right font-bold">Total:</td>
                  <td className="p-4 text-right font-bold text-red-600">{formatCurrency(sale.total_cost)}</td>
                  <td className={`p-4 text-right font-bold ${sale.profit >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                    {formatCurrency(sale.profit)}
                  </td>
                  <td className="p-4 text-right text-lg font-bold">{formatCurrency(sale.total_amount)}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </CardContent>
      </Card>

      {sale.notes && (
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Observações</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-sm">{sale.notes}</p>
          </CardContent>
        </Card>
      )}
    </div>
  );
}
