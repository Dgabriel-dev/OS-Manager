import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft, Loader2, Plus } from 'lucide-react';
import { salesApi } from '@/api/sales';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useToast } from '@/components/ui/toast';

export function SaleCategoryListPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { toast } = useToast();
  const [newCategoryName, setNewCategoryName] = useState('');

  const { data: categories, isLoading } = useQuery({
    queryKey: ['sale-categories'],
    queryFn: salesApi.listCategories,
  });

  const createMutation = useMutation({
    mutationFn: (name: string) => salesApi.createCategory({ name }),
    onSuccess: () => {
      toast('success', 'Categoria criada com sucesso');
      queryClient.invalidateQueries({ queryKey: ['sale-categories'] });
      setNewCategoryName('');
    },
    onError: () => {
      toast('error', 'Erro ao criar categoria');
    },
  });

  const handleCreate = () => {
    if (!newCategoryName.trim()) return;
    createMutation.mutate(newCategoryName.trim());
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" onClick={() => navigate('/sales')}>
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <div>
          <h1 className="text-2xl font-bold">Categorias de Venda</h1>
          <p className="text-muted-foreground">Gerencie as categorias de produtos vendidos</p>
        </div>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="text-base">Nova Categoria</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex gap-2">
            <Input
              placeholder="Nome da categoria (ex: Notebooks, Mouses, Teclados...)"
              value={newCategoryName}
              onChange={(e) => setNewCategoryName(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && handleCreate()}
            />
            <Button onClick={handleCreate} disabled={createMutation.isPending || !newCategoryName.trim()}>
              {createMutation.isPending ? (
                <Loader2 className="h-4 w-4 animate-spin" />
              ) : (
                <Plus className="h-4 w-4" />
              )}
            </Button>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="text-base">Categorias Existentes</CardTitle>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="flex justify-center p-4">
              <Loader2 className="h-6 w-6 animate-spin" />
            </div>
          ) : categories && categories.length > 0 ? (
            <div className="space-y-2">
              {categories.map((category) => (
                <div
                  key={category.id}
                  className="flex items-center justify-between rounded-lg border p-3"
                >
                  <span className="font-medium">{category.name}</span>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-sm text-muted-foreground text-center p-4">Nenhuma categoria cadastrada</p>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
