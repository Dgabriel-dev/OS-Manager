import { useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useForm, useFieldArray } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Loader2, ArrowLeft, Plus, Trash2 } from 'lucide-react';
import { serviceOrdersApi } from '@/api/serviceOrders';
import { clientsApi } from '@/api/clients';
import { equipmentApi } from '@/api/equipment';
import { usersApi } from '@/api/users';
import { serviceOrderSchema, type ServiceOrderFormData } from '@/utils/validators';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { useToast } from '@/components/ui/toast';

export function ServiceOrderFormPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const queryClient = useQueryClient();
  const isEditing = !!id;

  const { data: order, isLoading: loadingOrder } = useQuery({
    queryKey: ['service-order', id],
    queryFn: () => serviceOrdersApi.getById(Number(id)),
    enabled: isEditing,
  });

  const { data: clientsData } = useQuery({
    queryKey: ['clients-list'],
    queryFn: () => clientsApi.list({ per_page: 1000 }),
  });

  const { data: techniciansData } = useQuery({
    queryKey: ['technicians-list'],
    queryFn: () => usersApi.list({ per_page: 1000 }),
  });

  const {
    register,
    handleSubmit,
    setValue,
    watch,
    control,
    formState: { errors },
  } = useForm<ServiceOrderFormData>({
    resolver: zodResolver(serviceOrderSchema) as any,
    defaultValues: {
      priority: 'medium',
      warranty_days: 90,
      items: [],
    },
  });

  const { fields, append, remove } = useFieldArray({
    control,
    name: 'items',
  });

  const selectedClientId = watch('client_id');

  const { data: equipmentData } = useQuery({
    queryKey: ['equipment-list', selectedClientId],
    queryFn: () => equipmentApi.byClient(selectedClientId, 1000),
    enabled: !!selectedClientId,
  });

  useEffect(() => {
    if (order) {
      setValue('client_id', order.client?.id);
      setValue('equipment_id', order.equipment?.id);
      setValue('technician_id', order.technician?.id ?? null);
      setValue('priority', order.priority as any);
      setValue('estimated_value', order.estimated_value);
      setValue('warranty_days', order.warranty_days);
      setValue('estimated_delivery_date', order.estimated_delivery_date);
      setValue('notes', order.notes);
      setValue('internal_notes', order.internal_notes);
    }
  }, [order, setValue]);

  const mutation = useMutation({
    mutationFn: (data: ServiceOrderFormData) => {
      const payload: any = { ...data };
      if (!payload.technician_id || isNaN(payload.technician_id)) {
        payload.technician_id = null;
      }
      if (!payload.estimated_value || isNaN(payload.estimated_value)) {
        payload.estimated_value = null;
      }
      if (!payload.estimated_delivery_date) {
        payload.estimated_delivery_date = null;
      }
      if (!payload.notes) {
        payload.notes = null;
      }
      if (!payload.internal_notes) {
        payload.internal_notes = null;
      }
      return isEditing ? serviceOrdersApi.update(Number(id), payload) : serviceOrdersApi.create(payload);
    },
    onSuccess: () => {
      toast('success', isEditing ? 'OS atualizada' : 'OS criada');
      queryClient.invalidateQueries({ queryKey: ['service-orders'] });
      navigate('/service-orders');
    },
    onError: (error: any) => {
      const message = error?.response?.data?.message || error?.response?.data?.errors
        ? Object.values(error.response.data.errors).flat().join(', ')
        : 'Erro ao salvar OS';
      toast('error', message);
    },
  });

  const clientOptions = (clientsData?.data || []).map((c) => ({ value: c.id, label: c.name }));
  const equipmentOptions = (equipmentData?.data || []).map((e) => ({
    value: e.id,
    label: `${e.category} - ${e.brand || ''} ${e.model || ''}`.trim(),
  }));
  const technicianOptions = (techniciansData?.data || []).map((u) => ({ value: u.id, label: u.name }));

  if (isEditing && loadingOrder) {
    return <div className="flex justify-center p-8"><Loader2 className="h-8 w-8 animate-spin" /></div>;
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" onClick={() => navigate('/service-orders')}>
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <div>
          <h1 className="text-2xl font-bold">{isEditing ? 'Editar OS' : 'Nova OS'}</h1>
          <p className="text-muted-foreground">Preencha os dados da ordem de serviço</p>
        </div>
      </div>

      <form onSubmit={handleSubmit((data) => mutation.mutate(data))}>
        <Card>
          <CardHeader>
            <CardTitle>Dados da OS</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid gap-4 md:grid-cols-2">
              <Select
                label="Cliente *"
                options={clientOptions}
                placeholder="Selecione o cliente"
                error={errors.client_id?.message}
                {...register('client_id', { valueAsNumber: true })}
              />
              <Select
                label="Equipamento *"
                options={equipmentOptions}
                placeholder="Selecione o equipamento"
                error={errors.equipment_id?.message}
                {...register('equipment_id', { valueAsNumber: true })}
              />
            </div>
            <div className="grid gap-4 md:grid-cols-3">
              <Select
                label="Técnico"
                options={technicianOptions}
                placeholder="Selecione o técnico"
                {...register('technician_id', { valueAsNumber: true })}
              />
              <Select
                label="Prioridade *"
                options={[
                  { value: 'low', label: 'Baixa' },
                  { value: 'medium', label: 'Média' },
                  { value: 'high', label: 'Alta' },
                  { value: 'urgent', label: 'Urgente' },
                ]}
                error={errors.priority?.message}
                {...register('priority')}
              />
              <Input
                label="Dias de Garantia"
                type="number"
                {...register('warranty_days', { valueAsNumber: true })}
              />
            </div>
            <div className="grid gap-4 md:grid-cols-2">
              <Input
                label="Valor Estimado"
                type="number"
                step="0.01"
                {...register('estimated_value', { valueAsNumber: true })}
              />
              <Input
                label="Data Prevista de Entrega"
                type="date"
                {...register('estimated_delivery_date')}
              />
            </div>
          </CardContent>
        </Card>

        <Card className="mt-6">
          <CardHeader className="flex flex-row items-center justify-between">
            <CardTitle>Itens / Serviços</CardTitle>
            <Button
              type="button"
              variant="outline"
              size="sm"
              onClick={() => append({ description: '', quantity: 1, unit_price: 0, type: 'service' })}
            >
              <Plus className="mr-2 h-4 w-4" />
              Adicionar
            </Button>
          </CardHeader>
          <CardContent className="space-y-4">
            {fields.map((field, index) => (
              <div key={field.id} className="grid gap-4 md:grid-cols-[1fr_auto_auto_auto_auto] items-end">
                <Input
                  label="Descrição"
                  {...register(`items.${index}.description`)}
                />
                <Input
                  label="Qtd"
                  type="number"
                  className="w-20"
                  {...register(`items.${index}.quantity`, { valueAsNumber: true })}
                />
                <Input
                  label="Preço Unit."
                  type="number"
                  step="0.01"
                  className="w-32"
                  {...register(`items.${index}.unit_price`, { valueAsNumber: true })}
                />
                <Select
                  label="Tipo"
                  options={[
                    { value: 'service', label: 'Serviço' },
                    { value: 'part', label: 'Peça' },
                    { value: 'other', label: 'Outro' },
                  ]}
                  {...register(`items.${index}.type`)}
                />
                <Button type="button" variant="ghost" size="icon" onClick={() => remove(index)}>
                  <Trash2 className="h-4 w-4 text-destructive" />
                </Button>
              </div>
            ))}
          </CardContent>
        </Card>

        <Card className="mt-6">
          <CardHeader>
            <CardTitle>Observações</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <Textarea label="Observações" {...register('notes')} placeholder="Observações para o cliente..." />
            <Textarea label="Notas Internas" {...register('internal_notes')} placeholder="Notas internas..." />
          </CardContent>
        </Card>

        <div className="flex justify-end gap-4 mt-6">
          <Button type="button" variant="outline" onClick={() => navigate('/service-orders')}>
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
