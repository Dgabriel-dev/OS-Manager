import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Plus, Eye, Trash2 } from 'lucide-react';
import { serviceOrdersApi } from '@/api/serviceOrders';
import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { useToast } from '@/components/ui/toast';
import { useDebounce } from '@/hooks/useDebounce';
import { usePagination } from '@/hooks/usePagination';
import { formatDate, formatCurrency } from '@/utils/formatters';

const statusLabels: Record<string, { label: string; variant: 'default' | 'success' | 'warning' | 'info' | 'destructive' }> = {
  open: { label: 'Aberta', variant: 'info' },
  in_progress: { label: 'Em Andamento', variant: 'warning' },
  waiting_parts: { label: 'Aguardando Peças', variant: 'warning' },
  completed: { label: 'Concluída', variant: 'success' },
  delivered: { label: 'Entregue', variant: 'success' },
  cancelled: { label: 'Cancelada', variant: 'destructive' },
};

const priorityLabels: Record<string, { label: string; variant: 'default' | 'success' | 'warning' | 'destructive' }> = {
  low: { label: 'Baixa', variant: 'default' },
  medium: { label: 'Média', variant: 'warning' },
  high: { label: 'Alta', variant: 'destructive' },
  urgent: { label: 'Urgente', variant: 'destructive' },
};

export function ServiceOrderListPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { toast } = useToast();
  const { page, perPage, setPage, setPerPage } = usePagination();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const debouncedSearch = useDebounce(search);

  const { data, isLoading } = useQuery({
    queryKey: ['service-orders', page, perPage, debouncedSearch, statusFilter],
    queryFn: () => serviceOrdersApi.list({
      page,
      per_page: perPage,
      search: debouncedSearch,
      status: statusFilter || undefined,
    }),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => serviceOrdersApi.delete(id),
    onSuccess: () => {
      toast('success', 'OS removida com sucesso');
      queryClient.invalidateQueries({ queryKey: ['service-orders'] });
    },
    onError: () => {
      toast('error', 'Erro ao remover OS');
    },
  });

  const handleDelete = (id: number) => {
    if (window.confirm('Deseja realmente excluir esta OS?')) {
      deleteMutation.mutate(id);
    }
  };

  const columns = [
    {
      key: 'order_number',
      header: 'Nº OS',
      render: (item: any) => <span className="font-mono font-medium">{item.order_number}</span>,
    },
    {
      key: 'client',
      header: 'Cliente',
      render: (item: any) => item.client?.name || '-',
    },
    {
      key: 'equipment',
      header: 'Equipamento',
      render: (item: any) => `${item.equipment?.category} - ${item.equipment?.brand || ''} ${item.equipment?.model || ''}`.trim(),
    },
    {
      key: 'status',
      header: 'Status',
      render: (item: any) => {
        const status = statusLabels[item.status] || { label: item.status, variant: 'default' as const };
        return <Badge variant={status.variant}>{status.label}</Badge>;
      },
    },
    {
      key: 'priority',
      header: 'Prioridade',
      render: (item: any) => {
        const priority = priorityLabels[item.priority] || { label: item.priority, variant: 'default' as const };
        return <Badge variant={priority.variant}>{priority.label}</Badge>;
      },
    },
    {
      key: 'estimated_value',
      header: 'Valor',
      render: (item: any) => item.estimated_value ? formatCurrency(item.estimated_value) : '-',
    },
    {
      key: 'entry_date',
      header: 'Data Entrada',
      render: (item: any) => formatDate(item.entry_date),
    },
  ];

  const statusOptions = [
    { value: 'open', label: 'Aberta' },
    { value: 'in_progress', label: 'Em Andamento' },
    { value: 'waiting_parts', label: 'Aguardando Peças' },
    { value: 'completed', label: 'Concluída' },
    { value: 'delivered', label: 'Entregue' },
    { value: 'cancelled', label: 'Cancelada' },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Ordens de Serviço</h1>
          <p className="text-muted-foreground">Gerencie as ordens de serviço</p>
        </div>
        <Button onClick={() => navigate('/service-orders/new')}>
          <Plus className="mr-2 h-4 w-4" />
          Nova OS
        </Button>
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
        searchPlaceholder="Buscar OS..."
        searchValue={search}
        onSearch={setSearch}
        currentPage={page}
        totalPages={data?.meta?.last_page || 1}
        totalItems={data?.meta?.total || 0}
        perPage={perPage}
        onPageChange={setPage}
        onPerPageChange={setPerPage}
        loading={isLoading}
        emptyMessage="Nenhuma OS encontrada"
        onRowClick={(item) => navigate(`/service-orders/${item.id}`)}
        actions={(item) => (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" onClick={() => navigate(`/service-orders/${item.id}`)}>
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
