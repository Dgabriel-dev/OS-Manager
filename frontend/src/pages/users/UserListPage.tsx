import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useQuery } from '@tanstack/react-query';
import { Plus, Pencil, Trash2 } from 'lucide-react';
import { usersApi } from '@/api/users';
import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Avatar } from '@/components/ui/avatar';
import { useDebounce } from '@/hooks/useDebounce';
import { usePagination } from '@/hooks/usePagination';

export function UserListPage() {
  const navigate = useNavigate();
  const { page, perPage, setPage, setPerPage } = usePagination();
  const [search, setSearch] = useState('');
  const debouncedSearch = useDebounce(search);

  const { data, isLoading } = useQuery({
    queryKey: ['users', page, perPage, debouncedSearch],
    queryFn: () => usersApi.list({ page, per_page: perPage, search: debouncedSearch }),
  });

  const columns = [
    {
      key: 'name',
      header: 'Nome',
      render: (item: any) => (
        <div className="flex items-center gap-3">
          <Avatar src={item.avatar} fallback={item.name?.charAt(0)} size="sm" />
          <span className="font-medium">{item.name}</span>
        </div>
      ),
    },
    {
      key: 'email',
      header: 'E-mail',
      render: (item: any) => item.email,
    },
    {
      key: 'role',
      header: 'Perfil',
      render: (item: any) => <Badge variant="outline">{item.role?.name}</Badge>,
    },
    {
      key: 'is_active',
      header: 'Status',
      render: (item: any) => (
        <Badge variant={item.is_active ? 'success' : 'destructive'}>
          {item.is_active ? 'Ativo' : 'Inativo'}
        </Badge>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Usuários</h1>
          <p className="text-muted-foreground">Gerencie os usuários do sistema</p>
        </div>
        <Button onClick={() => navigate('/users/new')}>
          <Plus className="mr-2 h-4 w-4" />
          Novo Usuário
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data?.data || []}
        searchPlaceholder="Buscar usuário..."
        searchValue={search}
        onSearch={setSearch}
        currentPage={page}
        totalPages={data?.meta?.last_page || 1}
        totalItems={data?.meta?.total || 0}
        perPage={perPage}
        onPageChange={setPage}
        onPerPageChange={setPerPage}
        loading={isLoading}
        emptyMessage="Nenhum usuário encontrado"
        onRowClick={(item) => navigate(`/users/${item.id}`)}
        actions={(item) => (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" onClick={() => navigate(`/users/${item.id}/edit`)}>
              <Pencil className="h-4 w-4" />
            </Button>
            <Button variant="ghost" size="icon">
              <Trash2 className="h-4 w-4 text-destructive" />
            </Button>
          </div>
        )}
      />
    </div>
  );
}
