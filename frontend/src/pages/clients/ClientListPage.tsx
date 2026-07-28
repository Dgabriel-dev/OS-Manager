import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useQuery } from '@tanstack/react-query';
import { Plus, Pencil, Trash2 } from 'lucide-react';
import { clientsApi } from '@/api/clients';
import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { useDebounce } from '@/hooks/useDebounce';
import { usePagination } from '@/hooks/usePagination';
import { formatCPF_CNPJ, formatPhone } from '@/utils/formatters';

export function ClientListPage() {
  const navigate = useNavigate();
  const { page, perPage, setPage, setPerPage } = usePagination();
  const [search, setSearch] = useState('');
  const debouncedSearch = useDebounce(search);

  const { data, isLoading } = useQuery({
    queryKey: ['clients', page, perPage, debouncedSearch],
    queryFn: () => clientsApi.list({ page, per_page: perPage, search: debouncedSearch }),
  });

  const columns = [
    {
      key: 'name',
      header: 'Nome',
      render: (item: any) => <span className="font-medium">{item.name}</span>,
    },
    {
      key: 'cpf_cnpj',
      header: 'CPF/CNPJ',
      render: (item: any) => formatCPF_CNPJ(item.cpf_cnpj),
    },
    {
      key: 'phone',
      header: 'Telefone',
      render: (item: any) => formatPhone(item.phone),
    },
    {
      key: 'email',
      header: 'E-mail',
      render: (item: any) => item.email || '-',
    },
    {
      key: 'orders_count',
      header: 'OS',
      render: (item: any) => <Badge variant="info">{item.orders_count || 0}</Badge>,
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Clientes</h1>
          <p className="text-muted-foreground">Gerencie seus clientes</p>
        </div>
        <Button onClick={() => navigate('/clients/new')}>
          <Plus className="mr-2 h-4 w-4" />
          Novo Cliente
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data?.data || []}
        searchPlaceholder="Buscar cliente..."
        searchValue={search}
        onSearch={setSearch}
        currentPage={page}
        totalPages={data?.meta?.last_page || 1}
        totalItems={data?.meta?.total || 0}
        perPage={perPage}
        onPageChange={setPage}
        onPerPageChange={setPerPage}
        loading={isLoading}
        emptyMessage="Nenhum cliente encontrado"
        onRowClick={(item) => navigate(`/clients/${item.id}`)}
        actions={(item) => (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" onClick={() => navigate(`/clients/${item.id}/edit`)}>
              <Pencil className="h-4 w-4" />
            </Button>
            <Button variant="ghost" size="icon" onClick={() => {/* delete */}}>
              <Trash2 className="h-4 w-4 text-destructive" />
            </Button>
          </div>
        )}
      />
    </div>
  );
}
