import { useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useMutation, useQuery } from '@tanstack/react-query';
import { Loader2, ArrowLeft } from 'lucide-react';
import { equipmentApi } from '@/api/equipment';
import { clientsApi } from '@/api/clients';
import { equipmentSchema, type EquipmentFormData } from '@/utils/validators';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { useToast } from '@/components/ui/toast';

export function EquipmentFormPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const isEditing = !!id;

  const { data: equipment, isLoading: loadingEquipment } = useQuery({
    queryKey: ['equipment', id],
    queryFn: () => equipmentApi.getById(Number(id)),
    enabled: isEditing,
  });

  const { data: clientsData } = useQuery({
    queryKey: ['clients-list'],
    queryFn: () => clientsApi.list({ per_page: 1000 }),
  });

  const {
    register,
    handleSubmit,
    setValue,
    formState: { errors },
  } = useForm<EquipmentFormData>({
    resolver: zodResolver(equipmentSchema),
  });

  useEffect(() => {
    if (equipment) {
      setValue('client_id', equipment.client?.id);
      setValue('category', equipment.category);
      setValue('brand', equipment.brand);
      setValue('model', equipment.model);
      setValue('serial_number', equipment.serial_number);
      setValue('color', equipment.color);
      setValue('accessories_delivered', equipment.accessories_delivered);
      setValue('physical_state', equipment.physical_state);
      setValue('reported_defect', equipment.reported_defect);
      setValue('technical_diagnosis', equipment.technical_diagnosis);
    }
  }, [equipment, setValue]);

  const mutation = useMutation({
    mutationFn: (data: EquipmentFormData) =>
      isEditing ? equipmentApi.update(Number(id), data) : equipmentApi.create(data),
    onSuccess: () => {
      toast('success', isEditing ? 'Equipamento atualizado' : 'Equipamento criado');
      navigate('/equipment');
    },
    onError: () => {
      toast('error', 'Erro ao salvar equipamento');
    },
  });

  const clientOptions = (clientsData?.data || []).map((c) => ({ value: c.id, label: c.name }));

  const categories = [
    'Notebook', 'Desktop', 'Monitor', 'Impressora', 'Celular', 'Tablet',
    'Switch', 'Roteador', 'HD/SSD', 'Placa Mãe', 'Fonte', 'Outro'
  ];

  if (isEditing && loadingEquipment) {
    return <div className="flex justify-center p-8"><Loader2 className="h-8 w-8 animate-spin" /></div>;
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" onClick={() => navigate('/equipment')}>
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <div>
          <h1 className="text-2xl font-bold">{isEditing ? 'Editar Equipamento' : 'Novo Equipamento'}</h1>
          <p className="text-muted-foreground">Preencha os dados do equipamento</p>
        </div>
      </div>

      <form onSubmit={handleSubmit((data) => mutation.mutate(data))}>
        <Card>
          <CardHeader>
            <CardTitle>Informações do Equipamento</CardTitle>
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
                label="Categoria *"
                options={categories.map((c) => ({ value: c, label: c }))}
                placeholder="Selecione a categoria"
                error={errors.category?.message}
                {...register('category')}
              />
            </div>
            <div className="grid gap-4 md:grid-cols-3">
              <Input label="Marca" {...register('brand')} />
              <Input label="Modelo" {...register('model')} />
              <Input label="Nº Série" {...register('serial_number')} />
            </div>
            <Input label="Cor" {...register('color')} />
          </CardContent>
        </Card>

        <Card className="mt-6">
          <CardHeader>
            <CardTitle>Estado e Defeito</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <Input label="Acessórios Entregues" {...register('accessories_delivered')} />
            <Textarea label="Estado Físico" {...register('physical_state')} placeholder="Descreva o estado físico..." />
            <Textarea label="Defeito Relatado" {...register('reported_defect')} placeholder="Descreva o defeito..." />
            <Textarea label="Diagnóstico Técnico" {...register('technical_diagnosis')} placeholder="Diagnóstico técnico..." />
          </CardContent>
        </Card>

        <div className="flex justify-end gap-4 mt-6">
          <Button type="button" variant="outline" onClick={() => navigate('/equipment')}>
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
