import { useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Loader2, ArrowLeft } from 'lucide-react';
import { stockApi } from '@/api/stock';
import { stockItemSchema, type StockItemFormData } from '@/utils/validators';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { useToast } from '@/components/ui/toast';

export function StockItemFormPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const queryClient = useQueryClient();
  const isEditing = !!id;

  const { data: item, isLoading: loadingItem } = useQuery({
    queryKey: ['stock-item', id],
    queryFn: () => stockApi.getItemById(Number(id)),
    enabled: isEditing,
  });

  const { data: categories } = useQuery({
    queryKey: ['stock-categories'],
    queryFn: stockApi.listCategories,
  });

  const {
    register,
    handleSubmit,
    setValue,
    formState: { errors },
  } = useForm<StockItemFormData>({
    resolver: zodResolver(stockItemSchema) as any,
  });

  useEffect(() => {
    if (item) {
      setValue('name', item.name);
      setValue('internal_code', item.internal_code);
      setValue('barcode', item.barcode);
      setValue('category_id', item.category?.id ?? null);
      setValue('supplier', item.supplier);
      setValue('purchase_price', item.purchase_price);
      setValue('sale_price', item.sale_price);
      setValue('quantity', item.quantity);
      setValue('minimum_quantity', item.minimum_quantity);
      setValue('location', item.location);
    }
  }, [item, setValue]);

  const mutation = useMutation({
    mutationFn: (data: StockItemFormData) =>
      isEditing ? stockApi.updateItem(Number(id), data) : stockApi.createItem(data),
    onSuccess: () => {
      toast('success', isEditing ? 'Item atualizado' : 'Item criado');
      queryClient.invalidateQueries({ queryKey: ['stock-items'] });
      navigate('/stock');
    },
    onError: () => {
      toast('error', 'Erro ao salvar item');
    },
  });

  const categoryOptions = (categories || []).map((c) => ({ value: c.id, label: c.name }));

  if (isEditing && loadingItem) {
    return <div className="flex justify-center p-8"><Loader2 className="h-8 w-8 animate-spin" /></div>;
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" onClick={() => navigate('/stock')}>
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <div>
          <h1 className="text-2xl font-bold">{isEditing ? 'Editar Item' : 'Novo Item'}</h1>
          <p className="text-muted-foreground">Preencha os dados do item</p>
        </div>
      </div>

      <form onSubmit={handleSubmit((data) => mutation.mutate(data))}>
        <Card>
          <CardHeader>
            <CardTitle>Dados do Item</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <Input label="Nome *" error={errors.name?.message} {...register('name')} />
            <div className="grid gap-4 md:grid-cols-2">
              <Input label="Código Interno" {...register('internal_code')} />
              <Input label="Código de Barras" {...register('barcode')} />
            </div>
            <div className="grid gap-4 md:grid-cols-2">
              <Input label="Fornecedor" {...register('supplier')} />
              <div className="space-y-1">
                <label className="text-sm font-medium">Categoria</label>
                <select
                  className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                  {...register('category_id', { valueAsNumber: true })}
                >
                  <option value="">Selecione</option>
                  {categoryOptions.map((opt) => (
                    <option key={opt.value} value={opt.value}>{opt.label}</option>
                  ))}
                </select>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card className="mt-6">
          <CardHeader>
            <CardTitle>Preços e Estoque</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid gap-4 md:grid-cols-2">
              <Input
                label="Preço de Compra *"
                type="number"
                step="0.01"
                error={errors.purchase_price?.message}
                {...register('purchase_price', { valueAsNumber: true })}
              />
              <Input
                label="Preço de Venda *"
                type="number"
                step="0.01"
                error={errors.sale_price?.message}
                {...register('sale_price', { valueAsNumber: true })}
              />
            </div>
            <div className="grid gap-4 md:grid-cols-2">
              <Input
                label="Quantidade *"
                type="number"
                error={errors.quantity?.message}
                {...register('quantity', { valueAsNumber: true })}
              />
              <Input
                label="Quantidade Mínima *"
                type="number"
                error={errors.minimum_quantity?.message}
                {...register('minimum_quantity', { valueAsNumber: true })}
              />
            </div>
            <Input label="Localização" {...register('location')} />
          </CardContent>
        </Card>

        <div className="flex justify-end gap-4 mt-6">
          <Button type="button" variant="outline" onClick={() => navigate('/stock')}>
            Cancelar
          </Button>
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
            {isEditing ? 'Salvar' : 'Criar'}
          </Button>
        </div>
      </form>
    </div>
  );
}
