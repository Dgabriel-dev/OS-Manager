import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Plus, Eye, Trash2 } from 'lucide-react';
import { salesApi } from '@/api/sales';
import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { useToast } from '@/components/ui/toast';
import { useDebounce } from '@/hooks/useDebounce';
import { usePagination } from '@/hooks/usePagination';
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

export function SaleListPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { toast } = useToast();
  const { page, perPage, setPage, setPerPage } = usePagination();
  const [statusFilter, setStatusFilter] = useState('');
  const debouncedSearch = '';

  const { data, isLoading } = useQuery({
    queryKey: ['sales', page, perPage, statusFilter],
    queryFn: () => salesApi.list({
      page,
      per_page: perPage,
      payment_status: statusFilter || undefined,
    }),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => salesApi.delete(id),
    onSuccess: () => {
      toast('success', 'Venda removida com sucesso');
      queryClient.invalidateQueries({ queryKey: ['sales'] });
    },
    onError: () => {
      toast('error', 'Erro ao remover venda');
    },
  });

  const handleDelete = (id: number) => {
    if (window.confirm('Deseja realmente excluir esta venda?')) {
      deleteMutation.mutate(id);
    }
  };

  const columns = [
    {
      key: 'id',
      header: 'Nº Venda',
      render: (item: any) => <span className="font-mono font-medium">#{item.id}</span>,
    },
    {
      key: 'client',
      header: 'Cliente',
      render: (item: any) => item.client?.name || 'Venda Avulsa',
    },
    {
      key: 'items',
      header: 'Itens',
      render: (item: any) => `${item.items?.length || 0} item(s)`,
    },
    {
      key: 'total_amount',
      header: 'Total',
      render: (item: any) => formatCurrency(item.total_amount),
    },
    {
      key: 'payment_method',
      header: 'Pagamento',
      render: (item: any) => paymentMethodLabels[item.payment_method] || item.payment_method || '-',
    },
    {
      key: 'payment_status',
      header: 'Status',
      render: (item: any) => {
        const status = paymentStatusLabels[item.payment_status] || { label: item.payment_status, variant: 'default' as const };
        return <Badge variant={status.variant}>{status.label}</Badge>;
      },
    },
    {
      key: 'created_at',
      header: 'Data',
      render: (item: any) => formatDate(item.created_at),
    },
  ];

  const statusOptions = [
    { value: 'pending', label: 'Pendente' },
    { value: 'paid', label: 'Pago' },
    { value: 'cancelled', label: 'Cancelado' },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Vendas</h1>
          <p className="text-muted-foreground">Gerencie as vendas de produtos</p>
        </div>
        <div className="flex gap-2">
          <Button variant="outline" onClick={() => navigate('/sales/categories')}>
            Categorias
          </Button>
          <Button onClick={() => navigate('/sales/new')}>
            <Plus className="mr-2 h-4 w-4" />
            Nova Venda
          </Button>
        </div>
      </div>

      <div className="flex gap-4">
        <select
          className="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
          value={statusFilter}
          onChange={(e) => setStatusFilter(e.target.value)}
        >
          <option value="">Todos os status</option>
          {statusOptions.map((opt) => (
            <option key={opt.value} value={opt.value}>{opt.label}</option>
          ))}
        </select>
      </div>

      <DataTable
        columns={columns}
        data={data?.data || []}
        searchPlaceholder="Buscar venda..."
        searchValue=""
        onSearch={() => {}}
        currentPage={page}
        totalPages={data?.meta?.last_page || 1}
        totalItems={data?.meta?.total || 0}
        perPage={perPage}
        onPageChange={setPage}
        onPerPageChange={setPerPage}
        loading={isLoading}
        emptyMessage="Nenhuma venda encontrada"
        onRowClick={(item) => navigate(`/sales/${item.id}`)}
        actions={(item) => (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" onClick={() => navigate(`/sales/${item.id}`)}>
              <Eye className="h-4 w-4" />
            </Button>
            <Button variant="ghost" size="icon" onClick={() => handleDelete(item.id)}>
              <Trash2 className="h-4 w-4 text-destructive" />
            </Button>
          </div>
        )}
      />
    </div>
  );
}
