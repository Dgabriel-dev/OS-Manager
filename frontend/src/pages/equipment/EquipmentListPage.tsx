import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Plus, Pencil, Trash2 } from 'lucide-react';
import { equipmentApi } from '@/api/equipment';
import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { useToast } from '@/components/ui/toast';
import { useDebounce } from '@/hooks/useDebounce';
import { usePagination } from '@/hooks/usePagination';

export function EquipmentListPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { toast } = useToast();
  const { page, perPage, setPage, setPerPage } = usePagination();
  const [search, setSearch] = useState('');
  const debouncedSearch = useDebounce(search);

  const { data, isLoading } = useQuery({
    queryKey: ['equipment', page, perPage, debouncedSearch],
    queryFn: () => equipmentApi.list({ page, per_page: perPage, search: debouncedSearch }),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => equipmentApi.delete(id),
    onSuccess: () => {
      toast('success', 'Equipamento removido com sucesso');
      queryClient.invalidateQueries({ queryKey: ['equipment'] });
    },
    onError: () => {
      toast('error', 'Erro ao remover equipamento');
    },
  });

  const handleDelete = (id: number) => {
    if (window.confirm('Deseja realmente excluir este equipamento?')) {
      deleteMutation.mutate(id);
    }
  };

  const columns = [
    {
      key: 'category',
      header: 'Categoria',
      render: (item: any) => <Badge variant="outline">{item.category}</Badge>,
    },
    {
      key: 'brand',
      header: 'Marca',
      render: (item: any) => item.brand || '-',
    },
    {
      key: 'model',
      header: 'Modelo',
      render: (item: any) => item.model || '-',
    },
    {
      key: 'client',
      header: 'Cliente',
      render: (item: any) => item.client?.name || '-',
    },
    {
      key: 'serial_number',
      header: 'Nº Série',
      render: (item: any) => item.serial_number || '-',
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Equipamentos</h1>
          <p className="text-muted-foreground">Gerencie os equipamentos dos clientes</p>
        </div>
        <Button onClick={() => navigate('/equipment/new')}>
          <Plus className="mr-2 h-4 w-4" />
          Novo Equipamento
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data?.data || []}
        searchPlaceholder="Buscar equipamento..."
        searchValue={search}
        onSearch={setSearch}
        currentPage={page}
        totalPages={data?.meta?.last_page || 1}
        totalItems={data?.meta?.total || 0}
        perPage={perPage}
        onPageChange={setPage}
        onPerPageChange={setPerPage}
        loading={isLoading}
        emptyMessage="Nenhum equipamento encontrado"
        onRowClick={(item) => navigate(`/equipment/${item.id}`)}
        actions={(item) => (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" onClick={() => navigate(`/equipment/${item.id}/edit`)}>
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
