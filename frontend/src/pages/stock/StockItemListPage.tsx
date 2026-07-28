import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Plus, Pencil, Trash2, AlertTriangle } from 'lucide-react';
import { stockApi } from '@/api/stock';
import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { useToast } from '@/components/ui/toast';
import { useDebounce } from '@/hooks/useDebounce';
import { usePagination } from '@/hooks/usePagination';
import { formatCurrency } from '@/utils/formatters';

export function StockItemListPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { toast } = useToast();
  const { page, perPage, setPage, setPerPage } = usePagination();
  const [search, setSearch] = useState('');
  const debouncedSearch = useDebounce(search);

  const { data, isLoading } = useQuery({
    queryKey: ['stock-items', page, perPage, debouncedSearch],
    queryFn: () => stockApi.listItems({ page, per_page: perPage, search: debouncedSearch }),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => stockApi.deleteItem(id),
    onSuccess: () => {
      toast('success', 'Item removido com sucesso');
      queryClient.invalidateQueries({ queryKey: ['stock-items'] });
    },
    onError: () => {
      toast('error', 'Erro ao remover item');
    },
  });

  const handleDelete = (id: number) => {
    if (window.confirm('Deseja realmente excluir este item do estoque?')) {
      deleteMutation.mutate(id);
    }
  };

  const columns = [
    {
      key: 'name',
      header: 'Nome',
      render: (item: any) => (
        <div className="flex items-center gap-2">
          <span className="font-medium">{item.name}</span>
          {item.is_low_stock && <AlertTriangle className="h-4 w-4 text-yellow-500" />}
        </div>
      ),
    },
    {
      key: 'internal_code',
      header: 'Código',
      render: (item: any) => item.internal_code || '-',
    },
    {
      key: 'category',
      header: 'Categoria',
      render: (item: any) => item.category?.name || '-',
    },
    {
      key: 'quantity',
      header: 'Qtd',
      render: (item: any) => (
        <Badge variant={item.is_low_stock ? 'destructive' : 'default'}>
          {item.quantity}
        </Badge>
      ),
    },
    {
      key: 'purchase_price',
      header: 'Preço Compra',
      render: (item: any) => formatCurrency(item.purchase_price),
    },
    {
      key: 'sale_price',
      header: 'Preço Venda',
      render: (item: any) => formatCurrency(item.sale_price),
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Estoque</h1>
          <p className="text-muted-foreground">Gerencie seus itens em estoque</p>
        </div>
        <Button onClick={() => navigate('/stock/new')}>
          <Plus className="mr-2 h-4 w-4" />
          Novo Item
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data?.data || []}
        searchPlaceholder="Buscar item..."
        searchValue={search}
        onSearch={setSearch}
        currentPage={page}
        totalPages={data?.meta?.last_page || 1}
        totalItems={data?.meta?.total || 0}
        perPage={perPage}
        onPageChange={setPage}
        onPerPageChange={setPerPage}
        loading={isLoading}
        emptyMessage="Nenhum item encontrado"
        onRowClick={(item) => navigate(`/stock/${item.id}`)}
        actions={(item) => (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" onClick={() => navigate(`/stock/${item.id}/edit`)}>
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
