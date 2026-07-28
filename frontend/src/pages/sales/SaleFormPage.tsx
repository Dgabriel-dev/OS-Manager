import { useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useForm, useFieldArray } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { ArrowLeft, Loader2, Plus, Trash2 } from 'lucide-react';
import { salesApi } from '@/api/sales';
import { clientsApi } from '@/api/clients';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useToast } from '@/components/ui/toast';
import { saleSchema, type SaleFormData } from '@/utils/validators';
import { formatCurrency } from '@/utils/formatters';

export function SaleFormPage() {
  const { id } = useParams();
  const isEditing = !!id;
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { toast } = useToast();

  const { data: sale, isLoading: loadingSale } = useQuery({
    queryKey: ['sale', id],
    queryFn: () => salesApi.getById(Number(id)),
    enabled: isEditing,
  });

  const { data: clientsData } = useQuery({
    queryKey: ['clients-list'],
    queryFn: () => clientsApi.list({ per_page: 1000 }),
  });

  const { data: categories } = useQuery({
    queryKey: ['sale-categories'],
    queryFn: salesApi.listCategories,
  });

  const {
    register,
    handleSubmit,
    setValue,
    control,
    watch,
    formState: { errors },
  } = useForm<SaleFormData>({
    resolver: zodResolver(saleSchema) as any,
    defaultValues: {
      payment_status: 'pending',
      items: [{ name: '', quantity: 1, unit_price: 0, cost_price: 0, sale_category_id: null }],
    },
  });

  const { fields, append, remove } = useFieldArray({
    control,
    name: 'items',
  });

  const items = watch('items') || [];
  const totalAmount = items.reduce((sum, item) => sum + (item.quantity || 0) * (item.unit_price || 0), 0);
  const totalCost = items.reduce((sum, item) => sum + (item.quantity || 0) * (item.cost_price || 0), 0);

  useEffect(() => {
    if (sale) {
      setValue('client_id', sale.client?.id || null);
      setValue('payment_method', sale.payment_method || '');
      setValue('payment_status', sale.payment_status);
      setValue('notes', sale.notes || '');
      if (sale.items && sale.items.length > 0) {
        setValue('items', sale.items.map(item => ({
          name: item.name,
          sale_category_id: item.category?.id || null,
          quantity: item.quantity,
          unit_price: item.unit_price,
          cost_price: item.cost_price || 0,
        })));
      }
    }
  }, [sale, setValue]);

  const mutation = useMutation({
    mutationFn: (data: SaleFormData) => {
      const payload = {
        ...data,
        client_id: data.client_id || null,
        items: data.items.map(item => ({
          ...item,
          sale_category_id: item.sale_category_id || null,
        })),
      };
      return isEditing ? salesApi.update(Number(id), payload) : salesApi.create(payload);
    },
    onSuccess: () => {
      toast('success', isEditing ? 'Venda atualizada com sucesso' : 'Venda criada com sucesso');
      queryClient.invalidateQueries({ queryKey: ['sales'] });
      navigate('/sales');
    },
    onError: () => {
      toast('error', 'Erro ao salvar venda');
    },
  });

  if (isEditing && loadingSale) {
    return (
      <div className="flex justify-center p-8">
        <Loader2 className="h-8 w-8 animate-spin" />
      </div>
    );
  }

  const clientOptions = [
    { value: 0, label: 'Venda Avulsa (sem cliente)' },
    ...(clientsData?.data || []).map((c: any) => ({ value: c.id, label: c.name })),
  ];

  const categoryOptions = [
    { value: 0, label: 'Sem categoria' },
    ...(categories || []).map((c: any) => ({ value: c.id, label: c.name })),
  ];

  const paymentMethodOptions = [
    { value: 'cash', label: 'Dinheiro' },
    { value: 'credit_card', label: 'Cartão de Crédito' },
    { value: 'debit_card', label: 'Cartão de Débito' },
    { value: 'pix', label: 'PIX' },
    { value: 'bank_transfer', label: 'Transferência' },
    { value: 'boleto', label: 'Boleto' },
  ];

  const paymentStatusOptions = [
    { value: 'pending', label: 'Pendente' },
    { value: 'paid', label: 'Pago' },
    { value: 'cancelled', label: 'Cancelado' },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" onClick={() => navigate('/sales')}>
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <div>
          <h1 className="text-2xl font-bold">
            {isEditing ? 'Editar Venda' : 'Nova Venda'}
          </h1>
          <p className="text-muted-foreground">Preencha os dados da venda</p>
        </div>
      </div>

      <form onSubmit={handleSubmit((data) => mutation.mutate(data))}>
        <Card>
          <CardHeader>
            <CardTitle>Dados da Venda</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid gap-4 md:grid-cols-2">
              <Select
                label="Cliente"
                options={clientOptions}
                placeholder="Selecione o cliente"
                error={errors.client_id?.message}
                {...register('client_id', { valueAsNumber: true })}
              />
              <Select
                label="Forma de Pagamento"
                options={paymentMethodOptions}
                placeholder="Selecione"
                error={errors.payment_method?.message}
                {...register('payment_method')}
              />
            </div>
            <div className="grid gap-4 md:grid-cols-2">
              <Select
                label="Status do Pagamento"
                options={paymentStatusOptions}
                error={errors.payment_status?.message}
                {...register('payment_status')}
              />
              <div />
            </div>
            <Textarea
              label="Observações"
              placeholder="Observações sobre a venda..."
              error={errors.notes?.message}
              {...register('notes')}
            />
          </CardContent>
        </Card>

        <Card className="mt-4">
          <CardHeader className="flex flex-row items-center justify-between">
            <CardTitle>Itens da Venda</CardTitle>
            <Button
              type="button"
              variant="outline"
              size="sm"
              onClick={() => append({ name: '', quantity: 1, unit_price: 0, cost_price: 0, sale_category_id: null })}
            >
              <Plus className="mr-2 h-4 w-4" />
              Adicionar Item
            </Button>
          </CardHeader>
          <CardContent className="space-y-4">
            {errors.items?.message && (
              <p className="text-sm text-destructive">{errors.items.message}</p>
            )}
            {fields.map((field, index) => (
              <div key={field.id} className="grid gap-4 md:grid-cols-[2fr_1fr_1fr_1fr_1fr_auto] items-end">
                <Input
                  label={index === 0 ? 'Nome do Item *' : undefined}
                  placeholder="Ex: Notebook, Mouse, Teclado..."
                  error={errors.items?.[index]?.name?.message}
                  {...register(`items.${index}.name`)}
                />
                <Select
                  label={index === 0 ? 'Categoria' : undefined}
                  options={categoryOptions}
                  {...register(`items.${index}.sale_category_id`, { valueAsNumber: true })}
                />
                <Input
                  label={index === 0 ? 'Qtd *' : undefined}
                  type="number"
                  min="1"
                  error={errors.items?.[index]?.quantity?.message}
                  {...register(`items.${index}.quantity`, { valueAsNumber: true })}
                />
                <Input
                  label={index === 0 ? 'Preço Venda *' : undefined}
                  type="number"
                  step="0.01"
                  min="0"
                  error={errors.items?.[index]?.unit_price?.message}
                  {...register(`items.${index}.unit_price`, { valueAsNumber: true })}
                />
                <Input
                  label={index === 0 ? 'Custo *' : undefined}
                  type="number"
                  step="0.01"
                  min="0"
                  error={errors.items?.[index]?.cost_price?.message}
                  {...register(`items.${index}.cost_price`, { valueAsNumber: true })}
                />
                <div className={index === 0 ? 'pt-6' : ''}>
                  <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    onClick={() => remove(index)}
                    disabled={fields.length === 1}
                  >
                    <Trash2 className="h-4 w-4 text-destructive" />
                  </Button>
                </div>
              </div>
            ))}

            <div className="flex justify-end gap-6 pt-4 border-t">
              <div className="text-sm">
                <span className="text-muted-foreground">Custo: </span>
                <span className="font-medium text-red-600">{formatCurrency(totalCost)}</span>
              </div>
              <div className="text-sm">
                <span className="text-muted-foreground">Lucro: </span>
                <span className="font-medium text-green-600">{formatCurrency(totalAmount - totalCost)}</span>
              </div>
              <div className="text-lg font-bold">
                Total: {formatCurrency(totalAmount)}
              </div>
            </div>
          </CardContent>
        </Card>

        <div className="flex justify-end gap-4 mt-6">
          <Button type="button" variant="outline" onClick={() => navigate('/sales')}>
            Cancelar
          </Button>
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
            {isEditing ? 'Salvar' : 'Criar Venda'}
          </Button>
        </div>
      </form>
    </div>
  );
}
