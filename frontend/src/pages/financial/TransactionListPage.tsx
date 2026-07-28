import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Plus, Pencil, Trash2 } from 'lucide-react';
import { financialApi } from '@/api/financial';
import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { useToast } from '@/components/ui/toast';
import { useDebounce } from '@/hooks/useDebounce';
import { usePagination } from '@/hooks/usePagination';
import { formatDate, formatCurrency } from '@/utils/formatters';

const typeLabels: Record<string, string> = {
  income: 'Receita',
  expense: 'Despesa',
};

const statusLabels: Record<string, { label: string; variant: 'default' | 'success' | 'warning' | 'destructive' }> = {
  pending: { label: 'Pendente', variant: 'warning' },
  paid: { label: 'Pago', variant: 'success' },
  cancelled: { label: 'Cancelado', variant: 'destructive' },
};

export function TransactionListPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { toast } = useToast();
  const { page, perPage, setPage, setPerPage } = usePagination();
  const [search, setSearch] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const debouncedSearch = useDebounce(search);

  const { data, isLoading } = useQuery({
    queryKey: ['transactions', page, perPage, debouncedSearch, typeFilter],
    queryFn: () => financialApi.listTransactions({
      page,
      per_page: perPage,
      search: debouncedSearch,
      type: typeFilter || undefined,
    }),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => financialApi.deleteTransaction(id),
    onSuccess: () => {
      toast('success', 'Transação removida com sucesso');
      queryClient.invalidateQueries({ queryKey: ['transactions'] });
    },
    onError: () => {
      toast('error', 'Erro ao remover transação');
    },
  });

  const handleDelete = (id: number) => {
    if (window.confirm('Deseja realmente excluir esta transação?')) {
      deleteMutation.mutate(id);
    }
  };

  const columns = [
    {
      key: 'description',
      header: 'Descrição',
      render: (item: any) => <span className="font-medium">{item.description}</span>,
    },
    {
      key: 'type',
      header: 'Tipo',
      render: (item: any) => (
        <Badge variant={item.type === 'income' ? 'success' : 'destructive'}>
          {typeLabels[item.type]}
        </Badge>
      ),
    },
    {
      key: 'amount',
      header: 'Valor',
      render: (item: any) => (
        <span className={item.type === 'income' ? 'text-green-600' : 'text-red-600'}>
          {item.type === 'income' ? '+' : '-'} {formatCurrency(item.amount)}
        </span>
      ),
    },
    {
      key: 'payment_method',
      header: 'Pagamento',
      render: (item: any) => item.payment_method,
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
      key: 'payment_date',
      header: 'Data',
      render: (item: any) => formatDate(item.payment_date),
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Transações</h1>
          <p className="text-muted-foreground">Gerencie as transações financeiras</p>
        </div>
        <Button onClick={() => navigate('/financial/new')}>
          <Plus className="mr-2 h-4 w-4" />
          Nova Transação
        </Button>
      </div>

      <div className="flex gap-4">
        <select
          className="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
          value={typeFilter}
          onChange={(e) => setTypeFilter(e.target.value)}
        >
          <option value="">Todos os tipos</option>
          <option value="income">Receitas</option>
          <option value="expense">Despesas</option>
        </select>
      </div>

      <DataTable
        columns={columns}
        data={data?.data || []}
        searchPlaceholder="Buscar transação..."
        searchValue={search}
        onSearch={setSearch}
        currentPage={page}
        totalPages={data?.meta?.last_page || 1}
        totalItems={data?.meta?.total || 0}
        perPage={perPage}
        onPageChange={setPage}
        onPerPageChange={setPerPage}
        loading={isLoading}
        emptyMessage="Nenhuma transação encontrada"
        onRowClick={(item) => navigate(`/financial/${item.id}`)}
        actions={(item) => (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" onClick={() => navigate(`/financial/${item.id}/edit`)}>
              <Pencil className="h-4 w-4" />
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
