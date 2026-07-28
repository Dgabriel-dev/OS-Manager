import { useQuery } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import { Plus } from 'lucide-react';
import { stockApi } from '@/api/stock';
import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { usePagination } from '@/hooks/usePagination';
import { formatDateTime } from '@/utils/formatters';

const movementTypeLabels: Record<string, string> = {
  entry: 'Entrada',
  exit: 'Saída',
  adjustment: 'Ajuste',
};

export function StockMovementPage() {
  const navigate = useNavigate();
  const { page, perPage, setPage, setPerPage } = usePagination();

  const { data, isLoading } = useQuery({
    queryKey: ['stock-movements', page, perPage],
    queryFn: () => stockApi.listMovements({ page, per_page: perPage }),
  });

  const columns = [
    {
      key: 'stock_item',
      header: 'Item',
      render: (item: any) => <span className="font-medium">{item.stock_item?.name}</span>,
    },
    {
      key: 'type',
      header: 'Tipo',
      render: (item: any) => (
        <Badge variant={item.type === 'entry' ? 'success' : item.type === 'exit' ? 'destructive' : 'warning'}>
          {movementTypeLabels[item.type] || item.type}
        </Badge>
      ),
    },
    {
      key: 'quantity',
      header: 'Quantidade',
      render: (item: any) => (
        <span className={item.type === 'entry' ? 'text-green-600' : item.type === 'exit' ? 'text-red-600' : ''}>
          {item.type === 'exit' ? '-' : '+'}{item.quantity}
        </span>
      ),
    },
    {
      key: 'previous_quantity',
      header: 'Anterior',
      render: (item: any) => item.previous_quantity,
    },
    {
      key: 'new_quantity',
      header: 'Atual',
      render: (item: any) => item.new_quantity,
    },
    {
      key: 'user',
      header: 'Usuário',
      render: (item: any) => item.user?.name || '-',
    },
    {
      key: 'created_at',
      header: 'Data',
      render: (item: any) => formatDateTime(item.created_at),
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Movimentações de Estoque</h1>
          <p className="text-muted-foreground">Histórico de entradas e saídas</p>
        </div>
        <Button onClick={() => navigate('/stock/movements/new')}>
          <Plus className="mr-2 h-4 w-4" />
          Nova Movimentação
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data?.data || []}
        currentPage={page}
        totalPages={data?.meta?.last_page || 1}
        totalItems={data?.meta?.total || 0}
        perPage={perPage}
        onPageChange={setPage}
        onPerPageChange={setPerPage}
        loading={isLoading}
        emptyMessage="Nenhuma movimentação encontrada"
      />
    </div>
  );
}
