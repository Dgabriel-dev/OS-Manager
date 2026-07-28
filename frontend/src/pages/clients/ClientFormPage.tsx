import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Loader2, ArrowLeft } from 'lucide-react';
import { clientsApi } from '@/api/clients';
import { clientSchema, type ClientFormData } from '@/utils/validators';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { useToast } from '@/components/ui/toast';
import { formatCPF_CNPJ, formatPhone } from '@/utils/formatters';

export function ClientFormPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const queryClient = useQueryClient();
  const isEditing = !!id;

  const { data: client, isLoading: loadingClient } = useQuery({
    queryKey: ['client', id],
    queryFn: () => clientsApi.getById(Number(id)),
    enabled: isEditing,
  });

  const {
    register,
    handleSubmit,
    setValue,
    watch,
    formState: { errors },
  } = useForm<ClientFormData>({
    resolver: zodResolver(clientSchema),
  });

  useEffect(() => {
    if (client) {
      setValue('name', client.name);
      setValue('cpf_cnpj', client.cpf_cnpj);
      setValue('phone', client.phone);
      setValue('whatsapp', client.whatsapp);
      setValue('email', client.email);
      setValue('cep', client.cep);
      setValue('address', client.address);
      setValue('city', client.city);
      setValue('state', client.state);
      setValue('observations', client.observations);
    }
  }, [client, setValue]);

  const mutation = useMutation({
    mutationFn: (data: ClientFormData) =>
      isEditing ? clientsApi.update(Number(id), data) : clientsApi.create(data),
    onSuccess: () => {
      toast('success', isEditing ? 'Cliente atualizado' : 'Cliente criado');
      queryClient.invalidateQueries({ queryKey: ['clients'] });
      navigate('/clients');
    },
    onError: (error: any) => {
      const message = error?.response?.data?.message || error?.response?.data?.errors
        ? Object.values(error.response.data.errors).flat().join(', ')
        : 'Erro ao salvar cliente';
      toast('error', message);
    },
  });

  const cpfCnpjValue = watch('cpf_cnpj');
  const phoneValue = watch('phone');

  useEffect(() => {
    if (cpfCnpjValue) {
      setValue('cpf_cnpj', formatCPF_CNPJ(cpfCnpjValue));
    }
  }, [cpfCnpjValue, setValue]);

  useEffect(() => {
    if (phoneValue) {
      setValue('phone', formatPhone(phoneValue));
    }
  }, [phoneValue, setValue]);

  if (isEditing && loadingClient) {
    return <div className="flex justify-center p-8"><Loader2 className="h-8 w-8 animate-spin" /></div>;
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" onClick={() => navigate('/clients')}>
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <div>
          <h1 className="text-2xl font-bold">{isEditing ? 'Editar Cliente' : 'Novo Cliente'}</h1>
          <p className="text-muted-foreground">Preencha os dados do cliente</p>
        </div>
      </div>

      <form onSubmit={handleSubmit((data) => mutation.mutate(data))}>
        <Card>
          <CardHeader>
            <CardTitle>Dados Pessoais</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid gap-4 md:grid-cols-2">
              <Input
                label="Nome *"
                error={errors.name?.message}
                {...register('name')}
              />
              <Input
                label="CPF/CNPJ *"
                error={errors.cpf_cnpj?.message}
                {...register('cpf_cnpj')}
              />
            </div>
            <div className="grid gap-4 md:grid-cols-2">
              <Input
                label="Telefone *"
                error={errors.phone?.message}
                {...register('phone')}
              />
              <Input
                label="WhatsApp"
                {...register('whatsapp')}
              />
            </div>
            <Input
              label="E-mail"
              type="email"
              error={errors.email?.message}
              {...register('email')}
            />
          </CardContent>
        </Card>

        <Card className="mt-6">
          <CardHeader>
            <CardTitle>Endereço</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid gap-4 md:grid-cols-3">
              <Input label="CEP" {...register('cep')} />
              <Input label="Cidade" {...register('city')} className="md:col-span-2" />
            </div>
            <div className="grid gap-4 md:grid-cols-3">
              <Input label="Estado" {...register('state')} />
              <Input label="Endereço" {...register('address')} className="md:col-span-2" />
            </div>
          </CardContent>
        </Card>

        <Card className="mt-6">
          <CardHeader>
            <CardTitle>Observações</CardTitle>
          </CardHeader>
          <CardContent>
            <Textarea {...register('observations')} placeholder="Observações sobre o cliente..." />
          </CardContent>
        </Card>

        <div className="flex justify-end gap-4 mt-6">
          <Button type="button" variant="outline" onClick={() => navigate('/clients')}>
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
